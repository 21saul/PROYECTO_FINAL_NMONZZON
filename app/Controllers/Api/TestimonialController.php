<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * TESTIMONIALCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/TESTIMONIALCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\TestimonialModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TestimonialController extends BaseApiController
{
    protected TestimonialModel $testimonialModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->testimonialModel = model(TestimonialModel::class);
    }

    public function index(): ResponseInterface
    {
        $this->testimonialModel->where('is_active', 1);

        $featured = $this->request->getGet('is_featured');
        if ($featured !== null && $featured !== '' && filter_var($featured, FILTER_VALIDATE_BOOLEAN)) {
            $this->testimonialModel->where('is_featured', 1);
        }

        $rows = $this->testimonialModel->orderBy('sort_order', 'ASC')->findAll();

        return apiResponse($rows);
    }

    public function create(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'client_name' => 'required|max_length[100]',
            'content'     => 'required',
            'rating'      => 'required|integer|greater_than[0]|less_than[6]',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            'client_name' => (string) $validated['client_name'],
            'content'     => (string) $validated['content'],
            'rating'      => (int) $validated['rating'],
        ];

        $id = $this->testimonialModel->skipValidation(true)->insert($data);
        if ($id === false) {
            return apiError('Could not create testimonial', 500);
        }

        return apiResponse($this->testimonialModel->find($id), 201, 'Created');
    }

    public function update($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        if ($this->testimonialModel->find((int) $id) === null) {
            return apiError('Not found', 404);
        }

        $rules = [
            'client_name' => 'permit_empty|max_length[100]',
            'content'     => 'permit_empty',
            'rating'      => 'permit_empty|integer|greater_than[0]|less_than[6]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'is_active'   => 'permit_empty|in_list[0,1]',
            'sort_order'  => 'permit_empty|integer',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = array_filter(
            [
                'client_name' => $validated['client_name'] ?? null,
                'content'     => $validated['content'] ?? null,
                'rating'      => isset($validated['rating']) ? (int) $validated['rating'] : null,
                'is_featured' => isset($validated['is_featured']) ? (int) $validated['is_featured'] : null,
                'is_active'   => isset($validated['is_active']) ? (int) $validated['is_active'] : null,
                'sort_order'  => isset($validated['sort_order']) ? (int) $validated['sort_order'] : null,
            ],
            static fn ($v) => $v !== null
        );

        if ($data === []) {
            return apiResponse($this->testimonialModel->find((int) $id));
        }

        if (! $this->testimonialModel->skipValidation(true)->update((int) $id, $data)) {
            return apiError('Could not update testimonial', 500);
        }

        return apiResponse($this->testimonialModel->find((int) $id), 200, 'Updated');
    }

    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        if ($this->testimonialModel->delete((int) $id) === false) {
            return apiError('Not found', 404);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}