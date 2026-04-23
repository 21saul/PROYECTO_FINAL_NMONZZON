## Rol del agente
Eres el **Agente 01: Documentación y Onboarding**. Tu misión es dejar el proyecto fácil de entender para un nuevo dev y alinear la documentación con lo que realmente hace el código.

## Contexto
Este repo es una app full-stack en **CodeIgniter 4** (PHP) con backend (API + panel admin) y frontend servido desde `public/`. Ya existen varios documentos en `docs/` con descripciones técnicas (crons, n8n, resúmenes, mediciones).

## Alcance (solo documentación)
No modifiques lógica de negocio. Cambios permitidos:
1. Documentación general:
   - `README.md` (verificar si ya está completo; si no, completar secciones que falten)
   - `tests/README.md` (mejorar para describir qué cubren los tests del proyecto real)
2. Documentación en `docs/`:
   - Crear/actualizar un documento o sección para:
     - Cómo se ejecutan tests en este proyecto.
     - Cómo funciona el tooling `annotate:caps` (`tools/annotate_caps.php` + comando `php spark annotate:caps` o `composer annotate-caps`)
     - Qué significa el estado actual indicado en `tools/LAST_ANNOTATE_OK.txt`.

Cambios no permitidos (fuera del alcance):
- No toques `app/` excepto para citar rutas/archivos con precisión en la documentación.
- No toques código de servicios/lógica ni Vistas, salvo que sea estrictamente para corregir referencias textuales en docs.

## Qué hacer (pasos)
1. **Auditar estado actual de documentación**
   - Revisa `README.md` y confirma si realmente está “casi vacío” o si ya contiene contenido.
   - Revisa `tests/README.md` y detecta por qué no está alineado con los tests reales de este repo (si es que no lo está).
2. **Alinear “tests” con el proyecto real**
   - Inspecciona `tests/Unit`, `tests/Feature` y `tests/Integration` (si existe).
   - Resume en `tests/README.md`:
     - Listado de tests existentes (por archivo) y qué cubren en 1 línea.
     - Recomendación de cómo ejecutar:
       - Todos los tests
       - Solo `Unit`
       - Solo `Feature`
       - Cómo filtrar por nombre de test.
   - Aclara si los tests usan base de datos real (CI4 puede usar DB test según `phpunit.xml.dist`/config).
3. **Documentar tooling `annotate:caps`**
   - Usa `tools/annotate_caps.php`, `app/Commands/AnnotateCaps.php` y `tools/LAST_ANNOTATE_OK.txt` para describir:
     - Cómo se ejecuta (`php tools/annotate_caps.php`, `ddev exec php spark annotate:caps`, o `composer annotate-caps`).
     - Qué modifica exactamente (inserta comentarios `//` en mayúsculas línea a línea en PHP).
     - Qué excluye (por ejemplo `app/Views`, `tools`, etc., según script).
     - Qué significa “OK PARCIAL” en el contexto del repo (qué implica para el trabajo futuro).
4. **Verificación**
   - No cambies código, pero sí asegúrate de que la documentación no contradice:
     - `phpunit.xml.dist` (suites, directorios)
     - `composer.json` (scripts `test`, `annotate-caps`)
     - Estructura `docs/` ya existente.

## Entregables concretos
- Actualizar `tests/README.md` para que refleje:
  - suites reales
  - lista corta “qué cubre cada test”
  - comandos de ejecución exactos
- Actualizar `README.md` únicamente si detectas secciones incompletas o contradicciones.
- Añadir en `docs/` (o en `README.md` si encaja mejor) un documento corto “Herramientas de anotación” con:
  - cómo ejecutar `annotate:caps`
  - qué excluye
  - estado actual `tools/LAST_ANNOTATE_OK.txt`

## Criterio de aceptación
- Un dev nuevo puede:
  - ejecutar tests sin adivinar comandos
  - entender qué es `annotate:caps` y cómo/por qué se usa
  - saber qué tests existen y qué parte del sistema validan
- No se tocan rutas ni lógica de negocio.

## Cómo comunicar tu trabajo (salida final del agente)
En tu respuesta final, incluye:
1. Archivos modificados (lista)
2. Qué cambiaste en cada uno (1-3 bullets)
3. Comandos de verificación usados (aunque no ejecutes `phpunit`, menciona que revisaste `phpunit.xml.dist`/`composer.json`).

