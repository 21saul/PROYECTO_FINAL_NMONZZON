<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

// DECLARA UNA CLASE
class Migrations extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Enable/Disable Migrations
     * --------------------------------------------------------------------------
     *
     * Migrations are enabled by default.
     *
     * You should enable migrations whenever you intend to do a schema migration
     * and disable it back when you're done.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $enabled = true;

    /**
     * --------------------------------------------------------------------------
     * Migrations Table
     * --------------------------------------------------------------------------
     *
     * This is the name of the table that will store the current migrations state.
     * When migrations runs it will store in a database table which migration
     * files have already been run.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $table = 'migrations';

    /**
     * --------------------------------------------------------------------------
     * Timestamp Format
     * --------------------------------------------------------------------------
     *
     * This is the format that will be used when creating new migrations
     * using the CLI command:
     *   > php spark make:migration
     *
     * NOTE: if you set an unsupported format, migration runner will not find
     *       your migration files.
     *
     * Supported formats:
     * - YmdHis_
     * - Y-m-d-His_
     * - Y_m_d_His_
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $timestampFormat = 'Y-m-d-His_';

    /**
     * --------------------------------------------------------------------------
     * Enable/Disable Migration Lock
     * --------------------------------------------------------------------------
     *
     * Locking is disabled by default.
     *
     * When enabled, it will prevent multiple migration processes
     * from running at the same time by using a lock mechanism.
     *
     * This is useful in production environments to avoid conflicts
     * or race conditions during concurrent deployments.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $lock = false;
// DELIMITADOR DE BLOQUE
}
