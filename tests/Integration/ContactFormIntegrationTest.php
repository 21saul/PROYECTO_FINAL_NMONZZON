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
use App\Models\ContactMessageModel;

// DECLARA UNA CLASE
class ContactFormIntegrationTest extends CIUnitTestCase
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
    public function testContactFormSavesMessage(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->post('contacto', array_merge($this->csrf(), [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'     => 'Juan García',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'juan@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'phone'    => '600123456',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'category' => 'portrait',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subject'  => 'Consulta sobre retratos',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message'  => 'Me gustaría saber los plazos de entrega para un retrato familiar.',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertRedirect();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSessionHas('success');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $contactModel = new ContactMessageModel();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $message = $contactModel->where('email', 'juan@example.com')->first();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertNotNull($message);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals('portrait', $message['category']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->assertEquals(0, (int) $message['is_read']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testContactFormRejectsEmptyFields(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->post('contacto', array_merge($this->csrf(), [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'     => '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'bad-email',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subject'  => '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message'  => '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'category' => 'general',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertRedirect();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSessionHas('error');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function testContactFormRejectsInvalidCategory(): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = $this->post('contacto', array_merge($this->csrf(), [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'     => 'Test',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email'    => 'test@example.com',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subject'  => 'Test',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message'  => 'Test message content here',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'category' => 'invalid_category',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertRedirect();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $result->assertSessionHas('error');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
