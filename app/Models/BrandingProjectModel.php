<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * BRANDINGPROJECTMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: BRANDING_PROJECTS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class BrandingProjectModel extends Model
{
    protected $table            = 'branding_projects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'client_name',
        'description',
        'services_provided',
        'featured_image',
        'is_featured',
        'is_active',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'title' => 'required|max_length[200]',
        'slug'  => 'required|is_unique[branding_projects.slug,id,{id}]',
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

    public function getFeatured()
    {
        return $this->where('is_featured', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function getWithImages($id)
    {
        return $this->builder()
            ->select('branding_projects.*, branding_project_images.id AS image_row_id, branding_project_images.image_url, branding_project_images.alt_text, branding_project_images.sort_order AS image_sort_order')
            ->join('branding_project_images', 'branding_project_images.branding_project_id = branding_projects.id', 'left')
            ->where('branding_projects.id', $id)
            ->get()
            ->getResultArray();
    }
}