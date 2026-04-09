<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * BASEAPICONTROLLER — CONTROLADOR HTTP (API REST, JSON)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/API/BASEAPICONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function getUserData(): ?array
    {
        return $this->request->userData ?? null;
    }

    protected function getUserId(): ?int
    {
        $userData = $this->getUserData();
        return $userData['id'] ?? null;
    }

    protected function isAdmin(): bool
    {
        $userData = $this->getUserData();
        return ($userData['role'] ?? '') === 'admin';
    }

    protected function validateRequest(array $rules, array $messages = []): bool|array
    {
        if (!$this->validate($rules, $messages)) {
            return false;
        }
        return $this->validator->getValidated();
    }
}