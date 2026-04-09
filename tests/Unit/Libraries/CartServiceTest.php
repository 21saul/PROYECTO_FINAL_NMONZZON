<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS UNITARIAS DEL SERVICIO DE CARRITO: ARTÍCULOS, TOTALES, ENVÍO, IVA, CUPONES Y LIMPIEZA DE SESIÓN

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Unit\Libraries;

// IMPORTA UNA CLASE O TRAIT
use App\Libraries\CartService;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class CartServiceTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private CartService $cart;

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    protected function setUp(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        parent::setUp();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        session()->remove('cart');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        session()->remove('applied_coupon');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->cart = new CartService();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // COMPRUEBA QUE SIN SESIÓN EL CARRITO DEVUELVE UN ARRAY VACÍO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGetItemsReturnsEmptyArrayByDefault(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertIsArray($this->cart->getItems());
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEmpty($this->cart->getItems());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // COMPRUEBA QUE EL SUBTOTAL ES CERO CUANDO NO HAY PRODUCTOS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGetSubtotalReturnsZeroWhenEmpty(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, $this->cart->getSubtotal());
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGetTotalIncludesShippingWhenUnder50(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Cheap', 'price' => 20, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $total = $cart->getTotal();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subtotal = 20.0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $shipping = 4.95;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tax = round($subtotal * 0.21, 2);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(round($subtotal + $shipping + $tax, 2), $total);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ENVÍO GRATIS CUANDO EL SUBTOTAL SUPERA EL UMBRAL CONFIGURADO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testShippingFreeAbove50(): void
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // CONFIGURACIÓN MANUAL DE LA SESIÓN DEL CARRITO PARA PROBAR EL ENVÍO
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 60, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, $cart->getShippingCost());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // COBRO DE ENVÍO CUANDO EL SUBTOTAL ESTÁ POR DEBAJO DEL UMBRAL
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testShippingChargedUnder50(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 20, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(4.95, $cart->getShippingCost());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // CÁLCULO DEL IVA SOBRE EL SUBTOTAL DE LOS ARTÍCULOS
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTaxCalculation(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 100, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(21.0, $cart->getTax());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // CONTEO TOTAL DE UNIDADES EN EL CARRITO (SUMA DE CANTIDADES)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGetItemCount(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'a' => ['product_id' => 1, 'variant_id' => null, 'name' => 'A', 'price' => 10, 'quantity' => 3, 'image' => '', 'max_stock' => 10],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'b' => ['product_id' => 2, 'variant_id' => null, 'name' => 'B', 'price' => 20, 'quantity' => 2, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(5, $cart->getItemCount());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ACTUALIZACIÓN DE LA CANTIDAD DE UNA LÍNEA DEL CARRITO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testUpdateQuantity(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 10, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $cart->updateQuantity('test_0', 5);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $items = $cart->getItems();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(5, $items['test_0']['quantity']);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ELIMINACIÓN DE UN ARTÍCULO POR SU CLAVE
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testRemoveItem(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 10, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $cart->removeItem('test_0');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEmpty($cart->getItems());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // VACIADO COMPLETO DEL CARRITO Y DEL CUPÓN APLICADO EN SESIÓN
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testClear(): void
    // DELIMITADOR DE BLOQUE
    {
        // INSTRUCCIÓN O DECLARACIÓN PHP
        session()->set('cart', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'test_0' => ['product_id' => 1, 'variant_id' => null, 'name' => 'Test', 'price' => 10, 'quantity' => 1, 'image' => '', 'max_stock' => 10],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = new CartService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $cart->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEmpty($cart->getItems());
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNull(session()->get('applied_coupon'));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ESTRUCTURA DEL ARRAY DE TOTALES (SUBTOTAL, ENVÍO, IVA, DESCUENTO, TOTAL)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGetTotalsReturnsCorrectStructure(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $totals = $this->cart->getTotals();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('subtotal', $totals);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('shipping', $totals);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('tax', $totals);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('discount', $totals);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertArrayHasKey('total', $totals);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
