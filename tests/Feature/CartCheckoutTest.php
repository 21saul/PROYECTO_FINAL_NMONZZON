<?php

// COMENTARIO DE LÍNEA EXISTENTE
// PRUEBAS DE LÓGICA DE CHECKOUT: ENVÍO, IVA Y CÁLCULO DE TOTALES CON O SIN DESCUENTO

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Feature;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;

// DECLARA UNA CLASE
class CartCheckoutTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // REGLAS DE PORTES SEGÚN EL SUBTOTAL (UMBRAL Y TARIFA FIJA)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testShippingCalculation(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(4.95, $this->calculateShipping(20));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(4.95, $this->calculateShipping(49.99));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, $this->calculateShipping(50));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, $this->calculateShipping(100));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // IVA AL 21% SOBRE LA BASE IMPONIBLE INDICADA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTaxCalculation(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(21.0, $this->calculateTax(100));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(10.50, $this->calculateTax(50));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, $this->calculateTax(0));
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // TOTAL DEL PEDIDO SIN DESCUENTO NI PORTES ADICIONALES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTotalCalculation(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subtotal = 100;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $shipping = 0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tax = 21.0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $discount = 0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $total = round($subtotal + $shipping + $tax - $discount, 2);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(121.0, $total);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // TOTAL CUANDO SE APLICA UN DESCUENTO FIJO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTotalWithDiscount(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subtotal = 100;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $shipping = 0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tax = 21.0;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $discount = 10;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $total = round($subtotal + $shipping + $tax - $discount, 2);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(111.0, $total);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // TOTAL CON SUBTOTAL BAJO QUE INCLUYE GASTOS DE ENVÍO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testTotalWithShipping(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subtotal = 30;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $shipping = 4.95;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $tax = round(30 * 0.21, 2);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $total = round($subtotal + $shipping + $tax, 2);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(41.25, $total);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function calculateShipping(float $subtotal): float
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $subtotal >= 50 ? 0 : 4.95;
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function calculateTax(float $subtotal): float
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return round($subtotal * 0.21, 2);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
