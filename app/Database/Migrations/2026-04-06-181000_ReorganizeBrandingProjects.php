<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Migrations;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Migration;

// DECLARA UNA CLASE
class ReorganizeBrandingProjects extends Migration
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function up()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $db  = \Config\Database::connect();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $now = date('Y-m-d H:i:s');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $ju  = JSON_UNESCAPED_UNICODE;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['abeja-dorada-identidad', 'ilustraciones-mockups'] as $obsoleteSlug) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $old = $db->table('branding_projects')->where('slug', $obsoleteSlug)->where('deleted_at', null)->get()->getRowArray();
            // CONDICIONAL SI
            if ($old) {
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_project_images')->where('branding_project_id', $old['id'])->delete();
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->where('id', $old['id'])->update(['deleted_at' => $now, 'updated_at' => $now]);
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $brandDir = FCPATH . 'uploads/branding/';

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $syncGallery = static function (int $projectId, array $filenames) use ($db, $now, $brandDir): void {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $db->table('branding_project_images')->where('branding_project_id', $projectId)->delete();
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $sort = 1;
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($filenames as $f) {
                // CONDICIONAL SI
                if (! is_file($brandDir . $f)) {
                    // INSTRUCCIÓN O DECLARACIÓN PHP
                    continue;
                // DELIMITADOR DE BLOQUE
                }
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_project_images')->insert([
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'branding_project_id' => $projectId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'image_url'           => 'uploads/branding/' . $f,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'alt_text'            => 'Proyecto branding — ' . $sort,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'sort_order'          => $sort,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'created_at'          => $now,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ]);
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $sort++;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        };

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $upsert = static function (array $row, array $galleryFiles) use ($db, $now, $syncGallery): int {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $ex = $db->table('branding_projects')->where('slug', $row['slug'])->where('deleted_at', null)->get()->getRowArray();
            // CONDICIONAL SI
            if ($ex) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $id = (int) $ex['id'];
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->where('id', $id)->update(array_merge($row, ['updated_at' => $now]));
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                $db->table('branding_projects')->insert(array_merge($row, ['created_at' => $now, 'updated_at' => $now]));
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $id = (int) $db->insertID();
            // DELIMITADOR DE BLOQUE
            }
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $syncGallery($id, $galleryFiles);

            // RETORNA UN VALOR AL LLAMADOR
            return $id;
        // DELIMITADOR DE BLOQUE
        };

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $upsert([
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
            'featured_image'    => 'uploads/branding/Cartel Aldea Apalpador 2022.jpg',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_featured'       => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'         => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'        => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Cartel Aldea Apalpador 2022.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Cartel Aldea Apalpador. 2022.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Cartel A3 portada.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Cartel A3.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $upsert([
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
            'is_featured'       => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'         => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'        => 2,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], []);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $upsert([
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
            'featured_image'    => 'uploads/branding/Black and Beige Simple Book Mockup Instagram Post-2.jpg',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_featured'       => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'         => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'        => 3,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Black and Beige Simple Book Mockup Instagram Post-2.jpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Grey Black Modern Digital Product  Mockup Shadow Instagram Post 2.JPG',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'Grey Black Modern Digital Product  Mockup Shadow Instagram Post-3.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $upsert([
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
            'featured_image'    => 'uploads/branding/matcha.png',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_featured'       => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active'         => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sort_order'        => 4,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'matcha.png',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function down()
    // DELIMITADOR DE BLOQUE
    {
        // Sin reversión automática: los proyectos sustituidos requerirían restaurar datos a mano.
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
