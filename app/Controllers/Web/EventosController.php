<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * EVENTOSCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/EVENTOSCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\EventImageModel;
use App\Models\EventModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class EventosController extends BaseController
{
    public function index()
    {
        $data = [
            'meta_title'       => 'Eventos',
            'meta_description' => 'Papelería para eventos: invitaciones, recordatorios, minutas, meseros y seating. Presupuesto según papel y acabados. nmonzzon Studio, Vigo.',
        ];

        return view('web/eventos/index', $data);
    }

    public function show(string $slug)
    {
        $eventModel  = model(EventModel::class);
        $imageModel  = model(EventImageModel::class);

        $event = $eventModel->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if ($event === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $images = $imageModel->getByEvent((int) $event['id']);

        $data = [
            'meta_title'       => $event['title'] ?? 'Evento',
            'meta_description' => $event['description'] ?? 'Evento artístico por nmonzzon Studio.',
            'og_image'         => !empty($event['featured_image']) ? base_url($event['featured_image']) : null,
            'event'  => $event,
            'images' => $images,
        ];

        return view('web/eventos/show', $data);
    }
}