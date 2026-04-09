<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTFOLIOADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/PORTFOLIOADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\PortfolioWorkModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PortfolioAdminController extends BaseController
{
    protected PortfolioWorkModel $portfolio;
    protected CategoryModel $categories;

    /**
     * CARGA HELPERS Y MODELOS DE PORTFOLIO Y CATEGORÍAS.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->portfolio   = model(PortfolioWorkModel::class);
        $this->categories  = model(CategoryModel::class);
    }

    /**
     * REDIRIGE AL LOGIN DE ADMIN SI LA SESIÓN NO ES DE ADMINISTRADOR.
     */
    protected function requireAdminSession(): ?ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/admin/login');
        }

        return null;
    }

    /**
     * GUARDA UNA IMAGEN VALIDADA (TAMAÑO, MIME) Y DEVUELVE RUTA RELATIVA URL O FALSE SI FALLA.
     *
     * @return string|false RUTA RELATIVA (EJ. uploads/portfolio/abc.jpg) O FALSE SI FALLA
     */
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
     * LISTADO PAGINADO CON FILTROS OPCIONALES POR CATEGORÍA Y ETIQUETA DE ESTILO.
     */
    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $perPage = 20;

        $catFilter = $this->request->getGet('category_id');
        $styleFilter = $this->request->getGet('style_tag');
        if ($catFilter) {
            $this->portfolio->where('category_id', (int)$catFilter);
        }
        if ($styleFilter) {
            $this->portfolio->where('style_tag', (string)$styleFilter);
        }

        $items = $this->portfolio->orderBy('created_at', 'DESC')->paginate($perPage);

        return view('admin/portfolio/index', [
            'title'      => 'Portfolio',
            'works'      => $items,
            'items'      => $items,
            'categories' => $this->categories->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'styles'     => ['color', 'byn', 'figurin', 'sin_caras', 'linea'],
            'pager'      => $this->portfolio->pager,
        ]);
    }

    /**
     * FORMULARIO VACÍO PARA CREAR UNA NUEVA OBRA DE PORTFOLIO.
     */
    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/portfolio/form', [
            'title'      => 'Crear obra',
            'work'       => null,
            'categories' => $this->categories->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'styles'     => ['color', 'byn', 'figurin', 'sin_caras', 'linea'],
        ]);
    }

    /**
     * PERSISTE NUEVA OBRA TRAS VALIDAR CAMPOS Y SUBIR IMAGEN OBLIGATORIA.
     */
    public function store()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|integer',
            'image'       => 'uploaded[image]|max_size[image,5120]',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $file = $this->request->getFile('image');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            session()->setFlashdata('error', 'A valid image is required.');

            return redirect()->back()->withInput();
        }

        $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/portfolio/', 'uploads/portfolio/');
        if ($relative === false) {
            return redirect()->back()->withInput();
        }

        $data = [
            'category_id'   => (int) $this->request->getPost('category_id'),
            'title'         => (string) $this->request->getPost('title'),
            'description'   => $this->request->getPost('description') !== null && $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'image_url'     => $relative,
            'thumbnail_url' => $relative,
            'style_tag'     => $this->request->getPost('style_tag') !== null && $this->request->getPost('style_tag') !== ''
                ? (string) $this->request->getPost('style_tag') : null,
            'is_featured'   => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'     => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'    => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        if ($this->portfolio->insert($data) === false) {
            session()->setFlashdata('error', 'Could not save portfolio work.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Portfolio work created.');

        return redirect()->to('/admin/portfolio');
    }

    /**
     * FORMULARIO DE EDICIÓN DE UNA OBRA EXISTENTE (INCLUYE BORRADAS LÓGICAS CON withDeleted).
     */
    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $work = $this->portfolio->withDeleted()->find($id);
        if ($work === null) {
            session()->setFlashdata('error', 'Work not found.');

            return redirect()->to('/admin/portfolio');
        }

        return view('admin/portfolio/form', [
            'title'      => 'Editar obra',
            'work'       => $work,
            'categories' => $this->categories->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'styles'     => ['color', 'byn', 'figurin', 'sin_caras', 'linea'],
        ]);
    }

    /**
     * ACTUALIZA METADATOS Y OPCIONALMENTE REEMPLAZA LA IMAGEN SI SE SUBE UN ARCHIVO NUEVO.
     */
    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $work = $this->portfolio->withDeleted()->find($id);
        if ($work === null) {
            session()->setFlashdata('error', 'Work not found.');

            return redirect()->to('/admin/portfolio');
        }

        $rules = [
            'title'       => 'required|max_length[200]',
            'category_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $data = [
            'category_id' => (int) $this->request->getPost('category_id'),
            'title'       => (string) $this->request->getPost('title'),
            'description' => $this->request->getPost('description') !== null && $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'style_tag'   => $this->request->getPost('style_tag') !== null && $this->request->getPost('style_tag') !== ''
                ? (string) $this->request->getPost('style_tag') : null,
            'is_featured' => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'   => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'sort_order'  => $this->request->getPost('sort_order') !== null && $this->request->getPost('sort_order') !== ''
                ? (int) $this->request->getPost('sort_order') : 0,
        ];

        $file = $this->request->getFile('image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $this->validate(['image' => 'max_size[image,5120]'])) {
                session()->setFlashdata('error', 'Please fix the errors below.');

                return redirect()->back()->withInput();
            }
            $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/portfolio/', 'uploads/portfolio/');
            if ($relative === false) {
                return redirect()->back()->withInput();
            }
            $data['image_url']     = $relative;
            $data['thumbnail_url'] = $relative;
        }

        if (! $this->portfolio->update($id, $data)) {
            session()->setFlashdata('error', 'Could not update portfolio work.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Portfolio work updated.');

        return redirect()->to('/admin/portfolio');
    }

    /**
     * ELIMINA (BORRADO LÓGICO SI EL MODELO LO SOPORTA) UNA OBRA POR ID.
     */
    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->portfolio->delete($id)) {
            session()->setFlashdata('success', 'Portfolio work removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove portfolio work.');
        }

        return redirect()->to('/admin/portfolio');
    }

    /**
     * ACTUALIZA VÍA AJAX EL CAMPO is_featured DE UNA OBRA.
     */
    public function toggleFeatured(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $work = $this->portfolio->find($id);
        if ($work === null) {
            return $this->response->setJSON(['success' => false]);
        }

        $value = (int) $this->request->getPost('value');
        $ok = $this->portfolio->update($id, ['is_featured' => $value]);

        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}