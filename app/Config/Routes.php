<?php

/* NMZ-CABECERA-FICHERO-MAYUSCU */
/*
 * =============================================================================
 * CONFIG CI4: APP/CONFIG/ROUTES.PHP
 * =============================================================================
 * MAPA DE RUTAS HTTP A CONTROLADORES/MÉTODOS; INCLUYE GRUPOS, FILTROS Y RUTAS DE DESARROLLO.
 * CENTRALIZA LA API Y LA WEB EN UN SOLO LUGAR PARA NO ACUPLAR URLS A CLASES EN CADA PETICIÓN.
 * =============================================================================
 */

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ══════════════════════════════════════════════════════════════
// RUTAS WEB PÚBLICAS
// ══════════════════════════════════════════════════════════════

$routes->get('/', 'Web\HomeController::index');

// PORTFOLIO PÚBLICO (LISTADO Y DETALLE)
$routes->get('portfolio', 'Web\PortfolioController::index');
$routes->get('portfolio/(:segment)', 'Web\PortfolioController::show/$1');

// SECCIÓN RETRATOS Y CONFIGURADOR
$routes->get('retratos', 'Web\RetratosController::index');
$routes->get('retratos/configurador', 'Web\RetratosController::configurador');
$routes->post('retratos/calcular-precio', 'Web\RetratosController::calcularPrecio');

// ARTE EN VIVO (INFORMACIÓN Y RESERVA)
$routes->get('arte-en-vivo', 'Web\ArteEnVivoController::index');
$routes->get('arte-en-vivo/reservar', 'Web\ArteEnVivoController::reservar');
$routes->post('arte-en-vivo/reservar', 'Web\ArteEnVivoController::processReserva');

// BRANDING (LISTADO Y PROYECTO)
$routes->get('branding', 'Web\BrandingController::index');
$routes->get('branding/(:segment)', 'Web\BrandingController::show/$1');

// EVENTOS
$routes->get('eventos', 'Web\EventosController::index');
$routes->get('eventos/(:segment)', 'Web\EventosController::show/$1');

// DISEÑO
$routes->get('diseno', 'Web\DisenoController::index');
$routes->get('diseno/(:segment)', 'Web\DisenoController::show/$1');

// CATÁLOGO PÚBLICO: TIENDA (canónico) + ALIAS LEGACY /productos
$routes->get('tienda', 'Web\ProductosController::index');
$routes->get('tienda/(:segment)', 'Web\ProductosController::show/$1');
$routes->get('productos', 'Web\ProductosController::index');
$routes->get('productos/(:segment)', 'Web\ProductosController::show/$1');

// CARRITO (VISTA Y ACCIONES)
$routes->get('carrito', 'Web\CartController::index');
$routes->post('carrito/add', 'Web\CartController::add');
$routes->post('carrito/update', 'Web\CartController::update');
$routes->post('carrito/remove', 'Web\CartController::remove');
$routes->post('carrito/remove/(:num)', 'Web\CartController::remove/$1');

// CHECKOUT (REQUIERE SESIÓN DE CLIENTE)
$routes->group('', ['filter' => 'clientauth'], static function ($routes) {
    $routes->get('checkout', 'Web\CartController::checkout');
    $routes->post('checkout/process', 'Web\CartController::processCheckout');
    $routes->get('checkout/success', 'Web\CartController::success');
});

// WEBHOOK DE STRIPE (SIN SESIÓN; FIRMA CRYPTOGRÁFICA)
$routes->post('stripe/webhook', 'Web\StripeWebhookController::handleWebhook');

// CUPONES VÍA AJAX
$routes->post('carrito/apply-coupon', 'Web\CartController::applyCoupon');
$routes->post('carrito/remove-coupon', 'Web\CartController::removeCoupon');

// FORMULARIO DE CONTACTO
$routes->get('contacto', 'Web\ContactoController::index');
$routes->post('contacto', 'Web\ContactoController::send');

// CAPTCHA DE PUZZLE (IMÁGENES GD + REFRESH JSON)
$routes->get('captcha/bg/(:segment)', 'Web\CaptchaController::background/$1');
$routes->get('captcha/piece/(:segment)', 'Web\CaptchaController::piece/$1');
$routes->get('captcha/refresh', 'Web\CaptchaController::refresh');

// SITEMAP Y PÁGINAS LEGALES
$routes->get('sitemap.xml', 'Web\SitemapController::index');
$routes->get('privacidad', 'Web\LegalController::privacidad');
$routes->get('aviso-legal', 'Web\LegalController::avisoLegal');

