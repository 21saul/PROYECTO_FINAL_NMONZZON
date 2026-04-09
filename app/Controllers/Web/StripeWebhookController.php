<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * STRIPEWEBHOOKCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/STRIPEWEBHOOKCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class StripeWebhookController extends BaseController
{
    /**
     * PUNTO DE ENTRADA DEL WEBHOOK: LEE EL CUERPO RAW, VALIDA FIRMA Y ENRUTA POR TIPO DE EVENTO.
     */
    public function handleWebhook()
    {
        $endpointSecret = (string) env('STRIPE_WEBHOOK_SECRET', '');

        if ($endpointSecret === '' || str_starts_with($endpointSecret, 'whsec_xxx')) {
            log_message('warning', 'Stripe webhook: STRIPE_WEBHOOK_SECRET no configurado.');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Webhook not configured']);
        }

        $payload = file_get_contents('php://input');
        $sigHeader = $this->request->getHeaderLine('Stripe-Signature');

        // INICIO DE BLOQUE TRY
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            log_message('error', 'Stripe webhook error: ' . $e->getMessage());
            return $this->response->setStatusCode(400);
        }

        // DESPACHAR SEGÚN EL TIPO DE EVENTO DE STRIPE
        // SELECCIÓN MÚLTIPLE SWITCH
        switch ($event->type) {
            // CASO EN SWITCH
            case 'payment_intent.succeeded':
                $this->handlePaymentSuccess($event->data->object);
                // INTERRUMPE BUCLE O SWITCH
                break;
            // CASO EN SWITCH
            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event->data->object);
                // INTERRUMPE BUCLE O SWITCH
                break;
        }

        return $this->response->setStatusCode(200)->setJSON(['status' => 'ok']);
    }

    /**
     * CUANDO EL PAGO SE COMPLETA: ACTUALIZA PEDIDO DE PRODUCTO (E INVOICE PDF) O PEDIDO DE RETRATO SEGÚN order_type EN METADATOS.
     */
    private function handlePaymentSuccess($paymentIntent): void
    {
        $metadata = $paymentIntent->metadata;

        if (isset($metadata->order_type)) {
            // SELECCIÓN MÚLTIPLE SWITCH
            switch ($metadata->order_type) {
                // CASO EN SWITCH
                case 'product':
                    $orderModel = model('OrderModel');
                    $order = $orderModel->where('stripe_payment_id', $paymentIntent->id)->first();
                    if ($order) {
                        $orderModel->update($order['id'], [
                            'payment_status' => 'paid',
                            'status'         => 'processing',
                            'paid_at'        => date('Y-m-d H:i:s'),
                        ]);

                        // INICIO DE BLOQUE TRY
                        try {
                            $pdfService = new \App\Libraries\PdfService();
                            $invoicePath = $pdfService->generateInvoice($order);
                            $orderModel->update($order['id'], ['invoice_path' => $invoicePath]);
                        } catch (\Exception $e) {
                            log_message('error', 'Invoice generation failed: ' . $e->getMessage());
                        }

                        // INICIO DE BLOQUE TRY
                        try {
                            $client = model('UserModel')->find((int) $order['user_id']);
                            if ($client) {
                                $updatedOrder = $orderModel->find((int) $order['id']);
                                $emailService = new \App\Libraries\EmailService();
                                $emailService->sendOrderConfirmation($updatedOrder ?? $order, $client);
                            }
                        } catch (\Exception $e) {
                            log_message('error', 'Order confirmation email failed: ' . $e->getMessage());
                        }
                    }
                    // INTERRUMPE BUCLE O SWITCH
                    break;

                // CASO EN SWITCH
                case 'portrait':
                    $portraitModel = model('PortraitOrderModel');
                    $portrait = $portraitModel->where('stripe_payment_id', $paymentIntent->id)->first();
                    if ($portrait) {
                        $portraitModel->update($portrait['id'], [
                            'payment_status' => 'paid',
                            'status'         => 'accepted',
                            'paid_at'        => date('Y-m-d H:i:s'),
                        ]);

                        // INICIO DE BLOQUE TRY
                        try {
                            $client = model('UserModel')->find((int) $portrait['user_id']);
                            if ($client) {
                                $emailService = new \App\Libraries\EmailService();
                                $emailService->sendPortraitStatusUpdate($portrait, $client, 'accepted');
                            }
                        } catch (\Exception $e) {
                            log_message('error', 'Portrait payment email failed: ' . $e->getMessage());
                        }
                    }
                    // INTERRUMPE BUCLE O SWITCH
                    break;
            }
        }
    }

    /**
     * CUANDO EL PAGO FALLA: REGISTRA LOG Y MARCA payment_status COMO FAILED EN PEDIDOS DE PRODUCTO SI APLICA.
     */
    private function handlePaymentFailed($paymentIntent): void
    {
        log_message('error', 'Payment failed for intent: ' . $paymentIntent->id);

        $metadata = $paymentIntent->metadata;
        if (isset($metadata->order_type) && $metadata->order_type === 'product') {
            $orderModel = model('OrderModel');
            $order = $orderModel->where('stripe_payment_id', $paymentIntent->id)->first();
            if ($order) {
                $orderModel->update($order['id'], ['payment_status' => 'failed']);
            }
        }
    }
}