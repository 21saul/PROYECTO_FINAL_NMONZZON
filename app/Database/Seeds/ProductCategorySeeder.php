<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class ProductCategorySeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catModel = model('CategoryModel');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $printsCat = $catModel->where('slug', 'prints')->first();
        // CONDICIONAL SI
        if (!$printsCat) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $catModel->skipValidation(true)->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Prints', 'slug' => 'prints',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Láminas artísticas de alta calidad impresas en papel premium.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1, 'sort_order' => 1,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // EMITE SALIDA
            echo "Categoría 'Prints' creada.\n";
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } else {
            // EMITE SALIDA
            echo "Categoría 'Prints' ya existe.\n";
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totebagsCat = $catModel->where('slug', 'totebags')->first();
        // CONDICIONAL SI
        if (!$totebagsCat) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $catModel->skipValidation(true)->insert([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name' => 'Totebags', 'slug' => 'totebags',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'description' => 'Bolsas de tela con diseños exclusivos nmonzzon.',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'is_active' => 1, 'sort_order' => 2,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
            // EMITE SALIDA
            echo "Categoría 'Totebags' creada.\n";
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } else {
            // EMITE SALIDA
            echo "Categoría 'Totebags' ya existe.\n";
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
