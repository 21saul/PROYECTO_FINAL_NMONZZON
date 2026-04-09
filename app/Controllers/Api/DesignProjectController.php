<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * DESIGNPROJECTCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/DESIGNPROJECTCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\DesignProjectImageModel;
use App\Models\DesignProjectModel;

class DesignProjectController extends BaseApiController
{
    protected DesignProjectModel $projects;

    protected DesignProjectImageModel $images;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        helper('api');
        $this->projects = model(DesignProjectModel::class);
        $this->images   = model(DesignProjectImageModel::class);
    }

    public function index()
    {
        $rows = $this->projects->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return apiResponse($rows);
    }

    public function show($slug = null)
    {
        if ($slug === null || $slug === '') {
            return apiError('Not found', 404);
        }

        $project = $this->projects->where('slug', $slug)->where('is_active', 1)->first();
        if (! $project) {
            return apiError('Not found', 404);
        }

        $project['images'] = $this->images->getByProject((int) $project['id']);

        return apiResponse($project);
    }

    public function create()
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'title'          => 'required|max_length[200]',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,8192]|ext_in[featured_image,jpg,jpeg,png,gif,webp]',
            'description'    => 'permit_empty',
            'design_type'    => 'permit_empty|max_length[100]',
            'is_featured'    => 'permit_empty|in_list[0,1]',
            'sort_order'     => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $featuredPath = null;
        $file         = $this->request->getFile('featured_image');
        if ($file && $file->isValid()) {
            $dir = FCPATH . 'uploads/design/';
            if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
                return apiError('Could not create upload directory', 500);
            }
            $newName      = $file->getRandomName();
            $file->move($dir, $newName);
            $featuredPath = 'uploads/design/' . $newName;
        }

        $data = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description') ?: null,
            'design_type'   => $this->request->getPost('design_type') ?: null,
            'featured_image'=> $featuredPath,
            'is_featured'   => $this->request->getPost('is_featured') !== null && $this->request->getPost('is_featured') !== ''
                ? (int) $this->request->getPost('is_featured') : 0,
            'is_active'     => 1,
            'sort_order'    => $this->request->getPost('sort_order') !== '' && $this->request->getPost('sort_order') !== null
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $id = $this->projects->insert($data);
        if ($id === false) {
            return apiError('Could not create project', 500, $this->projects->errors());
        }

        return apiResponse($this->projects->find($id), 201, 'Created');
    }

    public function update($id = null)
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->projects->find($id)) {
            return apiError('Not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if ($payload === null) {
            $raw = $this->request->getRawInput();
            $payload = $raw !== '' ? (array) json_decode($raw, true) : [];
        }

        $rules = [
            'title'        => 'permit_empty|max_length[200]',
            'description'  => 'permit_empty',
            'design_type'  => 'permit_empty|max_length[100]',
            'is_featured'  => 'permit_empty|in_list[0,1]',
            'is_active'    => 'permit_empty|in_list[0,1]',
            'sort_order'   => 'permit_empty|integer',
        ];

        if (! $this->validateData($payload, $rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = array_filter(
            [
                'title'        => $payload['title'] ?? null,
                'description'  => $payload['description'] ?? null,
                'design_type'  => $payload['design_type'] ?? null,
                'is_featured'  => array_key_exists('is_featured', $payload) ? (int) $payload['is_featured'] : null,
                'is_active'    => array_key_exists('is_active', $payload) ? (int) $payload['is_active'] : null,
                'sort_order'   => isset($payload['sort_order']) ? (int) $payload['sort_order'] : null,
            ],
            static fn ($v) => $v !== null
        );

        if ($update === []) {
            return apiResponse($this->projects->find($id));
        }

        if (! $this->projects->update($id, $update)) {
            return apiError('Could not update', 500, $this->projects->errors());
        }

        return apiResponse($this->projects->find($id), 200, 'Updated');
    }

    public function delete($id = null)
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->projects->find($id)) {
            return apiError('Not found', 404);
        }

        if (! $this->projects->delete($id)) {
            return apiError('Could not delete', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }

    public function addImages($id = null)
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->projects->find($id)) {
            return apiError('Not found', 404);
        }

        $files = $this->request->getFileMultiple('images');
        if ($files === []) {
            $single = $this->request->getFile('images');
            $files  = $single && $single->isValid() ? [$single] : [];
        }

        if ($files === []) {
            return apiError('No images uploaded', 422);
        }

        $dir = FCPATH . 'uploads/design/gallery/';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            return apiError('Could not create upload directory', 500);
        }

        $created = [];
        $last    = $this->images->where('design_project_id', (int) $id)->orderBy('sort_order', 'DESC')->first();
        $sort    = (int) ($last['sort_order'] ?? 0);

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            if (! $file->isValid()) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $newName = $file->getRandomName();
            $file->move($dir, $newName);
            $url = 'uploads/design/gallery/' . $newName;
            ++$sort;

            $imgId = $this->images->insert([
                'design_project_id' => (int) $id,
                'image_url'         => $url,
                'alt_text'          => null,
                'sort_order'        => $sort,
            ]);

            if ($imgId !== false) {
                $created[] = $this->images->find($imgId);
            }
        }

        if ($created === []) {
            return apiError('Could not save images', 500);
        }

        return apiResponse($created, 201, 'Images added');
    }
}