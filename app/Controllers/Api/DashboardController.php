<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * DASHBOARDCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/DASHBOARDCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\ContactMessageModel;
use App\Models\LiveArtBookingModel;
use App\Models\OrderModel;
use App\Models\PortraitOrderModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class DashboardController extends BaseApiController
{
    protected OrderModel $orderModel;
    protected PortraitOrderModel $portraitOrderModel;
    protected LiveArtBookingModel $bookingModel;
    protected ContactMessageModel $contactModel;
    protected ProductModel $productModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->orderModel          = model(OrderModel::class);
        $this->portraitOrderModel  = model(PortraitOrderModel::class);
        $this->bookingModel        = model(LiveArtBookingModel::class);
        $this->contactModel        = model(ContactMessageModel::class);
        $this->productModel        = model(ProductModel::class);
    }

    protected function requireAdmin(): ?ResponseInterface
    {
        if (! $this->getUserId()) {
            return apiError('Unauthorized', 401);
        }
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        return null;
    }

    public function stats(): ResponseInterface
    {
        if ($err = $this->requireAdmin()) {
            return $err;
        }

        $db = db_connect();
        $month = date('Y-m');

        $productRevenue = (float) ($db->query(
            "SELECT COALESCE(SUM(total), 0) AS s FROM orders WHERE payment_status = 'paid' AND deleted_at IS NULL"
        )->getRow()?->s ?? 0);

        $portraitRevenue = (float) ($db->query(
            "SELECT COALESCE(SUM(total_price), 0) AS s FROM portrait_orders WHERE payment_status = 'paid' AND deleted_at IS NULL"
        )->getRow()?->s ?? 0);

        $thisMonthProduct = (float) ($db->query(
            "SELECT COALESCE(SUM(total), 0) AS s FROM orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$month]
        )->getRow()?->s ?? 0);

        $thisMonthPortrait = (float) ($db->query(
            "SELECT COALESCE(SUM(total_price), 0) AS s FROM portrait_orders WHERE payment_status = 'paid' AND deleted_at IS NULL
             AND DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') = ?",
            [$month]
        )->getRow()?->s ?? 0);

        return apiResponse([
            'total_revenue'          => round($productRevenue + $portraitRevenue, 2),
            'total_portrait_orders'  => $this->portraitOrderModel->countAllResults(),
            'total_product_orders'   => $this->orderModel->countAllResults(),
            'pending_bookings'       => $this->bookingModel->where('status', 'pending')->countAllResults(),
            'unread_messages'        => $this->contactModel->where('is_read', 0)->countAllResults(),
            'total_products'         => $this->productModel->where('is_active', 1)->countAllResults(),
            'this_month_revenue'     => round($thisMonthProduct + $thisMonthPortrait, 2),
        ]);
    }

    public function revenue(): ResponseInterface
    {
        if ($err = $this->requireAdmin()) {
            return $err;
        }

        $db = db_connect();

        $productRows = $db->query(
            "SELECT DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') AS ym, COALESCE(SUM(total), 0) AS product_revenue
             FROM orders
             WHERE payment_status = 'paid' AND deleted_at IS NULL
             GROUP BY ym"
        )->getResultArray();

        $portraitRows = $db->query(
            "SELECT DATE_FORMAT(COALESCE(paid_at, created_at), '%Y-%m') AS ym, COALESCE(SUM(total_price), 0) AS portrait_revenue
             FROM portrait_orders
             WHERE payment_status = 'paid' AND deleted_at IS NULL
             GROUP BY ym"
        )->getResultArray();

        $byMonth = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($productRows as $r) {
            $m = (string) $r['ym'];
            $byMonth[$m]['product_revenue'] = (float) $r['product_revenue'];
        }
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($portraitRows as $r) {
            $m = (string) $r['ym'];
            $byMonth[$m]['portrait_revenue'] = (float) $r['portrait_revenue'];
        }

        $out   = [];
        $start = strtotime('first day of this month');
        // BUCLE FOR
        for ($i = 11; $i >= 0; $i--) {
            $ts    = strtotime("-{$i} months", $start);
            $label = date('Y-m', $ts);
            $p     = $byMonth[$label]['product_revenue'] ?? 0.0;
            $po    = $byMonth[$label]['portrait_revenue'] ?? 0.0;
            $out[] = [
                'month'             => $label,
                'portrait_revenue'  => round($po, 2),
                'product_revenue'   => round($p, 2),
                'total'             => round($p + $po, 2),
            ];
        }

        return apiResponse($out);
    }

    public function ordersByStyle(): ResponseInterface
    {
        if ($err = $this->requireAdmin()) {
            return $err;
        }

        $db = db_connect();

        $rows = $db->query(
            'SELECT portrait_styles.name AS style_name,
                    COUNT(*) AS `count`,
                    COALESCE(SUM(CASE WHEN portrait_orders.payment_status = \'paid\' THEN portrait_orders.total_price ELSE 0 END), 0) AS total_revenue
             FROM portrait_orders
             INNER JOIN portrait_styles ON portrait_styles.id = portrait_orders.portrait_style_id
             WHERE portrait_orders.deleted_at IS NULL
             GROUP BY portrait_styles.id, portrait_styles.name
             ORDER BY `count` DESC'
        )->getResultArray();

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as &$r) {
            $r['count']          = (int) $r['count'];
            $r['total_revenue']  = round((float) $r['total_revenue'], 2);
        }
        unset($r);

        return apiResponse($rows);
    }

    public function topProducts(): ResponseInterface
    {
        if ($err = $this->requireAdmin()) {
            return $err;
        }

        $db = db_connect();

        $rows = $db->query(
            'SELECT products.name AS product_name,
                    COALESCE(SUM(order_items.quantity), 0) AS total_sold,
                    COALESCE(SUM(order_items.total_price), 0) AS total_revenue
             FROM order_items
             INNER JOIN products ON products.id = order_items.product_id
             INNER JOIN orders ON orders.id = order_items.order_id
             WHERE orders.deleted_at IS NULL
             GROUP BY products.id, products.name
             ORDER BY total_sold DESC
             LIMIT 10'
        )->getResultArray();

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as &$r) {
            $r['total_sold']     = (int) $r['total_sold'];
            $r['total_revenue']  = round((float) $r['total_revenue'], 2);
        }
        unset($r);

        return apiResponse($rows);
    }
}