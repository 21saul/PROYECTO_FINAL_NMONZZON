<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * EVENTMODEL — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: EVENTS.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class EventModel extends Model
{
    protected $table            = 'events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'description',
        'event_date',
        'event_type',
        'location',
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
        'slug'  => 'required|is_unique[events.slug,id,{id}]',
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

    public function getUpcoming()
    {
        return $this->where('event_date >=', date('Y-m-d'))
            ->where('is_active', 1)
            ->orderBy('event_date', 'ASC')
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }

    public function getWithImages($id)
    {
        return $this->builder()
            ->select('events.*, event_images.id AS image_row_id, event_images.image_url, event_images.alt_text, event_images.sort_order AS image_sort_order')
            ->join('event_images', 'event_images.event_id = events.id', 'left')
            ->where('events.id', $id)
            ->get()
            ->getResultArray();
    }
}