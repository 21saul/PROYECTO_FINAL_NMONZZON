<?php

/**
 * FILTRO DE LIMITACIÓN DE TASA POR DIRECCIÓN IP Y RUTA.
 * PERMITE UN NÚMERO MÁXIMO DE PETICIONES EN UNA VENTANA DE TIEMPO USANDO CACHÉ.
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Filters;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Filters\FilterInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\RequestInterface;
// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\ResponseInterface;

// DECLARA UNA CLASE
class RateLimitFilter implements FilterInterface
// DELIMITADOR DE BLOQUE
{
    /**
     * ANTES DE LA RUTA: INCREMENTAR CONTADOR POR IP+RUTA Y RESPONDER 429 SI SE SUPERA EL LÍMITE.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function before(RequestInterface $request, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cache = \Config\Services::cache();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $ip = $request->getIPAddress();
        // COMENTARIO DE LÍNEA EXISTENTE
        // CLAVE ÚNICA POR IP Y RUTA PARA NO MEZCLAR ENDPOINTS
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $key = 'rate_limit_' . md5($ip . $request->getPath());

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $attempts = (int) $cache->get($key);

        // COMENTARIO DE LÍNEA EXISTENTE
        // LÍMITE DE 60 INTENTOS EN EL PERIODO DE TTL DEL CACHÉ (60 SEGUNDOS)
        // CONDICIONAL SI
        if ($attempts >= 60) {
            // RETORNA UN VALOR AL LLAMADOR
            return service('response')
                // INSTRUCCIÓN O DECLARACIÓN PHP
                ->setStatusCode(429)
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                ->setJSON(['error' => 'Demasiadas solicitudes. Intenta de nuevo más tarde.']);
        // DELIMITADOR DE BLOQUE
        }

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $cache->save($key, $attempts + 1, 60);
    // DELIMITADOR DE BLOQUE
    }

    /**
     * DESPUÉS DE LA RUTA: SIN LÓGICA ADICIONAL.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    // DELIMITADOR DE BLOQUE
    {
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
