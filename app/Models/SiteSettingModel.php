<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * SITESETTINGMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: SITE_SETTINGS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table            = 'site_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'key',
        'value',
        'type',
        'group',
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
        'key'  => 'required|max_length[100]|is_unique[site_settings.key,id,{id}]',
        'type' => 'required|in_list[text,textarea,image,boolean,json]',
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

    public function get(string $key): ?string
    {
        $row = $this->where('key', $key)->first();

        return $row === null ? null : (string) ($row['value'] ?? '');
    }

    /**
     * Upsert by key. Named setValue because Model::set() is reserved for query-builder chaining.
     */
    public function setValue(string $key, string $value): bool
    {
        $existing = $this->where('key', $key)->first();

        if ($existing !== null) {
            return $this->update((int) $existing['id'], ['value' => $value]);
        }

        return $this->insert([
            'key'   => $key,
            'value' => $value,
            'type'  => 'text',
            'group' => 'general',
        ]) !== false;
    }

    public function getByGroup(string $group): array
    {
        return $this->where('group', $group)
            ->orderBy('key', 'ASC')
            ->findAll();
    }
}