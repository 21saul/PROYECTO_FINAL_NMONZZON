<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * FUNCIONES AUXILIARES PARA RESPUESTAS JSON DE API Y GENERACIÓN DE NÚMEROS DE PEDIDO.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * ENVUELVEN EL SERVICIO "RESPONSE" DE CI4 Y EVITAN REDEFINIR FUNCIONES SI EL HELPER SE CARGA DOS VECES.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// CONDICIONAL SI
if (!function_exists('apiResponse')) {
    // COMENTARIO DE LÍNEA EXISTENTE
    // CONSTRUYE UNA RESPUESTA JSON UNIFICADA CON CÓDIGO HTTP, MENSAJE Y DATOS OPCIONALES
    // DECLARA UNA FUNCIÓN
    function apiResponse($data = null, int $code = 200, string $message = 'OK'): \CodeIgniter\HTTP\Response
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return service('response')
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ->setStatusCode($code)
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ->setJSON([
                // COMENTARIO DE LÍNEA EXISTENTE
                // ÉXITO SI EL CÓDIGO ES MENOR QUE 400; SI NO, MARCA ERROR
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'status'  => $code < 400 ? 'success' : 'error',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'message' => $message,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'data'    => $data,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ]);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}

// CONDICIONAL SI
if (!function_exists('apiError')) {
    // COMENTARIO DE LÍNEA EXISTENTE
    // RESPUESTA DE ERROR CON MENSAJE, CÓDIGO HTTP Y OPCIONALMENTE DETALLE DE ERRORES DE VALIDACIÓN
    // DECLARA UNA FUNCIÓN
    function apiError(string $message, int $code = 400, $errors = null): \CodeIgniter\HTTP\Response
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'status'  => 'error',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'message' => $message,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
        // CONDICIONAL SI
        if ($errors) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $response['errors'] = $errors;
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return service('response')->setStatusCode($code)->setJSON($response);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}

// CONDICIONAL SI
if (!function_exists('generateOrderNumber')) {
    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA UN IDENTIFICADOR ÚNICO DE PEDIDO: PREFIJO + FECHA + SUFIJO ALEATORIO EN HEX
    // DECLARA UNA FUNCIÓN
    function generateOrderNumber(string $prefix = 'NMZ'): string
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $prefix . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
