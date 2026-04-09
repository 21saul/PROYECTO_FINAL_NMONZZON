#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
LIMPIA COMENTARIOS BASURA DEL ANOTADOR AUTOMÁTICO Y AÑADE CABECERAS EN MAYÚSCULAS
CON SENTIDO EN VIEWS, MODELS, CONTROLLERS, CONFIG RELEVANTE Y ASSETS JS/CSS.
USO: python3 tools/nmz_apply_headers.py
"""

from __future__ import annotations

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
MARKER = "NMZ-CABECERA-FICHERO-MAYUSCU"

# LÍNEAS ENTERAS QUE SON RUIDO (ANOTADOR PREVIO)
JUNK_LINE_RES: list[re.Pattern[str]] = [
    re.compile(r"^\s*// DECLARA EL ESPACIO DE NOMBRES\s*$"),
    re.compile(r"^\s*// IMPORTA UNA CLASE O TRAIT\s*$"),
    re.compile(r"^\s*// DECLARA UNA CLASE\s*$"),
    re.compile(r"^\s*// DELIMITADOR DE BLOQUE\s*$"),
    re.compile(r"^\s*// DECLARA PROPIEDAD O CONSTANTE DE CLASE\s*$"),
    re.compile(r"^\s*// INSTRUCCIÓN O DECLARACIÓN PHP\s*$"),
    re.compile(r"^\s*// ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN\s*$"),
    re.compile(r"^\s*// CONDICIONAL SI\s*$"),
    re.compile(r"^\s*// RETORNA UN VALOR AL LLAMADOR\s*$"),
    re.compile(r"^\s*// RETORNA SIN VALOR\s*$"),
    re.compile(r"^\s*// LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA\s*$"),
    re.compile(r"^\s*// DECLARA O FIRMA DE MÉTODO O FUNCIÓN\s*$"),
    re.compile(r"^\s*// COMENTARIO DE LÍNEA EXISTENTE\s*$"),
    re.compile(r"^\s*// CIERRE DE BLOQUE DE DOCUMENTACIÓN\s*$"),
]


def strip_junk_lines(text: str) -> str:
    lines = text.splitlines()
    out: list[str] = []
    for line in lines:
        if any(p.match(line) for p in JUNK_LINE_RES):
            continue
        out.append(line)
    t = "\n".join(out)
    while "\n\n\n" in t:
        t = t.replace("\n\n\n", "\n\n")
    return t


def extract_table(src: str) -> str | None:
    m = re.search(r"protected\s+\$table\s*=\s*['\"]([^'\"]+)['\"]", src)
    return m.group(1).upper() if m else None


def model_header(rel: str, src: str) -> str:
    bn = Path(rel).stem
    tbl = extract_table(src) or "DEFINIDA EN EL MODELO"
    return f"""/*
 * =============================================================================
 * {bn.upper()} — MODELO CODEIGNITER 4 (CAPA DE DATOS)
 * =============================================================================
 * TABLA PRINCIPAL: {tbl}.
 * QUÉ HACE: MAPEA FILAS SQL, CAMPOS PERMITIDOS, VALIDACIÓN Y CALLBACKS DE INSERCIÓN/ACTUALIZACIÓN.
 * POR QUÉ ASÍ: EVITA REPETIR SQL Y REGLAS EN CONTROLADORES; UN MODELO POR TABLA (PATRÓN CI4).
 * =============================================================================
 */

