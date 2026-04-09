<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CATEGORYMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: CATEGORIES.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'image',
        'icon',
        'sort_order',
        'is_active',
        'meta_title',
        'meta_description',
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
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[120]|is_unique[categories.slug,id,{id}]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $beforeUpdate   = ['generateSlug'];

    public function insert($row = null, bool $returnID = true)
    {
        if (isset($this->tempData['data'])) {
            if ($row === null) {
                $row = $this->tempData['data'];
            } else {
                $row = $this->transformDataToArray($row, 'insert');
                $row = array_merge($this->tempData['data'], $row);
            }
        } elseif ($row !== null) {
            $row = $this->transformDataToArray($row, 'insert');
        }

        $this->escape   = $this->tempData['escape'] ?? [];
        $this->tempData = [];

        if (is_array($row)) {
            $row = $this->applySlugFromName($row);
        }

        return parent::insert($row, $returnID);
    }

    public function update($id = null, $row = null): bool
    {
        if (isset($this->tempData['data'])) {
            if ($row === null) {
                $row = $this->tempData['data'];
            } else {
                $row = $this->transformDataToArray($row, 'update');
                $row = array_merge($this->tempData['data'], $row);
            }
        } elseif ($row !== null) {
            $row = $this->transformDataToArray($row, 'update');
        }

        $this->escape   = $this->tempData['escape'] ?? [];
        $this->tempData = [];

        if (is_array($row)) {
            $row = $this->applySlugFromName($row);
        }

        return parent::update($id, $row);
    }

    protected function applySlugFromName(array $row): array
    {
        $slug = trim((string) ($row['slug'] ?? ''));

        if ($slug === '' && ! empty($row['name'])) {
            helper('url');
            $row['slug'] = strtolower(url_title($row['name'], '-', true));
        }

        return $row;
    }

    protected function generateSlug(array $data): array
    {
        $data['data'] = $this->applySlugFromName($data['data'] ?? []);

        return $data;
    }
}