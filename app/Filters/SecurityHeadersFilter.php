<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Filters;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Filters\FilterInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\RequestInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\ResponseInterface;

// DECLARA UNA CLASE
class SecurityHeadersFilter implements FilterInterface
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function before(RequestInterface $request, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response->setHeader('X-XSS-Protection', '1; mode=block');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $response->setHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response->setHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response->setHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $response->setHeader('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://js.stripe.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: https://res.cloudinary.com https://*.stripe.com; font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; connect-src 'self' https://api.stripe.com https://api.cloudinary.com wss:; frame-src https://js.stripe.com;");

        // RETORNA UN VALOR AL LLAMADOR
        return $response;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
