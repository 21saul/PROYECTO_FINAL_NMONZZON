<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONTACTCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/CONTACTCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE MENSAJES DE CONTACTO: ENVÍO CON RATE LIMIT, LISTADO ADMIN, MARCAR LEÍDO Y ELIMINAR.
namespace App\Controllers\Api;

use App\Models\ContactMessageModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ContactController extends BaseApiController
{
    protected ContactMessageModel $messageModel;

    // CARGA EL MODELO DE MENSAJES Y EL HELPER API.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->messageModel = model(ContactMessageModel::class);
    }

    // REGISTRA UN MENSAJE DE CONTACTO CON LÍMITE POR IP Y NOTIFICA MEDIANTE WEBHOOK.
    public function create(): ResponseInterface
    {
        $ip = (string) $this->request->getIPAddress();
        $cache = cache();
        $rateKey = 'contact_form_' . md5($ip);
        $count = (int) ($cache->get($rateKey) ?? 0);
        if ($count >= 3) {
            return apiError('Too many requests. Try again later.', 429);
        }

        $rules = [
            'name'     => 'required|max_length[100]',
            'email'    => 'required|valid_email',
            'subject'  => 'required|max_length[200]',
            'message'  => 'required',
            'category' => 'required|in_list[general,portrait,live_art,branding,design,products,other]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $cache->save($rateKey, $count + 1, 3600);

        // GUARDAR SIN esc(): LOS DATOS SE ESCAPAN AL MOSTRAR, NO AL ALMACENAR
        $this->messageModel->skipValidation(true)->insert([
            'name'        => (string) $validated['name'],
            'email'       => (string) $validated['email'],
            'subject'     => (string) $validated['subject'],
            'message'     => (string) $validated['message'],
            'category'    => (string) $validated['category'],
            'ip_address'  => $ip,
            'is_read'     => 0,
        ]);

        // INICIO DE BLOQUE TRY
        try {
            $webhookCtrl = new \App\Controllers\Api\WebhookController();
            $webhookCtrl->notifyNewContact([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'subject'  => $validated['subject'],
                'category' => $validated['category'],
                'message'  => $validated['message'],
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Webhook notification failed: ' . $e->getMessage());
        }

        return apiResponse(null, 201, 'Message sent');
    }

    // LISTA MENSAJES CON FILTROS is_read Y category (SOLO ADMIN).
    public function index(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $builder = $this->messageModel->builder();

        $isRead = $this->request->getGet('is_read');
        if ($isRead !== null && $isRead !== '') {
            $builder->where('is_read', (int) $isRead);
        }

        $category = $this->request->getGet('category');
        if ($category !== null && $category !== '') {
            $builder->where('category', $category);
        }

        $rows = $builder->orderBy('created_at', 'DESC')->get()->getResultArray();

        return apiResponse($rows);
    }

    // MARCA UN MENSAJE COMO LEÍDO POR SU ID.
    public function markRead($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        if (! $this->messageModel->markAsRead((int) $id)) {
            return apiError('Not found', 404);
        }

        return apiResponse(null, 200, 'Marked as read');
    }

    // ELIMINA UN MENSAJE DE CONTACTO POR ID.
    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        if ($this->messageModel->delete((int) $id) === false) {
            return apiError('Not found', 404);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}