<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * TESTIMONIALADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/TESTIMONIALADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TestimonialModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class TestimonialAdminController extends BaseController
{
    protected TestimonialModel $testimonials;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->testimonials = model(TestimonialModel::class);
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

    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/testimonials/index', [
            'title'         => 'Testimonials',
            'testimonials'  => $this->testimonials->orderBy('sort_order', 'ASC')->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function new()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/testimonials/form', ['title' => 'New testimonial', 'testimonial' => null]);
    }

    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'client_name'  => 'required|max_length[100]',
            'content'      => 'required',
            'rating'       => 'required|integer|greater_than[0]|less_than[6]',
            'service_type' => 'permit_empty|max_length[100]',
            'sort_order'   => 'permit_empty|integer',
        ];

        $avatar = $this->request->getFile('avatar');
        if ($avatar instanceof UploadedFile && $avatar->isValid() && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['avatar'] = 'max_size[avatar,5120]';
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $clientImage = null;
        if ($avatar instanceof UploadedFile && $avatar->isValid() && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $stored = $this->storeValidatedImage($avatar, FCPATH . 'uploads/testimonials/', 'uploads/testimonials/');
            if ($stored === false) {
                return redirect()->back()->withInput();
            }
            $clientImage = $stored;
        }

        $row = [
            'client_name'  => (string) $this->request->getPost('client_name'),
            'client_image' => $clientImage,
            'service_type' => $this->request->getPost('service_type') !== null && (string) $this->request->getPost('service_type') !== ''
                ? (string) $this->request->getPost('service_type') : null,
            'rating'       => (int) $this->request->getPost('rating'),
            'content'      => (string) $this->request->getPost('content'),
            'is_featured'  => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'    => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'   => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        if ($this->testimonials->insert($row) === false) {
            session()->setFlashdata('error', 'Could not create testimonial.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Testimonial created.');

        return redirect()->to('/admin/testimonials');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $testimonial = $this->testimonials->find($id);
        if ($testimonial === null) {
            session()->setFlashdata('error', 'Testimonial not found.');

            return redirect()->to('/admin/testimonials');
        }

        return view('admin/testimonials/form', [
            'title'       => 'Edit testimonial',
            'testimonial' => $testimonial,
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->testimonials->find($id) === null) {
            session()->setFlashdata('error', 'Testimonial not found.');

            return redirect()->to('/admin/testimonials');
        }

        $rules = [
            'client_name'  => 'required|max_length[100]',
            'content'      => 'required',
            'rating'       => 'required|integer|greater_than[0]|less_than[6]',
            'service_type' => 'permit_empty|max_length[100]',
            'sort_order'   => 'permit_empty|integer',
        ];

        $avatar = $this->request->getFile('avatar');
        if ($avatar instanceof UploadedFile && $avatar->isValid() && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['avatar'] = 'max_size[avatar,5120]';
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $row = [
            'client_name'  => (string) $this->request->getPost('client_name'),
            'service_type' => $this->request->getPost('service_type') !== null && (string) $this->request->getPost('service_type') !== ''
                ? (string) $this->request->getPost('service_type') : null,
            'rating'       => (int) $this->request->getPost('rating'),
            'content'      => (string) $this->request->getPost('content'),
            'is_featured'  => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'    => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'   => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        if ($avatar instanceof UploadedFile && $avatar->isValid() && $avatar->getError() !== UPLOAD_ERR_NO_FILE) {
            $stored = $this->storeValidatedImage($avatar, FCPATH . 'uploads/testimonials/', 'uploads/testimonials/');
            if ($stored === false) {
                return redirect()->back()->withInput();
            }
            $row['client_image'] = $stored;
        }

        if (! $this->testimonials->update($id, $row)) {
            session()->setFlashdata('error', 'Could not update testimonial.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Testimonial updated.');

        return redirect()->to('/admin/testimonials');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->testimonials->delete($id)) {
            session()->setFlashdata('success', 'Testimonial removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove testimonial.');
        }

        return redirect()->to('/admin/testimonials');
    }

    public function toggleFeatured(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if ($this->testimonials->find($id) === null) {
            return $this->response->setJSON(['success' => false]);
        }

        $value = (int) $this->request->getPost('value');
        $ok    = $this->testimonials->skipValidation(true)->update($id, ['is_featured' => $value ? 1 : 0]);

        return $this->response->setJSON(['success' => $ok]);
    }
}