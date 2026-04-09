<?php

/**
 * ANOTADOR: INSERTA // COMENTARIOS EN MAYÚSCULAS LÍNEA A LÍNEA (RESPETA BLOQUES /** ... */).
 * USO:
 *   ddev exec php tools/annotate_caps.php
 *   ddev exec php spark annotate:caps
 */

declare(strict_types=1);

function annotatePhp(string $text): string
{
    $eol = str_contains($text, "\r\n") ? "\r\n" : "\n";
    $normalized = str_replace(["\r\n", "\r"], "\n", $text);
    $lines = explode("\n", $normalized);
    $out = [];
    $inDocblock = false;
    $seenNonEmpty = false;
    foreach ($lines as $line) {
        $trim = trim($line);
        if (! $inDocblock && str_contains($line, '/**')) {
            $inDocblock = true;
            $out[] = $line;
            if (str_contains($line, '*/')) {
                $inDocblock = false;
            }
            continue;
        }
        if ($inDocblock) {
            $out[] = $line;
            if (str_contains($line, '*/')) {
                $inDocblock = false;
            }
            continue;
        }
        if ($trim === '') {
            $out[] = $line;
            continue;
        }
        // No se puede poner // antes de <?php en la primera línea no vacía (sería texto fuera de PHP).
        if ($trim === '<?php' && ! $seenNonEmpty) {
            $seenNonEmpty = true;
            $out[] = $line;
            continue;
        }
        if (str_starts_with($trim, 'declare')) {
            $seenNonEmpty = true;
            $out[] = $line;
            continue;
        }
        $seenNonEmpty = true;
        $out[] = commentPrefixForLine($line);
        $out[] = $line;
    }

    $joined = implode($eol, $out);
    $hadTrailing = str_ends_with($normalized, "\n");
    if ($hadTrailing && ! str_ends_with($joined, "\n") && ! str_ends_with($joined, "\r")) {
        $joined .= $eol;
    }

    return $joined;
}

function commentPrefixForLine(string $line): string
{
    preg_match('/^(\s*)/', $line, $m);
    $pad = $m[1] ?? '';

    return $pad . '// ' . describePhpLine($line);
}

