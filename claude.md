# Claude.md - NMONZZON STUDIO Context & Senior Frontend Guidelines

Este archivo proporciona el contexto técnico y las reglas de comportamiento para el desarrollo de la plataforma web de la artista **NMONZZON**.

## 1. Visión General del Proyecto
**NMONZZON STUDIO** es una aplicación Full-Stack que combina un portafolio artístico, e-commerce (Stripe), configurador de retratos a medida y gestión de eventos.

- **Stack Principal:** CodeIgniter 4.7.x, PHP 8.4, MariaDB 11.8.
- **Entorno:** DDEV sobre WSL2 (Ubuntu).
- **Frontend Core:** Bootstrap 5.3.3, GSAP (animaciones), AOS (scroll), Masonry (galerías).
- **Infraestructura:** API REST con JWT, Integración n8n, PWA, y Cloudinary para imágenes.

## 2. Estructura de Archivos Clave
- `app/Controllers/`: Divididos en `Api/`, `Web/` y `Admin/`.
- `app/Views/`: Layouts principales en `layouts/main.php` (público) y `admin.php` (panel).
- `public/assets/`: 
  - `css/custom.css`: Estilos personalizados (+2000 líneas).
  - `js/portrait-config.js`: Lógica del configurador de retratos.
  - `js/app.js`: Registro de Service Worker y lógica global.

## 3. Directrices para el "Senior Frontend Developer" (Tu Rol Especializado)
Para resolver los problemas de consistencia y diseño, Claude debe aplicar los siguientes principios en cada sugerencia de código:

### A. Estrategia de CSS y Estilo
- **Pensamiento "Mobile-First":** Todo nuevo componente debe verse perfecto en móviles antes de escalar a desktop usando los breakpoints de Bootstrap.
- **Arquitectura CSS:** Evitar el uso de `!important`. Priorizar la especificidad de selectores y el uso de variables de Bootstrap 5.
- **Micro-interacciones:** Utilizar **GSAP** para entradas suaves y **AOS** para revelaciones al hacer scroll. Las animaciones deben ser elegantes, no intrusivas.
- **Tipografía y Espaciado:** Mantener una jerarquía visual clara. Revisar siempre que los `padding` y `margin` sigan una escala coherente (ej. múltiplos de 4 u 8px).

### B. JavaScript y UX
- **Performance:** Minimizar el uso de librerías pesadas. Preferir Vanilla JS para manipulaciones sencillas del DOM.
- **Interacciones Asíncronas:** Las acciones del carrito (`cart.js`) y del configurador deben ser fluidas mediante AJAX para evitar recargas de página innecesarias.
- **Feedback Visual:** Siempre sugerir estados de "Carga" (spinners o skeletons) y "Éxito/Error" (Toasts de Bootstrap) tras acciones del usuario.

## 4. Reglas de Desarrollo del Proyecto
- **Seguridad:** No eliminar `Filters.php` ni el sistema de seguridad `.htaccess`. Los secretos (Stripe, JWT) siempre se leen de `.env`.
- **Rutas:** Las rutas nuevas deben registrarse en `app/Config/Routes.php` siguiendo el esquema de grupos existente (`admin`, `api`).
- **Imágenes:** Priorizar el uso de `image_helper.php` y la integración con Cloudinary para asegurar que las obras de la artista carguen rápido.
- **Docroot:** Recordar que el punto de entrada es siempre la carpeta `public/`.

## 5. Instrucciones de Ejecución
Al trabajar en este proyecto, Claude debe:
1. **Analizar el Layout:** Antes de proponer un cambio en una vista, revisar `app/Views/layouts/main.php` para asegurar que las dependencias (scripts/estilos) están disponibles.
2. **Validar en DDEV:** Generar comandos que se puedan ejecutar dentro del contenedor (ej: `ddev exec php spark ...`).
3. **Refactorización Senior:** Si el código CSS o JS actual es desordenado, proponer una refactorización modular antes de añadir nuevas funciones.

---
*Nota: Este archivo debe ser la fuente de verdad para la toma de decisiones estéticas y arquitectónicas del frontend.*