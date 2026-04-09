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
use App\Models\PortraitStyleModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\PortraitSizeModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\UserModel;
// IMPORTA UNA CLASE O TRAIT
use App\Libraries\JWTService;

// DECLARA UNA CLASE
class PortraitOrderIntegrationTest extends CIUnitTestCase
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
    private function csrf(): array
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return [csrf_token() => csrf_hash()];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function createStyleAndSize(): array
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $styleModel = new PortraitStyleModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $styleId = $styleModel->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Color', 'slug' => 'color',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'base_price' => 73, 'is_active' => 1, 'sort_order' => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'sample_image' => 'uploads/retratos/estilos/color.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sizeModel = new PortraitSizeModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sizeId = $sizeModel->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'A4', 'dimensions' => '21x29.7cm',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'price_modifier' => 0, 'sort_order' => 1, 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // RETORNA UN VALOR AL LLAMADOR
        return [(int) $styleId, (int) $sizeId];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function createUserWithToken(string $role = 'client'): array
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $email = $role . '_' . bin2hex(random_bytes(4)) . '@test.com';
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userId = $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => ucfirst($role) . ' User', 'email' => $email,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => $role, 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $user = $userModel->find($userId);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $jwt = new JWTService();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $token = $jwt->generateAccessToken($user);

        // RETORNA UN VALOR AL LLAMADOR
        return [$user, $token];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testCreatePortraitOrderViaApi(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [$styleId, $sizeId] = $this->createStyleAndSize();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [, $token] = $this->createUserWithToken('client');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withHeaders([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'Authorization' => 'Bearer ' . $token,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ])->withBodyFormat('json')->post('api/portrait-orders', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_style_id' => $styleId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_size_id'  => $sizeId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_figures'       => 2,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'with_frame'        => false,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'client_notes'      => 'Test order',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(201);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $json = json_decode($result->getJSON(), true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals('quote', $json['data']['status']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThan(0, (float) $json['data']['total_price']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testPriceCalculationMatchesBetweenWebAndApi(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [$styleId, $sizeId] = $this->createStyleAndSize();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [, $token] = $this->createUserWithToken('client');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $webResult = $this->post('retratos/calcular-precio', array_merge($this->csrf(), [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'style_id'    => $styleId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'size_id'     => $sizeId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_figures' => 3,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'with_frame'  => '0',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $webJson = json_decode($webResult->getJSON(), true);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $webPrice = $webJson['price'];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $apiResult = $this->withHeaders([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'Authorization' => 'Bearer ' . $token,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ])->withBodyFormat('json')->post('api/portrait-orders', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_style_id' => $styleId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_size_id'  => $sizeId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_figures'       => 3,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'with_frame'        => false,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $apiJson = json_decode($apiResult->getJSON(), true);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $apiPrice = (float) $apiJson['data']['total_price'];

        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->assertEquals(
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $webPrice,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $apiPrice,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'El precio del configurador web debe coincidir con el del pedido API'
        // INSTRUCCIÓN O DECLARACIÓN PHP
        );
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testStatusTransitionFlow(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [$styleId, $sizeId] = $this->createStyleAndSize();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [, $adminToken] = $this->createUserWithToken('admin');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        [, $clientToken] = $this->createUserWithToken('client');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $createResult = $this->withHeaders([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'Authorization' => 'Bearer ' . $clientToken,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ])->withBodyFormat('json')->post('api/portrait-orders', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_style_id' => $styleId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'portrait_size_id'  => $sizeId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_figures'       => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $orderId = json_decode($createResult->getJSON(), true)['data']['id'];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $transitions = ['accepted', 'photo_received', 'in_progress', 'revision', 'delivered', 'completed'];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($transitions as $status) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $result = $this->withHeaders([
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'Authorization' => 'Bearer ' . $adminToken,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ])->withBodyFormat('json')->put("api/admin/portrait-orders/{$orderId}/status", [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'status' => $status,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'notes'  => "Transition to {$status}",
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);

            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $result->assertStatus(200);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $json = json_decode($result->getJSON(), true);
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $this->assertEquals($status, $json['data']['status']);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
