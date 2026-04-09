<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTFOLIOCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/PORTFOLIOCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\PortfolioWorkModel;

class PortfolioController extends BaseApiController
{
    protected PortfolioWorkModel $portfolio;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        helper('api');
        $this->portfolio = model(PortfolioWorkModel::class);
    }

    public function index()
    {
        $categoryId = $this->request->getGet('category_id');
        $styleTag   = $this->request->getGet('style_tag');
        $featured   = $this->request->getGet('is_featured');

        $perPage = (int) ($this->request->getGet('per_page') ?? 12);
        $perPage = min(100, max(1, $perPage));

        $builder = $this->portfolio->where('is_active', 1);

        if ($categoryId !== null && $categoryId !== '') {
            $builder->where('category_id', (int) $categoryId);
        }
        if ($styleTag !== null && $styleTag !== '') {
            $builder->where('style_tag', $styleTag);
        }
        if ($featured !== null && $featured !== '') {
            $builder->where('is_featured', (int) (bool) $featured);
        }

        $items = $builder->orderBy('sort_order', 'ASC')->paginate($perPage);
        $pager = $this->portfolio->pager;

        return apiResponse([
            'items' => $items,
            'pagination' => [
                'current_page' => $pager->getCurrentPage(),
                'per_page'     => $perPage,
                'total'        => $pager->getTotal(),
                'page_count'   => $pager->getPageCount(),
            ],
        ]);
    }

    public function show($slug = null)
    {
        if ($slug === null || $slug === '') {
            return apiError('Not found', 404);
        }

        $row = $this->portfolio->where('slug', $slug)->where('is_active', 1)->first();
        if (!$row) {
            return apiError('Not found', 404);
        }

        return apiResponse($row);
    }

    public function create()
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|integer',
            'image'       => 'uploaded[image]|max_size[image,8192]|ext_in[image,jpg,jpeg,png,gif,webp]',
            'description' => 'permit_empty',
            'style_tag'   => 'permit_empty|max_length[100]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'sort_order'  => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid()) {
            return apiError('Invalid image upload', 422);
        }

        $uploadDir = FCPATH . 'uploads/portfolio/';
        if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0755, true)) {
            return apiError('Could not create upload directory', 500);
        }

        $newName = $file->getRandomName();
        $file->move($uploadDir, $newName);
        $relativeUrl = 'uploads/portfolio/' . $newName;

        $data = [
            'category_id'   => (int) $this->request->getPost('category_id'),
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description') ?: null,
            'image_url'     => $relativeUrl,
            'thumbnail_url' => $relativeUrl,
            'style_tag'     => $this->request->getPost('style_tag') ?: null,
            'is_featured'   => $this->request->getPost('is_featured') !== null && $this->request->getPost('is_featured') !== ''
                ? (int) $this->request->getPost('is_featured') : 0,
            'is_active'     => 1,
            'sort_order'    => $this->request->getPost('sort_order') !== '' && $this->request->getPost('sort_order') !== null
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $id = $this->portfolio->insert($data);
        if ($id === false) {
            return apiError('Could not create portfolio item', 500, $this->portfolio->errors());
        }

        return apiResponse($this->portfolio->find($id), 201, 'Created');
    }

    public function update($id = null)
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        $existing = $this->portfolio->find($id);
        if (!$existing) {
            return apiError('Not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if ($payload === null) {
            $payload = $this->request->getRawInput();
            $payload = $payload !== '' ? (array) json_decode($payload, true) : [];
        }

        $rules = [
            'title'       => 'permit_empty|max_length[200]',
            'category_id' => 'permit_empty|integer',
            'description' => 'permit_empty',
            'style_tag'   => 'permit_empty|max_length[100]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'is_active'   => 'permit_empty|in_list[0,1]',
            'sort_order'  => 'permit_empty|integer',
        ];

        if (!$this->validateData($payload, $rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = array_filter(
            [
                'title'       => $payload['title'] ?? null,
                'category_id' => isset($payload['category_id']) ? (int) $payload['category_id'] : null,
                'description' => $payload['description'] ?? null,
                'style_tag'   => $payload['style_tag'] ?? null,
                'is_featured' => array_key_exists('is_featured', $payload) ? (int) $payload['is_featured'] : null,
                'is_active'   => array_key_exists('is_active', $payload) ? (int) $payload['is_active'] : null,
                'sort_order'  => isset($payload['sort_order']) ? (int) $payload['sort_order'] : null,
            ],
            static fn ($v) => $v !== null
        );

        if ($update === []) {
            return apiResponse($this->portfolio->find($id), 200, 'No changes');
        }

        if (! $this->portfolio->update($id, $update)) {
            return apiError('Could not update', 500, $this->portfolio->errors());
        }

        return apiResponse($this->portfolio->find($id), 200, 'Updated');
    }

    public function delete($id = null)
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->portfolio->find($id)) {
            return apiError('Not found', 404);
        }

        if (! $this->portfolio->delete($id)) {
            return apiError('Could not delete', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}