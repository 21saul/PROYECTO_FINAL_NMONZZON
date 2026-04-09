<?php

declare(strict_types=1);

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Commands;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\CLI\BaseCommand;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\CLI\CLI;

// DECLARA UNA CLASE
class AnnotateCaps extends BaseCommand
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $group       = 'Proyecto';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $name        = 'annotate:caps';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $description = 'Añade comentarios // EN MAYÚSCULAS línea a línea en PHP (excluye app/Views).';

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function run(array $params)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tools = ROOTPATH . 'tools/annotate_caps.php';
        // CONDICIONAL SI
        if (! is_file($tools)) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            CLI::error('No existe tools/annotate_caps.php');

            // RETORNA SIN VALOR
            return;
        // DELIMITADOR DE BLOQUE
        }
        // INSTRUCCIÓN O DECLARACIÓN PHP
        require_once $tools;
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        runAnnotateCaps(ROOTPATH);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
