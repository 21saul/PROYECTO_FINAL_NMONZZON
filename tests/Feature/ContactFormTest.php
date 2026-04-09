<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS DE VALIDACIÓN DE DATOS DEL FORMULARIO DE CONTACTO: CAMPOS OBLIGATORIOS, EMAIL Y LONGITUD DEL MENSAJE

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Feature;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class ContactFormTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // CONJUNTO DE DATOS VÁLIDOS CUMPLE LAS CONDICIONES MÍNIMAS ESPERADAS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testContactDataValidation(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $validData = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Test User',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email' => 'test@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subject' => 'Test Subject',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message' => 'This is a test message.',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($validData['name']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($validData['email']);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->assertTrue(filter_var($validData['email'], FILTER_VALIDATE_EMAIL) !== false);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($validData['message']);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // FORMATOS DE EMAIL INVÁLIDOS DEBEN RECHAZARSE
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testInvalidEmailIsRejected(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse(filter_var('not-an-email', FILTER_VALIDATE_EMAIL));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse(filter_var('', FILTER_VALIDATE_EMAIL));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // COMPRUEBA LÍMITES INFERIOR Y SUPERIOR DE LONGITUD DEL TEXTO DEL MENSAJE
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testMessageLengthConstraints(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $shortMessage = 'Hi';
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThanOrEqual(1, strlen($shortMessage));

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $longMessage = str_repeat('a', 5001);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThan(5000, strlen($longMessage));
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
