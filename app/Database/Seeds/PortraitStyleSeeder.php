<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class PortraitStyleSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Color todo detalle',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => 'color-todo-detalle',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Ilustración a color en papel de acuarela de 300 gr.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'base_price' => 73.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sample_image' => 'uploads/retratos/estilos/estilo_color.png',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 1,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Blanco y negro todo detalle',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => 'blanco-y-negro-todo-detalle',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Ilustración en blanco y negro en papel de acuarela de 300 gr.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'base_price' => 73.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sample_image' => 'uploads/retratos/estilos/sandra_maceira.png',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 2,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Figurín',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => 'figurin',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Ilustración estilo figurín de moda en papel de acuarela de 300 gr.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'base_price' => 19.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sample_image' => 'uploads/retratos/estilos/estilo_figurin.jpg',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 3,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Sin caras',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => 'sin-caras',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Ilustración a color sin cara, en papel de acuarela de 300 gr.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'base_price' => 37.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sample_image' => 'uploads/retratos/estilos/estilo_sin_caras.jpg',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 4,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'A línea',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug' => 'a-linea',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Ilustración a línea en papel de acuarela de 300 gr.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'base_price' => 13.00,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sample_image' => 'uploads/retratos/estilos/elisa_goris_a_linea.jpg',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'sort_order' => 5,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->db->table('portrait_styles')->insertBatch($data);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
