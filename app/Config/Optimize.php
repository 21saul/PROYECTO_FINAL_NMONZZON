<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

/**
 * Optimization Configuration.
 *
 * NOTE: This class does not extend BaseConfig for performance reasons.
 *       So you cannot replace the property values with Environment Variables.
 *
 * WARNING: Do not use these options when running the app in the Worker Mode.
 */
// DECLARA UNA CLASE
class Optimize
// DELIMITADOR DE BLOQUE
{
    /**
     * --------------------------------------------------------------------------
     * Config Caching
     * --------------------------------------------------------------------------
     *
     * @see https://codeigniter.com/user_guide/concepts/factories.html#config-caching
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $configCacheEnabled = false;

    /**
     * --------------------------------------------------------------------------
     * Config Caching
     * --------------------------------------------------------------------------
     *
     * @see https://codeigniter.com/user_guide/concepts/autoloader.html#file-locator-caching
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $locatorCacheEnabled = false;
// DELIMITADOR DE BLOQUE
}
