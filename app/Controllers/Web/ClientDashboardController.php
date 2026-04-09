<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CLIENTDASHBOARDCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/CLIENTDASHBOARDCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\AuthTokenModel;
use App\Models\OrderModel;
use App\Models\PortraitOrderModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Router\Attributes\Filter;

// COMENTARIO CON ALMOHADILLA
#[Filter('clientauth')]
class ClientDashboardController extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('user_id');

        $orderModel = new OrderModel();
        $orders     = $orderModel->where('user_id', $userId)->findAll();
        $orderCount = count($orders);

        $portraitModel = new PortraitOrderModel();
        $portraits     = $portraitModel->where('user_id', $userId)->findAll();
        $portraitCount = count($portraits);

        $recentOrders = $orderModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        $recentPortraits = $portraitModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();

        return view('web/client/dashboard', [
            'title'            => 'Mi cuenta',
            'order_count'      => $orderCount,
            'portrait_count'   => $portraitCount,
            'recent_orders'    => $recentOrders,
            'recent_portraits' => $recentPortraits,
        ]);
    }

    public function orders()
    {
        $userId = (int) session()->get('user_id');

        $orderModel = new OrderModel();
        $orders     = $orderModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('web/client/orders', [
            'title'  => 'Mis pedidos',
            'orders' => $orders,
        ]);
    }

    public function orderDetail(int $id)
    {
        $userId = (int) session()->get('user_id');

        $orderModel = new OrderModel();
        $order      = $orderModel->getWithItems($id);

        if ($order === null || (int) ($order['user_id'] ?? 0) !== $userId) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        return view('web/client/order-detail', [
            'title' => 'Pedido ' . ($order['order_number'] ?? $id),
            'order' => $order,
        ]);
    }

    public function portraits()
    {
        $userId = (int) session()->get('user_id');

        $portraitModel = new PortraitOrderModel();
        $portraits     = $portraitModel
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('web/client/portraits', [
            'title'     => 'Mis retratos',
            'portraits' => $portraits,
        ]);
    }

    public function portraitDetail(int $id)
    {
        $userId = (int) session()->get('user_id');

        $portraitModel = new PortraitOrderModel();
        $portrait      = $portraitModel->getWithRelations($id);

        if ($portrait === null || (int) ($portrait['user_id'] ?? 0) !== $userId) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        return view('web/client/portrait-detail', [
            'title'    => 'Retrato ' . ($portrait['order_number'] ?? $id),
            'portrait' => $portrait,
        ]);
    }

    public function profile()
    {
        $userId = (int) session()->get('user_id');

        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        if ($user === null) {
            // LANZA UNA EXCEPCIÓN
            throw PageNotFoundException::forPageNotFound();
        }

        return view('web/client/profile', [
            'title' => 'Mi perfil',
            'user'  => $user,
        ]);
    }

    public function updateProfile()
    {
        $userId = (int) session()->get('user_id');

        $validation = service('validation');
        $validation->setRules([
            'name'  => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('error', 'Revisa los datos del perfil.');
        }

        $userModel = new UserModel();
        $userModel->skipValidation(true)->update($userId, [
            'name'  => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone') ?: null,
        ]);

        $user = $userModel->find($userId);
        if ($user !== null) {
            session()->set('name', (string) $user['name']);
        }

        return redirect()->back()->with('success', 'Perfil actualizado.');
    }

    /**
     * Sube foto de perfil (JPEG/PNG/GIF/WebP, máx. 2 MB) y actualiza sesión.
     */
    public function updateProfileAvatar()
    {
        $userId = (int) session()->get('user_id');
        $file   = $this->request->getFile('profile_photo');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return redirect()->back()->with('error', 'No se ha podido procesar el archivo.');
        }

        if ($file->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->back()->with('error', 'Selecciona una imagen (JPEG, PNG, GIF o WebP, máx. 2 MB).');
        }

        $stored = $this->storeClientAvatar($file);
        if ($stored === false) {
            return redirect()->back()->with(
                'error',
                'La imagen debe ser JPEG, PNG, GIF o WebP y no superar 2 MB.',
            );
        }

        $userModel = new UserModel();
        $user      = $userModel->find($userId);
        if ($user === null) {
            $this->removeStoredAvatarFile($stored);

            return redirect()->back()->with('error', 'No se pudo actualizar el perfil.');
        }

        $oldAvatar = trim((string) ($user['avatar'] ?? ''));
        $updated   = $userModel->skipValidation(true)->update($userId, ['avatar' => $stored]);

        if (! $updated) {
            $this->removeStoredAvatarFile($stored);

            return redirect()->back()->with('error', 'No se pudo guardar la foto de perfil.');
        }

        if ($oldAvatar !== '' && $oldAvatar !== $stored) {
            $this->removeStoredAvatarFile($oldAvatar);
        }

        session()->set('avatar', $stored);

        return redirect()->back()->with('success', 'Foto de perfil actualizada.');
    }

    /**
     * Guarda un avatar en uploads/users/avatars/ y devuelve la ruta relativa al docroot público.
     */
    private function storeClientAvatar(UploadedFile $file): string|false
    {
        if ($file->getSize() > 2 * 1024 * 1024) {
            return false;
        }

        $mime    = (string) $file->getMimeType();
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (! in_array($mime, $allowed, true)) {
            return false;
        }

        $extByMime = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
        ];
        $ext = $extByMime[$mime] ?? 'jpg';
        $name = bin2hex(random_bytes(16)) . '.' . $ext;

        $diskDir = FCPATH . 'uploads/users/avatars';
        if (! is_dir($diskDir) && ! mkdir($diskDir, 0755, true) && ! is_dir($diskDir)) {
            return false;
        }

        if (! $file->hasMoved() && ! $file->move($diskDir, $name)) {
            return false;
        }

        return 'uploads/users/avatars/' . $name;
    }

    /**
     * Elimina un fichero previo solo si está bajo uploads/users/avatars/.
     */
    private function removeStoredAvatarFile(string $relativePath): void
    {
        $relativePath = str_replace('\\', '/', $relativePath);
        if (preg_match('#^https?://#i', $relativePath)) {
            return;
        }
        if (! str_starts_with($relativePath, 'uploads/users/avatars/')) {
            return;
        }
        $full = FCPATH . $relativePath;
        if (is_file($full)) {
            unlink($full);
        }
    }

    /**
     * Baja de cuenta (cliente): soft delete, revoca tokens API y cierra sesión.
     */
    public function deleteAccount()
    {
        $session = session();
        $userId  = (int) $session->get('user_id');
        $role    = (string) $session->get('role');

        if ($role !== 'client') {
            return redirect()->back()->with('error', 'Esta acción solo está disponible para cuentas de cliente.');
        }

        $validation = service('validation');
        $validation->setRules([
            'delete_password'       => 'required',
            'delete_confirm_phrase' => 'required|in_list[ELIMINAR]',
        ], [
            'delete_confirm_phrase' => [
                'in_list' => 'Escribe exactamente ELIMINAR en mayúsculas para confirmar.',
            ],
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            return redirect()->back()->with('error', 'Confirma la eliminación con tu contraseña y la palabra ELIMINAR.');
        }

        $userModel = new UserModel();
        $user      = $userModel->find($userId);

        if ($user === null || ($user['role'] ?? '') !== 'client') {
            return redirect()->back()->with('error', 'No se pudo completar la solicitud.');
        }

        if (! password_verify((string) $this->request->getPost('delete_password'), (string) $user['password'])) {
            return redirect()->back()->with('error', 'La contraseña no es correcta.');
        }

        model(AuthTokenModel::class)->revokeAllForUser($userId);
        $userModel->delete($userId);

        $session->remove(['user_id', 'name', 'email', 'role', 'avatar', 'isLoggedIn']);
        $session->regenerate(true);

        return redirect()->to('/')->with(
            'success',
            'Tu cuenta se ha dado de baja. Gracias por haber estado con nosotros.',
        );
    }
}