// AUTENTICACIÓN WEB (LOGIN, REGISTRO, LOGOUT, RESET)
$routes->get('login', 'Web\AuthWebController::login');
$routes->post('login', 'Web\AuthWebController::processLogin');
$routes->get('register', 'Web\AuthWebController::register');
$routes->post('register', 'Web\AuthWebController::processRegister');
$routes->get('logout', 'Web\AuthWebController::logout');
$routes->get('forgot-password', 'Web\AuthWebController::forgotPassword');
$routes->post('forgot-password', 'Web\AuthWebController::processForgotPassword');
$routes->get('reset-password/(:segment)', 'Web\AuthWebController::resetPassword/$1');
$routes->post('reset-password', 'Web\AuthWebController::processResetPassword');

// ÁREA PRIVADA DEL CLIENTE
$routes->group('mi-cuenta', ['filter' => 'clientauth'], static function ($routes) {
    $routes->get('/', 'Web\ClientDashboardController::index');
    $routes->get('pedidos', 'Web\ClientDashboardController::orders');
    $routes->get('pedidos/(:num)', 'Web\ClientDashboardController::orderDetail/$1');
    $routes->get('retratos', 'Web\ClientDashboardController::portraits');
    $routes->get('retratos/(:num)', 'Web\ClientDashboardController::portraitDetail/$1');
    $routes->get('perfil', 'Web\ClientDashboardController::profile');
    $routes->post('perfil', 'Web\ClientDashboardController::updateProfile');
    $routes->post('perfil/foto', 'Web\ClientDashboardController::updateProfileAvatar');
    $routes->post('perfil/eliminar-cuenta', 'Web\ClientDashboardController::deleteAccount');
});

// ══════════════════════════════════════════════════════════════
// RUTAS DE ADMINISTRACIÓN (FILTRO admin)
// ══════════════════════════════════════════════════════════════

$routes->get('admin/login', 'Admin\AdminLoginController::login');
$routes->post('admin/login', 'Admin\AdminLoginController::processLogin');
$routes->get('admin/logout', 'Admin\AdminLoginController::logout');

// /admin sin ruta explícita: redirige al dashboard si ya hay sesión admin, si no al login
$routes->get('admin', static function () {
    $session = session();
    if ($session->get('isLoggedIn') && $session->get('role') === 'admin') {
        return redirect()->to('/admin/dashboard');
    }

    return redirect()->to('/admin/login');
});

