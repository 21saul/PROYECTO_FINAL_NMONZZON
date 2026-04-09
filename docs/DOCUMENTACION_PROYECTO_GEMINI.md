# DOCUMENTACIÓN EXTENSA DEL PROYECTO — nmonzzon Studio (nmzonzzonstudio)

Versión del documento: 2026-04-07 (ampliada: desglose método a método)
Propósito: Contexto óptimo para modelos de lenguaje (p. ej. Gemini) y para
desarrolladores que necesitan saber QUÉ existe, DÓNDE está y QUÉ modificar.

IMPORTANTE SOBRE RUTAS: En este documento las rutas relativas son desde la raíz
del repositorio: /home/ddev/www/nmzonzzonstudio (o el clon equivalente).


## 1. QUÉ ES ESTE PROYECTO (VISIÓN GENERAL)


Es una aplicación web completa para el estudio creativo "nmonzzon Studio"
(Vigo, España): sitio público de marca, catálogo de productos con carrito y
checkout con Stripe, pedidos de retratos personalizados (configurador y
seguimiento), reservas de "arte en vivo", exposición de portfolio y proyectos
de branding, diseño y eventos, formulario de contacto, chatbot asistido por
webhook externo, área de cliente ("mi cuenta"), panel de administración web
completo, y una API REST con JWT pensada para app móvil o integraciones.

El backend es monolítico PHP sobre CodeIgniter 4; el front público mezcla
vistas PHP (server-side) con JavaScript propio y librerías cargadas por CDN.
No hay framework SPA separado (React/Vue) en el front principal.


## 2. TECNOLOGÍAS Y LENGUAJES


Lenguajes:
  - PHP 8.2+ (obligatorio; public/index.php comprueba la versión mínima)
  - SQL (MySQL/MariaDB vía driver MySQLi de CodeIgniter)
  - HTML5 embebido en vistas PHP
  - CSS3 (hoja principal muy grande: public/assets/css/custom.css)
  - JavaScript (ES5/ES6 según ficheros en public/assets/js/)
  - JSON (API, webhooks, metadatos)

Framework y runtime:
  - CodeIgniter 4.7+ (codeigniter4/framework vía Composer)
  - Front controller: public/index.php → Boot::bootWeb()

Gestión de dependencias PHP:
  - Composer (composer.json en raíz)
  - Autoload PSR-4: App\ → app/, Config\ → app/Config/

Paquetes PHP relevantes (require en composer.json):
  - stripe/stripe-php — pagos y webhooks
  - cloudinary/cloudinary_php — subida/transformación de imágenes (servicio
    propio también usa cURL a la API REST de Cloudinary)
  - dompdf/dompdf — generación de PDF (presupuestos, facturas)
  - intervention/image — manipulación de imágenes en servidor
  - firebase/php-jwt — tokens JWT para la API

Front-end vía CDN (referenciados en app/Views/layouts/main.php):
  - Bootstrap 5.3.3
  - Bootstrap Icons
  - jQuery 3.7.1
  - GSAP 3.12.5 + ScrollTrigger
  - AOS (animate on scroll)
  - Masonry + imagesLoaded
  - GLightbox
  - particles.js
  - Google Fonts: Cormorant Garamond

PWA:
  - public/manifest.json — nombre corto, iconos, theme_color, etc.

Base de datos:
  - MySQL/MariaDB (configuración típica en .env de CodeIgniter; Database.php
    tiene valores por defecto vacíos que se sobreescriben con el entorno)

Testing:
  - PHPUnit 10 (phpunit.xml.dist)
  - Tests en tests/ (Unit, Feature, Integration)

Herramientas auxiliares en tools/:
  - annotate_caps.php — script Composer "annotate-caps" para anotación de código
  - Scripts Windows/PowerShell para GitHub (entorno del autor)


## 3. ESTRUCTURA DE CARPETAS DE ALTO NIVEL


app/              Código de la aplicación (MVC + Config + migraciones + vistas)
public/           Document root web: index.php, assets, uploads, manifest
writable/         Sesiones, logs, caché, debugbar (generado en runtime; no versionar datos sensibles)
vendor/           Dependencias Composer (no editar a mano)
tests/            Pruebas automatizadas
tools/            Scripts de mantenimiento y anotación
system/           (si existe en instalación CI4) o solo vía vendor — núcleo del framework

Flujo HTTP típico:
  Navegador → public/index.php → CodeIgniter Router → Filtros → Controlador
  → Modelo(s) / Librerías → Vista PHP → HTML al cliente.


## 4. CONFIGURACIÓN EXTERNA Y VARIABLES DE ENTORNO


El proyecto usa el mecanismo estándar de CodeIgniter 4: fichero .env en la raíz
(no incluir secretos en el repositorio). En el código de aplicación aparecen
referencias explícitas a:

  STRIPE_SECRET_KEY          — Librería Stripe (servidor)
  STRIPE_PUBLIC_KEY          — expuesta al cliente en checkout (CartController)
  STRIPE_WEBHOOK_SECRET      — StripeWebhookController (firma del webhook)

  CLOUDINARY_CLOUD_NAME,
  CLOUDINARY_API_KEY,
  CLOUDINARY_API_SECRET      — CloudinaryService

  JWT_SECRET                 — JWTService; si falta, puede recurrir a
                              encryption.key del .env de CI4

  ADMIN_EMAIL                — EmailService (destinatario de notificaciones)

  N8N_CHATBOT_WEBHOOK        — Api\ChatbotController (proxy al flujo n8n)

  N8N_WEBHOOK_URL,
  N8N_WEBHOOK_SECRET         — Api\WebhookController (integración lealtad /
                              clientes; cabecera X-Webhook-Secret)

Además, CodeIgniter espera habitualmente en .env (convención del framework):
  - CI_ENVIRONMENT (development / production)
  - app.baseURL (o equivalente según versión)
  - database.default.* (hostname, database, username, password, etc.)
  - encryption.key (clave para operaciones criptográficas del framework)

La tabla site_settings puede almacenar claves de configuración editables desde
admin (incluye placeholders para Cloudinary en SiteSettingsSeeder); la lógica
runtime prioriza o combina .env y BD según implementación de cada controlador.

Servicios de terceros integrados:
  - Stripe: Checkout/pagos y POST stripe/webhook
  - Cloudinary: URLs optimizadas y API de subida/borrado
  - n8n (u orquestador compatible): webhooks para chatbot y loyalty
  - Google Fonts + jsDelivr CDN: activos estáticos externos

CSP (Content-Security-Policy): definida en app/Filters/SecurityHeadersFilter.php
incluye dominios Stripe, Cloudinary, cdn.jsdelivr.net, fonts, etc.


