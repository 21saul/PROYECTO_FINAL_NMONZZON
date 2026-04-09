<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * DESIGNPROJECTIMAGEMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: DESIGN_PROJECT_IMAGES.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class DesignProjectImageModel extends Model
{
    protected $table            = 'design_project_images';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'design_project_id',
        'image_url',
        'alt_text',
        'sort_order',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'design_project_id' => 'required|integer',
        'image_url'         => 'required',
    ];

    public function getByProject($projectId)
    {
        return $this->where('design_project_id', $projectId)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
}