$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    $routes->get('dashboard/chart-data', 'Admin\DashboardController::chartData');

    // CRUD PORTFOLIO (OBRAS)
    $routes->get('portfolio', 'Admin\PortfolioAdminController::index');
    $routes->get('portfolio/create', 'Admin\PortfolioAdminController::create');
    $routes->post('portfolio', 'Admin\PortfolioAdminController::store');
    $routes->get('portfolio/edit/(:num)', 'Admin\PortfolioAdminController::edit/$1');
    $routes->post('portfolio/(:num)', 'Admin\PortfolioAdminController::update/$1');
    $routes->post('portfolio/delete/(:num)', 'Admin\PortfolioAdminController::delete/$1');
    $routes->post('portfolio/(:num)/toggle', 'Admin\PortfolioAdminController::toggleFeatured/$1');

    // GESTIÓN DE PEDIDOS DE RETRATOS
    $routes->get('portrait-orders', 'Admin\PortraitOrderAdminController::index');
    $routes->get('portrait-orders/(:num)', 'Admin\PortraitOrderAdminController::show/$1');
    $routes->post('portrait-orders/(:num)/status', 'Admin\PortraitOrderAdminController::updateStatus/$1');
    $routes->post('portrait-orders/(:num)/sketch', 'Admin\PortraitOrderAdminController::uploadSketch/$1');
    $routes->post('portrait-orders/(:num)/final', 'Admin\PortraitOrderAdminController::uploadFinal/$1');

    // CRUD PRODUCTOS DE TIENDA
    $routes->get('products', 'Admin\ProductAdminController::index');
    $routes->get('products/create', 'Admin\ProductAdminController::create');
    $routes->post('products', 'Admin\ProductAdminController::store');
    $routes->get('products/edit/(:num)', 'Admin\ProductAdminController::edit/$1');
    $routes->post('products/(:num)', 'Admin\ProductAdminController::update/$1');
    $routes->post('products/delete/(:num)', 'Admin\ProductAdminController::delete/$1');
    $routes->post('products/(:num)/toggle', 'Admin\ProductAdminController::toggleActive/$1');
    $routes->post('products/(:num)/images', 'Admin\ProductAdminController::uploadImages/$1');
    $routes->post('products/images/(:num)/delete', 'Admin\ProductAdminController::deleteImage/$1');
    $routes->post('products/(:num)/variants', 'Admin\ProductAdminController::saveVariants/$1');

    // RESERVAS DE ARTE EN VIVO
    $routes->get('bookings', 'Admin\BookingAdminController::index');
    $routes->get('bookings/calendar', 'Admin\BookingAdminController::calendar');
    $routes->get('bookings/calendar-data', 'Admin\BookingAdminController::calendarData');
    $routes->get('bookings/(:num)', 'Admin\BookingAdminController::show/$1');
    $routes->post('bookings/(:num)/status', 'Admin\BookingAdminController::updateStatus/$1');
    $routes->post('bookings/(:num)/quote', 'Admin\BookingAdminController::generateQuote/$1');
    $routes->post('bookings/(:num)/notes', 'Admin\BookingAdminController::updateNotes/$1');

    // CRUD PROYECTOS DE BRANDING
    $routes->get('branding', 'Admin\BrandingAdminController::index');
    $routes->get('branding/create', 'Admin\BrandingAdminController::create');
    $routes->post('branding', 'Admin\BrandingAdminController::store');
    $routes->get('branding/edit/(:num)', 'Admin\BrandingAdminController::edit/$1');
    $routes->post('branding/(:num)', 'Admin\BrandingAdminController::update/$1');
    $routes->post('branding/delete/(:num)', 'Admin\BrandingAdminController::delete/$1');
    $routes->post('branding/(:num)/toggle', 'Admin\BrandingAdminController::toggleFeatured/$1');

    // CRUD PROYECTOS DE DISEÑO
    $routes->get('design', 'Admin\DesignAdminController::index');
    $routes->get('design/create', 'Admin\DesignAdminController::create');
    $routes->post('design', 'Admin\DesignAdminController::store');
    $routes->get('design/edit/(:num)', 'Admin\DesignAdminController::edit/$1');
    $routes->post('design/(:num)', 'Admin\DesignAdminController::update/$1');
    $routes->post('design/delete/(:num)', 'Admin\DesignAdminController::delete/$1');
    $routes->post('design/(:num)/toggle', 'Admin\DesignAdminController::toggleFeatured/$1');

    // CRUD EVENTOS
    $routes->get('events', 'Admin\EventAdminController::index');
    $routes->get('events/create', 'Admin\EventAdminController::create');
    $routes->post('events', 'Admin\EventAdminController::store');
    $routes->get('events/edit/(:num)', 'Admin\EventAdminController::edit/$1');
    $routes->post('events/(:num)', 'Admin\EventAdminController::update/$1');
    $routes->post('events/delete/(:num)', 'Admin\EventAdminController::delete/$1');
    $routes->post('events/(:num)/toggle', 'Admin\EventAdminController::toggleFeatured/$1');

    // CRUD CATEGORÍAS
    $routes->get('categories', 'Admin\CategoryAdminController::index');
    $routes->get('categories/new', 'Admin\CategoryAdminController::new');
    $routes->post('categories', 'Admin\CategoryAdminController::create');
    $routes->get('categories/edit/(:num)', 'Admin\CategoryAdminController::edit/$1');
    $routes->post('categories/(:num)', 'Admin\CategoryAdminController::update/$1');
    $routes->post('categories/delete/(:num)', 'Admin\CategoryAdminController::delete/$1');

    // CRUD TESTIMONIOS
    $routes->get('testimonials', 'Admin\TestimonialAdminController::index');
    $routes->get('testimonials/new', 'Admin\TestimonialAdminController::new');
    $routes->post('testimonials', 'Admin\TestimonialAdminController::create');
    $routes->get('testimonials/edit/(:num)', 'Admin\TestimonialAdminController::edit/$1');
    $routes->post('testimonials/(:num)', 'Admin\TestimonialAdminController::update/$1');
    $routes->post('testimonials/(:num)/toggle-featured', 'Admin\TestimonialAdminController::toggleFeatured/$1');
    $routes->post('testimonials/delete/(:num)', 'Admin\TestimonialAdminController::delete/$1');

    // CRUD CUPONES
    $routes->get('coupons', 'Admin\CouponAdminController::index');
    $routes->get('coupons/new', 'Admin\CouponAdminController::new');
    $routes->post('coupons', 'Admin\CouponAdminController::create');
    $routes->get('coupons/edit/(:num)', 'Admin\CouponAdminController::edit/$1');
    $routes->post('coupons/(:num)', 'Admin\CouponAdminController::update/$1');
    $routes->post('coupons/(:num)/toggle-active', 'Admin\CouponAdminController::toggleActive/$1');
    $routes->post('coupons/delete/(:num)', 'Admin\CouponAdminController::delete/$1');

    // MENSAJES DE CONTACTO RECIBIDOS
    $routes->get('messages', 'Admin\ContactAdminController::index');
    $routes->get('messages/(:num)', 'Admin\ContactAdminController::show/$1');
    $routes->post('messages/(:num)/read', 'Admin\ContactAdminController::markRead/$1');
    $routes->post('messages/delete/(:num)', 'Admin\ContactAdminController::delete/$1');

    // USUARIOS REGISTRADOS
    $routes->get('users', 'Admin\UserAdminController::index');
    $routes->get('users/(:num)', 'Admin\UserAdminController::show/$1');
    $routes->post('users/(:num)/toggle', 'Admin\UserAdminController::toggleActive/$1');

    // AJUSTES DEL SITIO
    $routes->get('settings', 'Admin\SettingsAdminController::index');
    $routes->post('settings', 'Admin\SettingsAdminController::update');
});

