<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * ORDERCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/ORDERCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE PEDIDOS DE TIENDA: CREACIÓN CON TRANSACCIÓN, STOCK, CUPÓN, LISTADO, DETALLE, ESTADO Y DESCARGA DE FACTURA PDF.
namespace App\Controllers\Api;

use App\Models\CouponModel;
use App\Models\OrderItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Libraries\PdfService;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class OrderController extends BaseApiController
{
    protected OrderModel $orderModel;
    protected OrderItemModel $itemModel;
    protected ProductModel $productModel;
    protected ProductVariantModel $variantModel;
    protected CouponModel $couponModel;

    // REGISTRA MODELOS NECESARIOS PARA PEDIDOS, LÍNEAS, PRODUCTOS, VARIANTES Y CUPONES.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->orderModel   = model(OrderModel::class);
        $this->itemModel    = model(OrderItemModel::class);
        $this->productModel = model(ProductModel::class);
        $this->variantModel = model(ProductVariantModel::class);
        $this->couponModel  = model(CouponModel::class);
    }

    // CREA UN PEDIDO COMPLETO: VALIDA LÍNEAS, DESCUENTA STOCK, APLICA CUPÓN Y CONFIRMA LA TRANSACCIÓN.
    public function create(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        $rules = [
            'items'                   => 'required',
            'items.*.product_id'      => 'required|integer',
            'items.*.variant_id'      => 'permit_empty|integer',
            'items.*.quantity'        => 'required|integer|greater_than[0]',
            'shipping_name'           => 'required|max_length[100]',
            'shipping_address'        => 'required|max_length[500]',
            'shipping_city'           => 'required|max_length[100]',
            'shipping_postal_code'    => 'required|max_length[10]',
            'shipping_country'        => 'required|max_length[100]',
            'shipping_phone'          => 'permit_empty|max_length[20]',
            'coupon_code'             => 'permit_empty|max_length[50]',
            'notes'                   => 'permit_empty|max_length[5000]',
        ];

        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        /** @var list<array{product_id: int, variant_id?: int|null, quantity: int}> $items */
        $items = $validated['items'];
        if (! is_array($items) || $items === []) {
            return apiError('items must be a non-empty array', 422);
        }

        $db = db_connect();
        $db->transStart();

        $lineRows = [];
        $subtotal   = 0.0;

        // RECORRE CADA LÍNEA: VALIDA PRODUCTO/VARIANTE, STOCK, ACTUALIZA EXISTENCIAS Y ACUMULA SUBTOTAL.
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($items as $row) {
            $productId = (int) $row['product_id'];
            $quantity  = (int) $row['quantity'];
            $variantId = isset($row['variant_id']) && $row['variant_id'] !== '' && $row['variant_id'] !== null
                ? (int) $row['variant_id']
                : null;

            $product = $this->productModel->find($productId);
            if ($product === null || empty($product['is_active'])) {
                $db->transRollback();

                return apiError('Product not found or inactive: ' . $productId, 422);
            }

            $basePrice = (float) ($product['price'] ?? 0);
            $unitPrice = $basePrice;

            if ($variantId !== null) {
                $variant = $this->variantModel->find($variantId);
                if ($variant === null || (int) ($variant['product_id'] ?? 0) !== $productId) {
                    $db->transRollback();

                    return apiError('Invalid variant for product ' . $productId, 422);
                }
                if (empty($variant['is_active'])) {
                    $db->transRollback();

                    return apiError('Variant is inactive', 422);
                }
                $unitPrice += (float) ($variant['price_modifier'] ?? 0);
                $available = (int) ($variant['stock'] ?? 0);
            } else {
                $available = (int) ($product['stock'] ?? 0);
            }

            if ($available < $quantity) {
                $db->transRollback();

                return apiError('Insufficient stock for product ' . $productId, 422);
            }

            $lineTotal = round($unitPrice * $quantity, 2);
            $subtotal += $lineTotal;

            if ($variantId !== null) {
                $newStock = $available - $quantity;
                if (! $this->variantModel->update($variantId, ['stock' => $newStock])) {
                    $db->transRollback();

                    return apiError('Could not update variant stock', 500);
                }
            } else {
                $newStock = $available - $quantity;
                if (! $this->productModel->update($productId, ['stock' => $newStock])) {
                    $db->transRollback();

                    return apiError('Could not update product stock', 500);
                }
            }

            $lineRows[] = [
                'product_id'           => $productId,
                'product_variant_id'   => $variantId,
                'product_name'         => (string) ($product['name'] ?? ''),
                'quantity'             => $quantity,
                'unit_price'           => $unitPrice,
                'total_price'          => $lineTotal,
            ];
        }

        $discount    = 0.0;
        $couponCode  = isset($validated['coupon_code']) ? trim((string) $validated['coupon_code']) : '';
        $couponRow   = null;

        // SI HAY CÓDIGO DE CUPÓN: BUSCA, VALIDA COMPRA MÍNIMA Y CALCULA DESCUENTO PORCENTUAL O FIJO.
        if ($couponCode !== '') {
            $couponRow = $this->couponModel->getByCode($couponCode);
            if ($couponRow === null || ! $this->couponModel->isValid($couponCode)) {
                $db->transRollback();

                return apiError('Invalid or expired coupon', 422);
            }
            $minPurchase = (float) ($couponRow['min_purchase'] ?? 0);
            if ($subtotal < $minPurchase) {
                $db->transRollback();

                return apiError('Subtotal below minimum purchase for this coupon', 422);
            }
            $type = (string) ($couponRow['type'] ?? '');
            $val  = (float) ($couponRow['value'] ?? 0);
            if ($type === 'percentage') {
                $discount = round($subtotal * ($val / 100), 2);
            } else {
                $discount = round(min($val, $subtotal), 2);
            }
        }

        // IVA SOBRE IMPORTE TRAS CUPÓN; ENVÍO GRATIS SI SUBTOTAL > 50.
        $afterDiscount = max(0.0, round($subtotal - $discount, 2));
        $tax           = round($afterDiscount * 0.21, 2);
        $shippingCost  = $subtotal > 50 ? 0.0 : 5.0;
        $total         = round($afterDiscount + $tax + $shippingCost, 2);

        $orderData = [
            'user_id'               => $userId,
            'status'                => 'pending',
            'subtotal'              => $subtotal,
            'shipping_cost'         => $shippingCost,
            'tax'                   => $tax,
            'discount'              => $discount,
            'total'                 => $total,
            'coupon_code'           => $couponCode !== '' ? $couponCode : null,
            'shipping_name'         => (string) $validated['shipping_name'],
            'shipping_address'      => (string) $validated['shipping_address'],
            'shipping_city'         => (string) $validated['shipping_city'],
            'shipping_postal_code'  => (string) $validated['shipping_postal_code'],
            'shipping_country'      => (string) $validated['shipping_country'],
            'shipping_phone'        => isset($validated['shipping_phone']) ? (string) $validated['shipping_phone'] : null,
            'payment_status'        => 'pending',
            'notes'                 => isset($validated['notes']) ? (string) $validated['notes'] : null,
        ];

        $orderId = $this->orderModel->skipValidation(true)->insert($orderData);
        if ($orderId === false) {
            $db->transRollback();

            return apiError('Could not create order', 500);
        }

        $orderId = (int) $orderId;

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($lineRows as $line) {
            $line['order_id'] = $orderId;
            if ($this->itemModel->skipValidation(true)->insert($line) === false) {
                $db->transRollback();

                return apiError('Could not create order items', 500);
            }
        }

        if ($couponRow !== null) {
            $this->couponModel->incrementUsage((int) $couponRow['id']);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return apiError('Transaction failed', 500);
        }

        $order = $this->orderModel->getWithItems($orderId);

        return apiResponse($order, 201, 'Order created');
    }

    // LISTA PEDIDOS: ADMIN CON FILTROS; CLIENTE SOLO LOS SUYOS.
    public function index(): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }

        $builder = $this->orderModel->builder();

        if ($this->isAdmin()) {
            $status = $this->request->getGet('status');
            if ($status !== null && $status !== '') {
                $builder->where('orders.status', $status);
            }
            $paymentStatus = $this->request->getGet('payment_status');
            if ($paymentStatus !== null && $paymentStatus !== '') {
                $builder->where('orders.payment_status', $paymentStatus);
            }
            $dateFrom = $this->request->getGet('date_from');
            if ($dateFrom !== null && $dateFrom !== '') {
                $builder->where('orders.created_at >=', $dateFrom);
            }
            $dateTo = $this->request->getGet('date_to');
            if ($dateTo !== null && $dateTo !== '') {
                $builder->where('orders.created_at <=', $dateTo . ' 23:59:59');
            }
        } else {
            $builder->where('orders.user_id', $userId);
        }

        $orders = $builder->orderBy('orders.created_at', 'DESC')->get()->getResultArray();

        return apiResponse($orders);
    }

    // MUESTRA UN PEDIDO CON SUS ÍTEMS SI EL USUARIO ES DUEÑO O ADMIN.
    public function show($id = null): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        $order = $this->orderModel->getWithItems((int) $id);
        if ($order === null) {
            return apiError('Not found', 404);
        }

        if (! $this->isAdmin() && (int) ($order['user_id'] ?? 0) !== $userId) {
            return apiError('Forbidden', 403);
        }

        return apiResponse($order);
    }

    // ACTUALIZA EL ESTADO DEL PEDIDO Y FECHAS DE ENVÍO/ENTREGA CUANDO CORRESPONDE (ADMIN).
    public function updateStatus($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        $order = $this->orderModel->find((int) $id);
        if ($order === null) {
            return apiError('Not found', 404);
        }

        $rules = [
            'status' => 'required|in_list[pending,processing,shipped,delivered,cancelled,refunded]',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $status = (string) $validated['status'];
        $update = ['status' => $status];
        $now    = date('Y-m-d H:i:s');

        if ($status === 'shipped') {
            $update['shipped_at'] = $now;
        }
        if ($status === 'delivered') {
            $update['delivered_at'] = $now;
        }

        if (! $this->orderModel->skipValidation(true)->update((int) $id, $update)) {
            return apiError('Could not update order', 500);
        }

        return apiResponse($this->orderModel->getWithItems((int) $id), 200, 'Status updated');
    }

    // DESCARGA LA FACTURA PDF; LA GENERA SI AÚN NO EXISTE RUTA GUARDADA.
    public function downloadInvoice($id = null): ResponseInterface
    {
        $userId = $this->getUserId();
        if (! $userId) {
            return apiError('Unauthorized', 401);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        $order = $this->orderModel->getWithItems((int) $id);
        if ($order === null) {
            return apiError('Not found', 404);
        }

        if (! $this->isAdmin() && (int) ($order['user_id'] ?? 0) !== $userId) {
            return apiError('Forbidden', 403);
        }

        $relative = (string) ($order['invoice_path'] ?? '');
        $fullPath = $relative !== '' ? FCPATH . $relative : '';

        if ($relative === '' || ! is_file($fullPath)) {
            $pdfService = new PdfService();
            $relative   = $pdfService->generateOrderInvoice($order);
            if (! $this->orderModel->skipValidation(true)->update((int) $id, ['invoice_path' => $relative])) {
                return apiError('Could not save invoice path', 500);
            }
            $fullPath = FCPATH . $relative;
        }

        $name = 'invoice-' . preg_replace('/[^a-zA-Z0-9_-]/', '', (string) ($order['order_number'] ?? 'order')) . '.pdf';

        $dl = $this->response->download($fullPath, null, true);
        if ($dl === null) {
            return apiError('Could not prepare download', 500);
        }

        return $dl->setFileName($name);
    }
}