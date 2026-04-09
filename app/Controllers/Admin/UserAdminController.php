<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * USERADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/USERADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OrderModel;
use App\Models\PortraitOrderModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class UserAdminController extends BaseController
{
    protected UserModel $users;
    protected OrderModel $orders;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->users  = model(UserModel::class);
        $this->orders = model(OrderModel::class);
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

        $query = $this->users->orderBy('created_at', 'DESC');

        $role = $this->request->getGet('role');
        if ($role !== null && $role !== '' && in_array($role, ['admin', 'client'], true)) {
            $query = $query->where('role', $role);
        }

        return view('admin/users/index', [
            'title'   => 'Users',
            'users'   => $query->findAll(),
            'filters' => ['role' => $role],
        ]);
    }

    public function show(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $user = $this->users->find($id);
        if ($user === null) {
            session()->setFlashdata('error', 'User not found.');

            return redirect()->to('/admin/users');
        }

        $orderRows      = $this->orders->where('user_id', $id)->orderBy('created_at', 'DESC')->findAll();
        $portraitOrders = model(PortraitOrderModel::class)->where('user_id', $id)->orderBy('created_at', 'DESC')->findAll();

        return view('admin/users/show', [
            'title'          => 'User',
            'user'           => $user,
            'orders'         => $orderRows,
            'orderHistory'   => $orderRows,
            'portraitOrders' => $portraitOrders,
        ]);
    }

    public function toggleActive(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $user = $this->users->find($id);
        if ($user === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false]);
            }
            session()->setFlashdata('error', 'User not found.');

            return redirect()->back();
        }

        if ($this->request->isAJAX()) {
            $value = (int) $this->request->getPost('value');
            $ok    = $this->users->skipValidation(true)->update($id, ['is_active' => $value ? 1 : 0]);

            return $this->response->setJSON(['success' => $ok]);
        }

        $next = (int) ($user['is_active'] ?? 0) === 1 ? 0 : 1;

        if ($this->users->skipValidation(true)->update($id, ['is_active' => $next])) {
            session()->setFlashdata('success', $next === 1 ? 'User activated.' : 'User deactivated.');
        } else {
            session()->setFlashdata('error', 'Could not update user.');
        }

        return redirect()->back();
    }
}