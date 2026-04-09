<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * WEBHOOKCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/WEBHOOKCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// WEBHOOKS HACIA N8N Y ENDPOINT PARA LISTAR CLIENTES INACTIVOS (FIDELIZACIÓN).
namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class WebhookController extends ResourceController
{
    protected $format = 'json';

    // ENVÍA PAYLOAD DE NUEVO ENCARGO DE RETRATO AL WEBHOOK CONFIGURADO.
    public function notifyNewPortraitOrder(array $orderData): bool
    {
        return $this->sendWebhook('new-portrait-order', $orderData);
    }

    // NOTIFICA CAMBIO DE ESTADO DE UN PEDIDO DE RETRATO (EMAILS / AUTOMATIZACIONES).
    public function notifyOrderStatusChange(array $data): bool
    {
        return $this->sendWebhook('order-status-change', $data);
    }

    // NOTIFICA NUEVA RESERVA DE ARTE EN VIVO.
    public function notifyNewBooking(array $bookingData): bool
    {
        return $this->sendWebhook('new-booking', $bookingData);
    }

    // NOTIFICA NUEVO MENSAJE DEL FORMULARIO DE CONTACTO.
    public function notifyNewContact(array $contactData): bool
    {
        return $this->sendWebhook('new-contact', $contactData);
    }

    // LISTA CLIENTES SIN ACTIVIDAD (PEDIDO TIENDA O RETRATO) EN LOS ÚLTIMOS TRES MESES.
    // SI CRON_API_KEY ESTÁ DEFINIDO EN .ENV, EXIGE CABECERA X-Cron-Key (MISMO VALOR).
    public function getLoyaltyClients()
    {
        $cronKey = env('CRON_API_KEY');
        if ($cronKey !== null && $cronKey !== '') {
            $header = $this->request->getHeaderLine('X-Cron-Key');
            if (! hash_equals((string) $cronKey, $header)) {
                return $this->failUnauthorized('Invalid or missing X-Cron-Key');
            }
        }

        $loyaltyClients = (new \App\Libraries\LoyaltyClientsService())->getInactiveClients(3);

        return $this->respond($loyaltyClients);
    }

    // REALIZA POST JSON A N8N CON CABECERA SECRETA; REGISTRA ERRORES SI EL CÓDIGO HTTP NO ES 2XX.
    private function sendWebhook(string $event, array $data): bool
    {
        $webhookUrl = env('N8N_WEBHOOK_URL');
        if (empty($webhookUrl)) {
            log_message('warning', 'N8N_WEBHOOK_URL not configured. Skipping webhook: ' . $event);
            return false;
        }

        $payload = json_encode([
            'event'     => $event,
            'timestamp' => date('c'),
            'data'      => $data,
        ]);

        $ch = curl_init($webhookUrl . '/' . $event);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-Webhook-Secret: ' . env('N8N_WEBHOOK_SECRET', 'nmz-webhook-secret'),
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        log_message('error', "Webhook '{$event}' failed with HTTP {$httpCode}: {$response}");
        return false;
    }
}