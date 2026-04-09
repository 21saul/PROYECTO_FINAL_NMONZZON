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
class ClientAuthFilter implements FilterInterface
// DELIMITADOR DE BLOQUE
{
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function before(RequestInterface $request, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $session = session();
        // CONDICIONAL SI
        if (!$session->get('isLoggedIn')) {
            // RETORNA UN VALOR AL LLAMADOR
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
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
