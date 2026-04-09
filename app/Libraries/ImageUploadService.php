<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE SUBIDA SEGURA DE IMÁGENES AL SERVIDOR.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * VALIDA MIME, TAMAÑO Y CONTENIDO REAL DEL ARCHIVO; GUARDA EN PUBLIC/UPLOADS CON NOMBRE ALEATORIO.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\HTTP\Files\UploadedFile;

// DECLARA UNA CLASE
class ImageUploadService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // TIPOS MIME PERMITIDOS (DEBEN COINCIDIR CON LA VERIFICACIÓN POR FINFO)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private array $allowedMimeTypes = [
        // INSTRUCCIÓN O DECLARACIÓN PHP
        'image/jpeg', 'image/png', 'image/gif', 'image/webp',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];
    // COMENTARIO DE LÍNEA EXISTENTE
    // LÍMITE EN BYTES (5 MEGABYTES)
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private int $maxFileSize = 5242880;
    // COMENTARIO DE LÍNEA EXISTENTE
    // DIRECTORIO RAÍZ DE TODAS LAS SUBIDAS
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private string $basePath;

    // COMENTARIO DE LÍNEA EXISTENTE
    // DEFINE LA RUTA BASE DONDE SE CREARÁN SUBCARPETAS POR TIPO (P. EJ. IMAGES). USA FCPATH PARA QUE SEAN ACCESIBLES VÍA WEB.
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->basePath = FCPATH . 'uploads/';
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // VALIDA EL ARCHIVO SUBIDO, LO MUEVE A SUBDIR Y DEVUELVE METADATOS PARA GUARDAR EN BD
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function upload(UploadedFile $file, string $subdir = 'images'): array
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if (!$file->isValid()) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Archivo no válido.');
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if (!in_array($file->getMimeType(), $this->allowedMimeTypes)) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Tipo de archivo no permitido. Solo JPEG, PNG, GIF y WebP.');
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($file->getSize() > $this->maxFileSize) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('El archivo excede el tamaño máximo de 5MB.');
        // DELIMITADOR DE BLOQUE
        }

        // COMENTARIO DE LÍNEA EXISTENTE
        // VERIFICACIÓN ADICIONAL DEL CONTENIDO REAL PARA EVITAR ARCHIVOS CON EXTENSIÓN ENGAÑOSA
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $realMime = $finfo->file($file->getTempName());
        // CONDICIONAL SI
        if (!in_array($realMime, $this->allowedMimeTypes)) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('El contenido del archivo no coincide con un tipo de imagen válido.');
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $uploadPath = $this->basePath . $subdir . '/';
        // CONDICIONAL SI
        if (!is_dir($uploadPath)) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            mkdir($uploadPath, 0755, true);
        // DELIMITADOR DE BLOQUE
        }

        // COMENTARIO DE LÍNEA EXISTENTE
        // NOMBRE ÚNICO PARA EVITAR SOBRESCRITURAS Y ADIVINACIÓN DE RUTAS
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $newName = bin2hex(random_bytes(16)) . '.' . $file->guessExtension();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $file->move($uploadPath, $newName);

        // RETORNA UN VALOR AL LLAMADOR
        return [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'filename'  => $newName,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'path'      => 'uploads/' . $subdir . '/' . $newName,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'full_path' => $uploadPath . $newName,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'mime_type' => $file->getMimeType(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'size'      => $file->getSize(),
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ELIMINA UN ARCHIVO RELATIVO A BASEPATH; DEVUELVE FALSE SI NO EXISTÍA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function delete(string $path): bool
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $fullPath = $this->basePath . $path;
        // CONDICIONAL SI
        if (file_exists($fullPath)) {
            // RETORNA UN VALOR AL LLAMADOR
            return unlink($fullPath);
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return false;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
