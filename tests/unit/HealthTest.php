<?php

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;
// IMPORTA UNA CLASE O TRAIT
use Config\App;
// IMPORTA UNA CLASE O TRAIT
use Tests\Support\Libraries\ConfigReader;

/**
 * @internal
 */
// INSTRUCCIÓN O DECLARACIÓN PHP
final class HealthTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testIsDefinedAppPath(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue(defined('APPPATH'));
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testBaseUrlHasBeenSet(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $validation = service('validation');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $env = false;

        // COMENTARIO DE LÍNEA EXISTENTE
        // Check the baseURL in .env
        // CONDICIONAL SI
        if (is_file(HOMEPATH . '.env')) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $env = preg_grep('/^app\.baseURL = ./', file(HOMEPATH . '.env')) !== false;
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($env) {
            // COMENTARIO DE LÍNEA EXISTENTE
            // BaseURL in .env is a valid URL?
            // COMENTARIO DE LÍNEA EXISTENTE
            // phpunit.xml.dist sets app.baseURL in $_SERVER
            // COMENTARIO DE LÍNEA EXISTENTE
            // So if you set app.baseURL in .env, it takes precedence
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $config = new App();
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->assertTrue(
                // INSTRUCCIÓN O DECLARACIÓN PHP
                $validation->check($config->baseURL, 'valid_url'),
                // INSTRUCCIÓN O DECLARACIÓN PHP
                'baseURL "' . $config->baseURL . '" in .env is not valid URL',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            );
        // DELIMITADOR DE BLOQUE
        }

        // COMENTARIO DE LÍNEA EXISTENTE
        // Get the baseURL in app/Config/App.php
        // COMENTARIO DE LÍNEA EXISTENTE
        // You can't use Config\App, because phpunit.xml.dist sets app.baseURL
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $reader = new ConfigReader();

        // COMENTARIO DE LÍNEA EXISTENTE
        // BaseURL in app/Config/App.php is a valid URL?
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->assertTrue(
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $validation->check($reader->baseURL, 'valid_url'),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'baseURL "' . $reader->baseURL . '" in app/Config/App.php is not valid URL',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        );
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
