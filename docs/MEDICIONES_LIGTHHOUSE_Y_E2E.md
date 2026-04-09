# Mediciones Lighthouse y simulaciones E2E (encargo de retrato)

Este documento cubre el **indicador 1** (Core Web Vitals / Lighthouse en portfolio) y el **indicador 2** (al menos 10 simulaciones del flujo de negocio retrato) descritos en la memoria del proyecto.

## 1. Contrato OpenAPI

- Especificación: `public/openapi.yaml` (OpenAPI 3.0.3).
- Exploración interactiva: abre en el navegador `https://<tu-dominio>/docs-api.html` (Swagger UI carga el YAML del mismo origen).

## 2. Cómo repetir la medición Lighthouse (portfolio)

Prerrequisitos: sitio accesible por HTTPS o `http://localhost` (Chrome), con datos reales o de desarrollo.

```bash
# Instalación one-shot (Node 18+)
npx --yes lighthouse@11 "<URL_DE_TU_ENTORNO>/portfolio" \
  --only-categories=performance,accessibility,best-practices,seo \
  --output=json,html \
  --output-path=./docs/lighthouse/portfolio-run \
  --chrome-flags="--headless --no-sandbox --disable-gpu"
```

Los archivos generados quedan como `docs/lighthouse/portfolio-run.report.json` y `.html`. Anota en la tabla siguiente las métricas del JSON (`audits.largest-contentful-paint.numericValue`, `audits.cumulative-layout-shift.numericValue`, categorías `categories.performance.score`, etc.).

### 2.1 Resultado de la última ejecución en este repositorio

| Métrica | Objetivo memoria | Valor medido | Fecha / URL | Notas |
|--------|-------------------|--------------|-------------|--------|
| LCP | &lt; 2,5 s | *(rellenar tras `npx lighthouse`)* | | Página: `/portfolio` |
| CLS | &lt; 0,1 | *(rellenar)* | | |
| Lighthouse Performance | ≥ 80 | *(rellenar)* | | |
| Lighthouse Accessibility | ≥ 90 | *(rellenar)* | | |

Si la ejecución no es posible en CI (sin Chrome), deja constancia del comando y del entorno donde sí se ejecutó (por ejemplo máquina local con `ddev launch`).

## 3. Diez simulaciones E2E — flujo encargo de retrato

Cada fila es una **simulación documentada**: entorno, datos usados y resultado esperado. Completar la columna «Resultado» tras ejecutar (manual o automatizado).

**Flujo de referencia:** registro/login → configurador → pedido en presupuesto (quote) → (pago si aplica) → email/webhook n8n si está configurado → subida foto referencia → admin cambia estado → cliente ve actualización en «Mi cuenta» → revisión → entregado → factura/PDF si aplica.

| # | Fecha | Entorno | Variante probada | Pasos clave ejecutados | Resultado (OK / fallo + nota) |
|---|-------|---------|-------------------|-------------------------|-------------------------------|
| 1 | | local/staging | Estilo A, 1 figura, sin marco | Configurador → crear encargo API o web → comprobar estado `quote` | |
| 2 | | | Estilo distinto, 2 figuras | Mismo flujo, validar precio calculado | |
| 3 | | | Con marco / `frame_type` | Validar suplemento y persistencia | |
| 4 | | | Subida foto referencia | POST referencia o formulario web | |
| 5 | | | Transición `quote` → `accepted` | Admin o API admin | |
| 6 | | | `accepted` → `photo_received` | | |
| 7 | | | `photo_received` → `in_progress` | Webhook n8n si activo | |
| 8 | | | `in_progress` → `revision` + boceto | Subida boceto admin | |
| 9 | | | `revision` → `delivered` + obra final | Cliente ve archivos en detalle retrato | |
| 10 | | | Cancelación o `completed` | Caso límite según reglas de negocio | |

### Criterio de éxito por simulación

- El pedido queda con **número único** y estados coherentes en BD.
- El **cliente** ve el estado actualizado en `/mi-cuenta/retratos` (o equivalente API `GET /api/portrait-orders`).
- Si **Stripe** está en test: pago reflejado en `payment_status` según webhook.
- Si **n8n** está configurado: los webhooks no devuelven error (revisar logs de CI4 y n8n).

## 4. Referencias rápidas

- Rutas HTTP: `app/Config/Routes.php`.
- Tests automatizados relacionados: `tests/Feature/PortraitOrderFlowTest.php`, `tests/Integration/PortraitOrderIntegrationTest.php`.
