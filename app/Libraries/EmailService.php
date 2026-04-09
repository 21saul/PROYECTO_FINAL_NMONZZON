<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE ENVÍO DE CORREOS TRANSACCIONALES CON EL COMPONENTE EMAIL DE CODEIGNITER 4.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * CONFIRMACIONES DE PEDIDO, ACTUALIZACIONES DE RETRATO, RESERVAS Y NOTIFICACIONES AL ADMIN.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// IMPORTA UNA CLASE O TRAIT
use CodeIgniter\Email\Email;

// DECLARA UNA CLASE
class EmailService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // INSTANCIA DEL SERVICIO DE EMAIL CONFIGURADO EN CONFIG/EMAIL
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private Email $email;

    // COMENTARIO DE LÍNEA EXISTENTE
    // INYECTA EL SERVICIO NATIVO DE CI PARA REUTILIZARLO EN CADA MÉTODO DE ENVÍO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email = \Config\Services::email();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ENVÍA CORREO DE CONFIRMACIÓN DE PEDIDO AL CLIENTE CON PLANTILLA HTML
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendOrderConfirmation(array $order, array $client): bool
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // REINICIA CABECERAS Y CUERPO PARA NO ARRASTRAR DATOS DE UN ENVÍO ANTERIOR
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo($client['email']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject('Confirmación de pedido #' . ($order['order_number'] ?? '') . ' - nmonzzon Studio');
        // COMENTARIO DE LÍNEA EXISTENTE
        // RENDERIZA LA VISTA DE EMAIL CON CONTEXTO DEL PEDIDO Y DEL CLIENTE
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/order_confirmation', ['order' => $order, 'client' => $client]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // NOTIFICA CAMBIO DE ESTADO DE UN ENCARGO DE RETRATO CON ASUNTO Y TEXTO SEGÚN EL ESTADO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendPortraitStatusUpdate(array $order, array $client, string $newStatus): bool
    // DELIMITADOR DE BLOQUE
    {
        // COMENTARIO DE LÍNEA EXISTENTE
        // MAPEO DE CLAVES DE ESTADO A FRASES LEGIBLES PARA ASUNTO Y CUERPO
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $statusMessages = [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'accepted'       => 'Tu encargo de retrato ha sido aceptado',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'photo_received' => 'Hemos recibido tu foto de referencia',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'in_progress'    => 'Tu retrato está en proceso',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'revision'       => 'Tu boceto está listo para revisión',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'delivered'      => '¡Tu retrato está terminado!',
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'completed'      => 'Gracias por tu compra',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];

        // COMENTARIO DE LÍNEA EXISTENTE
        // ASUNTO PERSONALIZADO SI EL ESTADO ES CONOCIDO; SI NO, TEXTO GENÉRICO
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subject = ($statusMessages[$newStatus] ?? 'Actualización de pedido') . ' - #' . ($order['order_number'] ?? '');

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo($client['email']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject($subject);
        // INSTRUCCIÓN O DECLARACIÓN PHP
        $this->email->setMessage(view('emails/portrait_status', [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'order'         => $order,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'client'        => $client,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'status'        => $newStatus,
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'statusMessage' => $statusMessages[$newStatus] ?? '',
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // CONFIRMA AL USUARIO QUE SE HA RECIBIDO SU SOLICITUD DE RESERVA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendBookingConfirmation(array $booking): bool
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // COMENTARIO DE LÍNEA EXISTENTE
        // DESTINATARIO: EMAIL DE CONTACTO INCLUIDO EN LA SOLICITUD DE RESERVA
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo($booking['contact_email']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject('Solicitud de reserva recibida - nmonzzon Studio');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/booking_confirmation', ['booking' => $booking]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // AVISA AL CORREO DE ADMINISTRACIÓN DE UN NUEVO MENSAJE DEL FORMULARIO DE CONTACTO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendContactNotification(array $contact): bool
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo(env('ADMIN_EMAIL', 'admin@nmonzzon.com'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject('Nuevo mensaje de contacto: ' . ($contact['subject'] ?? ''));
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/contact_notification', ['contact' => $contact]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // AUTO-RESPUESTA AL REMITENTE DEL FORMULARIO DE CONTACTO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendContactAutoReply(array $contact): bool
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo($contact['email']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject('Hemos recibido tu mensaje — nmonzzon Studio');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/contact_autoreply', ['contact' => $contact]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // CORREO DE REACTIVACIÓN A CLIENTES INACTIVOS (INVOCADO DESDE CRON).
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendLoyaltyReactivation(array $client): bool
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo($client['email']);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject('Te echamos de menos — nmonzzon Studio');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/loyalty_reactivation', ['client' => $client]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // RECORDATORIO AL ADMIN: RESERVA ARTE EN VIVO SIGUE EN PENDING TRAS EL PLAZO CONFIGURADO.
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function sendLiveArtBookingFollowupReminderToAdmin(array $booking): bool
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->clear();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setTo(env('ADMIN_EMAIL', 'admin@nmonzzon.com'));
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->email->setSubject(
            'Seguimiento reserva ' . ($booking['booking_number'] ?? '#' . ($booking['id'] ?? '')) . ' — aún pendiente'
        );
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->email->setMessage(view('emails/booking_followup_admin', ['booking' => $booking]));
        // RETORNA UN VALOR AL LLAMADOR
        return $this->email->send();
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
