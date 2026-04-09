<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTFOLIOWORKMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: PORTFOLIO_WORKS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class PortfolioWorkModel extends Model
{
    protected $table            = 'portfolio_works';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'category_id',
        'title',
        'slug',
        'description',
        'image_url',
        'thumbnail_url',
        'cloudinary_public_id',
        'style_tag',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title'       => 'required|max_length[200]',
        'slug'        => 'required|is_unique[portfolio_works.slug,id,{id}]',
        'image_url'   => 'required',
        'category_id' => 'required|integer',
    ];

    protected $beforeInsert = ['generateSlug'];
    protected $beforeUpdate = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        helper('url');

        $row = $data['data'] ?? [];
        if (empty($row['slug'] ?? '')) {
            $title = $row['title'] ?? $row['name'] ?? '';
            $data['data']['slug'] = strtolower(url_title($title, '-', true));
        }

        return $data;
    }

    public function getByCategory($categoryId)
    {
        return $this->where('category_id', $categoryId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function getFeatured()
    {
        return $this->where('is_featured', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}