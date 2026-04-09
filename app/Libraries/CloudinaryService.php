<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// DECLARA UNA CLASE
class CloudinaryService
// DELIMITADOR DE BLOQUE
{
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private bool $isConfigured = false;
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $cloudName = '';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $apiKey = '';
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $apiSecret = '';

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->cloudName = (string) env('CLOUDINARY_CLOUD_NAME', '');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->apiKey = (string) env('CLOUDINARY_API_KEY', '');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->apiSecret = (string) env('CLOUDINARY_API_SECRET', '');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->isConfigured = ($this->cloudName !== '' && $this->apiKey !== '' && $this->apiSecret !== '');
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function isAvailable(): bool
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $this->isConfigured;
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function upload(string $filePath, array $options = []): array
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!$this->isConfigured) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Cloudinary no está configurado.');
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $defaultOptions = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'folder' => 'nmonzzon-studio',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'quality' => 'auto',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'fetch_format' => 'auto',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $options = array_merge($defaultOptions, $options);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $timestamp = time();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $params = array_merge($options, ['timestamp' => $timestamp]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        ksort($params);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $toSign = '';
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($params as $k => $v) {
            // CONDICIONAL SI
            if ($k === 'file') {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            // DELIMITADOR DE BLOQUE
            }
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $toSign .= ($toSign ? '&' : '') . $k . '=' . $v;
        // DELIMITADOR DE BLOQUE
        }
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $signature = sha1($toSign . $this->apiSecret);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $postFields = array_merge($params, [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'file' => new \CURLFile($filePath),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'api_key' => $this->apiKey,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'signature' => $signature,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $ch = curl_init("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/upload");
        // INSTRUCCIÓN O DECLARACIÓN PHP
        curl_setopt_array($ch, [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_POST => true,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_POSTFIELDS => $postFields,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_RETURNTRANSFER => true,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_TIMEOUT => 30,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response = curl_exec($ch);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        curl_close($ch);

        // CONDICIONAL SI
        if ($httpCode !== 200) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Cloudinary upload failed: ' . $response);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $result = json_decode($response, true);
        // RETORNA UN VALOR AL LLAMADOR
        return [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'public_id' => $result['public_id'] ?? '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'url' => $result['secure_url'] ?? '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'thumbnail' => $this->generateThumbnailUrl($result['public_id'] ?? ''),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'width' => $result['width'] ?? 0,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'height' => $result['height'] ?? 0,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'format' => $result['format'] ?? '',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'bytes' => $result['bytes'] ?? 0,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function delete(string $publicId): bool
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!$this->isConfigured) {
            // RETORNA UN VALOR AL LLAMADOR
            return false;
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $timestamp = time();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $toSign = "public_id={$publicId}&timestamp={$timestamp}" . $this->apiSecret;
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $signature = sha1($toSign);

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $ch = curl_init("https://api.cloudinary.com/v1_1/{$this->cloudName}/image/destroy");
        // INSTRUCCIÓN O DECLARACIÓN PHP
        curl_setopt_array($ch, [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_POST => true,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_POSTFIELDS => [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'public_id' => $publicId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'timestamp' => $timestamp,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'api_key' => $this->apiKey,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'signature' => $signature,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ],
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_RETURNTRANSFER => true,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            CURLOPT_TIMEOUT => 10,
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $response = curl_exec($ch);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        curl_close($ch);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $data = json_decode($response, true);
        // RETORNA UN VALOR AL LLAMADOR
        return ($data['result'] ?? '') === 'ok';
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateUrl(string $publicId, array $transformations = []): string
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $baseUrl = "https://res.cloudinary.com/{$this->cloudName}/image/upload/";
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $transforms = [];
        // CONDICIONAL SI
        if (!empty($transformations)) {
            // BUCLE FOREACH SOBRE COLECCIÓN
            foreach ($transformations as $key => $value) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $transforms[] = "{$key}_{$value}";
            // DELIMITADOR DE BLOQUE
            }
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } else {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $transforms = ['q_auto', 'f_auto'];
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return $baseUrl . implode(',', $transforms) . '/' . $publicId;
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generateThumbnailUrl(string $publicId): string
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $this->generateUrl($publicId, ['w' => 400, 'h' => 400, 'c' => 'fill', 'q' => 'auto', 'f' => 'auto']);
    // DELIMITADOR DE BLOQUE
    }

    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function generatePortfolioUrl(string $publicId): string
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $this->generateUrl($publicId, ['w' => 1200, 'q' => 'auto:best', 'f' => 'auto']);
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