"""


def controller_header(rel: str, src: str) -> str:
    m = re.search(r"^class\s+(\w+)", src, re.MULTILINE)
    cls = m.group(1) if m else Path(rel).stem
    rel_u = rel.replace("\\", "/").upper()
    if "/Api/" in rel.replace("\\", "/"):
        area = "API REST, JSON"
    elif "/Admin/" in rel.replace("\\", "/"):
        area = "PANEL DE ADMINISTRACIÓN"
    else:
        area = "WEB PÚBLICA, HTML"
    return f"""/*
 * =============================================================================
 * {cls.upper()} — CONTROLADOR HTTP — {area}
 * =============================================================================
 * UBICACIÓN: {rel_u}.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

"""


def view_header_block(rel: str) -> str:
    p = rel.replace("\\", "/").upper()
    return f"""/*
 * =============================================================================
 * VISTA CI4: {p}
 * =============================================================================
 * QUÉ HACE: MARCA HTML (Y PHP LIGERO) QUE RENDERIZA EL SISTEMA; PUEDE EXTENDER LAYOUTS Y SECCIONES.
 * POR QUÍ AQUÍ: LA PRESENTACIÓN NO VA EN CONTROLADORES; MANTIENE MAQUETACIÓN Y COPIA EN UN SOLO SITIO.
 * =============================================================================
 */

