<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * SETTINGSADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/SETTINGSADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SiteSettingModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class SettingsAdminController extends BaseController
{
    private const SENSITIVE_NO_OVERWRITE_IF_EMPTY = [
        'stripe_secret_key',
        'cloudinary_api_secret',
    ];

    protected SiteSettingModel $settings;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->settings = model(SiteSettingModel::class);
    }

    protected function requireAdminSession(): ?ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/admin/login');
        }

        return null;
    }

    public function index()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rows = $this->settings->orderBy('group', 'ASC')->orderBy('key', 'ASC')->findAll();

        $grouped  = [];
        $settings = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($rows as $row) {
            $group = (string) ($row['group'] ?? 'general');
            if (! isset($grouped[$group])) {
                $grouped[$group] = [];
            }
            $grouped[$group][] = $row;
            $settings[(string) $row['key']] = $row['value'];
        }

        return view('admin/settings/index', [
            'title'    => 'Site settings',
            'grouped'  => $grouped,
            'settings' => $settings,
        ]);
    }

    public function update()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $pairs = $this->request->getPost('settings');
        if (! is_array($pairs)) {
            session()->setFlashdata('error', 'Invalid form data.');

            return redirect()->back();
        }

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($pairs as $key => $value) {
            $key = (string) $key;
            if ($key === '') {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $existing = $this->settings->where('key', $key)->first();
            if ($existing === null) {
                // SALTA A LA SIGUIENTE ITERACIÓN
                continue;
            }

            $stored = is_array($value) ? json_encode($value) : (string) $value;

            if (in_array($key, self::SENSITIVE_NO_OVERWRITE_IF_EMPTY, true) && trim($stored) === '') {
                continue;
            }

            $this->settings->skipValidation(true)->update((int) $existing['id'], [
                'value' => $stored,
            ]);
        }

        session()->setFlashdata('success', 'Settings saved.');

        return redirect()->to('/admin/settings');
    }
}