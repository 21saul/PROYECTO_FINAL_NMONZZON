<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * SETTINGSCONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/SETTINGSCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use App\Models\SiteSettingModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class SettingsController extends BaseApiController
{
    private const SENSITIVE_KEYS = [
        'stripe_secret_key',
        'cloudinary_api_secret',
    ];

    protected SiteSettingModel $settingModel;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper('api');

        $this->settingModel = model(SiteSettingModel::class);
    }

    public function show($key = null): ResponseInterface
    {
        if ($key === null || $key === '') {
            return apiError('Key is required', 400);
        }

        $key = (string) $key;
        if (in_array($key, self::SENSITIVE_KEYS, true)) {
            return apiError('Not found', 404);
        }

        $row = $this->settingModel->where('key', $key)->first();
        if ($row === null) {
            return apiError('Not found', 404);
        }

        return apiResponse([
            'key'   => $row['key'],
            'value' => $row['value'],
            'type'  => $row['type'] ?? 'text',
            'group' => $row['group'] ?? 'general',
        ]);
    }

    public function index(): ResponseInterface
    {
        if (! $this->getUserId()) {
            return apiError('Unauthorized', 401);
        }
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $rows = $this->settingModel->orderBy('group', 'ASC')->orderBy('key', 'ASC')->findAll();

        $grouped = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $row) {
            $g = (string) ($row['group'] ?? 'general');
            if (! isset($grouped[$g])) {
                $grouped[$g] = [];
            }
            $grouped[$g][] = $row;
        }

        return apiResponse($grouped);
    }

    public function update($id = null): ResponseInterface
    {
        if (! $this->getUserId()) {
            return apiError('Unauthorized', 401);
        }
        if (! $this->isAdmin()) {
            return apiError('Forbidden', 403);
        }

        $body = $this->request->getJSON(true);
        if (! is_array($body) || $body === []) {
            return apiError('JSON object with key-value pairs required', 422);
        }

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($body as $k => $v) {
            if (! is_string($k) || $k === '') {
                return apiError('Invalid setting key', 422);
            }
            $value = is_scalar($v) || $v === null ? (string) $v : json_encode($v);
            if (! $this->settingModel->setValue($k, $value)) {
                return apiError('Could not update setting: ' . $k, 500);
            }
        }

        return apiResponse(null, 200, 'Settings updated');
    }
}