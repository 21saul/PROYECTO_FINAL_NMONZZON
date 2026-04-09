<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CARTCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/CARTCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Libraries\CartService;
use App\Libraries\ShopCatalogCartResolver;
use App\Libraries\StripeConfig;
use App\Libraries\StripeService;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\ProductModel;
use App\Models\ProductVariantModel;
use App\Models\CouponModel;
use Config\Database;

class CartController extends BaseController
{
    protected CartService $cartService;

    /**
     * INICIALIZA EL SERVICIO DE CARRITO EN MEMORIA/SESIÓN.
     */
    public function __construct()
    {
        $this->cartService = new CartService();
    }

    /**
     * MUESTRA LA VISTA DEL CARRITO CON ÍTEMS, TOTALES Y CUPÓN APLICADO SI EXISTE.
     */
    public function index()
    {
        return view('web/cart/index', [
            'title'    => 'Carrito',
            'items'    => $this->cartService->getItems(),
            'totals'   => $this->cartService->getTotals(),
            'coupon'   => session()->get('applied_coupon'),
        ]);
    }

    /**
     * AÑADE UN PRODUCTO (Y VARIANTE OPCIONAL) AL CARRITO; SOPORTA RESPUESTA AJAX O REDIRECCIÓN.
     */
    public function add()
    {
        $input = $this->mergePostAndJson();

        $validation = service('validation');
        $validation->setRules([
            'product_id'    => 'permit_empty|integer',
            'catalog_image' => 'permit_empty|string|max_length[512]',
            'product_slug'  => 'permit_empty|string|max_length[191]',
            'variant_id'    => 'permit_empty|integer',
            'quantity'      => 'required|integer|greater_than[0]',
        ]);

        if (! $validation->run($input)) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => 'Datos del producto no válidos.',
                    'errors'  => $validation->getErrors(),
                ]);
            }

            return redirect()->back()->withInput()->with('error', 'Datos del producto no válidos.');
        }

        $productId = $this->resolveProductIdForAdd($input);
        if ($productId <= 0) {
            $msg = 'No se pudo identificar el producto. Si es un print o totebag, comprueba que la tienda esté sincronizada con la base de datos.';
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(422)->setJSON([
                    'success' => false,
                    'message' => $msg,
                ]);
            }

            return redirect()->back()->with('error', $msg);
        }
        $variantRaw = $input['variant_id'] ?? null;
        $variantId    = ($variantRaw !== null && $variantRaw !== '') ? (int) $variantRaw : null;
        $quantity     = (int) $input['quantity'];

        // INICIO DE BLOQUE TRY
        try {
            $this->cartService->addItem($productId, $quantity, $variantId);
        } catch (\RuntimeException $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }

        // RESPUESTA JSON PARA PETICIONES AJAX
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Producto añadido al carrito.',
                'itemCount' => $this->cartService->getItemCount(),
            ]);
        }

        return redirect()->back()->with('success', 'Producto añadido al carrito.');
    }

    /**
     * ID de producto por POST (grid/ficha) o resolución prints/totebags por ruta de imagen o slug.
     *
     * @param array<string, mixed> $input
     */
    private function resolveProductIdForAdd(array $input): int
    {
        $productId = (int) ($input['product_id'] ?? 0);
        if ($productId > 0) {
            return $productId;
        }

        $img = ltrim(str_replace('\\', '/', trim((string) ($input['catalog_image'] ?? ''))), '/');
        if ($img !== '') {
            if (! preg_match('#^uploads/productos/(prints|totebags)/[^/]+$#i', $img)) {
                return 0;
            }
            $row = ShopCatalogCartResolver::resolveDiskCatalogToDbRow([
                'featured_image' => $img,
                'slug'           => '',
                'category_slug'  => ShopCatalogCartResolver::inferCategorySlugFromPath($img),
            ]);

            return $row !== null ? (int) $row['id'] : 0;
        }

        $slug = trim((string) ($input['product_slug'] ?? ''));
        if ($slug !== '') {
            $row = ShopCatalogCartResolver::resolveDiskCatalogToDbRow([
                'featured_image' => '',
                'slug'           => $slug,
            ]);

            return $row !== null ? (int) $row['id'] : 0;
        }

        return 0;
    }

    /**
     * Combina POST y JSON (actualizar carrito/cupón vía JSON; añadir vía FormData multipart).
     *
     * @return array<string, mixed>
     */
    private function mergePostAndJson(): array
    {
        $post = $this->request->getPost();
        $out  = is_array($post) ? $post : [];

        $ct = strtolower($this->request->getHeaderLine('Content-Type'));
        if ($ct === '' || ! str_contains($ct, 'application/json')) {
            return $out;
        }

        try {
            $json = $this->request->getJSON(true);
        } catch (\CodeIgniter\HTTP\Exceptions\HTTPException $e) {
            return $out;
        }

        if (is_array($json) && $json !== []) {
            return array_merge($out, $json);
        }

        return $out;
    }

    /**
     * ACTUALIZA LA CANTIDAD DE UN ÍTEM DEL CARRITO IDENTIFICADO POR CLAVE.
     */
    public function update()
    {
        $input    = $this->mergePostAndJson();
        $key      = $input['key'] ?? null;
        $quantity = (int) ($input['quantity'] ?? 0);

        if (!$key) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Ítem no encontrado.']);
            }
            return redirect()->back()->with('error', 'Ítem no encontrado.');
        }

        $this->cartService->updateQuantity($key, $quantity);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'itemCount' => $this->cartService->getItemCount(),
                'totals'    => $this->cartService->getTotals(),
            ]);
        }

        return redirect()->back()->with('success', 'Carrito actualizado.');
    }

    /**
     * ELIMINA UN ÍTEM DEL CARRITO; LA CLAVE PUEDE VENIR POR RUTA O POST.
     */
    public function remove($key = null)
    {
        if ($key === null || $key === '') {
            $input = $this->mergePostAndJson();
            $key   = $input['key'] ?? null;
        }

        if ($key === null || $key === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Ítem no encontrado.']);
            }

            return redirect()->back()->with('error', 'Ítem no encontrado.');
        }

        $this->cartService->removeItem((string) $key);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'   => true,
                'itemCount' => $this->cartService->getItemCount(),
                'totals'    => $this->cartService->getTotals(),
            ]);
        }

        return redirect()->back()->with('success', 'Producto eliminado del carrito.');
    }

    /**
     * APLICA UN CÓDIGO DE CUPÓN AL CARRITO Y DEVUELVE RESULTADO (AJAX O FLASH).
     */
    public function applyCoupon()
    {
        $input = $this->mergePostAndJson();
        $code  = (string) ($input['coupon_code'] ?? '');
        $result = $this->cartService->applyCoupon($code);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(array_merge($result, [
                'totals' => $this->cartService->getTotals(),
            ]));
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }
        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * QUITA EL CUPÓN APLICADO DE LA SESIÓN Y RECALCULA TOTALES.
     */
    public function removeCoupon()
    {
        $this->cartService->removeCoupon();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'totals'  => $this->cartService->getTotals(),
            ]);
        }

        return redirect()->back()->with('success', 'Cupón eliminado.');
    }

    /**
     * PÁGINA DE CHECKOUT: EXIGE CARRITO NO VACÍO Y PASA CLAVE PÚBLICA DE STRIPE A LA VISTA.
     */
    public function checkout()
    {
        $items = $this->cartService->getItems();
        if (empty($items)) {
            return redirect()->to('/productos')->with('error', 'Tu carrito está vacío.');
        }

        $stripeReady = StripeConfig::paymentsReady();

        return view('web/cart/checkout', [
            'title'            => 'Checkout',
            'items'            => $items,
            'totals'           => $this->cartService->getTotals(),
            'coupon'           => session()->get('applied_coupon'),
            'stripePublicKey'  => $stripeReady ? StripeConfig::publicKey() : '',
        ]);
    }

    /**
     * CREA EL PEDIDO EN TRANSACCIÓN, DESCUENTA STOCK, REGISTRA CUPÓN Y CREA PAYMENT INTENT EN STRIPE (RESPUESTA JSON).
     */
    public function processCheckout()
    {
        $items = $this->cartService->getItems();
        if (empty($items)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tu carrito está vacío.']);
        }

        // VALIDAR DATOS DE ENVÍO OBLIGATORIOS
        $validation = service('validation');
        $validation->setRules([
            'shipping_name'        => 'required|max_length[200]',
            'shipping_address'     => 'required|max_length[500]',
            'shipping_city'        => 'required|max_length[120]',
            'shipping_postal_code' => 'required|max_length[20]',
            'shipping_country'     => 'required|max_length[120]',
            'shipping_phone'       => 'required|max_length[40]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Revisa los datos de envío.',
                'errors'  => $validation->getErrors(),
            ]);
        }

        // VERIFICAR QUE CADA PRODUCTO SIGA ACTIVO Y HAYA STOCK SUFICIENTE (VARIANTE O PRODUCTO BASE)
        $productModel = new ProductModel();
        $variantModel = new ProductVariantModel();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($items as $item) {
            $product = $productModel->find($item['product_id']);
            if (!$product || !$product['is_active']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El producto "' . ($item['name'] ?? '') . '" ya no está disponible.',
                ]);
            }
            if ($item['variant_id']) {
                $variant = $variantModel->find($item['variant_id']);
                $stock = $variant ? (int) ($variant['stock'] ?? 0) : 0;
            } else {
                $stock = (int) ($product['stock'] ?? 0);
            }
            if ($stock < $item['quantity']) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Stock insuficiente para "' . ($item['name'] ?? '') . '".',
                ]);
            }
        }

        $totals = $this->cartService->getTotals();
        $userId = (int) session()->get('user_id');
        $coupon = session()->get('applied_coupon');

        $db = Database::connect();
        $db->transStart();

        // INSERTAR CABECERA DEL PEDIDO CON NÚMERO ÚNICO
        $orderModel = new OrderModel();
        $orderNumber = 'NMZ-' . strtoupper(substr(md5(uniqid((string) mt_rand(), true)), 0, 8));

        $orderId = $orderModel->insert([
            'user_id'              => $userId,
            'order_number'         => $orderNumber,
            'status'               => 'pending',
            'payment_status'       => 'pending',
            'subtotal'             => $totals['subtotal'],
            'shipping_cost'        => $totals['shipping'],
            'tax'                  => $totals['tax'],
            'discount'             => $totals['discount'],
            'total'                => $totals['total'],
            'coupon_code'          => $coupon['code'] ?? null,
            'shipping_name'        => $this->request->getVar('shipping_name'),
            'shipping_address'     => $this->request->getVar('shipping_address'),
            'shipping_city'        => $this->request->getVar('shipping_city'),
            'shipping_postal_code' => $this->request->getVar('shipping_postal_code'),
            'shipping_country'     => $this->request->getVar('shipping_country'),
            'shipping_phone'       => $this->request->getVar('shipping_phone'),
        ], true);

        if (!$orderId) {
            $db->transRollback();
            return $this->response->setJSON(['success' => false, 'message' => 'No se pudo crear el pedido.']);
        }

        // LÍNEAS DE PEDIDO Y DECREMENTO DE STOCK
        $orderItemModel = new OrderItemModel();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($items as $item) {
            $orderItemModel->insert([
                'order_id'           => (int) $orderId,
                'product_id'         => (int) $item['product_id'],
                'product_variant_id' => $item['variant_id'],
                'product_name'       => $item['name'],
                'variant_info'       => $item['variant_name'] ?? null,
                'quantity'           => (int) $item['quantity'],
                'unit_price'         => (float) $item['price'],
                'total_price'        => round($item['price'] * $item['quantity'], 2),
            ]);

            if ($item['variant_id']) {
                $variantModel->set('stock', 'stock - ' . (int) $item['quantity'], false)
                    ->where('id', $item['variant_id'])->update();
            } else {
                $productModel->set('stock', 'stock - ' . (int) $item['quantity'], false)
                    ->where('id', $item['product_id'])->update();
            }
        }

        // INCREMENTAR CONTADOR DE USOS DEL CUPÓN SI HABÍA CUPÓN APLICADO
        if ($coupon) {
            $couponModel = new CouponModel();
            $couponModel->set('used_count', 'used_count + 1', false)
                ->where('id', $coupon['id'])->update();
        }

        // CREAR PAYMENT INTENT EN STRIPE (SI ESTÁ CONFIGURADO) Y GUARDAR ID EN EL PEDIDO
        $clientSecret = null;
        $stripeService = new StripeService();

        if ($stripeService->isConfigured()) {
            // INICIO DE BLOQUE TRY
            try {
                $paymentIntent = $stripeService->createPaymentIntent(
                    $totals['total'],
                    'eur',
                    [
                        'order_id'     => $orderId,
                        'order_number' => $orderNumber,
                        'order_type'   => 'product',
                    ]
                );

                $orderModel->update($orderId, [
                    'stripe_payment_id' => $paymentIntent->id,
                ]);

                $clientSecret = $paymentIntent->client_secret;
            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', 'Stripe PaymentIntent creation failed: ' . $e->getMessage());
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al procesar el pago. Intenta de nuevo.',
                ]);
            }
        } else {
            log_message('info', "Pedido {$orderNumber} creado sin Stripe (pasarela no configurada).");
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al procesar el pedido.']);
        }

        $this->cartService->clear();

        $response = [
            'success'      => true,
            'order_number' => $orderNumber,
        ];
        if ($clientSecret) {
            $response['client_secret'] = $clientSecret;
        }

        return $this->response->setJSON($response);
    }

    /**
     * PÁGINA DE ÉXITO POST-PAGO: OPCIONALMENTE CARGA EL PEDIDO POR NÚMERO EN QUERY STRING.
     */
    public function success()
    {
        $orderNumber = $this->request->getGet('order');
        $order = null;

        if ($orderNumber) {
            $orderModel = new OrderModel();
            $order = $orderModel->where('order_number', $orderNumber)->first();
        }

        return view('web/cart/success', [
            'title' => 'Pedido Confirmado',
            'order' => $order,
        ]);
    }
}