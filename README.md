# `PROYECTO NMONZZON STUDIO 🎨`

> [!NOTE]
> ***NMONZZON STUDIO es una aplicación web full-stack para la artista NMONZZON: portafolio, tienda con Stripe, configurador de retratos, reservas de arte en vivo, servicios de branding, diseño y eventos, panel administrativo completo, PWA, chatbot, API REST con JWT, automatización n8n y correo con Mailhog en desarrollo.***
> ***Está desarrollada con CodeIgniter 4.7.x, PHP 8.4 y MariaDB 11.8, ejecutándose de forma recomendada mediante DDEV sobre WSL2.***

Repositorio remoto: <https://github.com/21saul/PROYECTO_FINAL_NMONZZON>

---

## `ÍNDICE 📑`

1. [Visión general del producto](#visión-general-del-producto)
2. [Últimas mejoras de UX/seguridad](#últimas-mejoras-de-uxseguridad)
3. [Estructura del proyecto](#estructura-del-proyecto-)
4. [Tecnologías utilizadas](#tecnologías-utilizadas-️)
5. [Cómo arrancar el proyecto](#cómo-arrancar-el-proyecto-)
6. [Base de datos](#base-de-datos-️)
7. [API REST](#api-rest-)
8. [Autenticación JWT](#autenticación-jwt-)
9. [Frontend público](#frontend-público-)
10. [E-commerce y Stripe](#e-commerce-y-stripe-)
11. [Panel de administración](#panel-de-administración-️)
12. [PWA](#pwa---progressive-web-app-)
13. [Chatbot](#chatbot-)
14. [n8n - automatización](#n8n---automatización-)
15. [Emails con Mailhog](#emails-con-mailhog-)
16. [Cloudinary](#cloudinary---imágenes-️)
17. [Testing con PHPUnit](#testing-con-phpunit-)
18. [Seguridad](#seguridad-️)
19. [Configuración para producción](#configuración-para-producción-)
20. [Comandos de referencia rápida](#comandos-de-referencia-rápida-)

---

## `Visión general del producto`

| MÓDULO | FUNCIÓN DE NEGOCIO |
| --- | --- |
| ***Portafolio*** | Muestra obras y proyectos con galería rica (masonry, GLightbox) y animaciones. |
| ***Retratos a medida*** | Configurador web que calcula precio según estilo, tamaño y opciones; flujo de pedido y seguimiento. |
| ***E-commerce*** | Productos con variantes, carrito, cupones, checkout y pago con Stripe. |
| ***Arte en vivo*** | Captura de eventos para pintura en directo con gestión en admin y calendario. |
| ***Servicios*** | Landings y fichas por slug para branding, diseño gráfico y eventos. |
| ***Admin*** | CRUDs, dashboard analítico, gestión de usuarios y ajustes del sitio. |
| ***PWA*** | Instalable, cache básico y página offline. |
| ***Integraciones*** | n8n vía webhook, Cloudinary opcional, DomPDF para PDF. |

***El proyecto se desarrolló en 5 fases (prompts):***
- **Prompt 1:** Entorno DDEV/WSL2, CodeIgniter 4 con Composer.
- **Prompt 2:** Base de datos (18 migraciones), modelos, API REST completa con JWT.
- **Prompt 3:** Frontend público con Bootstrap 5, GSAP, AOS, particles.js, Masonry, GLightbox.
- **Prompt 4:** E-commerce Stripe, carrito, checkout, facturas PDF, panel de administración completo.
- **Prompt 5:** PWA, Cloudinary, n8n, chatbot, EmailService, PHPUnit, seguridad .htaccess.

```text
# FLUJO RESUMIDO DEL VISITANTE
# 1) Llegada al home → navegación a portafolio, retratos o tienda.
# 2) Si compra o encarga retrato → login/registro → checkout o confirmación.
# 3) Admin recibe eventos (email, n8n) y actualiza estados en el panel.
```

---

## `Últimas mejoras de UX/seguridad`

> [!TIP]
> Iteración centrada en limpieza visual, galerías de alto rendimiento y un captcha realmente resistente a bots básicos.

### Cabeceras planas en "Carrito" y "Mi cuenta"
- Eliminadas las cabeceras con imagen de fondo en:
  - `app/Views/web/cart/index.php`
  - `app/Views/web/client/{dashboard,profile,orders,portraits,order-detail,portrait-detail}.php`
- Sustituidas por una cabecera `nmz-page-header` plana con breadcrumbs `--on-light` y título `nmz-page-hero__title--on-light`.
- `app/Views/web/client/profile.php` ahora usa una `profile-identity-card--flat` (variante horizontal ≥768 px, sin sombra ni borde) y `public/assets/css/profile-account.css` añade el modificador y un reset de `margin-top` para evitar el overlap heredado del hero antiguo.

### Arte en Vivo
- CTAs de la sección *"¿Qué es el Arte en Vivo?"* movidos fuera de la columna de texto a una fila centrada al pie (`btn-lg` + `data-aos="fade-up"`).
- Título del formulario cambiado a *"Reserva el servicio Arte en Vivo para tu evento"* (el componente `.section-title` aplica `text-transform: uppercase`, de modo que el render final es `RESERVA EL SERVICIO ARTE EN VIVO PARA TU EVENTO`).
- Nuevo bloque CTA reforzado en *"Cómo funciona"* con dos botones (`Reserva ahora` + `Ver portafolio`).

### Carrusel horizontal *masonry* en galerías
- Nuevo componente `ret-hmasonry` en `public/assets/css/custom.css`:
  - `grid-auto-flow: column` con dos filas de altura fija (`clamp(150px, 22vw, 230px)` en desktop).
  - Piezas con `aspect-ratio` dedicado por variante: `--wide` 4:3, `--half` 1:1, `--tall` 3:5 (ocupa las dos filas).
  - Fade en los bordes, `scroll-snap-type: x proximity` y focus visible accesible.
- `public/assets/js/gallery-carousel.js` (nuevo) vincula `[data-hmasonry]`: un solo botón **"Ver más"** desplaza el track un 85 % del viewport a la derecha (y vuelve al inicio al llegar al final). Soporta teclado ← →.
- Aplicado en `app/Views/web/retratos/index.php` y `app/Views/web/arte-en-vivo/index.php`.

### CAPTCHA de puzzle deslizante
Sustituye el antiguo captcha matemático (sumas/restas), trivialmente bypassable.

| Fichero | Rol |
| --- | --- |
| `app/Helpers/captcha_helper.php` | Genera token, guarda `{x, y, size, w, h, seed, t}` en sesión; `nmz_captcha_verify()` valida la X con tolerancia ±6 px y consume el token. |
| `app/Controllers/Web/CaptchaController.php` | Renderiza el PNG de fondo y de pieza con GD (gradiente + blobs + ruido + 2 decoys), y expone `GET /captcha/refresh` como JSON. |
| `app/Config/Routes.php` | Añade `GET /captcha/bg/:token`, `GET /captcha/piece/:token`, `GET /captcha/refresh`. |
| `app/Views/partials/captcha.php` | UI del stage + pieza absoluta + slider con `role="slider"`, teclado y botón *Nuevo desafío*. |
| `public/assets/js/captcha-puzzle.js` | Drag con mouse/touch/teclado, normaliza la X de pantalla a píxeles del servidor y la escribe en `captcha_answer`. |
| `public/assets/css/custom.css` | Estilos `.nmz-captcha-puzzle__*` con aspect-ratio, estados `--active/--ok/--err` y `touch-action: none`. |

Los controladores `ContactoController::send()` y `ArteEnVivoController::processReserva()` conservan los campos `captcha_token` + `captcha_answer` → el upgrade es transparente en el backend.

> [!IMPORTANT]
> La X secreta **nunca** llega al cliente: sólo aparece como píxeles dentro del PNG. El fondo procedural + los dos decoys + el ruido exigen visión por computador para intentar un bypass.

---

## `Estructura del proyecto 📁`

> [!CAUTION]
> ***No elimines carpetas como `app/`, `public/` o `writable/` sin revisar la configuración de CodeIgniter 4. El docroot en DDEV debe apuntar a `public/`.***

```
app/
├── Config/
│   ├── Routes.php              # Rutas web, admin y API (100+ rutas)
│   ├── Filters.php             # Filtros auth, admin, CORS, rate limit, CSRF
│   └── Autoload.php            # Helpers registrados: api, image, captcha
├── Controllers/
│   ├── Api/                    # 20 controladores API REST
│   ├── Web/                    # 14 controladores web públicos (incluye CaptchaController)
│   └── Admin/                  # 15 controladores admin
├── Database/
│   ├── Migrations/             # 18 migraciones
│   └── Seeds/                  # 11 seeders
├── Filters/                    # 4 filtros de seguridad
│   ├── AuthFilter.php
│   ├── AdminFilter.php
│   ├── RateLimitFilter.php
│   └── CorsFilter.php
├── Helpers/
│   ├── api_helper.php
│   ├── captcha_helper.php      # Puzzle captcha: generate / entry / verify
│   └── image_helper.php
├── Libraries/                  # 7 librerías personalizadas
│   ├── JWTService.php
│   ├── CartService.php
│   ├── StripeService.php
│   ├── PdfService.php
│   ├── ImageUploadService.php
│   ├── CloudinaryService.php
│   └── EmailService.php
├── Models/                     # 18 modelos
└── Views/
    ├── layouts/
    │   ├── main.php            # Layout público (Bootstrap 5, GSAP, AOS, particles, captcha-puzzle.js)
    │   └── admin.php           # Layout admin (sidebar, topbar, Chart.js, FullCalendar)
    ├── partials/
    │   ├── navbar.php
    │   ├── footer.php
    │   ├── captcha.php         # UI del puzzle (reutilizable)
    │   └── chatbot.php         # Widget chatbot
    ├── web/                    # 35+ vistas públicas
    ├── admin/                  # 28 vistas admin
    ├── emails/                 # 4 plantillas email HTML
    └── pdf/                    # 2 plantillas PDF (factura, presupuesto)

public/
├── assets/
│   ├── css/
│   │   ├── custom.css          # +2000 líneas CSS personalizado (ret-hmasonry, captcha, layouts)
│   │   ├── profile-account.css # Perfil de cliente y variante --flat
│   │   └── admin.css           # CSS panel admin
│   └── js/
│       ├── app.js              # JS global + Service Worker registration
│       ├── cart.js             # Carrito AJAX
│       ├── captcha-puzzle.js   # Drag + teclado + refresh del nuevo captcha
│       ├── gallery-carousel.js # Carrusel horizontal masonry (Retratos/Arte en vivo)
│       ├── portrait-config.js  # Configurador de retratos
│       ├── retratos-carousel.js# Marquee de estilos
│       └── admin.js            # JS panel admin
├── manifest.json               # Manifest PWA
├── sw.js                       # Service Worker
├── offline.html                # Página offline
└── .htaccess                   # Seguridad + rewrite

tests/
├── Unit/
│   ├── Libraries/              # CartService, JWTService, PdfService, LoyaltyClients, ScheduledTask
│   └── Helpers/
└── Feature/                    # PortraitOrderFlow, CartCheckout, Auth, ContactForm

.ddev/
├── config.yaml
└── docker-compose.n8n.yaml     # Servicio n8n

docs/
├── RESUMEN.md                  # Resumen exhaustivo (fuente)
├── RESUMEN_COMPLETO_PROYECTO.md
├── ESTRUCTURA_BACKEND.md
├── CRONS.md                    # Tareas programadas
└── n8n-workflows.md            # Documentación flujos n8n
```

---

## `Tecnologías utilizadas 🛠️`

| TECNOLOGÍA | VERSIÓN | USO |
| --- | --- | --- |
| ***CodeIgniter*** | ***4.7.x*** | Framework MVC PHP |
| ***PHP*** | ***8.4*** | Lenguaje del servidor (requerido: `ext-gd` para el captcha) |
| ***MariaDB*** | ***11.8*** | Base de datos relacional |
| ***DDEV*** | ***actual*** | Entorno local (contenedores) |
| ***WSL2*** | ***-*** | Ejecución Linux en Windows |
| ***Bootstrap*** | ***5.3.3*** | UI responsive |
| ***Bootstrap Icons*** | ***compatible 5.x*** | Iconografía |
| ***GSAP + ScrollTrigger*** | ***3.12.5 CDN*** | Animaciones avanzadas |
| ***AOS*** | ***2.3.4 CDN*** | Animaciones al hacer scroll |
| ***particles.js*** | ***2.0.0 CDN*** | Fondo particulado |
| ***Masonry + imagesLoaded*** | ***4.2 / 5.0 CDN*** | Rejilla portafolio |
| ***GLightbox*** | ***3.3.0 CDN*** | Visor de imágenes |
| ***Chart.js*** | ***asset*** | Gráficos en dashboard admin |
| ***FullCalendar*** | ***asset*** | Calendario de reservas |
| ***Stripe*** | ***API*** | Pagos en checkout |
| ***DomPDF*** | ***composer*** | Generación de PDF |
| ***firebase/php-jwt*** | ***composer*** | Tokens JWT |
| ***n8n*** | ***docker compose*** | Automatización y webhooks |
| ***Cloudinary*** | ***opcional*** | CDN de imágenes |
| ***Mailhog*** | ***DDEV*** | Captura de email en desarrollo |
| ***PHPUnit*** | ***dev*** | Pruebas automatizadas |

---

## `Cómo arrancar el proyecto 🚀`

> [!CAUTION]
> ***Es necesario tener Docker, DDEV y WSL2 configurados. Sin ellos los comandos DDEV no funcionarán en Windows.***

### `Paso 1: Clonar en WSL`

```bash
cd /home/ddev/www
git clone https://github.com/21saul/PROYECTO_FINAL_NMONZZON.git nmzonzzonstudio
cd nmzonzzonstudio
```

### `Paso 2: Configurar e iniciar DDEV`

```bash
# Si aún no existe configuración DDEV:
ddev config --project-type=php --docroot=public

# Levantar servicios web + DB + router:
ddev start
```

### `Paso 3: Instalar dependencias PHP`

```bash
ddev composer install
```

> [!IMPORTANT]
> ***El archivo `.env` no debe subirse al repositorio. Copia `env` a `.env` y ajusta claves de Stripe, JWT, n8n y Cloudinary.***

### `Paso 4: Variables de entorno (.env)`

```bash
cp env .env
```

```env
# Entorno de ejecución
CI_ENVIRONMENT = development

# URL base (sustituir por tu hostname DDEV)
app.baseURL = 'https://nmzonzzonstudio.ddev.site/'

# Base de datos DDEV por defecto
database.default.hostname = db
database.default.database = db
database.default.username = db
database.default.password = db
database.default.DBDriver = MySQLi
database.default.port = 3306

# Ejemplos de claves (completar con valores reales)
# stripe.secretKey = sk_test_...
# stripe.publishableKey = pk_test_...
# jwt.secret = <CADENA_LARGA_ALEATORIA>
# N8N_WEBHOOK_URL = http://host.docker.internal:5678/webhook
# N8N_WEBHOOK_SECRET = <SECRETO_COMPARTIDO>
```

### `Paso 5: Migraciones y seeders`

```bash
ddev exec php spark migrate
ddev exec php spark migrate:status
# Ejemplo de seeder:
# ddev exec php spark db:seed NombreSeeder
```

### `Paso 6: Servicios adicionales (n8n)`

```bash
# Tras añadir docker-compose.n8n.yaml en .ddev/
ddev restart
# n8n típicamente en http://localhost:5678 (ver docs/n8n-workflows.md)
```

### `Paso 7: Verificar GD (requisito del captcha)`

```bash
ddev exec php -m | grep -i gd
# Debe mostrar "gd". Si no, añade php-gd al Dockerfile de DDEV.
```

### `Paso 8: Abrir en el navegador`

```bash
ddev launch
# o visita manualmente https://nmzonzzonstudio.ddev.site
```

---

## `Base de datos 🗄️`

> [!NOTE]
> El esquema se genera con las migraciones en `app/Database/Migrations/`. A continuación se listan las 18 tablas principales; las tablas de imágenes y líneas de pedido completan las relaciones 1:N.

| Nº | TABLA | PROPÓSITO |
| --- | --- | --- |
| 1 | `users` | Clientes y administradores (auth web y API) |
| 2 | `auth_tokens` | Refresh tokens y metadatos de sesión API |
| 3 | `categories` | Categorías de productos de la tienda |
| 4 | `portrait_styles` | Estilos artísticos para encargos de retrato |
| 5 | `portrait_sizes` | Formatos y precios base del configurador |
| 6 | `portrait_orders` | Pedidos de retrato con estado y precios calculados |
| 7 | `portrait_order_status_history` | Auditoría de cambios de estado |
| 8 | `products` | Artículos de la tienda (slug, precio, stock lógico) |
| 9 | `product_images` | Galería de imágenes por producto |
| 10 | `product_variants` | Variantes (talla, acabado, SKU) |
| 11 | `portfolio_works` | Obras del portafolio público |
| 12 | `live_art_bookings` | Reservas de arte en vivo y datos del evento |
| 13 | `branding_projects` | Casos de branding con slug y metadatos |
| 14 | `branding_project_images` | Imágenes asociadas a proyectos de branding |
| 15 | `design_projects` | Casos de diseño gráfico / identidad |
| 16 | `design_project_images` | Imágenes de proyectos de diseño |
| 17 | `events` | Servicios y eventos mostrados en /eventos |
| 18 | `event_images` | Imágenes de fichas de evento |

Tablas transaccionales y de soporte: `orders`, `order_items`, `contact_messages`, `coupons`, `testimonials`, `site_settings`.

---

## `API REST 🔌`

> [!TIP]
> La API está agrupada bajo el prefijo `/api`. Las rutas administrativas usan el subgrupo `/api/admin/`. Las peticiones protegidas requieren `Authorization: Bearer <token>`.

**Autenticación y recursos públicos (extracto):**
- `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/refresh`, `POST /api/auth/forgot-password`, `POST /api/auth/reset-password`
- `GET /api/categories`, `/api/portrait-styles`, `/api/portrait-sizes`, `/api/products`, `/api/portfolio`, `/api/branding`, `/api/design`, `/api/events`, `/api/testimonials`
- `POST /api/contact`, `/api/chatbot`, `/api/live-art-bookings`
- `GET /api/webhooks/loyalty-clients` (consumido por n8n)

**Rutas autenticadas (JWT):**
- Perfil: `GET/PUT /api/auth/profile`, `POST /api/auth/logout`
- Retratos: `POST/GET/GET /api/portrait-orders[/{id}][/history]`, `POST /api/portrait-orders/{id}/reference-photo`
- Tienda: `POST/GET /api/orders[/{id}]`, `GET /api/orders/{id}/invoice`
- Cupones: `POST /api/coupons/validate`

**Rutas admin (JWT ADMIN):** CRUD completo en categorías, estilos, productos, portafolio, branding, diseño, eventos, testimonios, cupones, pedidos, reservas y dashboard (`/api/admin/dashboard/{stats,revenue,orders-by-style,top-products}`).

```bash
# Ejemplo: login
curl -s -X POST "https://nmzonzzonstudio.ddev.site/api/auth/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"cliente@ejemplo.com","password":"********"}'

# Ejemplo: recurso protegido
curl -s "https://nmzonzzonstudio.ddev.site/api/auth/profile" \
  -H "Authorization: Bearer <ACCESS_TOKEN>"
```

El listado completo de endpoints se mantiene en [`docs/RESUMEN.md`](docs/RESUMEN.md).

---

## `Autenticación JWT 🔐`

1. El cliente envía credenciales a `/api/auth/login`.
2. El servidor valida usuario y emite *access token* (corta duración) y *refresh token* (larga duración / rotativo).
3. Las peticiones autenticadas incluyen `Authorization: Bearer <ACCESS_TOKEN>`.
4. Si el access token expira, se usa `/api/auth/refresh` con el refresh token válido.
5. `POST /api/auth/logout` invalida tokens en servidor (tabla `auth_tokens`).

> [!IMPORTANT]
> En producción usa HTTPS siempre, secretos largos para JWT y rotación de refresh tokens. No expongas el secret en repositorios públicos.

---

## `Frontend público 🌐`

| RUTA WEB | CONTROLADOR / VISTA | EFECTO VISUAL / LIBRERÍA |
| --- | --- | --- |
| `/` | Home | Hero con GSAP/AOS, particles en fondo opcional |
| `/portfolio` | Listado portafolio | Masonry + GLightbox |
| `/portfolio/{slug}` | Detalle obra | Galería ampliada |
| `/retratos` | Landing retratos | Carrusel horizontal masonry + AOS |
| `/retratos/configurador` | Configurador | JS dedicado `portrait-config.js` |
| `/arte-en-vivo` | Arte en vivo | CTA al final + CTA en "Cómo funciona" + galería masonry |
| `/arte-en-vivo/reservar` | Reserva | Validación, captcha puzzle y envío |
| `/branding`, `/branding/{slug}` | Branding | Tarjetas animadas |
| `/eventos`, `/eventos/{slug}` | Eventos | Grid + GLightbox |
| `/diseno`, `/diseno/{slug}` | Diseño | AOS / case study |
| `/productos`, `/productos/{slug}` | Tienda | Filtros / ficha con variantes |
| `/carrito` | Carrito | Cabecera plana + `cart.js` AJAX |
| `/checkout` | Checkout | Stripe + formulario (clientauth) |
| `/contacto` | Contacto | Formulario + CSRF + captcha puzzle |
| `/login`, `/register` | Auth web | Sesiones + validación |
| `/mi-cuenta`, `/mi-cuenta/*` | Dashboard cliente | Cabecera plana + pedidos y perfil |

---

## `E-commerce y Stripe 🛒`

**Flujo de compra:**
1. El usuario añade productos al carrito (`CartService` en sesión o persistencia híbrida).
2. `cart.js` envía peticiones `POST` para add/update/remove.
3. En checkout se valida autenticación de cliente (filtro `clientauth`).
4. `StripeService` crea sesión de pago o PaymentIntent.
5. `StripeWebhookController` procesa eventos (`checkout.session.completed`, etc.).
6. Se actualiza `orders`/`order_items` y se disparan emails / n8n.

> [!CAUTION]
> La clave secreta de Stripe solo en servidor. **Nunca** incrustes `sk_live` en JavaScript del cliente.

---

## `Panel de administración ⚙️`

| ÁREA | RUTAS | FUNCIÓN |
| --- | --- | --- |
| Login | `/admin/login` | Autenticación de staff |
| Dashboard | `/admin/dashboard` | Métricas y Chart.js |
| Portafolio | `/admin/portfolio` | CRUD obras, destacados |
| Retratos | `/admin/portrait-orders` | Estados, boceto, final |
| Productos | `/admin/products` | CRUD, imágenes, variantes |
| Reservas | `/admin/bookings` | Listado, calendario, presupuesto |
| Branding | `/admin/branding` | CRUD proyectos |
| Diseño | `/admin/design` | CRUD proyectos |
| Eventos | `/admin/events` | CRUD eventos |
| Categorías | `/admin/categories` | CRUD categorías |
| Testimonios | `/admin/testimonials` | Moderación |
| Cupones | `/admin/coupons` | Descuentos |
| Mensajes | `/admin/messages` | Contacto entrante |
| Usuarios | `/admin/users` | Activar/desactivar |
| Ajustes | `/admin/settings` | `site_settings` |

---

## `PWA - Progressive Web App 📱`

| COMPONENTE | ARCHIVO | DESCRIPCIÓN |
| --- | --- | --- |
| Manifest | `public/manifest.json` | Nombre, iconos, `theme_color`, `start_url` |
| Service Worker | `public/sw.js` | Cache de assets estáticos |
| Offline | `public/offline.html` | Página cuando no hay red |
| Registro SW | `public/assets/js/app.js` | Registro después de `load` |

> [!TIP]
> Invalida la cache del Service Worker tras despliegues mayores cambiando la versión en `sw.js`.

---

## `Chatbot 🤖`

- El widget en `app/Views/partials/chatbot.php` se incluye en el layout principal.
- `chatbot.js` envía mensajes a `/api/chatbot` (`ChatbotController`).
- Respuestas FAQ estáticas o plantillas; si no hay coincidencia, puede derivar a n8n o a un mensaje genérico.

---

## `n8n - Automatización 🔄`

Cuatro flujos documentados en `docs/n8n-workflows.md`:

| # | EVENTO WEBHOOK | OBJETIVO |
| --- | --- | --- |
| 1 | `new-portrait-order` | Notificar estudio y cliente tras nuevo encargo |
| 2 | `order-status-change` | Emails según transición de estado (retrato / tienda) |
| 3 | `new-booking` | Confirmación de reserva arte en vivo + recordatorio diferido |
| 4 | `loyalty-clients` | Consulta `GET /api/webhooks/loyalty-clients` y campaña mensual |

> [!NOTE]
> `WebhookController` envía la cabecera `X-Webhook-Secret`. Configura el mismo valor en n8n para rechazar peticiones falsas.

---

## `Emails con Mailhog 📧`

- `EmailService` centraliza el envío SMTP. En DDEV, Mailhog captura los mensajes para inspección.
- Plantillas en `app/Views/emails/*.html` (confirmación de pedido, contacto, actualización de estado, recuperación).

---

## `Cloudinary - Imágenes ☁️`

- `CloudinaryService` envía subidas a la nube cuando las credenciales están configuradas.
- `image_helper` y `ImageUploadService` permiten almacenamiento local como respaldo.

> [!TIP]
> Si Cloudinary no está configurado, la app debe continuar guardando en disco (`public/writable/uploads` o ruta definida).

---

## `Testing con PHPUnit 🧪`

| ARCHIVO | TIPO | QUÉ CUBRE |
| --- | --- | --- |
| `tests/Unit/Libraries/CartServiceTest.php` | Unit | Lógica del carrito |
| `tests/Unit/Libraries/JWTServiceTest.php` | Unit | Codificación/decodificación JWT |
| `tests/Unit/Libraries/PdfServiceTest.php` | Unit | Generación PDF |
| `tests/Unit/Libraries/LoyaltyClientsServiceTest.php` | Unit | Segmentación de clientes inactivos |
| `tests/Unit/Libraries/ScheduledTaskServiceTest.php` | Unit | Cron controlado |
| `tests/Unit/Helpers/ApiHelperTest.php` | Unit | Helper API |
| `tests/Feature/PortraitOrderFlowTest.php` | Feature | Flujo de pedido de retrato |
| `tests/Feature/CartCheckoutTest.php` | Feature | Carrito y checkout |
| `tests/Feature/AuthTest.php` | Feature | Login/registro |
| `tests/Feature/ContactFormTest.php` | Feature | Formulario de contacto |

```bash
ddev exec vendor/bin/phpunit
# Con filtro:
ddev exec vendor/bin/phpunit --filter CartServiceTest
```

---

## `Seguridad 🛡️`

| MECANISMO | UBICACIÓN / USO |
| --- | --- |
| CSRF | Tokens en formularios web; config global |
| Filtros | `AuthFilter`, `AdminFilter`, `RateLimitFilter`, `CorsFilter` |
| JWT | API stateless para clientes |
| `.htaccess` | Rewrite, cabeceras, bloqueo de archivos sensibles |
| Validación | Reglas por campo en modelos CodeIgniter |
| Sanitización | `esc()` en vistas |
| **Captcha puzzle** | `app/Helpers/captcha_helper.php` + `CaptchaController` (GD) |
| **Rate limit contacto** | `ContactoController` con caché (3 envíos / IP / hora) |
| **Honeypot** | Campo `website` oculto en formularios públicos |

> [!IMPORTANT]
> Desactiva DebugBar y la exposición de errores detallados en producción (`CI_ENVIRONMENT = production`).

---

## `Configuración para producción 🚀`

| VARIABLE / TAREA | ACCIÓN |
| --- | --- |
| `CI_ENVIRONMENT` | `production` |
| `app.baseURL` | HTTPS canónico |
| `database.*` | Credenciales reales y backups |
| Stripe | `pk_live` / `sk_live` + webhook `whsec_...` |
| `jwt.secret` | Secreto largo único |
| `N8N_WEBHOOK_*` | URL pública HTTPS y secreto rotado |
| Cloudinary | `cloud_name`, `api_key`, `api_secret` |
| SMTP | Servidor real (sin Mailhog) |
| OPcache | Habilitado en PHP-FPM |
| Logs | Rotación y monitoreo |
| `ext-gd` | **Requerido** para servir el captcha |

---

## `Comandos de referencia rápida 🥣`

### DDEV
```bash
ddev start
ddev stop
ddev restart
ddev ssh
ddev launch
ddev mysql
ddev logs
ddev exec php spark routes
```

### Spark (CodeIgniter 4)
```bash
ddev exec php spark migrate
ddev exec php spark migrate:status
ddev exec php spark migrate:rollback
ddev exec php spark db:seed NombreSeeder
ddev exec php spark make:controller Nombre
ddev exec php spark make:model NombreModel
ddev exec php spark make:migration Nombre
ddev exec php spark make:filter Nombre
```

### WSL desde PowerShell
```powershell
wsl --shutdown
wsl --list --verbose
wsl -d DDEV
```

---

## `Créditos y licencia`

Proyecto desarrollado como trabajo final para la artista **NMONZZON**.  
Autor del código: equipo del estudio (ver `composer.json`/`git log`).  
Para uso comercial contacta con el estudio.
