<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITORDERMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PORTRAIT_ORDERS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class PortraitOrderModel extends Model
{
    protected $table            = 'portrait_orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_number',
        'user_id',
        'portrait_style_id',
        'portrait_size_id',
        'num_figures',
        'with_frame',
        'frame_type',
        'status',
        'base_price',
        'extras_price',
        'total_price',
        'reference_photo',
        'sketch_image',
        'final_image',
        'client_notes',
        'admin_notes',
        'stripe_payment_id',
        'payment_status',
        'invoice_path',
        'paid_at',
        'delivered_at',
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
        'user_id'            => 'required|integer',
        'portrait_style_id'  => 'required|integer',
        'portrait_size_id'   => 'required|integer',
        'num_figures'        => 'required|integer|greater_than[0]',
        'status'             => 'in_list[quote,accepted,photo_received,in_progress,revision,delivered,completed,cancelled]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateOrderNumber'];

    protected function generateOrderNumber(array $data): array
    {
        if (! isset($data['data']['order_number']) || $data['data']['order_number'] === '') {
            $data['data']['order_number'] = 'PO-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        }

        return $data;
    }

    public function getWithRelations(int $id): ?array
    {
        return $this->select(
            'portrait_orders.*, users.name AS user_name, users.email AS user_email, '
            . 'portrait_styles.name AS style_name, portrait_sizes.name AS size_name'
        )
            ->join('users', 'users.id = portrait_orders.user_id', 'left')
            ->join('portrait_styles', 'portrait_styles.id = portrait_orders.portrait_style_id', 'left')
            ->join('portrait_sizes', 'portrait_sizes.id = portrait_orders.portrait_size_id', 'left')
            ->where('portrait_orders.id', $id)
            ->first();
    }
}