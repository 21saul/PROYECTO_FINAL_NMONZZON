<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * ARTEENVIVOCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/ARTEENVIVOCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\LiveArtBookingModel;
use App\Models\PortfolioWorkModel;
use App\Models\SiteSettingModel;

class ArteEnVivoController extends BaseController
{
    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        helper(['form', 'url']);
    }

    public function index()
    {
        $categoryModel  = model(CategoryModel::class);
        $portfolioModel = model(PortfolioWorkModel::class);
        $settingsModel  = model(SiteSettingModel::class);

        $category = $categoryModel->where('slug', 'arte-en-vivo')
            ->where('is_active', 1)
            ->first();

        $portfolioWorks = [];
        if ($category !== null) {
            $portfolioWorks = $portfolioModel->where('category_id', (int) $category['id'])
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();
        }

        $featuredFromWorks = array_values(array_filter(
            $portfolioWorks,
            static fn (array $row): bool => ! empty($row['is_featured'])
        ));

        $galleryJson = $settingsModel->get('arte_en_vivo_featured_images');
        $featuredLiveArtImages = [];
        if ($galleryJson !== null && $galleryJson !== '') {
            $decoded = json_decode($galleryJson, true);
            if (is_array($decoded)) {
                $featuredLiveArtImages = $decoded;
            }
        }

        if ($featuredLiveArtImages === [] && $featuredFromWorks !== []) {
            $featuredLiveArtImages = array_map(
                static fn (array $w): array => [
                    'title'     => $w['title'] ?? '',
                    'image_url' => $w['image_url'] ?? '',
                    'slug'      => $w['slug'] ?? '',
                ],
                $featuredFromWorks
            );
        }

        $liveArtGalleryDir = FCPATH . 'uploads/live-art/fotosartenvivo';
        $liveArtGalleryImages = [];
        if (is_dir($liveArtGalleryDir)) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $files       = scandir($liveArtGalleryDir, SCANDIR_SORT_NONE) ?: [];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') {
                    continue;
                }
                $ext = strtolower((string) pathinfo($f, PATHINFO_EXTENSION));
                if (! in_array($ext, $allowedExt, true)) {
                    continue;
                }
                $base  = (string) pathinfo($f, PATHINFO_FILENAME);
                $label = ucfirst(str_replace(['_', '-'], ' ', $base));
                $liveArtGalleryImages[] = [
                    'file'  => $f,
                    'label' => $label,
                ];
            }
            usort($liveArtGalleryImages, static function (array $a, array $b): int {
                return strnatcasecmp($a['file'], $b['file']);
            });
        }

        $data = [
            'meta_title'       => 'Arte en Vivo',
            'meta_description' => 'Pintura y retratos en vivo para bodas, eventos corporativos y celebraciones. Reserva tu artista.',
            'category'                 => $category,
            'portfolio_works'          => $portfolioWorks,
            'featured_live_art_images' => $featuredLiveArtImages,
            'liveArtGalleryImages'     => $liveArtGalleryImages,
        ];

        return view('web/arte-en-vivo/index', $data);
    }

    public function reservar()
    {
        return view('web/arte-en-vivo/reservar', [
            'event_types' => [
                'wedding'    => 'Boda',
                'corporate'  => 'Corporativo',
                'birthday'   => 'Cumpleaños',
                'festival'   => 'Festival',
                'private'    => 'Privado',
                'other'      => 'Otro',
            ],
        ]);
    }

    public function processReserva()
    {
        if (! $this->request->is('post')) {
            return redirect()->back();
        }

        // CAPTCHA DE PUZZLE: VALIDAR ANTES DE LAS REGLAS DE NEGOCIO PARA RECHAZAR BOTS RÁPIDO
        helper('captcha');
        if (! nmz_captcha_verify(
            (string) $this->request->getPost('captcha_token'),
            (string) $this->request->getPost('captcha_answer')
        )) {
            return redirect()->back()->withInput()->with('error', 'La verificación anti-spam no es correcta. Inténtalo de nuevo.');
        }

        $rules = [
            'contact_name'         => 'required|max_length[100]',
            'contact_email'        => 'required|valid_email',
            'event_date'           => 'required|valid_date',
            'event_location'       => 'required',
            'event_city'           => 'required',
            'event_type'           => 'required|in_list[wedding,corporate,birthday,festival,private,other]',
            'contact_phone'        => 'permit_empty|max_length[50]',
            'num_guests'           => 'permit_empty|integer',
            'num_portraits'        => 'permit_empty|integer',
            'special_requirements' => 'permit_empty',
            'event_start_time'     => 'permit_empty',
            'event_end_time'       => 'permit_empty',
            'event_postal_code'    => 'permit_empty|max_length[20]',
            'travel_distance_km'   => 'permit_empty|decimal',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $validated = $this->validator->getValidated();

        $numGuests = isset($validated['num_guests']) ? (int) $validated['num_guests'] : 0;
        $baseRate  = $numGuests * 5;

        $travelFee = 0.0;
        if (isset($validated['travel_distance_km']) && $validated['travel_distance_km'] !== '') {
            $km        = (float) $validated['travel_distance_km'];
            $travelFee = round($km * 0.75, 2);
        }

        $session = session();
        $userId  = null;
        if ($session->get('isLoggedIn')) {
            $uid = $session->get('user_id') ?? $session->get('id');
            if ($uid !== null && $uid !== '') {
                $userId = (int) $uid;
                if ($userId <= 0) {
                    $userId = null;
                }
            }
        }

        $bookings = model(LiveArtBookingModel::class);

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

        if ($bookings->skipValidation(true)->insert($row) === false) {
            return redirect()->back()->withInput()->with('error', 'No se pudo enviar la reserva. Inténtalo de nuevo.');
        }

        // NOTIFICAR A N8N SI ESTÁ CONFIGURADO
        $bookingId = $bookings->getInsertID();
        $booking = $bookings->find($bookingId);
        if ($booking) {
            // INICIO DE BLOQUE TRY
            try {
                $webhookCtrl = new \App\Controllers\Api\WebhookController();
                $webhookCtrl->notifyNewBooking([
                    'booking_id'    => $bookingId,
                    'contact_name'  => $booking['contact_name'] ?? '',
                    'contact_email' => $booking['contact_email'] ?? '',
                    'event_type'    => $booking['event_type'] ?? '',
                    'event_date'    => $booking['event_date'] ?? '',
                    'event_city'    => $booking['event_city'] ?? '',
                    'total_quote'   => $booking['total_quote'] ?? 0,
                ]);
            } catch (\Throwable $e) {
                log_message('warning', 'Booking webhook failed: ' . $e->getMessage());
            }

            // INICIO DE BLOQUE TRY
            try {
                $emailService = new \App\Libraries\EmailService();
                $emailService->sendBookingConfirmation($booking);
            } catch (\Throwable $e) {
                log_message('error', 'Booking confirmation email failed: ' . $e->getMessage());
            }

            // RECORDATORIO INTERNO TRAS BOOKING_FOLLOWUP_DELAY_HOURS (CRON:PROCESS-SCHEDULED).
            // INICIO DE BLOQUE TRY
            try {
                (new \App\Libraries\ScheduledTaskService())->enqueueLiveArtBookingFollowup((int) $bookingId);
            } catch (\Throwable $e) {
                log_message('warning', 'Scheduled booking follow-up enqueue failed: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 'Reserva enviada correctamente. Te contactaremos pronto.');
    }
}