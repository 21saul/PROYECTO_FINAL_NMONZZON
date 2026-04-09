<?php

// DECLARA EL ESPACIO DE NOMBRES
namespace Config;

// DECLARA UNA CLASE
class DocTypes
// DELIMITADOR DE BLOQUE
{
    /**
     * List of valid document types.
     *
     * @var array<string, string>
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public array $list = [
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml11'           => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml1-strict'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml1-trans'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml1-frame'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml-basic11'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'html5'             => '<!DOCTYPE html>',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'html4-strict'      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'html4-trans'       => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'html4-frame'       => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mathml1'           => '<!DOCTYPE math SYSTEM "http://www.w3.org/Math/DTD/mathml1/mathml.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'mathml2'           => '<!DOCTYPE math PUBLIC "-//W3C//DTD MathML 2.0//EN" "http://www.w3.org/Math/DTD/mathml2/mathml2.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'svg10'             => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN" "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'svg11'             => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'svg11-basic'       => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Basic//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-basic.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'svg11-tiny'        => '<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1 Tiny//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11-tiny.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml-math-svg-xh' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml-math-svg-sh' => '<!DOCTYPE svg:svg PUBLIC "-//W3C//DTD XHTML 1.1 plus MathML 2.0 plus SVG 1.1//EN" "http://www.w3.org/2002/04/xhtml-math-svg/xhtml-math-svg.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml-rdfa-1'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">',
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        'xhtml-rdfa-2'      => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.1//EN" "http://www.w3.org/MarkUp/DTD/xhtml-rdfa-2.dtd">',
    // INSTRUCCIÓN O DECLARACIÓN PHP
    ];

    /**
     * Whether to remove the solidus (`/`) character for void HTML elements (e.g. `<input>`)
     * for HTML5 compatibility.
     *
     * Set to:
     *    `true` - to be HTML5 compatible
     *    `false` - to be XHTML compatible
     */
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    public bool $html5 = true;
// DELIMITADOR DE BLOQUE
}
