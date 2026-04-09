<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * AUTHTOKENMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: AUTH_TOKENS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class AuthTokenModel extends Model
{
    protected $table            = 'auth_tokens';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'token_hash',
        'expires_at',
        'ip_address',
        'user_agent',
        'is_revoked',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'user_id'     => 'required|integer',
        'token_hash'  => 'required',
        'expires_at'  => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getValidToken(string $tokenHash): ?array
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('token_hash', $tokenHash)
            ->where('is_revoked', 0)
            ->where('expires_at >', $now)
            ->first();
    }

    public function revokeAllForUser(int $userId): bool
    {
        return $this->builder()
            ->where('user_id', $userId)
            ->update(['is_revoked' => 1]);
    }

    public function revokeToken(string $tokenHash): bool
    {
        return $this->builder()
            ->where('token_hash', $tokenHash)
            ->update(['is_revoked' => 1]);
    }

    public function cleanExpired(): bool
    {
        $now = date('Y-m-d H:i:s');

        return $this->where('expires_at <', $now)->delete();
    }
}