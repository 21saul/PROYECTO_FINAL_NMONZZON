<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * COUPONADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/COUPONADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CouponModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class CouponAdminController extends BaseController
{
    protected CouponModel $coupons;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->coupons = model(CouponModel::class);
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

        return view('admin/coupons/index', [
            'title'   => 'Coupons',
            'coupons' => $this->coupons->orderBy('created_at', 'DESC')->findAll(),
        ]);
    }

    public function new()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        return view('admin/coupons/form', ['title' => 'New coupon', 'coupon' => null]);
    }

    public function create()
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $rules = [
            'code'         => 'required|max_length[50]|is_unique[coupons.code]',
            'type'         => 'required|in_list[percentage,fixed]',
            'value'        => 'required|decimal',
            'valid_from'   => 'permit_empty|valid_date',
            'valid_until'  => 'permit_empty|valid_date',
            'min_purchase' => 'permit_empty|decimal',
            'max_uses'     => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $row = [
            'code'         => strtoupper(trim((string) $this->request->getPost('code'))),
            'type'         => (string) $this->request->getPost('type'),
            'value'        => (string) $this->request->getPost('value'),
            'min_purchase' => $this->request->getPost('min_purchase') !== null && (string) $this->request->getPost('min_purchase') !== ''
                ? (string) $this->request->getPost('min_purchase') : null,
            'max_uses'     => $this->request->getPost('max_uses') !== null && $this->request->getPost('max_uses') !== ''
                ? (int) $this->request->getPost('max_uses') : null,
            'used_count'   => 0,
            'valid_from'   => $this->request->getPost('valid_from') !== null && trim((string) $this->request->getPost('valid_from')) !== ''
                ? (string) $this->request->getPost('valid_from') : null,
            'valid_until'  => $this->request->getPost('valid_until') !== null && trim((string) $this->request->getPost('valid_until')) !== ''
                ? (string) $this->request->getPost('valid_until') : null,
            'is_active'    => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
        ];

        if ($this->coupons->insert($row) === false) {
            session()->setFlashdata('error', 'Could not create coupon.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Coupon created.');

        return redirect()->to('/admin/coupons');
    }

    public function edit(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $coupon = $this->coupons->find($id);
        if ($coupon === null) {
            session()->setFlashdata('error', 'Coupon not found.');

            return redirect()->to('/admin/coupons');
        }

        return view('admin/coupons/form', [
            'title'  => 'Edit coupon',
            'coupon' => $coupon,
        ]);
    }

    public function update(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->coupons->find($id) === null) {
            session()->setFlashdata('error', 'Coupon not found.');

            return redirect()->to('/admin/coupons');
        }

        $rules = [
            'code'         => "required|max_length[50]|is_unique[coupons.code,id,{$id}]",
            'type'         => 'required|in_list[percentage,fixed]',
            'value'        => 'required|decimal',
            'valid_from'   => 'permit_empty|valid_date',
            'valid_until'  => 'permit_empty|valid_date',
            'min_purchase' => 'permit_empty|decimal',
            'max_uses'     => 'permit_empty|integer',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Please fix the errors below.');

            return redirect()->back()->withInput();
        }

        $row = [
            'code'         => strtoupper(trim((string) $this->request->getPost('code'))),
            'type'         => (string) $this->request->getPost('type'),
            'value'        => (string) $this->request->getPost('value'),
            'min_purchase' => $this->request->getPost('min_purchase') !== null && (string) $this->request->getPost('min_purchase') !== ''
                ? (string) $this->request->getPost('min_purchase') : null,
            'max_uses'     => $this->request->getPost('max_uses') !== null && $this->request->getPost('max_uses') !== ''
                ? (int) $this->request->getPost('max_uses') : null,
            'valid_from'   => $this->request->getPost('valid_from') !== null && trim((string) $this->request->getPost('valid_from')) !== ''
                ? (string) $this->request->getPost('valid_from') : null,
            'valid_until'  => $this->request->getPost('valid_until') !== null && trim((string) $this->request->getPost('valid_until')) !== ''
                ? (string) $this->request->getPost('valid_until') : null,
            'is_active'    => (int) ($this->request->getPost('is_active') !== '0' ? 1 : 0),
        ];

        if (! $this->coupons->update($id, $row)) {
            session()->setFlashdata('error', 'Could not update coupon.');

            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Coupon updated.');

        return redirect()->to('/admin/coupons');
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->coupons->delete($id)) {
            session()->setFlashdata('success', 'Coupon removed.');
        } else {
            session()->setFlashdata('error', 'Could not remove coupon.');
        }

        return redirect()->to('/admin/coupons');
    }

    public function toggleActive(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400);
        }

        if ($this->coupons->find($id) === null) {
            return $this->response->setJSON(['success' => false]);
        }

        $value = (int) $this->request->getPost('value');
        $ok    = $this->coupons->skipValidation(true)->update($id, ['is_active' => $value ? 1 : 0]);

        return $this->response->setJSON(['success' => $ok]);
    }
}