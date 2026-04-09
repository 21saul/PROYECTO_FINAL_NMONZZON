<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTVARIANTMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PRODUCT_VARIANTS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantModel extends Model
{
    protected $table            = 'product_variants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'variant_name',
        'variant_value',
        'price_modifier',
        'stock',
        'sku',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'product_id'    => 'required|integer',
        'variant_name'  => 'required|max_length[100]',
        'variant_value' => 'required|max_length[100]',
    ];

    public function getByProduct($productId)
    {
        return $this->where('product_id', $productId)
            ->orderBy('variant_name', 'ASC')
            ->findAll();
    }
}