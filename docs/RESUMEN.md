# `PROYECTO NMONZZON STUDIO 🎨`

> [!NOTE]
> ***NMONZZON STUDIO ES UNA APLICACIÓN WEB FULL-STACK PARA LA ARTISTA NMONZZON: PORTAFOLIO, TIENDA CON STRIPE, CONFIGURADOR DE RETRATOS, RESERVAS DE ARTE EN VIVO, SERVICIOS DE BRANDING, DISEÑO Y EVENTOS, PANEL ADMINISTRATIVO COMPLETO, PWA, CHATBOT, API REST CON JWT, AUTOMATIZACIÓN N8N Y CORREO CON MAILHOG EN DESARROLLO.***
> ***ESTÁ DESARROLLADA CON CODEIGNITER 4.7.X, PHP 8.4 Y MARIADB 11.8, EJECUTÁNDOSE DE FORMA RECOMENDADA MEDIANTE DDEV SOBRE WSL2.***

---

***VISIÓN GENERAL DEL PRODUCTO DIGITAL:***

| MÓDULO | FUNCIÓN DE NEGOCIO |
| --- | --- |
| ***PORTAFOLIO*** | ***MUESTRA OBRAS Y PROYECTOS CON GALERÍA RICA (MASONRY, GLIGHTBOX) Y ANIMACIONES.*** |
| ***RETRATOS A MEDIDA*** | ***CONFIGURADOR WEB QUE CALCULA PRECIO SEGÚN ESTILO, TAMAÑO Y OPCIONES; FLUJO DE PEDIDO Y SEGUIMIENTO.*** |
| ***E-COMMERCE*** | ***PRODUCTOS CON VARIANTES, CARRITO, CUPONES, CHECKOUT Y PAGO CON STRIPE.*** |
| ***ARTE EN VIVO*** | ***CAPTURA DE EVENTOS PARA PINTURA EN DIRECTO CON GESTIÓN EN ADMIN Y CALENDARIO.*** |
| ***SERVICIOS*** | ***LANDINGS Y FICHAS POR SLUG PARA BRANDING, DISEÑO GRÁFICO Y EVENTOS.*** |
| ***ADMIN*** | ***CRUDS, DASHBOARD ANALÍTICO, GESTIÓN DE USUARIOS Y AJUSTES DEL SITIO.*** |
| ***PWA*** | ***INSTALABLE, CACHE BÁSICO Y PÁGINA OFFLINE.*** |
| ***INTEGRACIONES*** | ***N8N VÍA WEBHOOK, CLOUDINARY OPCIONAL, DOMPDF PARA PDF.*** |

***EL PROYECTO SE DESARROLLÓ EN 5 FASES (PROMPTS):***
***PROMPT 1: ENTORNO DDEV/WSL2, CODEIGNITER 4 CON COMPOSER.***
***PROMPT 2: BASE DE DATOS (18 MIGRACIONES), MODELOS, API REST COMPLETA CON JWT.***
***PROMPT 3: FRONTEND PÚBLICO CON BOOTSTRAP 5, GSAP, AOS, PARTICLES.JS, MASONRY, GLIGHTBOX.***
***PROMPT 4: E-COMMERCE STRIPE, CARRITO, CHECKOUT, FACTURAS PDF, PANEL DE ADMINISTRACIÓN COMPLETO.***
***PROMPT 5: PWA, CLOUDINARY, N8N, CHATBOT, EMAILSERVICE, PHPUNIT, SEGURIDAD .HTACCESS.***

```text
# FLUJO RESUMIDO DEL VISITANTE
# 1) LLEGADA A HOME -> NAVEGACIÓN A PORTAFOLIO, RETRATOS O TIENDA
# 2) SI COMPRA O ENCARGA RETRATO -> LOGIN/REGISTRO -> CHECKOUT O CONFIRMACIÓN
# 3) ADMIN RECIBE EVENTOS (EMAIL, N8N) Y ACTUALIZA ESTADOS EN PANEL
```

## `ÚLTIMAS MEJORAS DE UX/SEGURIDAD ✨`

> [!TIP]
> ***ITERACIÓN CENTRADA EN LIMPIEZA VISUAL, GALERÍAS DE ALTO RENDIMIENTO Y UN CAPTCHA REALMENTE RESISTENTE A BOTS BÁSICOS.***

### `CABECERAS PLANAS EN "CARRITO" Y "MI CUENTA"`
- ***ELIMINADAS LAS CABECERAS CON IMAGEN DE FONDO EN `CART/INDEX.PHP` Y `CLIENT/{DASHBOARD,PROFILE,ORDERS,PORTRAITS,ORDER-DETAIL,PORTRAIT-DETAIL}.PHP`.***
- ***SUSTITUIDAS POR UNA SECCIÓN `NMZ-PAGE-HEADER` CON BREADCRUMBS `--ON-LIGHT` Y TÍTULO `NMZ-PAGE-HERO__TITLE--ON-LIGHT`.***
- ***`PROFILE.PHP` USA LA NUEVA VARIANTE `.PROFILE-IDENTITY-CARD--FLAT` (HORIZONTAL EN ≥768 PX) DEFINIDA EN `PROFILE-ACCOUNT.CSS`.***

### `ARTE EN VIVO`
- ***CTAS DE "¿QUÉ ES EL ARTE EN VIVO?" MOVIDOS FUERA DE LA COLUMNA DE TEXTO A UNA FILA CENTRADA AL PIE (`BTN-LG` + `DATA-AOS="FADE-UP"`).***
- ***TÍTULO DEL FORMULARIO: "RESERVA EL SERVICIO ARTE EN VIVO PARA TU EVENTO" (VÍA `.SECTION-TITLE` + `TEXT-TRANSFORM: UPPERCASE`).***
- ***NUEVO BLOQUE CTA REFORZADO EN "CÓMO FUNCIONA" CON DOS BOTONES (`RESERVA AHORA` + `VER PORTAFOLIO`).***

### `CARRUSEL HORIZONTAL MASONRY (RETRATOS + ARTE EN VIVO)`
- ***NUEVO COMPONENTE `.RET-HMASONRY*` EN `CUSTOM.CSS`: `GRID-AUTO-FLOW: COLUMN` CON DOS FILAS DE ALTURA FIJA `CLAMP(150PX, 22VW, 230PX)`, `SCROLL-SNAP-TYPE: X PROXIMITY`, FADE EN BORDES Y `FOCUS-VISIBLE` ACCESIBLE.***
- ***PIEZAS CON `ASPECT-RATIO` DEDICADO POR VARIANTE: `--WIDE` 4:3, `--HALF` 1:1, `--TALL` 3:5 (OCUPA 2 FILAS).***
- ***NUEVO `PUBLIC/ASSETS/JS/GALLERY-CAROUSEL.JS`: UN SOLO BOTÓN "VER MÁS" DESPLAZA EL TRACK UN 85 % DEL VIEWPORT A LA DERECHA (Y REINICIA AL LLEGAR AL FINAL). TECLADO ← → SOPORTADO.***