"""


CONFIG_BLURBS: dict[str, tuple[str, str]] = {
    "App.php": (
        "PARÁMETROS GLOBALES DE LA APP: BASE URL, ÍNDICE PÚBLICO, ZONA HORARIA, LOCALES, COOKIES Y CHARSET.",
        "VALORES SENSIBLES VAN EN .ENV; ESTE FICHERO DEFINE COMPORTAMIENTO Y RUTAS LÓGICAS DEL FRAMEWORK.",
    ),
    "Routes.php": (
        "MAPA DE RUTAS HTTP A CONTROLADORES/MÉTODOS; INCLUYE GRUPOS, FILTROS Y RUTAS DE DESARROLLO.",
        "CENTRALIZA LA API Y LA WEB EN UN SOLO LUGAR PARA NO ACUPLAR URLS A CLASES EN CADA PETICIÓN.",
    ),
    "Database.php": (
        "CONEXIONES A BASE DE DATOS (GRUPO DEFAULT, TEST, ETC.) Y OPCIONES DE PDO PARA CI4.",
        "CREDENCIALES DESDE .ENV; SEPARA ENTORNOS SIN TOCAR CÓDIGO DE MODELOS.",
    ),
    "Filters.php": (
        "REGISTRO Y ALIAS DE FILTROS HTTP (CORS, AUTH, RATE LIMIT, CABECERAS, ETC.).",
        "LOS FILTROS SE APLICAN EN Routes O EN CONTROLADORES SEGÚN NECESIDAD DE SEGURIDAD Y RENDIMIENTO.",
    ),
    "Security.php": (
        "CSRF, COOKIE SEGURA, CIFRADO DE SESIÓN Y REGLAS DE SANITIZACIÓN ASOCIADAS A SEGURIDAD.",
        "AJUSTA COMPORTAMIENTO DE FORMULARIOS Y SESIONES ACORDE AL ENTORNO (HTTPS, PRODUCCIÓN).",
    ),
    "Autoload.php": (
        "PSR-4, HELPERS, ARCHIVOS INCLUIDOS AL ARRANQUE Y MAPA DE CLASES QUE CARGA EL FRAMEWORK.",
        "PERMITE REGISTRAR NAMESPACES Y HELPERS GLOBALES SIN TOCAR EL NÚCLEO DE CODEIGNITER.",
    ),
    "Paths.php": (
        "RUTAS ABSOLUTAS A APP, WRITABLE, SYSTEM, PUBLIC Y VISTAS; BASE PARA RESOLVER INCLUDES.",
        "NORMALMENTE NO SE TOCA; SE GENERA CON LA ESTRUCTURA ESTÁNDAR DEL PROYECTO.",
    ),
    "Services.php": (
        "CONTENEDOR DE SERVICIOS: SOBRESCRITURAS Y FÁBRICAS PARA CLASES DEL NÚCLEO Y CUSTOM.",
        "ÚTIL PARA INYECTAR IMPLEMENTACIONES PROPIAS (CACHÉ, CORREO, ETC.) MANTENIENDO INTERFAZ CI4.",
    ),
    "Email.php": (
        "CONFIGURACIÓN DEL EMAILER CI4: PROTOCOLO, HOST, PUERTO, USUARIO Y OPCIONES DE ENVÍO.",
        "LAS CREDENCIALES REALES DEBEN VENIR DE VARIABLES DE ENTORNO EN PRODUCCIÓN.",
    ),
    "Session.php": (
        "DRIVER DE SESIÓN, NOMBRE DE COOKIE, TIEMPO DE VIDA Y OPCIONES DE REGENERACIÓN.",
        "AFECTA AL LOGIN WEB Y AL PANEL; DEBE SER COHERENTE CON HTTPS Y SAME SITE.",
    ),
    "Constants.php": (
        "CONSTANTES GLOBALES OPCIONALES CARGADAS MUY PRONTO EN EL CICLO DE ARRANQUE.",
        "PARA VALORES ESTÁTICOS QUE NO PERTENECEN A App NI A .ENV.",
    ),
    "Cors.php": (
        "ORIGENES, MÉTODOS Y CABECERAS PERMITIDAS PARA PETICIONES CROSS-ORIGIN (API Y FRONT).",
        "EVITA BLOQUEOS DEL NAVEGADOR CUANDO EL FRONT Y LA API ESTÁN EN DOMINIOS DISTINTOS.",
    ),
    "ContentSecurityPolicy.php": (
        "POLÍTICA CSP PARA MITIGAR XSS: FUENTES PERMITIDAS DE SCRIPT, ESTILO, IMG Y CONEXIONES.",
        "SE AJUSTA CON EL DOMINIO REAL Y LOS CDNS QUE USE EL FRONT (STRIPE, FUENTES, ETC.).",
    ),
    "Feature.php": (
        "FLAGS DE FUNCIONALIDAD EXPERIMENTAL O OPCIONAL DEL NÚCLEO CI4.",
        "PERMITE ACTIVAR CARACTERÍSTICAS SIN CAMBIAR VERSIÓN DEL FRAMEWORK DE GOLPE.",
    ),
    "Validation.php": (
        "REGLAS Y MENSAJES DE VALIDACIÓN REUTILIZABLES A NIVEL DE APLICACIÓN.",
        "COMPLEMENTA LAS REGLAS EN MODELOS Y FORMULARIOS CON CONJUNTOS NOMBRADOS.",
    ),
    "Routing.php": (
        "COMPORTAMIENTO DEL ROUTER: TRAILING SLASH, LOCALES EN URL, 404 PERSONALIZADO, ETC.",
        "AFECTA CÓMO SE RESUELVEN TODAS LAS RUTAS DEFINIDAS EN Routes.php.",
    ),
    "Exceptions.php": (
        "PLANTILLAS Y COMPORTAMIENTO DE EXCEPCIONES NO CAPTURADAS (HTML/CLI).",
        "EN PRODUCCIÓN SE COMBINA CON Boot/production.php PARA NO FILTRAR DETALLES.",
    ),
    "Encryption.php": (
        "CLAVE Y DRIVER PARA SERVICIOS DE CIFRADO DEL FRAMEWORK.",
        "LA CLAVE DEBE SER SECRETA Y ESTABLE; TÍPICAMENTE DESDE .ENV.",
    ),
    "Images.php": (
        "MANIPULACIÓN DE IMÁGENES DEL NÚCLEO (SI SE USA LA LIBRERÍA DE IMÁGENES CI4).",
        "RUTAS Y CALIDAD POR DEFECTO PARA REDIMENSIONADOS Y MINIATURAS.",
    ),
    "Logger.php": (
        "CANALES Y NIVELES DE LOG; DÓNDE SE ESCRIBEN ERRORES Y DEPURACIÓN.",
        "ESENCIAL PARA AUDITORÍA Y DIAGNÓSTICO EN SERVIDOR.",
    ),
    "Cookie.php": (
        "PREFIJOS, DOMINIO Y OPCIONES POR DEFECTO DE COOKIES EMITIDAS POR LA APP.",
        "DEBE ALINEARSE CON HTTPS Y POLÍTICAS DE PRIVACIDAD.",
    ),
    "production.php": (
        "ARRANQUE PRODUCCIÓN: SUPRIME DETALLES DE ERROR AL USUARIO Y DESACTIVA DISPLAY_ERRORS / CI_DEBUG.",
        "SE INCLUYE DESDE EL FRONT CONTROLLER SEGÚN ENTORNO; MANTIENE LOGS SIN EXPONER PILAS EN EL NAVEGADOR.",
    ),
    "development.php": (
        "ARRANQUE DESARROLLO: MÁXIMO ERROR_REPORTING Y DISPLAY_ERRORS PARA DEPURAR EN LOCAL.",
        "NO USAR ASÍ EN PRODUCCIÓN; FACILITA ENCONTRAR FALLOS MIENTRAS SE CODIFICA.",
    ),
    "Pager.php": (
        "PLANTILLA Y OPCIONES DEL PAGINADOR (VISTA NMZ_PAGER, CLASES CSS, NÚMERO DE ENLACES).",
        "CENTRALIZA EL ASPECTO DE LA PAGINACIÓN EN LISTADOS PÚBLICOS Y ADMIN.",
    ),
}


def config_header(rel: str) -> str:
    name = Path(rel).name
    if name in CONFIG_BLURBS:
        a, b = CONFIG_BLURBS[name]
    else:
        a = f"FICHERO DE CONFIGURACIÓN CI4: {name.upper()}."
        b = "PARTE DEL SISTEMA DE CONFIGURACIÓN POR CLASES DE CODEIGNITER 4."
    rel_u = rel.replace("\\", "/").upper()
    return f"""/*
 * =============================================================================
 * CONFIG CI4: {rel_u}
 * =============================================================================
 * {a}
 * {b}
 * =============================================================================
 */

