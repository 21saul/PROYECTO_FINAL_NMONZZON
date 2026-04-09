<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/PRODUCTADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ProductAdminController extends BaseController
{
    protected ProductModel $products;
    protected ProductImageModel $images;
    protected ProductVariantModel $variants;
    protected CategoryModel $categories;

    /**
     * REGISTRA MODELOS Y HELPERS NECESARIOS PARA FORMULARIOS DE PRODUCTO.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->products    = model(ProductModel::class);
        $this->images      = model(ProductImageModel::class);
        $this->variants    = model(ProductVariantModel::class);
        $this->categories  = model(CategoryModel::class);
    }

    /**
     * BLOQUEA EL ACCESO SI NO HAY SESIÓN DE ADMINISTRADOR VÁLIDA.
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
     * GUARDA UN ARCHIVO DE IMAGEN VALIDADO Y DEVUELVE SU RUTA WEB RELATIVA.
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
     * PROCESA MÚLTIPLES ARCHIVOS DE GALERÍA Y LOS INSERTA CON sort_order CRECIENTE DESDE $sortStart.
     *
     * @param list<UploadedFile> $files
     */
    private function saveGalleryUploads(int $productId, array $files, int $sortStart): int
    {
        $sort = $sortStart;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            if ($file->getSize() > 5 * 1024 * 1024) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/products/', 'uploads/products/');
            if ($relative === false) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $this->images->insert([
                'product_id'  => $productId,
                'image_url'   => $relative,
                'alt_text'    => null,
                'sort_order'  => $sort,
                'is_primary'  => 0,
            ]);
            $sort++;
        }

        return $sort;
    }

    /**
     * RECONSTRUYE LAS VARIANTES DEL PRODUCTO A PARTIR DE ARRAYS EN POST (BORRA LAS ANTERIORES SI HAY FILAS VÁLIDAS).
     */
    private function syncVariantsFromPost(int $productId): void
    {
        $names = $this->request->getPost('variant_name');
        if (! is_array($names)) {
            return;
        }

        $values = $this->request->getPost('variant_value');
        $mods   = $this->request->getPost('price_modifier');
        $stocks = $this->request->getPost('variant_stock');
        $skus   = $this->request->getPost('variant_sku');

        $hasRow = false;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($names as $i => $name) {
            $n = trim((string) $name);
            $v = trim((string) (is_array($values) ? ($values[$i] ?? '') : ''));
            if ($n !== '' && $v !== '') {
                $hasRow = true;
                // INTERRUMPE BUCLE O SWITCH
                break;
            }
        }
        if (! $hasRow) {
            return;
        }

        $this->variants->where('product_id', $productId)->delete();

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($names as $i => $name) {
            $name = trim((string) $name);
            if ($name === '') {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $value = trim((string) (is_array($values) ? ($values[$i] ?? '') : ''));
            if ($value === '') {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $modifier = is_array($mods) && isset($mods[$i]) && $mods[$i] !== ''
                ? (string) $mods[$i] : '0';
            $stock    = is_array($stocks) && isset($stocks[$i]) && $stocks[$i] !== ''
                ? (int) $stocks[$i] : 0;
            $sku      = is_array($skus) && isset($skus[$i]) && $skus[$i] !== ''
                ? (string) $skus[$i] : null;

            $this->variants->insert([
                'product_id'     => $productId,
                'variant_name'   => $name,
                'variant_value'  => $value,
                'price_modifier' => $modifier,
                'stock'          => $stock,
                'sku'            => $sku,
                'is_active'      => 1,
            ]);
        }
    }

    /**
     * Añade rango de precio tienda (base + modificadores de variantes activas) para alinear el listado con el carrito.
     *
     * @param list<array<string, mixed>> $products
     *
     * @return list<array<string, mixed>>
     */
    private function attachVariantPriceRangeForAdmin(array $products): array
    {
        if ($products === []) {
            return [];
        }

        $ids = [];
        foreach ($products as $p) {
            $id = (int) ($p['id'] ?? 0);
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        if ($ids === []) {
            return $products;
        }

        $variantRows = $this->variants->whereIn('product_id', $ids)->where('is_active', 1)->findAll();
        $byPid         = [];
        foreach ($variantRows as $v) {
            $pid           = (int) $v['product_id'];
            $byPid[$pid][] = $v;
        }

        foreach ($products as &$p) {
            $pid  = (int) ($p['id'] ?? 0);
            $base = (float) ($p['price'] ?? 0);
            $list = $byPid[$pid] ?? [];
            if ($list === []) {
                $p['admin_price_from'] = $base;
                $p['admin_price_to']   = $base;
            } else {
                $totals = [];
                foreach ($list as $v) {
                    $totals[] = $base + (float) ($v['price_modifier'] ?? 0);
                }
                $p['admin_price_from'] = min($totals);
                $p['admin_price_to']   = max($totals);
            }
        }
        unset($p);

        return $products;
    }

    /**
     * LISTADO PAGINADO DE PRODUCTOS ORDENADOS POR FECHA DE CREACIÓN.
     */
    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $perPage = 48;

        $this->products
            ->select(
                'products.*, categories.name AS category_name, '
                . '(SELECT pi.image_url FROM product_images pi WHERE pi.product_id = products.id '
                . 'ORDER BY pi.is_primary DESC, pi.sort_order ASC LIMIT 1) AS primary_gallery_image',
                false
            )
            ->join('categories', 'categories.id = products.category_id', 'left')
            ->orderBy('products.created_at', 'DESC');

        $products = $this->products->paginate($perPage);
        $products = $this->attachVariantPriceRangeForAdmin($products);

        return view('admin/products/index', [
            'title'    => 'Productos',
            'products' => $products,
            'pager'    => $this->products->pager,
        ]);
    }

    /**
     * FORMULARIO DE ALTA DE PRODUCTO CON CATEGORÍAS ACTIVAS.
     */
    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/products/form', [
            'title'      => 'Crear producto',
            'product'    => null,
            'categories' => $this->categories->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'images'     => [],
            'variants'   => [],
        ]);
    }

    /**
     * CREA PRODUCTO CON IMAGEN PRINCIPAL, REGISTRO PRIMARY EN GALERÍA, EXTRAS Y VARIANTES.
     */
    public function store()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'name'        => 'required|max_length[200]',
            'price'       => 'required|decimal',
            'category_id' => 'required|integer',
            'slug'        => 'permit_empty|max_length[200]',
            'main_image'  => 'uploaded[main_image]|max_size[main_image,5120]',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $main = $this->request->getFile('main_image');
        if (! $main instanceof UploadedFile || ! $main->isValid()) {
            session()->setFlashdata('error', 'A valid main image is required.');

            return redirect()->back()->withInput();
        }

        $featuredPath = $this->storeValidatedImage($main, FCPATH . 'uploads/products/', 'uploads/products/');
        if ($featuredPath === false) {
            return redirect()->back()->withInput();
        }

        $slug = $this->request->getPost('slug');
        $slug = $slug !== null && trim((string) $slug) !== '' ? trim((string) $slug) : null;

        $data = [
            'name'               => (string) $this->request->getPost('name'),
            'slug'               => $slug ?? '',
            'price'              => (string) $this->request->getPost('price'),
            'category_id'        => (int) $this->request->getPost('category_id'),
            'description'        => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'short_description'  => $this->request->getPost('short_description') !== null && (string) $this->request->getPost('short_description') !== ''
                ? (string) $this->request->getPost('short_description') : null,
            'featured_image'     => $featuredPath,
            'is_featured'        => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'          => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'stock'              => $this->request->getPost('stock') !== null && $this->request->getPost('stock') !== ''
                ? (int) $this->request->getPost('stock') : 0,
            'sku'                => $this->request->getPost('sku') !== null && (string) $this->request->getPost('sku') !== ''
                ? (string) $this->request->getPost('sku') : null,
        ];

        $productId = $this->products->insert($data, true);
        if ($productId === false) {
            session()->setFlashdata('error', 'Could not create product.');

            return redirect()->back()->withInput();
        }

        $productId = (int) $productId;

        $this->images->insert([
            'product_id' => $productId,
            'image_url'  => $featuredPath,
            'alt_text'   => (string) $this->request->getPost('name'),
            'sort_order' => 0,
            'is_primary' => 1,
        ]);

        $extra = $this->request->getFileMultiple('images') ?? [];
        $this->saveGalleryUploads($productId, $extra, 1);

        $this->syncVariantsFromPost($productId);

        session()->setFlashdata('success', 'Product created.');

        return redirect()->to('/admin/products');
    }

    /**
     * FORMULARIO DE EDICIÓN CON IMÁGENES Y VARIANTES CARGADAS.
     */
    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $product = $this->products->getWithImages($id);
        if ($product === null) {
            session()->setFlashdata('error', 'Product not found.');

            return redirect()->to('/admin/products');
        }

        $productImages = $product['images'] ?? $this->images->where('product_id', $id)->orderBy('sort_order', 'ASC')->findAll();
        $productVariants = $this->variants->where('product_id', $id)->findAll();

        return view('admin/products/form', [
            'title'      => 'Editar producto',
            'product'    => $product,
            'categories' => $this->categories->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll(),
            'images'     => $productImages,
            'variants'   => $productVariants,
        ]);
    }

    /**
     * ACTUALIZA PRODUCTO, IMAGEN PRINCIPAL OPCIONAL, BORRADO DE IMÁGENES DE GALERÍA, NUEVAS SUBIDAS Y VARIANTES.
     */
    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->products->find($id) === null) {
            session()->setFlashdata('error', 'Product not found.');

            return redirect()->to('/admin/products');
        }

        $rules = [
            'name'        => 'required|max_length[200]',
            'price'       => 'required|decimal',
            'category_id' => 'required|integer',
            'slug'        => "permit_empty|max_length[200]|is_unique[products.slug,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $file = $this->request->getFile('main_image');
        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $this->validate(['main_image' => 'max_size[main_image,5120]'])) {
                session()->setFlashdata('error', 'Please fix the errors below.');

                return redirect()->back()->withInput();
            }
        }

        $slug = $this->request->getPost('slug');
        $slug = $slug !== null && trim((string) $slug) !== '' ? trim((string) $slug) : null;

        $data = [
            'name'               => (string) $this->request->getPost('name'),
            'slug'               => $slug ?? '',
            'price'              => (string) $this->request->getPost('price'),
            'category_id'        => (int) $this->request->getPost('category_id'),
            'description'        => $this->request->getPost('description') !== null && (string) $this->request->getPost('description') !== ''
                ? (string) $this->request->getPost('description') : null,
            'short_description'  => $this->request->getPost('short_description') !== null && (string) $this->request->getPost('short_description') !== ''
                ? (string) $this->request->getPost('short_description') : null,
            'is_featured'        => (int) ($this->request->getPost('is_featured') ? 1 : 0),
            'is_active'          => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
            'stock'              => $this->request->getPost('stock') !== null && $this->request->getPost('stock') !== ''
                ? (int) $this->request->getPost('stock') : 0,
            'sku'                => $this->request->getPost('sku') !== null && (string) $this->request->getPost('sku') !== ''
                ? (string) $this->request->getPost('sku') : null,
        ];

        if ($file instanceof UploadedFile && $file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            $featuredPath = $this->storeValidatedImage($file, FCPATH . 'uploads/products/', 'uploads/products/');
            if ($featuredPath === false) {
                return redirect()->back()->withInput();
            }
            $data['featured_image'] = $featuredPath;

            $primary = $this->images->where('product_id', $id)->where('is_primary', 1)->first();
            if ($primary !== null) {
                $this->images->update((int) $primary['id'], ['image_url' => $featuredPath]);
            } else {
                $this->images->insert([
                    'product_id'  => $id,
                    'image_url'   => $featuredPath,
                    'alt_text'    => (string) $this->request->getPost('name'),
                    'sort_order'  => 0,
                    'is_primary'  => 1,
                ]);
            }
        }

        if (! $this->products->update($id, $data)) {
            session()->setFlashdata('error', 'Could not update product.');

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
                $row = $this->images->find($imgId);
                if ($row !== null && (int) ($row['product_id'] ?? 0) === $id) {
                    $this->images->delete($imgId);
                }
            }
        }

        $extra = $this->request->getFileMultiple('images') ?? [];
        $row   = $this->images->builder()->selectMax('sort_order')->where('product_id', $id)->get()->getRowArray();
        $maxSort = (int) ($row['sort_order'] ?? 0);
        $this->saveGalleryUploads($id, $extra, $maxSort + 1);

        $this->syncVariantsFromPost($id);

        session()->setFlashdata('success', 'Product updated.');

        return redirect()->to('/admin/products');
    }

    /**
     * ELIMINA UN PRODUCTO (SEGÚN COMPORTAMIENTO DEL MODELO).
     */
    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->products->delete($id)) {
            session()->setFlashdata('success', 'Product removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove product.');
        }

        return redirect()->to('/admin/products');
    }

    /**
     * ACTUALIZA is_active DEL PRODUCTO VÍA POST AJAX.
     */
    public function toggleActive(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $product = $this->products->find($id);
        if ($product === null) {
            return $this->response->setJSON(['success' => false]);
        }

        $value = (int) $this->request->getPost('value');
        $ok = $this->products->update($id, ['is_active' => $value]);

        return $this->response->setJSON(['success' => (bool) $ok]);
    }
}