<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * RETRATOSCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/RETRATOSCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\PortraitSizeModel;
use App\Models\PortraitStyleModel;

class RetratosController extends BaseController
{
    /**
     * PÁGINA ÍNDICE DE RETRATOS CON ESTILOS Y TAMAÑOS ACTIVOS ORDENADOS.
     */
    public function index()
    {
        $galleryDir = FCPATH . 'uploads/retratos/fotosretratos';
        $galleryImages = [];
        if (is_dir($galleryDir)) {
            $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            $files       = scandir($galleryDir, SCANDIR_SORT_NONE) ?: [];
            foreach ($files as $f) {
                if ($f === '.' || $f === '..') {
                    continue;
                }
                $ext = strtolower((string) pathinfo($f, PATHINFO_EXTENSION));
                if (! in_array($ext, $allowedExt, true)) {
                    continue;
                }
                $base        = (string) pathinfo($f, PATHINFO_FILENAME);
                $label       = ucfirst(str_replace(['_', '-'], ' ', $base));
                $galleryImages[] = [
                    'file'  => $f,
                    'label' => $label,
                ];
            }
            usort($galleryImages, static function (array $a, array $b) : int {
                return strnatcasecmp($a['file'], $b['file']);
            });
        }

        $data = [
            'meta_title'       => 'Retratos Personalizados',
            'meta_description' => 'Retratos artísticos personalizados en distintos estilos: color, blanco y negro, digital. Encarga el tuyo.',
            'styles' => model(PortraitStyleModel::class)->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll(),
            'sizes'  => model(PortraitSizeModel::class)->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll(),
            'galleryImages' => $galleryImages,
        ];

        return view('web/retratos/index', $data);
    }

    /**
     * VISTA DEL CONFIGURADOR DE RETRATO CON LOS MISMOS DATOS DE ESTILOS Y TAMAÑOS.
     */
    public function configurador()
    {
        $styles = model(PortraitStyleModel::class)->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();

        $data = [
            'styles' => $this->portraitStylesWithSamples($styles),
            'sizes'  => model(PortraitSizeModel::class)->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll(),
        ];

        return view('web/retratos/configurador', $data);
    }

    /**
     * IMÁGENES DE MUESTRA POR SLUG (BLANCO/NEGRO, A LÍNEA) Y RUTA uploads/ COHERENTE.
     *
     * @param list<array<string, mixed>> $styles
     *
     * @return list<array<string, mixed>>
     */
    protected function portraitStylesWithSamples(array $styles): array
    {
        $overrides = [
            'blanco-y-negro-todo-detalle' => 'uploads/retratos/estilos/sandra_maceira.png',
            'a-linea'                     => 'uploads/retratos/estilos/elisa_goris_a_linea.jpg',
        ];

        $out = [];
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($styles as $row) {
            $slug = strtolower((string) ($row['slug'] ?? ''));
            if (isset($overrides[$slug])) {
                $row['sample_image'] = $overrides[$slug];
            }
            $img = (string) ($row['sample_image'] ?? '');
            if ($img !== '' && strpos($img, 'http') !== 0 && strpos($img, 'uploads/') !== 0) {
                $row['sample_image'] = 'uploads/' . ltrim($img, '/');
            }
            $out[] = $row;
        }

        return $out;
    }

    /**
     * PETICIÓN AJAX POST: PARÁMETROS style_id, size_id, num_figures, with_frame.
     * DELEGA EL CÁLCULO AL SERVICIO CENTRALIZADO PortraitPricingService.
     */
    public function calcularPrecio()
    {
        if (! $this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'error'   => 'Method not allowed',
            ]);
        }

        $rules = [
            'style_id'    => 'required|integer',
            'size_id'     => 'required|integer',
            'num_figures' => 'required|integer|greater_than[0]|less_than_equal_to[10]',
            'with_frame'  => 'permit_empty',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $rawFrame  = $this->request->getPost('with_frame');
        $withFrame = filter_var($rawFrame, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false;

        // INICIO DE BLOQUE TRY
        try {
            $pricingService = new \App\Libraries\PortraitPricingService();
            $result = $pricingService->calculate(
                (int) $this->request->getPost('style_id'),
                (int) $this->request->getPost('size_id'),
                (int) $this->request->getPost('num_figures'),
                $withFrame
            );

            return $this->response->setJSON([
                'success'   => true,
                'price'     => $result['total_price'],
                'breakdown' => $result,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}