### `CAPTCHA DE PUZZLE DESLIZANTE 🧩`

***SUSTITUYE EL ANTIGUO CAPTCHA MATEMÁTICO (SUMAS/RESTAS), QUE ERA TRIVIALMENTE BYPASSABLE POR BOTS.***

| FICHERO | ROL |
| --- | --- |
| ***`APP/HELPERS/CAPTCHA_HELPER.PHP`*** | ***GENERA TOKEN + SECRETOS (`X`, `Y`, `SEED`) EN SESIÓN; `NMZ_CAPTCHA_VERIFY()` VALIDA LA X CON TOLERANCIA ±6 PX Y CONSUME EL TOKEN.*** |
| ***`APP/CONTROLLERS/WEB/CAPTCHACONTROLLER.PHP`*** | ***RENDERIZA EL PNG DEL FONDO Y LA PIEZA CON GD (GRADIENTE + BLOBS + RUIDO + 2 DECOYS) Y EXPONE `GET /CAPTCHA/REFRESH` JSON.*** |
| ***`APP/CONFIG/ROUTES.PHP`*** | ***AÑADE `GET /CAPTCHA/BG/:TOKEN`, `GET /CAPTCHA/PIECE/:TOKEN`, `GET /CAPTCHA/REFRESH`.*** |
| ***`APP/VIEWS/PARTIALS/CAPTCHA.PHP`*** | ***UI DEL STAGE + PIEZA ABSOLUTA + SLIDER CON `ROLE="SLIDER"`, TECLADO Y BOTÓN "NUEVO DESAFÍO".*** |
| ***`PUBLIC/ASSETS/JS/CAPTCHA-PUZZLE.JS`*** | ***DRAG MOUSE/TOUCH/TECLADO, NORMALIZA LA X DE PANTALLA A PÍXELES DEL SERVIDOR Y LA ESCRIBE EN `CAPTCHA_ANSWER`.*** |
| ***`PUBLIC/ASSETS/CSS/CUSTOM.CSS`*** | ***ESTILOS `.NMZ-CAPTCHA-PUZZLE__*` CON ASPECT-RATIO, ESTADOS `--ACTIVE/--OK/--ERR` Y `TOUCH-ACTION: NONE`.*** |

***LOS CONTROLADORES `CONTACTOCONTROLLER::SEND()` Y `ARTEENVIVOCONTROLLER::PROCESSRESERVA()` CONSERVAN LOS CAMPOS `CAPTCHA_TOKEN` + `CAPTCHA_ANSWER` → EL UPGRADE ES TRANSPARENTE EN BACKEND.***

> [!IMPORTANT]
> ***LA X SECRETA NUNCA LLEGA AL CLIENTE: SOLO APARECE COMO PÍXELES DENTRO DEL PNG. EL FONDO PROCEDURAL + LOS DOS DECOYS + EL RUIDO EXIGEN VISIÓN POR COMPUTADOR PARA CUALQUIER BYPASS. REQUIERE `EXT-GD` EN PHP.***

---

## `ESTRUCTURA DEL PROYECTO 📁`

> [!CAUTION]
> ***NO ELIMINES CARPETAS COMO APP/, PUBLIC/ O WRITABLE/ SIN REVISAR LA CONFIGURACIÓN DE CODEIGNITER 4. EL DOCROOT EN DDEV DEBE APUNTAR A PUBLIC/.***

```
app/
├── Config/
│   ├── Routes.php              # RUTAS WEB, ADMIN Y API (100+ RUTAS)
│   ├── Filters.php             # FILTROS AUTH, ADMIN, CORS, RATE LIMIT, CSRF
│   └── Autoload.php            # HELPERS REGISTRADOS: API, IMAGE
├── Controllers/
│   ├── Api/                    # 20 CONTROLADORES API REST
│   │   ├── BaseApiController.php
│   │   ├── AuthController.php
│   │   ├── CategoryController.php
│   │   ├── PortraitStyleController.php
│   │   ├── PortraitSizeController.php
│   │   ├── ProductController.php
│   │   ├── PortfolioController.php
│   │   ├── PortraitOrderController.php
│   │   ├── OrderController.php
│   │   ├── CouponController.php
│   │   ├── LiveArtBookingController.php
│   │   ├── ContactController.php
│   │   ├── BrandingProjectController.php
│   │   ├── DesignProjectController.php
│   │   ├── EventController.php
│   │   ├── TestimonialController.php
│   │   ├── SettingsController.php
│   │   ├── DashboardController.php
│   │   ├── WebhookController.php
│   │   └── ChatbotController.php
│   ├── Web/                    # 14 CONTROLADORES WEB PÚBLICOS
│   │   ├── HomeController.php
│   │   ├── PortfolioController.php
│   │   ├── RetratosController.php
│   │   ├── ArteEnVivoController.php
│   │   ├── BrandingController.php
│   │   ├── EventosController.php
│   │   ├── DisenoController.php
│   │   ├── ProductosController.php
│   │   ├── CartController.php
│   │   ├── ContactoController.php
│   │   ├── AuthWebController.php
│   │   ├── ClientDashboardController.php
│   │   ├── CaptchaController.php   # IMÁGENES GD DEL PUZZLE + REFRESH JSON
│   │   └── StripeWebhookController.php
│   └── Admin/                  # 15 CONTROLADORES ADMIN
│       ├── AdminLoginController.php
│       ├── DashboardController.php
│       ├── PortfolioAdminController.php
│       ├── PortraitOrderAdminController.php
│       ├── ProductAdminController.php
│       ├── BookingAdminController.php
│       ├── BrandingAdminController.php
│       ├── DesignAdminController.php
│       ├── EventAdminController.php
│       ├── CategoryAdminController.php
│       ├── TestimonialAdminController.php
│       ├── CouponAdminController.php
│       ├── ContactAdminController.php
│       ├── UserAdminController.php
│       └── SettingsAdminController.php
├── Database/
│   ├── Migrations/             # 18 MIGRACIONES
│   └── Seeds/                  # 11 SEEDERS
├── Filters/                    # 4 FILTROS DE SEGURIDAD
│   ├── AuthFilter.php
│   ├── AdminFilter.php
│   ├── RateLimitFilter.php
│   └── CorsFilter.php
├── Helpers/
│   ├── api_helper.php
│   ├── captcha_helper.php     # PUZZLE: GENERATE / ENTRY / VERIFY
│   └── image_helper.php
├── Libraries/                  # 7 LIBRERÍAS PERSONALIZADAS
│   ├── JWTService.php
│   ├── CartService.php
│   ├── StripeService.php
│   ├── PdfService.php
│   ├── ImageUploadService.php
│   ├── CloudinaryService.php
│   └── EmailService.php
├── Models/                     # 18 MODELOS
└── Views/
    ├── layouts/
    │   ├── main.php            # LAYOUT PÚBLICO (BOOTSTRAP 5, GSAP, AOS, PARTICLES)
    │   └── admin.php           # LAYOUT ADMIN (SIDEBAR, TOPBAR, CHART.JS, FULLCALENDAR)
    ├── partials/
    │   ├── navbar.php
    │   ├── footer.php
    │   └── chatbot.php         # WIDGET CHATBOT
    ├── web/                    # 35+ VISTAS PÚBLICAS
    ├── admin/                  # 28 VISTAS ADMIN
    ├── emails/                 # 4 PLANTILLAS EMAIL HTML
    └── pdf/                    # 2 PLANTILLAS PDF (FACTURA, PRESUPUESTO)

public/
├── assets/
│   ├── css/
│   │   ├── custom.css          # 2000+ LÍNEAS CSS PERSONALIZADO
│   │   └── admin.css           # CSS PANEL ADMIN
│   └── js/
│       ├── app.js              # JS GLOBAL + SERVICE WORKER REGISTRATION
│       ├── cart.js             # CARRITO AJAX
│       ├── captcha-puzzle.js   # DRAG + TECLADO + REFRESH DEL CAPTCHA
│       ├── gallery-carousel.js # CARRUSEL HORIZONTAL MASONRY
│       ├── portrait-config.js  # CONFIGURADOR DE RETRATOS
│       ├── retratos-carousel.js# MARQUEE DE ESTILOS
│       ├── admin.js            # JS PANEL ADMIN
│       └── chatbot.js          # CHATBOT WIDGET
├── manifest.json               # MANIFEST PWA
├── sw.js                       # SERVICE WORKER
├── offline.html                # PÁGINA OFFLINE
└── .htaccess                   # SEGURIDAD + REWRITE

tests/
├── Unit/
│   ├── Libraries/
│   │   ├── CartServiceTest.php
│   │   ├── JWTServiceTest.php
│   │   └── PdfServiceTest.php
│   └── Helpers/
│       └── ApiHelperTest.php
└── Feature/
    ├── PortraitOrderFlowTest.php
    ├── CartCheckoutTest.php
    ├── AuthTest.php
    └── ContactFormTest.php

.ddev/
├── config.yaml
└── docker-compose.n8n.yaml     # SERVICIO N8N

docs/
└── n8n-workflows.md            # DOCUMENTACIÓN FLUJOS N8N
```

