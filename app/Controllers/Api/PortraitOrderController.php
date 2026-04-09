<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITORDERCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/PORTRAITORDERCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE ENCARGOS DE RETRATO: CREACIÓN CON PRESUPUESTO, LISTADO, DETALLE, CAMBIO DE ESTADO, SUBIDA DE IMÁGENES E HISTORIAL.
namespace App\Controllers\Api;

use App\Models\PortraitOrderModel;
use App\Models\PortraitOrderStatusHistoryModel;
use App\Models\PortraitSizeModel;
use App\Models\PortraitStyleModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PortraitOrderController extends BaseApiController
{
    protected PortraitOrderModel $orderModel;
    protected PortraitStyleModel $styleModel;
    protected PortraitSizeModel $sizeModel;
    protected PortraitOrderStatusHistoryModel $historyModel;

    // CARGA MODELOS Y HELPER API TRAS INICIALIZAR EL PADRE.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->orderModel   = model(PortraitOrderModel::class);
        $this->styleModel   = model(PortraitStyleModel::class);
        $this->sizeModel    = model(PortraitSizeModel::class);
        $this->historyModel = model(PortraitOrderStatusHistoryModel::class);
    }

    // POST: USUARIO AUTENTICADO CREA UN PEDIDO EN ESTADO «QUOTE» CON CÁLCULO DE PRECIO Y NOTIFICACIÓN WEBHOOK.
    public function create(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        $rules = [
            'portrait_style_id' => 'required|integer',
            'portrait_size_id'  => 'required|integer',
            'num_figures'       => 'required|integer|greater_than[0]',
            'with_frame'        => 'permit_empty',
            'frame_type'        => 'permit_empty|max_length[100]',
            'client_notes'      => 'permit_empty|max_length[2000]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $styleId = (int) $validated['portrait_style_id'];
        $sizeId  = (int) $validated['portrait_size_id'];
        $figures = (int) $validated['num_figures'];

        if ($figures > 10) {
            return apiError('num_figures must be between 1 and 10 for automated pricing', 422);
        }

        $withFrameRaw = $validated['with_frame'] ?? $this->request->getVar('with_frame');
        $withFrame    = filter_var($withFrameRaw, FILTER_VALIDATE_BOOLEAN);

        // CÁLCULO DE PRECIO CENTRALIZADO VIA PortraitPricingService
        $pricingService = new \App\Libraries\PortraitPricingService();

        // INICIO DE BLOQUE TRY
        try {
            $pricing = $pricingService->calculate(
                $styleId,
                $sizeId,
                $figures,
                $withFrame,
                $validated['frame_type'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return apiError($e->getMessage(), 422);
        }

        $orderNumber = 'PO-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));

        $style = $this->styleModel->find($styleId);
        $size  = $this->sizeModel->find($sizeId);

        $row = [
            'order_number'       => $orderNumber,
            'user_id'            => $userId,
            'portrait_style_id'  => $styleId,
            'portrait_size_id'   => $sizeId,
            'num_figures'        => $figures,
            'with_frame'         => $withFrame ? 1 : 0,
            'frame_type'         => $validated['frame_type'] ?? null,
            'status'             => 'quote',
            'base_price'         => $pricing['base_price'],
            'extras_price'       => $pricing['extras_price'],
            'total_price'        => $pricing['total_price'],
            'client_notes'       => $validated['client_notes'] ?? null,
            'payment_status'     => 'pending',
        ];

        $this->orderModel->skipValidation(true);
        $id = $this->orderModel->insert($row, true);
        $this->orderModel->skipValidation(false);

        if (! $id) {
            return apiError('Failed to create order', 500, $this->orderModel->errors());
        }

        // NOTIFICACIÓN A N8N U OTRO FLUJO: ERRORES SOLO EN LOG PARA NO FALLAR LA RESPUESTA API.
        // INICIO DE BLOQUE TRY
        try {
            $user = model('UserModel')->find($userId);
            $webhookCtrl = new \App\Controllers\Api\WebhookController();
            $webhookCtrl->notifyNewPortraitOrder([
                'order_number' => $orderNumber,
                'client_name'  => $user['name'] ?? '',
                'client_email' => $user['email'] ?? '',
                'style'        => $style['name'] ?? '',
                'size'         => $size['name'] ?? '',
                'total'        => $pricing['total_price'],
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Webhook notification failed: ' . $e->getMessage());
        }

        return apiResponse($this->orderModel->find((int) $id), 201, 'Created');
    }

    // GET: LISTA PEDIDOS (ADMIN TODOS CON FILTROS; USUARIO SOLO LOS SUYOS).
    public function index(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        if (! $this->isAdmin()) {
            $this->orderModel->where('user_id', $userId);
        } else {
            $status = $this->request->getGet('status');
            if (is_string($status) && $status !== '') {
                $this->orderModel->where('status', $status);
            }
            $from = $this->request->getGet('date_from');
            $to   = $this->request->getGet('date_to');
            if (is_string($from) && $from !== '') {
                $this->orderModel->where('created_at >=', $from . ' 00:00:00');
            }
            if (is_string($to) && $to !== '') {
                $this->orderModel->where('created_at <=', $to . ' 23:59:59');
            }
        }

        $orders = $this->orderModel->orderBy('created_at', 'DESC')->findAll();

        return apiResponse($orders, 200, 'OK');
    }

    // GET: DETALLE DE UN PEDIDO CON ESTILO Y TAMAÑO (PROPIETARIO O ADMIN).
    public function show($id = null): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $order = $this->orderModel->getWithRelations($id);
        if (! $order) {
            return apiError('Order not found', 404);
        }

        if (! $this->isAdmin() && (int) ($order['user_id'] ?? 0) !== $userId) {
            return apiError('Forbidden', 403);
        }

        $style = $this->styleModel->find((int) ($order['portrait_style_id'] ?? 0));
        $size  = $this->sizeModel->find((int) ($order['portrait_size_id'] ?? 0));

        $order['style'] = $style;
        $order['size']  = $size;

        return apiResponse($order, 200, 'OK');
    }

    // PUT: ADMIN ACTUALIZA ESTADO CON MÁQUINA DE ESTADOS, HISTORIAL Y WEBHOOK DE CAMBIO.
    public function updateStatus($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $adminId = $this->getUserId();
        if (! $adminId) {
            return apiError('Unauthorized', 401);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $order = $this->orderModel->find($id);
        if (! $order) {
            return apiError('Order not found', 404);
        }

        $rules = [
            'status' => 'required|in_list[quote,accepted,photo_received,in_progress,revision,delivered,completed,cancelled]',
            'notes'  => 'permit_empty|max_length[1000]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $from   = (string) ($order['status'] ?? '');
        $to     = (string) $validated['status'];
        $notes  = $validated['notes'] ?? null;

        if ($from === $to) {
            return apiResponse($this->orderModel->find($id), 200, 'Unchanged');
        }

        if (! $this->isValidPortraitStatusTransition($from, $to)) {
            return apiError('Invalid status transition', 422);
        }

        $this->orderModel->skipValidation(true);
        $ok = $this->orderModel->update($id, ['status' => $to]);
        $this->orderModel->skipValidation(false);

        if (! $ok) {
            return apiError('Failed to update status', 500);
        }

        $this->historyModel->insert([
            'portrait_order_id' => $id,
            'from_status'       => $from,
            'to_status'         => $to,
            'changed_by'        => $adminId,
            'notes'             => $notes,
        ]);

        // INICIO DE BLOQUE TRY
        try {
            $client = model('UserModel')->find((int) ($order['user_id'] ?? 0));
            if ($client) {
                $webhookCtrl = new \App\Controllers\Api\WebhookController();
                $webhookCtrl->notifyOrderStatusChange([
                    'order_number' => $order['order_number'] ?? '',
                    'client_email' => $client['email'] ?? '',
                    'client_name'  => $client['name'] ?? '',
                    'from_status'  => $from,
                    'to_status'    => $to,
                    'sketch_url'   => $order['sketch_image'] ?? null,
                    'final_url'    => $order['final_image'] ?? null,
                ]);
            }
        } catch (\Throwable $e) {
            log_message('warning', 'Webhook notification failed: ' . $e->getMessage());
        }

        // INICIO DE BLOQUE TRY
        try {
            $client = $client ?? model('UserModel')->find((int) ($order['user_id'] ?? 0));
            if ($client) {
                $emailService = new \App\Libraries\EmailService();
                $emailService->sendPortraitStatusUpdate($order, $client, $to);
            }
        } catch (\Throwable $e) {
            log_message('error', 'Portrait status email failed: ' . $e->getMessage());
        }

        return apiResponse($this->orderModel->find($id), 200, 'Updated');
    }

    // POST: SUBIDA DE FOTO DE REFERENCIA POR EL PROPIETARIO DEL PEDIDO.
    public function uploadReferencePhoto($id = null): ResponseInterface
    {
        return $this->handlePortraitImageUpload($id, 'reference_photo', false);
    }

    // POST: SUBIDA DE BOCETO (SOLO ADMIN).
    public function uploadSketch($id = null): ResponseInterface
    {
        return $this->handlePortraitImageUpload($id, 'sketch_image', true);
    }

    // POST: SUBIDA DE IMAGEN FINAL (SOLO ADMIN).
    public function uploadFinal($id = null): ResponseInterface
    {
        return $this->handlePortraitImageUpload($id, 'final_image', true);
    }

    // GET: HISTORIAL DE CAMBIOS DE ESTADO DEL PEDIDO (PROPIETARIO O ADMIN).
    public function history($id = null): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $order = $this->orderModel->find($id);
        if (! $order) {
            return apiError('Order not found', 404);
        }

        if (! $this->isAdmin() && (int) ($order['user_id'] ?? 0) !== $userId) {
            return apiError('Forbidden', 403);
        }

        $rows = $this->historyModel->getByOrder($id);

        return apiResponse($rows, 200, 'OK');
    }

    // PROCESA SUBIDA DE ARCHIVO AL CAMPO INDICADO; ADMINONLY RESTRINGE A ADMINISTRADORES.
    protected function handlePortraitImageUpload($id, string $field, bool $adminOnly): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        if ($adminOnly && ! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $id = (int) $id;
        if ($id <= 0) {
            return apiError('Invalid id', 400);
        }

        $order = $this->orderModel->find($id);
        if (! $order) {
            return apiError('Order not found', 404);
        }

        if (! $adminOnly && (int) ($order['user_id'] ?? 0) !== $userId) {
            return apiError('Forbidden', 403);
        }

        $file = $this->request->getFile('file');
        if (! $file || ! $file->isValid()) {
            return apiError('Valid file upload (field: file) is required', 422);
        }

        if ($file->getSize() > 10 * 1024 * 1024) {
            return apiError('File must be 10MB or smaller', 422);
        }

        $mime = $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            return apiError('Only JPEG, PNG, GIF, or WebP images are allowed', 422);
        }

        $uploadDir = FCPATH . 'uploads/portrait_orders/';
        if (! is_dir($uploadDir) && ! mkdir($uploadDir, 0755, true) && ! is_dir($uploadDir)) {
            return apiError('Upload directory is not available', 500);
        }

        $newName = $file->getRandomName();
        if (! $file->hasMoved() && ! $file->move($uploadDir, $newName)) {
            return apiError('Failed to store file', 500);
        }

        $relativePath = 'uploads/portrait_orders/' . $newName;

        $this->orderModel->skipValidation(true);
        $ok = $this->orderModel->update($id, [$field => $relativePath]);
        $this->orderModel->skipValidation(false);

        if (! $ok) {
            return apiError('Failed to update order', 500);
        }

        return apiResponse($this->orderModel->find($id), 200, 'Uploaded');
    }

    // VALIDA SI LA TRANSICIÓN ENTRE ESTADOS DE RETRATO ESTÁ PERMITIDA (CANCELLED Y COMPLETED TIENEN REGLAS ESPECIALES).
    protected function isValidPortraitStatusTransition(string $from, string $to): bool
    {
        if ($to === 'cancelled') {
            return true;
        }

        if ($from === 'cancelled' || $from === 'completed') {
            return false;
        }

        $allowed = [
            'quote'           => ['accepted'],
            'accepted'        => ['photo_received'],
            'photo_received'  => ['in_progress'],
            'in_progress'     => ['revision'],
            'revision'        => ['in_progress', 'delivered'],
            'delivered'       => ['completed'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }
}