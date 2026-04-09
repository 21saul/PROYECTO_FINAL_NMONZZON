<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * COUPONMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: COUPONS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'type',
        'value',
        'min_purchase',
        'max_uses',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'code'  => 'required|max_length[50]|is_unique[coupons.code,id,{id}]',
        'type'  => 'required|in_list[percentage,fixed]',
        'value' => 'required|decimal',
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

    public function getByCode(string $code): ?array
    {
        return $this->where('code', $code)->first();
    }

    public function isValid(string $code): bool
    {
        $coupon = $this->getByCode($code);
        if ($coupon === null) {
            return false;
        }

        if ((int) ($coupon['is_active'] ?? 0) !== 1) {
            return false;
        }

        $now = date('Y-m-d H:i:s');

        if (! empty($coupon['valid_from']) && $coupon['valid_from'] > $now) {
            return false;
        }

        if (! empty($coupon['valid_until']) && $coupon['valid_until'] < $now) {
            return false;
        }

        if (isset($coupon['max_uses']) && $coupon['max_uses'] !== null && $coupon['max_uses'] !== '') {
            $maxUses = (int) $coupon['max_uses'];
            if ($maxUses > 0 && (int) ($coupon['used_count'] ?? 0) >= $maxUses) {
                return false;
            }
        }

        return true;
    }

    public function incrementUsage(int $id): bool
    {
        $coupon = $this->find($id);
        if ($coupon === null) {
            return false;
        }

        return $this->update($id, [
            'used_count' => (int) ($coupon['used_count'] ?? 0) + 1,
        ]);
    }
}