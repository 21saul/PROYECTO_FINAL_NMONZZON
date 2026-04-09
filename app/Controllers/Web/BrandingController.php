<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * BRANDINGCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/BRANDINGCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\BrandingProjectImageModel;
use App\Models\BrandingProjectModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class BrandingController extends BaseController
{
    public function index()
    {
        $data = [
            'meta_title'       => 'Branding',
            'meta_description' => 'Logotipo y manual de identidad: casos de estudio, tarifas y proceso creativo con nmonzzon Studio.',
        ];

        return view('web/branding/index', $data);
    }

    public function show(string $slug)
    {
        helper('branding');

        $projectModel = model(BrandingProjectModel::class);
        $imageModel   = model(BrandingProjectImageModel::class);

        $project = $projectModel->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if ($project === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $images = $imageModel->getByProject((int) $project['id']);

        $data = [
            'meta_title'       => $project['title'] ?? 'Proyecto de branding',
            'meta_description' => $project['description'] ?? 'Proyecto de branding por nmonzzon Studio.',
            'og_image'         => !empty($project['featured_image']) ? base_url($project['featured_image']) : null,
            'project' => $project,
            'images'  => $images,
        ];

        return view('web/branding/show', $data);
    }
}