<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE TOKENS JWT PARA AUTENTICACIÓN API.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * GENERA TOKENS DE ACCESO Y REFRESCO, Y VALIDA TOKENS CON HS256 USANDO LA CLAVE SECRETA DEL ENTORNO.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use Firebase\JWT\JWT;
// IMPORTA UNA CLASE O TRAIT
use Firebase\JWT\Key;

// DECLARA UNA CLASE
class JWTService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // CLAVE SECRETA PARA FIRMAR Y VERIFICAR LOS JWT (NO EXPONER AL CLIENTE)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $secretKey;
    // COMENTARIO DE LÍNEA EXISTENTE
    // ALGORITMO DE FIRMA HMAC (DEBE COINCIDIR AL DECODIFICAR)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $algorithm = 'HS256';
    // COMENTARIO DE LÍNEA EXISTENTE
    // DURACIÓN DEL TOKEN DE ACCESO EN SEGUNDOS (1 HORA)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private int $accessTokenExpiry = 3600;
    // COMENTARIO DE LÍNEA EXISTENTE
    // DURACIÓN DEL TOKEN DE REFRESCO EN SEGUNDOS (7 DÍAS)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private int $refreshTokenExpiry = 604800;

    // COMENTARIO DE LÍNEA EXISTENTE
    // INICIALIZA LA CLAVE SECRETA DESDE VARIABLE DE ENTORNO O CLAVE DE ENCRIPTACIÓN DE CI4
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // PRIORIDAD: JWT_SECRET; SI NO EXISTE, USA LA CLAVE DE ENCRIPTACIÓN COMO RESPALDO
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->secretKey = getenv('JWT_SECRET') ?: env('encryption.key');
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA UN JWT DE ACCESO CON DATOS DE USUARIO Y CADUCIDAD CORTA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateAccessToken(array $userData): string
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // PAYLOAD ESTÁNDAR: EMISOR, EMISIÓN, EXPIRACIÓN Y DATOS DE USUARIO EN "DATA"
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $payload = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'iss' => base_url(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'iat' => time(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'exp' => time() + $this->accessTokenExpiry,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'data' => [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'id'    => $userData['id'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'email' => $userData['email'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'role'  => $userData['role'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'  => $userData['name'],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // COMENTARIO DE LÍNEA EXISTENTE
        // CODIFICA Y FIRMA EL PAYLOAD; DEVUELVE LA CADENA JWT
        // RETORNA UN VALOR AL LLAMADOR
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // GENERA UN JWT DE REFRESCO CON SOLO EL ID DE USUARIO Y CADUCIDAD LARGA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateRefreshToken(array $userData): string
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // MARCA TYPE COMO TOKEN DE REFRESCO PARA DISTINGUIRLO DEL DE ACCESO EN VALIDACIONES POSTERIORES
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $payload = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'iss' => base_url(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'iat' => time(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'exp' => time() + $this->refreshTokenExpiry,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'type' => 'refresh',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'data' => [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'id' => $userData['id'],
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // RETORNA UN VALOR AL LLAMADOR
        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DECODIFICA Y VERIFICA FIRMA Y EXPIRACIÓN; LANZA EXCEPCIÓN SI EL TOKEN ES INVÁLIDO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function validateToken(string $token): object
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return JWT::decode($token, new Key($this->secretKey, $this->algorithm));
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