## `TECNOLOGÍAS UTILIZADAS 🛠️`

| TECNOLOGÍA | VERSIÓN | USO |
| --- | --- | --- |
| ***CODEIGNITER*** | ***4.7.X*** | ***FRAMEWORK MVC PHP*** |
| ***PHP*** | ***8.4*** | ***LENGUAJE DEL SERVIDOR*** |
| ***MARIADB*** | ***11.8*** | ***BASE DE DATOS RELACIONAL*** |
| ***DDEV*** | ***ACTUAL*** | ***ENTORNO LOCAL (CONTENEDORES)*** |
| ***WSL2*** | ***-*** | ***EJECUCIÓN LINUX EN WINDOWS*** |
| ***BOOTSTRAP*** | ***5.3.3*** | ***UI RESPONSIVE*** |
| ***BOOTSTRAP ICONS*** | ***COMPATIBLE 5.X*** | ***ICONOGRAFÍA*** |
| ***GSAP*** | ***CDN / ASSET*** | ***ANIMACIONES AVANZADAS*** |
| ***AOS*** | ***CDN / ASSET*** | ***ANIMACIONES AL HACER SCROLL*** |
| ***PARTICLES.JS*** | ***ASSET*** | ***FONDO PARTICULADO*** |
| ***MASONRY*** | ***ASSET*** | ***REJILLA DE PORTAFOLIO*** |
| ***GLIGHTBOX*** | ***ASSET*** | ***VISOR DE IMÁGENES*** |
| ***CHART.JS*** | ***ASSET*** | ***GRÁFICOS EN DASHBOARD ADMIN*** |
| ***FULLCALENDAR*** | ***ASSET*** | ***CALENDARIO DE RESERVAS*** |
| ***STRIPE*** | ***API*** | ***PAGOS EN CHECKOUT*** |
| ***DOMPDF*** | ***COMPOSER*** | ***GENERACIÓN DE PDF*** |
| ***FIREBASE/PHP-JWT*** | ***COMPOSER*** | ***TOKENS JWT*** |
| ***N8N*** | ***DOCKER COMPOSE*** | ***AUTOMATIZACIÓN Y WEBHOOKS*** |
| ***CLOUDINARY*** | ***OPCIONAL*** | ***CDN DE IMÁGENES*** |
| ***MAILHOG*** | ***DDEV*** | ***CAPTURA DE EMAIL EN DESARROLLO*** |
| ***PHPUNIT*** | ***DEV*** | ***PRUEBAS AUTOMATIZADAS*** |


## `CÓMO ARRANCAR EL PROYECTO 🚀`

> [!CAUTION]
> ***ES NECESARIO TENER DOCKER, DDEV Y WSL2 CONFIGURADOS. SIN ELLOS LOS COMANDOS DDEV NO FUNCIONARÁN EN WINDOWS.***

### `PASO 1: UBICAR EL CÓDIGO EN WSL (DDEV)`

```bash
# NAVEGAR AL DIRECTORIO DE PROYECTOS DDEV DENTRO DE WSL
cd /home/ddev/www/nmzonzzonstudio

# SI CLONAS DESDE GIT, SUSTITUYE LA URL POR LA DE TU REPOSITORIO
# git clone <URL> nmzonzzonstudio && cd nmzonzzonstudio
```

### `PASO 2: CONFIGURAR E INICIAR DDEV`

```bash
# SI AÚN NO EXISTE CONFIGURACIÓN DDEV
ddev config --project-type=php --docroot=public

# LEVANTAR SERVICIOS WEB + DB + ROUTER
ddev start
```

### `PASO 3: INSTALAR DEPENDENCIAS PHP`

```bash
ddev composer install
```

> [!IMPORTANT]
> ***EL ARCHIVO .ENV NO DEBE SUBIRSE AL REPOSITORIO. COPIA ENV A .ENV Y AJUSTA CLAVES DE STRIPE, JWT, N8N Y CLOUDINARY.***

### `PASO 4: VARIABLES DE ENTORNO (.ENV)`

```bash
cp env .env
```