// ══════════════════════════════════════════════════════════════
// API REST (APP MÓVIL Y CLIENTES AJAX)
// ══════════════════════════════════════════════════════════════

$routes->group('api', static function ($routes) {
    // AUTENTICACIÓN API (PÚBLICO)
    $routes->post('auth/register', 'Api\AuthController::register');
    $routes->post('auth/login', 'Api\AuthController::login');
    $routes->post('auth/refresh', 'Api\AuthController::refresh');
    $routes->post('auth/forgot-password', 'Api\AuthController::forgotPassword');
    $routes->post('auth/reset-password', 'Api\AuthController::resetPassword');

    // ENDPOINTS PÚBLICOS DE LECTURA Y ALGUNOS POST
    $routes->get('categories', 'Api\CategoryController::index');
    $routes->get('categories/(:num)', 'Api\CategoryController::show/$1');
    $routes->get('portrait-styles', 'Api\PortraitStyleController::index');
    $routes->get('portrait-styles/(:num)', 'Api\PortraitStyleController::show/$1');
    $routes->get('portrait-sizes', 'Api\PortraitSizeController::index');
    $routes->get('products', 'Api\ProductController::index');
    $routes->get('products/(:segment)', 'Api\ProductController::show/$1');
    $routes->get('portfolio', 'Api\PortfolioController::index');
    $routes->get('portfolio/(:segment)', 'Api\PortfolioController::show/$1');
    $routes->get('branding', 'Api\BrandingProjectController::index');
    $routes->get('branding/(:segment)', 'Api\BrandingProjectController::show/$1');
    $routes->get('design', 'Api\DesignProjectController::index');
    $routes->get('design/(:segment)', 'Api\DesignProjectController::show/$1');
    $routes->get('events', 'Api\EventController::index');
    $routes->get('events/(:segment)', 'Api\EventController::show/$1');
    $routes->get('testimonials', 'Api\TestimonialController::index');
    $routes->post('contact', 'Api\ContactController::create');
    $routes->post('chatbot/chat', 'Api\ChatbotController::chat');
    $routes->post('live-art-bookings', 'Api\LiveArtBookingController::create');
    $routes->get('webhooks/loyalty-clients', 'Api\WebhookController::getLoyaltyClients');
    $routes->get('settings/(:segment)', 'Api\SettingsController::show/$1');

    // RUTAS QUE EXIGEN JWT VÁLIDO (FILTRO auth)
    $routes->group('', ['filter' => 'auth'], static function ($routes) {
        $routes->get('auth/profile', 'Api\AuthController::profile');
        $routes->put('auth/profile', 'Api\AuthController::updateProfile');
        $routes->post('auth/logout', 'Api\AuthController::logout');

        $routes->post('portrait-orders', 'Api\PortraitOrderController::create');
        $routes->get('portrait-orders', 'Api\PortraitOrderController::index');
        $routes->get('portrait-orders/(:num)', 'Api\PortraitOrderController::show/$1');
        $routes->post('portrait-orders/(:num)/reference-photo', 'Api\PortraitOrderController::uploadReferencePhoto/$1');
        $routes->get('portrait-orders/(:num)/history', 'Api\PortraitOrderController::history/$1');

        $routes->post('orders', 'Api\OrderController::create');
        $routes->get('orders', 'Api\OrderController::index');
        $routes->get('orders/(:num)', 'Api\OrderController::show/$1');
        $routes->get('orders/(:num)/invoice', 'Api\OrderController::downloadInvoice/$1');

        $routes->post('coupons/validate', 'Api\CouponController::validateCoupon');
    });

    // SUBAPI DE ADMINISTRACIÓN (MISMO FILTRO auth; PERMISOS EN CONTROLADORES)
    $routes->group('admin', ['filter' => 'auth'], static function ($routes) {
        $routes->put('portrait-orders/(:num)/status', 'Api\PortraitOrderController::updateStatus/$1');
        $routes->post('portrait-orders/(:num)/sketch', 'Api\PortraitOrderController::uploadSketch/$1');
        $routes->post('portrait-orders/(:num)/final', 'Api\PortraitOrderController::uploadFinal/$1');
        $routes->put('live-art-bookings/(:num)/status', 'Api\LiveArtBookingController::updateStatus/$1');
        $routes->get('live-art-bookings/calendar', 'Api\LiveArtBookingController::calendar');
        $routes->post('live-art-bookings/(:num)/quote', 'Api\LiveArtBookingController::generateQuote/$1');
        $routes->get('dashboard/stats', 'Api\DashboardController::stats');
        $routes->get('dashboard/revenue', 'Api\DashboardController::revenue');
        $routes->get('dashboard/orders-by-style', 'Api\DashboardController::ordersByStyle');
        $routes->get('dashboard/top-products', 'Api\DashboardController::topProducts');

        $routes->post('categories', 'Api\CategoryController::create');
        $routes->put('categories/(:num)', 'Api\CategoryController::update/$1');
        $routes->delete('categories/(:num)', 'Api\CategoryController::delete/$1');
        $routes->post('portrait-styles', 'Api\PortraitStyleController::create');
        $routes->put('portrait-styles/(:num)', 'Api\PortraitStyleController::update/$1');
        $routes->delete('portrait-styles/(:num)', 'Api\PortraitStyleController::delete/$1');
        $routes->post('products', 'Api\ProductController::create');
        $routes->put('products/(:num)', 'Api\ProductController::update/$1');
        $routes->delete('products/(:num)', 'Api\ProductController::delete/$1');
        $routes->post('portfolio', 'Api\PortfolioController::create');
        $routes->put('portfolio/(:num)', 'Api\PortfolioController::update/$1');
        $routes->delete('portfolio/(:num)', 'Api\PortfolioController::delete/$1');
        $routes->put('settings', 'Api\SettingsController::update');
        $routes->get('settings', 'Api\SettingsController::index');

        $routes->get('contact', 'Api\ContactController::index');
        $routes->put('contact/(:num)/read', 'Api\ContactController::markRead/$1');
        $routes->delete('contact/(:num)', 'Api\ContactController::delete/$1');

        $routes->get('coupons', 'Api\CouponController::index');
        $routes->post('coupons', 'Api\CouponController::create');
        $routes->put('coupons/(:num)', 'Api\CouponController::update/$1');
        $routes->delete('coupons/(:num)', 'Api\CouponController::delete/$1');

        $routes->get('testimonials', 'Api\TestimonialController::index');
        $routes->post('testimonials', 'Api\TestimonialController::create');
        $routes->put('testimonials/(:num)', 'Api\TestimonialController::update/$1');
        $routes->delete('testimonials/(:num)', 'Api\TestimonialController::delete/$1');

        $routes->get('orders', 'Api\OrderController::index');
        $routes->put('orders/(:num)/status', 'Api\OrderController::updateStatus/$1');

        $routes->post('branding', 'Api\BrandingProjectController::create');
        $routes->put('branding/(:num)', 'Api\BrandingProjectController::update/$1');
        $routes->delete('branding/(:num)', 'Api\BrandingProjectController::delete/$1');
        $routes->post('branding/(:num)/images', 'Api\BrandingProjectController::addImages/$1');

        $routes->post('design', 'Api\DesignProjectController::create');
        $routes->put('design/(:num)', 'Api\DesignProjectController::update/$1');
        $routes->delete('design/(:num)', 'Api\DesignProjectController::delete/$1');
        $routes->post('design/(:num)/images', 'Api\DesignProjectController::addImages/$1');

        $routes->post('events', 'Api\EventController::create');
        $routes->put('events/(:num)', 'Api\EventController::update/$1');
        $routes->delete('events/(:num)', 'Api\EventController::delete/$1');
        $routes->post('events/(:num)/images', 'Api\EventController::addImages/$1');

        $routes->post('products/(:num)/images', 'Api\ProductController::addImage/$1');
        $routes->delete('products/(:num)/images/(:num)', 'Api\ProductController::deleteImage/$1/$2');
    });
});