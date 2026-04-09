<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

/**
 * This file contains an array of mime types.  It is used by the
 * Upload class to help identify allowed file types.
 *
 * When more than one variation for an extension exist (like jpg, jpeg, etc)
 * the most common one should be first in the array to aid the guess*
 * methods. The same applies when more than one mime-type exists for a
 * single extension.
 *
 * When working with mime types, please make sure you have the ´fileinfo´
 * extension enabled to reliably detect the media types.
 */
// DECLARA UNA CLASE
class Mimes
// DELIMITADOR DE BLOQUE
{
    /**
     * Map of extensions to mime types.
     *
     * @var array<string, list<string>|string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public static array $mimes = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'hqx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/mac-binhex40',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/mac-binhex',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-binhex40',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-mac-binhex40',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cpt' => 'application/mac-compactpro',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'csv' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/csv',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/x-comma-separated-values',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/comma-separated-values',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-csv',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/x-csv',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/csv',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.msexcel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bin' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/macbinary',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/mac-binary',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/octet-stream',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-binary',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-macbinary',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dms' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'lha' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'lzh' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'exe' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/octet-stream',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.microsoft.portable-executable',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-dosexec',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-msdownload',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'class' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'psd'   => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-photoshop',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/vnd.adobe.photoshop',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'so'  => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sea' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dll' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'oda' => 'application/oda',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'pdf' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pdf',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/force-download',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-download',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ai' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pdf',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/postscript',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'eps'  => 'application/postscript',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ps'   => 'application/postscript',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'smi'  => 'application/smil',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'smil' => 'application/smil',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mif'  => 'application/vnd.mif',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xls'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msexcel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-msexcel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-ms-excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-dos_ms_excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/xls',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-xls',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/download',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-office',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ppt' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-powerpoint',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/powerpoint',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-office',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'pptx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wbxml' => 'application/wbxml',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wmlc'  => 'application/wmlc',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dcr'   => 'application/x-director',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dir'   => 'application/x-director',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dxr'   => 'application/x-director',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dvi'   => 'application/x-dvi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gtar'  => 'application/x-gtar',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gz'    => 'application/x-gzip',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gzip'  => 'application/x-gzip',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'php'   => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-php',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-httpd-php',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/php',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/php',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/x-php',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-httpd-php-source',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'php4'  => 'application/x-httpd-php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'php3'  => 'application/x-httpd-php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'phtml' => 'application/x-httpd-php',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'phps'  => 'application/x-httpd-php-source',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'js'    => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-javascript',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'swf' => 'application/x-shockwave-flash',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sit' => 'application/x-stuffit',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'tar' => 'application/x-tar',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'tgz' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-tar',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-gzip-compressed',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'z'     => 'application/x-compress',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml' => 'application/xhtml+xml',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xht'   => 'application/xhtml+xml',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'zip'   => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip-compressed',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/s-compressed',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'multipart/x-zip',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rar' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.rar',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-rar',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/rar',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-rar-compressed',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mid'  => 'audio/midi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'midi' => 'audio/midi',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mpga' => 'audio/mpeg',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mp2'  => 'audio/mpeg',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mp3'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/mpeg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/mpg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/mpeg3',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/mp3',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'aif' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/x-aiff',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/aiff',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'aiff' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/x-aiff',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/aiff',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'aifc' => 'audio/x-aiff',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ram'  => 'audio/x-pn-realaudio',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rm'   => 'audio/x-pn-realaudio',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rpm'  => 'audio/x-pn-realaudio-plugin',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ra'   => 'audio/x-realaudio',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rv'   => 'video/vnd.rn-realvideo',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wav'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/x-wav',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/wave',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/wav',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'bmp' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-bitmap',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-xbitmap',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-win-bitmap',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-windows-bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/ms-bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-ms-bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-bmp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-win-bitmap',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gif' => 'image/gif',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpg' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpeg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/pjpeg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpeg' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpeg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/pjpeg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpe' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpeg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/pjpeg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jp2' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'j2k' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpf' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpg2' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jpm' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mj2' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mjp2' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jp2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mj2',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpx',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/jpm',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'png' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/png',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-png',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'webp' => 'image/webp',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'tif'  => 'image/tiff',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'tiff' => 'image/tiff',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'css'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/css',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'html' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/html',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'htm' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/html',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'shtml' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/html',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'txt'  => 'text/plain',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'text' => 'text/plain',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'log'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/x-log',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rtx' => 'text/richtext',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rtf' => 'text/rtf',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xml' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xsl' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/xsl',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/xml',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mpeg' => 'video/mpeg',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mpg'  => 'video/mpeg',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mpe'  => 'video/mpeg',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'qt'   => 'video/quicktime',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mov'  => 'video/quicktime',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'avi'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/x-msvideo',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/msvideo',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/avi',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-troff-msvideo',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'movie' => 'video/x-sgi-movie',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'doc'   => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-office',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'docx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dot' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-office',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'dotx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xlsx' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-excel',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'word' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/msword',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/octet-stream',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xl'   => 'application/excel',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'eml'  => 'message/rfc822',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'json' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/json',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/json',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'pem' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-x509-user-cert',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-pem-file',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/octet-stream',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p10' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-pkcs10',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkcs10',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p12' => 'application/x-pkcs12',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p7a' => 'application/x-pkcs7-signature',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p7c' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkcs7-mime',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-pkcs7-mime',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p7m' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkcs7-mime',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-pkcs7-mime',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p7r' => 'application/x-pkcs7-certreqresp',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'p7s' => 'application/pkcs7-signature',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'crt' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-x509-ca-cert',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-x509-user-cert',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkix-cert',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'crl' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkix-crl',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkcs-crl',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'der' => 'application/x-x509-ca-cert',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'kdb' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'pgp' => 'application/pgp',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'gpg' => 'application/gpg-keys',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'sst' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'csr' => 'application/octet-stream',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'rsa' => 'application/x-pkcs7',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cer' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/pkix-cert',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-x509-ca-cert',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        '3g2' => 'video/3gpp2',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        '3gp' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/3gp',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/3gpp',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mp4' => 'video/mp4',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'm4a' => 'audio/x-m4a',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'f4v' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/mp4',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/x-f4v',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'flv'  => 'video/x-flv',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'webm' => 'video/webm',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'aac'  => 'audio/x-acc',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'm4u'  => 'application/vnd.mpegurl',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'm3u'  => 'text/plain',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xspf' => 'application/xspf+xml',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'vlc'  => 'application/videolan',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wmv'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/x-ms-wmv',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/x-ms-asf',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'au'   => 'audio/x-au',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ac3'  => 'audio/ac3',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'flac' => 'audio/x-flac',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ogg'  => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/ogg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/ogg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/ogg',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'kmz' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.google-earth.kmz',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'kml' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.google-earth.kml+xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/xml',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ics'  => 'text/calendar',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ical' => 'text/calendar',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'zsh'  => 'text/x-scriptzsh',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        '7zip' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-compressed',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-zip-compressed',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/zip',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'multipart/x-zip',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'cdr' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/cdr',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/coreldraw',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-cdr',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-coreldraw',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/cdr',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-cdr',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'zz-application/zz-winassoc-cdr',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'wma' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'audio/x-ms-wma',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'video/x-ms-asf',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'jar' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/java-archive',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-java-application',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-jar',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-compressed',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'svg' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/svg+xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/svg',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/xml',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/xml',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'vcf' => 'text/x-vcard',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'srt' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/srt',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'vtt' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/vtt',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'text/plain',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'ico' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-icon',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/x-ico',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'image/vnd.microsoft.icon',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'stl' => [
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/sla',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/vnd.ms-pki.stl',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/x-navistyle',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'model/stl',
            // INSTRUCCIÓN O DECLARACIÓN PHP
            'application/octet-stream',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ],
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * Attempts to determine the best mime type for the given file extension.
     *
     * @return string|null The mime type found, or none if unable to determine.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public static function guessTypeFromExtension(string $extension)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $extension = trim(strtolower($extension), '. ');

        // CONDICIONAL SI
        if (! array_key_exists($extension, static::$mimes)) {
            // RETORNA UN VALOR AL LLAMADOR
            return null;
        // DELIMITADOR DE BLOQUE
        }

        // RETORNA UN VALOR AL LLAMADOR
        return is_array(static::$mimes[$extension]) ? static::$mimes[$extension][0] : static::$mimes[$extension];
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Attempts to determine the best file extension for a given mime type.
     *
     * @param string|null $proposedExtension - default extension (in case there is more than one with the same mime type)
     *
     * @return string|null The extension determined, or null if unable to match.
     */
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public static function guessExtensionFromType(string $type, ?string $proposedExtension = null)
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $type = trim(strtolower($type), '. ');

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $proposedExtension = trim(strtolower($proposedExtension ?? ''));

        // CONDICIONAL SI
        if (
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $proposedExtension !== ''
            // INSTRUCCIÓN O DECLARACIÓN PHP
            && array_key_exists($proposedExtension, static::$mimes)
            // INSTRUCCIÓN O DECLARACIÓN PHP
            && in_array($type, (array) static::$mimes[$proposedExtension], true)
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ) {
            // COMENTARIO DE LÍNEA EXISTENTE
            // The detected mime type matches with the proposed extension.
            // RETORNA UN VALOR AL LLAMADOR
            return $proposedExtension;
        // DELIMITADOR DE BLOQUE
        }

        // COMENTARIO DE LÍNEA EXISTENTE
        // Reverse check the mime type list if no extension was proposed.
        // COMENTARIO DE LÍNEA EXISTENTE
        // This search is order sensitive!
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (static::$mimes as $ext => $types) {
            // CONDICIONAL SI
            if (in_array($type, (array) $types, true)) {
                // RETORNA UN VALOR AL LLAMADOR
                return $ext;
            // DELIMITADOR DE BLOQUE
            }
        // DELIMITADOR DE BLOQUE
        }

        // RETORNA UN VALOR AL LLAMADOR
        return null;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
