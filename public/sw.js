/**
 * SERVICE WORKER: CACHÉ OFFLINE, INSTALACIÓN Y ESTRATEGIA RED PRIMERO CON RESPALDO EN CACHÉ.
 * PRECARGA RECURSOS CLAVE; EN FETCH FALLIDO SIRVE OFFLINE.HTML PARA DOCUMENTOS.
 */
const CACHE_NAME = 'nmz-studio-v1';
const PRECACHE_URLS = [
    '/',
    '/assets/css/custom.css',
    '/assets/js/app.js',
    '/assets/images/logo.png',
    '/assets/images/logo-white.png',
    '/manifest.json',
    '/offline.html',
];

/* EVENTO INSTALL: ABRE CACHÉ Y AÑADE URLS INICIALES; ACTIVA EL SW SIN ESPERAR */
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => cache.addAll(PRECACHE_URLS))
    );
    self.skipWaiting();
});

/* EVENTO ACTIVATE: TOMA CONTROL DE LOS CLIENTES ABIERTOS */
self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

/* INTERCEPTA PETICIONES GET DEL MISMO ORIGEN; NO CACHEA /API/ */
self.addEventListener('fetch', (event) => {
    const req = event.request;
    if (req.method !== 'GET') {
        return;
    }
    const url = new URL(req.url);
    if (url.origin !== self.location.origin) {
        return;
    }
    if (url.pathname.startsWith('/api/')) {
        return;
    }

    /* RED PRIMERO: SI OK, ACTUALIZA CACHÉ; SI FALLA, USA CACHÉ U OFFLINE */
    event.respondWith(
        fetch(req)
            .then((response) => {
                if (response.ok) {
                    const copy = response.clone();
                    caches.open(CACHE_NAME).then((cache) => cache.put(req, copy));
                }
                return response;
            })
            .catch(() => {
                const accept = req.headers.get('accept') || '';
                const isDocument =
                    req.mode === 'navigate' || accept.includes('text/html');
                /* NAVEGACIÓN HTML: PÁGINA OFFLINE SI EXISTE EN CACHÉ */
                if (isDocument) {
                    return caches.match('/offline.html').then((cached) => {
                        if (cached) {
                            return cached;
                        }
                        return new Response('Sin conexión', {
                            status: 503,
                            headers: { 'Content-Type': 'text/plain; charset=utf-8' },
                        });
                    });
                }
                /* OTROS RECURSOS: COINCIDENCIA EXACTA EN CACHÉ */
                return caches.match(req).then((cached) => {
                    if (cached) {
                        return cached;
                    }
                    return new Response('', {
                        status: 503,
                        statusText: 'Offline',
                    });
                });
            })
    );
});
