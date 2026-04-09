<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Support\Models;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Model;

// DECLARA UNA CLASE
class ExampleModel extends Model
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $table          = 'factories';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $primaryKey     = 'id';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $returnType     = 'object';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $useSoftDeletes = false;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $allowedFields  = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'name',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'uid',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'class',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'icon',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'summary',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $useTimestamps      = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $validationRules    = [];
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $validationMessages = [];
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $skipValidation     = false;
// DELIMITADOR DE BLOQUE
}
