<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * DISENOCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/DISENOCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\DesignProjectImageModel;
use App\Models\DesignProjectModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class DisenoController extends BaseController
{
    public function index()
    {
        $designProjectModel = new DesignProjectModel();
        $projects           = $designProjectModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('web/diseno/index', [
            'title'            => 'Diseño',
            'meta_title'       => 'Diseño',
            'meta_description' => 'Cartelería, diseño textil y cover art en Vigo. Flyers, portadas, camisetas y más. nmonzzon Studio — cuéntame tu idea.',
            'projects' => $projects,
        ]);
    }

    public function show(string $slug)
    {
        $designProjectModel = new DesignProjectModel();
        $project            = $designProjectModel
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if ($project === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $imageModel = new DesignProjectImageModel();
        $images     = $imageModel->getByProject((int) $project['id']);

        return view('web/diseno/show', [
            'title'            => $project['title'] ?? 'Proyecto',
            'meta_title'       => $project['title'] ?? 'Proyecto de diseño',
            'meta_description' => $project['description'] ?? 'Proyecto de diseño por nmonzzon Studio.',
            'og_image'         => !empty($project['featured_image']) ? base_url($project['featured_image']) : null,
            'project' => $project,
            'images'  => $images,
        ]);
    }
}