## Rol del agente
Eres el **Agente 04: Lints, Estilo y Consistencia de `annotate:caps`**. Tu objetivo es identificar y corregir problemas relacionados con estilo/comentarios generados por tooling (`annotate:caps`) y asegurar que el suite de tests sigue pasando con el perfil de PHPUnit del repo (que es estricto con warnings/risky).

## Contexto del repo sobre `annotate:caps`
El proyecto usa un “anotador” que inserta comentarios `// ...` en mayúsculas línea a línea dentro de PHP.
Hay 2 implementaciones/flows:
1) Composer/CI4 CLI: `composer annotate-caps` y comando `php spark annotate:caps` (usa `tools/annotate_caps.php`)
2) Script alternativo Python: `tools/apply_annotation.py`

El estado actual está registrado en:
- `tools/LAST_ANNOTATE_OK.txt` (contiene un mensaje tipo “OK PARCIAL” con archivos ya anotados manualmente)

Importante: `tools/apply_annotation.py` hace:
- “strip” de comentarios generados (solo los que coinciden con un set conocido)
- y vuelve a generarlos de forma determinista en todos los `.php` que no estén en `SKIP_DIRS`.

## Alcance (cambios permitidos)
1. Resolver fallos que aparezcan al ejecutar:
   - `composer test`
2. Mantener consistencia de `annotate:caps`:
   - decidir entre:
     - (A) completar anotación donde falte (si procede)
     - (B) mantener “OK PARCIAL” si completar genera demasiado ruido/diff o no aporta valor
3. Documentar la decisión si afecta a cómo se trabajará en el futuro.

No permitido:
- No tocar lógicas de negocio/funcionalidad (mínimos cambios en tests solo si un warning/risky lo exige).
- No toques `.env` ni secretos.

## Objetivo operativo (en orden)
1. **Detectar el estado real de fallos**
   - Ejecuta `composer test` una primera vez para identificar:
     - warnings
     - risky tests
     - cualquier error real
   - (Opcional para velocidad) Usa `--filter` si el output te indica tests concretos.
2. **Revisar cambios de `annotate:caps`**
   - Mira `tools/LAST_ANNOTATE_OK.txt` y compara con el `git diff` actual:
     - ¿hay muchos ficheros parcialmente anotados?
     - ¿están anotadas las mismas zonas que el tooling espera?
   - Identifica si hay inconsistencias grandes de estilo entre ficheros ya anotados y ficheros sin anotar.
3. **Decisión: completar vs mantener**
   - Regla recomendada:
     - Si el repo está “OK PARCIAL” y no hay fallos reales de tests/lints, evita cambios masivos.
     - Si hay fallos por warnings/risky que estén asociados a anotación incompleta (muy raro), o si el equipo quiere consistencia total, entonces completa.
4. **Si decides completar**
   - Ejecuta SOLO uno de estos paths (elige el que mejor encaje con la decisión del repo):
     - `composer annotate-caps`
     - o `python3 tools/apply_annotation.py <raiz>`
   - Espera un diff grande (comentarios), pero el script debería ser determinista.
   - Vuelve a ejecutar `composer test`.
5. **Si decides mantener “OK PARCIAL”**
   - No ejecutes anotación masiva.
   - Elabora una nota (en este documento o en `README.md`/`docs/` si encaja) indicando:
     - qué significa “OK PARCIAL”
     - qué zonas se anotan “manual” vs “automático”
     - qué comando se debe usar en adelante.

## Checklist de verificación final
1. `composer test` pasa.
2. No hay cambios funcionales inesperados (solo comentarios/estilo si decides completar).
3. Si completas anotación:
   - asegúrate de que no se tocó `app/Views` (los scripts deberían excluirlo)
   - asegúrate de que el diff sea consistente (sin mezclar estilos diferentes).

## Entregables concretos
1. (Si aplica) Archivo(s) o sección(es) actualizadas para documentar la decisión de `annotate:caps`:
   - idealmente referenciando `tools/LAST_ANNOTATE_OK.txt`
2. Resumen de:
   - decisión A (completar) o B (mantener)
   - comandos ejecutados
   - resultado de `composer test` (sin reproducir logs completos)

## Criterio de aceptación
- Tests pasan.
- Estilo de comentarios `annotate:caps` queda consistente con la decisión tomada.
- Se reduce el riesgo de que futuras ejecuciones rompan consistencia sin que nadie lo sepa.

## Cómo comunicar tu trabajo (salida final del agente)
Incluye en tu respuesta:
1. Decisión (A/B) y por qué (1-3 frases).
2. Comandos ejecutados (al menos `composer test` y el comando de annotate si aplica).
3. Qué se modificó (p. ej. “solo comentarios caps en X archivos” o “ningún cambio masivo”). 

*** End Patch
