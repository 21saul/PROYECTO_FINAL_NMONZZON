# Crons y automatizaciones en nmonzzon Studio

Este documento explica **qué es un cron** en este contexto, **cómo está montado el proyecto** y **qué se añadió** para desarrollo con DDEV y despliegue en producción. La referencia rápida de comandos, variables `.env` y ejemplos de terminal sigue en [`cron-jobs.md`](cron-jobs.md).

---

## ¿Qué es un cron?

En servidores Linux, **cron** es el planificador de tareas del sistema: un demonio que, según una **expresión de tiempo** (minuto, hora, día del mes, mes, día de la semana), ejecuta un **comando** de forma repetida sin que nadie pulse un botón.

En la práctica:

- Tú defines *cuándo* corre algo (por ejemplo “cada 30 minutos” o “el día 1 de cada mes a las 09:00”).
- El sistema ejecuta *qué* le indiques (en este proyecto, casi siempre **`php spark …`** en la carpeta del código).

**Importante:** el propio PHP o CodeIgniter no “sustituyen” al cron. La aplicación expone **comandos CLI** (`cron:loyalty-send`, `cron:process-scheduled`); quien decide la periodicidad es el **cron del servidor** (o el contenedor DDEV), llamando a esos comandos.

---

## Qué problema resuelve este proyecto con los crons

Dos automatizaciones que antes podían depender solo de herramientas externas (por ejemplo n8n) quedan cubiertas también con **PHP + base de datos + correo**:

1. **Fidelización por email** — Enviar un correo de reactivación a clientes activos que llevan **al menos 3 meses** sin pedido en tienda ni encargo de retrato (misma regla que el webhook `GET /api/webhooks/loyalty-clients`), evitando reenvíos repetidos para el mismo periodo de inactividad.
2. **Seguimiento de reservas “Arte en vivo”** — Tras crear una reserva, se programa una **tarea diferida** en tabla `scheduled_tasks`. Cuando llega la hora, si la reserva **sigue en estado `pending`**, se envía un **recordatorio al administrador** (`ADMIN_EMAIL`), con reintentos acotados y trazabilidad de errores.

La lógica de negocio vive en **servicios y modelos**; el cron solo **dispara** el comando en el momento adecuado.

---

## Cómo está integrado en el proyecto (visión general)

```
┌─────────────────────────────────────────────────────────────────┐
│  Cron del sistema (producción) o cron en contenedor DDEV (dev)   │
└───────────────────────────────┬─────────────────────────────────┘
                                │ ejecuta periódicamente
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│  php spark cron:process-scheduled    │  php spark cron:loyalty-send │
└───────────────┬──────────────────────┴──────────────┬───────────┘
                │                                       │
                ▼                                       ▼
┌───────────────────────────┐           ┌─────────────────────────────┐
│ ScheduledTaskService       │           │ LoyaltyClientsService        │
│ + tabla scheduled_tasks    │           │ + EmailService (reactivación)│
│ + EmailService (admin)     │           │                              │
└───────────────────────────┘           └─────────────────────────────┘
```

- **`cron:process-scheduled`** — Implementado en `app/Commands/CronProcessScheduled.php`. Lee filas pendientes de `scheduled_tasks` cuya `run_at` ya pasó y las procesa vía `app/Libraries/ScheduledTaskService.php`.
- **`cron:loyalty-send`** — Implementado en `app/Commands/CronLoyaltySend.php`. Obtiene candidatos con `app/Libraries/LoyaltyClientsService.php` y envía correos con `app/Libraries/EmailService.php` (plantilla `app/Views/emails/loyalty_reactivation.php`).
- **`cron:status`** — Implementado en `app/Commands/CronStatus.php`. Resume pendientes, vencidas y fallidas para operación y diagnóstico rápido.

Las reservas **encolan** la tarea de seguimiento al crearse:

- Web: `app/Controllers/Web/ArteEnVivoController.php`
- API: `app/Controllers/Api/LiveArtBookingController.php`

La migración de la tabla está en `app/Database/Migrations/2026-04-08-120000_CreateScheduledTasksTable.php`.

---

## Qué se configuró en DDEV (desarrollo local)

