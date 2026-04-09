#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""Copia de annotate_php_caps.py — ejecutar: python3 tools/apply_annotation.py <raíz>"""
import re
import sys
from pathlib import Path

SKIP_DIRS = {"ThirdParty", "vendor", ".git", "node_modules", "Views", "tools", ".ddev"}

KNOWN_CAPS_COMMENTS = frozenset(
    {
        "ETIQUETA DE APERTURA PHP",
        "ETIQUETA DE ECHO CORTO PHP",
        "ETIQUETA PHP CORTA",
        "DECLARA EL ESPACIO DE NOMBRES",
        "IMPORTA UNA CLASE O TRAIT",
        "INICIO DE BLOQUE DE DOCUMENTACIÓN",
        "CIERRE DE BLOQUE DE DOCUMENTACIÓN",
        "LÍNEA DE DOCUMENTACIÓN EN BLOQUE",
        "COMENTARIO DE LÍNEA EXISTENTE",
        "COMENTARIO CON ALMOHADILLA",
        "DECLARA UNA CLASE",
        "DECLARA UNA INTERFAZ",
        "DECLARA UN TRAIT",
        "DECLARA O FIRMA DE MÉTODO O FUNCIÓN",
        "DECLARA PROPIEDAD O CONSTANTE DE CLASE",
        "DECLARA UNA FUNCIÓN",
        "RETORNA UN VALOR AL LLAMADOR",
        "RETORNA SIN VALOR",
        "CONDICIONAL SI",
        "CONDICIONAL SI NO SI",
        "RAMA ALTERNATIVA",
        "BUCLE FOREACH SOBRE COLECCIÓN",
        "BUCLE FOR",
        "BUCLE WHILE",
        "INICIO DE BUCLE DO-WHILE",
        "SELECCIÓN MÚLTIPLE SWITCH",
        "CASO EN SWITCH",
        "CASO POR DEFECTO EN SWITCH",
        "INTERRUMPE BUCLE O SWITCH",
        "SALTA A LA SIGUIENTE ITERACIÓN",
        "LANZA UNA EXCEPCIÓN",
        "INICIO DE BLOQUE TRY",
        "CAPTURA DE EXCEPCIÓN",
        "BLOQUE FINALLY",
        "DELIMITADOR DE BLOQUE",
        "EMITE SALIDA",
        "ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN",
        "LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA",
        "CIERRE DE BLOQUE PHP",
        "INSTRUCCIÓN O DECLARACIÓN PHP",
    }
)


def strip_generated_caps_comments(text: str) -> str:
    lines = text.splitlines(keepends=True)
    out: list[str] = []
    for line in lines:
        m = re.match(r"^(\s*)//\s*(.+?)\s*$", line)
        if m and m.group(2) in KNOWN_CAPS_COMMENTS:
            continue
        out.append(line)
    return "".join(out)


