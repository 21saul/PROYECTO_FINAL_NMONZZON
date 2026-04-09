<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;
// IMPORTA UNA CLASE O TRAIT
use Config\ShopPrintCatalog;

// DECLARA UNA CLASE
class ProductImagesSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catModel     = model('CategoryModel');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $productModel = model('ProductModel');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $imgModel     = model('ProductImageModel');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $printsCat   = $catModel->where('slug', 'prints')->first();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebagsCat = $catModel->where('slug', 'totebags')->first();

        // CONDICIONAL SI
        if (!$printsCat || !$totebagsCat) {
            // EMITE SALIDA
            echo "ERROR: Ejecuta primero ProductCategorySeeder.\n";
            // RETORNA SIN VALOR
            return;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $printsId  = (int) $printsCat['id'];
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebagId = (int) $totebagsCat['id'];

        // COMENTARIO DE LÍNEA EXISTENTE
        // --- PRINTS ---
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $printsDir  = FCPATH . 'uploads/productos/prints/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $printFiles = $this->getImageFiles($printsDir);

        // EMITE SALIDA
        echo "Procesando " . count($printFiles) . " prints...\n";
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $created = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($printFiles as $order => $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/productos/prints/' . $file;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $num   = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug  = 'print-' . $this->safeSlug($num);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $title = ShopPrintCatalog::printTitleForFilename($file);

            // CONDICIONAL SI
            if ($productModel->where('slug', $slug)->first()) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $productId = $productModel->skipValidation(true)->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'       => $printsId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'              => $title,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => $slug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Lámina artística nmonzzon. Impresión de alta calidad sobre papel premium de 300g.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'short_description' => 'Lámina artística nmonzzon.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'price'             => $this->getPrintPrice($order + 1),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'stock'             => 50,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image'    => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'         => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'       => ($order < 8) ? 1 : 0,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ], true);

            // CONDICIONAL SI
            if ($productId) {
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $imgModel->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'product_id' => (int) $productId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'image_url'  => $path,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'alt_text'   => $title,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order' => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_primary' => 1,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $created++;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  ✓ {$created} prints creados.\n";

        // COMENTARIO DE LÍNEA EXISTENTE
        // --- TOTEBAGS ---
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebagDir   = FCPATH . 'uploads/productos/totebags/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebagFiles = $this->getImageFiles($totebagDir);

        // EMITE SALIDA
        echo "Procesando " . count($totebagFiles) . " totebags...\n";
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $created = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($totebagFiles as $order => $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/productos/totebags/' . $file;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $num  = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug = 'totebag-' . $this->safeSlug($num);

            // CONDICIONAL SI
            if ($productModel->where('slug', $slug)->first()) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $displayName = ucwords(str_replace(['_', '-'], ' ', $num));

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $productId = $productModel->skipValidation(true)->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'       => $totebagId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'              => 'Totebag nmonzzon — ' . $displayName,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => $slug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Bolsa de tela 100% algodón con diseño exclusivo nmonzzon. Ideal para el día a día.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'short_description' => 'Totebag algodón nmonzzon.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'price'             => 15.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'stock'             => 100,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image'    => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'         => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'       => 1,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ], true);

            // CONDICIONAL SI
            if ($productId) {
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $imgModel->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'product_id' => (int) $productId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'image_url'  => $path,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'alt_text'   => 'Totebag nmonzzon — ' . $displayName,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order' => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_primary' => 1,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $created++;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  ✓ {$created} totebags creados.\n";

        // ACTUALIZA NOMBRES DE LÁMINAS YA EXISTENTES (p. ej. antiguos «Print #1»)
        $this->syncExistingPrintTitles((int) $printsId);
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Corrige nombres en BD según el fichero de la imagen principal.
     */
    private function syncExistingPrintTitles(int $printsCategoryId): void
    {
        $rows = $this->db->table('products')
            ->where('category_id', $printsCategoryId)
            ->like('featured_image', 'uploads/productos/prints/', 'after')
            ->get()
            ->getResultArray();

        $updated = 0;
        foreach ($rows as $row) {
            $path = (string) ($row['featured_image'] ?? '');
            if ($path === '') {
                continue;
            }
            $file  = basename($path);
            $title = ShopPrintCatalog::printTitleForFilename($file);
            if (($row['name'] ?? '') === $title) {
                continue;
            }
            $this->db->table('products')->where('id', (int) $row['id'])->update([
                'name'       => $title,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->db->table('product_images')
                ->where('product_id', (int) $row['id'])
                ->where('is_primary', 1)
                ->update(['alt_text' => $title]);
            $updated++;
        }
        if ($updated > 0) {
            echo "  ✓ {$updated} nombres de láminas actualizados en la base de datos.\n";
        }
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function getImageFiles(string $dir): array
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!is_dir($dir)) {
            // RETORNA UN VALOR AL LLAMADOR
            return [];
        // DELIMITADOR DE BLOQUE
        }
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $files   = [];
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (scandir($dir) as $f) {
            // CONDICIONAL SI
            if ($f === '.' || $f === '..') {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            // DELIMITADOR DE BLOQUE
            }
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
            // CONDICIONAL SI
            if (in_array($ext, $allowed, true)) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $files[] = $f;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        sort($files);
        // RETORNA UN VALOR AL LLAMADOR
        return $files;
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function safeSlug(string $name): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $slug = strtolower(trim($name));
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        // RETORNA UN VALOR AL LLAMADOR
        return trim($slug, '-') ?: 'item';
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function getPrintPrice(int $index): float
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if ($index <= 20) return 25.00;
        // CONDICIONAL SI
        if ($index <= 40) return 30.00;
        // CONDICIONAL SI
        if ($index <= 55) return 35.00;
        // RETORNA UN VALOR AL LLAMADOR
        return 40.00;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