"""


def insert_header_after_open_php(content: str, header: str) -> str:
    if MARKER in content:
        return content
    raw = content.replace("\r\n", "\n").replace("\r", "\n")
    if not raw.startswith("<?php"):
        return content
    lines = raw.split("\n")
    out: list[str] = [lines[0]]
    i = 1
    while i < len(lines) and lines[i].strip() == "":
        i += 1
    # DECLARE(STRICT_TYPES=1);
    if i < len(lines) and "declare" in lines[i] and "strict_types" in lines[i]:
        while i < len(lines):
            out.append(lines[i])
            if ";" in lines[i]:
                i += 1
                break
            i += 1
        while i < len(lines) and lines[i].strip() == "":
            i += 1
    # SUSTITUIR SOLO UN DOCBLOCK TIPO PHPDOC INICIAL /** ... */ (NO TOCAR BLOQUES /* | DE CI)
    if i < len(lines) and lines[i].lstrip().startswith("/**"):
        while i < len(lines):
            if "*/" in lines[i]:
                i += 1
                break
            i += 1
        while i < len(lines) and lines[i].strip() == "":
            i += 1
    out.append("")
    out.append(f"/* {MARKER} */")
    out.extend(header.rstrip().split("\n"))
    out.append("")
    out.extend(lines[i:])
    result = "\n".join(out)
    if raw.endswith("\n") and not result.endswith("\n"):
        result += "\n"
    return result


def process_model(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    text = strip_junk_lines(text)
    if MARKER in text:
        path.write_text(text, encoding="utf-8")
        return
    h = model_header(rel, text)
    path.write_text(insert_header_after_open_php(text, h), encoding="utf-8")


def process_controller(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    text = strip_junk_lines(text)
    if MARKER in text:
        path.write_text(text, encoding="utf-8")
        return
    h = controller_header(rel, text)
    path.write_text(insert_header_after_open_php(text, h), encoding="utf-8")


def process_config(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    text = strip_junk_lines(text)
    if MARKER in text:
        path.write_text(text, encoding="utf-8")
        return
    h = config_header(rel)
    path.write_text(insert_header_after_open_php(text, h), encoding="utf-8")


def process_view(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    text = strip_junk_lines(text)
    if MARKER in text:
        path.write_text(text, encoding="utf-8")
        return
    block = view_header_block(rel)
    raw = text.replace("\r\n", "\n").replace("\r", "\n")
    had_nl = raw.endswith("\n")

    def wrap_body(body: str) -> str:
        core = f"<?php\n/* {MARKER} */\n{block}\n?>\n\n{body}"
        if had_nl and not core.endswith("\n"):
            core += "\n"
        return core

    if raw.startswith("<?="):
        path.write_text(wrap_body(raw), encoding="utf-8")
        return
    if raw.startswith("<?php"):
        first_line = raw.split("\n", 1)[0]
        rest = raw.split("\n", 1)[1] if "\n" in raw else ""
        # UNA SOLA LÍNEA <?php ... ?>
        if "?>" in first_line and first_line.strip().endswith("?>"):
            path.write_text(wrap_body(raw), encoding="utf-8")
            return
        # <?php SEGUIDO DE CONTENIDO MULTILÍNEA
        inner = f"<?php\n/* {MARKER} */\n{block}" + (rest if rest.startswith("\n") else "\n" + rest)
        if had_nl and not inner.endswith("\n"):
            inner += "\n"
        path.write_text(inner, encoding="utf-8")
        return
    path.write_text(wrap_body(raw), encoding="utf-8")


def process_js(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    if MARKER in text:
        return
    p = rel.replace("\\", "/").upper()
    block = f"""/*
 * =============================================================================
 * SCRIPT FRONT: {p}
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

"""
    path.write_text(block + text.lstrip("\n"), encoding="utf-8")


def process_css(path: Path, rel: str) -> None:
    text = path.read_text(encoding="utf-8")
    p = rel.replace("\\", "/").upper()
    block = f"""/*
 * =============================================================================
 * ESTILOS: {p}
 * =============================================================================
 * QUÉ HACE: REGLAS CSS PARA MAQUETACIÓN, TEMA, COMPONENTES Y RESPONSIVE DEL SITIO O DEL ADMIN.
 * POR QUÍ CSS: PRESENTACIÓN SEPARADA DEL HTML/PHP; VARIABLES Y SECCIONES ORDENAN MILES DE REGLAS.
 * =============================================================================
 */