function describePhpLine(string $line): string
{
    $s = trim($line);
    if ($s === '') {
        return '';
    }
    if (str_starts_with($s, '<?php')) {
        return 'ETIQUETA DE APERTURA PHP';
    }
    if (str_starts_with($s, '<?=')) {
        return 'ETIQUETA DE ECHO CORTO PHP';
    }
    if (preg_match('/^\s*<\?\s*$/', $line)) {
        return 'ETIQUETA PHP CORTA';
    }
    if (str_starts_with($s, 'namespace ')) {
        return 'DECLARA EL ESPACIO DE NOMBRES';
    }
    if (str_starts_with($s, 'use ')) {
        return 'IMPORTA UNA CLASE O TRAIT';
    }
    if (preg_match('/^\/\*\*/', $s)) {
        return 'INICIO DE BLOQUE DE DOCUMENTACIÓN';
    }
    if ($s === '*/') {
        return 'CIERRE DE BLOQUE DE DOCUMENTACIÓN';
    }
    if (str_starts_with($s, '*') && ! str_starts_with($s, '*/')) {
        return 'LÍNEA DE DOCUMENTACIÓN EN BLOQUE';
    }
    if (str_starts_with($s, '//')) {
        return 'COMENTARIO DE LÍNEA EXISTENTE';
    }
    if (str_starts_with($s, '#')) {
        return 'COMENTARIO CON ALMOHADILLA';
    }
    if (str_starts_with($s, 'class ')) {
        return 'DECLARA UNA CLASE';
    }
    if (str_starts_with($s, 'interface ')) {
        return 'DECLARA UNA INTERFAZ';
    }
    if (str_starts_with($s, 'trait ')) {
        return 'DECLARA UN TRAIT';
    }
    if (preg_match('/^(public|protected|private|static)\s/', $s)) {
        return str_contains($s, 'function ') ? 'DECLARA O FIRMA DE MÉTODO O FUNCIÓN' : 'DECLARA PROPIEDAD O CONSTANTE DE CLASE';
    }
    if (str_starts_with($s, 'function ')) {
        return 'DECLARA UNA FUNCIÓN';
    }
    if (str_starts_with($s, 'return ')) {
        return 'RETORNA UN VALOR AL LLAMADOR';
    }
    if (str_starts_with($s, 'return;')) {
        return 'RETORNA SIN VALOR';
    }
    if (str_starts_with($s, 'if (')) {
        return 'CONDICIONAL SI';
    }
    if (str_starts_with($s, 'elseif (')) {
        return 'CONDICIONAL SI NO SI';
    }
    if (str_starts_with($s, 'else')) {
        return 'RAMA ALTERNATIVA';
    }
    if (str_starts_with($s, 'foreach (')) {
        return 'BUCLE FOREACH SOBRE COLECCIÓN';
    }
    if (str_starts_with($s, 'for (')) {
        return 'BUCLE FOR';
    }
    if (str_starts_with($s, 'while (')) {
        return 'BUCLE WHILE';
    }
    if (str_starts_with($s, 'do ')) {
        return 'INICIO DE BUCLE DO-WHILE';
    }
    if (str_starts_with($s, 'switch (')) {
        return 'SELECCIÓN MÚLTIPLE SWITCH';
    }
    if (str_starts_with($s, 'case ')) {
        return 'CASO EN SWITCH';
    }
    if (str_starts_with($s, 'default:')) {
        return 'CASO POR DEFECTO EN SWITCH';
    }
    if (str_starts_with($s, 'break')) {
        return 'INTERRUMPE BUCLE O SWITCH';
    }
    if (str_starts_with($s, 'continue')) {
        return 'SALTA A LA SIGUIENTE ITERACIÓN';
    }
    if (str_starts_with($s, 'throw ')) {
        return 'LANZA UNA EXCEPCIÓN';
    }
    if (str_starts_with($s, 'try ')) {
        return 'INICIO DE BLOQUE TRY';
    }
    if (str_starts_with($s, 'catch ')) {
        return 'CAPTURA DE EXCEPCIÓN';
    }
    if (str_starts_with($s, 'finally')) {
        return 'BLOQUE FINALLY';
    }
    if ($s === '{' || $s === '}') {
        return 'DELIMITADOR DE BLOQUE';
    }
    if (str_starts_with($s, 'echo ')) {
        return 'EMITE SALIDA';
    }
    if (str_contains($s, '=') && ! str_starts_with($s, '==')) {
        return 'ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN';
    }
    if (str_ends_with($s, ';') && str_contains($s, '(')) {
        return 'LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA';
    }
    if (str_starts_with($s, '?>')) {
        return 'CIERRE DE BLOQUE PHP';
    }

    return 'INSTRUCCIÓN O DECLARACIÓN PHP';
}

function runAnnotateCaps(string $root): int
{
    $skipDirs = ['ThirdParty', 'vendor', '.git', 'node_modules', 'Views', 'tools'];
    $changed = 0;

    $iterator = new RecursiveIteratorIterator(
        new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $current) use ($skipDirs, $root): bool {
                $path = $current->getPathname();
                $rel = str_replace($root . DIRECTORY_SEPARATOR, '', $path);
                foreach (explode(DIRECTORY_SEPARATOR, $rel) as $part) {
                    if ($part !== '' && in_array($part, $skipDirs, true)) {
                        return false;
                    }
                }

                return true;
            }
        )
    );

    foreach ($iterator as $file) {
        /** @var SplFileInfo $file */
        if (! $file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $path = $file->getPathname();
        $text = file_get_contents($path);
        if ($text === false) {
            continue;
        }
        $newText = annotatePhp($text);
        if ($newText !== $text) {
            file_put_contents($path, $newText);
            $changed++;
            echo 'Anotado: ', str_replace($root . DIRECTORY_SEPARATOR, '', $path), PHP_EOL;
        }
    }

    echo "Archivos modificados: {$changed}", PHP_EOL;

    return $changed;
}

if (PHP_SAPI === 'cli' && realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === realpath(__FILE__)) {
    runAnnotateCaps(dirname(__DIR__));
}
