<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class AllImagesSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private array $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        helper('url');

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->fixExistingPaths();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->ensureCategories();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedPortfolioWorks();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedClientPortraits();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedLiveArtGallery();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedBrandingProjects();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedDesignProjects();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedEvents();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedPortraitStyles();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedLogos();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->seedSiteUniqueImages();

        // EMITE SALIDA
        echo "--- Todas las imagenes han sido referenciadas en la BD. ---\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Corrige rutas existentes que no incluyen el prefijo uploads/.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function fixExistingPaths(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tables = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portfolio_works' => ['image_url', 'thumbnail_url'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_styles' => ['sample_image'],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fixed = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($tables as $table => $cols) {
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($cols as $col) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $rows = $db->query(
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    "SELECT id, {$col} FROM {$table} WHERE {$col} IS NOT NULL AND {$col} != '' AND {$col} NOT LIKE 'uploads/%' AND {$col} NOT LIKE 'http%'"
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                )->getResultArray();

                // BUCLE FOREACH SOBRE COLECCIÓN
                foreach ($rows as $row) {
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    $newPath = 'uploads/' . $row[$col];
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    $db->query("UPDATE {$table} SET {$col} = ? WHERE id = ?", [$newPath, $row['id']]);
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    $fixed++;
                // DELIMITADOR DE BLOQUE
                }
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $settingsRows = $db->query(
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            "SELECT id, `key`, value FROM site_settings WHERE value IS NOT NULL AND value != '' AND value NOT LIKE 'uploads/%' AND value NOT LIKE 'http%' AND `type` = 'image'"
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        )->getResultArray();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($settingsRows as $row) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $db->query("UPDATE site_settings SET value = ? WHERE id = ?", ['uploads/' . $row['value'], $row['id']]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $fixed++;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $settingsImgLike = $db->query(
            // INSTRUCCIÓN O DECLARACIÓN PHP
            "SELECT id, `key`, value FROM site_settings WHERE value LIKE '%.png' OR value LIKE '%.jpg' OR value LIKE '%.jpeg' OR value LIKE '%.webp' OR value LIKE '%.gif'"
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        )->getResultArray();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($settingsImgLike as $row) {
            // CONDICIONAL SI
            if (strpos($row['value'], 'uploads/') !== 0 && strpos($row['value'], 'http') !== 0) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $db->query("UPDATE site_settings SET value = ? WHERE id = ?", ['uploads/' . $row['value'], $row['id']]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $fixed++;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // EMITE SALIDA
        echo "  Rutas corregidas: {$fixed}\n";
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function ensureCategories(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $needed = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Retratos',      'slug' => 'retratos',      'sort_order' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Arte en Vivo',   'slug' => 'arte-en-vivo',  'sort_order' => 2],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Branding',       'slug' => 'branding',      'sort_order' => 3],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Eventos',        'slug' => 'eventos',       'sort_order' => 4],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Diseño',         'slug' => 'diseno',        'sort_order' => 5],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Productos',      'slug' => 'productos',     'sort_order' => 6],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Portfolio',      'slug' => 'portfolio',     'sort_order' => 7],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($needed as $cat) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $existing = $db->query("SELECT id FROM categories WHERE slug = ? AND deleted_at IS NULL", [$cat['slug']])->getRow();
            // CONDICIONAL SI
            if (!$existing) {
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $db->table('categories')->insert(array_merge($cat, [
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_active'  => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at' => date('Y-m-d H:i:s'),
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'updated_at' => date('Y-m-d H:i:s'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]));
                // EMITE SALIDA
                echo "  Categoria creada: {$cat['name']}\n";
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function getCategoryId(string $slug): int
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $row = $db->query("SELECT id FROM categories WHERE slug = ? AND deleted_at IS NULL", [$slug])->getRow();
        // RETORNA UN VALOR AL LLAMADOR
        return $row ? (int) $row->id : 1;
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Portfolio: 67 archivos de imagen (numerados + variantes .png / _edited).
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedPortfolioWorks(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $this->getCategoryId('productos');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $dir = FCPATH . 'uploads/portfolio/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $files = $this->getImageFiles($dir);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $added = 0;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $maxSort = $db->query("SELECT COALESCE(MAX(sort_order), 0) as m FROM portfolio_works")->getRow()->m;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = (int) $maxSort + 1;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/portfolio/' . $file;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $exists = $db->query("SELECT id FROM portfolio_works WHERE image_url = ? AND deleted_at IS NULL", [$path])->getRow();
            // CONDICIONAL SI
            if ($exists) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $num = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = 'obra-' . strtolower(preg_replace('/[^a-z0-9]/i', '-', $num));
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = $this->uniqueSlug($db, 'portfolio_works', $safeSlug);

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'   => $catId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'         => 'Obra ' . $num,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'          => $safeSlug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'   => 'Trabajo artistico del portfolio de nmonzzon.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'     => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'thumbnail_url' => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'   => ($order <= 8) ? 1 : 0,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'     => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'    => $order,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'    => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'    => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $order++;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $added++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Portfolio: {$added} obras nuevas anadidas.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Retratos/clientes: 31 imágenes. Añadir las que falten.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedClientPortraits(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $this->getCategoryId('retratos');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $dir = FCPATH . 'uploads/retratos/clientes/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $files = $this->getImageFiles($dir);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $added = 0;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $maxSort = $db->query("SELECT COALESCE(MAX(sort_order), 0) as m FROM portfolio_works WHERE category_id = ?", [$catId])->getRow()->m;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = (int) $maxSort + 1;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/retratos/clientes/' . $file;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $exists = $db->query("SELECT id FROM portfolio_works WHERE image_url = ? AND deleted_at IS NULL", [$path])->getRow();
            // CONDICIONAL SI
            if ($exists) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = str_replace(['_edited', '_'], [' ', ' '], $name);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = ucwords(trim($name));

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = 'retrato-' . strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = trim($safeSlug, '-');
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = $this->uniqueSlug($db, 'portfolio_works', $safeSlug);

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'   => $catId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'         => 'Retrato - ' . $name,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'          => $safeSlug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'   => 'Retrato personalizado para ' . $name . '.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'     => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'thumbnail_url' => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'style_tag'     => 'retrato',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'   => ($order <= 6) ? 1 : 0,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'     => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'    => $order,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'    => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'    => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $order++;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $added++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Retratos clientes: {$added} nuevos anadidos.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Live-art: imágenes de arte en vivo → portfolio_works con cat "Arte en Vivo".
     * Ignora .heic. No duplica archivos _edited si el original ya está.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedLiveArtGallery(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $this->getCategoryId('arte-en-vivo');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $dir = FCPATH . 'uploads/live-art/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $files = $this->getImageFiles($dir);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $added = 0;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $maxSort = $db->query("SELECT COALESCE(MAX(sort_order), 0) as m FROM portfolio_works WHERE category_id = ?", [$catId])->getRow()->m;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = max((int) $maxSort + 1, 200);

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($files as $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/live-art/' . $file;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $exists = $db->query("SELECT id FROM portfolio_works WHERE image_url = ? AND deleted_at IS NULL", [$path])->getRow();
            // CONDICIONAL SI
            if ($exists) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $base = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = str_replace(['_jpg', '_edited', 'IMG_', '_'], ['', '', '', ' '], $base);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = trim($name) ?: 'Live Art ' . $order;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = 'live-art-' . $order . '-' . substr(md5($file), 0, 6);

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'   => $catId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'         => 'Arte en Vivo - ' . $name,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'          => $safeSlug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'   => 'Momento capturado durante un evento de arte en vivo.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'     => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'thumbnail_url' => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'style_tag'     => 'live-art',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'   => ($added < 6) ? 1 : 0,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'     => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'    => $order,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'    => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'    => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $order++;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $added++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Live art: {$added} obras anadidas.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Branding: Aldea (cartelería), Teatro Enxebre, mockups y NUBA Matcha — sin mezclar contenidos.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedBrandingProjects(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db  = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $now = date('Y-m-d H:i:s');

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['abeja-dorada-identidad', 'ilustraciones-mockups'] as $obsoleteSlug) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $old = $db->table('branding_projects')->where('slug', $obsoleteSlug)->where('deleted_at', null)->get()->getRow();
            // CONDICIONAL SI
            if ($old) {
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_project_images')->where('branding_project_id', $old->id)->delete();
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->where('id', $old->id)->update(['deleted_at' => $now, 'updated_at' => $now]);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $ju = JSON_UNESCAPED_UNICODE;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $projects = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'             => 'Aldea do Apalpador — Cartelería',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => 'aldea-apalpador-carteleria',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name'       => 'Aldea do Apalpador',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Carteles y piezas de comunicación gráfica para la ruta de la Aldea do Apalpador: impresión, redes y soportes informativos del evento. Contenido exclusivo de esta campaña.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'services_provided' => json_encode(['Cartelería', 'Diseño gráfico', 'Redes sociales'], $ju),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'            => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Cartel Aldea Apalpador 2022.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Cartel Aldea Apalpador. 2022.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Cartel A3 portada.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Cartel A3.jpg',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'             => 'Teatro Enxebre — Gráfica de sala',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => 'teatro-enxebre',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name'       => 'Teatro Enxebre',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Apoyo gráfico para la programación y la comunicación del Teatro Enxebre: piezas digitales y cartelería del espacio escénico. Es un proyecto aparte de la cartelería de la Aldea do Apalpador u otras campañas.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'services_provided' => json_encode(['Cartelería', 'Redes sociales', 'Programación de sala'], $ju),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image'    => 'assets/images/placeholder.svg',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'            => [],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'             => 'Mockups — Presentación digital',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => 'mockups-presentacion-digital',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name'       => '',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Mockups digitales para presentar productos editoriales en redes o dossiers: escenas con libro y packaging. Galería limitada a este tipo de piezas, sin mezclar otros encargos.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'services_provided' => json_encode(['Mockups', 'Presentación en redes', 'Producto digital'], $ju),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'            => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Black and Beige Simple Book Mockup Instagram Post-2.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Grey Black Modern Digital Product  Mockup Shadow Instagram Post 2.JPG',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Grey Black Modern Digital Product  Mockup Shadow Instagram Post-3.jpg',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'             => 'NUBA Matcha — Packaging',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => 'nuba-matcha-packaging',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name'       => 'NUBA Matcha',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => 'Diseño de packaging y piezas gráficas para NUBA Matcha: envases, coherencia cromática y aplicaciones de marca específicas del producto.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'services_provided' => json_encode(['Packaging', 'Identidad de producto', 'Papelería'], $ju),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'            => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'matcha.png',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sortProject = 1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($projects as $proj) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $featured = $proj['featured_image'] ?? null;
            // CONDICIONAL SI
            if ($featured === null && ($proj['images'] ?? []) !== []) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $real0 = $this->findFileInDir(FCPATH . 'uploads/branding/', $proj['images'][0]);
                // CONDICIONAL SI
                if ($real0) {
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    $featured = 'uploads/branding/' . $real0;
                // DELIMITADOR DE BLOQUE
                }
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $existing = $db->table('branding_projects')->where('slug', $proj['slug'])->where('deleted_at', null)->get()->getRow();
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $row      = [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'             => $proj['title'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'              => $proj['slug'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'client_name'       => $proj['client_name'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'       => $proj['description'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'services_provided' => $proj['services_provided'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'featured_image'    => $featured,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'       => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'         => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'        => $sortProject,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'        => $now,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ];

            // CONDICIONAL SI
            if ($existing) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $projId = (int) $existing->id;
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->where('id', $projId)->update($row);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $row['created_at'] = $now;
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->insert($row);
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $projId = (int) $db->insertID();
            // DELIMITADOR DE BLOQUE
            }

            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $db->table('branding_project_images')->where('branding_project_id', $projId)->delete();
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $sortImg = 1;
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($proj['images'] ?? [] as $img) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $realFile = $this->findFileInDir(FCPATH . 'uploads/branding/', $img);
                // CONDICIONAL SI
                if (! $realFile) {
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    continue;
                // DELIMITADOR DE BLOQUE
                }
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $imgPath = 'uploads/branding/' . $realFile;
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_project_images')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'branding_project_id' => $projId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'image_url'           => $imgPath,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'alt_text'            => $proj['title'] . ' — ' . $sortImg,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order'          => $sortImg,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at'          => $now,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $sortImg++;
            // DELIMITADOR DE BLOQUE
            }
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $sortProject++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo '  Branding: ' . count($projects) . " proyectos sincronizados.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Diseño: 15 imágenes (excluye PDF) → 2 proyectos con galerías.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedDesignProjects(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $projects = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'       => 'Libro Apalpador — diseño editorial',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'        => 'libro-apalpador-editorial',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => "Diseño e ilustración del libro infantil «El Apalpador», un proyecto editorial para la Aldea do Apalpador: maquetación de interiores, portada e ilustraciones, con archivos preparados para imprenta y una línea gráfica unificada.\n\nHistoria (resumida): el Apalpador es un personaje del imaginario infantil vinculado a la cultura gallega y al entorno rural; el libro lo presenta con un lenguaje cercano para familias y escuelas, y una narrativa visual que acompaña la lectura de principio a fin.",
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'design_type' => 'editorial',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'      => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador_page-0001.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador2.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador3.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador4.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador5.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador6.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador7.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Libro Apalpador8.jpg',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // Proyecto textil / ropa
            [
                'title'       => 'Diseño textil — ropa y merchandising',
                'slug'        => 'diseno-textil-ropa',
                'description' => "Diseño gráfico aplicado a ropa y complementos: ilustración para camiseta (motivo reloj de arena), mockup en perchero y tote bag con ilustración tipo tinta.\n\nArchivos pensados para imprenta textil o proveedor, con línea gráfica unificada sobre tejidos claros.",
                'design_type' => 'textil',
                'images'      => [
                    'diseno-textil-camiseta-modelo.png',
                    'diseno-textil-camiseta-mockup.png',
                    'diseno-textil-tote-erizo.png',
                ],
            ],
            [
                'title'       => 'Os Gatos — cartel e entradas (Teatro Enxebre)',
                'slug'        => 'os-gatos-teatro-enxebre',
                'description' => "Piezas gráficas para la obra de teatro de la Asociación Cultural Enxebre (Teatro Enxebre), con texto de Agustín Gómez Arcos. Cartel A3 y dos entradas con talón desprendible para las funciones del 4 y 5 de febrero de 2023 en el Auditorio do Grove.\n\nLínea visual oscura y dramática, tipografía y tratamiento de imagen (retrato del gato, ojos, salpicaduras), integración de código QR, logotipos institucionales y textos en gallego; maquetación pensada para impresión.",
                'design_type' => 'eventos',
                'images'      => [
                    'diseno-os-gatos-cartel-a3.png',
                    'diseno-os-gatos-entrada-sabado.png',
                    'diseno-os-gatos-entrada-domingo.png',
                ],
            ],
            [
                'title'       => 'Agora Quero — Skadelos',
                'slug'        => 'agora-quero-skadelos',
                'description' => "Arte de portada para el lanzamiento «Agora Quero» de Skadelos: ilustración en blanco y negro con trazo de tinta y sombreado a trazo, sobre fondo con textura de papel.\n\nComposición centrada en un corazón anatómico, manos, bocina de gramófono y detalles simbólicos; tipografía serif para título y firma del artista. Pieza cuadrada pensada para carátula digital (streaming y redes).",
                'design_type' => 'musica',
                'images'      => [
                    'diseno-agora-quero-skadelos.png',
                ],
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sortProject = 1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($projects as $proj) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $existing = $db->query("SELECT id FROM design_projects WHERE slug = ? AND deleted_at IS NULL", [$proj['slug']])->getRow();
            // CONDICIONAL SI
            if ($existing) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $projId = (int) $existing->id;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $firstImg = $this->findFileInDir(FCPATH . 'uploads/diseno/', $proj['images'][0]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $db->table('design_projects')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'title'          => $proj['title'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'slug'           => $proj['slug'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'description'    => $proj['description'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'design_type'    => $proj['design_type'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'featured_image' => $firstImg ? ('uploads/diseno/' . $firstImg) : '',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_featured'    => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_active'      => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order'     => $sortProject,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at'     => date('Y-m-d H:i:s'),
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'updated_at'     => date('Y-m-d H:i:s'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $projId = (int) $db->insertID();
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $sortImg = 1;
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($proj['images'] as $img) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $realFile = $this->findFileInDir(FCPATH . 'uploads/diseno/', $img);
                // CONDICIONAL SI
                if (!$realFile) continue;
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $ext = strtolower(pathinfo($realFile, PATHINFO_EXTENSION));
                // CONDICIONAL SI
                if ($ext === 'pdf') continue;

                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $imgPath = 'uploads/diseno/' . $realFile;
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $imgExists = $db->query(
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    "SELECT id FROM design_project_images WHERE design_project_id = ? AND image_url = ?",
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    [$projId, $imgPath]
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                )->getRow();

                // CONDICIONAL SI
                if (!$imgExists) {
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    $db->table('design_project_images')->insert([
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'design_project_id' => $projId,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'image_url'         => $imgPath,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'alt_text'          => $proj['title'] . ' - ' . $sortImg,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'sort_order'        => $sortImg,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'created_at'        => date('Y-m-d H:i:s'),
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    ]);
                // DELIMITADOR DE BLOQUE
                }
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $sortImg++;
            // DELIMITADOR DE BLOQUE
            }
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $sortProject++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Diseno: " . count($projects) . " proyectos con imagenes.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Eventos: 5 imágenes → 2 eventos con galerías.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedEvents(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $events = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'       => 'Os Gatos - Evento Musical',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'        => 'os-gatos-evento',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Evento musical en Os Gatos con arte en vivo y venta de entradas ilustradas.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'event_type'  => 'festival',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'event_date'  => '2023-02-04',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'location'    => 'Os Gatos, Vigo',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'      => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Entradas _Os Gatos_ Domingo 05.02.23.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Entradas _Os Gatos_ Sábado 04.02.23.jpg',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'       => 'Festival Skadelos & Valle Fragoso',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'        => 'skadelos-valle-fragoso',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Participacion artistica en los festivales Skadelos y Valle Fragoso.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'event_type'  => 'festival',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'event_date'  => '2023-07-15',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'location'    => 'Vigo y alrededores',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'images'      => [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Skadelos 2.jpg',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Valle Fragoso 2.png',
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    'Valle Fragoso.png',
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sortEvent = 1;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($events as $evt) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $existing = $db->query("SELECT id FROM events WHERE slug = ? AND deleted_at IS NULL", [$evt['slug']])->getRow();
            // CONDICIONAL SI
            if ($existing) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $eventId = (int) $existing->id;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $firstImg = $this->findFileInDir(FCPATH . 'uploads/eventos/', $evt['images'][0]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $db->table('events')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'title'          => $evt['title'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'slug'           => $evt['slug'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'description'    => $evt['description'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'event_type'     => $evt['event_type'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'event_date'     => $evt['event_date'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'location'       => $evt['location'],
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'featured_image' => $firstImg ? ('uploads/eventos/' . $firstImg) : '',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_featured'    => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'is_active'      => 1,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order'     => $sortEvent,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at'     => date('Y-m-d H:i:s'),
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'updated_at'     => date('Y-m-d H:i:s'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $eventId = (int) $db->insertID();
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $sortImg = 1;
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($evt['images'] as $img) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $realFile = $this->findFileInDir(FCPATH . 'uploads/eventos/', $img);
                // CONDICIONAL SI
                if (!$realFile) continue;

                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $imgPath = 'uploads/eventos/' . $realFile;
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $imgExists = $db->query(
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    "SELECT id FROM event_images WHERE event_id = ? AND image_url = ?",
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    [$eventId, $imgPath]
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                )->getRow();

                // CONDICIONAL SI
                if (!$imgExists) {
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    $db->table('event_images')->insert([
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'event_id'   => $eventId,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'image_url'  => $imgPath,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'alt_text'   => $evt['title'] . ' - ' . $sortImg,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'sort_order' => $sortImg,
                        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                        'created_at' => date('Y-m-d H:i:s'),
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    ]);
                // DELIMITADOR DE BLOQUE
                }
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $sortImg++;
            // DELIMITADOR DE BLOQUE
            }
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $sortEvent++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Eventos: " . count($events) . " con imagenes.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Estilos de retrato: actualiza sample_image y añade extras al portfolio.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedPortraitStyles(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $mappings = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'color-todo-detalle'          => 'uploads/retratos/estilos/estilo_color.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sin-caras'                   => 'uploads/retratos/estilos/estilo_sin_caras.jpg',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'figurin'                     => 'uploads/retratos/estilos/estilo_figurin.jpg',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'blanco-y-negro-todo-detalle' => 'uploads/retratos/estilos/sandra_maceira.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'a-linea'                     => 'uploads/retratos/estilos/elisa_goris_a_linea.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($mappings as $slug => $imgPath) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $style = $db->query("SELECT id, sample_image FROM portrait_styles WHERE slug = ? AND deleted_at IS NULL", [$slug])->getRow();
            // CONDICIONAL SI
            if ($style) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $db->query("UPDATE portrait_styles SET sample_image = ?, updated_at = ? WHERE id = ?", [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    $imgPath, date('Y-m-d H:i:s'), $style->id,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $extraStyleImages = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'estilo_color_sin_caras.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'estilo_figurin_alt.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'estilo_sin_caras_alt.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'retratos_hero.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $this->getCategoryId('retratos');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = 300;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $added = 0;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($extraStyleImages as $img) {
            // CONDICIONAL SI
            if (!file_exists(FCPATH . 'uploads/retratos/estilos/' . $img)) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/retratos/estilos/' . $img;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $exists = $db->query("SELECT id FROM portfolio_works WHERE image_url = ? AND deleted_at IS NULL", [$path])->getRow();
            // CONDICIONAL SI
            if ($exists) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = str_replace(['estilo_', '_', '.jpg', '.png'], [' ', ' ', '', ''], $img);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = 'estilo-' . $order . '-' . substr(md5($img), 0, 6);

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'   => $catId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'         => 'Estilo - ' . ucwords(trim($name)),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'          => $safeSlug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'   => 'Ejemplo de estilo de retrato.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'     => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'thumbnail_url' => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'     => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'    => $order,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'    => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'    => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $order++;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $added++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Estilos de retrato actualizados + {$added} extras al portfolio.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Logos: guarda las rutas en site_settings.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedLogos(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $logoSettings = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'site_logo'            => 'nmonzzon.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'site_logo_white'      => 'nmonzzon en blanco.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'logo_grey'            => 'nmonzzon gris.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'logo_negative'        => 'Negativo.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'logo_positive_circle' => 'Positivo círculo.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'logo_positive_fg'     => 'Positivo FG.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'logo_nmonzzon_jpg'    => 'nmonzzon.JPG',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($logoSettings as $key => $filename) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $realFile = $this->findFileInDir(FCPATH . 'uploads/logos/', $filename);
            // CONDICIONAL SI
            if (!$realFile) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $value = 'uploads/logos/' . $realFile;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $existing = $db->query("SELECT id, value FROM site_settings WHERE `key` = ?", [$key])->getRow();

            // CONDICIONAL SI
            if ($existing) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $db->query("UPDATE site_settings SET value = ?, updated_at = ? WHERE id = ?", [
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    $value, date('Y-m-d H:i:s'), $existing->id,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $db->table('site_settings')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'key'        => $key,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'value'      => $value,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'type'       => 'image',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'group'      => 'branding',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at' => date('Y-m-d H:i:s'),
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'updated_at' => date('Y-m-d H:i:s'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Logos guardados en site_settings.\n";
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Site/: imágenes que NO están ya en otras carpetas → portfolio.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function seedSiteUniqueImages(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $this->getCategoryId('portfolio');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $dir = FCPATH . 'uploads/site/';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $siteFiles = $this->getImageFiles($dir);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $otherDirs = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'portfolio', 'retratos/clientes', 'retratos/estilos',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'live-art', 'branding', 'diseno', 'eventos', 'logos',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $knownFiles = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($otherDirs as $subdir) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $subPath = FCPATH . 'uploads/' . $subdir . '/';
            // CONDICIONAL SI
            if (!is_dir($subPath)) continue;
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($this->getImageFiles($subPath) as $f) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $knownFiles[strtolower($f)] = true;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $allDbPaths = $db->query(
            // INSTRUCCIÓN O DECLARACIÓN PHP
            "SELECT image_url FROM portfolio_works WHERE deleted_at IS NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT image_url FROM branding_project_images
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT image_url FROM design_project_images
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT image_url FROM event_images
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT featured_image FROM branding_projects WHERE deleted_at IS NULL AND featured_image IS NOT NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT featured_image FROM design_projects WHERE deleted_at IS NULL AND featured_image IS NOT NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT featured_image FROM events WHERE deleted_at IS NULL AND featured_image IS NOT NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT featured_image FROM products WHERE deleted_at IS NULL AND featured_image IS NOT NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT sample_image FROM portrait_styles WHERE deleted_at IS NULL AND sample_image IS NOT NULL
             // INSTRUCCIÓN O DECLARACIÓN PHP
             UNION SELECT value FROM site_settings WHERE value LIKE 'uploads/%'"
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        )->getResultArray();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $dbPathSet = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($allDbPaths as $row) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $val = $row['image_url'] ?? $row['featured_image'] ?? $row['sample_image'] ?? $row['value'] ?? '';
            // CONDICIONAL SI
            if ($val !== '') $dbPathSet[$val] = true;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $maxSort = $db->query("SELECT COALESCE(MAX(sort_order), 0) as m FROM portfolio_works")->getRow()->m;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $order = max((int) $maxSort + 1, 500);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $added = 0;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($siteFiles as $file) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $lower = strtolower($file);

            // CONDICIONAL SI
            if (isset($knownFiles[$lower])) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $withoutEdited = preg_replace('/(_edited)+\./i', '.', $lower);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $singleEdited = preg_replace('/(_edited){2,}\./i', '_edited.', $lower);
            // CONDICIONAL SI
            if ($withoutEdited !== $lower && isset($knownFiles[$withoutEdited]) && isset($knownFiles[$singleEdited])) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $path = 'uploads/site/' . $file;

            // CONDICIONAL SI
            if (isset($dbPathSet[$path])) continue;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = pathinfo($file, PATHINFO_FILENAME);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = preg_replace('/(_edited)+$/', '', $name);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = str_replace(['_', '-'], ' ', $name);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $name = trim($name) ?: 'Imagen ' . $order;

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $safeSlug = 'site-' . $order . '-' . substr(md5($file), 0, 6);

            // INSTRUCCIÓN O DECLARACIÓN PHP
            $db->table('portfolio_works')->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'category_id'   => $catId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'title'         => ucwords($name),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'          => $safeSlug,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description'   => 'Trabajo del archivo nmonzzon.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image_url'     => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'thumbnail_url' => $path,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active'     => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_featured'   => 0,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order'    => $order,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'created_at'    => date('Y-m-d H:i:s'),
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'updated_at'    => date('Y-m-d H:i:s'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $order++;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $added++;
        // DELIMITADOR DE BLOQUE
        }
        // EMITE SALIDA
        echo "  Site (unicas): {$added} imagenes anadidas al portfolio.\n";
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function getImageFiles(string $dir): array
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!is_dir($dir)) return [];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $files = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (scandir($dir) as $file) {
            // CONDICIONAL SI
            if ($file === '.' || $file === '..' || $file === '.gitkeep') continue;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            // CONDICIONAL SI
            if (in_array($ext, $this->allowedExts)) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $files[] = $file;
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
    private function findFileInDir(string $dir, string $approxName): ?string
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!is_dir($dir)) return null;

        // CONDICIONAL SI
        if (file_exists($dir . $approxName)) return $approxName;

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $needle = strtolower($approxName);
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (scandir($dir) as $file) {
            // CONDICIONAL SI
            if ($file === '.' || $file === '..') continue;
            // CONDICIONAL SI
            if (strtolower($file) === $needle) return $file;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $needleCleaned = strtolower(preg_replace('/[^a-z0-9.\-_ ]/i', '', $approxName));
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (scandir($dir) as $file) {
            // CONDICIONAL SI
            if ($file === '.' || $file === '..') continue;
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $cleaned = strtolower(preg_replace('/[^a-z0-9.\-_ ]/i', '', $file));
            // CONDICIONAL SI
            if ($cleaned === $needleCleaned) return $file;
        // DELIMITADOR DE BLOQUE
        }

        // RETORNA UN VALOR AL LLAMADOR
        return null;
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function uniqueSlug(\CodeIgniter\Database\BaseConnection $db, string $table, string $slug): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $original = $slug;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $i = 1;
        // BUCLE WHILE
        while ($db->query("SELECT id FROM {$table} WHERE slug = ?", [$slug])->getRow()) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $slug = $original . '-' . $i;
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $i++;
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return $slug;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
