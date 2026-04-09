<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS UNITARIAS DE LOS HELPERS DE API: RESPUESTAS JSON, ERRORES Y GENERACIÓN DE NÚMEROS DE PEDIDO

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Unit\Helpers;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class ApiHelperTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    protected function setUp(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        parent::setUp();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        helper('api');
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LA FUNCIÓN DE RESPUESTA EXITOSA DEVUELVE UN OBJETO DE RESPUESTA HTTP
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testApiResponseReturnsResponseObject(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response = apiResponse(['key' => 'value']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertInstanceOf(\CodeIgniter\HTTP\ResponseInterface::class, $response);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LA FUNCIÓN DE ERROR DEVUELVE UN OBJETO DE RESPUESTA HTTP
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testApiErrorReturnsResponseObject(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response = apiError('Something went wrong', 400);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertInstanceOf(\CodeIgniter\HTTP\ResponseInterface::class, $response);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // EL NÚMERO DE PEDIDO GENERADO INCLUYE EL PREFIJO ESPERADO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateOrderNumberHasPrefix(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $orderNumber = generateOrderNumber('NMZ');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertStringStartsWith('NMZ-', $orderNumber);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DOS LLAMADAS CONSECUTIVAS PRODUCEN NÚMEROS DE PEDIDO DISTINTOS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateOrderNumberIsUnique(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $a = generateOrderNumber('TEST');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $b = generateOrderNumber('TEST');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEquals($a, $b);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
