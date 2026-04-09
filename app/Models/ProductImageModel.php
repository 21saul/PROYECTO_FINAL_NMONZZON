<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTIMAGEMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PRODUCT_IMAGES.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'image_url',
        'alt_text',
        'sort_order',
        'is_primary',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'product_id' => 'required|integer',
        'image_url'  => 'required',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;

    public function getByProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}