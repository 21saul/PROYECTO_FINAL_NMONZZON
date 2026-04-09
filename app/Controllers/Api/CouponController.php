<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * COUPONCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/COUPONCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

// API DE CUPONES: VALIDACIÓN PARA USUARIO AUTENTICADO Y ABM COMPLETO PARA ADMINISTRADORES.
namespace App\Controllers\Api;

use App\Models\CouponModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CouponController extends BaseApiController
{
    protected CouponModel $couponModel;

    // CARGA EL MODELO DE CUPONES Y EL HELPER API.
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->couponModel = model(CouponModel::class);
    }

    // POST: VALIDA CÓDIGO Y SUBTOTAL DEVOLVIENDO DESCUENTO E IMPORTE FINAL (USUARIO LOGUEADO).
    public function validateCoupon(): ResponseInterface
    {
        if (! $this->getUserId()) {
            return apiError('Unauthorized', 401);
        }

        $rules = [
            'code'     => 'required|max_length[50]',
            'subtotal' => 'required|decimal',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $code    = trim((string) $validated['code']);
        $subtotal = (float) $validated['subtotal'];

        $coupon = $this->couponModel->getByCode($code);
        if ($coupon === null) {
            return apiError('Coupon not found', 422);
        }

        if (! $this->couponModel->isValid($code)) {
            return apiError('Coupon is not valid', 422);
        }

        $minPurchase = (float) ($coupon['min_purchase'] ?? 0);
        if ($subtotal < $minPurchase) {
            return apiError('Subtotal below minimum purchase for this coupon', 422);
        }

        $type = (string) ($coupon['type'] ?? '');
        $val  = (float) ($coupon['value'] ?? 0);
        if ($type === 'percentage') {
            $discount = round($subtotal * ($val / 100), 2);
        } else {
            $discount = round(min($val, $subtotal), 2);
        }

        $finalPrice = round(max(0.0, $subtotal - $discount), 2);

        return apiResponse([
            'discount_amount' => $discount,
            'final_price'     => $finalPrice,
            'coupon'          => [
                'code' => $coupon['code'],
                'type' => $coupon['type'],
            ],
        ]);
    }

    // GET: LISTA TODOS LOS CUPONES ORDENADOS POR FECHA DE CREACIÓN (ADMIN).
    public function index(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        return apiResponse($this->couponModel->orderBy('created_at', 'DESC')->findAll());
    }

    // POST: CREA UN CUPÓN NUEVO CON REGLAS DE VALIDACIÓN (ADMIN).
    public function create(): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rules = [
            'code'         => 'required|max_length[50]|is_unique[coupons.code]',
            'type'         => 'required|in_list[percentage,fixed]',
            'value'        => 'required|decimal',
            'min_purchase' => 'permit_empty|decimal',
            'max_uses'     => 'permit_empty|integer',
            'valid_from'   => 'permit_empty|valid_date',
            'valid_until'  => 'permit_empty|valid_date',
            'is_active'    => 'permit_empty|in_list[0,1]',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [
            'code'         => strtoupper(trim((string) $validated['code'])),
            'type'         => (string) $validated['type'],
            'value'        => (string) $validated['value'],
            'min_purchase' => isset($validated['min_purchase']) ? (string) $validated['min_purchase'] : '0',
            'max_uses'     => $validated['max_uses'] ?? null,
            'valid_from'   => $validated['valid_from'] ?? null,
            'valid_until'  => $validated['valid_until'] ?? null,
            'is_active'    => isset($validated['is_active']) ? (int) $validated['is_active'] : 1,
            'used_count'   => 0,
        ];

        $id = $this->couponModel->insert($data);
        if ($id === false) {
            return apiError('Could not create coupon', 500);
        }

        return apiResponse($this->couponModel->find($id), 201, 'Created');
    }

    // PUT/PATCH: ACTUALIZA CAMPOS PERMITIDOS DE UN CUPÓN POR ID (ADMIN).
    public function update($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        $existing = $this->couponModel->find((int) $id);
        if ($existing === null) {
            return apiError('Not found', 404);
        }

        $rules = [
            'code'         => "permit_empty|max_length[50]|is_unique[coupons.code,id,{$id}]",
            'type'         => 'permit_empty|in_list[percentage,fixed]',
            'value'        => 'permit_empty|decimal',
            'min_purchase' => 'permit_empty|decimal',
            'max_uses'     => 'permit_empty|integer',
            'valid_from'   => 'permit_empty|valid_date',
            'valid_until'  => 'permit_empty|valid_date',
            'is_active'    => 'permit_empty|in_list[0,1]',
        ];
        $validated = $this->validateRequest($rules);
        if ($validated === false) {
            return apiError('Validation failed', 422, $this->validator->getErrors());
        }

        $data = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach (['code', 'type', 'value', 'min_purchase', 'max_uses', 'valid_from', 'valid_until', 'is_active'] as $field) {
            if (array_key_exists($field, $validated)) {
                if ($field === 'code') {
                    $data[$field] = strtoupper(trim((string) $validated[$field]));
                } elseif ($field === 'is_active') {
                    $data[$field] = (int) $validated[$field];
                } else {
                    $data[$field] = $validated[$field];
                }
            }
        }

        if ($data === []) {
            return apiResponse($existing);
        }

        if (! $this->couponModel->update((int) $id, $data)) {
            return apiError('Could not update coupon', 500);
        }

        return apiResponse($this->couponModel->find((int) $id), 200, 'Updated');
    }

    // DELETE: ELIMINA UN CUPÓN POR ID (ADMIN).
    public function delete($id = null): ResponseInterface
    {
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }
        if ($id === null || $id === '') {
            return apiError('Not found', 404);
        }

        if ($this->couponModel->delete((int) $id) === false) {
            return apiError('Not found', 404);
        }

        return apiResponse(null, 200, 'Deleted');
    }
}