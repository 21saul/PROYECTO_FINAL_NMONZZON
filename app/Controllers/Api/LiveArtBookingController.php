<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * LIVEARTBOOKINGCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/LIVEARTBOOKINGCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE RESERVAS DE ARTE EN VIVO: CREACIÓN PÚBLICA, LISTADO ADMIN, DETALLE, ESTADO, CALENDARIO Y PDF DE PRESUPUESTO.
namespace App\Controllers\Api;

use App\Models\LiveArtBookingModel;
use App\Libraries\PdfService;

class LiveArtBookingController extends BaseApiController
{
    protected LiveArtBookingModel $bookings;

    // INICIALIZA MODELO DE RESERVAS Y HELPER API.
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        helper('api');
        $this->bookings = model(LiveArtBookingModel::class);
    }

    // CREA UNA RESERVA CON COTIZACIÓN (BASE POR INVITADOS + TASA POR KM) Y NOTIFICA POR WEBHOOK.
    public function create()
    {
        $input = $this->request->getJSON(true);
        if (! is_array($input) || $input === []) {
            $input = $this->request->getPost();
        }

        $rules = [
            'contact_name'       => 'required|max_length[100]',
            'contact_email'      => 'required|valid_email',
            'event_date'         => 'required|valid_date',
            'event_location'     => 'required',
            'event_city'         => 'required',
            'event_type'         => 'required|in_list[wedding,corporate,birthday,festival,private,other]',
            'contact_phone'      => 'permit_empty|max_length[50]',
            'num_guests'         => 'permit_empty|integer',
            'num_portraits'      => 'permit_empty|integer',
            'special_requirements' => 'permit_empty',
            'event_start_time'   => 'permit_empty',
            'event_end_time'     => 'permit_empty',
            'event_postal_code'  => 'permit_empty|max_length[20]',
            'travel_distance_km' => 'permit_empty|decimal',
        ];

        if (! $this->validateData($input, $rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $validated = $this->validator->getValidated();

        $numGuests = isset($validated['num_guests']) ? (int) $validated['num_guests'] : 0;
        $baseRate  = $numGuests * 5;

        $travelFee = 0.0;
        if (isset($validated['travel_distance_km']) && $validated['travel_distance_km'] !== '') {
            $km        = (float) $validated['travel_distance_km'];
            $travelFee = round($km * 0.75, 2);
        }

        $userId = $this->getUserId();

        $row = [
            'user_id'                => $userId,
            'contact_name'           => $validated['contact_name'],
            'contact_email'          => $validated['contact_email'],
            'contact_phone'          => $validated['contact_phone'] ?? null,
            'event_type'             => $validated['event_type'],
            'event_date'             => $validated['event_date'],
            'event_start_time'       => $validated['event_start_time'] ?? null,
            'event_end_time'         => $validated['event_end_time'] ?? null,
            'event_location'         => $validated['event_location'],
            'event_city'             => $validated['event_city'],
            'event_postal_code'      => $validated['event_postal_code'] ?? null,
            'num_guests'             => $numGuests ?: null,
            'num_portraits'          => isset($validated['num_portraits']) ? (int) $validated['num_portraits'] : null,
            'travel_distance_km'     => isset($validated['travel_distance_km']) && $validated['travel_distance_km'] !== ''
                ? (float) $validated['travel_distance_km'] : null,
            'base_rate'              => $baseRate,
            'travel_fee'             => $travelFee,
            'total_quote'            => round($baseRate + $travelFee, 2),
            'status'                 => 'pending',
            'special_requirements'   => $validated['special_requirements'] ?? null,
        ];

        $id = $this->bookings->skipValidation(true)->insert($row);
        if ($id === false) {
            return apiError('Could not create booking', 500);
        }

        $booking = $this->bookings->find($id);

        // INICIO DE BLOQUE TRY
        try {
            $webhookCtrl = new \App\Controllers\Api\WebhookController();
            $webhookCtrl->notifyNewBooking([
                'booking_id'    => $id,
                'contact_name'  => $booking['contact_name'] ?? '',
                'contact_email' => $booking['contact_email'] ?? '',
                'event_type'    => $booking['event_type'] ?? '',
                'event_date'    => $booking['event_date'] ?? '',
                'num_guests'    => $booking['num_guests'] ?? 0,
                'total_quote'   => $booking['total_quote'] ?? 0,
            ]);
        } catch (\Throwable $e) {
            log_message('warning', 'Webhook notification failed: ' . $e->getMessage());
        }

        // RECORDATORIO INTERNO TRAS BOOKING_FOLLOWUP_DELAY_HOURS (CRON:PROCESS-SCHEDULED).
        // INICIO DE BLOQUE TRY
        try {
            (new \App\Libraries\ScheduledTaskService())->enqueueLiveArtBookingFollowup((int) $id);
        } catch (\Throwable $e) {
            log_message('warning', 'Scheduled booking follow-up enqueue failed: ' . $e->getMessage());
        }

        return apiResponse($booking, 201, 'Booking created');
    }

    // LISTA RESERVAS CON FILTROS OPCIONALES (SOLO ADMIN).
    public function index()
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $status    = $this->request->getGet('status');
        $dateFrom  = $this->request->getGet('date_from');
        $dateTo    = $this->request->getGet('date_to');
        $eventType = $this->request->getGet('event_type');

        $builder = $this->bookings->builder();

        if ($status) {
            $builder->where('status', $status);
        }
        if ($dateFrom) {
            $builder->where('event_date >=', $dateFrom);
        }
        if ($dateTo) {
            $builder->where('event_date <=', $dateTo);
        }
        if ($eventType) {
            $builder->where('event_type', $eventType);
        }

        $rows = $builder->orderBy('event_date', 'ASC')->get()->getResultArray();

        return apiResponse($rows);
    }

    // MUESTRA UNA RESERVA: ADMIN VE CUALQUIERA; USUARIO SOLO SI COINCIDE user_id O EMAIL DE CONTACTO.
    public function show($id = null)
    {
        if ($id === null) {
            return apiError('Not found', 404);
        }

        $booking = $this->bookings->find($id);
        if (!$booking) {
            return apiError('Not found', 404);
        }

        if ($this->isAdmin()) {
            return apiResponse($booking);
        }

        $userId = $this->getUserId();
        $user   = $this->getUserData();
        $email  = isset($user['email']) ? strtolower((string) $user['email']) : null;

        $ownerByUser = $userId !== null && (int) ($booking['user_id'] ?? 0) === $userId;
        $ownerByMail = $email !== null && strtolower((string) $booking['contact_email']) === $email;

        if (!$ownerByUser && !$ownerByMail) {
            return apiError('Forbidden', 403);
        }

        return apiResponse($booking);
    }

    // ACTUALIZA ESTADO Y NOTAS DE ADMINISTRADOR DE UNA RESERVA.
    public function updateStatus($id = null)
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        $booking = $this->bookings->find($id);
        if (!$booking) {
            return apiError('Not found', 404);
        }

        $payload = $this->request->getJSON(true) ?? [];
        $this->validator->reset();

        $rules = [
            'status'      => 'required|in_list[pending,quoted,confirmed,deposit_paid,completed,cancelled]',
            'admin_notes' => 'permit_empty',
        ];

        if (!$this->validateData($payload, $rules)) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $update = ['status' => $payload['status']];
        if (array_key_exists('admin_notes', $payload)) {
            $update['admin_notes'] = $payload['admin_notes'];
        }

        if (! $this->bookings->skipValidation(true)->update($id, $update)) {
            return apiError('Could not update booking', 500);
        }

        return apiResponse($this->bookings->find($id), 200, 'Status updated');
    }

    // DEVUELVE EVENTOS PARA CALENDARIO (FULLCALENDAR) CON COLORES POR ESTADO.
    public function calendar()
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $bookings = $this->bookings->orderBy('event_date', 'ASC')->findAll();
        $colors   = [
            'pending'      => '#6b7280',
            'quoted'       => '#8b5cf6',
            'confirmed'    => '#22c55e',
            'deposit_paid' => '#14b8a6',
            'completed'    => '#15803d',
            'cancelled'    => '#ef4444',
        ];

        $events = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($bookings as $b) {
            $date = $b['event_date'] ?? '';
            $startTime = ! empty($b['event_start_time']) ? trim((string) $b['event_start_time']) : '00:00:00';
            $endTime   = ! empty($b['event_end_time']) ? trim((string) $b['event_end_time']) : '23:59:59';

            if (strlen($startTime) === 5) {
                $startTime .= ':00';
            }
            if (strlen($endTime) === 5) {
                $endTime .= ':00';
            }

            $status = $b['status'] ?? 'pending';
            $events[] = [
                'id'            => (int) $b['id'],
                'title'         => trim(($b['contact_name'] ?? '') . ' — ' . ($b['event_type'] ?? '')),
                'start'         => $date ? $date . 'T' . $startTime : null,
                'end'           => $date ? $date . 'T' . $endTime : null,
                'color'         => $colors[$status] ?? '#6b7280',
                'extendedProps' => [
                    'status'   => $status,
                    'location' => $b['event_location'] ?? '',
                    'contact'  => [
                        'name'  => $b['contact_name'] ?? '',
                        'email' => $b['contact_email'] ?? '',
                        'phone' => $b['contact_phone'] ?? '',
                    ],
                ],
            ];
        }

        return apiResponse($events);
    }

    // GENERA PDF DE PRESUPUESTO Y GUARDA LA RUTA RELATIVA EN LA RESERVA.
    public function generateQuote($id = null)
    {
        if (!$this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null) {
            return apiError('Not found', 404);
        }

        $booking = $this->bookings->find($id);
        if (!$booking) {
            return apiError('Not found', 404);
        }

        $pdfService = new PdfService();
        $relativePath = $pdfService->generateQuote($booking);

        if (! $this->bookings->skipValidation(true)->update($id, ['invoice_path' => $relativePath])) {
            return apiError('Could not save invoice path', 500);
        }

        return apiResponse([
            'invoice_path'  => $relativePath,
            'download_url'  => base_url($relativePath),
        ], 200, 'Quote generated');
    }
}