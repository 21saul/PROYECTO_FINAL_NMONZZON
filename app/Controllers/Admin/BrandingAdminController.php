<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * BRANDINGADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/BRANDINGADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BrandingProjectImageModel;
use App\Models\BrandingProjectModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BrandingAdminController extends BaseController
{
    protected BrandingProjectModel $projects;
    protected BrandingProjectImageModel $images;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->projects = model(BrandingProjectModel::class);
        $this->images   = model(BrandingProjectImageModel::class);
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

        $mime = (string) $file->getMimeType();
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
     * @return array{project: array<string, mixed>, images: list<array<string, mixed>>}|null
     */
    private function foldProjectWithImages(int $id): ?array
    {
        $rows = $this->projects->getWithImages($id);
        if ($rows === []) {
            return null;
        }

        $first = $rows[0];
        $skip  = ['image_row_id', 'image_url', 'alt_text', 'image_sort_order'];

        $project = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($first as $key => $value) {
            if (in_array($key, $skip, true)) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $project[$key] = $value;
        }

        $images = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $row) {
            if (($row['image_row_id'] ?? null) === null) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $images[] = [
                'id'         => (int) $row['image_row_id'],
                'image_url'  => (string) ($row['image_url'] ?? ''),
                'alt_text'   => $row['alt_text'] ?? null,
                'sort_order' => (int) ($row['image_sort_order'] ?? 0),
            ];
        }

        return ['project' => $project, 'images' => $images];
    }

    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $projects = $this->projects->orderBy('sort_order', 'ASC')->orderBy('created_at', 'DESC')->findAll();

        return view('admin/branding/index', [
            'title'    => 'Branding projects',
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/branding/form', [
            'title'   => 'Nuevo proyecto de branding',
            'project' => null,
            'images'  => [],
        ]);
    }

    public function store()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'title'           => 'required|max_length[200]',
            'featured_image'  => 'uploaded[featured_image]|max_size[featured_image,5120]',
            'slug'            => 'permit_empty|max_length[200]',
            'client_name'     => 'permit_empty|max_length[200]',
            'description'     => 'permit_empty',
            'services_provided' => 'permit_empty',
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

        $featuredPath = $this->storeValidatedImage($featured, FCPATH . 'uploads/branding/', 'uploads/branding/');
        if ($featuredPath === false) {
            return redirect()->back()->withInput();
        }

        $data = [
            'title'             => (string) $this->request->getPost('title'),
            'slug'              => $this->request->getPost('slug') !== null && trim((string) $this->request->getPost('slug')) !== ''
                ? trim((string) $this->request->getPost('slug')) : '',
            'client_name'       => $this->request->getPost('client_name') !== null && (string) $this->request->getPost('client_name') !== ''
                ? (string) $this->request->getPost('client_name') : null,
            'description'       => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'services_provided' => $this->request->getPost('services_provided') !== null && (string) $this->request->getPost('services_provided') !== ''
                ? (string) $this->request->getPost('services_provided') : null,
            'featured_image'    => $featuredPath,
            'is_featured'       => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'         => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'        => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $projectId = $this->projects->insert($data, true);
        if ($projectId === false) {
            session()->setFlashdata('error', 'Could not create project.');

            return redirect()->back()->withInput();
        }

        $projectId = (int) $projectId;
        $sort      = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($this->request->getFileMultiple('gallery') ?? [] as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $url = $this->storeValidatedImage($file, FCPATH . 'uploads/branding/gallery/', 'uploads/branding/gallery/');
            if ($url === false) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $this->images->insert([
                'branding_project_id' => $projectId,
                'image_url'           => $url,
                'alt_text'            => null,
                'sort_order'          => $sort,
            ]);
            $sort++;
        }

        session()->setFlashdata('success', 'Branding project created.');

        return redirect()->to('/admin/branding');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $folded = $this->foldProjectWithImages($id);
        if ($folded === null) {
            session()->setFlashdata('error', 'Project not found.');

            return redirect()->to('/admin/branding');
        }

        return view('admin/branding/form', [
            'title'   => 'Editar proyecto de branding',
            'project' => $folded['project'],
            'images'  => $folded['images'],
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->projects->find($id) === null) {
            session()->setFlashdata('error', 'Project not found.');

            return redirect()->to('/admin/branding');
        }

        $rules = [
            'title'           => 'required|max_length[200]',
            'slug'            => "permit_empty|max_length[200]|is_unique[branding_projects.slug,id,{$id}]",
            'client_name'     => 'permit_empty|max_length[200]',
            'description'     => 'permit_empty',
            'services_provided' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $file = $this->request->getFile('featured_image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $this->validate(['featured_image' => 'max_size[featured_image,5120]'])) {
                session()->setFlashdata('error', 'Please fix the errors below.');

                return redirect()->back()->withInput();
            }
        }

        $data = [
            'title'             => (string) $this->request->getPost('title'),
            'slug'              => $this->request->getPost('slug') !== null && trim((string) $this->request->getPost('slug')) !== ''
                ? trim((string) $this->request->getPost('slug')) : '',
            'client_name'       => $this->request->getPost('client_name') !== null && (string) $this->request->getPost('client_name') !== ''
                ? (string) $this->request->getPost('client_name') : null,
            'description'       => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'services_provided' => $this->request->getPost('services_provided') !== null && (string) $this->request->getPost('services_provided') !== ''
                ? (string) $this->request->getPost('services_provided') : null,
            'is_featured'       => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'         => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'        => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $featuredPath = $this->storeValidatedImage($file, FCPATH . 'uploads/branding/', 'uploads/branding/');
            if ($featuredPath === false) {
                return redirect()->back()->withInput();
            }
            $data['featured_image'] = $featuredPath;
        }

        if (! $this->projects->update($id, $data)) {
            session()->setFlashdata('error', 'Could not update project.');

            return redirect()->back()->withInput();
        }

        $deleteIds = $this->request->getPost('delete_image_ids');
        if (is_array($deleteIds)) {
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($deleteIds as $raw) {
                $imgId = (int) $raw;
                if ($imgId <= 0) {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }
                $row = $this->images->find($imgId);
                if ($row !== null && (int) ($row['branding_project_id'] ?? 0) === $id) {
                    $this->images->delete($imgId);
                }
            }
        }

        $row   = $this->images->builder()->selectMax('sort_order')->where('branding_project_id', $id)->get()->getRowArray();
        $sort  = (int) ($row['sort_order'] ?? -1) + 1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($this->request->getFileMultiple('gallery') ?? [] as $gfile) {
            if (! $gfile instanceof UploadedFile || ! $gfile->isValid() || $gfile->getError() === UPLOAD_ERR_NO_FILE) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            if ($gfile->getSize() > 5 * 1024 * 1024) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $url = $this->storeValidatedImage($gfile, FCPATH . 'uploads/branding/gallery/', 'uploads/branding/gallery/');
            if ($url === false) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $this->images->insert([
                'branding_project_id' => $id,
                'image_url'           => $url,
                'alt_text'            => null,
                'sort_order'          => $sort,
            ]);
            $sort++;
        }

        session()->setFlashdata('success', 'Branding project updated.');

        return redirect()->to('/admin/branding');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->projects->delete($id)) {
            session()->setFlashdata('success', 'Branding project removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove project.');
        }

        return redirect()->to('/admin/branding');
    }

    public function toggleFeatured(int $id): ResponseInterface
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->projects->find($id) === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false]);
        }

        $on       = (int) $this->request->getPost('value') === 1 ? 1 : 0;
        $updated  = $this->projects->update($id, ['is_featured' => $on]);

        return $this->response->setJSON(['success' => (bool) $updated]);
    }
}