<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CATEGORYCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/CATEGORYCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use App\Models\PortfolioWorkModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CategoryController extends BaseApiController
{
    protected CategoryModel $categoryModel;
    protected PortfolioWorkModel $portfolioWorkModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->categoryModel      = model(CategoryModel::class);
        $this->portfolioWorkModel = model(PortfolioWorkModel::class);
    }

    /**
     * GET — active categories by sort_order.
     */
    public function index(): ResponseInterface
    {
        $rows = $this->categoryModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return apiResponse($rows, 200, 'OK');
    }

    /**
     * GET — category by id with portfolio works count.
     */
    public function show($id = null): ResponseInterface
    {
        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid category id', 400);
        }

        $category = $this->categoryModel->find($id);
        if (! $category) {
            return apiError('Category not found', 404);
        }

        $worksCount = $this->portfolioWorkModel
            ->where('category_id', $id)
            ->where('is_active', 1)
            ->countAllResults();

        $category['portfolio_works_count'] = $worksCount;

        return apiResponse($category, 200, 'OK');
    }

    /**
     * POST — admin only.
     */
    public function create(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'name' => 'required|max_length[100]',
            'slug' => 'permit_empty|max_length[120]|is_unique[categories.slug]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $insert = [
            'name'       => $validated['name'],
            'slug'       => $validated['slug'] ?? '',
            'is_active'  => 1,
            'sort_order' => (int) ($this->request->getVar('sort_order') ?? 0),
        ];

        $id = $this->categoryModel->insert($insert, true);
        if (! $id) {
            return apiError('Failed to create category', 500, $this->categoryModel->errors());
        }

        return apiResponse($this->categoryModel->find($id), 201, 'Created');
    }

    /**
     * PUT — admin only.
     */
    public function update($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->categoryModel->find($id)) {
            return apiError('Category not found', 404);
        }

        $rules = [
            'name' => 'permit_empty|max_length[100]',
            'slug' => "permit_empty|max_length[120]|is_unique[categories.slug,id,{$id}]",
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = array_filter(
            [
                'name' => $validated['name'] ?? null,
                'slug' => $validated['slug'] ?? null,
            ],
            static fn ($v) => $v !== null
        );

        if ($update === []) {
            return apiError('No valid fields to update', 422);
        }

        if (! $this->categoryModel->update($id, $update)) {
            return apiError('Update failed', 500, $this->categoryModel->errors());
        }

        return apiResponse($this->categoryModel->find($id), 200, 'Updated');
    }

    /**
     * DELETE — admin only (soft delete).
     */
    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->categoryModel->find($id)) {
            return apiError('Category not found', 404);
        }

        if (! $this->categoryModel->delete($id)) {
            return apiError('Delete failed', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}