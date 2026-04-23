## Rol del agente
Eres el **Agente 03: Tests de Servicios Infra**. Tu objetivo es añadir (o ampliar) tests para servicios que hoy dependen de integraciones o de filesystem y que por tanto suelen quedar sin cobertura o con cobertura insuficiente.

## Servicios y funciones objetivo
Debes cubrir, al menos con pruebas de error/contrato y de casos deterministas:
1. `app/Libraries/StripeService.php` (sin llamadas reales a Stripe)
2. `app/Libraries/CloudinaryService.php` (sin llamadas reales)
3. `app/Libraries/ImageUploadService.php` (subida local validada + delete)
4. `app/Libraries/PortraitPricingService.php` (cálculo de precio + casos borde)
5. `app/Helpers/portrait_helper.php` (parseo/merge de `reference_photo`)

## Alcance (cambios permitidos)
1. Crear/actualizar tests en:
   - `tests/Unit/` y/o `tests/Integration/`
2. Puedes crear archivos auxiliares dentro de `tests/_support/` si hace falta para fixtures (por ejemplo, PNG base64).

No permitido:
- No cambies lógica de negocio.
- No toques `.env` ni dependencias.
- No hagas llamadas reales a Stripe ni Cloudinary.

## Estrategia para evitar llamadas externas
1. `StripeService`:
   - `StripeService::__construct()` usa `StripeConfig::secretKey()` y marca `configured=false` si:
     - clave es vacía o placeholder
   - `StripeConfig` toma la clave de `.env` si no es placeholder, y si es placeholder consulta `SiteSettingModel`.
   - Por tanto, para tests seguros:
     - Implementa **Integration tests** que preparen `site_settings` con:
       - `stripe_secret_key` como placeholder (ej. `sk_test_xxx...`)
       - `stripe_public_key` como placeholder (si necesitas)
     - Verifica que:
       - `new StripeService()->isConfigured()` da `false`
       - `createPaymentIntent()` lanza `RuntimeException`
       - `confirmPayment()` lanza `RuntimeException`
   - Evita probar “casos éxito” porque requeriría llamar a la API real.
2. `CloudinaryService`:
   - Para tests deterministas, prueba:
     - `isAvailable()` con env sin config real => false
     - `generateUrl()`, `generateThumbnailUrl()`, `generatePortfolioUrl()` (no lanzan curl)
     - `upload()` / `delete()` solo en modo “no configurado”:
       - `upload()` debe lanzar `RuntimeException` si no está configurado
       - `delete()` debe devolver `false` si no está configurado

## Casos de test mínimos (contrato)

### A) `portrait_helper.php` (Unit)
Cubre estas funciones (nombre tal cual en el helper):
1. `portrait_reference_photo_paths(null)` => `[]`
2. `portrait_reference_photo_paths('')` => `[]`
3. `portrait_reference_photo_paths('["a","b"]')` => `['a','b']` (ignora vacíos si aparecen)
4. `portrait_reference_photo_paths('a')` => `['a']`
5. `portrait_reference_photo_store_merged(null, 'uploads/x1.png')` => `'uploads/x1.png'`
6. `portrait_reference_photo_store_merged('uploads/x1.png', 'uploads/x2.png')`
   - debe devolver JSON si resultan 2+ paths (orden no estricto, pero sin duplicados)
7. `portrait_reference_photo_store_merged('uploads/x1.png', 'uploads/x1.png')`
   - debe deduplicar => vuelve a `'uploads/x1.png'` (string, no JSON)

Notas:
- Asegura que el helper esté cargado en el test. Si no está autoloaded, haz `helper('portrait_helper')` o `require_once` del fichero.

### B) `ImageUploadService` (Unit + filesystem controlado)
Objetivo: validar MIME por contenido real usando `finfo`, tamaño máximo y manejo de rutas.
1. Crea un archivo temporal válido PNG en una carpeta temporal del sistema (no dependas de subir a `public/` manualmente).
2. Llama a:
   - `$service = new ImageUploadService();`
   - `$uploaded = new UploadedFile($tempPath, 'test.png', 'image/png', filesize($tempPath), UPLOAD_ERR_OK, true);`
3. Verifica:
   - `upload($uploaded, 'unit-tests')` devuelve:
     - `path` empieza por `uploads/unit-tests/`
     - `full_path` existe en disco
     - `mime_type` coincide con `image/png`
