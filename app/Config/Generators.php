<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

// DECLARA UNA CLASE
class Generators extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Generator Commands' Views
     * --------------------------------------------------------------------------
     *
     * This array defines the mapping of generator commands to the view files
     * they are using. If you need to customize them for your own, copy these
     * view files in your own folder and indicate the location here.
     *
     * You will notice that the views have special placeholders enclosed in
     * curly braces `{...}`. These placeholders are used internally by the
     * generator commands in processing replacements, thus you are warned
     * not to delete them or modify the names. If you will do so, you may
     * end up disrupting the scaffolding process and throw errors.
     *
     * YOU HAVE BEEN WARNED!
     *
     * @var array<string, array<string, string>|string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $views = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:cell' => [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'class' => 'CodeIgniter\Commands\Generators\Views\cell.tpl.php',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'view'  => 'CodeIgniter\Commands\Generators\Views\cell_view.tpl.php',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:command'      => 'CodeIgniter\Commands\Generators\Views\command.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:config'       => 'CodeIgniter\Commands\Generators\Views\config.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:controller'   => 'CodeIgniter\Commands\Generators\Views\controller.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:entity'       => 'CodeIgniter\Commands\Generators\Views\entity.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:filter'       => 'CodeIgniter\Commands\Generators\Views\filter.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:migration'    => 'CodeIgniter\Commands\Generators\Views\migration.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:model'        => 'CodeIgniter\Commands\Generators\Views\model.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:seeder'       => 'CodeIgniter\Commands\Generators\Views\seeder.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'make:validation'   => 'CodeIgniter\Commands\Generators\Views\validation.tpl.php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'session:migration' => 'CodeIgniter\Commands\Generators\Views\migration.tpl.php',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
// DELIMITADOR DE BLOQUE
}
