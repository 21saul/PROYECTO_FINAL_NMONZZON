<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class PortraitSizeSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Lámina A5 sin marco', 'dimensions' => 'A5 (148x210mm)', 'price_modifier' => 0.00, 'sort_order' => 1, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Lámina A4 sin marco', 'dimensions' => 'A4 (210x297mm)', 'price_modifier' => 0.00, 'sort_order' => 2, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Lámina A3 sin marco', 'dimensions' => 'A3 (297x420mm)', 'price_modifier' => 0.00, 'sort_order' => 3, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 18x24', 'dimensions' => '18x24cm', 'price_modifier' => 15.00, 'sort_order' => 4, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 20x20', 'dimensions' => '20x20cm', 'price_modifier' => 15.00, 'sort_order' => 5, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 24x30', 'dimensions' => '24x30cm', 'price_modifier' => 18.00, 'sort_order' => 6, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 30x30', 'dimensions' => '30x30cm', 'price_modifier' => 18.00, 'sort_order' => 7, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 30x40', 'dimensions' => '30x40cm', 'price_modifier' => 20.00, 'sort_order' => 8, 'is_active' => 1],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ['name' => 'Marco 40x50', 'dimensions' => '40x50cm', 'price_modifier' => 20.00, 'sort_order' => 9, 'is_active' => 1],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->db->table('portrait_sizes')->insertBatch($data);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
