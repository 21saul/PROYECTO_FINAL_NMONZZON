<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * PORTRAITORDERADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/PORTRAITORDERADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PortraitOrderModel;
use App\Models\PortraitOrderStatusHistoryModel;
use App\Models\PortraitSizeModel;
use App\Models\PortraitStyleModel;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class PortraitOrderAdminController extends BaseController
{
    protected PortraitOrderModel $orders;
    protected PortraitOrderStatusHistoryModel $history;
    protected PortraitStyleModel $styles;
    protected PortraitSizeModel $sizes;

    /**
     * INICIALIZA MODELOS Y HELPERS PARA FORMULARIOS Y URLs.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->orders   = model(PortraitOrderModel::class);
        $this->history  = model(PortraitOrderStatusHistoryModel::class);
        $this->styles   = model(PortraitStyleModel::class);
        $this->sizes    = model(PortraitSizeModel::class);
    }

    /**
     * COMPRUEBA SESIÓN DE ADMINISTRADOR ANTES DE CUALQUIER ACCIÓN.
     */
    protected function requireAdminSession(): ?ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/admin/login');
        }

        return null;
    }

    /**
     * VALIDA Y ALMACENA UNA IMAGEN EN DISCO; DEVUELVE RUTA RELATIVA O FALSE.
     */
    private function storeValidatedImage(UploadedFile $file, string $diskDir, string $urlPrefix): string|false
    {
        if ($file->getSize() > 5 * 1024 * 1024) {
            session()->setFlashdata('error', 'Image must be 5MB or smaller.');

            return false;
        }

        $mime = (string) $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            session()->setFlashdata('error', 'Only JPEG, PNG, GIF, or WebP images are allowed.');

            return false;
        }

        $extByMime = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $extByMime[$mime] ?? preg_replace('/[^a-z0-9]/', '', strtolower((string) $file->getClientExtension())) ?: 'jpg';

        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        if (! is_dir($diskDir) && ! mkdir($diskDir, 0755, true) && ! is_dir($diskDir)) {
            session()->setFlashdata('error', 'Could not create upload directory.');

            return false;
        }

        if (! $file->hasMoved() && ! $file->move($diskDir, $name)) {
            session()->setFlashdata('error', 'Failed to save image.');

            return false;
        }

        return $urlPrefix . $name;
    }

    /**
     * INDICA SI LA TRANSICIÓN ENTRE DOS ESTADOS DE PEDIDO DE RETRATO ESTÁ PERMITIDA POR EL FLUJO DE NEGOCIO.
     */
    protected function isValidPortraitStatusTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return false;
        }

        if ($to === 'cancelled') {
            return ! in_array($from, ['completed', 'cancelled'], true);
        }

        if (in_array($from, ['completed', 'cancelled'], true)) {
            return false;
        }

        $allowed = [
            'quote'          => ['accepted'],
            'accepted'       => ['photo_received'],
            'photo_received' => ['in_progress'],
            'in_progress'    => ['revision'],
            'revision'       => ['in_progress', 'delivered'],
            'delivered'      => ['completed'],
        ];

        return in_array($to, $allowed[$from] ?? [], true);
    }

    /**
     * LISTA PEDIDOS CON FILTROS POR ESTADO, RANGO DE FECHAS E ID DE ESTILO.
     */
    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $status   = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo   = $this->request->getGet('date_to');

        if ($status !== null && $status !== '') {
            $this->orders->where('status', (string) $status);
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $this->orders->where('created_at >=', (string) $dateFrom . ' 00:00:00');
        }
        if ($dateTo !== null && $dateTo !== '') {
            $this->orders->where('created_at <=', (string) $dateTo . ' 23:59:59');
        }

        $styleFilter = $this->request->getGet('portrait_style_id');
        if ($styleFilter !== null && $styleFilter !== '') {
            $this->orders->where('portrait_style_id', (int) $styleFilter);
        }

        $orders = $this->orders->orderBy('created_at', 'DESC')->findAll();

        return view('admin/portrait-orders/index', [
            'title'     => 'Pedidos de Retratos',
            'orders'    => $orders,
            'styles'    => $this->styles->findAll(),
            'filters'   => [
                'status'             => $status,
                'portrait_style_id'  => $styleFilter,
                'date_from'          => $dateFrom,
                'date_to'            => $dateTo,
            ],
        ]);
    }

    /**
     * DETALLE DE UN PEDIDO CON RELACIONES, HISTORIAL Y ESTADOS SIGUIENTES PERMITIDOS EN LA VISTA.
     */
    public function show(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $order = $this->orders->getWithRelations($id);
        if ($order === null) {
            session()->setFlashdata('error', 'Order not found.');

            return redirect()->to('/admin/portrait-orders');
        }

        // MAPA DE ESTADOS SIGUIENTES PARA LA UI (PUEDE DIFERIR LIGERAMENTE DEL VALIDADOR DE TRANSICIONES)
        $stateTransitions = [
            'quote'          => ['accepted', 'cancelled'],
            'accepted'       => ['photo_received', 'cancelled'],
            'photo_received' => ['in_progress'],
            'in_progress'    => ['revision'],
            'revision'       => ['delivered', 'in_progress'],
            'delivered'      => ['completed'],
            'completed'      => [],
            'cancelled'      => [],
        ];
        $currentStatus = $order['status'] ?? 'quote';
        $nextStates = $stateTransitions[$currentStatus] ?? [];

        return view('admin/portrait-orders/show', [
            'title'      => 'Pedido #' . ($order['order_number'] ?? $id),
            'order'      => $order,
            'history'    => $this->history->getByOrder($id),
            'nextStates' => $nextStates,
            'styles'     => $this->styles->findAll(),
            'sizes'      => $this->sizes->findAll(),
        ]);
    }

    /**
     * CAMBIA EL ESTADO DEL PEDIDO SI ES VÁLIDO Y REGISTRA UNA ENTRADA EN EL HISTORIAL CON NOTAS OPCIONALES.
     */
    public function updateStatus(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $order = $this->orders->find($id);
        if ($order === null) {
            session()->setFlashdata('error', 'Order not found.');

            return redirect()->to('/admin/portrait-orders');
        }

        $newStatus = (string) $this->request->getPost('status');
        $allowedStatuses = ['quote', 'accepted', 'photo_received', 'in_progress', 'revision', 'delivered', 'completed', 'cancelled'];
        if (! in_array($newStatus, $allowedStatuses, true)) {
            session()->setFlashdata('error', 'Invalid status.');

            return redirect()->back();
        }

        $from = (string) ($order['status'] ?? '');
        if (! $this->isValidPortraitStatusTransition($from, $newStatus)) {
            session()->setFlashdata('error', 'That status change is not allowed.');

            return redirect()->back();
        }

        $adminId = (int) (session()->get('user_id') ?? 0);
        if ($adminId <= 0) {
            session()->setFlashdata('error', 'Session expired. Please log in again.');

            return redirect()->to('/admin/login');
        }

        $notes = $this->request->getPost('notes');
        $notes = $notes !== null && $notes !== '' ? (string) $notes : null;

        $this->orders->skipValidation(true);
        $ok = $this->orders->update($id, ['status' => $newStatus]);
        $this->orders->skipValidation(false);

        if (! $ok) {
            session()->setFlashdata('error', 'Could not update status.');

            return redirect()->back();
        }

        $this->history->insert([
            'portrait_order_id' => $id,
            'from_status'       => $from !== '' ? $from : null,
            'to_status'         => $newStatus,
            'changed_by'        => $adminId,
            'notes'             => $notes,
        ]);

        // INICIO DE BLOQUE TRY
        try {
            $updatedOrder = $this->orders->getWithRelations($id);
            $client = model('UserModel')->find((int) ($order['user_id'] ?? 0));
            if ($client && $updatedOrder) {
                $emailService = new \App\Libraries\EmailService();
                $emailService->sendPortraitStatusUpdate($updatedOrder, $client, $newStatus);
            }
        } catch (\Exception $e) {
            log_message('error', 'Portrait status email failed: ' . $e->getMessage());
        }

        session()->setFlashdata('success', 'Status updated.');

        return redirect()->back();
    }

    /**
     * SUBE Y ASOCIA LA IMAGEN DE BOCETO AL PEDIDO INDICADO.
     */
    public function uploadSketch(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->orders->find($id) === null) {
            session()->setFlashdata('error', 'Order not found.');

            return redirect()->to('/admin/portrait-orders');
        }

        $file = $this->request->getFile('sketch_image');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            session()->setFlashdata('error', 'A valid sketch image is required.');

            return redirect()->back();
        }

        if (! $this->validate(['sketch_image' => 'max_size[sketch_image,5120]'])) {
            session()->setFlashdata('error', 'Please fix the upload.');

            return redirect()->back();
        }

        $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/portrait_orders/', 'uploads/portrait_orders/');
        if ($relative === false) {
            return redirect()->back();
        }

        $this->orders->skipValidation(true);
        $ok = $this->orders->update($id, ['sketch_image' => $relative]);
        $this->orders->skipValidation(false);

        if (! $ok) {
            session()->setFlashdata('error', 'Could not save sketch.');

            return redirect()->back();
        }

        session()->setFlashdata('success', 'Sketch uploaded.');

        return redirect()->back();
    }

    /**
     * SUBE Y ASOCIA LA IMAGEN FINAL ENTREGADA AL CLIENTE.
     */
    public function uploadFinal(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->orders->find($id) === null) {
            session()->setFlashdata('error', 'Order not found.');

            return redirect()->to('/admin/portrait-orders');
        }

        $file = $this->request->getFile('final_image');
        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            session()->setFlashdata('error', 'A valid final image is required.');

            return redirect()->back();
        }

        if (! $this->validate(['final_image' => 'max_size[final_image,5120]'])) {
            session()->setFlashdata('error', 'Please fix the upload.');

            return redirect()->back();
        }

        $relative = $this->storeValidatedImage($file, FCPATH . 'uploads/portrait_orders/', 'uploads/portrait_orders/');
        if ($relative === false) {
            return redirect()->back();
        }

        $this->orders->skipValidation(true);
        $ok = $this->orders->update($id, ['final_image' => $relative]);
        $this->orders->skipValidation(false);

        if (! $ok) {
            session()->setFlashdata('error', 'Could not save final image.');

            return redirect()->back();
        }

        session()->setFlashdata('success', 'Final image uploaded.');

        return redirect()->back();
    }
}