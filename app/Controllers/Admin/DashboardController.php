<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * DASHBOARDCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/DASHBOARDCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;
use App\Models\LiveArtBookingModel;
use App\Models\OrderModel;
use App\Models\PortraitOrderModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class DashboardController extends BaseController
{
    protected OrderModel $orderModel;
    protected PortraitOrderModel $portraitOrderModel;
    protected LiveArtBookingModel $bookingModel;
    protected ContactMessageModel $contactModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['url']);
        $this->orderModel         = model(OrderModel::class);
        $this->portraitOrderModel = model(PortraitOrderModel::class);
        $this->bookingModel       = model(LiveArtBookingModel::class);
        $this->contactModel       = model(ContactMessageModel::class);
    }

    protected function requireAdminSession(): ?ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/admin/login');
        }

        return null;
    }

    /**
     * Ingresos pagados (tienda + retratos) para un mes calendario YYYY-MM.
     */
    protected function revenuePaidForMonth(string $yearMonth): float
    {
        $db = db_connect();
        $r  = (float) ($db->query(
            "SELECT COALESCE(SUM(total), 0) AS s FROM orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$yearMonth]
        )->getRow()?->s ?? 0);
        $r += (float) ($db->query(
            "SELECT COALESCE(SUM(total_price), 0) AS s FROM portrait_orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$yearMonth]
        )->getRow()?->s ?? 0);

        return $r;
    }

    protected function spanishMonthAbbrForFirstDayOfMonth(string $yearMonth): string
    {
        $ts  = strtotime($yearMonth . '-01');
        $map = ['ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'];
        $n   = (int) date('n', $ts);

        return $map[$n - 1] ?? '';
    }

    protected function dashboardDateLabelSpanish(): string
    {
        $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $d     = (int) date('j');
        $m     = (int) date('n') - 1;
        $y     = date('Y');

        return $d . ' de ' . ($meses[$m] ?? '') . ', ' . $y;
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function buildRecentOrdersMerged(int $limit = 5): array
    {
        $shopRows = $this->orderModel
            ->select('orders.*, users.name AS user_name')
            ->join('users', 'users.id = orders.user_id', 'left')
            ->orderBy('orders.created_at', 'DESC')
            ->limit(max(10, $limit))
            ->findAll();

        $portraitRows = $this->portraitOrderModel
            ->select('portrait_orders.*, users.name AS user_name')
            ->join('users', 'users.id = portrait_orders.user_id', 'left')
            ->orderBy('portrait_orders.created_at', 'DESC')
            ->limit(max(10, $limit))
            ->findAll();

        $recent = [];
        foreach ($shopRows as $row) {
            $recent[] = [
                'id'           => $row['id'],
                'order_number' => $row['order_number'],
                'customer_name'=> $row['user_name'] ?? '',
                'type'         => 'Tienda',
                'status'       => $row['status'],
                'total'        => $row['total'],
                'created_at'   => $row['created_at'],
                'admin_href'   => ! empty($row['user_id']) ? site_url('admin/users/' . (int) $row['user_id']) : null,
            ];
        }
        foreach ($portraitRows as $row) {
            $recent[] = [
                'id'           => $row['id'],
                'order_number' => $row['order_number'],
                'customer_name'=> $row['user_name'] ?? '',
                'type'         => 'Retrato',
                'status'       => $row['status'],
                'total'        => $row['total_price'],
                'created_at'   => $row['created_at'],
                'admin_href'   => site_url('admin/portrait-orders/' . (int) $row['id']),
            ];
        }

        usort($recent, static function (array $a, array $b): int {
            return strtotime((string) $b['created_at']) <=> strtotime((string) $a['created_at']);
        });

        return array_slice($recent, 0, $limit);
    }

    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $db    = db_connect();
        $month = date('Y-m');

        $revenueThisMonth = (float) ($db->query(
            "SELECT COALESCE(SUM(total), 0) AS s FROM orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$month]
        )->getRow()?->s ?? 0);

        $revenueThisMonth += (float) ($db->query(
            "SELECT COALESCE(SUM(total_price), 0) AS s FROM portrait_orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$month]
        )->getRow()?->s ?? 0);

        $recentOrders = $this->buildRecentOrdersMerged(5);

        $prevMonth = date('Y-m', strtotime('-1 month'));
        $revPrev = (float) ($db->query(
            "SELECT COALESCE(SUM(total), 0) AS s FROM orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$prevMonth]
        )->getRow()?->s ?? 0);
        $revPrev += (float) ($db->query(
            "SELECT COALESCE(SUM(total_price), 0) AS s FROM portrait_orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$prevMonth]
        )->getRow()?->s ?? 0);
        $revenueChange = $revPrev > 0 ? round(($revenueThisMonth - $revPrev) / $revPrev * 100, 1) : 0;

        $activeOrders = $this->orderModel->whereNotIn('status', ['completed', 'cancelled'])->countAllResults(false);
        $activeOrders += $this->portraitOrderModel->whereNotIn('status', ['completed', 'cancelled'])->countAllResults(false);

        $upcomingBookings = $this->bookingModel
            ->where('status', 'confirmed')
            ->where('event_date >=', date('Y-m-d'))
            ->where('event_date <=', date('Y-m-d', strtotime('+30 days')))
            ->countAllResults(false);

        $unreadMessages = $this->contactModel->where('is_read', 0)->countAllResults(false);

        $nextBookings = $this->bookingModel
            ->where('event_date >=', date('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('event_date', 'ASC')
            ->limit(3)
            ->findAll();

        $monthLabels = [];
        $revenueDataArr = [];
        for ($i = 11; $i >= 0; $i--) {
            $m               = date('Y-m', strtotime("-{$i} months"));
            $monthLabels[]   = $this->spanishMonthAbbrForFirstDayOfMonth($m);
            $revenueDataArr[] = round($this->revenuePaidForMonth($m), 2);
        }

        $styleRows = $db->query(
            "SELECT ps.name, COUNT(po.id) AS cnt FROM portrait_orders po
             LEFT JOIN portrait_styles ps ON ps.id = po.portrait_style_id
             WHERE po.deleted_at IS NULL
             GROUP BY po.portrait_style_id, ps.name ORDER BY cnt DESC LIMIT 5"
        )->getResultArray();
        $styleLabels = array_column($styleRows, 'name');
        $styleData = array_map('intval', array_column($styleRows, 'cnt'));

        $data = [
            'title'               => 'Dashboard',
            'revenue'             => round($revenueThisMonth, 2),
            'revenueChange'       => $revenueChange,
            'activeOrders'        => $activeOrders,
            'upcomingBookings'    => $upcomingBookings,
            'unreadMessages'      => $unreadMessages,
            'recentOrders'        => $recentOrders,
            'nextBookings'        => $nextBookings,
            'monthLabels'         => $monthLabels,
            'revenueData'         => $revenueDataArr,
            'styleLabels'         => $styleLabels,
            'styleData'           => $styleData,
            'dashboardDateLabel'  => $this->dashboardDateLabelSpanish(),
        ];

        return view('admin/dashboard', $data);
    }

    public function chartData()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $monthLabels = [];
        $revenueDataArr = [];
        for ($i = 11; $i >= 0; $i--) {
            $m                = date('Y-m', strtotime("-{$i} months"));
            $monthLabels[]    = $this->spanishMonthAbbrForFirstDayOfMonth($m);
            $revenueDataArr[] = round($this->revenuePaidForMonth($m), 2);
        }

        return $this->response->setJSON([
            'monthLabels' => $monthLabels,
            'revenueData' => $revenueDataArr,
        ]);
    }
}