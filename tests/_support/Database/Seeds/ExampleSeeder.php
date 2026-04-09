<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Support\Database\Seeds;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Database\Seeder;

// DECLARA UNA CLASE
class ExampleSeeder extends Seeder
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $factories = [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'    => 'Test Factory',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'uid'     => 'test001',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'class'   => 'Factories\Tests\NewFactory',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'icon'    => 'fas fa-puzzle-piece',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'summary' => 'Longer sample text for testing',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'    => 'Widget Factory',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'uid'     => 'widget',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'class'   => 'Factories\Tests\WidgetPlant',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'icon'    => 'fas fa-puzzle-piece',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'summary' => 'Create widgets in your factory',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'    => 'Evil Factory',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'uid'     => 'evil-maker',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'class'   => 'Factories\Evil\MyFactory',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'icon'    => 'fas fa-book-dead',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'summary' => 'Abandon all hope, ye who enter here',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $builder = $this->db->table('factories');

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($factories as $factory) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $builder->insert($factory);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
