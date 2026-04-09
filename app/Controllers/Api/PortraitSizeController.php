<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITSIZECONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/PORTRAITSIZECONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\PortraitSizeModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PortraitSizeController extends BaseApiController
{
    protected PortraitSizeModel $sizeModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->sizeModel = model(PortraitSizeModel::class);
    }

    /**
     * GET — active sizes by sort_order.
     */
    public function index(): ResponseInterface
    {
        $rows = $this->sizeModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return apiResponse($rows, 200, 'OK');
    }

    /**
     * GET — size by id.
     */
    public function show($id = null): ResponseInterface
    {
        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $row = $this->sizeModel->find($id);
        if (! $row) {
            return apiError('Portrait size not found', 404);
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
            'name'       => 'required|max_length[50]',
            'dimensions' => 'required|max_length[50]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $insert = [
            'name'            => $validated['name'],
            'dimensions'      => $validated['dimensions'],
            'price_modifier'  => $this->request->getJsonVar('price_modifier') ?? $this->request->getPost('price_modifier') ?? 0,
            'sort_order'      => (int) ($this->request->getJsonVar('sort_order') ?? $this->request->getPost('sort_order') ?? 0),
            'is_active'       => 1,
        ];

        $id = $this->sizeModel->insert($insert, true);
        if (! $id) {
            return apiError('Failed to create portrait size', 500, $this->sizeModel->errors());
        }

        return apiResponse($this->sizeModel->find($id), 201, 'Created');
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
        if ($id <= 0 || ! $this->sizeModel->find($id)) {
            return apiError('Portrait size not found', 404);
        }

        $rules = [
            'name'       => 'permit_empty|max_length[50]',
            'dimensions' => 'permit_empty|max_length[50]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['name', 'dimensions'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                $update[$key] = $validated[$key];
            }
        }
        if ($this->request->getVar('price_modifier') !== null && $this->request->getVar('price_modifier') !== '') {
            $update['price_modifier'] = $this->request->getVar('price_modifier');
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

        if (! $this->sizeModel->update($id, $update)) {
            return apiError('Update failed', 500, $this->sizeModel->errors());
        }

        return apiResponse($this->sizeModel->find($id), 200, 'Updated');
    }

    /**
     * DELETE — admin only.
     *
     * Uses model delete(); enable soft deletes on PortraitSizeModel if a deleted_at column exists.
     */
    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->sizeModel->find($id)) {
            return apiError('Portrait size not found', 404);
        }

        if (! $this->sizeModel->delete($id)) {
            return apiError('Delete failed', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}