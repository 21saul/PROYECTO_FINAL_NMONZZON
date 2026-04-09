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
use App\Models\ProductModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\CategoryModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\CouponModel;
// IMPORTA UNA CLASE O TRAIT
use App\Models\UserModel;

// DECLARA UNA CLASE
class CartCheckoutIntegrationTest extends CIUnitTestCase
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
    private function createTestProduct(bool $active = true): array
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catModel = new CategoryModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $existing = $catModel->where('slug', 'test-cat')->first();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $catId = $existing ? (int) $existing['id'] : (int) $catModel->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Test Category', 'slug' => 'test-cat',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active' => 1, 'sort_order' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $productModel = new ProductModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $slug = 'test-print-' . bin2hex(random_bytes(4));
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $productId = (int) $productModel->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'category_id' => $catId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Test Print',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'slug' => $slug,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'price' => 25.00,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'stock' => 10,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active' => $active ? 1 : 0,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'featured_image' => 'uploads/productos/prints/test.jpg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // RETORNA UN VALOR AL LLAMADOR
        return ['id' => $productId, 'slug' => $slug];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function buildCartSession(int $productId): array
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $key = $productId . '_0';
        // RETORNA UN VALOR AL LLAMADOR
        return [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'cart' => [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $key => [
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'product_id'   => $productId,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'variant_id'   => null,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'name'         => 'Test Print',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'variant_name' => null,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'price'        => 25.00,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'quantity'     => 2,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'image'        => 'uploads/productos/prints/test.jpg',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'slug'         => 'test-print',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'max_stock'    => 10,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testAddToCartCreatesSession(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = $this->createTestProduct();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->post('carrito/add', array_merge($this->csrf(), [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'product_id' => $product['id'],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'quantity'    => 2,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertRedirect();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSessionHas('success');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testCartPageShowsItems(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = $this->createTestProduct();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cartSession = $this->buildCartSession($product['id']);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withSession($cartSession)->get('carrito');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(200);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSee('Test Print');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testCheckoutRequiresLogin(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->get('checkout');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertRedirect();
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testCouponAppliesDiscount(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = $this->createTestProduct();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $couponModel = new CouponModel();
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $couponModel->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'code' => 'TEST10', 'type' => 'percentage', 'value' => 10,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'min_purchase' => 0, 'max_uses' => 100, 'used_count' => 0,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'valid_from' => date('Y-m-d', strtotime('-1 day')),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'valid_until' => date('Y-m-d', strtotime('+30 days')),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cartSession = $this->buildCartSession($product['id']);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withSession($cartSession)
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            ->withHeaders(['X-Requested-With' => 'XMLHttpRequest'])
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ->post('carrito/apply-coupon', array_merge($this->csrf(), [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'coupon_code' => 'TEST10',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(200);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $json = json_decode($result->getJSON(), true);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertTrue($json['success']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThan(0, $json['totals']['discount']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testCheckoutWithLoginShowsForm(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = $this->createTestProduct();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userModel = new UserModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $userId = $userModel->skipValidation(true)->insert([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name' => 'Client', 'email' => 'client@test.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'password' => 'Test1234!', 'role' => 'client', 'is_active' => 1,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ], true);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cartSession = $this->buildCartSession($product['id']);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $loginSession = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'isLoggedIn' => true,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'user_id'    => $userId,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'       => 'Client',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'      => 'client@test.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'role'       => 'client',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->withSession(array_merge($cartSession, $loginSession))
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            ->get('checkout');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertStatus(200);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSee('Finalizar pedido');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
