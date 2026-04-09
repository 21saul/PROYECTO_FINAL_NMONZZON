# Tareas programadas (cron) en nmonzzon Studio

Este proyecto usa **CodeIgniter 4 Spark** (`php spark …`) para ejecutar desde el **cron del sistema** (o desde DDEV) las automatizaciones que antes podías delegar solo a n8n: **fidelización por email** y **recordatorio interno de reservas Arte en vivo** pendientes.

## Requisitos

1. **Migración** `scheduled_tasks` (tabla de tareas diferidas):

   ```bash
   ddev exec php spark migrate
   ```

   *(Sin DDEV: `php spark migrate` desde la raíz del proyecto, con PHP y `.env` configurados.)*

2. Variables en **`.env`** (las que apliquen):

   | Variable | Uso |
   |----------|-----|
   | `ADMIN_EMAIL` | Destinatario del recordatorio de reserva pendiente. |
   | `CRON_API_KEY` | Si la defines (no vacía), el endpoint `GET api/webhooks/loyalty-clients` exige la cabecera `X-Cron-Key` con el **mismo** valor. Así puedes seguir usando n8n u otro cliente HTTP sin abrir el listado al público. |
   | `BOOKING_FOLLOWUP_DELAY_HOURS` | Horas hasta el recordatorio interno (por defecto **36**). |
   | `LOYALTY_EMAIL_BATCH_PAUSE_MS` | Pausa en milisegundos entre cada correo de fidelización (por defecto **500**) para no saturar el SMTP. |

3. **SMTP / email** configurado en `app/Config/Email.php` y entorno, igual que el resto de correos transaccionales.

---

## Comandos

### `cron:loyalty-send`

- **Qué hace:** Obtiene clientes con rol `client`, activos, sin pedido de tienda ni retrato en los **últimos 3 meses** (misma lógica que `LoyaltyClientsService` y que el JSON de `api/webhooks/loyalty-clients`) y envía el correo `emails/loyalty_reactivation.php` a cada uno.
- **Cuándo programarlo:** Típicamente **una vez al mes** (por ejemplo el día 1 a las 09:00). El comando no “sabe” que es mensual; quien define la periodicidad es **crontab**.
- **Archivos:** `app/Commands/CronLoyaltySend.php`, `app/Libraries/LoyaltyClientsService.php`, `app/Libraries/EmailService::sendLoyaltyReactivation()`, vista `app/Views/emails/loyalty_reactivation.php`.

Ejemplos:

```bash
# Simulación: lista candidatos (hasta 10 en consola) sin enviar
ddev exec php spark cron:loyalty-send --dry-run

# Envío real
ddev exec php spark cron:loyalty-send

# Como máximo 20 correos en esta pasada (útil para pruebas)
ddev exec php spark cron:loyalty-send --limit 20
```

**Crontab (producción, usuario del sitio):** ajusta la ruta a `spark` y al proyecto.

```cron
0 9 1 * * cd /ruta/al/proyecto && /usr/bin/php spark cron:loyalty-send >> /var/log/nmz-loyalty.log 2>&1
```

---

### `cron:process-scheduled`

- **Qué hace:** Lee filas de la tabla **`scheduled_tasks`** con `processed_at` nulo y `run_at <= ahora`, y las ejecuta. Hoy el único tipo implementado es `live_art_booking_followup`: si la reserva **sigue en `pending`**, envía un correo al **admin** (`ADMIN_EMAIL`) con enlace al panel; si ya no está en `pending`, la tarea se marca hecha sin enviar.
- **Cuándo programarlo:** Cada **15–60 minutos** según la precisión que quieras para el recordatorio.
- **Archivos:** `app/Commands/CronProcessScheduled.php`, `app/Libraries/ScheduledTaskService.php`, `app/Models/ScheduledTaskModel.php`, migración `app/Database/Migrations/2026-04-08-120000_CreateScheduledTasksTable.php`, `app/Libraries/EmailService::sendLiveArtBookingFollowupReminderToAdmin()`, vista `app/Views/emails/booking_followup_admin.php`.

Encolado de la tarea al crear la reserva:

- `app/Controllers/Web/ArteEnVivoController.php` (formulario web)
- `app/Controllers/Api/LiveArtBookingController.php` (API)

Tras guardar la reserva se inserta **una** fila única por `booking_id` (`task_type` + `reference_id`).

Ejemplos:

```bash
ddev exec php spark cron:process-scheduled
ddev exec php spark cron:process-scheduled --limit 100
```

**Crontab:**

```cron
*/30 * * * * cd /ruta/al/proyecto && /usr/bin/php spark cron:process-scheduled >> /var/log/nmz-scheduled.log 2>&1
```

Si el envío de correo falla, la fila **no** se marca como procesada para que un siguiente pase del cron pueda reintentar.

---

## Endpoint HTTP de fidelización y `CRON_API_KEY`

`GET /api/webhooks/loyalty-clients` sigue existiendo para integraciones externas (p. ej. n8n).

- Si **`CRON_API_KEY` no está definida o está vacía:** el comportamiento sigue siendo público (como antes).
- Si **`CRON_API_KEY` tiene valor:** la petición debe incluir `X-Cron-Key: <mismo valor>`; si no, respuesta **401**.

Implementación: `app/Controllers/Api/WebhookController::getLoyaltyClients()`.

La lógica de negocio del listado está centralizada en `app/Libraries/LoyaltyClientsService.php` para que el endpoint y `cron:loyalty-send` no diverjan.

---

## Relación con n8n

Puedes **seguir usando n8n** para otros flujos o dejarlo solo como respaldo. La guía `docs/n8n-workflows.md` describe los webhooks; para fidelización y recordatorio de reservas, estos comandos cubren el caso **solo PHP + cron** sin orquestador externo.

---

## Resumen de archivos nuevos o tocados

| Archivo | Motivo |
|---------|--------|
| `app/Database/Migrations/2026-04-08-120000_CreateScheduledTasksTable.php` | Tabla `scheduled_tasks`. |
| `app/Models/ScheduledTaskModel.php` | Modelo de tareas. |
| `app/Libraries/ScheduledTaskService.php` | Encolar y procesar tareas (follow-up reservas). |
| `app/Libraries/LoyaltyClientsService.php` | Listado de clientes inactivos (reutilizable). |
| `app/Commands/CronLoyaltySend.php` | Comando Spark fidelización. |
| `app/Commands/CronProcessScheduled.php` | Comando Spark tareas vencidas. |
| `app/Libraries/EmailService.php` | Métodos `sendLoyaltyReactivation` y `sendLiveArtBookingFollowupReminderToAdmin`. |
| `app/Views/emails/loyalty_reactivation.php` | Plantilla correo cliente. |
| `app/Views/emails/booking_followup_admin.php` | Plantilla correo admin. |
| `app/Controllers/Api/WebhookController.php` | API key opcional + uso de `LoyaltyClientsService`. |
| `app/Controllers/Web/ArteEnVivoController.php` | Encola follow-up tras reserva web. |
| `app/Controllers/Api/LiveArtBookingController.php` | Encola follow-up tras reserva API. |
