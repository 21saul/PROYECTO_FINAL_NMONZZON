<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Config\BaseConfig;

// DECLARA UNA CLASE
class Honeypot extends BaseConfig
// DELIMITADOR DE BLOQUE
{
    /**
     * Makes Honeypot visible or not to human
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $hidden = true;

    /**
     * Honeypot Label Content
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $label = 'Fill This Field';

    /**
     * Honeypot Field Name
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $name = 'honeypot';

    /**
     * Honeypot HTML Template
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $template = '<label>{label}</label><input type="text" name="{name}" value="">';

    /**
     * Honeypot container
     *
     * If you enabled CSP, you can remove `style="display:none"`.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $container = '<div style="display:none">{template}</div>';

    /**
     * The id attribute for Honeypot container tag
     *
     * Used when CSP is enabled.
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public string $containerId = 'hpc';
// DELIMITADOR DE BLOQUE
}
