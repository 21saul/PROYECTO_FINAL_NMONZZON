<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONTACTOCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/CONTACTOCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;

class ContactoController extends BaseController
{
    /**
     * RENDERIZA LA VISTA DEL FORMULARIO DE CONTACTO.
     */
    public function index()
    {
        return view('web/contacto/index', [
            'title'            => 'Contacto',
            'meta_title'       => 'Contacto',
            'meta_description' => 'Contacta con nmonzzon Studio para consultas sobre retratos, arte en vivo, branding o diseño.',
        ]);
    }

    /**
     * PROCESA EL ENVÍO DEL FORMULARIO: TRAMPA HONEYPOT, LÍMITE POR IP, VALIDACIÓN Y GUARDADO EN BASE DE DATOS.
     */
    public function send()
    {
        $request = $this->request;

        // CAMPO OCULTO "WEBSITE": SI TIENE VALOR, ES PROBABLE BOT (NO PROCESAR COMO ÉXITO REAL)
        if (trim((string) $request->getPost('website')) !== '') {
            return redirect()->back()->with('error', 'No se pudo enviar el mensaje.');
        }

        // CAPTCHA DE PUZZLE: TOKEN + X DE LA PIEZA SE VALIDAN CONTRA SESIÓN (CONSUME EL TOKEN AL VERIFICAR)
        helper('captcha');
        if (! nmz_captcha_verify(
            (string) $request->getPost('captcha_token'),
            (string) $request->getPost('captcha_answer')
        )) {
            return redirect()->back()->withInput()->with('error', 'La verificación anti-spam no es correcta. Inténtalo de nuevo.');
        }

        // RATE LIMIT POR IP: MÁXIMO 3 ENVÍOS EN VENTANA DE UNA HORA (USANDO CACHÉ)
        $ip = (string) $request->getIPAddress();
        $cache = cache();
        $rateKey = 'contact_rate_' . md5($ip);
        $hits    = $cache->get($rateKey);
        if (! is_array($hits)) {
            $hits = [];
        }

        $now = time();
        // CONSERVAR SOLO MARCAS DE TIEMPO DE LA ÚLTIMA HORA
        $hits = array_values(array_filter($hits, static fn (int $t): bool => ($now - $t) < 3600));

        if (count($hits) >= 3) {
            return redirect()->back()->withInput()->with('error', 'Has enviado demasiados mensajes. Prueba más tarde.');
        }

        // VALIDACIÓN DE CAMPOS OBLIGATORIOS Y CATEGORÍA PERMITIDA
        $validation = service('validation');
        $validation->setRules([
            'name'     => 'required|max_length[100]',
            'email'    => 'required|valid_email|max_length[255]',
            'subject'  => 'required|max_length[200]',
            'message'  => 'required',
            'category' => 'required|in_list[general,portrait,live_art,branding,design,products,other]',
        ]);

        if (! $validation->withRequest($request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Revisa los campos del formulario.');
        }

        // REGISTRAR ESTE ENVÍO EN EL CONTADOR DE RATE LIMIT
        $hits[] = $now;
        $cache->save($rateKey, $hits, 3600);

        // PERSISTIR EL MENSAJE DE CONTACTO (SIN esc(): LOS DATOS SE ESCAPAN AL MOSTRAR EN VISTAS, NO AL GUARDAR)
        $messageModel = new ContactMessageModel();
        $messageModel->insert([
            'name'        => $request->getPost('name'),
            'email'       => $request->getPost('email'),
            'phone'       => (string) $request->getPost('phone'),
            'subject'     => $request->getPost('subject'),
            'message'     => $request->getPost('message'),
            'category'    => $request->getPost('category'),
            'is_read'     => 0,
            'is_replied'  => 0,
            'ip_address'  => $ip,
        ]);

        // NOTIFICAR A N8N SI ESTÁ CONFIGURADO
        // INICIO DE BLOQUE TRY
        try {
            $webhookCtrl = new \App\Controllers\Api\WebhookController();
            $webhookCtrl->notifyNewContact([
                'name'     => $request->getPost('name'),
                'email'    => $request->getPost('email'),
                'subject'  => $request->getPost('subject'),
                'category' => $request->getPost('category'),
                'message'  => $request->getPost('message'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Contact webhook failed: ' . $e->getMessage());
        }

        // NOTIFICAR AL ADMIN POR EMAIL + AUTO-RESPUESTA AL REMITENTE
        // INICIO DE BLOQUE TRY
        try {
            $contactData = [
                'name'     => $request->getPost('name'),
                'email'    => $request->getPost('email'),
                'phone'    => $request->getPost('phone'),
                'subject'  => $request->getPost('subject'),
                'message'  => $request->getPost('message'),
                'category' => $request->getPost('category'),
            ];
            $emailService = new \App\Libraries\EmailService();
            $emailService->sendContactNotification($contactData);
            $emailService->sendContactAutoReply($contactData);
        } catch (\Throwable $e) {
            log_message('error', 'Contact email failed: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Gracias, hemos recibido tu mensaje.');
    }
}