<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * HOMECONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/HOMECONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PortfolioWorkModel;
use App\Models\SiteSettingModel;
use App\Models\TestimonialModel;

class HomeController extends BaseController
{
    /**
     * MUESTRA LA PÁGINA DE INICIO CON DATOS PARA EL HERO, PORTFOLIO DESTACADO Y TESTIMONIOS.
     */
    public function index()
    {
        // INSTANCIAR MODELOS NECESARIOS PARA CATEGORÍAS, PORTFOLIO, TESTIMONIOS Y CONFIGURACIÓN DEL SITIO
        $categoryModel   = model(CategoryModel::class);
        $portfolioModel  = model(PortfolioWorkModel::class);
        $testimonialModel = model(TestimonialModel::class);
        $settingsModel   = model(SiteSettingModel::class);

        // ARREGLO DE DATOS PARA LA VISTA: LISTADOS ACTIVOS Y VALORES DE CONFIGURACIÓN CLAVE
        $data = [
            'meta_title'       => 'Inicio',
            'meta_description' => 'nmonzzon Studio — Arte, retratos personalizados, arte en vivo, branding y diseño. Vigo, España.',
            'categories'    => $categoryModel->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll(),
            'featuredWorks' => $portfolioModel->where('is_featured', 1)
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->limit(8)
                ->findAll(),
            'testimonials'  => $testimonialModel->where('is_active', 1)
                ->where('is_featured', 1)
                ->orderBy('sort_order')
                ->findAll(),
            'settings'      => [
                'hero_title'    => $settingsModel->get('hero_title'),
                'hero_subtitle' => $settingsModel->get('hero_subtitle'),
                'about_text'    => $settingsModel->get('about_text'),
            ],
        ];

        return view('web/home', $data);
    }
}