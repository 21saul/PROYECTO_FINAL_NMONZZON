# n8n workflows for nmonzzon Studio (CodeIgniter 4)

This guide explains how to wire **n8n** to the nmzonzzonstudio app: webhook URLs, example JSON payloads, and step-by-step workflow builds.

## Prerequisites

1. **DDEV + n8n**  
   Add `.ddev/docker-compose.n8n.yaml`, then:

   ```bash
   ddev restart
   ```

   n8n listens on **http://localhost:5678** (basic auth as defined in compose).

2. **App environment** (e.g. `.env` in the project root):

   | Variable | Purpose |
   |----------|---------|
   | `N8N_WEBHOOK_URL` | Base URL n8n uses for **incoming** webhooks from PHP (no trailing slash). Example: `http://host.docker.internal:5678/webhook` if the web container must reach n8n on the host. Adjust if you use a named Docker network or Traefik. |
   | `N8N_WEBHOOK_SECRET` | Sent as header `X-Webhook-Secret` on outbound POSTs from `WebhookController::sendWebhook()`. Use the same value in n8n (HTTP Header Auth or Function node) to reject forged requests. |
   | `ADMIN_EMAIL` | Used by `EmailService::sendContactNotification()` for admin copies. |

3. **Site base URL (DDEV)**  
   Default HTTPS URL: **https://nmzonzzonstudio.ddev.site**  
   Use this in email links and when n8n calls back into the app (e.g. loyalty HTTP Request).

---

## How PHP calls n8n

`App\Controllers\Api\WebhookController` POSTs JSON to:

```text
{N8N_WEBHOOK_URL}/{event}
```

Where `{event}` is one of:

- `new-portrait-order`
- `order-status-change`
- `new-booking`
- `new-contact`

**Request body shape:**

```json
{
  "event": "new-portrait-order",
  "timestamp": "2026-03-30T12:00:00+00:00",
  "data": { }
}
```

**Headers:**

- `Content-Type: application/json`
- `X-Webhook-Secret: <same as N8N_WEBHOOK_SECRET>`

In each n8n workflow, add a **Webhook** node (POST), set the **Path** to the event name (e.g. `new-portrait-order`), and enable **Header Auth** or a **IF** node to compare `$json.headers["x-webhook-secret"]` to your secret.

---

## 1. New Portrait Order

**Goal:** When a new portrait order is created, notify the client and the studio.

### Webhook URL (example)

If `N8N_WEBHOOK_URL=http://host.docker.internal:5678/webhook`, PHP posts to:

```text
http://host.docker.internal:5678/webhook/new-portrait-order
```

(Exact URL is shown in the Webhook node after you set **Path** to `new-portrait-order`.)

### Example payload

```json
{
  "event": "new-portrait-order",
  "timestamp": "2026-03-30T10:15:00+01:00",
  "data": {
    "order_number": "PRT-2026-0042",
    "user_id": 12,
    "client": {
      "name": "María López",
      "email": "maria@example.com"
    },
    "total_price": 189.0,
    "status": "pending",
    "portrait_style_id": 2,
    "portrait_size_id": 1
  }
}
```

### Steps in n8n

1. **Webhook** — Method POST, path `new-portrait-order`, respond `Immediately` or `When Last Node Finishes` as you prefer.
2. **Set** — Map `{{ $json.body.data }}` (or `{{ $json.data }}` depending on n8n version) into clean fields: `clientEmail`, `clientName`, `orderNumber`, `adminEmail` (from static config or env).
3. **Send Email (client)** — To `{{ $json.clientEmail }}`, subject/body using order fields; optional HTML.
4. **Send Email (admin)** — To your studio address; include full JSON or formatted summary.

*(You can replace “Send Email” with Gmail, SMTP, or Resend nodes.)*

---

## 2. Order Status Change

**Goal:** On portrait (or shop) status updates, branch by `to_status` and send the right message.

### Webhook URL (example)

```text
{N8N_WEBHOOK_URL}/order-status-change
```

### Example payload

```json
{
  "event": "order-status-change",
  "timestamp": "2026-03-30T11:00:00+01:00",
  "data": {
    "order_type": "portrait",
    "order_number": "PRT-2026-0042",
    "from_status": "in_progress",
    "to_status": "revision",
    "client": {
      "name": "María López",
      "email": "maria@example.com"
    },
    "order": {
      "order_number": "PRT-2026-0042",
      "sketch_image": "uploads/portraits/sketch_42.jpg",
      "final_image": null
    }
  }
}
```

Statuses aligned with `EmailService::sendPortraitStatusUpdate()` / templates: `accepted`, `photo_received`, `in_progress`, `revision`, `delivered`, `completed`.

