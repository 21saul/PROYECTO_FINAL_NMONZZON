<?php

namespace App\Libraries;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use Config\Database;

/**
 * Resuelve productos de la tienda (prints/totebags) generados en disco o referenciados por imagen/slug
 * hacia la fila real en `products` para carrito y catálogo.
 */
class ShopCatalogCartResolver
{
    public static function inferCategorySlugFromPath(string $path): ?string
    {
        $n = strtolower(str_replace('\\', '/', trim($path)));
        if (str_contains($n, '/productos/prints/')) {
            return 'prints';
        }
        if (str_contains($n, '/productos/totebags/')) {
            return 'totebags';
        }

        return null;
    }

    /**
     * @param array<string, mixed> $p Debe incluir al menos featured_image y/o slug (p. ej. listado tienda o POST carrito).
     *
     * @return array<string, mixed>|null
     */
    public static function resolveDiskCatalogToDbRow(array $p): ?array
    {
        $productModel = new ProductModel();
        $img          = ltrim(str_replace('\\', '/', trim((string) ($p['featured_image'] ?? ''))), '/');

        $catSlug = isset($p['category_slug']) && is_string($p['category_slug']) ? $p['category_slug'] : null;
        if ($catSlug === null || $catSlug === '') {
            $catSlug = self::inferCategorySlugFromPath($img);
        }

        $categoryId = null;
        if ($catSlug === 'prints' || $catSlug === 'totebags') {
            $cat = (new CategoryModel())->where('slug', $catSlug)->where('is_active', 1)->first();
            if ($cat !== null) {
                $categoryId = (int) $cat['id'];
            }
        }

        if ($img !== '') {
            $imgNorm  = strtolower($img);
            $basename = basename($img);

            $row = $productModel->where('is_active', 1)->where('featured_image', $img)->first();
            if ($row !== null) {
                return $row;
            }

            if ($basename !== '' && $basename !== '.' && $basename !== '..') {
                $builder = $productModel->where('is_active', 1)->like('featured_image', $basename, 'before');
                if ($categoryId !== null) {
                    $builder->where('category_id', $categoryId);
                }
                $candidates = $builder->findAll(25);
                foreach ($candidates as $cand) {
                    $fi = strtolower(ltrim(str_replace('\\', '/', trim((string) ($cand['featured_image'] ?? ''))), '/'));
                    if ($fi === $imgNorm || str_ends_with($fi, '/' . strtolower($basename))) {
                        return $cand;
                    }
                }

                $db = Database::connect();
                $qb = $db->table('product_images pi')
                    ->select('p.*, pi.image_url AS match_image_url')
                    ->join('products p', 'p.id = pi.product_id', 'inner')
                    ->where('p.is_active', 1);
                if ($categoryId !== null) {
                    $qb->where('p.category_id', $categoryId);
                }
                $fromImages = $qb->groupStart()
                    ->where('pi.image_url', $img)
                    ->orLike('pi.image_url', $basename, 'before')
                    ->groupEnd()
                    ->limit(15)
                    ->get()
                    ->getResultArray();
                foreach ($fromImages as $cand) {
                    $matchImg = strtolower(ltrim(str_replace('\\', '/', trim((string) ($cand['match_image_url'] ?? ''))), '/'));
                    if ($matchImg === '' || $matchImg === '0') {
                        continue;
                    }
                    if ($matchImg === $imgNorm || str_ends_with($matchImg, '/' . strtolower($basename))) {
                        unset($cand['match_image_url']);

                        return $cand;
                    }
                    $fi = strtolower(ltrim(str_replace('\\', '/', (string) ($cand['featured_image'] ?? '')), '/'));
                    if ($fi !== '' && ($fi === $imgNorm || str_ends_with($fi, '/' . strtolower($basename)))) {
                        unset($cand['match_image_url']);

                        return $cand;
                    }
                }
            }
        }

        $slug = trim((string) ($p['slug'] ?? ''));
        if ($slug !== '') {
            foreach (self::diskShopSlugAliasesForDb($slug) as $candidateSlug) {
                $row = $productModel->where('is_active', 1)->where('slug', $candidateSlug)->first();
                if ($row !== null) {
                    return $row;
                }
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private static function diskShopSlugAliasesForDb(string $slug): array
    {
        $out = [$slug];
        if (str_starts_with($slug, 'prints-')) {
            $out[] = 'print-' . substr($slug, strlen('prints-'));
        }
        if (str_starts_with($slug, 'totebags-')) {
            $out[] = 'totebag-' . substr($slug, strlen('totebags-'));
        }

        return array_values(array_unique($out));
    }
}