4. Prueba errores:
   - archivo con extensión/mime “falso” (por contenido): debe lanzar `RuntimeException`
   - tamaño > 5MB: debe lanzar `RuntimeException` (si no quieres generar 5MB real, puedes usar un archivo grande generado por bytes aleatorios y ajustar el mime controlado; si finfo falla, debes diseñarlo para que mime sea uno permitido para que el error sea por tamaño)
5. Verifica `delete($path)`:
   - devuelve `true` para el archivo existente
   - devuelve `false` para un path inexistente
6. Limpieza:
   - borra el archivo y, si queda vacío, borra la carpeta `public/uploads/unit-tests/` para no ensuciar el repo

### C) `PortraitPricingService` (Integration con BD)
Objetivo: validar fórmulas y excepciones.
1. Crea en BD (usando los Models y `skipValidation(true)`):
   - `PortraitStyleModel` con:
     - uno `slug='color'`, `name='Color'`, `base_price` cualquiera
     - otro `slug='bw'`, `name='B&W'`, `base_price` conocido (ej. 100)
   - `PortraitSizeModel` con:
     - un `price_modifier` conocido (ej. 10)
2. Tests de cálculo (ejemplos con expectativas):
   - Color style + `numFigures=3` => base_price debe ser `197` (según tabla) (redondeo 2 dec)
   - Non-color style + `numFigures=4` => base_price = base + (numFigures-1)*base*0.25
   - Con frame:
     - extras_price = (basePrice + sizeModifier)*0.15
     - total = round(basePrice + sizeModifier + extras_price, 2)
3. Excepción:
   - `calculate()` con styleId inexistente o sizeId inexistente debe lanzar `InvalidArgumentException`

### D) `CloudinaryService` (Unit)
1. `isAvailable()`:
   - con `CLOUDINARY_CLOUD_NAME/API_KEY/API_SECRET` vacíos => `false`
2. `generateUrl()`:
   - con `cloudName='demo'`:
     - si `transformations` vacías => usa `['q_auto','f_auto']`
3. `generateThumbnailUrl()`:
   - devuelve URL con transformaciones:
     - `w_400,h_400,c_fill,q_auto,f_auto`
4. `generatePortfolioUrl()`:
   - devuelve URL con transformaciones:
     - `w_1200,q_auto:best,f_auto`
5. `upload()`:
   - si no está configurado => debe lanzar `RuntimeException`
6. `delete()`:
   - si no está configurado => debe devolver `false`

### E) `StripeService` + `StripeConfig` (Integration segura, sin llamadas reales)
1. Carga `site_settings` con:
   - `stripe_secret_key` = placeholder que `StripeConfig::isPlaceholderSecret()` considere placeholder (ej. `sk_test_xxx_...`)
2. Instancia:
   - `$service = new StripeService();`
3. Verifica:
   - `$service->isConfigured() === false`
   - `$this->expectException(RuntimeException::class);` para:
     - `$service->createPaymentIntent(10.00);`
     - `$service->createCustomer('a@b.com','Name');` (también debe fallar por `requireConfigured()`)
     - `$service->confirmPayment('pi_123');`

## Ejecución y validación
1. Corre:
   - `composer test` antes de empezar (para conocer baseline)
   - `composer test` después de implementar tests
2. Evita flakiness:
   - No asumas orden de paths en `json_encode` si no está garantizado (deduplicación usa `array_unique` que preserva orden de aparición; pero en pruebas puedes evitar comparar string exacta si hay riesgo).

## Entregables
- Añade al menos:
  - 1 archivo de test para `portrait_helper.php` (Unit)
  - 1 archivo de test para `ImageUploadService.php` (Unit)
  - 1 archivo de test para `PortraitPricingService.php` (Integration)
  - 1 archivo de test para `StripeService.php` (Integration segura, sin llamadas)
  - (CloudinaryService unit) mínimo 1 archivo o incluir dentro de otro.

## Criterio de aceptación
- `composer test` pasa.
- No hay llamadas externas (Stripe/Cloudinary) en los tests.
- Las pruebas cubren los métodos/ramas clave descritos.

## Cómo comunicar tu trabajo (salida final del agente)
Incluye:
1. Lista de archivos de test creados/actualizados.
2. Qué cubre cada test (1 línea).
3. Comandos usados: `composer test` y si usaste `--filter` con nombres de test.

