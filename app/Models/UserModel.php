<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * USERMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: USERS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'email_verified_at',
        'remember_token',
        'is_active',
        'last_login_at',
        'last_login_ip',
        'failed_login_attempts',
        'locked_until',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'name'     => 'required|min_length[2]|max_length[100]',
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role'     => 'required|in_list[admin,client]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email ya esta registrado.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword', 'generateUuid'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_ARGON2ID);
        }

        return $data;
    }

    protected function generateUuid(array $data): array
    {
        if (! isset($data['data']['uuid']) || $data['data']['uuid'] === '') {
            $data['data']['uuid'] = bin2hex(random_bytes(18));
        }

        return $data;
    }

    public function getByEmail(string $email): ?array
    {
        return $this->where('email', $email)->first();
    }

    public function isLocked(array $user): bool
    {
        if (empty($user['locked_until'])) {
            return false;
        }

        return strtotime($user['locked_until']) > time();
    }

    public function incrementFailedAttempts(int $userId): void
    {
        $user = $this->find($userId);
        if ($user === null) {
            return;
        }

        $attempts = (int) ($user['failed_login_attempts'] ?? 0) + 1;
        $update   = ['failed_login_attempts' => $attempts];

        if ($attempts >= 5) {
            $update['locked_until'] = date('Y-m-d H:i:s', time() + (15 * 60));
        }

        $this->skipValidation(true)->update($userId, $update);
    }

    public function resetFailedAttempts(int $userId): void
    {
        $this->skipValidation(true)->update($userId, [
            'failed_login_attempts' => 0,
            'locked_until'          => null,
        ]);
    }
}