# RESUMEN COMPLETO DEL PROYECTO — nmonzzon Studio

Documento generado como memoria técnica y de entrega del proyecto.
Aplicación web full-stack: e-commerce + portfolio + servicios para artista.
Stack principal: CodeIgniter 4, PHP 8.4, MariaDB 11.8, DDEV (WSL2), Apache.


## 1. VISIÓN GENERAL DEL PROYECTO


nmonzzon Studio es una plataforma web integral pensada para un artista que
ofrece múltiples líneas de negocio: retratos personalizados (con configurador
y flujo de pedidos tipo “presupuesto → entrega”), tienda de productos físicos
o digitales, portfolio creativo, reservas de “arte en vivo” para eventos, y
páginas de servicios (branding, diseño, eventos). El sistema incluye:

  • Sitio público con identidad visual definida (negro #1a1a1a, dorado #c9a96e,
    fondo off-white #f5f3f0), animaciones y experiencia de usuario cuidada.
  • API REST con autenticación JWT, filtros de seguridad y más de cien rutas.
  • Panel de administración completo para gestionar catálogo, pedidos,
    reservas, mensajes, cupones, usuarios y ajustes del sitio.
  • Checkout con Stripe, carrito en sesión, facturas y presupuestos en PDF.
  • Integraciones opcionales: PWA, Cloudinary, n8n (webhooks y automatización),
    chatbot con FAQ y posible fallback a n8n/OpenAI.
  • Pruebas automatizadas con PHPUnit y endurecimiento básico vía .htaccess.

El desarrollo se articuló en cinco fases (cinco prompts), cada una ampliando
capas del sistema: infraestructura, datos y API, frontend público, comercio y
admin, y por último PWA, integraciones externas, correo y testing.


## 2. FASE 1 — ENTORNO Y CONFIGURACIÓN INICIAL (PROMPT 1)


### 2.1. Objetivo de la fase
Establecer un entorno de desarrollo reproducible bajo Windows con WSL2,
orquestado por DDEV, e instalar CodeIgniter 4 mediante Composer (appstarter)
para servir como esqueleto de la aplicación.

### 2.2. DDEV sobre WSL
DDEV permite levantar contenedores con PHP, base de datos y servidor web
coordinados. En este proyecto, el sitio quedó accesible en desarrollo en:

  https://nmzonzzonstudio.ddev.site

Tras iniciar DDEV, se verificó que el entorno respondía correctamente (HTTP/S
según configuración de DDEV y certificados locales).

### 2.3. Archivo .ddev/config.yaml
Configuración orientada a:
  • PHP 8.4 — versión moderna con buen rendimiento y características recientes.
  • MariaDB 11.8 — motor relacional compatible con el ecosistema MySQL.
  • Apache — servidor web tradicional, habitual en hosting compartido y
    despliegues clásicos de PHP.

Esta combinación aproxima un entorno de producción típico para aplicaciones PHP.

### 2.4. Variables de entorno (.env)
  • Credenciales de base de datos alineadas con DDEV (convención db/db/db en
    muchos proyectos DDEV: usuario, contraseña y nombre de base).
  • baseURL configurada para reflejar la URL del proyecto en desarrollo.
  • CI_ENVIRONMENT=development — activa comportamientos de depuración y
    mensajes de error más verbosos de CodeIgniter (no usar en producción).

### 2.5. Estructura de carpetas del proyecto
Se organizó la estructura estándar de CodeIgniter 4 (app/, public/, writable/,
vendor/, etc.) y se prepararon ubicaciones lógicas para controladores Web, API,
Admin, vistas, migraciones, seeders, librerías, helpers, tests y documentación.

### 2.6. Resultado de la fase
Base técnica lista: Composer, CI4, DDEV operativo, conexión a base de datos
configurada y URL de desarrollo verificada.


## 3. FASE 2 — BASE DE DATOS, MIGRACIONES, MODELOS Y API REST (PROMPT 2)


### 3.1. Panorama
Se diseñó un esquema relacional coherente con el negocio del artista: usuarios
con roles, catálogo e-commerce, pedidos de retrato con máquina de estados,
reservas, portfolio y contenidos de marketing (testimonios, proyectos, etc.).

### 3.2. Migraciones (18 tablas)
Las migraciones permiten versionar el esquema y reproducirlo en cualquier
entorno. Resumen conceptual por dominio:

(1) USUARIOS Y SEGURIDAD
  • users — Cuentas con contraseñas hasheadas (bcrypt). Roles principales:
    admin y client, diferenciando panel de gestión y área de cliente.

(2) CATÁLOGO Y CLASIFICACIÓN
  • categories — Organización de portfolio y/o productos según el modelo de
    negocio implementado.

(3) RETRATOS — CONFIGURACIÓN DE PRECIOS
  • portrait_styles — Estilos como Color, B&W, Figurín, Sin Caras, A Línea,
    cada uno con precio base asociado.
  • portrait_sizes — Tamaños A4, A3, A2 con modificadores de precio respecto
    al cálculo del encargo.

(4) RETRATOS — PEDIDOS Y AUDITORÍA
  • portrait_orders — Pedido completo con importes, opciones (figuras, marco,
    etc.) y estado actual.
  • portrait_order_status_history — Historial de cambios de estado para trazabilidad
    y atención al cliente.

La máquina de estados del retrato (conceptual):
  quote → accepted → photo_received → in_progress → revision → delivered →
  completed (o cancelled en cualquier punto válido del flujo según reglas de negocio).

(5) E-COMMERCE
  • products — Productos con slugs y campos orientados a SEO (títulos,
    descripciones, meta, etc., según implementación).
  • product_images — Galería asociada a cada producto.
  • product_variants — Variantes con stock y precios (talla, formato, etc.).
  • orders — Cabecera de pedidos de tienda.
  • order_items — Líneas de pedido vinculadas a productos/variantes.
  • coupons — Cupones por porcentaje o importe fijo, límites de uso y ventanas
    de fechas.

(6) SERVICIOS Y CONTENIDO
  • live_art_bookings — Reservas de arte en vivo (bodas, corporativo,
    cumpleaños, etc.) con datos de evento, fecha y ubicación.
  • portfolio_works — Piezas del portfolio del artista.
  • branding_projects, design_projects, events — Escaparates de trabajos de
    marca, diseño y eventos.
  • testimonials — Opiniones de clientes para refuerzo social en la web pública.
  • contact_messages — Mensajes recibidos desde el formulario de contacto.

(7) CONFIGURACIÓN GLOBAL
  • site_settings — Almacén clave-valor para parámetros del sitio (textos,
    toggles, URLs de integración, etc.) sin redeploy por cada pequeño ajuste.

### 3.3. Modelos (18)
Cada modelo encapsula:
  • Reglas de validación para entradas de API y formularios.
  • Relaciones (hasMany, belongsTo, etc.) reflejando integridad lógica.
  • Campos permitidos (allowedFields) para protección contra asignación masiva.

### 3.4. Seeders (11 archivos) y datos de prueba
Seeders que poblan tablas con contenido realista en español: categorías,
estilos de retrato, productos de ejemplo, pedidos, reservas, testimonios,
mensajes de contacto y ajustes por defecto. Facilitan demostraciones, QA manual
y desarrollo sin captura manual de datos.

### 3.5. API REST — controladores (16 en inventario final; 14+ en narrativa inicial)
Patrón común: extensión de BaseApiController para respuestas JSON homogéneas,
manejo de errores y convenciones HTTP.

Listado funcional:
  • AuthController — Registro, login, refresh de JWT, flujo forgot/reset
    password.
  • CategoryController — CRUD o listados de categorías.
  • PortraitStyleController, PortraitSizeController — Datos para configurador
    y API móvil/futura.
  • ProductController — Catálogo e-commerce vía API.
  • PortfolioController — Obras del portfolio.
  • PortraitOrderController — Creación y transición de estados con validación
    de la máquina de estados.
  • OrderController — Pedidos de tienda.
  • CouponController — Validación y gestión de cupones.
  • LiveArtBookingController — Reservas; incluye endpoint de calendario.
  • ContactController — Alta de mensajes con rate limiting.
  • BrandingProjectController, DesignProjectController, EventController —
    Contenidos de servicios.
  • TestimonialController — Testimonios.
  • SettingsController — Lectura/escritura de ajustes (según permisos).
  • DashboardController — Métricas agregadas para paneles o integraciones.
  • WebhookController — (Fase 5) Disparadores hacia n8n y endpoints auxiliares.
  • ChatbotController — (Fase 5) FAQ y fallback automatizado.

### 3.6. JWT (JWTService)
  • Tokens de acceso y refresh para sesiones API stateless.
  • Librería firebase/php-jwt en backend.
  • Rotación o renovación vía endpoint dedicado según diseño de AuthController.

### 3.7. Filtros de seguridad
  • AuthFilter — Exige JWT válido en rutas protegidas.
  • AdminFilter — Restringe operaciones de administración API.
  • RateLimitFilter — Mitiga abuso en endpoints sensibles (p. ej. contacto).
  • CorsFilter — Cabeceras CORS para consumo desde otros orígenes controlados.

### 3.8. Helpers y utilidades API
  • apiResponse(), apiError() — Formato JSON consistente (código, mensaje, datos).
  • generateOrderNumber() — Generación de referencias legibles para pedidos.

### 3.9. Rutas (Routes.php)
Más de 100 rutas agrupadas por contexto:
  • Públicas — Catálogo, contenido, registro si aplica.
  • Autenticadas — Recursos del usuario/cliente.
  • Admin API — Gestión con privilegios elevados.

### 3.10. Resultado de la fase
Backend “datos + API” operativo: esquema versionado, datos semilla, capa de
acceso con validación y seguridad transversal.


## 4. FASE 3 — FRONTEND PÚBLICO COMPLETO (PROMPT 3)


### 4.1. Layout principal (layouts/main.php)
  • Bootstrap 5.3 para rejilla, componentes y utilidades responsive.
  • Google Fonts: Playfair Display (elegancia/títulos) + Inter (cuerpo/UI).
  • Bootstrap Icons 1.11.3.
  • AOS (Animate On Scroll) para aparición suave de bloques.
  • GLightbox para lightbox en imágenes.
  • GSAP 3.12.5 + ScrollTrigger para animaciones avanzadas al scroll.
  • Particles.js para fondos dinámicos en hero/landing.
  • Masonry + imagesLoaded para grillas tipo portfolio irregulares.

### 4.2. Hoja de estilos custom.css (~1900+ líneas)
  • Variables CSS para colores, espaciados, tipografías y transiciones.
  • Animaciones reutilizables (hover, fade, microinteracciones).
  • Diseño responsive (breakpoints, tipografía fluida donde aplique).
  • Coherencia con marca nmonzzon (negro, dorado, off-white).

### 4.3. JavaScript global (app.js)
  • Preloader inicial para percepción de carga controlada.
  • Inicialización de AOS.
  • GSAP ScrollTrigger en secciones clave.
  • Masonry + imagesLoaded en grillas.
  • GLightbox en galerías.
  • Particles.js en fondos configurados.
  • Botón “volver arriba”.
  • Efectos de navbar al hacer scroll (sticky/transparencia/sombra).
  • Carga diferida (lazy) de imágenes.
  • Helper CSRF para peticiones AJAX compatibles con CodeIgniter.

### 4.4. Parciales reutilizables
  • partials/navbar.php — Navegación responsive con dropdowns de servicios.
  • partials/footer.php — Enlaces, redes sociales, newsletter.

### 4.5. Páginas y módulos públicos
  • Home — Hero, resumen de servicios, portfolio destacado, carrusel de
    testimonios, CTAs.
  • Retratos — Configurador interactivo (portrait-config.js): selección de
    estilo/tamaño, número de figuras, opción de marco, cálculo de precio en
    tiempo real.
  • Arte en vivo — Tipos de evento, selector de fecha, ubicación, flujo de
    reserva acorde a vistas implementadas.
  • Portfolio — Masonry, filtros por categoría, GLightbox.
  • Productos — Vista de rejilla e integración con carrito (base para fase 4).
  • Servicios individuales — Branding, Diseño, Eventos (listado + detalle show).
  • Contacto — Formulario con honeypot anti-bots y selector de categoría.
  • Autenticación web — Login, registro, olvidé contraseña, restablecimiento.
  • Área cliente — Perfil, pedidos de tienda, pedidos de retratos, detalle de
    pedido/retrato.

### 4.6. Controladores Web
El proyecto evolucionó a ~12-13 controladores en la carpeta Web según el
inventario completo (incluye StripeWebhookController para webhooks de pago en
fase posterior, aunque el archivo vive bajo Web). Lista de referencia:

  HomeController, PortfolioController, RetratosController, ArteEnVivoController,
  BrandingController, EventosController, DisenoController, ProductosController,
  CartController, ContactoController, AuthWebController, ClientDashboardController,
  StripeWebhookController.

Cada uno orquesta vistas, datos del modelo/servicios y redirecciones según rol.

### 4.7. Vistas públicas (35+)
Organización bajo app/Views/web/... con layouts y partials compartidos.
Incluye home, portfolio, retratos (index + configurador), arte en vivo,
branding/eventos/diseño (index + show), productos (index + show), carrito
(index, checkout, success), contacto, auth (login, register, forgot, reset),
cliente (dashboard, orders, order-detail, portraits, portrait-detail, profile).

### 4.8. Resultado de la fase
Sitio público “presentable” y funcional a nivel UX: marca consistente,
animaciones, formularios y bases para e-commerce y cuenta de cliente.


## 5. FASE 4 — E-COMMERCE COMPLETO Y PANEL DE ADMINISTRACIÓN (PROMPT 4)


### 5.1. CartService (librería)
Carrito basado en sesión con operaciones:
  • Añadir, actualizar cantidades, eliminar líneas.
  • Cálculo de subtotal.
  • Envío: regla de envío gratuito por encima de 50 € (según especificación).
  • Impuestos: IVA 21 % sobre la base imponible según diseño implementado.
  • Descuentos vía cupones (integración con modelo/API de cupones).
  • Total final coherente para checkout y resumen.

### 5.2. StripeService y webhook
  • Creación de PaymentIntents y clientes en Stripe.
  • Manejo de eventos webhook en StripeWebhookController:
      - payment_intent.succeeded — confirmar pago y actualizar pedido.
      - payment_intent.failed — registrar fallo y feedback al flujo.

### 5.3. Flujo de checkout (CartController)
  • Formulario de datos de envío.
  • Verificación de stock antes de comprometer inventario.
  • Creación de orden en base de datos.
  • Creación de PaymentIntent y devolución de datos al frontend para Stripe.js.
  • Página de éxito post-pago.

### 5.4. Frontend del carrito
  • Actualizaciones AJAX sin recarga completa.
  • Aplicación de cupón en caliente.
  • Integración con Stripe Elements para datos de tarjeta de forma segura
    (PCI: el servidor no almacena PAN completo).

### 5.5. PDF — PdfService + dompdf
  • Facturas profesionales con branding.
  • Presupuestos (quotes) para retratos u otros servicios según vistas pdf/.
  • Vistas dedicadas: invoice.php, quote.php.

### 5.6. Panel admin — layout y activos
  • layouts/admin.php — Sidebar fija, topbar, estructura de contenido.
  • CDNs: Chart.js (gráficos), FullCalendar (reservas), DataTables (tablas
    ricas en listados).
  • admin.css — Tema completo: variables, sidebar, cards, tablas, badges,
    toggles, timelines, formularios.
  • admin.js — Toggle sidebar, AJAX para toggles (p. ej. destacados),
    marcar como leído, confirmación de borrado, preview de subida de imagen,
    generador de slug, filas dinámicas de variantes de producto.

### 5.7. Dashboard administrativo
  • Tarjetas KPI: ingresos, pedidos, reservas, mensajes.
  • Gráfico de ingresos últimos 12 meses (Chart.js).
  • Gráfico de dona por estilo de retrato.
  • Tabla de pedidos recientes.
  • Próximas reservas de arte en vivo.

### 5.8. Vistas de administración (~27-28)
Módulos con listado + formulario (o show) según entidad:

  • Portfolio — index + form (toggle destacado).
  • Pedidos de retrato — index + show con timeline visual de estados,
    transiciones, subida de boceto y versión final.
  • Productos — index + form con multi-imagen, variantes, SEO.
  • Reservas live art — index + calendario FullCalendar + show con generación
    de presupuesto/cotización según flujo.
  • Branding, Diseño, Eventos — CRUD completo cada uno.
  • Categorías, Testimonios, Cupones — CRUD.
  • Mensajes de contacto — index + show, leído/no leído, respuesta.
  • Usuarios — index + show con historial de pedidos.
  • Ajustes del sitio — formulario por pestañas: general, imágenes, e-commerce,
    retratos, arte en vivo.

### 5.9. Controladores Admin (12)
AdminLoginController, DashboardController, PortfolioAdminController,
PortraitOrderAdminController, ProductAdminController, BookingAdminController,
BrandingAdminController, DesignAdminController, EventAdminController,
CategoryAdminController, TestimonialAdminController, CouponAdminController,
ContactAdminController, UserAdminController, SettingsAdminController.

(Nota: el inventario enumera 12 nombres de archivo; verificar en repo si
AdminLogin está incluido en el conteo global de “12 admin controllers” frente
a listados que agrupan login por separado.)

### 5.10. Resultado de la fase
El negocio digital queda cerrado en bucle: cliente compra y paga; admin gestiona
catálogo, pedidos, reservas y comunicación; documentación PDF refuerza imagen
profesional.


## 6. FASE 5 — PWA, CLOUDINARY, N8N, CHATBOT Y TESTING (PROMPT 5)


### 6.1. PWA (Progressive Web App)
  • manifest.json — nombre, colores, iconos, display standalone/browser.
  • Service Worker (sw.js) — Estrategia network-first con fallback a caché para
    activos estáticos; página offline.html cuando no hay red.
  • Placeholders de iconos — sustituir en producción por arte final.
  • Meta en layout: theme-color, apple-mobile-web-app-capable, etc.

### 6.2. CloudinaryService
  • Subida y borrado de recursos.
  • Generación de URLs con transformaciones (thumbnails, portfolio).
  • Implementación sin SDK oficial: peticiones cURL puras para menor dependencia.

### 6.3. image_helper
  • optimizedImage() y lazyImage() — URLs optimizadas con fallback a archivos
    locales si Cloudinary no está configurado.

### 6.4. n8n con DDEV
  • docker-compose.n8n.yaml — servicio n8n expuesto típicamente en puerto 5678
    para diseñar workflows de automatización (emails externos, CRM, Slack, etc.).

### 6.5. WebhookController
Cuatro disparadores principales documentados en flujo de negocio:
  • Nuevo pedido de retrato.
  • Cambio de estado de pedido de retrato.
  • Nueva reserva de arte en vivo.
  • Nuevo mensaje de contacto.

Además, endpoint de “loyalty clients” u homólogo para listar o filtrar clientes
habituales según la implementación (para campañas desde n8n).

### 6.6. Integración en controladores existentes
PortraitOrderController, LiveArtBookingController y ContactController
invocan o registran eventos que alimentan webhooks hacia n8n cuando procede.

### 6.7. Documentación n8n
  • docs/n8n-workflows.md — Cuatro flujos detallados alineados con los webhooks
    (recomendaciones de nodos, datos esperados, manejo de errores).

### 6.8. Chatbot embebido
  • partials/chatbot.php — Widget fijo abajo a la derecha.
  • Ventana de chat con FAQ, indicador de escritura.
  • ChatbotController — Coincidencia local por palabras clave; fallback a n8n
    u OpenAI si está cableado; patrones de saludo.
  • chatbot.css / chatbot.js — Widget responsive y mensajes vía AJAX.

### 6.9. EmailService y vistas de correo
  • Cuatro métodos de correo: confirmación de pedido de tienda, cambio de estado
    de retrato, confirmación de reserva, notificación de nuevo contacto.
  • Entorno desarrollo: Mailhog integrado en DDEV para inspeccionar correos sin
    enviar a Internet.
  • Cuatro vistas HTML con CSS inline para compatibilidad con clientes de correo.

### 6.10. PHPUnit — 8 tests
Cobertura orientada a lógica crítica:
  • CartServiceTest — reglas de carrito y totales.
  • JWTServiceTest — emisión/validación de tokens.
  • PdfServiceTest — generación básica de documentos.
  • ApiHelperTest — helpers de respuesta API.
  • PortraitOrderFlowTest — transiciones y reglas de pedido retrato.
  • CartCheckoutTest — flujo de compra.
  • AuthTest — autenticación API/web según alcance.
  • ContactFormTest — formulario y validaciones/rate limit asociado.

### 6.11. Seguridad en public/.htaccess
Cabeceras recomendadas:
  • X-Content-Type-Options
  • X-Frame-Options
  • X-XSS-Protection
  • Referrer-Policy
Bloqueo de archivos sensibles, deshabilitar listado de directorios. En producción,
forzar HTTPS donde el hosting lo permita.

### 6.12. Resultado de la fase
Producto más “enterprise-ready”: instalable como PWA, imágenes escalables con
CDN, automatización externa, asistente en sitio, correos transaccionales y red
de pruebas automatizadas.


## 7. INVENTARIO COMPLETO DE ARCHIVOS (POR CATEGORÍA)


### 7.1. Configuración
  .env
  .ddev/config.yaml
  .ddev/docker-compose.n8n.yaml
  app/Config/Routes.php
  app/Config/Filters.php
  app/Config/Autoload.php

### 7.2. Base de datos
  app/Database/Migrations/     (18 archivos de migración)
  app/Database/Seeds/          (11 archivos seeder)
  app/Models/                  (18 modelos)

### 7.3. Librerías (7)
  app/Libraries/JWTService.php
  app/Libraries/CartService.php
  app/Libraries/StripeService.php
  app/Libraries/PdfService.php
  app/Libraries/ImageUploadService.php
  app/Libraries/CloudinaryService.php
  app/Libraries/EmailService.php

### 7.4. Helpers (2)
  app/Helpers/api_helper.php
  app/Helpers/image_helper.php

### 7.5. Controladores API (16)
  app/Controllers/Api/BaseApiController.php
  app/Controllers/Api/AuthController.php
  app/Controllers/Api/CategoryController.php
  app/Controllers/Api/PortraitStyleController.php
  app/Controllers/Api/PortraitSizeController.php
  app/Controllers/Api/ProductController.php
  app/Controllers/Api/PortfolioController.php
  app/Controllers/Api/PortraitOrderController.php
  app/Controllers/Api/OrderController.php
  app/Controllers/Api/CouponController.php
  app/Controllers/Api/LiveArtBookingController.php
  app/Controllers/Api/ContactController.php
  app/Controllers/Api/BrandingProjectController.php
  app/Controllers/Api/DesignProjectController.php
  app/Controllers/Api/EventController.php
  app/Controllers/Api/TestimonialController.php
  app/Controllers/Api/SettingsController.php
  app/Controllers/Api/DashboardController.php
  app/Controllers/Api/WebhookController.php
  app/Controllers/Api/ChatbotController.php

### 7.6. Controladores Web (13 en inventario detallado)
  app/Controllers/Web/HomeController.php
  app/Controllers/Web/PortfolioController.php
  app/Controllers/Web/RetratosController.php
  app/Controllers/Web/ArteEnVivoController.php
  app/Controllers/Web/BrandingController.php
  app/Controllers/Web/EventosController.php
  app/Controllers/Web/DisenoController.php
  app/Controllers/Web/ProductosController.php
  app/Controllers/Web/CartController.php
  app/Controllers/Web/ContactoController.php
  app/Controllers/Web/AuthWebController.php
  app/Controllers/Web/ClientDashboardController.php
  app/Controllers/Web/StripeWebhookController.php

### 7.7. Controladores Admin (12 archivos listados)
  app/Controllers/Admin/AdminLoginController.php
  app/Controllers/Admin/DashboardController.php
  app/Controllers/Admin/PortfolioAdminController.php
  app/Controllers/Admin/PortraitOrderAdminController.php
  app/Controllers/Admin/ProductAdminController.php
  app/Controllers/Admin/BookingAdminController.php
  app/Controllers/Admin/BrandingAdminController.php
  app/Controllers/Admin/DesignAdminController.php
  app/Controllers/Admin/EventAdminController.php
  app/Controllers/Admin/CategoryAdminController.php
  app/Controllers/Admin/TestimonialAdminController.php
  app/Controllers/Admin/CouponAdminController.php
  app/Controllers/Admin/ContactAdminController.php
  app/Controllers/Admin/UserAdminController.php
  app/Controllers/Admin/SettingsAdminController.php

### 7.8. Filtros (4)
  app/Filters/AuthFilter.php
  app/Filters/AdminFilter.php
  app/Filters/RateLimitFilter.php
  app/Filters/CorsFilter.php

### 7.9. Vistas públicas (35+)
  app/Views/layouts/main.php
  app/Views/layouts/admin.php
  app/Views/partials/navbar.php
  app/Views/partials/footer.php
  app/Views/partials/chatbot.php
  app/Views/web/home/index.php
  app/Views/web/portfolio/index.php
  app/Views/web/portfolio/show.php
  app/Views/web/retratos/index.php
  app/Views/web/retratos/configurador.php
  app/Views/web/arte-en-vivo/index.php
  app/Views/web/arte-en-vivo/reservar.php
  app/Views/web/branding/index.php
  app/Views/web/branding/show.php
  app/Views/web/eventos/index.php
  app/Views/web/eventos/show.php
  app/Views/web/diseno/index.php
  app/Views/web/diseno/show.php
  app/Views/web/productos/index.php
  app/Views/web/productos/show.php
  app/Views/web/cart/index.php
  app/Views/web/cart/checkout.php
  app/Views/web/cart/success.php
  app/Views/web/contacto/index.php
  app/Views/web/auth/login.php
  app/Views/web/auth/register.php
  app/Views/web/auth/forgot.php
  app/Views/web/auth/reset.php
  app/Views/web/client/dashboard.php
  app/Views/web/client/orders.php
  app/Views/web/client/order-detail.php
  app/Views/web/client/portraits.php
  app/Views/web/client/portrait-detail.php
  app/Views/web/client/profile.php

### 7.10. Vistas Admin (28)
  app/Views/admin/login.php
  app/Views/admin/dashboard.php
  app/Views/admin/portfolio/index.php
  app/Views/admin/portfolio/form.php
  app/Views/admin/portrait-orders/index.php
  app/Views/admin/portrait-orders/show.php
  app/Views/admin/products/index.php
  app/Views/admin/products/form.php
  app/Views/admin/bookings/index.php
  app/Views/admin/bookings/calendar.php
  app/Views/admin/bookings/show.php
  app/Views/admin/branding/index.php
  app/Views/admin/branding/form.php
  app/Views/admin/design/index.php
  app/Views/admin/design/form.php
  app/Views/admin/events/index.php
  app/Views/admin/events/form.php
  app/Views/admin/categories/index.php
  app/Views/admin/categories/form.php
  app/Views/admin/testimonials/index.php
  app/Views/admin/testimonials/form.php
  app/Views/admin/coupons/index.php
  app/Views/admin/coupons/form.php
  app/Views/admin/messages/index.php
  app/Views/admin/messages/show.php
  app/Views/admin/users/index.php
  app/Views/admin/users/show.php
  app/Views/admin/settings/index.php

### 7.11. Vistas de email (4)
  app/Views/emails/order_confirmation.php
  app/Views/emails/portrait_status.php
  app/Views/emails/booking_confirmation.php
  app/Views/emails/contact_notification.php

### 7.12. Vistas PDF (2)
  app/Views/pdf/invoice.php
  app/Views/pdf/quote.php

### 7.13. Activos públicos
  public/assets/css/custom.css
  public/assets/css/admin.css
  public/assets/js/app.js
  public/assets/js/cart.js
  public/assets/js/portrait-config.js
  public/assets/js/admin.js
  public/assets/js/chatbot.js
  public/manifest.json
  public/sw.js
  public/offline.html
  public/.htaccess
  (Iconos PWA — placeholders / scripts tipo generate-icons.php si existen en el repo)

### 7.14. Tests (8)
  tests/Unit/Libraries/CartServiceTest.php
  tests/Unit/Libraries/JWTServiceTest.php
  tests/Unit/Libraries/PdfServiceTest.php
  tests/Unit/Helpers/ApiHelperTest.php
  tests/Feature/PortraitOrderFlowTest.php
  tests/Feature/CartCheckoutTest.php
  tests/Feature/AuthTest.php
  tests/Feature/ContactFormTest.php

### 7.15. Documentación
  docs/n8n-workflows.md


## 8. STACK TECNOLÓGICO Y DEPENDENCIAS CLAVE


  • CodeIgniter 4.7.x — Framework MVC PHP.
  • PHP 8.4 — Runtime.
  • MariaDB 11.8 — Base de datos relacional.
  • DDEV v1.25+ sobre WSL2 — Entorno local containerizado.
  • Apache — Servidor HTTP.
  • Bootstrap 5.3.3 — UI responsive.
  • Bootstrap Icons 1.11.3 — Iconografía.
  • GSAP 3.12.5 + ScrollTrigger — Animación.
  • AOS 2.3.4 — Animación al scroll declarativa.
  • Particles.js 2.0.0 — Fondos de partículas.
  • Masonry 4.2.2 + imagesLoaded 5.0.0 — Grillas desiguales.
  • GLightbox 3.3.0 — Lightbox.
  • Chart.js — Gráficos en dashboard admin.
  • FullCalendar.js — Calendario de reservas admin.
  • Stripe — Pagos con tarjeta (PaymentIntents + webhooks).
  • dompdf — Render HTML → PDF.
  • firebase/php-jwt — Tokens JWT.
  • n8n — Automatización y orquestación vía webhooks.
  • Mailhog — SMTP de pruebas en DDEV.
  • PWA — Manifest + Service Worker + página offline.
  • Cloudinary (opcional) — CDN y transformaciones de imagen.


## 9. FLUJOS DE NEGOCIO RESUMIDOS (REFERENCIA RÁPIDA)


### 9.1. Cliente compra en la tienda
Navegación productos → añade al carrito (sesión) → checkout (datos envío) →
validación stock → creación de pedido → Stripe Elements cobra → webhook confirma
pago → email de confirmación → factura PDF disponible según política.

### 9.2. Cliente encarga un retrato
Configurador público calcula precio → solicitud/pedido en estado inicial
(quote u homólogo) → aceptación y recepción de fotos → trabajo en curso →
revisiones → entrega → cierre. Historial de estados en tabla de auditoría.
Emails en hitos clave. Webhooks opcionales a n8n.

### 9.3. Reserva de arte en vivo
Formulario de evento/fecha/ubicación → registro en live_art_bookings → panel
admin con calendario → generación de presupuesto/cotización PDF si aplica →
email de confirmación → webhook a automatizaciones.

### 9.4. Contacto
Formulario con honeypot y categoría → rate limit → almacenamiento en
contact_messages → notificación por email → gestión en admin (leído/responder).

### 9.5. Administración
Login admin separado → dashboard con KPIs → CRUD por módulo → subida de
imágenes (local o Cloudinary según config) → ajustes globales por pestañas.


## 10. NOTAS DE DESPLIEGUE A PRODUCCIÓN


  1. Sustituir claves de prueba de Stripe por claves live y configurar endpoint
     de webhook público con secreto de firma correcto.
  2. Configurar credenciales Cloudinary (cloud name, API key, secret) y políticas
     de carpeta/transformaciones acordes al uso.
  3. Desplegar n8n (o instancia gestionada) y actualizar URLs de webhooks en
     WebhookController / settings para apuntar a producción.
  4. Definir JWT_SECRET largo y aleatorio; rotar si hubo compromiso.
  5. En .htaccess o proxy reverso, forzar HTTPS y cabeceras de seguridad
     adicionales (HSTS donde corresponda).
  6. Cambiar CI_ENVIRONMENT a production y desactivar exposición de errores.
  7. Generar iconos PWA definitivos (512/192/maskable) y actualizar manifest.
  8. Configurar SMTP real (SendGrid, SES, Mailgun, servidor propio) y retirar
     dependencia de Mailhog fuera de desarrollo.
  9. Backups automáticos de MariaDB y de archivos subidos (writable/uploads o
     bucket si se externaliza).
  10. Revisión de permisos de carpetas writable/ y políticas de CORS en producción.


## 11. MANTENIMIENTO Y EXTENSIONES FUTURAS SUGERIDAS


  • Añadir más tests de integración (API end-to-end con base de datos de prueba).
  • Internacionalización (i18n) si el mercado no es solo español.
  • Panel de cliente con descarga de facturas y seguimiento de envíos (tracking).
  • Integración con pasarelas adicionales (PayPal, Bizum vía proveedor, etc.).
  • CDN para assets estáticos y compresión Brotli en el servidor.
  • Monitorización (Sentry, OpenTelemetry) para errores y rendimiento.
  • Hard rate limits en reverse proxy (nginx/Cloudflare) además del filtro PHP.


## 12. GLOSARIO BREVE


  API REST      Interfaz HTTP JSON para clientes móviles, SPAs o integraciones.
  JWT           Token firmado para autenticación stateless.
  DDEV          Herramienta de desarrollo local basada en Docker.
  PWA           App web instalable con offline limitado vía Service Worker.
  Webhook       HTTP callback disparado por eventos de negocio hacia n8n u otros.
  PaymentIntent Objeto Stripe que representa un intento de cobro confirmable.
  IVA           Impuesto al valor añadido (21 % en la lógica de ejemplo indicada).


## 13. APÉNDICE A — DETALLE AMPLIADO POR DOMINIO DE DATOS


### 13.1. Usuarios (users)
Propósito: identidad digital del visitante registrado y del administrador.
Campos típicos en este tipo de sistemas: email único, hash de contraseña,
nombre para mostrar, rol (admin/client), timestamps. El uso de bcrypt reduce
riesgo ante filtraciones de base de datos (el atacante debe fuerza bruta con
coste elevado por hash). En producción conviene política de contraseñas,
opcional 2FA en roadmap, y bloqueo progresivo ante intentos fallidos (a nivel
aplicación o WAF).

### 13.2. Categorías (categories)
Sirven para filtrar portfolio en la vista pública y para ordenar contenido en
admin. Un slug estable por categoría mejora URLs amigables y SEO. La relación
con obras o productos depende del modelo de datos concreto de cada migración.

### 13.3. Estilos y tamaños de retrato
portrait_styles fija el “precio base” creativo del estilo (más horas de trabajo
o complejidad visual). portrait_sizes aplica multiplicadores o sumandos para
formato físico mayor (más superficie, más detalle). El configurador del frontend
combina ambos más opciones (número de figuras, marco) para mostrar un precio
orientativo antes de que el artista confirme el presupuesto final.

### 13.4. Pedido de retrato y auditoría
portrait_orders concentra: referencia al cliente, estilo/tamaño elegidos,
importes calculados o acordados, notas, archivos asociados (rutas o IDs de
media), estado actual. portrait_order_status_history guarda quién/cuándo/qué
para disputas, soporte y analítica de tiempos de ciclo (lead time por fase).

### 13.5. Productos, imágenes y variantes
products es la entidad de venta: título, descripción rica, slug, SEO.
product_images permite ordenar la galería (portada, secundarias). product_variants
modela SKUs: combinaciones con stock numérico y precio; el checkout debe
decrementar stock de forma atómica o con transacciones para evitar overselling
bajo concurrencia.

### 13.6. Pedidos de tienda (orders / order_items)
orders almacena estado del pedido de e-commerce, totales, dirección, método de
pago referenciado a Stripe, flags de pago. order_items enlaza cantidad, precio
unitario snapshot (importante: congelar precio al comprar aunque el catálogo
cambie después), y la variante elegida.

### 13.7. Cupones (coupons)
Reglas de negocio habituales: porcentaje vs importe fijo, fecha inicio/fin,
máximo de usos globales o por usuario, mínimo de compra. El CartService valida
el cupón antes de aplicar descuento al total. En admin se gestionan altas y
pausas de campañas.

### 13.8. Reservas live art (live_art_bookings)
Capturan tipo de evento (boda, empresa, cumpleaños…), fecha/hora, ubicación,
notas logísticas, estado administrativo y posible presupuesto. El calendario
FullCalendar en admin da vista mensual/semanal para evitar solapes y planificar
desplazamientos del artista.

### 13.9. Portfolio y showcases (portfolio_works, branding, diseño, eventos)
portfolio_works: piezas artísticas con imagen destacada, texto, categoría,
posible flag “destacado” en home. Las tablas branding_projects, design_projects
y events documentan casos de estudio de servicios profesionales con páginas
públicas index/show y formularios admin paralelos.

### 13.10. Testimonios y contacto
testimonials alimentan prueba social. contact_messages recogen leads; el rate
limit reduce spam y scraping. El honeypot en vista pública filtra bots simples.

### 13.11. Ajustes del sitio (site_settings)
Patrón clave-valor o JSON serializado por clave: textos legales, teléfonos,
enlaces a redes, toggles de funcionalidades, claves públicas (p. ej. Stripe
publishable) si se decidió centralizarlas (nunca almacenar secretos en settings
sin cifrado). La UI admin por pestañas agrupa dominios: general, medios,
e-commerce, retratos, live art.


## 14. APÉNDICE B — CAPA API: PATRONES Y RESPONSABILIDADES


### 14.1. BaseApiController
Centraliza formato de respuesta, códigos HTTP semánticos, posible captura de
excepciones y logging. Evita duplicar try/catch en cada método de cada
controlador.

### 14.2. Autenticación y autorización
Login devuelve access token (corta duración) y refresh token (larga duración o
rotación). Rutas de cliente requieren AuthFilter. Operaciones destructivas o de
listado global requieren AdminFilter. En producción, almacenar refresh tokens
con revocación (lista negra o tabla de sesiones) si el modelo de amenaza lo exige.

### 14.3. Validación y reglas de negocio
Los modelos CI4 declaran reglas; los controladores pueden añadir reglas de
dominio (p. ej. “no pasar de delivered a quote”). PortraitOrderController es el
punto crítico de la máquina de estados: invalidar transiciones ilegales y
devolver mensajes claros en español para integraciones.

### 14.4. CORS
CorsFilter permite llamadas desde un frontend SPA alojado en otro subdominio.
En producción, restringir orígenes a dominios conocidos, no usar * con credenciales.

### 14.5. Rate limiting
RateLimitFilter típicamente usa IP + ruta o IP + usuario. Ajustar umbrales para
contacto y registro masivo. Complementar con CAPTCHA si el abuso persiste.

### 14.6. Webhooks salientes (fase 5)
WebhookController construye payloads JSON con IDs, emails, importes, estados.
n8n recibe y ramifica: Slack, Google Sheets, CRM, envío de SMS vía proveedor, etc.
La idempotencia en n8n (evitar duplicar acciones) se resuelve con deduplicación
por ID de pedido y nodo “IF” sobre estado previo.

### 14.7. Chatbot API
Primera capa: FAQ local por keywords (rápido, sin coste). Segunda capa: n8n u
OpenAI para preguntas abiertas. Importante sanitizar entrada y limitar longitud
para evitar prompt injection si se conecta a LLM.


## 15. APÉNDICE C — FRONTEND PÚBLICO: EXPERIENCIA Y COMPONENTES


### 15.1. Sistema de diseño implícito
Variables CSS permiten cambiar acento dorado o tipografías en un solo lugar.
El contraste entre #1a1a1a y #f5f3f0 debe cumplir WCAG en textos largos; los
titulares decorativos pueden ser más expresivos.

### 15.2. Rendimiento percibido
Preloader evita parpadeo FOUC en fuentes o CSS pesado. Lazy loading reduce bytes
iniciales en portfolio. imagesLoaded evita saltos de layout al completar Masonry.

### 15.3. Accesibilidad (mejoras continuas)
Bootstrap aporta foco y roles básicos; revisar contraste en botones dorados,
etiquetas en formularios, y navegación por teclado en dropdowns y modal del
chatbot.

### 15.4. Carrito y checkout (fase 4)
cart.js encapsula llamadas AJAX al servidor para sincronizar sesión. Stripe
Elements mantiene datos sensibles en iframe seguro; el backend solo maneja
PaymentIntent ID y estado.

### 15.5. Configurador de retratos
portrait-config.js escucha cambios en selects y sliders; recalcula en cliente
para UX instantánea y opcionalmente valida contra API de precios si los precios
son dinámicos por promoción.

### 15.6. Área cliente
Separación clara entre pedidos de tienda (Order) y encargos artísticos
(PortraitOrder). El detalle debe mostrar timeline amigable y enlaces a descarga
de archivos cuando el estado lo permita.


## 16. APÉNDICE D — PANEL DE ADMINISTRACIÓN: OPERACIÓN DIARIA


### 16.1. Flujo típico del artista / gestor
(1) Revisar dashboard por ingresos y alertas. (2) Atender mensajes no leídos.
(3) Confirmar reservas en calendario. (4) Avanzar pedidos de retrato subiendo
bocetos. (5) Actualizar stock de variantes tras inventario físico. (6) Crear
cupón temporal para campaña en redes.

### 16.2. Gestión de medios
Subida local vs Cloudinary: en desarrollo basta disco; en producción Cloudinary
entrega resize on the fly, format auto (webp/avif), y CDN geográfico.

### 16.3. DataTables
Listados largos (productos, usuarios) ganan búsqueda, ordenación y paginación
sin recargar página completa; coherente con admin.js.

### 16.4. FullCalendar
Sincroniza con API de bookings; colores por estado (pendiente/confirmado/cancelado)
si se implementó en vistas.

### 16.5. PDFs
dompdf tiene limitaciones con CSS avanzado; las plantillas invoice/quote usan
tablas y estilos inline compatibles. Probar con caracteres UTF-8 (eñes, símbolo €).


## 17. APÉNDICE E — PWA, OFFLINE Y CACHÉ


### 17.1. manifest.json
Define cómo se instala la web en móvil: nombre corto, iconos, color de barra de
estado, orientación. Los iconos maskable evitan recortes feos en Android.

### 17.2. Service Worker (sw.js)
Network-first sirve HTML fresco; fallback a caché para CSS/JS/imágenes estáticas.
offline.html informa al usuario si la API no está disponible. Invalidar caché al
desplegar nueva versión (cambiar versión en install event o usar hash de build).

### 17.3. Riesgos
No cachear respuestas API con datos personales sin estrategia estricta; el SW
debe excluir /api/ o usar network-only para rutas sensibles.


## 18. APÉNDICE F — CORREO ELECTRÓNICO (Mailhog → Producción)


### 18.1. Desarrollo
Mailhog captura todos los correos salientes de PHP mail() o SMTP configurado en
DDEV; UI web para inspeccionar HTML y cabeceras.

### 18.2. Plantillas
CSS inline es obligatorio en muchos clientes (Outlook). Probar en Gmail, Apple
Mail y móviles. Incluir texto plano alternativo si EmailService lo soporta.

### 18.3. Producción
SPF, DKIM y DMARC en DNS del dominio remitente mejoran entregabilidad. Usar colas
(redis + worker) si el volumen crece, para no bloquear requests HTTP.


## 19. APÉNDICE G — PRUEBAS (PHPUNIT) Y CALIDAD


### 19.1. Pirámide de tests
Unit tests sobre librerías puras (CartService, JWT, Pdf helper paths). Feature
tests sobre rutas y base de datos en memoria o test DB. Aumentar cobertura en
PortraitOrderController y StripeWebhook con mocks de Stripe SDK/eventos.

### 19.2. Datos de prueba
Usar entorno .env.testing y migraciones + seeds mínimos para reproducibilidad.

### 19.3. CI/CD sugerido
Pipeline que ejecute composer install, phpunit, y opcionalmente phpstan/psalm
para análisis estático.


## 20. APÉNDICE H — SEGURIDAD EN PROFUNDIDAD


### 20.1. Aplicación
CSRF en formularios web; allowedFields en modelos; escapar salidas en vistas;
validar tipos en API; no filtrar stack traces en producción.

### 20.2. Servidor
Permisos mínimos en writable/, deshabilitar listado de directorios (ya en
.htaccess), actualizar PHP y extensiones, firewall solo 80/443.

### 20.3. Pagos
Verificar firma de webhooks Stripe; idempotencia al marcar pedidos pagados;
nunca confiar solo en el cliente (success page) sin confirmación servidor.

### 20.4. Secretos
.env fuera de git; usar variables en hosting; rotar JWT y API keys periódicamente.


## 21. APÉNDICE I — VARIABLES Y CHECKLIST DE ENTORNO (.env)


Revisar en cada entorno (local/staging/prod):

  [ ] database.default.hostname, database, username, password, port
  [ ] app.baseURL alineado con dominio y HTTPS
  [ ] CI_ENVIRONMENT (development | production)
  [ ] encryption.key de CodeIgniter (única y secreta)
  [ ] JWT_SECRET robusto
  [ ] stripe keys (publicable vs secreta) y webhook secret
  [ ] cloudinary cloud_name, api_key, api_secret (si aplica)
  [ ] mail: SMTP host, usuario, contraseña, puerto, encryption
  [ ] n8n base URL o tokens si el backend llama a n8n
  [ ] Opcional: OPENAI_API_KEY u otros proveedores del chatbot


## 22. APÉNDICE J — COMANDOS ÚTILES DDEV (REFERENCIA)


  ddev start / ddev stop     — Ciclo de vida del proyecto
  ddev ssh                   — Shell dentro del contenedor web
  ddev exec php spark migrate — Ejecutar migraciones CI4
  ddev exec php spark db:seed — Poblar datos (según DatabaseSeeder)
  ddev describe              — URLs, puertos, credenciales
  ddev logs                  — Depuración de servicios

(Ajustar nombres de comandos spark según comandos personalizados registrados.)


## 23. APÉNDICE K — CRONOLOGÍA DE LAS 5 FASES (RESUMEN EJECUTIVO)


  Fase 1 — Cimientos: DDEV + CI4 + .env + estructura + URL funcionando.
  Fase 2 — Motor de datos y API: 18 migraciones, modelos, seeders, JWT, filtros,
           100+ rutas, controladores REST base.
  Fase 3 — Cara pública: layout rico, CSS/JS, páginas de servicios, auth web,
           área cliente, configurador y portfolio.
  Fase 4 — Monetización y gestión: carrito, Stripe, PDF, admin completo con
           KPIs, calendario, CRUD total.
  Fase 5 — Producto y operaciones: PWA, Cloudinary, n8n, webhooks, chatbot,
           emails, PHPUnit, hardening .htaccess.


## FIN DEL DOCUMENTO — nmonzzon Studio — Resumen completo del proyecto