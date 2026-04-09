<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class CategorySeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Retratos', 'slug' => 'retratos', 'description' => 'Retratos personalizados en diferentes estilos artísticos', 'sort_order' => 1, 'is_active' => 1, 'meta_title' => 'Retratos Personalizados | nmonzzon Studio', 'meta_description' => 'Retratos artísticos personalizados en color, blanco y negro, figurín y más estilos'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Arte en Vivo', 'slug' => 'arte-en-vivo', 'description' => 'Retratos en vivo para bodas, eventos corporativos y festivales', 'sort_order' => 2, 'is_active' => 1, 'meta_title' => 'Arte en Vivo | nmonzzon Studio', 'meta_description' => 'Servicio de retratos en vivo para eventos especiales'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Branding', 'slug' => 'branding', 'description' => 'Diseño de identidad visual y branding para marcas', 'sort_order' => 3, 'is_active' => 1, 'meta_title' => 'Branding | nmonzzon Studio', 'meta_description' => 'Servicios de branding e identidad visual'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Eventos', 'slug' => 'eventos', 'description' => 'Portfolio de eventos donde la artista ha participado', 'sort_order' => 4, 'is_active' => 1, 'meta_title' => 'Eventos | nmonzzon Studio', 'meta_description' => 'Eventos artísticos y participaciones'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Diseño', 'slug' => 'diseno', 'description' => 'Proyectos de diseño gráfico e ilustración editorial', 'sort_order' => 5, 'is_active' => 1, 'meta_title' => 'Diseño | nmonzzon Studio', 'meta_description' => 'Proyectos de diseño gráfico e ilustración'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Productos', 'slug' => 'productos', 'description' => 'Prints, totebags y merchandising artístico', 'sort_order' => 6, 'is_active' => 1, 'meta_title' => 'Productos | nmonzzon Studio', 'meta_description' => 'Tienda de prints, totebags y productos artísticos'],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->db->table('categories')->insertBatch($data);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