```env
# ENTORNO DE EJECUCIÓN
CI_ENVIRONMENT = development

# URL BASE (SUSTITUIR POR TU HOSTNAME DDEV)
app.baseURL = 'https://nmzonzzonstudio.ddev.site/'

# BASE DE DATOS DDEV POR DEFECTO
database.default.hostname = db
database.default.database = db
database.default.username = db
database.default.password = db
database.default.DBDriver = MySQLi
database.default.port = 3306

# EJEMPLOS DE CLAVES (COMPLETAR CON TUS VALORES REALES)
# stripe.secretKey = sk_test_...
# stripe.publishableKey = pk_test_...
# jwt.secret = <CADENA_LARGA_ALEATORIA>
# N8N_WEBHOOK_URL = http://host.docker.internal:5678/webhook
# N8N_WEBHOOK_SECRET = <SECRETO_COMPARTIDO>
```

### `PASO 5: MIGRACIONES Y SEEDERS`

```bash
# CREAR TABLAS
ddev exec php spark migrate

# ESTADO DE MIGRACIONES
ddev exec php spark migrate:status

# POBLAR DATOS DE PRUEBA (EJECUTAR SEEDERS SEGÚN TU PROYECTO)
# ddev exec php spark db:seed NombreSeeder
```

### `PASO 6: SERVICIOS ADICIONALES (N8N)`

```bash
# TRAS AÑADIR DOCKER-COMPOSE.N8N.YAML EN .DDEV/
ddev restart
# N8N TÍPICAMENTE EN HTTP://LOCALHOST:5678 (VER DOCUMENTACIÓN EN DOCS/N8N-WORKFLOWS.MD)
```

### `PASO 7: ABRIR EN EL NAVEGADOR`

```bash
ddev launch
# O VISITAR MANUALMENTE HTTPS://NMZONZZONSTUDIO.DDEV.SITE SEGÚN CONFIG.YAML
```


## `BASE DE DATOS 🗄️`

> [!NOTE]
> ***EL ESQUEMA SE GENERA CON LAS MIGRACIONES EN APP/DATABASE/MIGRATIONS/. A CONTINUACIÓN SE LISTAN LAS 18 TABLAS PRINCIPALES DEL MODELO DE DATOS; LAS TABLAS DE IMÁGENES Y LÍNEAS DE PEDIDO COMPLETAN LAS RELACIONES 1:N.***

| Nº | TABLA | PROPÓSITO |
| --- | --- | --- |
| ***1*** | ***users*** | ***CLIENTES Y ADMINISTRADORES (AUTENTICACIÓN WEB Y API)*** |
| ***2*** | ***auth_tokens*** | ***REFRESH TOKENS Y METADATOS DE SESIÓN API*** |
| ***3*** | ***categories*** | ***CATEGORÍAS DE PRODUCTOS DE LA TIENDA*** |
| ***4*** | ***portrait_styles*** | ***ESTILOS ARTÍSTICOS PARA ENCARGOS DE RETRATO*** |
| ***5*** | ***portrait_sizes*** | ***FORMATOS Y PRECIOS BASE DEL CONFIGURADOR*** |
| ***6*** | ***portrait_orders*** | ***PEDIDOS DE RETRATO CON ESTADO Y PRECIOS CALCULADOS*** |
| ***7*** | ***portrait_order_status_history*** | ***AUDITORÍA DE CAMBIOS DE ESTADO DE RETRATOS*** |
| ***8*** | ***products*** | ***ARTÍCULOS DE LA TIENDA (SLUG, PRECIO, STOCK LÓGICO)*** |
| ***9*** | ***product_images*** | ***GALERÍA DE IMÁGENES POR PRODUCTO*** |
| ***10*** | ***product_variants*** | ***VARIANTES (TALLA, ACABADO, SKU)*** |
| ***11*** | ***portfolio_works*** | ***OBRAS DEL PORTAFOLIO PÚBLICO*** |
| ***12*** | ***live_art_bookings*** | ***RESERVAS DE ARTE EN VIVO Y DATOS DEL EVENTO*** |
| ***13*** | ***branding_projects*** | ***CASOS DE BRANDING CON SLUG Y METADATOS*** |
| ***14*** | ***branding_project_images*** | ***IMÁGENES ASOCIADAS A PROYECTOS DE BRANDING*** |
| ***15*** | ***design_projects*** | ***CASOS DE DISEÑO GRÁFICO / IDENTIDAD*** |
| ***16*** | ***design_project_images*** | ***IMÁGENES DE PROYECTOS DE DISEÑO*** |
| ***17*** | ***events*** | ***SERVICIOS Y EVENTOS MOSTRADOS EN /EVENTOS*** |
| ***18*** | ***event_images*** | ***IMÁGENES DE FICHAS DE EVENTO*** |

***TABLAS TRANSACCIONALES Y DE SOPORTE (RELACIONADAS):***

| TABLA | RELACIÓN CLAVE |
| --- | --- |
| ***orders*** | ***USUARIO (FK), STRIPE SESSION/PAYMENT INTENT, TOTALES*** |
| ***order_items*** | ***N:1 CON ORDERS Y PRODUCTOS / VARIANTES*** |
| ***contact_messages*** | ***FORMULARIO DE CONTACTO Y BANDEJA ADMIN*** |
| ***coupons*** | ***DESCUENTOS APLICABLES EN CARRITO*** |
| ***testimonials*** | ***OPINIONES MOSTRADAS EN HOME / LANDING*** |
| ***site_settings*** | ***CLAVE/VALOR PARA TEXTO LEGAL, REDES Y FLAGS*** |

```sql
-- EJEMPLO ILUSTRATIVO: RELACIÓN PEDIDO -> LÍNEAS (CONCEPTUAL)
-- SELECT O.ID, OI.PRODUCT_ID, OI.QUANTITY FROM ORDERS O
-- JOIN ORDER_ITEMS OI ON OI.ORDER_ID = O.ID WHERE O.USER_ID = ?
```


## `API REST 🔌`

> [!TIP]
> ***LA API ESTÁ AGRUPADA BAJO EL PREFIJO /API. LAS RUTAS ADMINISTRATIVAS USAN EL SUBGRUPO /API/ADMIN/. COMPRUEBA CABECERA AUTHORIZATION: BEARER <TOKEN> EN ENDPOINTS PROTEGIDOS.***

