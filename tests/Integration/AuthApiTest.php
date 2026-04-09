<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Integration;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\DatabaseTestTrait;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\FeatureTestTrait;
// IMPORTA UNA CLASE O TRAIT
use App\Models\UserModel;

// DECLARA UNA CLASE
class AuthApiTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // IMPORTA UNA CLASE O TRAIT
    use DatabaseTestTrait;
    // IMPORTA UNA CLASE O TRAIT
    use FeatureTestTrait;

    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $migrate     = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $migrateOnce = false;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $refresh     = true;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    protected $namespace   = null;

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testRegisterCreatesUser(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/register', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'             => 'Test User',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'            => 'test@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password'         => 'Test1234!',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password_confirm' => 'Test1234!',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(201);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $json = json_decode($result->getJSON(), true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('data', $json);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('access_token', $json['data']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('refresh_token', $json['data']);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $user = $userModel->where('email', 'test@example.com')->first();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotNull($user);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals('client', $user['role']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testRegisterRejectsShortPassword(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/register', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'             => 'Test',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'            => 'weak@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password'         => '1234567',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password_confirm' => '1234567',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(422);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testRegisterRejectsDuplicateEmail(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Existing', 'email' => 'dupe@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => 'client', 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/register', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'             => 'Another',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'            => 'dupe@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password'         => 'Test1234!',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password_confirm' => 'Test1234!',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(422);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testLoginReturnsTokens(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Login User', 'email' => 'login@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => 'client', 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/login', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'login@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(200);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $json = json_decode($result->getJSON(), true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('access_token', $json['data']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testLoginFailsWithWrongPassword(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Wrong Pass', 'email' => 'wrong@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => 'client', 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/login', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'wrong@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'WrongPassword1!',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(401);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testAccountLocksAfter5FailedAttempts(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Lock Test', 'email' => 'lock@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => 'client', 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // BUCLE FOR
        for ($i = 0; $i < 5; $i++) {
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->withBodyFormat('json')->post('api/auth/login', [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'email' => 'lock@example.com', 'password' => 'Wrong!',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withBodyFormat('json')->post('api/auth/login', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'lock@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(423);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
