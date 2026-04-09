<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * BOOKINGADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/BOOKINGADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LiveArtBookingModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BookingAdminController extends BaseController
{
    protected LiveArtBookingModel $bookings;

    /**
     * ASOCIA EL MODELO DE RESERVAS Y CARGA HELPERS.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->bookings = model(LiveArtBookingModel::class);
    }

    /**
     * REDIRIGE AL LOGIN DE ADMIN SI NO HAY SESIÓN AUTORIZADA.
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
     * TRANSFORMA FILAS DE RESERVA EN EVENTOS PARA FULLCALENDAR (INICIO, FIN OPCIONAL, PROPIEDADES EXTENDIDAS).
     *
     * @return list<array<string, mixed>>
     */
    private function buildCalendarEvents(array $rows): array
    {
        $events = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $b) {
            $date = (string) ($b['event_date'] ?? '');
            $startTime = trim((string) ($b['event_start_time'] ?? ''));
            $endTime   = trim((string) ($b['event_end_time'] ?? ''));

            $start = $date;
            if ($startTime !== '') {
                $start .= 'T' . $startTime;
            }

            $end = null;
            if ($endTime !== '') {
                $end = $date . 'T' . $endTime;
            }

            $events[] = [
                'id'            => (int) ($b['id'] ?? 0),
                'title'         => trim(($b['booking_number'] ?? '') . ' — ' . ($b['event_type'] ?? '')),
                'start'         => $start,
                'end'           => $end,
                'allDay'        => $startTime === '',
                'extendedProps' => [
                    'status'   => $b['status'] ?? '',
                    'city'     => $b['event_city'] ?? '',
                    'location' => $b['event_location'] ?? '',
                ],
            ];
        }

        return $events;
    }

    /**
     * TABLA DE RESERVAS CON FILTROS POR ESTADO, TIPO DE EVENTO Y RANGO DE FECHAS.
     */
    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $status    = $this->request->getGet('status');
        $eventType = $this->request->getGet('event_type');
        $dateFrom  = $this->request->getGet('date_from');
        $dateTo    = $this->request->getGet('date_to');

        if ($status !== null && $status !== '') {
            $this->bookings->where('status', (string) $status);
        }
        if ($eventType !== null && $eventType !== '') {
            $this->bookings->where('event_type', (string) $eventType);
        }
        if ($dateFrom !== null && $dateFrom !== '') {
            $this->bookings->where('event_date >=', (string) $dateFrom);
        }
        if ($dateTo !== null && $dateTo !== '') {
            $this->bookings->where('event_date <=', (string) $dateTo);
        }

        $bookings = $this->bookings->orderBy('event_date', 'DESC')->orderBy('created_at', 'DESC')->findAll();

        return view('admin/bookings/index', [
            'title'    => 'Live art bookings',
            'bookings' => $bookings,
            'filters'  => [
                'status'     => $status,
                'event_type' => $eventType,
                'date_from'  => $dateFrom,
                'date_to'    => $dateTo,
            ],
        ]);
    }

    /**
     * VISTA DE CALENDARIO CON EVENTOS SERIALIZADOS PARA EL FRONTEND.
     */
    public function calendar()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rows   = $this->bookings->orderBy('event_date', 'ASC')->findAll();
        $events = $this->buildCalendarEvents($rows);

        return view('admin/bookings/calendar', [
            'title'  => 'Booking calendar',
            'events' => $events,
        ]);
    }

    /**
     * FICHA DE UNA RESERVA CONCRETA.
     */
    public function show(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $booking = $this->bookings->find($id);
        if ($booking === null) {
            session()->setFlashdata('error', 'Booking not found.');

            return redirect()->to('/admin/bookings');
        }

        return view('admin/bookings/show', [
            'title'   => 'Booking #' . $id,
            'booking' => $booking,
        ]);
    }

    /**
     * ACTUALIZA ESTADO Y NOTAS DE ADMINISTRADOR TRAS VALIDACIÓN.
     */
    public function updateStatus(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->bookings->find($id) === null) {
            session()->setFlashdata('error', 'Booking not found.');

            return redirect()->to('/admin/bookings');
        }

        $rules = [
            'status'      => 'required|in_list[pending,quoted,confirmed,deposit_paid,completed,cancelled]',
            'admin_notes' => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $data = [
            'status'      => (string) $this->request->getPost('status'),
            'admin_notes' => $this->request->getPost('admin_notes') !== null && (string) $this->request->getPost('admin_notes') !== ''
                ? (string) $this->request->getPost('admin_notes') : null,
        ];

        if (! $this->bookings->update($id, $data)) {
            session()->setFlashdata('error', 'Could not update booking.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Booking updated.');

        return redirect()->back();
    }

    /**
     * DATOS JSON PARA CALENDARIO SIMPLE: UN EVENTO POR DÍA CON COLOR SEGÚN ESTADO.
     */
    public function calendarData()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rows = $this->bookings->orderBy('event_date', 'ASC')->findAll();
        $events = [];
        $statusColors = [
            'pending'      => '#ffc107',
            'confirmed'    => '#198754',
            'completed'    => '#0d6efd',
            'cancelled'    => '#dc3545',
            'quoted'       => '#6c757d',
            'deposit_paid' => '#0dcaf0',
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $b) {
            $date = (string) ($b['event_date'] ?? '');
            $status = $b['status'] ?? 'pending';
            $events[] = [
                'id'    => (int) ($b['id'] ?? 0),
                'title' => trim(($b['contact_name'] ?? '') . ' — ' . ($b['event_type'] ?? '')),
                'start' => $date,
                'color' => $statusColors[$status] ?? '#c9a96e',
            ];
        }

        return $this->response->setJSON($events);
    }

    /**
     * GENERA PDF DE PRESUPUESTO PARA LA RESERVA Y GUARDA LA RUTA EN quote_path.
     */
    public function generateQuote(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $booking = $this->bookings->find($id);
        if ($booking === null) {
            session()->setFlashdata('error', 'Reserva no encontrada.');
            return redirect()->to('/admin/bookings');
        }

        // INICIO DE BLOQUE TRY
        try {
            $pdfService = new \App\Libraries\PdfService();
            $path = $pdfService->generateQuote($booking);
            $this->bookings->update($id, ['quote_path' => $path]);
            session()->setFlashdata('success', 'Presupuesto PDF generado correctamente.');
        } catch (\Exception $e) {
            log_message('error', 'Quote generation failed: ' . $e->getMessage());
            session()->setFlashdata('error', 'No se pudo generar el presupuesto.');
        }

        return redirect()->back();
    }

    /**
     * ACTUALIZA SOLO EL CAMPO admin_notes DE LA RESERVA.
     */
    public function updateNotes(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $booking = $this->bookings->find($id);
        if ($booking === null) {
            session()->setFlashdata('error', 'Reserva no encontrada.');
            return redirect()->to('/admin/bookings');
        }

        $this->bookings->update($id, [
            'admin_notes' => (string) $this->request->getPost('admin_notes'),
        ]);

        session()->setFlashdata('success', 'Notas actualizadas.');
        return redirect()->back();
    }
}