| MÉTODO | ENDPOINT | DESCRIPCIÓN | AUTH |
| --- | --- | --- | --- |
| ***POST*** | ***`/api/auth/register`*** | ***REGISTRO DE USUARIO API (JSON)*** | ***NO*** |
| ***POST*** | ***`/api/auth/login`*** | ***LOGIN Y EMISIÓN DE TOKENS JWT*** | ***NO*** |
| ***POST*** | ***`/api/auth/refresh`*** | ***REFRESCO DE TOKEN DE ACCESO*** | ***NO*** |
| ***POST*** | ***`/api/auth/forgot-password`*** | ***SOLICITUD DE RECUPERACIÓN DE CONTRASEÑA*** | ***NO*** |
| ***POST*** | ***`/api/auth/reset-password`*** | ***RESTABLECER CONTRASEÑA CON TOKEN*** | ***NO*** |
| ***GET*** | ***`/api/categories`*** | ***LISTADO DE CATEGORÍAS DE PRODUCTOS*** | ***NO*** |
| ***GET*** | ***`/api/categories/{id}`*** | ***DETALLE DE CATEGORÍA POR ID*** | ***NO*** |
| ***GET*** | ***`/api/portrait-styles`*** | ***ESTILOS DE RETRATO DISPONIBLES*** | ***NO*** |
| ***GET*** | ***`/api/portrait-styles/{id}`*** | ***DETALLE DE ESTILO DE RETRATO*** | ***NO*** |
| ***GET*** | ***`/api/portrait-sizes`*** | ***TAMAÑOS DE RETRATO Y PRECIOS BASE*** | ***NO*** |
| ***GET*** | ***`/api/products`*** | ***CATÁLOGO DE PRODUCTOS (LISTADO)*** | ***NO*** |
| ***GET*** | ***`/api/products/{slug}`*** | ***DETALLE DE PRODUCTO POR SLUG*** | ***NO*** |
| ***GET*** | ***`/api/portfolio`*** | ***OBRAS DE PORTAFOLIO*** | ***NO*** |
| ***GET*** | ***`/api/portfolio/{slug}`*** | ***DETALLE DE OBRA POR SLUG*** | ***NO*** |
| ***GET*** | ***`/api/branding`*** | ***PROYECTOS DE BRANDING*** | ***NO*** |
| ***GET*** | ***`/api/branding/{slug}`*** | ***DETALLE PROYECTO BRANDING*** | ***NO*** |
| ***GET*** | ***`/api/design`*** | ***PROYECTOS DE DISEÑO*** | ***NO*** |
| ***GET*** | ***`/api/design/{slug}`*** | ***DETALLE PROYECTO DISEÑO*** | ***NO*** |
| ***GET*** | ***`/api/events`*** | ***EVENTOS Y SERVICIOS DE EVENTOS*** | ***NO*** |
| ***GET*** | ***`/api/events/{slug}`*** | ***DETALLE DE EVENTO POR SLUG*** | ***NO*** |
| ***GET*** | ***`/api/testimonials`*** | ***TESTIMONIOS PÚBLICOS*** | ***NO*** |
| ***POST*** | ***`/api/contact`*** | ***ENVÍO DE FORMULARIO DE CONTACTO*** | ***NO*** |
| ***POST*** | ***`/api/chatbot`*** | ***MENSAJE AL CHATBOT (FAQ / FLUJO)*** | ***NO*** |
| ***POST*** | ***`/api/live-art-bookings`*** | ***CREAR RESERVA DE ARTE EN VIVO*** | ***NO*** |
| ***GET*** | ***`/api/webhooks/loyalty-clients`*** | ***LISTADO PARA CAMPANAS DE FIDELIZACIÓN (N8N)*** | ***NO*** |
| ***GET*** | ***`/api/settings/{key}`*** | ***VALOR DE CONFIGURACIÓN PÚBLICA POR CLAVE*** | ***NO*** |
| ***GET*** | ***`/api/auth/profile`*** | ***PERFIL DEL USUARIO AUTENTICADO*** | ***SÍ (JWT)*** |
| ***PUT*** | ***`/api/auth/profile`*** | ***ACTUALIZAR PERFIL DEL USUARIO*** | ***SÍ (JWT)*** |
| ***POST*** | ***`/api/auth/logout`*** | ***INVALIDAR SESIÓN / REFRESH TOKEN*** | ***SÍ (JWT)*** |
| ***POST*** | ***`/api/portrait-orders`*** | ***CREAR PEDIDO DE RETRATO*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/portrait-orders`*** | ***LISTAR PEDIDOS DE RETRATO DEL CLIENTE*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/portrait-orders/{id}`*** | ***DETALLE DE PEDIDO DE RETRATO*** | ***SÍ (JWT)*** |
| ***POST*** | ***`/api/portrait-orders/{id}/reference-photo`*** | ***SUBIR FOTO DE REFERENCIA*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/portrait-orders/{id}/history`*** | ***HISTORIAL DE ESTADOS DEL PEDIDO*** | ***SÍ (JWT)*** |
| ***POST*** | ***`/api/orders`*** | ***CREAR PEDIDO DE TIENDA (DESDE APP)*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/orders`*** | ***LISTAR PEDIDOS DE TIENDA DEL CLIENTE*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/orders/{id}`*** | ***DETALLE DE PEDIDO DE TIENDA*** | ***SÍ (JWT)*** |
| ***GET*** | ***`/api/orders/{id}/invoice`*** | ***DESCARGAR / OBTENER FACTURA PDF*** | ***SÍ (JWT)*** |
| ***POST*** | ***`/api/coupons/validate`*** | ***VALIDAR CÓDIGO DE CUPÓN*** | ***SÍ (JWT)*** |
| ***PUT*** | ***`/api/admin/portrait-orders/{id}/status`*** | ***ACTUALIZAR ESTADO DE RETRATO (ADMIN API)*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/portrait-orders/{id}/sketch`*** | ***SUBIR BOCETO (ADMIN API)*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/portrait-orders/{id}/final`*** | ***SUBIR OBRA FINAL (ADMIN API)*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/live-art-bookings/{id}/status`*** | ***ACTUALIZAR ESTADO DE RESERVA*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/live-art-bookings/calendar`*** | ***DATOS PARA CALENDARIO DE RESERVAS*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/live-art-bookings/{id}/quote`*** | ***GENERAR PRESUPUESTO DE RESERVA*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/dashboard/stats`*** | ***ESTADÍSTICAS AGREGADAS DEL DASHBOARD*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/dashboard/revenue`*** | ***SERIE / RESUMEN DE INGRESOS*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/dashboard/orders-by-style`*** | ***PEDIDOS AGRUPADOS POR ESTILO DE RETRATO*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/dashboard/top-products`*** | ***PRODUCTOS MÁS VENDIDOS*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/categories`*** | ***CREAR CATEGORÍA*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/categories/{id}`*** | ***ACTUALIZAR CATEGORÍA*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/categories/{id}`*** | ***ELIMINAR CATEGORÍA*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/portrait-styles`*** | ***CREAR ESTILO DE RETRATO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/portrait-styles/{id}`*** | ***ACTUALIZAR ESTILO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/portrait-styles/{id}`*** | ***ELIMINAR ESTILO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/products`*** | ***CREAR PRODUCTO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/products/{id}`*** | ***ACTUALIZAR PRODUCTO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/products/{id}`*** | ***ELIMINAR PRODUCTO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/portfolio`*** | ***CREAR ENTRADA DE PORTAFOLIO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/portfolio/{id}`*** | ***ACTUALIZAR PORTAFOLIO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/portfolio/{id}`*** | ***ELIMINAR PORTAFOLIO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/settings`*** | ***ACTUALIZAR CONFIGURACIÓN GLOBAL*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/settings`*** | ***LISTAR TODAS LAS CONFIGURACIONES*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/contact`*** | ***LISTAR MENSAJES DE CONTACTO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/contact/{id}/read`*** | ***MARCAR MENSAJE COMO LEÍDO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/contact/{id}`*** | ***ELIMINAR MENSAJE*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/coupons`*** | ***LISTAR CUPONES*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/coupons`*** | ***CREAR CUPÓN*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/coupons/{id}`*** | ***ACTUALIZAR CUPÓN*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/coupons/{id}`*** | ***ELIMINAR CUPÓN*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/testimonials`*** | ***LISTAR TESTIMONIOS (ADMIN)*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/testimonials`*** | ***CREAR TESTIMONIO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/testimonials/{id}`*** | ***ACTUALIZAR TESTIMONIO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/testimonials/{id}`*** | ***ELIMINAR TESTIMONIO*** | ***SÍ (JWT ADMIN)*** |
| ***GET*** | ***`/api/admin/orders`*** | ***LISTAR TODOS LOS PEDIDOS TIENDA*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/orders/{id}/status`*** | ***ACTUALIZAR ESTADO DE PEDIDO TIENDA*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/branding`*** | ***CREAR PROYECTO BRANDING*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/branding/{id}`*** | ***ACTUALIZAR BRANDING*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/branding/{id}`*** | ***ELIMINAR BRANDING*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/branding/{id}/images`*** | ***AÑADIR IMÁGENES A BRANDING*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/design`*** | ***CREAR PROYECTO DISEÑO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/design/{id}`*** | ***ACTUALIZAR DISEÑO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/design/{id}`*** | ***ELIMINAR DISEÑO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/design/{id}/images`*** | ***AÑADIR IMÁGENES A DISEÑO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/events`*** | ***CREAR EVENTO*** | ***SÍ (JWT ADMIN)*** |
| ***PUT*** | ***`/api/admin/events/{id}`*** | ***ACTUALIZAR EVENTO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/events/{id}`*** | ***ELIMINAR EVENTO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/events/{id}/images`*** | ***AÑADIR IMÁGENES A EVENTO*** | ***SÍ (JWT ADMIN)*** |
| ***POST*** | ***`/api/admin/products/{id}/images`*** | ***AÑADIR IMAGEN A PRODUCTO*** | ***SÍ (JWT ADMIN)*** |
| ***DELETE*** | ***`/api/admin/products/{id}/images/{imgId}`*** | ***ELIMINAR IMAGEN DE PRODUCTO*** | ***SÍ (JWT ADMIN)*** |

```bash
# EJEMPLO: LOGIN API (RESPUESTA SIMPLIFICADA)
curl -s -X POST "https://nmzonzzonstudio.ddev.site/api/auth/login" \
  -H "Content-Type: application/json" \
  -d "{\"email\":\"cliente@ejemplo.com\",\"password\":\"********\"}\"

