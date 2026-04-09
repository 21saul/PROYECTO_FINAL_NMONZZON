<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITSTYLECONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/PORTRAITSTYLECONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\PortraitStyleModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PortraitStyleController extends BaseApiController
{
    protected PortraitStyleModel $styleModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->styleModel = model(PortraitStyleModel::class);
    }

    /**
     * GET — active portrait styles.
     */
    public function index(): ResponseInterface
    {
        $rows = $this->styleModel
            ->select('id, name, slug, description, base_price, sample_image, sort_order, is_active, created_at, updated_at')
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return apiResponse($rows, 200, 'OK');
    }

    /**
     * GET — style by id.
     */
    public function show($id = null): ResponseInterface
    {
        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $row = $this->styleModel->find($id);
        if (! $row) {
            return apiError('Portrait style not found', 404);
        }

        return apiResponse($row, 200, 'OK');
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
            'name'       => 'required|max_length[100]',
            'slug'       => 'permit_empty|max_length[120]|is_unique[portrait_styles.slug]',
            'base_price' => 'required|decimal',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $insert = [
            'name'        => $validated['name'],
            'slug'        => $validated['slug'] ?? '',
            'base_price'  => $validated['base_price'],
            'description' => $this->request->getJsonVar('description') ?? $this->request->getPost('description'),
            'sample_image'=> $this->request->getJsonVar('sample_image') ?? $this->request->getPost('sample_image'),
            'is_active'   => 1,
            'sort_order'  => (int) ($this->request->getJsonVar('sort_order') ?? $this->request->getPost('sort_order') ?? 0),
        ];

        $id = $this->styleModel->insert($insert, true);
        if (! $id) {
            return apiError('Failed to create portrait style', 500, $this->styleModel->errors());
        }

        return apiResponse($this->styleModel->find($id), 201, 'Created');
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
        if ($id <= 0 || ! $this->styleModel->find($id)) {
            return apiError('Portrait style not found', 404);
        }

        $rules = [
            'name'       => 'permit_empty|max_length[100]',
            'slug'       => "permit_empty|max_length[120]|is_unique[portrait_styles.slug,id,{$id}]",
            'base_price' => 'permit_empty|decimal',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['name', 'slug', 'base_price'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                $update[$key] = $validated[$key];
            }
        }
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['description', 'sample_image'] as $key) {
            $v = $this->request->getVar($key);
            if ($v !== null && $v !== '') {
                $update[$key] = $v;
            }
        }
        if ($this->request->getVar('sort_order') !== null && $this->request->getVar('sort_order') !== '') {
            $update['sort_order'] = (int) $this->request->getVar('sort_order');
        }
        if ($this->request->getVar('is_active') !== null && $this->request->getVar('is_active') !== '') {
            $update['is_active'] = filter_var($this->request->getVar('is_active'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
        }

        if ($update === []) {
            return apiError('No valid fields to update', 422);
        }

        if (! $this->styleModel->update($id, $update)) {
            return apiError('Update failed', 500, $this->styleModel->errors());
        }

        return apiResponse($this->styleModel->find($id), 200, 'Updated');
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
        if ($id <= 0 || ! $this->styleModel->find($id)) {
            return apiError('Portrait style not found', 404);
        }

        if (! $this->styleModel->delete($id)) {
            return apiError('Delete failed', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}