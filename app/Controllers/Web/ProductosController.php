<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PRODUCTOSCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/PRODUCTOSCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Libraries\CartService;
use App\Libraries\ShopCatalogCartResolver;
use Config\ShopPrintCatalog;
use App\Models\CategoryModel;
use App\Models\ProductImageModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ProductosController extends BaseController
{
    private const SHOP_CATEGORY_SLUGS = ['prints', 'totebags'];
    private const ALLOWED_EXTENSIONS  = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    public function index()
    {
        $request = $this->request;

        $catSlug = $request->getGet('categoria');
        $catSlug = is_string($catSlug) && $catSlug !== '' ? $catSlug : null;

        $orden = (string) $request->getGet('orden');
        $orden = in_array($orden, ['precio_asc', 'precio_desc', 'recientes', 'destacados'], true)
            ? $orden : 'recientes';

        $categoryModel  = new CategoryModel();
        $shopCategories = $categoryModel
            ->whereIn('slug', self::SHOP_CATEGORY_SLUGS)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        if (empty($shopCategories)) {
            $shopCategories = [
                ['id' => 0, 'name' => 'Prints',   'slug' => 'prints',   'is_active' => 1, 'sort_order' => 1],
                ['id' => 0, 'name' => 'Totebags', 'slug' => 'totebags', 'is_active' => 1, 'sort_order' => 2],
            ];
        }

        $dbProducts = $this->getDbProducts($shopCategories, $catSlug, $orden);
        $diskProducts = $this->getProductsFromDisk($catSlug);

        $dbImageIndex = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($dbProducts as &$p) {
            $img = $p['featured_image'] ?? '';
            if ($img !== '') {
                $basename = basename($img);
                $dbImageIndex[$basename] = true;
            }
            if (empty($p['featured_image']) || !file_exists(FCPATH . $p['featured_image'])) {
                $match = $this->findDiskImageForProduct($p);
                if ($match !== null) {
                    $p['featured_image'] = $match;
                }
            }
        }
        unset($p);

        $autoId = -1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($diskProducts as $dp) {
            $basename = basename($dp['featured_image']);
            if (isset($dbImageIndex[$basename])) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $dp['id'] = $autoId--;
            $dbProducts[] = $dp;
        }

        $allProducts = $this->sortProducts($dbProducts, $orden);
        $allProducts = $this->enrichShopCatalogProducts($allProducts);

        foreach ($allProducts as &$p) {
            $p['card_title'] = $this->resolveShopProductCardTitle($p);
        }
        unset($p);

        $perPage     = 24;
        $currentPage = max(1, (int) $request->getGet('page'));
        $total       = count($allProducts);
        $totalPages  = max(1, (int) ceil($total / $perPage));
        $currentPage = min($currentPage, $totalPages);
        $offset      = ($currentPage - 1) * $perPage;
        $products    = array_slice($allProducts, $offset, $perPage);

        return view('web/productos/index', [
            'title'            => 'Tienda',
            'meta_title'       => 'Tienda — Prints y Totebags',
            'meta_description' => 'Prints artísticos y totebags con diseños exclusivos de nmonzzon Studio.',
            'products'         => $products,
            'categories'       => $shopCategories,
            'selectedCategory' => $catSlug,
            'currentOrder'     => $orden,
            'currentPage'      => $currentPage,
            'totalPages'       => $totalPages,
            'totalProducts'    => $total,
        ]);
    }

    public function show(string $slug)
    {
        $productModel = new ProductModel();
        $row = $productModel->where('slug', $slug)->where('is_active', 1)->first();

        if ($row === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $product = $productModel->getWithImages((int) $row['id']);
        if ($product === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        $product['name'] = $this->resolveShopProductCardTitle($product);

        $variantModel = new ProductVariantModel();
        $variants = $variantModel
            ->where('product_id', (int) $row['id'])
            ->where('is_active', 1)
            ->orderBy('variant_name', 'ASC')
            ->findAll();

        $imageModel = new ProductImageModel();
        $images = $product['images'] ?? $imageModel->getByProduct((int) $row['id']);

        $related = $productModel
            ->where('is_active', 1)
            ->where('category_id', (int) $row['category_id'])
            ->where('id !=', (int) $row['id'])
            ->orderBy('RAND()')
            ->limit(4)
            ->findAll();

        foreach ($related as &$rp) {
            $rp['name'] = $this->resolveShopProductCardTitle($rp);
        }
        unset($rp);

        return view('web/productos/show', [
            'title'            => $product['name'] ?? 'Producto',
            'meta_title'       => $product['name'] ?? 'Producto',
            'meta_description' => $product['short_description'] ?? 'Producto artístico nmonzzon.',
            'og_image'         => !empty($product['featured_image']) ? base_url($product['featured_image']) : null,
            'product'          => $product,
            'images'           => $images,
            'variants'         => $variants,
            'relatedProducts'  => $related,
        ]);
    }

    private function getDbProducts(array $shopCategories, ?string $catSlug, string $orden): array
    {
        $shopCategoryIds = array_map(fn($c) => (int) $c['id'], $shopCategories);
        $shopCategoryIds = array_filter($shopCategoryIds, fn($id) => $id > 0);

        if (empty($shopCategoryIds)) {
            return [];
        }

        $productModel = new ProductModel();
        $builder = $productModel->where('is_active', 1)->whereIn('category_id', $shopCategoryIds);

        if ($catSlug !== null) {
            $categoryModel = new CategoryModel();
            $selectedCat = $categoryModel->where('slug', $catSlug)->where('is_active', 1)->first();
            if ($selectedCat) {
                $builder->where('category_id', (int) $selectedCat['id']);
            }
        }

        return $builder->findAll() ?: [];
    }

    private function getProductsFromDisk(?string $categorySlug): array
    {
        $products = [];

        $dirs = [
            'prints'   => 'uploads/productos/prints/',
            'totebags' => 'uploads/productos/totebags/',
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($dirs as $slug => $relPath) {
            if ($categorySlug !== null && $categorySlug !== $slug) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $absDir = FCPATH . $relPath;
            if (!is_dir($absDir)) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $files = @scandir($absDir);
            if ($files === false) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $price = ($slug === 'prints') ? 25.00 : 15.00;

            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }

                $name     = pathinfo($f, PATHINFO_FILENAME);
                $safeName = preg_replace('/[^a-z0-9]+/', '-', strtolower($name));
                $safeName = trim($safeName, '-') ?: 'item';

                $displayName = $slug === 'prints'
                    ? ShopPrintCatalog::printTitleForFilename($f)
                    : ShopPrintCatalog::totebagTitleForFilename($f);

                $products[] = [
                    'id'             => 0,
                    'name'           => $displayName,
                    'slug'           => $slug . '-' . $safeName,
                    'price'          => $price,
                    'stock'          => 50,
                    'featured_image' => $relPath . $f,
                    'is_active'      => 1,
                    'is_featured'    => 0,
                    'category_slug'  => $slug,
                ];
            }
        }

        return $products;
    }

    /**
     * Título en tarjetas de la tienda: según imagen principal (prints/totebags) o nombre en BD.
     */
    private function resolveShopProductCardTitle(array $product): string
    {
        $img = (string) ($product['featured_image'] ?? '');
        if ($img !== '') {
            if (str_contains($img, 'uploads/productos/prints/')) {
                return ShopPrintCatalog::printTitleForFilename(basename($img));
            }
            if (str_contains($img, 'uploads/productos/totebags/')) {
                return ShopPrintCatalog::totebagTitleForFilename(basename($img));
            }
        }

        return (string) ($product['name'] ?? 'Producto');
    }

    private function findDiskImageForProduct(array $product): ?string
    {
        $slug = $product['slug'] ?? '';
        $name = $product['name'] ?? '';

        $dirs = [
            'uploads/productos/prints/',
            'uploads/productos/totebags/',
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($dirs as $relPath) {
            $absDir = FCPATH . $relPath;
            if (!is_dir($absDir)) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            $files = @scandir($absDir);
            if ($files === false) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }
                $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                    // SALTA A LA SIGUIENTE ITERACIÓN
                    continue;
                }
                $base = strtolower(pathinfo($f, PATHINFO_FILENAME));
                if (str_contains($slug, $base) || str_contains(strtolower($name), $base)) {
                    return $relPath . $f;
                }
            }
        }

        return null;
    }

    /**
     * Enlaza filas solo-disco a productos reales por imagen y calcula stock vendible (padre + variantes).
     *
     * @param list<array<string, mixed>> $products
     *
     * @return list<array<string, mixed>>
     */
    private function enrichShopCatalogProducts(array $products): array
    {
        foreach ($products as &$p) {
            $pid = (int) ($p['id'] ?? 0);

            if ($pid <= 0) {
                $row = ShopCatalogCartResolver::resolveDiskCatalogToDbRow($p);
                if ($row !== null) {
                    $p['id']    = (int) $row['id'];
                    $p['slug']  = (string) ($row['slug'] ?? $p['slug']);
                    $p['price'] = (float) ($row['price'] ?? $p['price']);
                    $pid        = (int) $p['id'];
                }
            }

            if ($pid > 0) {
                $p['catalog_stock'] = $this->effectiveShopStock($pid);
            } else {
                $imgRel = (string) ($p['featured_image'] ?? '');
                $isShop = str_contains($imgRel, 'uploads/productos/prints/')
                    || str_contains($imgRel, 'uploads/productos/totebags/');
                // Sin fila en BD no se puede cobrar por el carrito: no mostrar stock ficticio del listado en disco.
                $p['catalog_stock'] = $isShop ? 0 : (int) ($p['stock'] ?? 0);
            }
        }
        unset($p);

        return $products;
    }

    /**
     * Stock mostrado en grid / carrito rápido: misma regla que CartService al añadir sin variant_id.
     */
    private function effectiveShopStock(int $productId): int
    {
        return CartService::catalogQuickAddStock($productId);
    }

    private function sortProducts(array $products, string $orden): array
    {
        // SELECCIÓN MÚLTIPLE SWITCH
        switch ($orden) {
            // CASO EN SWITCH
            case 'precio_asc':
                usort($products, fn($a, $b) => ((float)($a['price'] ?? 0)) <=> ((float)($b['price'] ?? 0)));
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO EN SWITCH
            case 'precio_desc':
                usort($products, fn($a, $b) => ((float)($b['price'] ?? 0)) <=> ((float)($a['price'] ?? 0)));
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO EN SWITCH
            case 'destacados':
                usort($products, fn($a, $b) => ((int)($b['is_featured'] ?? 0)) <=> ((int)($a['is_featured'] ?? 0)));
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO POR DEFECTO EN SWITCH
            default:
                // INTERRUMPE BUCLE O SWITCH
                break;
        }
        return $products;
    }
}