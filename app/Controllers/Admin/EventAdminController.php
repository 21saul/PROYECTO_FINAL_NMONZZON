<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * EVENTADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/EVENTADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\EventImageModel;
use App\Models\EventModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class EventAdminController extends BaseController
{
    protected EventModel $events;
    protected EventImageModel $images;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->events = model(EventModel::class);
        $this->images = model(EventImageModel::class);
    }

    protected function requireAdminSession(): ?ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/admin/login');
        }

        return null;
    }

    private function storeValidatedImage(UploadedFile $file, string $diskDir, string $urlPrefix): string|false
    {
        if ($file->getSize() > 5 * 1024 * 1024) {
            session()->setFlashdata('error', 'Image must be 5MB or smaller.');

            return false;
        }

        $mime    = (string) $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            session()->setFlashdata('error', 'Only JPEG, PNG, GIF, or WebP images are allowed.');

            return false;
        }

        $extByMime = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $extByMime[$mime] ?? preg_replace('/[^a-z0-9]/', '', strtolower((string) $file->getClientExtension())) ?: 'jpg';

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        if (! is_dir($diskDir) && ! mkdir($diskDir, 0755, true) && ! is_dir($diskDir)) {
            session()->setFlashdata('error', 'Could not create upload directory.');

            return false;
        }

        if (! $file->hasMoved() && ! $file->move($diskDir, $name)) {
            session()->setFlashdata('error', 'Failed to save image.');

            return false;
        }

        return $urlPrefix . $name;
    }

    /**
     * @param list<UploadedFile> $files
     */
    private function saveGalleryUploads(int $eventId, array $files, int $sortStart): void
    {
        $sort = $sortStart;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/events/', 'uploads/events/');
            if ($relative === false) {
                return;
            }
            $this->images->insert([
                'event_id'   => $eventId,
                'image_url'  => $relative,
                'alt_text'   => null,
                'sort_order' => $sort,
            ]);
            $sort++;
        }
    }

    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $data = [
            'title'  => 'Events',
            'events' => $this->events->orderBy('event_date', 'DESC')->orderBy('sort_order', 'ASC')->findAll(),
        ];

        return view('admin/events/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/events/form', [
            'title'  => 'Nuevo evento',
            'event'  => null,
            'images' => [],
        ]);
    }

    public function store()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'title'           => 'required|max_length[200]',
            'event_date'      => 'required|valid_date[Y-m-d]',
            'description'     => 'permit_empty',
            'event_type'      => 'permit_empty|max_length[100]',
            'location'        => 'permit_empty|max_length[255]',
            'slug'            => 'permit_empty|max_length[220]',
            'sort_order'      => 'permit_empty|integer',
            'featured_image'  => 'uploaded[featured_image]|max_size[featured_image,5120]',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $featured = $this->request->getFile('featured_image');
        if (! $featured instanceof UploadedFile || ! $featured->isValid()) {
            session()->setFlashdata('error', 'A valid featured image is required.');

            return redirect()->back()->withInput();
        }

        $featuredPath = $this->storeValidatedImage($featured, FCPATH . 'uploads/events/', 'uploads/events/');
        if ($featuredPath === false) {
            return redirect()->back()->withInput();
        }

        $title = (string) $this->request->getPost('title');
        $slug  = $this->request->getPost('slug');
        $slug  = $slug !== null && trim((string) $slug) !== '' ? trim((string) $slug) : strtolower(url_title($title, '-', true));

        $row = [
            'title'           => $title,
            'slug'            => $slug,
            'description'     => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'event_date'      => (string) $this->request->getPost('event_date'),
            'event_type'      => $this->request->getPost('event_type') !== null && (string) $this->request->getPost('event_type') !== ''
                ? (string) $this->request->getPost('event_type') : null,
            'location'        => $this->request->getPost('location') !== null && (string) $this->request->getPost('location') !== ''
                ? (string) $this->request->getPost('location') : null,
            'featured_image'  => $featuredPath,
            'is_featured'     => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'       => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'      => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $eventId = $this->events->insert($row, true);
        if ($eventId === false) {
            session()->setFlashdata('error', 'Could not create event.');

            return redirect()->back()->withInput();
        }

        $eventId = (int) $eventId;
        $gallery = $this->request->getFileMultiple('gallery') ?? [];
        $this->saveGalleryUploads($eventId, $gallery, 0);

        session()->setFlashdata('success', 'Event created.');

        return redirect()->to('/admin/events');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $event = $this->events->find($id);
        if ($event === null) {
            session()->setFlashdata('error', 'Event not found.');

            return redirect()->to('/admin/events');
        }

        $data = [
            'title'  => 'Editar evento',
            'event'  => $event,
            'images' => $this->images->getByEvent($id),
        ];

        return view('admin/events/form', $data);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->events->find($id) === null) {
            session()->setFlashdata('error', 'Event not found.');

            return redirect()->to('/admin/events');
        }

        $rules = [
            'title'       => 'required|max_length[200]',
            'event_date'  => 'required|valid_date[Y-m-d]',
            'description' => 'permit_empty',
            'event_type'  => 'permit_empty|max_length[100]',
            'location'    => 'permit_empty|max_length[255]',
            'slug'        => "permit_empty|max_length[220]|is_unique[events.slug,id,{$id}]",
            'sort_order'  => 'permit_empty|integer',
        ];

        $file = $this->request->getFile('featured_image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['featured_image'] = 'max_size[featured_image,5120]';
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $title = (string) $this->request->getPost('title');
        $slug  = $this->request->getPost('slug');
        $slug  = $slug !== null && trim((string) $slug) !== '' ? trim((string) $slug) : strtolower(url_title($title, '-', true));

        $row = [
            'title'       => $title,
            'slug'        => $slug,
            'description' => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'event_date'  => (string) $this->request->getPost('event_date'),
            'event_type'  => $this->request->getPost('event_type') !== null && (string) $this->request->getPost('event_type') !== ''
                ? (string) $this->request->getPost('event_type') : null,
            'location'    => $this->request->getPost('location') !== null && (string) $this->request->getPost('location') !== ''
                ? (string) $this->request->getPost('location') : null,
            'is_featured' => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'   => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'  => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $featuredPath = $this->storeValidatedImage($file, FCPATH . 'uploads/events/', 'uploads/events/');
            if ($featuredPath === false) {
                return redirect()->back()->withInput();
            }
            $row['featured_image'] = $featuredPath;
        }

        if (! $this->events->update($id, $row)) {
            session()->setFlashdata('error', 'Could not update event.');

            return redirect()->back()->withInput();
        }

        $deleteIds = $this->request->getPost('delete_image_ids');
        if (is_array($deleteIds)) {
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($deleteIds as $rawId) {
                $imgId = (int) $rawId;
                if ($imgId <= 0) {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }
                $imgRow = $this->images->find($imgId);
                if ($imgRow !== null && (int) ($imgRow['event_id'] ?? 0) === $id) {
                    $this->images->delete($imgId);
                }
            }
        }

        $gallery = $this->request->getFileMultiple('gallery') ?? [];
        $last    = $this->images->builder()->selectMax('sort_order')->where('event_id', $id)->get()->getRowArray();
        $maxSort = (int) ($last['sort_order'] ?? 0);
        $this->saveGalleryUploads($id, $gallery, $maxSort + 1);

        session()->setFlashdata('success', 'Event updated.');

        return redirect()->to('/admin/events');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->events->delete($id)) {
            session()->setFlashdata('success', 'Event removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove event.');
        }

        return redirect()->to('/admin/events');
    }

    public function toggleFeatured(int $id): ResponseInterface
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->events->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false]);
        }

        $on      = (int) $this->request->getPost('value') === 1 ? 1 : 0;
        $updated = $this->events->update($id, ['is_featured' => $on]);

        return $this->response->setJSON(['success' => (bool) $updated]);
    }
}