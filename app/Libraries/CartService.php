<?php

// INSTRUCCIÓN O DECLARACIÓN PHP
/*
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * SERVICIO DE CARRITO DE COMPRA BASADO EN SESIÓN.
 // LÍNEA DE DOCUMENTACIÓN EN BLOQUE
 * AÑADE PRODUCTOS CON VARIANTES, CALCULA SUBTOTAL, ENVÍO, IVA, DESCUENTOS POR CUPÓN Y TOTALES.
 // CIERRE DE BLOQUE DE DOCUMENTACIÓN
 */

// DECLARA EL ESPACIO DE NOMBRES
namespace App\Libraries;

// DECLARA UNA CLASE
class CartService
// DELIMITADOR DE BLOQUE
{
    // COMENTARIO DE LÍNEA EXISTENTE
    // INSTANCIA DE SESIÓN DE CODEIGNITER DONDE SE PERSISTE EL ARRAY DEL CARRITO
    // DECLARA PROPIEDAD O CONSTANTE DE CLASE
    private $session;

    // COMENTARIO DE LÍNEA EXISTENTE
    // OBTIENE EL SERVICIO DE SESIÓN PARA LEER Y ESCRIBIR CLAVES "CART" Y CUPONES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function __construct()
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $this->session = session();
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DEVUELVE EL ARRAY DEL CARRITO O UN ARRAY VACÍO SI AÚN NO EXISTE EN SESIÓN
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getItems(): array
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return $this->session->get('cart') ?? [];
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // AÑADE O INCREMENTA CANTIDAD DE UN PRODUCTO (OPCIONALMENTE CON VARIANTE); VALIDA STOCK Y ACTIVO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $productModel = model('ProductModel');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = $productModel->find($productId);
        // CONDICIONAL SI
        if (!$product || !$product['is_active']) {
            // LANZA UNA EXCEPCIÓN
            throw new \RuntimeException('Producto no disponible.');
        // DELIMITADOR DE BLOQUE
        }

        // SIN variant_id (GRID / API): PRIMERA VARIANTE ACTIVA CON STOCK > 0; SI NO HAY, LÍNEA SIN VARIANTE (STOCK PADRE)
        // CONDICIONAL SI
        if ($variantId === null) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $variantId = self::firstActiveVariantWithStockId($productId);
        // DELIMITADOR DE BLOQUE
        }

        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = $this->getItems();
        // COMENTARIO DE LÍNEA EXISTENTE
        // CLAVE ÚNICA POR PRODUCTO + VARIANTE (0 SI NO HAY VARIANTE)
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $key = $productId . '_' . ($variantId ?? '0');

        // COMENTARIO DE LÍNEA EXISTENTE
        // SI LA LÍNEA YA EXISTE, SOLO SUMA CANTIDAD SIN VOLVER A CARGAR EL PRODUCTO
        // CONDICIONAL SI
        if (isset($cart[$key])) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $cart[$key]['quantity'] += $quantity;
        // INSTRUCCIÓN O DECLARACIÓN PHP
        } else {
            // COMENTARIO DE LÍNEA EXISTENTE
            // PRECIO BASE DEL PRODUCTO; SE AJUSTA SI HAY VARIANTE CON MODIFICADOR
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $price = (float) $product['price'];
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $variantName = null;

            // CONDICIONAL SI
            if ($variantId) {
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $variant = model('ProductVariantModel')->find($variantId);
                // CONDICIONAL SI
                if ($variant) {
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    $price += (float) $variant['price_modifier'];
                    // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                    $variantName = $variant['variant_name'] . ': ' . $variant['variant_value'];
                // DELIMITADOR DE BLOQUE
                }
            // DELIMITADOR DE BLOQUE
            }

            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $maxForNew = $this->getAvailableStock($productId, $variantId);
            // CONDICIONAL SI
            if ($maxForNew <= 0) {
                // LANZA UNA EXCEPCIÓN
                throw new \RuntimeException('Producto sin stock disponible.');
            // DELIMITADOR DE BLOQUE
            }

            // COMENTARIO DE LÍNEA EXISTENTE
            // GUARDA METADATOS DE LÍNEA PARA VISTA Y CHECKOUT (IMAGEN, SLUG, STOCK MÁXIMO)
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $cart[$key] = [
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'product_id'   => $productId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'variant_id'   => $variantId,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'name'         => $product['name'],
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'variant_name' => $variantName,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'price'        => $price,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'quantity'     => $quantity,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'image'        => $product['featured_image'] ?? $product['image'] ?? null,
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'slug'         => $product['slug'] ?? '',
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                'max_stock'    => $maxForNew,
            // INSTRUCCIÓN O DECLARACIÓN PHP
            ];
        // DELIMITADOR DE BLOQUE
        }

        // COMENTARIO DE LÍNEA EXISTENTE
        // NO PERMITIR SUPERAR EL STOCK DISPONIBLE TRAS LA SUMA
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $maxStock = $this->getAvailableStock($productId, $variantId);
        // CONDICIONAL SI
        if ($cart[$key]['quantity'] > $maxStock) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $cart[$key]['quantity'] = $maxStock;
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($cart[$key]['quantity'] <= 0) {
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            unset($cart[$key]);
        // DELIMITADOR DE BLOQUE
        }

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->set('cart', $cart);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ACTUALIZA LA CANTIDAD DE UNA LÍNEA POR SU CLAVE; SI ES <= 0 ELIMINA LA LÍNEA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function updateQuantity(string $key, int $quantity): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = $this->getItems();
        // CONDICIONAL SI
        if (isset($cart[$key])) {
            // CONDICIONAL SI
            if ($quantity <= 0) {
                // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                unset($cart[$key]);
            // INSTRUCCIÓN O DECLARACIÓN PHP
            } else {
                // COMENTARIO DE LÍNEA EXISTENTE
                // RESPETA EL MÁXIMO GUARDADO EN LA LÍNEA (STOCK AL MOMENTO DE AÑADIR)
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $maxStock = $cart[$key]['max_stock'] ?? 999;
                // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
                $cart[$key]['quantity'] = min($quantity, $maxStock);
            // DELIMITADOR DE BLOQUE
            }
            // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
            $this->session->set('cart', $cart);
        // DELIMITADOR DE BLOQUE
        }
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ELIMINA UNA LÍNEA DEL CARRITO POR CLAVE Y PERSISTE LA SESIÓN
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function removeItem(string $key): void
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $cart = $this->getItems();
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        unset($cart[$key]);
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->set('cart', $cart);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // SUMA PRECIO * CANTIDAD DE TODAS LAS LÍNEAS Y REDONDEA A DOS DECIMALES
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getSubtotal(): float
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $subtotal = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($this->getItems() as $item) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $subtotal += $item['price'] * $item['quantity'];
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return round($subtotal, 2);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // ENVÍO GRATUITO SI EL SUBTOTAL ALCANZA UMBRAL; SI NO, TARIFA FIJA
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getShippingCost(): float
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if ($this->getSubtotal() >= 50) {
            // RETORNA UN VALOR AL LLAMADOR
            return 0;
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return 4.95;
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // IVA DEL 21% SOBRE EL SUBTOTAL (REGLA DE NEGOCIO FIJA)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getTax(): float
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return round($this->getSubtotal() * 0.21, 2);
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DESCUENTO SEGÚN CUPÓN EN SESIÓN: PORCENTAJE SOBRE SUBTOTAL O IMPORTE FIJO ACOTADO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getDiscount(): float
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $coupon = $this->session->get('applied_coupon');
        // CONDICIONAL SI
        if (!$coupon) {
            // RETORNA UN VALOR AL LLAMADOR
            return 0;
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($coupon['type'] === 'percentage') {
            // RETORNA UN VALOR AL LLAMADOR
            return round($this->getSubtotal() * ($coupon['value'] / 100), 2);
        // DELIMITADOR DE BLOQUE
        }
        // COMENTARIO DE LÍNEA EXISTENTE
        // IMPORTE FIJO NO PUEDE SUPERAR EL SUBTOTAL
        // RETORNA UN VALOR AL LLAMADOR
        return min((float) $coupon['value'], $this->getSubtotal());
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // TOTAL FINAL: SUBTOTAL + ENVÍO + IMPUESTOS - DESCUENTO
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getTotal(): float
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return round(
            // INSTRUCCIÓN O DECLARACIÓN PHP
            $this->getSubtotal() + $this->getShippingCost() + $this->getTax() - $this->getDiscount(),
            // INSTRUCCIÓN O DECLARACIÓN PHP
            2
        // INSTRUCCIÓN O DECLARACIÓN PHP
        );
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // NÚMERO TOTAL DE UNIDADES (SUMA DE CANTIDADES, NO DE LÍNEAS)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getItemCount(): int
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $count = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($this->getItems() as $item) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $count += $item['quantity'];
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return $count;
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // VALIDA Y APLICA UN CUPÓN POR CÓDIGO; DEVUELVE ARRAY CON ÉXITO O MENSAJE DE ERROR
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function applyCoupon(string $code): array
    // DELIMITADOR DE BLOQUE
    {
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $couponModel = model('CouponModel');
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $coupon = $couponModel->where('code', strtoupper(trim($code)))
                              // INSTRUCCIÓN O DECLARACIÓN PHP
                              ->where('is_active', 1)
                              // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
                              ->first();

        // CONDICIONAL SI
        if (!$coupon) {
            // RETORNA UN VALOR AL LLAMADOR
            return ['success' => false, 'message' => 'Cupón no válido.'];
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($coupon['valid_until'] && strtotime($coupon['valid_until']) < time()) {
            // RETORNA UN VALOR AL LLAMADOR
            return ['success' => false, 'message' => 'Este cupón ha expirado.'];
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($coupon['max_uses'] && $coupon['used_count'] >= $coupon['max_uses']) {
            // RETORNA UN VALOR AL LLAMADOR
            return ['success' => false, 'message' => 'Este cupón ya ha sido usado el máximo de veces.'];
        // DELIMITADOR DE BLOQUE
        }

        // CONDICIONAL SI
        if ($this->getSubtotal() < (float) ($coupon['min_purchase'] ?? 0)) {
            // RETORNA UN VALOR AL LLAMADOR
            return ['success' => false, 'message' => 'El mínimo de compra para este cupón es ' . number_format($coupon['min_purchase'], 2) . ' €.'];
        // DELIMITADOR DE BLOQUE
        }

        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->set('applied_coupon', $coupon);
        // RETORNA UN VALOR AL LLAMADOR
        return ['success' => true, 'message' => 'Cupón aplicado correctamente.', 'coupon' => $coupon];
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // QUITA EL CUPÓN APLICADO DE LA SESIÓN
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function removeCoupon(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->remove('applied_coupon');
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // VACÍA EL CARRITO Y EL CUPÓN (P. EJ. TRAS PEDIDO COMPLETADO)
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function clear(): void
    // DELIMITADOR DE BLOQUE
    {
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->remove('cart');
        // LLAMADA O INSTRUCCIÓN TERMINADA EN PUNTO Y COMA
        $this->session->remove('applied_coupon');
    // DELIMITADOR DE BLOQUE
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // DEVUELVE TODOS LOS IMPORTES AGREGADOS EN UN SOLO ARRAY PARA VISTAS O API
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    public function getTotals(): array
    // DELIMITADOR DE BLOQUE
    {
        // RETORNA UN VALOR AL LLAMADOR
        return [
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'subtotal' => $this->getSubtotal(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'shipping' => $this->getShippingCost(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'tax'      => $this->getTax(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'discount' => $this->getDiscount(),
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            'total'    => $this->getTotal(),
        // INSTRUCCIÓN O DECLARACIÓN PHP
        ];
    // DELIMITADOR DE BLOQUE
    }

    /**
     * Primera variante activa con stock positivo (id ASC), o null para usar inventario del producto padre.
     */
    private static function firstActiveVariantWithStockId(int $productId): ?int
    {
        $variantRow = model('ProductVariantModel')
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->where('stock >', 0)
            ->orderBy('id', 'ASC')
            ->first();

        return $variantRow !== null ? (int) $variantRow['id'] : null;
    }

    /**
     * Stock vendible con la misma regla que addItem sin variant_id (grid / añadido rápido).
     */
    public static function catalogQuickAddStock(int $productId): int
    {
        $product = model('ProductModel')->find($productId);
        if ($product === null || empty($product['is_active'])) {
            return 0;
        }

        $vid = self::firstActiveVariantWithStockId($productId);
        if ($vid !== null) {
            $variant = model('ProductVariantModel')->find($vid);

            return $variant ? (int) ($variant['stock'] ?? 0) : 0;
        }

        return (int) ($product['stock'] ?? 0);
    }

    // COMENTARIO DE LÍNEA EXISTENTE
    // STOCK DISPONIBLE POR VARIANTE O POR PRODUCTO BASE; VALOR POR DEFECTO ALTO SI NO VIENE EN BD
    // DECLARA O FIRMA DE MÉTODO O FUNCIÓN
    private function getAvailableStock(int $productId, ?int $variantId): int
    // DELIMITADOR DE BLOQUE
    {
        // CONDICIONAL SI
        if ($variantId) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $variant = model('ProductVariantModel')->find($variantId);
            // RETORNA UN VALOR AL LLAMADOR
            return $variant ? (int) ($variant['stock'] ?? 999) : 0;
        // DELIMITADOR DE BLOQUE
        }
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $product = model('ProductModel')->find($productId);
        // CONDICIONAL SI
        if (!$product) {
            // RETORNA UN VALOR AL LLAMADOR
            return 0;
        // DELIMITADOR DE BLOQUE
        }
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $base = (int) ($product['stock'] ?? 0);
        // CONDICIONAL SI
        if ($base > 0) {
            // RETORNA UN VALOR AL LLAMADOR
            return $base;
        // DELIMITADOR DE BLOQUE
        }
        // STOCK SOLO EN VARIANTES (PADRE A 0)
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $variants = model('ProductVariantModel')
            ->where('product_id', $productId)
            ->where('is_active', 1)
            ->findAll();
        // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
        $sum = 0;
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($variants as $v) {
            // ASIGNACIÓN O OPERACIÓN CON ASIGNACIÓN
            $sum += (int) ($v['stock'] ?? 0);
        // DELIMITADOR DE BLOQUE
        }
        // RETORNA UN VALOR AL LLAMADOR
        return $sum > 0 ? $sum : $base;
    // DELIMITADOR DE BLOQUE
    }
// DELIMITADOR DE BLOQUE
}