### Steps in n8n

1. **Webhook** — POST, path `order-status-change`.
2. **Switch** — Expression on `{{ $json.body.data.to_status }}` (or `{{ $json.data.to_status }}`); one output per status you care about.
3. For each branch: **Send Email** with a template text/HTML matching that status (e.g. “Tu boceto está listo para revisión”).
4. Optional **Merge** path for a generic “status updated” fallback.

---

## 3. Live Art Booking

**Goal:** Confirm booking to the client, wait, then send a reminder if there is no manual follow-up.

### Webhook URL (example)

```text
{N8N_WEBHOOK_URL}/new-booking
```

### Example payload

```json
{
  "event": "new-booking",
  "timestamp": "2026-03-30T09:30:00+01:00",
  "data": {
    "contact_name": "Carlos Ruiz",
    "contact_email": "carlos@example.com",
    "contact_phone": "+34 600 000 000",
    "event_type": "wedding",
    "event_date": "2026-06-15",
    "event_start_time": "18:00",
    "event_end_time": "02:00",
    "event_location": "Finca Los Robles",
    "event_city": "Vigo",
    "event_postal_code": "36200",
    "num_guests": 120,
    "special_requirements": "Espacio cubierto para lluvia"
  }
}
```

### Steps in n8n

1. **Webhook** — POST, path `new-booking`.
2. **Send Email** — To `{{ $json.body.data.contact_email }}` (or mapped field); subject “Solicitud de reserva recibida”; body summarizing event type, date, location, guests; mention response within **24–48 hours**.
3. **Wait** — `Wait` node, e.g. **36 hours** (or 2 days), fixed duration.
4. **Send Email (reminder)** — Internal reminder to `ADMIN_EMAIL` or a second client-friendly “still processing” message (your choice).

**PHP alternative:** Creating a booking enqueues a `scheduled_tasks` row; `php spark cron:process-scheduled` sends the admin reminder if status is still `pending` after `BOOKING_FOLLOWUP_DELAY_HOURS` (default 36). See `docs/cron-jobs.md`.

---

## 4. Monthly Loyalty (inactive clients)

**Goal:** On the **1st of each month**, fetch clients inactive for **3+ months** and send batched, personalized emails.

### HTTP endpoint (CodeIgniter)

The app exposes:

```text
GET https://nmzonzzonstudio.ddev.site/api/webhooks/loyalty-clients
```

*(Replace host with your production domain when not using DDEV.)*

Returns JSON array of objects:

```json
[
  {
    "name": "Cliente Ejemplo",
    "email": "cliente@example.com",
    "last_activity": "2025-11-01 14:22:00",
    "days_inactive": 149
  }
]
```

**Security:** Set `CRON_API_KEY` in `.env` (non-empty). Then the endpoint requires header `X-Cron-Key` with the same value; otherwise it returns 401. If `CRON_API_KEY` is unset or empty, the route stays public (legacy).

**Alternative without n8n:** Run `php spark cron:loyalty-send` on a monthly system cron; see `docs/cron-jobs.md`.

### Steps in n8n

1. **Schedule Trigger** — Cron: `0 9 1 * *` (09:00 on day 1 of every month; adjust timezone in workflow settings).
2. **HTTP Request** — GET the loyalty URL above; `Response Format: JSON`.
3. **Split In Batches** — Batch size `5` or `10` to avoid SMTP rate limits.
4. **Send Email** — Inside the batch loop: to `{{ $json.email }}`, personalized body with `{{ $json.name }}`, `{{ $json.days_inactive }}`, and a CTA back to **https://nmzonzzonstudio.ddev.site** (or production URL).

---

## Calling `WebhookController` from your app

The notify methods are intended for use from services/controllers after you persist an order/booking/contact, for example:

```php
$wh = new \App\Controllers\Api\WebhookController();
$wh->initController($this->request, $this->response, \Config\Services::logger());
$wh->notifyNewPortraitOrder($payload);
```

Prefer **injecting a small dedicated library** long term so you do not instantiate controllers manually; the above matches typical CI4 patterns for quick integration.

---

## Checklist

- [ ] `ddev restart` after adding n8n compose override  
- [ ] `N8N_WEBHOOK_URL` reachable from the **web** container  
- [ ] Webhook paths in n8n match: `new-portrait-order`, `order-status-change`, `new-booking`, `new-contact`  
- [ ] `X-Webhook-Secret` validated in n8n  
- [ ] `CRON_API_KEY` set in production if using `GET api/webhooks/loyalty-clients`  
- [ ] SMTP/email in n8n (or PHP `Email`) tested end-to-end  
