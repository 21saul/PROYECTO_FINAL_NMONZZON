<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTFOLIOCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/PORTFOLIOCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PortfolioWorkModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PortfolioController extends BaseController
{
    protected int $perPage = 12;

    public function index()
    {
        $categorySlug = $this->request->getGet('category');
        $categorySlug = is_string($categorySlug) ? trim($categorySlug) : '';
        $categorySlug = $categorySlug === '' ? null : $categorySlug;

        $categoryModel  = model(CategoryModel::class);
        $portfolioModel = model(PortfolioWorkModel::class);

        $selectedCategory = null;
        if ($categorySlug !== null) {
            $selectedCategory = $categoryModel->where('slug', $categorySlug)
                ->where('is_active', 1)
                ->first();
            if ($selectedCategory === null) {
                $categorySlug = null;
            }
        }

        $portfolioModel->where('is_active', 1);
        if ($selectedCategory !== null) {
            $portfolioModel->where('category_id', (int) $selectedCategory['id']);
        }

        $data = [
            'meta_title'       => 'Portfolio',
            'meta_description' => 'Galería de trabajos: retratos, branding, eventos y diseño por nmonzzon.',
            'works'              => $portfolioModel->orderBy('sort_order', 'ASC')->paginate($this->perPage),
            'pager'              => $portfolioModel->pager,
            'categories'         => $categoryModel->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll(),
            'selected_category'  => $selectedCategory,
            'selected_category_slug' => $categorySlug,
        ];

        return view('web/portfolio/index', $data);
    }

    public function show(string $slug)
    {
        $portfolioModel = model(PortfolioWorkModel::class);
        $work           = $portfolioModel->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if ($work === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $data = [
            'meta_title'       => $work['title'] ?? 'Obra',
            'meta_description' => $work['description'] ?? 'Obra del portfolio de nmonzzon Studio.',
            'og_image'         => !empty($work['image_url']) ? base_url($work['image_url']) : null,
            'work' => $work,
        ];

        return view('web/portfolio/show', $data);
    }
}