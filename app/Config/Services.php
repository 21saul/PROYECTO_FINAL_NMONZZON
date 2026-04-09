<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONFIG CI4: APP/CONFIG/SERVICES.PHP
 * =============================================================================
 * CONTENEDOR DE SERVICIOS: SOBRESCRITURAS Y FÁBRICAS PARA CLASES DEL NÚCLEO Y CUSTOM.
 * ÚTIL PARA INYECTAR IMPLEMENTACIONES PROPIAS (CACHÉ, CORREO, ETC.) MANTENIENDO INTERFAZ CI4.
 * =============================================================================
 */

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * public static function example($getShared = true)
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * {
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *     if ($getShared) {
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *         return static::getSharedInstance('example');
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *     }
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     *     return new \CodeIgniter\Example();
     // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
     * }
     */
}