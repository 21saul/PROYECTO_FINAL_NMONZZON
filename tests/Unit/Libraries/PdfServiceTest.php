<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Tests\Unit\Libraries;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Test\CIUnitTestCase;
// IMPORTA UNA CLASE O TRAIT
use App\Libraries\PdfService;

// DECLARA UNA CLASE
class PdfServiceTest extends CIUnitTestCase
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testPdfServiceCanBeInstantiated(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $service = new PdfService();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertInstanceOf(PdfService::class, $service);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateInvoiceCreatesPdfFile(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $service = new PdfService();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $orderData = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'                    => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'order_number'          => 'NMZ-TEST001',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'            => date('Y-m-d H:i:s'),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'paid_at'               => date('Y-m-d H:i:s'),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subtotal'              => 50.00,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_cost'         => 4.95,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'tax'                   => 10.50,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'discount'              => 0,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total'                 => 65.45,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'coupon_code'           => null,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_name'         => 'Test Client',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_address'      => 'Calle Test 1',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_city'         => 'Vigo',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_postal_code'  => '36201',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_country'      => 'España',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping_phone'        => '600123456',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'items' => [
                // INSTRUCCIÓN O DECLARACIÓN PHP
                [
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'product_name' => 'Print Artístico',
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'quantity'     => 2,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'unit_price'   => 25.00,
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    'total_price'  => 50.00,
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $path = $service->generateInvoice($orderData);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($path);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertStringEndsWith('.pdf', $path);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fullPath = WRITEPATH . $path;
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFileExists($fullPath);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThan(0, filesize($fullPath));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        @unlink($fullPath);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testGenerateQuoteCreatesPdfFile(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $service = new PdfService();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $bookingData = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'id'                    => 1,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'booking_number'        => 'LA-TEST001',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'contact_name'          => 'María López',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'contact_email'         => 'maria@test.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'contact_phone'         => '600456789',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_type'            => 'wedding',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_date'            => '2026-06-15',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_start_time'      => '18:00',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_end_time'        => '22:00',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_location'        => 'Hotel Gran Vigo',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_city'            => 'Vigo',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'event_postal_code'     => '36201',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_guests'            => 100,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'num_portraits'         => 30,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'base_rate'             => 500.00,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'travel_fee'            => 50.00,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total_quote'           => 550.00,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'special_requirements'  => 'Espacio al aire libre',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'created_at'            => date('Y-m-d H:i:s'),
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $path = $service->generateQuote($bookingData);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotEmpty($path);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertStringEndsWith('.pdf', $path);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fullPath = WRITEPATH . $path;
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertFileExists($fullPath);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertGreaterThan(0, filesize($fullPath));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        @unlink($fullPath);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