"""
    if text.strip().startswith("/*"):
        end = text.find("*/")
        if end != -1:
            rest = text[end + 2 :].lstrip("\n")
            path.write_text(block + rest, encoding="utf-8")
            return
    path.write_text(block + text.lstrip("\n"), encoding="utf-8")


IMPORTANT_CONFIG = [
    "App.php",
    "Routes.php",
    "Database.php",
    "Filters.php",
    "Security.php",
    "Autoload.php",
    "Paths.php",
    "Services.php",
    "Email.php",
    "Session.php",
    "Constants.php",
    "Cors.php",
    "ContentSecurityPolicy.php",
    "Feature.php",
    "Validation.php",
    "Routing.php",
    "Exceptions.php",
    "Encryption.php",
    "Images.php",
    "Logger.php",
    "Cookie.php",
    "Pager.php",
    "Boot/production.php",
    "Boot/development.php",
]


def main() -> None:
    for p in sorted((ROOT / "app" / "Models").glob("*.php")):
        process_model(p, str(p.relative_to(ROOT)))

    for p in (ROOT / "app" / "Controllers").rglob("*.php"):
        process_controller(p, str(p.relative_to(ROOT)))

    for p in (ROOT / "app" / "Views").rglob("*.php"):
        process_view(p, str(p.relative_to(ROOT)))

    cfg = ROOT / "app" / "Config"
    for sub in IMPORTANT_CONFIG:
        p = cfg / sub
        if p.is_file():
            process_config(p, str(p.relative_to(ROOT)))

    assets = ROOT / "public" / "assets"
    for p in assets.rglob("*.js"):
        process_js(p, str(p.relative_to(ROOT)))
    for p in assets.rglob("*.css"):
        process_css(p, str(p.relative_to(ROOT)))

    print("HECHO: MODELS, CONTROLLERS, VIEWS, CONFIG (LISTA), ASSETS JS/CSS.")


if __name__ == "__main__":
    main()