# EJEMPLO: RECURSO PROTEGIDO CON JWT
curl -s "https://nmzonzzonstudio.ddev.site/api/auth/profile" \
  -H "Authorization: Bearer <ACCESS_TOKEN>\"
```


## `AUTENTICACIÓN JWT 🔐`

***FLUJO DE TOKENS (RESUMEN):***

1. ***EL CLIENTE ENVÍA CREDENCIALES A /API/AUTH/LOGIN.***
2. ***EL SERVIDOR VALIDA USUARIO Y EMITE ACCESS TOKEN (CORTA DURACIÓN) Y REFRESH TOKEN (LARGA DURACIÓN / ROTATIVO).***
3. ***LAS PETICIONES AUTENTICADAS INCLUYEN `AUTHORIZATION: BEARER <ACCESS_TOKEN>`.***
4. ***SI EL ACCESS TOKEN EXPIRA, SE USA /API/AUTH/REFRESH CON EL REFRESH TOKEN VÁLIDO.***
5. ***LOGOUT INVALIDA TOKENS EN SERVIDOR SEGÚN IMPLEMENTACIÓN (TABLA AUTH_TOKENS).***

> [!IMPORTANT]
> ***EN PRODUCCIÓN USA HTTPS SIEMPRE, SECRETOS LARGOS PARA JWT Y ROTACIÓN DE REFRESH TOKENS. NO EXPONGAS EL SECRET EN REPOSITORIOS PÚBLICOS.***

```php
// PSEUDOCÓDIGO: VALIDACIÓN EN FILTRO API (CONCEPTO)
// $token = EXTRACT_BEARER($request->getHeaderLine('Authorization'));
// $payload = JWTSERVICE::DECODE($token);
// SI FALLA -> 401 JSON
```


## `FRONTEND PÚBLICO 🌐`

| RUTA WEB | CONTROLADOR / VISTA | EFECTO VISUAL / LIBRERÍA |
| --- | --- | --- |
| ***/*** | ***HOME*** | ***HERO CON GSAP/AOS, PARTICLES EN FONDO OPCIONAL*** |
| ***/portfolio*** | ***LISTADO PORTAFOLIO*** | ***MASONRY + GLIGHTBOX*** |
| ***/portfolio/{slug}*** | ***DETALLE OBRA*** | ***GALERÍA AMPLIADA*** |
| ***/retratos*** | ***LANDING RETRATOS*** | ***AOS EN SECCIONES*** |
| ***/retratos/configurador*** | ***CONFIGURADOR*** | ***JS DEDICADO PORTRAIT-CONFIG.JS*** |
| ***/arte-en-vivo*** | ***ARTE EN VIVO*** | ***CTA Y FORMULARIO DE RESERVA*** |
| ***/arte-en-vivo/reservar*** | ***RESERVA*** | ***VALIDACIÓN Y ENVÍO*** |
| ***/branding*** | ***LISTADO BRANDING*** | ***TARJETAS ANIMADAS*** |
| ***/branding/{slug}*** | ***DETALLE*** | ***IMÁGENES Y TEXTO*** |
| ***/eventos*** | ***LISTADO EVENTOS*** | ***GRID RESPONSIVE*** |
| ***/eventos/{slug}*** | ***DETALLE EVENTO*** | ***GLIGHTBOX*** |
| ***/diseno*** | ***LISTADO DISEÑO*** | ***AOS*** |
| ***/diseno/{slug}*** | ***DETALLE*** | ***CASE STUDY*** |
| ***/productos*** | ***TIENDA*** | ***FILTROS Y CARDS*** |
| ***/productos/{slug}*** | ***FICHA PRODUCTO*** | ***VARIANTES*** |
| ***/carrito*** | ***CARRITO*** | ***CART.JS AJAX*** |
| ***/checkout*** | ***CHECKOUT*** | ***STRIPE + FORMULARIO (CLIENTAUTH)*** |
| ***/contacto*** | ***CONTACTO*** | ***FORMULARIO + CSRF*** |
| ***/login*** | ***LOGIN WEB*** | ***SESIONES*** |
| ***/register*** | ***REGISTRO*** | ***VALIDACIÓN*** |
| ***/mi-cuenta*** | ***DASHBOARD CLIENTE*** | ***PEDIDOS Y PERFIL*** |

```javascript
// EJEMPLO: REGISTRO DEL SERVICE WORKER EN APP.JS (CONCEPTO)
// if ('serviceWorker' in navigator) {
//   navigator.serviceWorker.register('/sw.js');
// }
```


## `E-COMMERCE Y STRIPE 🛒`

***FLUJO DE COMPRA:***

1. ***EL USUARIO AÑADE PRODUCTOS AL CARRITO (CARTSERVICE EN SESIÓN O PERSISTENCIA HÍBRIDA).***
2. ***CART.JS ENVÍA PETICIONES POST PARA ADD/UPDATE/REMOVE.***
3. ***EN CHECKOUT SE VALIDA AUTENTICACIÓN DE CLIENTE (FILTRO CLIENTAUTH).***
4. ***STRIPESERVICE CREA SESIÓN DE PAGO O PAYMENTINTENT SEGÚN IMPLEMENTACIÓN.***
5. ***STRIPEWEBHOOKCONTROLLER PROCESA EVENTOS (CHECKOUT.SESSION.COMPLETED, ETC.).***
6. ***SE ACTUALIZA LA TABLA ORDERS Y ORDER_ITEMS Y SE DISPARAN EMAILS / N8N.***

> [!CAUTION]
> ***LA CLAVE SECRETA DE STRIPE SOLO EN SERVIDOR. NUNCA INCRUSTES SK_LIVE EN JAVASCRIPT DEL CLIENTE.***

```bash
# WEBHOOK LOCAL (TÚNEL NGROK O STRIPE CLI - EJEMPLO GENÉRICO)
# STRIPE LISTEN --FORWARD-TO HTTPS://TUHOST/stripe/webhook
```


## `PANEL DE ADMINISTRACIÓN ⚙️`

| ÁREA ADMIN | RUTAS TÍPICAS | FUNCIÓN |
| --- | --- | --- |
| ***LOGIN*** | ***/admin/login*** | ***AUTENTICACIÓN DE STAFF*** |
| ***DASHBOARD*** | ***/admin/dashboard*** | ***MÉTRICAS Y CHART.JS*** |
| ***PORTAFOLIO*** | ***/admin/portfolio*** | ***CRUD OBRAS, DESTACADOS*** |
| ***RETRATOS*** | ***/admin/portrait-orders*** | ***ESTADOS, BOCETO, FINAL*** |
| ***PRODUCTOS*** | ***/admin/products*** | ***CRUD, IMÁGENES, VARIANTES*** |
| ***RESERVAS*** | ***/admin/bookings*** | ***LISTADO, CALENDARIO, PRESUPUESTO*** |
| ***BRANDING*** | ***/admin/branding*** | ***CRUD PROYECTOS*** |
| ***DISEÑO*** | ***/admin/design*** | ***CRUD PROYECTOS*** |
| ***EVENTOS*** | ***/admin/events*** | ***CRUD EVENTOS*** |
| ***CATEGORÍAS*** | ***/admin/categories*** | ***CRUD CATEGORÍAS*** |
| ***TESTIMONIOS*** | ***/admin/testimonials*** | ***MODERACIÓN*** |
| ***CUPONES*** | ***/admin/coupons*** | ***DESCUENTOS*** |
| ***MENSAJES*** | ***/admin/messages*** | ***CONTACTO ENTRANTE*** |
| ***USUARIOS*** | ***/admin/users*** | ***ACTIVAR/DESACTIVAR*** |
| ***AJUSTES*** | ***/admin/settings*** | ***SITE_SETTINGS*** |

```php
// EJEMPLO: GRUPO DE RUTAS ADMIN CON FILTRO EN ROUTES.PHP
$routes->group('admin', ['filter' => 'admin'], static function ($routes) {
    $routes->get('dashboard', 'Admin\DashboardController::index');
    // ... MÁS CRUDS ...
});
```


## `PWA - PROGRESSIVE WEB APP 📱`

| COMPONENTE | ARCHIVO | DESCRIPCIÓN |
| --- | --- | --- |
| ***MANIFEST*** | ***public/manifest.json*** | ***NOMBRE, ICONOS, THEME_COLOR, START_URL*** |
| ***SERVICE WORKER*** | ***public/sw.js*** | ***CACHE DE ASSETS ESTÁTICOS*** |
| ***OFFLINE*** | ***public/offline.html*** | ***PÁGINA CUANDO NO HAY RED*** |
| ***REGISTRO SW*** | ***public/assets/js/app.js*** | ***REGISTRO DESPUÉS DE LOAD*** |

> [!TIP]
> ***INVALIDA LA CACHE DEL SERVICE WORKER TRAS DESPLIEGUES MAYORES CAMBIANDO LA VERSIÓN DEL CACHE EN SW.JS.***

```json
// FRAGMENTO ILUSTRATIVO MANIFEST (NO ES EL ARCHIVO REAL)
{ "name": "NMONZZON STUDIO", "short_name": "NMONZZON", "start_url": "/", "display": "standalone" }
```


## `CHATBOT 🤖`

***EL WIDGET EN VIEWS/PARTIALS/CHATBOT.PHP SE INCLUYE EN EL LAYOUT PRINCIPAL.***
***CHATBOT.JS ENVÍA MENSAJES A /API/CHATBOT (CHATBOTCONTROLLER).***
***RESPUESTAS FAQ ESTÁTICAS O PLANTILLAS; SI NO HAY COINCIDENCIA, SE PUEDE DERIVAR A N8N O MENSAJE GENÉRICO.***

```javascript
// EJEMPLO: POST AL ENDPOINT DEL BOT (CONCEPTO)
// fetch('/api/chatbot', { method: 'POST', body: JSON.stringify({ message: text }) });
```


## `N8N - AUTOMATIZACIÓN 🔄`

***CUATRO FLUJOS DOCUMENTADOS EN DOCS/N8N-WORKFLOWS.MD:***

| # | EVENTO WEBHOOK | OBJETIVO |
| --- | --- | --- |
| ***1*** | ***NEW-PORTRAIT-ORDER*** | ***NOTIFICAR ESTUDIO Y CLIENTE TRAS NUEVO ENCARGO*** |
| ***2*** | ***ORDER-STATUS-CHANGE*** | ***EMAILS SEGÚN TRANSICIÓN DE ESTADO DE RETRATO/TIENDA*** |
| ***3*** | ***NEW-BOOKING*** | ***CONFIRMACIÓN DE RESERVA ARTE EN VIVO + RECORDATORIO DIFERIDO*** |
| ***4*** | ***LOYALTY / CLIENTES INACTIVOS*** | ***CONSULTA API GET /API/WEBHOOKS/LOYALTY-CLIENTS Y CAMPAÑA MENSUAL*** |

> [!NOTE]
> ***WEBHOOKCONTROLLER ENVÍA CABECERA X-WEBHOOK-SECRET. CONFIGURA EL MISMO VALOR EN N8N PARA RECHAZAR PETICIONES FALSAS.***

```text
URL BASE SALIENTE DESDE PHP:
{N8N_WEBHOOK_URL}/{event}
EJEMPLOS DE EVENT: new-portrait-order | order-status-change | new-booking
```


## `EMAILS CON MAILHOG 📧`

***EMAILSERVICE CENTRALIZA EL ENVÍO SMTP. EN DDEV, MAILHOG CAPTURA LOS MENSAJES PARA INSPECCIÓN.***

| PLANTILLA (VIEWS/EMAILS/) | USO |
| --- | --- |
| ***VARIAS HTML*** | ***CONFIRMACIÓN DE PEDIDO, CONTACTO, ACTUALIZACIÓN DE ESTADO DE RETRATO, RECUPERACIÓN*** |

```php
// USO ILUSTRATIVO (NOMBRES PUEDEN VARIAR EN TU CÓDIGO)
// $emailService->sendContactNotification($data);
```


## `CLOUDINARY - IMÁGENES ☁️`

***CLOUDINARYSERVICE ENVÍA SUBIDAS A LA NUBE CUANDO LAS CREDENCIALES ESTÁN CONFIGURADAS.***
***IMAGE_HELPER Y IMAGEUPLOADSERVICE PERMITEN ALMACENAMIENTO LOCAL COMO RESPALDO.***

> [!TIP]
> ***SI CLOUDINARY NO ESTÁ CONFIGURADO, LA APP DEBE CONTINUAR GUARDANDO EN DISCO (PUBLIC/WRITABLE/UPLOADS O RUTA DEFINIDA).***

```php
// DECISIÓN TÍPICA: SI CLOUDINARY ACTIVO -> SUBIR Y GUARDAR URL; SI NO -> RUTA LOCAL
```


## `TESTING CON PHPUNIT 🧪`

| ARCHIVO | TIPO | QUÉ CUBRE |
| --- | --- | --- |
| ***tests/Unit/Libraries/CartServiceTest.php*** | ***UNIT*** | ***LÓGICA DEL CARRITO*** |
| ***tests/Unit/Libraries/JWTServiceTest.php*** | ***UNIT*** | ***CODIFICACIÓN/DECODIFICACIÓN JWT*** |
| ***tests/Unit/Libraries/PdfServiceTest.php*** | ***UNIT*** | ***GENERACIÓN PDF*** |
| ***tests/Unit/Helpers/ApiHelperTest.php*** | ***UNIT*** | ***HELPER API (RESPUESTAS, UTILIDADES)*** |
| ***tests/Feature/PortraitOrderFlowTest.php*** | ***FEATURE*** | ***FLUJO DE PEDIDO DE RETRATO*** |
| ***tests/Feature/CartCheckoutTest.php*** | ***FEATURE*** | ***CARRITO Y CHECKOUT*** |
| ***tests/Feature/AuthTest.php*** | ***FEATURE*** | ***LOGIN/REGISTRO*** |
| ***tests/Feature/ContactFormTest.php*** | ***FEATURE*** | ***FORMULARIO DE CONTACTO*** |

```bash
ddev exec vendor/bin/phpunit
# O CON FILTRO:
# ddev exec vendor/bin/phpunit --filter CartServiceTest
```


## `SEGURIDAD 🛡️`

| MECANISMO | UBICACIÓN / USO |
| --- | --- |
| ***CSRF*** | ***TOKENS EN FORMULARIOS WEB; CONFIG GLOBAL*** |
| ***FILTROS*** | ***AUTHFILTER, ADMINFILTER, RATELIMITFILTER, CORSFILTER*** |
| ***JWT*** | ***API STATELESS PARA CLIENTES*** |
| ***.HTACCESS*** | ***REWRITE, CABECERAS, BLOQUEO DE ARCHIVOS SENSIBLES*** |
| ***VALIDACIÓN*** | ***MODELOS CODEIGNITER Y REGLAS POR CAMPO*** |
| ***SANITIZACIÓN*** | ***ESC() EN VISTAS*** |
| ***CAPTCHA PUZZLE*** | ***CAPTCHA_HELPER + CAPTCHACONTROLLER (GD). X SECRETA EN SESIÓN, NUNCA EN EL CLIENTE.*** |
| ***HONEYPOT*** | ***CAMPO OCULTO `WEBSITE` EN FORMULARIOS PÚBLICOS*** |
| ***RATE LIMIT CONTACTO*** | ***CACHÉ POR IP: 3 ENVÍOS / HORA EN `/CONTACTO`*** |

> [!IMPORTANT]
> ***DESACTIVA DEBUGBAR Y EXPOSICIÓN DE ERRORES DETALLADOS EN PRODUCCIÓN. CI_ENVIRONMENT = PRODUCTION.***

```apache
# EJEMPLO .HTACCESS: FORZAR REWRITE A INDEX.PHP (SIMPLIFICADO)
# RewriteEngine On
# RewriteCond %{REQUEST_FILENAME} !-f
# RewriteRule ^(.*)$ index.php/$1 [L,QSA]
```


## `CONFIGURACIÓN PARA PRODUCCIÓN 🚀`

| VARIABLE / TAREA | ACCIÓN |
| --- | --- |
| ***CI_ENVIRONMENT*** | ***PRODUCTION*** |
| ***APP.BASEURL*** | ***HTTPS CANÓNICO*** |
| ***DATABASE_*** | ***CREDENCIALES REALES Y BACKUPS*** |
| ***STRIPE KEYS*** | ***PK_LIVE / SK_LIVE + WEBHOOK WHSEC*** |
| ***JWT.SECRET*** | ***SECRETO LARGO ÚNICO*** |
| ***N8N_WEBHOOK_*** | ***URL PÚBLICA HTTPS Y SECRETO ROTADO*** |
| ***CLOUDINARY*** | ***CLOUD_NAME, API_KEY, API_SECRET*** |
| ***SMTP*** | ***SERVIDOR REAL (SIN MAILHOG)*** |
| ***OPCACHE*** | ***HABILITADO EN PHP-FPM*** |
| ***LOGS*** | ***ROTACIÓN Y MONITOREO*** |


## `COMANDOS DE REFERENCIA RÁPIDA 🥣`

### `DDEV`
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

### `SPARK (CODEIGNITER 4)`
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

### `WSL DESDE POWERSHELL`
```powershell
wsl --shutdown
wsl --list --verbose
wsl -d DDEV
```

