<?php
declare(strict_types=1);

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONTACTADMINCONTROLLER — CONTROLADOR HTTP (PANEL DE ADMINISTRACIÓN)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/ADMIN/CONTACTADMINCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class ContactAdminController extends BaseController
{
    protected ContactMessageModel $messages;

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        helper(['form', 'url']);
        $this->messages = model(ContactMessageModel::class);
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

        $query = $this->messages->orderBy('created_at', 'DESC');

        $isRead = $this->request->getGet('is_read');
        if ($isRead === '0' || $isRead === '1') {
            $query = $query->where('is_read', (int) $isRead);
        }

        $category = $this->request->getGet('category');
        if ($category !== null && $category !== '') {
            $query = $query->where('category', (string) $category);
        }

        $data = [
            'title'    => 'Contact messages',
            'messages' => $query->findAll(),
            'filters'  => [
                'is_read'  => $isRead,
                'category' => $category,
            ],
        ];

        return view('admin/messages/index', $data);
    }

    public function show(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        $message = $this->messages->find($id);
        if ($message === null) {
            session()->setFlashdata('error', 'Message not found.');

            return redirect()->to('/admin/messages');
        }

        $this->messages->markAsRead($id);
        $message['is_read'] = 1;

        return view('admin/messages/show', [
            'title'   => 'Message',
            'message' => $message,
        ]);
    }

    public function markRead(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->messages->find($id) === null) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false]);
            }
            session()->setFlashdata('error', 'Message not found.');

            return redirect()->back();
        }

        $ok = $this->messages->markAsRead($id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['success' => $ok]);
        }

        if ($ok) {
            session()->setFlashdata('success', 'Marked as read.');
        } else {
            session()->setFlashdata('error', 'Could not update message.');
        }

        return redirect()->back();
    }

    public function delete(int $id)
    {
        if ($redirect = $this->requireAdminSession()) {
            return $redirect;
        }

        if ($this->messages->delete($id)) {
            session()->setFlashdata('success', 'Message deleted.');
        } else {
            session()->setFlashdata('error', 'Could not delete message.');
        }

        return redirect()->to('/admin/messages');
    }
}