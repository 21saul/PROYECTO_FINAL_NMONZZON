<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS RELACIONADAS CON AUTENTICACIÓN: HASH DE CONTRASEÑA, VALIDACIÓN DE EMAIL Y REQUISITOS DE FORTALEZA

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Feature;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class AuthTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // COMPRUEBA QUE PASSWORD_HASH Y PASSWORD_VERIFY SON COHERENTES PARA LA MISMA CONTRASEÑA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testPasswordHashingWorks(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $password = 'TestPassword123!';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $hash = password_hash($password, PASSWORD_DEFAULT);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue(password_verify($password, $hash));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // UNA CONTRASEÑA INCORRECTA NO DEBE VERIFICARSE CONTRA EL HASH
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testWrongPasswordFails(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $hash = password_hash('CorrectPassword', PASSWORD_DEFAULT);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse(password_verify('WrongPassword', $hash));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // VALIDACIÓN BÁSICA DE FORMATO DE DIRECCIÓN DE CORREO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testEmailValidation(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotFalse(filter_var('user@example.com', FILTER_VALIDATE_EMAIL));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse(filter_var('not-email', FILTER_VALIDATE_EMAIL));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFalse(filter_var('', FILTER_VALIDATE_EMAIL));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // EJEMPLO DE CONTRASEÑA QUE CUMPLE LONGITUD Y TIPOS DE CARACTERES EXIGIDOS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testPasswordStrengthRequirements(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $strong = 'MyP@ssw0rd!';
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThanOrEqual(8, strlen($strong));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertMatchesRegularExpression('/[A-Z]/', $strong);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertMatchesRegularExpression('/[a-z]/', $strong);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertMatchesRegularExpression('/[0-9]/', $strong);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
