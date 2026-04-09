<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * ADMINLOGINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/ADMINLOGINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class AdminLoginController extends BaseController
{
    protected UserModel $users;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->users = model(UserModel::class);
    }

    public function login()
    {
        $session = session();
        if ($session->get('isLoggedIn') && $session->get('role') === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/login', ['title' => 'Admin login']);
    }

    public function processLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            session()->setFlashdata('error', 'Indica un email y una contraseña válidos.');

            return redirect()->back()->withInput();
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $user = $this->users->getByEmail($email);
        if ($user === null) {
            session()->setFlashdata('error', 'Credenciales incorrectas.');

            return redirect()->back()->withInput();
        }

        if (($user['role'] ?? '') !== 'admin') {
            session()->setFlashdata('error', 'Credenciales incorrectas.');

            return redirect()->back()->withInput();
        }

        if ((int) ($user['is_active'] ?? 0) !== 1) {
            session()->setFlashdata('error', 'Esta cuenta está inactiva.');

            return redirect()->back()->withInput();
        }

        if ($this->users->isLocked($user)) {
            session()->setFlashdata('error', 'Cuenta bloqueada temporalmente. Inténtalo más tarde.');

            return redirect()->back()->withInput();
        }

        if (! password_verify($password, (string) ($user['password'] ?? ''))) {
            $this->users->incrementFailedAttempts((int) $user['id']);
            session()->setFlashdata('error', 'Credenciales incorrectas.');

            return redirect()->back()->withInput();
        }

        $this->users->resetFailedAttempts((int) $user['id']);
        $this->users->skipValidation(true)->update((int) $user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->request->getIPAddress(),
        ]);
        $this->users->skipValidation(false);

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'name'       => (string) ($user['name'] ?? ''),
            'user_name'  => (string) ($user['name'] ?? ''),
            'email'      => (string) ($user['email'] ?? ''),
            'role'       => 'admin',
            'avatar'     => (string) ($user['avatar'] ?? ''),
        ]);

        session()->setFlashdata('success', 'Sesión iniciada correctamente.');

        return redirect()->to('/admin/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/admin/login');
    }
}