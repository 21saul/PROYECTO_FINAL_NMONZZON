<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CATEGORYADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/CATEGORYADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CategoryAdminController extends BaseController
{
    protected CategoryModel $categories;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->categories = model(CategoryModel::class);
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

        $data = [
            'title'      => 'Categories',
            'categories' => $this->categories->orderBy('sort_order', 'ASC')->orderBy('name', 'ASC')->findAll(),
        ];

        return view('admin/categories/index', $data);
    }

    public function new()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/categories/form', ['title' => 'New category', 'category' => null]);
    }

    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'name'        => 'required|max_length[100]',
            'slug'        => 'permit_empty|max_length[120]',
            'description' => 'permit_empty',
            'icon'        => 'permit_empty|max_length[100]',
            'sort_order'  => 'permit_empty|integer',
        ];

        $file = $this->request->getFile('image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['image'] = 'max_size[image,5120]';
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $imagePath = null;
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $stored = $this->storeValidatedImage($file, FCPATH . 'uploads/categories/', 'uploads/categories/');
            if ($stored === false) {
                return redirect()->back()->withInput();
            }
            $imagePath = $stored;
        }

        $slug = $this->request->getPost('slug');
        $slug = $slug !== null ? trim((string) $slug) : '';

        $row = [
            'name'             => (string) $this->request->getPost('name'),
            'slug'             => $slug,
            'description'      => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'image'            => $imagePath,
            'icon'             => $this->request->getPost('icon') !== null && (string) $this->request->getPost('icon') !== ''
                ? (string) $this->request->getPost('icon') : null,
            'sort_order'       => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
            'is_active'        => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'meta_title'       => $this->request->getPost('meta_title') !== null && (string) $this->request->getPost('meta_title') !== ''
                ? (string) $this->request->getPost('meta_title') : null,
            'meta_description' => $this->request->getPost('meta_description') !== null && (string) $this->request->getPost('meta_description') !== ''
                ? (string) $this->request->getPost('meta_description') : null,
        ];

        if ($this->categories->insert($row) === false) {
            session()->setFlashdata('error', 'Could not create category.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Category created.');

        return redirect()->to('/admin/categories');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $category = $this->categories->find($id);
        if ($category === null) {
            session()->setFlashdata('error', 'Category not found.');

            return redirect()->to('/admin/categories');
        }

        return view('admin/categories/form', [
            'title'    => 'Edit category',
            'category' => $category,
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $existing = $this->categories->find($id);
        if ($existing === null) {
            session()->setFlashdata('error', 'Category not found.');

            return redirect()->to('/admin/categories');
        }

        $rules = [
            'name'        => 'required|max_length[100]',
            'slug'        => "permit_empty|max_length[120]|is_unique[categories.slug,id,{$id}]",
            'description' => 'permit_empty',
            'icon'        => 'permit_empty|max_length[100]',
            'sort_order'  => 'permit_empty|integer',
        ];

        $file = $this->request->getFile('image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $rules['image'] = 'max_size[image,5120]';
        }

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $slug = $this->request->getPost('slug');
        $slug = $slug !== null ? trim((string) $slug) : '';

        $row = [
            'name'             => (string) $this->request->getPost('name'),
            'slug'             => $slug,
            'description'      => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'icon'             => $this->request->getPost('icon') !== null && (string) $this->request->getPost('icon') !== ''
                ? (string) $this->request->getPost('icon') : null,
            'sort_order'       => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
            'is_active'        => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'meta_title'       => $this->request->getPost('meta_title') !== null && (string) $this->request->getPost('meta_title') !== ''
                ? (string) $this->request->getPost('meta_title') : null,
            'meta_description' => $this->request->getPost('meta_description') !== null && (string) $this->request->getPost('meta_description') !== ''
                ? (string) $this->request->getPost('meta_description') : null,
        ];

        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $stored = $this->storeValidatedImage($file, FCPATH . 'uploads/categories/', 'uploads/categories/');
            if ($stored === false) {
                return redirect()->back()->withInput();
            }
            $row['image'] = $stored;
        }

        if (! $this->categories->update($id, $row)) {
            session()->setFlashdata('error', 'Could not update category.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Category updated.');

        return redirect()->to('/admin/categories');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->categories->delete($id)) {
            session()->setFlashdata('success', 'Category removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove category.');
        }

        return redirect()->to('/admin/categories');
    }
}