<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE INTEGRACIÓN CON STRIPE (PAGOS).
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * CONFIGURA LA API, CREA INTENCIONES DE PAGO, CLIENTES Y CONFIRMA EL ESTADO DEL PAGO.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use Stripe\Stripe;
// IMPORTA UNA CLASE O TRAIT
use Stripe\PaymentIntent;
// IMPORTA UNA CLASE O TRAIT
use Stripe\Customer;

// DECLARA UNA CLASE
class StripeService
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private bool $configured = false;

    // COMENTARIO DE LÍNEA EXISTENTE
    // ESTABLECE LA CLAVE SECRETA DESDE .env O SITE_SETTINGS (VER STRIPECONFIG)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $key = StripeConfig::secretKey();

        // CONDICIONAL SI
        if ($key === '' || StripeConfig::isPlaceholderSecret($key)) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            log_message('warning', 'Stripe no configurado: clave secreta ausente o placeholder (.env o admin).');
            // RETORNA SIN VALOR
            return;
        // DELIMITADOR DE BLOQUE
        }

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        Stripe::setApiKey($key);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->configured = true;
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // INDICA SI STRIPE ESTÁ CONFIGURADO CON CLAVES REALES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function isConfigured(): bool
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $this->configured;
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // LANZA EXCEPCIÓN SI SE INTENTA USAR LA API SIN CONFIGURAR
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function requireConfigured(): void
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!$this->configured) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Stripe no está configurado. Añade claves en .env o en Admin → Configuración → E-commerce (Stripe).');
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // CREA UN PAYMENTINTENT CON IMPORTE EN CÉNTIMOS, MONEDA Y METADATOS OPCIONALES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function createPaymentIntent(float $amount, string $currency = 'eur', array $metadata = []): PaymentIntent
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->requireConfigured();

        // RETORNA UN VALOR AL LLAMADOR
        return PaymentIntent::create([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'amount'                    => (int) ($amount * 100),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'currency'                  => $currency,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'metadata'                  => $metadata,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'automatic_payment_methods' => ['enabled' => true],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // REGISTRA UN CLIENTE EN STRIPE CON EMAIL Y NOMBRE PARA FACTURACIÓN O HISTORIAL
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function createCustomer(string $email, string $name): Customer
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->requireConfigured();

        // RETORNA UN VALOR AL LLAMADOR
        return Customer::create([
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'email' => $email,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'name'  => $name,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // OBTIENE UN PAYMENTINTENT EXISTENTE POR SU ID (ESTADO, IMPORTE, ETC.)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->requireConfigured();

        // RETORNA UN VALOR AL LLAMADOR
        return PaymentIntent::retrieve($paymentIntentId);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // RECUPERA LA INTENCIÓN Y COMPRUEBA QUE EL PAGO HAYA TERMINADO EN "SUCCEEDED"
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function confirmPayment(string $paymentIntentId): PaymentIntent
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->requireConfigured();

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $intent = PaymentIntent::retrieve($paymentIntentId);
        // CONDICIONAL SI
        if ($intent->status === 'succeeded') {
            // RETORNA UN VALOR AL LLAMADOR
            return $intent;
        // DELIMITADOR DE BLOQUE
        }
        // LANZA UNA EXCEPCIÓN
        throw new \RuntimeException('El pago no se completó correctamente.');
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
