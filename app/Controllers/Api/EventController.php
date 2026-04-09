<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * EVENTCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/EVENTCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\EventImageModel;
use App\Models\EventModel;

class EventController extends BaseApiController
{
    protected EventModel $events;

    protected EventImageModel $images;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        helper('api');
        $this->events = model(EventModel::class);
        $this->images = model(EventImageModel::class);
    }

    public function index()
    {
        $rows = $this->events->where('is_active', 1)
            ->orderBy('event_date', 'DESC')
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        return apiResponse($rows);
    }

    public function show($slug = null)
    {
        if ($slug === null || $slug === '') {
            return apiError('Not found', 404);
        }

        $event = $this->events->where('slug', $slug)->where('is_active', 1)->first();
        if (! $event) {
            return apiError('Not found', 404);
        }

        $event['images'] = $this->images->getByEvent((int) $event['id']);

        return apiResponse($event);
    }

    public function create()
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'title'          => 'required|max_length[200]',
            'event_date'     => 'required|valid_date',
            'featured_image' => 'uploaded[featured_image]|max_size[featured_image,8192]|ext_in[featured_image,jpg,jpeg,png,gif,webp]',
            'description'    => 'permit_empty',
            'event_type'     => 'permit_empty|max_length[100]',
            'location'       => 'permit_empty|max_length[255]',
            'is_featured'    => 'permit_empty|in_list[0,1]',
            'sort_order'     => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $featuredPath = null;
        $file         = $this->request->getFile('featured_image');
        if ($file && $file->isValid()) {
            $dir = FCPATH . 'uploads/events/';
            if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
                return apiError('Could not create upload directory', 500);
            }
            $newName      = $file->getRandomName();
            $file->move($dir, $newName);
            $featuredPath = 'uploads/events/' . $newName;
        }

        $data = [
            'title'          => $this->request->getPost('title'),
            'event_date'     => $this->request->getPost('event_date'),
            'description'    => $this->request->getPost('description') ?: null,
            'event_type'     => $this->request->getPost('event_type') ?: null,
            'location'       => $this->request->getPost('location') ?: null,
            'featured_image' => $featuredPath,
            'is_featured'    => $this->request->getPost('is_featured') !== null && $this->request->getPost('is_featured') !== ''
                ? (int) $this->request->getPost('is_featured') : 0,
            'is_active'      => 1,
            'sort_order'     => $this->request->getPost('sort_order') !== '' && $this->request->getPost('sort_order') !== null
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $id = $this->events->insert($data);
        if ($id === false) {
            return apiError('Could not create event', 500, $this->events->errors());
        }

        return apiResponse($this->events->find($id), 201, 'Created');
    }

    public function update($id = null)
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->events->find($id)) {
            return apiError('Not found', 404);
        }

        $payload = $this->request->getJSON(true);
        if ($payload === null) {
            $raw = $this->request->getRawInput();
            $payload = $raw !== '' ? (array) json_decode($raw, true) : [];
        }

        $rules = [
            'title'       => 'permit_empty|max_length[200]',
            'event_date'  => 'permit_empty|valid_date',
            'description' => 'permit_empty',
            'event_type'  => 'permit_empty|max_length[100]',
            'location'    => 'permit_empty|max_length[255]',
            'is_featured' => 'permit_empty|in_list[0,1]',
            'is_active'   => 'permit_empty|in_list[0,1]',
            'sort_order'  => 'permit_empty|integer',
        ];

        if (! $this->validateData($payload, $rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = array_filter(
            [
                'title'       => $payload['title'] ?? null,
                'event_date'  => $payload['event_date'] ?? null,
                'description' => $payload['description'] ?? null,
                'event_type'  => $payload['event_type'] ?? null,
                'location'    => $payload['location'] ?? null,
                'is_featured' => array_key_exists('is_featured', $payload) ? (int) $payload['is_featured'] : null,
                'is_active'   => array_key_exists('is_active', $payload) ? (int) $payload['is_active'] : null,
                'sort_order'  => isset($payload['sort_order']) ? (int) $payload['sort_order'] : null,
            ],
            static fn ($v) => $v !== null
        );

        if ($update === []) {
            return apiResponse($this->events->find($id));
        }

        if (! $this->events->update($id, $update)) {
            return apiError('Could not update', 500, $this->events->errors());
        }

        return apiResponse($this->events->find($id), 200, 'Updated');
    }

    public function delete($id = null)
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        if (! $this->events->find($id)) {
            return apiError('Not found', 404);
        }

        if (! $this->events->delete($id)) {
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

        if (! $this->events->find($id)) {
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

        $dir = FCPATH . 'uploads/events/gallery/';
        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            return apiError('Could not create upload directory', 500);
        }

        $created = [];
        $last    = $this->images->where('event_id', (int) $id)->orderBy('sort_order', 'DESC')->first();
        $sort    = (int) ($last['sort_order'] ?? 0);

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            if (! $file->isValid()) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $newName = $file->getRandomName();
            $file->move($dir, $newName);
            $url = 'uploads/events/gallery/' . $newName;
            ++$sort;

            $imgId = $this->images->insert([
                'event_id'   => (int) $id,
                'image_url'  => $url,
                'alt_text'   => null,
                'sort_order' => $sort,
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