Para que en local las mismas tareas se ejecuten **sin tener que acordarse de lanzarlas a mano**, se añadió integración en **`.ddev/web-build/`**:

| Fichero | Función |
|---------|---------|
| `Dockerfile.nmzonzzonstudio-cron` | Instala el paquete `cron` en la imagen del contenedor web. |
| `nmzonzzonstudio-supervisor-cron.conf` | Registra el demonio `cron` en **supervisord** (proceso en primer plano `cron -f`), para que arranque con el contenedor. |
| `nmzonzzonstudio-cron-d` | Se copia a **`/etc/cron.d/nmzonzzonstudio`** dentro del contenedor. Define las dos líneas de planificación ejecutadas como usuario **`ddev`**. |

**Horarios por defecto en DDEV** (ajustables editando `nmzonzzonstudio-cron-d` y reiniciando DDEV):

- Cada **30 minutos**: `php spark cron:process-scheduled` (salida típica en `/tmp/nmz-cron-scheduled.log`).
- **Día 1 de cada mes a las 09:00**: `php spark cron:loyalty-send` (salida típica en `/tmp/nmz-cron-loyalty.log`).

Tras añadir o cambiar estos ficheros hace falta **`ddev restart`** para reconstruir la imagen web.

---

## Producción

En un servidor real **no** se usa el `Dockerfile` de DDEV tal cual: allí se configura el **crontab del usuario del sitio** (o systemd timers, según tu operación) con las mismas invocaciones a `php` y `spark` desde el directorio del proyecto.

En la raíz del repositorio hay un ejemplo comentado: **`crontab.production.example`**. Las líneas concretas y rutas también se documentan en [`cron-jobs.md`](cron-jobs.md).

---

## Cómo comprobar que todo funciona

1. **Supervisor y cron en DDEV**  
   `ddev exec sudo supervisorctl status`  
   Debe aparecer **`nmzonzzonstudio-cron`** en estado **RUNNING**.

2. **Definición instalada**  
   `ddev exec cat /etc/cron.d/nmzonzzonstudio`  
   Deben verse las dos tareas y los comandos `spark`.

3. **Comandos a mano (sin esperar al reloj del cron)**  
   - `ddev exec php spark cron:loyalty-send --dry-run`  
   - `ddev exec php spark cron:process-scheduled`

4. **Logs tras las ejecuciones programadas**  
   `ddev exec tail -20 /tmp/nmz-cron-scheduled.log`  
   `ddev exec tail -20 /tmp/nmz-cron-loyalty.log`  
   *(Los ficheros pueden no existir hasta la primera ejecución del cron.)*

---

## Relación con n8n y el webhook HTTP

- El endpoint **`GET /api/webhooks/loyalty-clients`** sigue disponible para flujos externos; la lista de clientes usa la misma lógica que `cron:loyalty-send` (`LoyaltyClientsService`). Si defines **`CRON_API_KEY`** en `.env`, el webhook exige la cabecera **`X-Cron-Key`**. Detalle en [`cron-jobs.md`](cron-jobs.md).
- Puedes usar **n8n**, **solo cron PHP** o **ambos** según necesites.

---

## Resumen

| Concepto | En este proyecto |
|----------|------------------|
| **Cron** | Planificador del SO (o del contenedor DDEV) que ejecuta `php spark …` en horarios definidos. |
| **Entrada** | Comandos Spark `cron:process-scheduled` y `cron:loyalty-send`. |
| **Integración código** | `ScheduledTaskService`, `LoyaltyClientsService`, `EmailService`, controladores de reserva, migración `scheduled_tasks`. |
| **Integración DDEV** | `web-build`: imagen con `cron` + supervisord + `/etc/cron.d/nmzonzzonstudio`. |
| **Documentación de operación** | [`cron-jobs.md`](cron-jobs.md), `crontab.production.example` en la raíz. |

---

## Código comentado

Los comandos y servicios implicados en estas automatizaciones incluyen **comentarios en español** línea a línea (o casi) en los ficheros bajo `app/Commands/` y `app/Libraries/` citados arriba, para facilitar el mantenimiento y la revisión del flujo.
