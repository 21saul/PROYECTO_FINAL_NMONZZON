<?php

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
// INSTRUCCIÓN O DECLARACIÓN PHP
final class ExampleSessionTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testSessionSimple(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $session = service('session');

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $session->set('logged_in', 123);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertSame(123, $session->get('logged_in'));
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
