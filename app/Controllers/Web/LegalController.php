<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * LEGALCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/LEGALCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class LegalController extends BaseController
{
    public function privacidad()
    {
        return view('web/legal/privacidad', [
            'title'            => 'Política de Privacidad',
            'meta_title'       => 'Política de Privacidad',
            'meta_description' => 'Política de privacidad de nmonzzon Studio.',
        ]);
    }

    public function avisoLegal()
    {
        return view('web/legal/aviso-legal', [
            'title'            => 'Aviso Legal',
            'meta_title'       => 'Aviso Legal',
            'meta_description' => 'Aviso legal y condiciones de uso de nmonzzon Studio.',
        ]);
    }
}