## 5. SEGURIDAD: FILTROS Y AUTENTICACIÓN


Archivo clave: app/Config/Filters.php

Alias personalizados:
  - secureheaders → App\Filters\SecurityHeadersFilter
  - ratelimit     → App\Filters\RateLimitFilter
  - auth          → App\Filters\AuthFilter (JWT Bearer en API)
  - admin         → App\Filters\AdminFilter
  - clientauth    → App\Filters\ClientAuthFilter

Globales:
  - CSRF activo excepto: rutas api/* y stripe/webhook (necesario para JSON y
    firma Stripe)
  - honeypot, invalidchars
  - after: secureheaders

Sesión web (cliente y admin):
  - ClientAuthFilter: exige session isLoggedIn para checkout y mi-cuenta
  - AdminFilter: exige isLoggedIn y role === 'admin'

API:
  - AuthFilter lee Authorization: Bearer <jwt>, valida con JWTService y adjunta
    userData al request para controladores API.

Roles en users.role:
  - 'admin' para panel admin y endpoints api/admin/* con permisos comprobados
    en BaseApiController::isAdmin()


## 6. MAPA DE RUTAS (RESUMEN FUNCIONAL)


Definición única: app/Config/Routes.php (muy comentado; es la fuente de verdad).

Público:
  /                           HomeController::index
  /portfolio, /portfolio/{slug}
  /retratos, /retratos/configurador, POST retratos/calcular-precio
  /arte-en-vivo, reservar (GET/POST)
  /branding, /branding/{slug}
  /eventos, /eventos/{slug}
  /diseno, /diseno/{slug}
  /productos, /productos/{slug}
  /carrito (+ POST add, update, remove, apply-coupon, remove-coupon)
  /contacto (GET/POST)
  /sitemap.xml, /privacidad, /aviso-legal

Protegido cliente (filter clientauth):
  /checkout, POST checkout/process, /checkout/success
  /mi-cuenta/* (dashboard, pedidos, retratos, perfil)

Auth web:
  /login, /register, /logout, forgot-password, reset-password

Stripe:
  POST /stripe/webhook

Admin (prefijo admin, filter admin excepto login):
  admin/login, admin/logout
  admin/dashboard (+ chart-data)
  admin/portfolio CRUD
  admin/portrait-orders
  admin/products CRUD + imágenes + variantes
  admin/bookings (+ calendar, calendar-data)
  admin/branding, admin/design, admin/events CRUD
  admin/categories, admin/testimonials, admin/coupons
  admin/messages (contacto)
  admin/users
  admin/settings

API (prefijo api):
  Público: auth/*, categories, portrait-styles, portrait-sizes, products,
    portfolio, branding, design, events, testimonials, POST contact,
    POST chatbot, POST live-art-bookings, GET webhooks/loyalty-clients,
    GET settings/{key}

  Con JWT (filter auth): perfil, portrait-orders, orders, coupons/validate, etc.

  Subgrupo api/admin con JWT: CRUD extendido, dashboard stats, subida de
  imágenes a proyectos/productos, etc. (permisos admin en código)


## 7. BASE DE DATOS: TABLAS Y AGRUPACIÓN LÓGICA


Migraciones en app/Database/Migrations/ (orden cronológico 2026-03-30 salvo
reorganización de branding 2026-04-06):

Usuarios y tokens:
  - users — clientes y administradores (role, soft deletes)
  - auth_tokens — refresh/sesiones API (según migración)

Contenido y catálogo:
  - categories — categorías de productos / navegación
  - products — productos tienda (slug, precio, stock, soft delete)
  - product_images — galería por producto
  - product_variants — tallas/opciones con precio propio
  - coupons — cupones de descuento

Portfolio y showcases:
  - portfolio_works — obras; campo cloudinary_public_id opcional
  - branding_projects + branding_project_images
  - design_projects + design_project_images
  - events + event_images

Pedidos tienda:
  - orders — pedidos tras checkout
  - order_items — líneas

Retratos:
  - portrait_styles, portrait_sizes — catálogo configurable
  - portrait_orders — pedidos de retrato
  - portrait_order_status_history — historial de estados

Arte en vivo:
  - live_art_bookings — reservas

Comunicación y sitio:
  - contact_messages
  - testimonials
  - site_settings — pares clave/valor por grupos (hero, media, etc.)

Migración adicional ReorganizeBrandingProjects:
  - Limpia slugs obsoletos, sincroniza galerías con ficheros en
    public/uploads/branding/, hace upsert de proyectos definidos en código.

Seeds (app/Database/Seeds/):
  - AdminUserSeeder, CategorySeeder, SiteSettingsSeeder, TestimonialSeeder,
    PortfolioWorkSeeder, PortraitStyleSeeder, PortraitSizeSeeder,
    ProductCategorySeeder, ProductSeeder, ProductImagesSeeder, AllImagesSeeder


## 8. CAPA DE APLICACIÓN: LIBRERÍAS (app/Libraries/)


CartService — Carrito en sesión; variantes, cupones, totales (IVA/envío según
  lógica interna), integración con modelos de producto.

StripeService — Cliente Stripe con clave desde env.

CloudinaryService — Subida vía API, destroy, generateUrl; usa env CLOUDINARY_*.

ImageUploadService — Subidas locales validadas a public/uploads/{subdir}/.

PortraitPricingService — Cálculo de precios del configurador de retratos.

PdfService — PDF con Dompdf (presupuestos/facturas en vistas pdf/).

JWTService — Creación/validación JWT (firebase/php-jwt + secreto env).

EmailService — Envío de correos (notificaciones pedidos, contacto, reservas).

Cada librería suele ser instanciada desde controladores o inyectada vía
constructor según el patrón del fichero concreto.


## 9. MODELOS (app/Models/) — TABLA ASOCIADA


AuthTokenModel           → auth_tokens
BrandingProjectModel     → branding_projects
BrandingProjectImageModel→ branding_project_images
CategoryModel            → categories
ContactMessageModel      → contact_messages
CouponModel              → coupons
DesignProjectModel       → design_projects
DesignProjectImageModel  → design_project_images
EventModel                 → events
EventImageModel          → event_images
LiveArtBookingModel      → live_art_bookings
OrderModel                 → orders
OrderItemModel           → order_items
PortfolioWorkModel       → portfolio_works
PortraitOrderModel       → portrait_orders
PortraitOrderStatusHistoryModel → portrait_order_status_history
PortraitSizeModel        → portrait_sizes
PortraitStyleModel       → portrait_styles
ProductModel             → products
ProductImageModel        → product_images
ProductVariantModel      → product_variants
SiteSettingModel         → site_settings
TestimonialModel         → testimonials
UserModel                → users

No hay carpeta app/Entities/ en este proyecto: se usan arrays como returnType
en la mayoría de modelos.


## 10. CONTROLADORES WEB (app/Controllers/Web/)


HomeController — Inicio: categorías, portfolio destacado, testimonios, settings.

PortfolioController — Listado y ficha de obra.

RetratosController — Landing retratos, configurador, POST calcular precio.

ArteEnVivoController — Info y formulario de reserva.

BrandingController — Listado y detalle por slug (helper branding_parse_service_tags).

DisenoController — Listado y detalle diseño.

EventosController — Listado y detalle eventos.

ProductosController — Tienda y ficha producto.

CartController — Carrito, cupones, checkout Stripe, éxito.

StripeWebhookController — Eventos Stripe (actualización pedidos/pagos).

ContactoController — Formulario contacto → BD y/o email.

AuthWebController — Login/registro/logout/recuperación contraseña web.

ClientDashboardController — Área cliente: pedidos, retratos, perfil.

SitemapController — sitemap.xml

LegalController — privacidad, aviso legal


## 11. CONTROLADORES ADMIN (app/Controllers/Admin/)


AdminLoginController — Login/logout admin (sesión distinta de lógica cliente).

DashboardController — Panel principal y datos para gráficas.

PortfolioAdminController — CRUD obras portfolio.

PortraitOrderAdminController — Gestión pedidos retrato, estados, subida boceto/final.

ProductAdminController — Productos, imágenes, variantes, toggle activo.

BookingAdminController — Reservas arte en vivo, calendario, presupuesto PDF.

BrandingAdminController, DesignAdminController, EventAdminController —
  CRUD proyectos con imágenes y destacados.

CategoryAdminController — Categorías.

TestimonialAdminController, CouponAdminController — contenido y promociones.

ContactAdminController — Bandeja mensajes contacto.

UserAdminController — Listado usuarios registrados, activar/desactivar.

SettingsAdminController — Ajustes globales site_settings.


## 12. CONTROLADORES API (app/Controllers/Api/)


BaseApiController — JSON, getUserData, getUserId, isAdmin, validateRequest.

AuthController — register, login, refresh, forgot/reset, profile JWT.

CategoryController, PortraitStyleController, PortraitSizeController — catálogos.

ProductController, PortfolioController — lectura pública + CRUD admin en grupo.

BrandingProjectController, DesignProjectController, EventController — igual patrón.

PortraitOrderController — creación y seguimiento por usuario; admin sub-rutas.

OrderController — pedidos tienda + factura PDF.

LiveArtBookingController — crear reserva; admin estado/calendario/cotización.

ContactController — API contacto + admin listado/marcar leído.

TestimonialController, CouponController, SettingsController.

DashboardController — estadísticas para app admin.

ChatbotController — reenvía mensajes a N8N_CHATBOT_WEBHOOK.

WebhookController — loyalty clients hacia n8n con secreto.


## 13. VISTAS (app/Views/) — GUÍA RÁPIDA POR FICHERO


layouts/main.php — Layout público: SEO, OG, Twitter, PWA, CSP meta CSRF,
  JSON-LD LocalBusiness (+ Product si $product), CDN CSS/JS, preloader,
  navbar, footer, chatbot, app.js + chatbot.js.

layouts/admin.php — Layout backoffice (estilos admin).

partials/navbar.php — Menú principal y enlaces a secciones.

partials/footer.php — Pie, redes, enlaces legales.

partials/chatbot.php — UI del asistente (ligada a assets/js/chatbot.js).

pagers/nmz_pager.php — Plantilla de paginación personalizada.

web/home.php — Página de inicio.

web/portfolio/index.php, web/portfolio/show.php — Portfolio público.

web/retratos/index.php, web/retratos/configurador.php — Retratos.

web/arte-en-vivo/index.php, web/arte-en-vivo/reservar.php

web/branding/index.php, web/branding/show.php

web/diseno/index.php, web/diseno/show.php

web/eventos/index.php, web/eventos/show.php

web/productos/index.php, web/productos/show.php

web/cart/index.php, checkout.php, success.php

web/contacto/index.php

web/auth/*.php — login, register, forgot-password, reset-password

web/client/*.php — dashboard, orders, order-detail, portraits, portrait-detail, profile

web/legal/privacidad.php, aviso-legal.php

admin/login.php, admin/dashboard.php

admin/portfolio/index.php, form.php

admin/products/index.php, form.php

admin/portrait-orders/index.php, show.php

admin/bookings/index.php, show.php, calendar.php

admin/branding/index.php, form.php

admin/design/index.php, form.php

admin/events/index.php, form.php

admin/categories/index.php, form.php

admin/testimonials/index.php, form.php

admin/coupons/index.php, form.php

admin/messages/index.php, show.php

admin/users/index.php, show.php

admin/settings/index.php

emails/*.php — Plantillas HTML de correo (contacto, pedidos, reservas, retratos).

pdf/quote.php, pdf/invoice.php — Plantillas para Dompdf.

sitemap.php — Vista XML usada por SitemapController.

errors/html/* y errors/cli/* — Páginas de error CI4.

welcome_message.php — Plantilla por defecto CI (puede no usarse en prod).


## 14. ACTIVOS FRONT (public/assets/)


css/custom.css — Estilos globales del sitio (muy extenso; tipografía, secciones,
  componentes, responsive, animaciones, overrides Bootstrap).

css/admin.css — Estilos panel administración.

js/app.js — Comportamiento general (navegación, AOS, GLightbox, partículas, UI).

js/chatbot.js — Cliente del widget de chat (llamadas a /api/chatbot).

js/cart.js — Lógica de carrito en cliente (si se usa en vistas carrito).

js/admin.js — Scripts backoffice.

js/portrait-config.js — Configurador de retratos en navegador.

js/retratos-carousel.js — Carrusel sección retratos.

images/placeholder.svg — Imagen por defecto.

images/icons/generate-icons.php — Utilidad PHP para iconos (revisar si es legacy).

Otros estáticos referenciados en layout: /assets/images/logo.png, favicon,
  iconos PWA bajo /assets/images/icons/ (tamaños en manifest.json).


## 15. PUBLIC/ RAÍZ (ADEMÁS DE index.php)


.htaccess — Reglas Apache típicas CI4 (rewrite a index.php).

manifest.json — Manifiesto PWA.

favicon.ico — (si existe en despliegue)

uploads/ — Destino de ImageUploadService y subidas admin; protegido en parte
  con .htaccess donde aplique. La migración de branding usa uploads/branding/.


## 16. CONFIGURACIÓN APP (app/Config/) — FICHERO POR FICHERO (BREVE)


App.php — baseURL por defecto localhost:8080; indexPage; locale; etc.

Autoload.php — PSR-4 App namespace.

Boot/development.php, production.php, testing.php — ajustes por entorno.

Cache.php, Constants.php, ContentSecurityPolicy.php, Cookie.php, Cors.php,
CURLRequest.php, Database.php, DocTypes.php, Email.php, Encryption.php,
Events.php, Exceptions.php, Feature.php, Filters.php — ya descrito Filters.

Format.php, ForeignCharacters.php, Generators.php, Honeypot.php, Hostnames.php,
Images.php, Kint.php, Logger.php, Migrations.php, Mimes.php, Modules.php,
Optimize.php, Paths.php, Publisher.php, Routes.php — rutas (corazón del routing).

Routing.php, Security.php, Session.php, Toolbar.php (Debug Bar en dev),
UserAgents.php, Validation.php, View.php, Pager.php — vista paginación NMZ,
WorkerMode.php

Services.php — Vacío de registro personalizado (solo clase extendida por defecto).

La mayoría son archivos estándar de CodeIgniter 4; los cambios habituales del
proyecto están en Routes.php, Filters.php, Database (DSN vía .env), Email, y
Security según despliegue.


## 17. FILTROS PERSONALIZADOS (app/Filters/)


SecurityHeadersFilter.php — Cabeceras seguridad + CSP amplia para Stripe/CDN.

RateLimitFilter.php — Limitación de frecuencia (revisar umbrales en código).

ClientAuthFilter.php — Sesión cliente.

AdminFilter.php — Sesión + rol admin.

AuthFilter.php — JWT API.


## 18. HELPERS (app/Helpers/)


api_helper.php — apiResponse, apiError, utilidades JSON y números de pedido.

image_helper.php — optimizedImage, lazyImage (integración Cloudinary opcional).

branding_helper.php — branding_parse_service_tags() para campo services_provided.


## 19. IDIOMA (app/Language/)


en/Validation.php — Mensajes validación (idioma inglés por defecto CI; el sitio
público está orientado a español en vistas).


## 20. PRUEBAS (tests/)


Unit/Libraries/: PdfServiceTest, JWTServiceTest, CartServiceTest

Unit/Helpers/: ApiHelperTest

Feature/: AuthTest, CartCheckoutTest, ContactFormTest, PortraitOrderFlowTest

Integration/: AuthApiTest, CartCheckoutIntegrationTest,
  ContactFormIntegrationTest, PortraitOrderIntegrationTest

_support/ — Utilidades y seeds/migraciones de ejemplo para tests.

El README en tests/ describe convenciones.


## 21. HERRAMIENTAS (tools/)


annotate_caps.php — Anotación automática de comentarios tipo "caps" en PHP
  (script referenciado en composer.json).

apply_annotation.py, EJECUTAR_ANOTACION.bat — flujo asociado en Windows.

subir_github.ps1, PASOS_GITHUB.txt — documentación de publicación Git.

LAST_ANNOTATE_OK.txt — marcador de última ejecución correcta.


## 22. RELACIONES ENTRE MÓDULOS (CÓMO SE AGRUPAN LAS COSAS)


TIENDA: ProductModel + ProductVariantModel + ProductImageModel + CategoryModel
  ↔ Web ProductosController, CartController, Api ProductController
  ↔ Admin ProductAdminController
  ↔ Vistas web/productos, web/cart, emails de pedido

PAGOS: StripeService + CartController (checkout) + StripeWebhookController
  ↔ OrderModel + OrderItemModel
  ↔ CouponModel aplicado en CartService

RETRATOS: Portrait* models + PortraitPricingService + RetratosController
  ↔ Api PortraitOrderController
  ↔ Admin PortraitOrderAdminController
  ↔ Vistas retratos/, client/portraits*, emails portrait_status

PORTFOLIO: PortfolioWorkModel (+ Cloudinary opcional) + PortfolioController
  ↔ Admin PortfolioAdminController + Api PortfolioController

BRANDING / DISEÑO / EVENTOS: tres pares proyecto+imágenes, controladores Web
  paralelos, admin CRUD homólogo, API espejo para móvil.

ARTE EN VIVO: LiveArtBookingModel ↔ ArteEnVivoController web, BookingAdminController,
  Api LiveArtBookingController, emails booking.

CONTACTO: ContactMessageModel ↔ ContactoController, Api ContactController,
  Admin ContactAdminController.

CONFIGURACIÓN GLOBAL: SiteSettingModel ↔ HomeController (hero), SettingsAdminController,
  Api SettingsController.

CHATBOT: partials/chatbot + chatbot.js → Api ChatbotController → n8n webhook.

AUTENTICACIÓN DUAL:
  - Web: sesión PHP para clientes y admins (filtros distintos).
  - API: JWT + auth_tokens según AuthController/JWTService.


## 23. GUÍA "QUÉ FICHERO TOCAR SI…"


Cambiar menú o enlaces del sitio:
  → app/Views/partials/navbar.php (y posiblemente footer).

Cambiar estilos globales o de una sección:
  → public/assets/css/custom.css (o admin.css para backoffice).

Añadir una página pública estática nueva:
  → Nuevo método en Web\*Controller + vista en app/Views/web/ + entrada en
     Routes.php.

Modificar textos legales:
  → app/Views/web/legal/*.php

Cambiar meta tags por defecto del sitio:
  → app/Views/layouts/main.php y/o datos que pasen los controladores ($meta_title).

Nueva ruta API:
  → app/Config/Routes.php dentro del grupo api + nuevo método en Api\*Controller.

Nueva tabla BD:
  → Migración en app/Database/Migrations/ + Model en app/Models/ + (opcional) seed.

Lógica de precios retratos:
  → app/Libraries/PortraitPricingService.php y RetratosController::calcularPrecio.

Integración pagos:
  → app/Libraries/StripeService.php, Web/CartController.php,
     Web/StripeWebhookController.php, .env STRIPE_*.

Emails transaccionales:
  → app/Libraries/EmailService.php + plantillas app/Views/emails/

PDF facturas/presupuestos:
  → app/Libraries/PdfService.php + app/Views/pdf/

Subida de archivos locales:
  → app/Libraries/ImageUploadService.php y rutas public/uploads/

Imágenes Cloudinary en portfolio:
  → CloudinaryService + campo cloudinary_public_id en portfolio_works +
     image_helper optimizedImage() en vistas.

Chatbot / automatización externa:
  → Api/ChatbotController.php, N8N_* en .env, public/assets/js/chatbot.js.

Seguridad cabeceras / CSP:
  → app/Filters/SecurityHeadersFilter.php (coherente con CDNs usados).

Desactivar CSRF para un endpoint concreto:
  → app/Config/Filters.php (except en globals['before']['csrf']) — hacerlo con
     cuidado y solo si hay sustituto de seguridad.

Panel admin nuevo listado:
  → Admin*Controller + vistas admin/* + rutas en grupo admin.


## 24. NOTAS SOBRE writable/ Y vendor/


writable/cache, writable/logs, writable/session, writable/debugbar — datos
volátiles de entorno. No son la fuente de lógica de negocio.

vendor/ — se regenera con `composer install`. Documentar dependencias en
composer.json, no editar a mano salvo parches puntuales (no recomendado).


## 25. NOMBRE DEL PROYECTO Y MARCA EN CÓDIGO


En vistas y metadatos aparece "nmonzzon Studio" / "nmonzzon" (doble n en
"monzzon"). El directorio del workspace es nmzonzzonstudio (posible variante
tipográfica del nombre de carpeta). Al buscar en código usar ambas convenciones
por si hubiera inconsistencias históricas.


## 26. COMANDOS ÚTILES (DESARROLLO)


composer install          — Instalar dependencias PHP
php spark migrate         — Ejecutar migraciones
php spark db:seed Nombre  — Poblar datos (según seeds registrados)
php spark routes          — Listar rutas (útil para depuración)
composer test             — PHPUnit
php spark serve           — Servidor de desarrollo embebido (si se usa)


## 27. RAÍZ DEL REPOSITORIO (FICHEROS Y CARPETAS EN /)


DOCUMENTACION_PROYECTO_GEMINI.md — Este documento (contexto para IA / onboarding).

README.md — Documentación humana muy extensa del proyecto: visión por módulos,
  fases de desarrollo, estructura de carpetas, comandos DDEV, seguridad, PWA,
  tabla de rutas resumida, variables de entorno sugeridas. Complementario a
  este documento Markdown; si hay divergencia puntual, priorizar el código fuente.

RESUMEN_COMPLETO_PROYECTO.md — Otro resumen largo ya presente en el repo;
  puede solaparse con este archivo; útil para comparar enfoques de documentación.

composer.json / composer.lock — Dependencias PHP y versiones resueltas.

phpunit.xml.dist — Configuración de tests PHPUnit (bootstrap CI4, suites,
  cobertura hacia build/logs).

spark — CLI de CodeIgniter 4 (migraciones, rutas, serve, etc.).

preload.php — Precarga OPcache de archivos del framework (optimización prod).

builds — Script relacionado con releases/builds CI4 (ejecutable).

env — Plantilla o ejemplo de variables de entorno (sin el punto inicial);
  el fichero activo suele ser .env (no documentar su contenido aquí).

LICENSE — Licencia del proyecto.

.ddev/ — Configuración DDEV (Docker): define servicios web, PHP, BD, posible
  docker-compose.n8n.yaml según docs/n8n-workflows.md. README indica despliegue
  recomendado con DDEV sobre WSL2; URL típica https://nmzonzzonstudio.ddev.site

docs/n8n-workflows.md — Guía detallada de integración n8n: URLs de webhook,
  eventos (new-portrait-order, order-status-change, new-booking, new-contact),
  payloads JSON, cabecera X-Webhook-Secret, prerequisitos DDEV+n8n.

.vscode/ — launch.json, tasks.json, settings.json del editor (depuración local).

.gitignore — Excluye vendor, writable volátil, .env, etc.

app/, public/, writable/, vendor/, tests/, tools/ — Ya descritos en secciones
  anteriores.


## 28. EVENTOS N8N REFERENCIADOS DESDE PHP (RESUMEN)


Según docs/n8n-workflows.md y WebhookController, la aplicación puede notificar
vía POST a {N8N_WEBHOOK_URL}/{event} con cuerpo JSON que incluye "event",
"timestamp" y "data". Eventos mencionados en la guía:

  - new-portrait-order
  - order-status-change
  - new-booking
  - new-contact

El chatbot usa otro endpoint configurable: N8N_CHATBOT_WEBHOOK (ChatbotController).


## 29. SEEDS: QUÉ HACE CADA UNO (app/Database/Seeds/)


AdminUserSeeder — Crea usuario administrador inicial (credenciales en seed;
  cambiar en producción).

CategorySeeder — Categorías de ejemplo para la tienda/navegación.

SiteSettingsSeeder — Pares clave/valor iniciales (hero, textos, placeholders
  Cloudinary en grupo media, etc.).

TestimonialSeeder — Testimonios de ejemplo.

PortfolioWorkSeeder — Obras de portfolio de demostración.

PortraitStyleSeeder / PortraitSizeSeeder — Catálogo para el configurador.

ProductCategorySeeder / ProductSeeder / ProductImagesSeeder — Productos demo.

AllImagesSeeder — Carga o asocia imágenes de demostración de forma agregada
  (revisar implementación antes de ejecutar en prod).

Orden de ejecución: respetar dependencias FK (categorías antes que productos,
  usuarios admin antes de pruebas que lo requieran, etc.).


## 30. MIGRACIONES 2026-03-30 (NOMBRE DE FICHERO → TABLA)


CreateUsersTable.php                    → users
CreateCategoriesTable.php               → categories
CreatePortraitSizesTable.php            → portrait_sizes
CreatePortraitStylesTable.php           → portrait_styles
CreatePortraitOrdersTable.php           → portrait_orders
CreatePortraitOrderStatusHistoryTable.php → portrait_order_status_history
CreateProductsTable.php                 → products
CreateProductImagesTable.php            → product_images
CreateProductVariantsTable.php          → product_variants
CreateLiveArtBookingsTable.php          → live_art_bookings
CreatePortfolioWorksTable.php           → portfolio_works
CreateBrandingProjectsTable.php         → branding_projects
CreateBrandingProjectImagesTable.php    → branding_project_images
CreateDesignProjectsTable.php           → design_projects
CreateDesignProjectImagesTable.php      → design_project_images
CreateEventsTable.php                   → events
CreateEventImagesTable.php              → event_images
CreateOrdersTable.php                   → orders
CreateOrderItemsTable.php               → order_items
CreateContactMessagesTable.php          → contact_messages
CreateCouponsTable.php                  → coupons
CreateTestimonialsTable.php             → testimonials
CreateAuthTokensTable.php               → auth_tokens
CreateSiteSettingsTable.php             → site_settings

ReorganizeBrandingProjects.php (2026-04-06) — Datos/carpetas branding (no nueva
  tabla; altera/inserta en branding_* y archivos en public/uploads/branding/).


## 31. APÉNDICE: CONTROLADORES API (LISTA Y PAPEL)


BaseApiController      — Base JSON, usuario JWT, validación.

AuthController         — Registro, login, refresh, recuperación, perfil.

CategoryController     — Categorías lectura + CRUD admin API.

PortraitStyleController / PortraitSizeController — Catálogo retratos.

ProductController      — Productos público + CRUD admin + imágenes.

PortfolioController    — Portfolio público + CRUD admin.

PortraitOrderController — Pedidos retrato usuario + admin estados/archivos.

OrderController        — Pedidos tienda, factura, estado admin.

CouponController       — Validación (auth) + CRUD admin.

LiveArtBookingController — Reservas + admin calendario/estado/cotización.

ContactController      — POST público + gestión admin.

BrandingProjectController — Proyectos branding REST.

DesignProjectController   — Proyectos diseño REST.

EventController        — Eventos REST.

TestimonialController  — Testimonios lectura pública + CRUD admin.

SettingsController     — Lectura por clave pública + índice/actualización admin.

DashboardController    — Métricas para panel app admin.

WebhookController      — Salida hacia n8n; endpoint loyalty getLoyaltyClients.

ChatbotController      — Proxy al webhook de chat n8n.


## 32. APÉNDICE: CONTROLADORES WEB (LISTA EXPANDIDA)


HomeController         — index (home).

PortfolioController    — index, show(slug).

RetratosController     — index, configurador, calcularPrecio (POST).

ArteEnVivoController   — index, reservar (GET), processReserva (POST).

BrandingController     — index, show(slug).

DisenoController       — index, show(slug).

EventosController      — index, show(slug).

ProductosController    — index, show(slug).

CartController         — index, add/update/remove, checkout, processCheckout,
                         success, applyCoupon, removeCoupon.

StripeWebhookController — handleWebhook (POST).

ContactoController     — index, send.

AuthWebController      — login, register, logout, forgot/reset password.

ClientDashboardController — mi-cuenta: dashboard, orders, orderDetail,
                         portraits, portraitDetail, profile, updateProfile.

SitemapController      — index (XML).

LegalController        — privacidad, avisoLegal.


## 33. APÉNDICE: CONTROLADORES ADMIN (LISTA EXPANDIDA)


AdminLoginController   — login, processLogin, logout.

DashboardController    — index, chartData.

PortfolioAdminController — CRUD portfolio + toggle featured.

PortraitOrderAdminController — listado, detalle, updateStatus, uploadSketch,
                         uploadFinal.

ProductAdminController — CRUD productos, imágenes, variantes, toggles.

BookingAdminController — reservas, calendario, datos calendario, detalle,
                         updateStatus, generateQuote, updateNotes.

BrandingAdminController / DesignAdminController / EventAdminController —
                         CRUD + toggles + imágenes según rutas.

CategoryAdminController — CRUD categorías.

TestimonialAdminController — CRUD testimonios + featured.

CouponAdminController  — CRUD cupones + toggle active.

ContactAdminController — listado mensajes, detalle, markRead, delete.

UserAdminController    — usuarios, detalle, toggleActive.

SettingsAdminController — formulario ajustes site_settings.


## 34. COHERENCIA CON README Y VERSIONES


README.md menciona PHP 8.4 y MariaDB 11.8 en el stack DDEV; composer.json exige
php ^8.2. La versión efectiva es la del contenedor/entorno (DDEV). Si se usa
solo PHP 8.2 en otro host, debe cumplirse el mínimo del composer.json.


## 35. DESGLOSE MÉTODO A MÉTODO — CONTROLADORES WEB (app/Controllers/Web/)


Ruta de clase: App\Controllers\Web\<Nombre>. Vistas típicas en
app/Views/web/... salvo indicación. "→" indica efecto principal.

--- HomeController ---
  index() → Carga categorías activas, obras portfolio destacadas, testimonios
    destacados y claves de SiteSettingModel (hero, about, etc.); renderiza
    web/home.php.

--- PortfolioController ---
  index() → Listado de obras activas (paginación si aplica); vista portfolio/index.
  show($slug) → Detalle por slug; 404 si no existe; vista portfolio/show.

--- RetratosController ---
  index() → Landing retratos; vista web/retratos/index.php.
  configurador() → Formulario/wizard del configurador; vista retratos/configurador.
  calcularPrecio() → POST AJAX/JSON; usa PortraitPricingService (o lógica
    equivalente) y devuelve precio estimado.

--- ArteEnVivoController ---
  initController(...) → Puede cargar helpers adicionales respecto a BaseController.
  index() → Información del servicio; vista arte-en-vivo/index.
  reservar() → Formulario GET de reserva; vista arte-en-vivo/reservar.
  processReserva() → POST: valida, guarda LiveArtBookingModel, emails/notificaciones
    según implementación.

--- BrandingController ---
  index() → Lista proyectos branding públicos; vista branding/index.
  show($slug) → Ficha; usa branding_helper para tags de servicios; branding/show.

--- DisenoController ---
  index() → Lista proyectos diseño; diseno/index.
  show($slug) → Ficha; diseno/show.

--- EventosController ---
  index() → Lista eventos; eventos/index.
  show($slug) → Ficha con galería; eventos/show.

--- ProductosController ---
  index() → Catálogo con categorías/filtros; productos/index.
  show($slug) → Ficha producto, variantes, imágenes; productos/show.

--- CartController ---
  __construct() → Inicializa CartService u dependencias.
  index() → Vista carrito con líneas y totales desde sesión.
  add() → POST: añade línea (producto + cantidad + variante opcional).
  update() → POST: actualiza cantidades.
  remove($key = null) → POST: elimina línea por clave compuesta o por segmento.
  applyCoupon() → POST: aplica código vía CartService::applyCoupon.
  removeCoupon() → POST: quita cupón de sesión.
  checkout() → GET: formulario checkout (Stripe Elements / datos); exige clientauth.
  processCheckout() → POST: crea Order, PaymentIntent Stripe, redirección o JSON.
  success() → Página de éxito tras pago confirmado.

--- StripeWebhookController ---
  handleWebhook() → POST raw body; verifica STRIPE_WEBHOOK_SECRET; procesa eventos
    Stripe (payment_intent.succeeded, etc.) y actualiza pedidos.

--- ContactoController ---
  index() → Formulario contacto; contacto/index.
  send() → POST: validación, ContactMessageModel, EmailService, posible Webhook n8n.

--- AuthWebController ---
  login() / processLogin() → Formulario y POST sesión cliente (UserModel, role).
  register() / processRegister() → Alta cliente.
  logout() → Destruye sesión.
  forgotPassword() / processForgotPassword() → Token recuperación (email).
  resetPassword($token) / processResetPassword() → Formulario y POST nuevo password.

--- ClientDashboardController ---
  index() → Resumen mi-cuenta; client/dashboard.
  orders() → Lista pedidos tienda del usuario; client/orders.
  orderDetail($id) → Detalle pedido; comprueba propiedad; client/order-detail.
  portraits() → Lista pedidos retrato; client/portraits.
  portraitDetail($id) → Detalle retrato; client/portrait-detail.
  profile() → Ver perfil; client/profile.
  updateProfile() → POST actualización datos usuario.

--- SitemapController ---
  index() → Respuesta XML (vista sitemap.php o generación directa).

--- LegalController ---
  privacidad() → web/legal/privacidad.
  avisoLegal() → web/legal/aviso-legal.

--- Controlador legacy app/Controllers/Home.php ---
  index(): string → Plantilla por defecto CodeIgniter (welcome); la home real es
    Web\HomeController vía Routes. No confundir rutas.


## 36. DESGLOSE MÉTODO A MÉTODO — ADMIN (app/Controllers/Admin/)


Prefijo URL: /admin/... (excepto admin/login, admin/logout sin grupo filter).

--- AdminLoginController ---
  initController → Helpers/layout admin.
  login() → Formulario acceso admin.
  processLogin() → Valida usuario con role admin, sesión isLoggedIn + role.
  logout() → Cierra sesión admin.

--- DashboardController (Admin) ---
  index() → Métricas y vista admin/dashboard.php.
  chartData() → JSON para gráficas (pedidos, ingresos, etc.).

--- PortfolioAdminController ---
  index() → Listado obras.
  create() / store() → Formulario alta y POST guardado.
  edit($id) / update($id) → Edición y POST.
  delete($id) → Borrado (soft delete según modelo).
  toggleFeatured($id) → POST AJAX destacado.

--- PortraitOrderAdminController ---
  index() → Listado pedidos retrato.
  show($id) → Detalle, historial estados, archivos.
  updateStatus($id) → POST cambio estado + email/n8n si aplica.
  uploadSketch($id) / uploadFinal($id) → POST subida boceto/entrega final.

--- ProductAdminController ---
  index() / create() / store() / edit($id) / update($id) / delete($id) / toggleActive($id)
    → CRUD productos y activación.
  NOTA DE ALINEACIÓN: En app/Config/Routes.php existen rutas POST hacia
    uploadImages, deleteImage, saveVariants en este controlador; si en el
    código actual no aparecen métodos públicos con esos nombres al final del
    fichero, las peticiones devolverían error hasta implementarlas o corregir
    rutas. Comprobar siempre Routes.php frente al .php del controlador.

--- BookingAdminController ---
  index() → Listado reservas arte en vivo.
  calendar() → Vista calendario; calendarData() → JSON eventos FullCalendar/similar.
  show($id) → Detalle reserva.
  updateStatus($id) → POST estado (pending/confirmed/cancelled...).
  generateQuote($id) → POST genera PDF presupuesto (PdfService).
  updateNotes($id) → POST notas internas.

--- BrandingAdminController ---
  index() / create() / store() / edit($id) / update($id) / delete($id) /
  toggleFeatured($id) → CRUD proyectos branding + galería en store/update.

--- DesignAdminController ---
  Misma estructura que branding para design_projects.

--- EventAdminController ---
  index() / create() / store() / edit() / update() / delete() / toggleFeatured().

--- CategoryAdminController ---
  index() / new() / create() / edit($id) / update($id) / delete($id).

--- TestimonialAdminController ---
  index() / new() / create() / edit() / update() / delete() / toggleFeatured().

--- CouponAdminController ---
  index() / new() / create() / edit() / update() / delete() / toggleActive().

--- ContactAdminController ---
  index() → Bandeja mensajes.
  show($id) → Lectura detalle.
  markRead($id) → POST marca leído.
  delete($id) → POST elimina mensaje.

--- UserAdminController ---
  index() → Lista usuarios (clientes).
  show($id) → Detalle.
  toggleActive($id) → POST activar/desactivar cuenta.

--- SettingsAdminController ---
  index() → Formulario claves site_settings agrupadas.
  update() → POST guarda ajustes (texto, media, etc.).


## 37. DESGLOSE MÉTODO A MÉTODO — API (app/Controllers/Api/)


Prefijo: /api/... Salvo auth pública, muchas rutas exigen header
Authorization: Bearer <JWT>. El grupo api/admin/* exige JWT y comprobaciones
isAdmin() en controladores.

--- BaseApiController (clase base, no expone rutas propias) ---
  getUserData(): ?array → Payload decodificado del JWT (AuthFilter).
  getUserId(): ?int
  isAdmin(): bool → role === 'admin'.
  validateRequest($rules, $messages) → bool|array validado o false si falla.

--- AuthController ---
  register() / login() → POST alta y login; devuelve access/refresh tokens.
  refresh() → POST nuevo access con refresh token.
  logout() → POST invalida tokens según lógica AuthTokenModel.
  profile() GET / updateProfile() PUT → datos usuario autenticado.
  forgotPassword() / resetPassword() → flujo API recuperación.

--- CategoryController ---
  index() / show($id) → público lectura.
  create() / update($id) / delete($id) → api/admin (admin).

--- PortraitStyleController / PortraitSizeController ---
  index() / show($id) público; create/update/delete en api/admin.

--- ProductController ---
  index() / show($slug) público.
  create() / update($id) / delete($id) admin API.
  addImage($id) / deleteImage($id, $imageId) admin API.

--- PortfolioController ---
  index() / show($slug) público; create/update/delete admin API.

--- PortraitOrderController ---
  create() → POST pedido retrato (usuario JWT).
  index() / show($id) → listado y detalle propios.
  updateStatus($id) → PUT api/admin: cambio estado.
  uploadReferencePhoto($id) → POST foto referencia cliente.
  uploadSketch($id) / uploadFinal($id) → POST admin API entregas.
  history($id) → GET historial de estados.

--- OrderController ---
  create() → POST pedido desde app (alternativa al checkout web).
  index() / show($id) → pedidos del usuario.
  updateStatus($id) → PUT admin API.
  downloadInvoice($id) → GET PDF factura (PdfService).

--- CouponController ---
  validateCoupon() → POST con JWT (carrito app).
  index/create/update/delete → CRUD admin API.

--- LiveArtBookingController ---
  create() → POST reserva pública.
  index() / show($id) → con auth según rutas.
  updateStatus($id) → admin API.
  calendar() → datos calendario admin.
  generateQuote($id) → POST PDF presupuesto.

--- ContactController ---
  create() → POST mensaje público.
  index() / markRead($id) / delete($id) → gestión admin API.

--- BrandingProjectController / DesignProjectController / EventController ---
  index/show público por slug; create/update/delete/addImages admin API.

--- TestimonialController ---
  index() público (lectura); en api/admin CRUD completo create/update/delete.

--- SettingsController ---
  show($key) → GET valor público por clave (p. ej. textos legales expuestos).
  index() / update() → api/admin listado y actualización masiva.

--- DashboardController (API) ---
  stats() / revenue() / ordersByStyle() / topProducts() → JSON métricas admin app.

--- WebhookController (API) ---
  notifyNewPortraitOrder($orderData): bool → uso interno; POST a n8n
    evento new-portrait-order.
  notifyOrderStatusChange($data): bool → order-status-change.
  notifyNewBooking($bookingData): bool → new-booking.
  notifyNewContact($contactData): bool → new-contact.
  sendWebhook() → privado; ensambla URL N8N_WEBHOOK_URL + evento y cabecera secreto.
  getLoyaltyClients() → GET ruta pública api/webhooks/loyalty-clients; usuarios
    role client sin pedidos/retratos recientes (3 meses) para campañas.

--- ChatbotController ---
  chat() → POST cuerpo mensaje; reenvía a N8N_CHATBOT_WEBHOOK; devuelve respuesta
    JSON al front (chatbot.js).


## 38. TABLA RÁPIDA: RUTA API → MÉTODO (REFERENCIA; VER Routes.php)


Autenticación:
  POST api/auth/register          → AuthController::register
  POST api/auth/login             → AuthController::login
  POST api/auth/refresh           → AuthController::refresh
  POST api/auth/forgot-password   → AuthController::forgotPassword
  POST api/auth/reset-password    → AuthController::resetPassword
  GET/PUT api/auth/profile        → profile / updateProfile (filter auth)
  POST api/auth/logout            → logout (filter auth)

Públicos lectura (GET salvo contact/chatbot/bookings):
  api/categories, portrait-styles, portrait-sizes, products, portfolio,
  branding, design, events, testimonials
  POST api/contact                → ContactController::create
  POST api/chatbot                → ChatbotController::chat
  POST api/live-art-bookings      → LiveArtBookingController::create
  GET  api/webhooks/loyalty-clients → WebhookController::getLoyaltyClients
  GET  api/settings/{segment}     → SettingsController::show

Con JWT (filter auth): portrait-orders*, orders*, coupons/validate, etc.

api/admin/* con JWT: portrait-orders/*/status, sketch, final; live-art-bookings;
  dashboard/*; CRUD categories, portrait-styles, products, portfolio, settings,
  contact, coupons, testimonials, orders, branding, design, events, imágenes.


## 39. LIBRERÍAS (app/Libraries/) — MÉTODOS PÚBLICOS RELEVANTES


CartService
  getItems(), addItem(), updateQuantity(), removeItem(), getSubtotal(),
  getShippingCost(), getTax(), getDiscount(), getTotal(), getItemCount(),
  applyCoupon(), removeCoupon(), clear(), getTotals()

StripeService
  isConfigured(), createPaymentIntent(), createCustomer(),
  retrievePaymentIntent(), confirmPayment()

CloudinaryService
  isAvailable(), upload(), delete(), generateUrl(), generateThumbnailUrl(),
  generatePortfolioUrl()

ImageUploadService
  upload(UploadedFile, $subdir), delete($path)

PortraitPricingService
  calculate(...) → precio según estilo, tamaño y opciones (parámetros ver firma)

PdfService
  generateInvoice($orderData), generateQuote($bookingData) → string HTML/PDF según implementación

JWTService
  generateAccessToken($userData), generateRefreshToken($userData),
  validateToken($token)

EmailService
  sendOrderConfirmation(), sendPortraitStatusUpdate(), sendBookingConfirmation(),
  sendContactNotification(), sendContactAutoReply()


## 40. FILTROS (app/Filters/) — PUNTOS DE EXTENSIÓN


AdminFilter::before → redirect admin/login si no hay sesión o role !== admin.
ClientAuthFilter::before → redirect /login si no isLoggedIn.
AuthFilter::before → 401 JSON si falta Bearer o token inválido; rellena userData.
SecurityHeadersFilter::after → cabeceras seguridad + CSP.
RateLimitFilter::before/after → límites por IP/ruta (revisar umbrales en código).


## 41. HELPERS — FUNCIONES GLOBALES


api_helper.php: apiResponse(), apiError(), (y otras si existen en el fichero
  completo, p. ej. generación order number).

image_helper.php: optimizedImage(), lazyImage() — Cloudinary opcional.

branding_helper.php: branding_parse_service_tags($raw) — normaliza etiquetas
  de servicios para vistas branding.


## 42. NOTA PARA IA: CÓMO USAR ESTE DESGLOSE


1) Para cambiar comportamiento de una URL concreta: abrir app/Config/Routes.php,
   localizar el string URI y anotar Controller::método; luego abrir ese método
   en el fichero bajo app/Controllers/...
2) Si la ruta apunta a un método inexistente, PHP devolverá error; contrastar
   siempre grep "public function" en el controlador con Routes.php.
3) Los métodos notify* de WebhookController no son rutas HTTP directas; se
   invocan desde otros controladores/servicios al crear o actualizar entidades.
4) initController en muchos controladores Admin/API carga helpers (form, url,
   api) o el layout; leer el cuerpo del método si falla un helper ausente.


## FIN DEL DOCUMENTO

Este archivo intenta cubrir el alcance funcional y la ubicación de cada pieza
para que un modelo o un desarrollador pueda localizar rápidamente el archivo
correcto antes de proponer cambios. Para detalle línea a línea de cada clase,
usar lectura directa del código fuente en app/ y las rutas en Routes.php como
índice de entradas HTTP.

Documentación adicional en el mismo repositorio:
  - README.md (guía humana completa, DDEV, seguridad, flujos)
  - docs/n8n-workflows.md (automatización y webhooks)
  - RESUMEN_COMPLETO_PROYECTO.md (otro resumen textual del proyecto)
