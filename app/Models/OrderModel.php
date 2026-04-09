<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * ORDERMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: ORDERS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'shipping_cost',
        'tax',
        'discount',
        'total',
        'coupon_code',
        'shipping_name',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'shipping_phone',
        'tracking_number',
        'stripe_payment_id',
        'payment_status',
        'invoice_path',
        'notes',
        'paid_at',
        'shipped_at',
        'delivered_at',
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
        'user_id'               => 'required|integer',
        'status'                => 'permit_empty|in_list[pending,processing,shipped,delivered,cancelled,refunded]',
        'shipping_name'         => 'required',
        'shipping_address'      => 'required',
        'shipping_city'         => 'required',
        'shipping_postal_code'  => 'required',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateOrderNumber'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function generateOrderNumber(array $data): array
    {
        $data['data']['order_number'] = 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        return $data;
    }

    public function getByUser(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    public function getWithItems(int $id): ?array
    {
        $order = $this->find($id);
        if ($order === null) {
            return null;
        }

        $itemModel = new OrderItemModel();
        $order['items'] = $itemModel->getByOrder($id);

        return $order;
    }
}