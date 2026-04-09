<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * AUTHWEBCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/AUTHWEBCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\UserModel;

class AuthWebController extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('web/auth/login', [
            'title' => 'Iniciar sesión',
        ]);
    }

    public function processLogin()
    {
        $validation = service('validation');
        $validation->setRules([
            'email'    => 'required|valid_email',
            'password' => 'required',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Email o contraseña no válidos.');
        }

        $email    = (string) $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->getByEmail($email);

        if ($user === null || (int) ($user['is_active'] ?? 0) !== 1) {
            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        if ($userModel->isLocked($user)) {
            return redirect()->back()->withInput()->with('error', 'Cuenta temporalmente bloqueada. Inténtalo más tarde.');
        }

        if (! password_verify($password, (string) $user['password'])) {
            $userModel->incrementFailedAttempts((int) $user['id']);

            return redirect()->back()->withInput()->with('error', 'Credenciales incorrectas.');
        }

        $userModel->resetFailedAttempts((int) $user['id']);
        $userModel->skipValidation(true)->update((int) $user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->request->getIPAddress(),
        ]);

        $session = session();
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'name'       => (string) $user['name'],
            'email'      => (string) $user['email'],
            'role'       => (string) $user['role'],
            'avatar'     => (string) ($user['avatar'] ?? ''),
        ]);

        if (($user['role'] ?? '') === 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        $target = (string) ($session->get('redirect_after_login') ?: '/');
        $session->remove('redirect_after_login');

        return redirect()->to($target !== '' ? $target : '/');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('web/auth/register', [
            'title' => 'Registro',
        ]);
    }

    public function processRegister()
    {
        $validation = service('validation');
        $validation->setRules([
            'name'              => 'required|min_length[2]|max_length[100]',
            'email'             => 'required|valid_email',
            'password'          => 'required|min_length[8]',
            'password_confirm'  => 'required|matches[password]',
            'accept_terms'      => 'required|in_list[1]',
        ], [
            'password' => [
                'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
            ],
            'accept_terms' => [
                'required' => 'Debes aceptar la política de privacidad y los términos de uso.',
                'in_list'  => 'Debes aceptar la política de privacidad y los términos de uso.',
            ],
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = new UserModel();
        $email     = (string) $this->request->getPost('email');
        $existing  = $userModel->withDeleted()->where('email', $email)->first();
        if ($existing !== null) {
            if (! empty($existing['deleted_at'])) {
                return redirect()->back()->withInput()->with(
                    'error',
                    'Este email ya estuvo registrado. Si necesitas recuperar la cuenta, contacta con soporte.',
                );
            }

            return redirect()->back()->withInput()->with('error', 'Este email ya está registrado.');
        }

        $userId = $userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $email,
            'password' => (string) $this->request->getPost('password'),
            'role'     => 'client',
            'is_active'=> 1,
        ], true);

        if ($userId === false) {
            log_message('error', 'Registro web: insert fallido. Errores modelo: ' . json_encode($userModel->errors()));

            return redirect()->back()->withInput()->with(
                'error',
                'No se pudo crear la cuenta. Inténtalo de nuevo o contacta con soporte.',
            );
        }

        $user = $userModel->find((int) $userId);
        if ($user === null) {
            return redirect()->to('/login')->with('success', 'Cuenta creada. Inicia sesión.');
        }

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => (int) $user['id'],
            'name'       => (string) $user['name'],
            'email'      => (string) $user['email'],
            'role'       => (string) $user['role'],
            'avatar'     => (string) ($user['avatar'] ?? ''),
        ]);

        return redirect()->to('/');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/');
    }

    public function forgotPassword()
    {
        return view('web/auth/forgot-password', [
            'title' => 'Recuperar contraseña',
        ]);
    }

    public function processForgotPassword()
    {
        $validation = service('validation');
        $validation->setRules([
            'email' => 'required|valid_email',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Indica un email válido.');
        }

        $email     = (string) $this->request->getPost('email');
        $userModel = new UserModel();
        $user      = $userModel->getByEmail($email);

        if ($user !== null && (int) ($user['is_active'] ?? 0) === 1) {
            $plain = bin2hex(random_bytes(32));
            $hash  = hash('sha256', $plain);
            $userModel->skipValidation(true)->update((int) $user['id'], [
                'remember_token' => $hash,
            ]);
            cache()->save('pwdreset_' . (int) $user['id'], time(), 3600);
            // En producción: enviar $plain por email (enlace a /reset-password?token=...)
        }

        return redirect()->back()->with(
            'success',
            'Si existe una cuenta con ese email, recibirás instrucciones para restablecer la contraseña.',
        );
    }

    public function resetPassword(string $token)
    {
        return view('web/auth/reset-password', [
            'title' => 'Nueva contraseña',
            'token' => $token,
        ]);
    }

    public function processResetPassword()
    {
        $validation = service('validation');
        $validation->setRules([
            'token'             => 'required',
            'password'          => 'required|min_length[8]',
            'password_confirm'  => 'required|matches[password]',
        ], [
            'password' => [
                'min_length' => 'La contraseña debe tener al menos 8 caracteres.',
            ],
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $token = (string) $this->request->getPost('token');
        $hash  = hash('sha256', $token);

        $userModel = new UserModel();
        $user      = $userModel->where('remember_token', $hash)->first();

        if ($user === null) {
            return redirect()->to('/login')->with('error', 'El enlace no es válido o ha caducado.');
        }

        $userId = (int) $user['id'];
        if (cache()->get('pwdreset_' . $userId) === null) {
            return redirect()->to('/login')->with('error', 'El enlace no es válido o ha caducado.');
        }

        $userModel->skipValidation(true)->update($userId, [
            'password'        => (string) $this->request->getPost('password'),
            'remember_token'  => null,
        ]);

        cache()->delete('pwdreset_' . $userId);

        return redirect()->to('/login')->with('success', 'Contraseña actualizada. Ya puedes iniciar sesión.');
    }
}