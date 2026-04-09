<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Filters;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Filters\FilterInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\RequestInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\ResponseInterface;
// IMPORTA UNA CLASE O TRAIT
use App\Libraries\JWTService;

// DECLARA UNA CLASE
class AuthFilter implements FilterInterface
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function before(RequestInterface $request, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $header = $request->getHeaderLine('Authorization');

        // CONDICIONAL SI
        if (empty($header) || !str_starts_with($header, 'Bearer ')) {
            // RETORNA UN VALOR AL LLAMADOR
            return service('response')
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ->setStatusCode(401)
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ->setJSON(['error' => 'Token de autenticación requerido.']);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $token = substr($header, 7);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $jwtService = new JWTService();

        // INICIO DE BLOQUE TRY
        try {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $decoded = $jwtService->validateToken($token);
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $request->userData = (array) $decoded->data;
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } catch (\Exception $e) {
            // RETORNA UN VALOR AL LLAMADOR
            return service('response')
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ->setStatusCode(401)
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ->setJSON(['error' => 'Token inválido o expirado.']);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
