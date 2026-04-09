<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * SITEMAPCONTROLLER — CONTROLADOR HTTP (WEB PÚBLICA, HTML)
 * =============================================================================
 * UBICACIÓN: APP/CONTROLLERS/WEB/SITEMAPCONTROLLER.PHP.
 * QUÉ HACE: RECIBE PETICIONES, USA MODELOS/LIBRARIES Y RESPONDE CON VISTA O JSON.
 * POR QUÉ ASÍ: SEPARA ENTRADA/SALIDA HTTP DE PERSISTENCIA Y DE SERVICIOS TRANSVERSALES.
 * =============================================================================
 */

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class SitemapController extends BaseController
{
    public function index()
    {
        $baseUrl = rtrim(base_url(), '/');
        $urls = [];

        $staticPages = [
            ['url' => '/',              'priority' => '1.0', 'changefreq' => 'weekly'],
            ['url' => '/retratos',      'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => '/arte-en-vivo',  'priority' => '0.9', 'changefreq' => 'monthly'],
            ['url' => '/productos',     'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => '/portfolio',     'priority' => '0.8', 'changefreq' => 'weekly'],
            ['url' => '/branding',      'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/eventos',       'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/diseno',        'priority' => '0.7', 'changefreq' => 'monthly'],
            ['url' => '/contacto',      'priority' => '0.6', 'changefreq' => 'yearly'],
            ['url' => '/privacidad',    'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => '/aviso-legal',   'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($staticPages as $page) {
            $urls[] = [
                'loc'        => $baseUrl . $page['url'],
                'priority'   => $page['priority'],
                'changefreq' => $page['changefreq'],
                'lastmod'    => date('Y-m-d'),
            ];
        }

        $products = model('ProductModel')->where('is_active', 1)->findAll();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($products as $p) {
            $urls[] = [
                'loc'        => $baseUrl . '/productos/' . $p['slug'],
                'priority'   => '0.8',
                'changefreq' => 'weekly',
                'lastmod'    => date('Y-m-d', strtotime($p['updated_at'] ?? $p['created_at'] ?? 'now')),
            ];
        }

        $works = model('PortfolioWorkModel')->where('is_active', 1)->findAll();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($works as $w) {
            $urls[] = [
                'loc'        => $baseUrl . '/portfolio/' . $w['slug'],
                'priority'   => '0.7',
                'changefreq' => 'monthly',
                'lastmod'    => date('Y-m-d', strtotime($w['updated_at'] ?? $w['created_at'] ?? 'now')),
            ];
        }

        $branding = model('BrandingProjectModel')->where('is_active', 1)->findAll();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($branding as $b) {
            $urls[] = [
                'loc'        => $baseUrl . '/branding/' . $b['slug'],
                'priority'   => '0.6',
                'changefreq' => 'monthly',
                'lastmod'    => date('Y-m-d', strtotime($b['updated_at'] ?? $b['created_at'] ?? 'now')),
            ];
        }

        $events = model('EventModel')->where('is_active', 1)->findAll();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($events as $e) {
            $urls[] = [
                'loc'        => $baseUrl . '/eventos/' . $e['slug'],
                'priority'   => '0.6',
                'changefreq' => 'monthly',
                'lastmod'    => date('Y-m-d', strtotime($e['updated_at'] ?? $e['created_at'] ?? 'now')),
            ];
        }

        $designs = model('DesignProjectModel')->where('is_active', 1)->findAll();
        // BUCLE FOREACH SOBRE COLECCIÓN
        foreach ($designs as $d) {
            $urls[] = [
                'loc'        => $baseUrl . '/diseno/' . $d['slug'],
                'priority'   => '0.6',
                'changefreq' => 'monthly',
                'lastmod'    => date('Y-m-d', strtotime($d['updated_at'] ?? $d['created_at'] ?? 'now')),
            ];
        }

        return $this->response
            ->setContentType('application/xml')
            ->setBody(view('sitemap', ['urls' => $urls]));
    }
}