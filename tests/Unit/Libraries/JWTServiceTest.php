<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Unit\Libraries;

// IMPORTA UNA CLASE O TRAIT
use App\Libraries\JWTService;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class JWTServiceTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private JWTService $jwt;

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    protected function setUp(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        parent::setUp();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->jwt = new JWTService();
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateAccessTokenReturnsString(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $token = $this->jwt->generateAccessToken([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id' => 1, 'email' => 'test@test.com', 'role' => 'client', 'name' => 'Test',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertIsString($token);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($token);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateRefreshTokenReturnsString(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $token = $this->jwt->generateRefreshToken([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id' => 1, 'email' => 'test@test.com', 'role' => 'client', 'name' => 'Test',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertIsString($token);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testValidateTokenReturnsDecodedData(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userData = ['id' => 42, 'email' => 'user@example.com', 'role' => 'admin', 'name' => 'Admin'];
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $token = $this->jwt->generateAccessToken($userData);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $decoded = $this->jwt->validateToken($token);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertIsObject($decoded);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(42, $decoded->data->id);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals('user@example.com', $decoded->data->email);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testValidateInvalidTokenThrowsException(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->expectException(\Exception::class);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->jwt->validateToken('invalid.token.here');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
