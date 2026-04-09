<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/PRODUCTCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE PRODUCTOS DE TIENDA: CATÁLOGO PÚBLICO PAGINADO, DETALLE POR SLUG Y CRUD + IMÁGENES PARA ADMIN.
namespace App\Controllers\Api;

use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ProductController extends BaseApiController
{
    protected ProductModel $productModel;
    protected ProductImageModel $imageModel;
    protected ProductVariantModel $variantModel;

    // INICIALIZA MODELOS DE PRODUCTO, IMÁGENES Y VARIANTES.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->productModel = model(ProductModel::class);
        $this->imageModel   = model(ProductImageModel::class);
        $this->variantModel = model(ProductVariantModel::class);
    }

    // LISTA PRODUCTOS ACTIVOS CON PAGINACIÓN, FILTROS Y ORDENACIÓN.
    public function index(): ResponseInterface
    {
        $perPage = (int) ($this->request->getGet('per_page') ?? 12);
        $perPage = max(1, min(100, $perPage));
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));

        $this->productModel->where('is_active', 1);

        $category = $this->request->getGet('category');
        if ($category !== null && $category !== '') {
            $this->productModel->where('category_id', (int) $category);
        }

        $minPrice = $this->request->getGet('min_price');
        if ($minPrice !== null && $minPrice !== '') {
            $this->productModel->where('price >=', (float) $minPrice);
        }

        $maxPrice = $this->request->getGet('max_price');
        if ($maxPrice !== null && $maxPrice !== '') {
            $this->productModel->where('price <=', (float) $maxPrice);
        }

        $featured = $this->request->getGet('featured');
        if ($featured !== null && $featured !== '' && filter_var($featured, FILTER_VALIDATE_BOOLEAN)) {
            $this->productModel->where('is_featured', 1);
        }

        $sort = (string) ($this->request->getGet('sort') ?? 'newest');
        // SELECCIÓN MÚLTIPLE SWITCH
        switch ($sort) {
            // CASO EN SWITCH
            case 'price_asc':
                $this->productModel->orderBy('price', 'ASC');
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO EN SWITCH
            case 'price_desc':
                $this->productModel->orderBy('price', 'DESC');
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO EN SWITCH
            case 'name':
                $this->productModel->orderBy('name', 'ASC');
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO POR DEFECTO EN SWITCH
            default:
                $this->productModel->orderBy('created_at', 'DESC');
                // INTERRUMPE BUCLE O SWITCH
                break;
        }

        $items = $this->productModel->paginate($perPage, 'default', $page);
        $pager = $this->productModel->pager;

        return apiResponse([
            'items' => $items,
            'pager' => [
                'current_page' => $pager->getCurrentPage(),
                'per_page'     => $pager->getPerPage(),
                'total'        => $pager->getTotal(),
                'page_count'   => $pager->getPageCount(),
            ],
        ], 200, 'OK');
    }

    // OBTIENE UN PRODUCTO POR SLUG CON IMÁGENES Y VARIANTES.
    public function show($slug = null): ResponseInterface
    {
        if ($slug === null || $slug === '') {
            return apiError('Slug is required', 400);
        }

        $slug = (string) $slug;

        $product = $this->productModel
            ->where('slug', $slug)
            ->where('is_active', 1)
            ->first();

        if (! $product) {
            return apiError('Product not found', 404);
        }

        $id = (int) $product['id'];

        $withImages = $this->productModel->getWithImages($id);
        if ($withImages === null) {
            $withImages = $product;
            $withImages['images'] = [];
        }

        $withImages['variants'] = $this->variantModel->getByProduct($id);

        return apiResponse($withImages, 200, 'OK');
    }

    // CREA UN PRODUCTO NUEVO (ADMIN).
    public function create(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'name'        => 'required|max_length[200]',
            'price'       => 'required|decimal',
            'category_id' => 'required|integer',
            'slug'        => 'permit_empty|max_length[200]|is_unique[products.slug]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $insert = [
            'name'         => $validated['name'],
            'slug'         => $validated['slug'] ?? '',
            'price'        => $validated['price'],
            'category_id'  => (int) $validated['category_id'],
            'is_active'    => 1,
            'description'  => $this->request->getVar('description'),
            'short_description' => $this->request->getVar('short_description'),
        ];

        $id = $this->productModel->insert($insert, true);
        if (! $id) {
            return apiError('Failed to create product', 500, $this->productModel->errors());
        }

        return apiResponse($this->productModel->find($id), 201, 'Created');
    }

    // ACTUALIZA CAMPOS DE UN PRODUCTO EXISTENTE (ADMIN).
    public function update($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->productModel->find($id)) {
            return apiError('Product not found', 404);
        }

        $rules = [
            'name'        => 'permit_empty|max_length[200]',
            'price'       => 'permit_empty|decimal',
            'category_id' => 'permit_empty|integer',
            'slug'        => "permit_empty|max_length[200]|is_unique[products.slug,id,{$id}]",
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['name', 'slug', 'price', 'category_id'] as $key) {
            if (array_key_exists($key, $validated) && $validated[$key] !== null && $validated[$key] !== '') {
                $update[$key] = $validated[$key];
            }
        }

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['description', 'short_description', 'is_featured', 'is_active', 'stock', 'sku'] as $key) {
            $v = $this->request->getVar($key);
            if ($v !== null) {
                $update[$key] = $v;
            }
        }

        if ($update === []) {
            return apiError('No valid fields to update', 422);
        }

        if (! $this->productModel->update($id, $update)) {
            return apiError('Update failed', 500, $this->productModel->errors());
        }

        return apiResponse($this->productModel->find($id), 200, 'Updated');
    }

    // ELIMINA (BORRADO LÓGICO SI APLICA EL MODELO) UN PRODUCTO (ADMIN).
    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->productModel->find($id)) {
            return apiError('Product not found', 404);
        }

        if (! $this->productModel->delete($id)) {
            return apiError('Delete failed', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }

    // SUBE UNA IMAGEN DE GALERÍA ASOCIADA AL PRODUCTO (ADMIN).
    public function addImage($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0 || ! $this->productModel->find($id)) {
            return apiError('Product not found', 404);
        }

        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return apiError('Valid file upload (field: file) is required', 422);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return apiError('File must be 10MB or smaller', 422);
        }

        $mime = $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            return apiError('Only JPEG, PNG, GIF, or WebP images are allowed', 422);
        }

        $uploadDir = FCPATH . 'uploads/products/';
        if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0755, true) && ! is_dir($uploadDir)) {
            return apiError('Upload directory is not available', 500);
        }

        $newName = $file->getRandomName();
        if (! $file->hasMoved() && ! $file->move($uploadDir, $newName)) {
            return apiError('Failed to store file', 500);
        }

        $relativePath = 'uploads/products/' . $newName;

        $sortOrder = (int) ($this->request->getVar('sort_order') ?? 0);
        $altText   = $this->request->getVar('alt_text');
        $isPrimary = filter_var($this->request->getVar('is_primary'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $imageId = $this->imageModel->insert([
            'product_id'  => $id,
            'image_url'   => $relativePath,
            'alt_text'    => $altText,
            'sort_order'  => $sortOrder,
            'is_primary'  => $isPrimary,
        ], true);

        if (! $imageId) {
            return apiError('Failed to save image record', 500, $this->imageModel->errors());
        }

        return apiResponse($this->imageModel->find($imageId), 201, 'Created');
    }

    // ELIMINA UN REGISTRO DE IMAGEN DE PRODUCTO VERIFICANDO QUE PERTENEZCA AL PRODUCTO (ADMIN).
    public function deleteImage($id = null, $imageId = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $productId = (int) $id;
        $imagePk   = (int) $imageId;

        if ($productId <= 0 || $imagePk <= 0) {
            return apiError('Invalid id', 400);
        }

        if (! $this->productModel->find($productId)) {
            return apiError('Product not found', 404);
        }

        $image = $this->imageModel->find($imagePk);
        if (! $image || (int) ($image['product_id'] ?? 0) !== $productId) {
            return apiError('Image not found', 404);
        }

        if (! $this->imageModel->delete($imagePk)) {
            return apiError('Delete failed', 500);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}