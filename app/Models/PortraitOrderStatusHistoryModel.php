<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITORDERSTATUSHISTORYMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PORTRAIT_ORDER_STATUS_HISTORY.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class PortraitOrderStatusHistoryModel extends Model
{
    protected $table            = 'portrait_order_status_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'portrait_order_id',
        'from_status',
        'to_status',
        'changed_by',
        'notes',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'portrait_order_id' => 'required|integer',
        'to_status'         => 'required',
        'changed_by'        => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;

    public function getByOrder(int $orderId): array
    {
        return $this->where('portrait_order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}