def describe_php_line(line: str) -> str:
    s = line.strip()
    if not s:
        return ""
    if s.startswith("<?php"):
        return "ETIQUETA DE APERTURA PHP"
    if s.startswith("<?="):
        return "ETIQUETA DE ECHO CORTO PHP"
    if re.match(r"^\s*<\?\s*$", line):
        return "ETIQUETA PHP CORTA"
    if s.startswith("namespace "):
        return "DECLARA EL ESPACIO DE NOMBRES"
    if s.startswith("use "):
        return "IMPORTA UNA CLASE O TRAIT"
    if re.match(r"^/\*\*", s):
        return "INICIO DE BLOQUE DE DOCUMENTACIÓN"
    if s == "*/":
        return "CIERRE DE BLOQUE DE DOCUMENTACIÓN"
    if s.startswith("*") and not s.startswith("*/"):
        return "LÍNEA DE DOCUMENTACIÓN EN BLOQUE"
    if s.startswith("//"):
        return "COMENTARIO DE LÍNEA EXISTENTE"
    if s.startswith("#"):
        return "COMENTARIO CON ALMOHADILLA"
    if s.startswith("class "):
        return "DECLARA UNA CLASE"
    if s.startswith("interface "):
        return "DECLARA UNA INTERFAZ"
    if s.startswith("trait "):
        return "DECLARA UN TRAIT"
    if re.match(r"^(public|protected|private|static)\s", s):
        if "function " in s:
            return "DECLARA O FIRMA DE MÉTODO O FUNCIÓN"
        return "DECLARA PROPIEDAD O CONSTANTE DE CLASE"
    if s.startswith("function "):
        return "DECLARA UNA FUNCIÓN"
    if s.startswith("return "):
        return "RETORNA UN VALOR AL LLAMADOR"
    if s.startswith("return;"):
        return "RETORNA SIN VALOR"
    if s.startswith("if ("):
        return "CONDICIONAL SI"
    if s.startswith("elseif ("):
        return "CONDICIONAL SI NO SI"
    if s.startswith("else"):
        return "RAMA ALTERNATIVA"
    if s.startswith("foreach ("):
        return "BUCLE FOREACH SOBRE COLECCIÓN"
    if s.startswith("for ("):
        return "BUCLE FOR"
    if s.startswith("while ("):
        return "BUCLE WHILE"
    if s.startswith("do "):
        return "INICIO DE BUCLE DO-WHILE"
    if s.startswith("switch ("):
        return "SELECCIÓN MÚLTIPLE SWITCH"
    if s.startswith("case "):
        return "CASO EN SWITCH"
    if s.startswith("default:"):
        return "CASO POR DEFECTO EN SWITCH"
    if s.startswith("break"):
        return "INTERRUMPE BUCLE O SWITCH"
    if s.startswith("continue"):
        return "SALTA A LA SIGUIENTE ITERACIÓN"
    if s.startswith("throw "):
        return "LANZA UNA EXCEPCIÓN"
    if s.startswith("try "):
        return "INICIO DE BLOQUE TRY"
    if s.startswith("catch "):
        return "CAPTURA DE EXCEPCIÓN"
    if s.startswith("finally"):
        return "BLOQUE FINALLY"
    if s in ("{", "}"):
        return "DELIMITADOR DE BLOQUE"
    if s.startswith("echo "):
        return "EMITE SALIDA"
    if "=" in s and not s.startswith("=="):
        return "ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN"
    if s.endswith(";") and "(" in s:
        return "LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA"
    if s.startswith("?>"):
        return "CIERRE DE BLOQUE PHP"
    return "INSTRUCCIÓN O DECLARACIÓN PHP"


def comment_prefix_for_line(line: str) -> str:
    indent = re.match(r"^(\s*)", line)
    pad = indent.group(1) if indent else ""
    desc = describe_php_line(line)
    return f"{pad}// {desc}\n"


def annotate_pure_php(lines: list[str]) -> list[str]:
    out: list[str] = []
    in_docblock = False
    seen_nonempty = False
    for line in lines:
        s = line.strip()
        if not in_docblock and "/**" in line:
            in_docblock = True
            out.append(line)
            if "*/" in line:
                in_docblock = False
            continue
        if in_docblock:
            out.append(line)
            if "*/" in line:
                in_docblock = False
            continue
        if s == "":
            out.append(line)
            continue
        if s == "<?php" and not seen_nonempty:
            seen_nonempty = True
            out.append(line)
            continue
        if s.startswith("declare"):
            seen_nonempty = True
            out.append(line)
            continue
        seen_nonempty = True
        out.append(comment_prefix_for_line(line))
        out.append(line)
    return out


def annotate_file(path: Path) -> bool:
    try:
        text = path.read_text(encoding="utf-8")
    except UnicodeDecodeError:
        text = path.read_text(encoding="utf-8", errors="replace")
    text = strip_generated_caps_comments(text)
    lines = text.splitlines(keepends=True)
    new_text = "".join(annotate_pure_php(lines))
    if new_text != text:
        path.write_text(new_text, encoding="utf-8")
        return True
    return False


def should_process(root: Path, path: Path) -> bool:
    try:
        rel = path.relative_to(root)
    except ValueError:
        return False
    for p in rel.parts:
        if p in SKIP_DIRS:
            return False
    if path.suffix.lower() != ".php":
        return False
    return True


def main() -> int:
    if len(sys.argv) < 2:
        print("Uso: apply_annotation.py <raíz_del_proyecto>", file=sys.stderr)
        return 1
    root = Path(sys.argv[1]).resolve()
    if not root.is_dir():
        print("No es un directorio:", root, file=sys.stderr)
        return 1
    changed = 0
    for p in sorted(root.rglob("*.php")):
        if not should_process(root, p):
            continue
        if annotate_file(p):
            changed += 1
            print("Anotado:", p.relative_to(root))
    print(f"Archivos modificados: {changed}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
