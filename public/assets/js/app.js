/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/APP.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/*
 * =============================================================================
 * SCRIPT FRONT: PUBLIC/ASSETS/JS/APP.JS
 * =============================================================================
 * QUÉ HACE: COMPORTAMIENTO EN EL NAVEGADOR (UI, PETICIONES, INTEGRACIONES) PARA ESTA PARTE DEL SITIO.
 * POR QUÍ EN JS: INTERACTIVIDAD SIN RECARGA; SE CARGA DONDE LO PIDE EL LAYOUT O LA VISTA.
 * =============================================================================
 */

/**
 * JAVASCRIPT GLOBAL DEL SITIO NMONZZON STUDIO.
 * REGISTRA EL SERVICE WORKER, OCULTA EL PRELOADER, INICIALIZA NAVBAR Y VOLVER ARRIBA,
 * AOS, ANCLAS SUAVES, MASONRY, GLIGHTBOX, GSAP/SCROLLTRIGGER, OFFCANVAS Y AYUDA CSRF.
 */

/**
 * Configuración compartida de Particles.js (footer y secciones como Retratos).
 * Evita duplicar JSON en cada vista y mantiene color/densidad coherentes con el pie.
 */
window.nmzParticlesDefaultConfig = {
    particles: {
        number: { value: 80, density: { enable: true, value_area: 900 } },
        color: { value: '#c9a96e' },
        shape: { type: 'circle' },
        opacity: { value: 0.5, random: true, anim: { enable: true, speed: 0.8, opacity_min: 0.15, sync: false } },
        size: { value: 3, random: true, anim: { enable: true, speed: 2, size_min: 0.5, sync: false } },
        line_linked: { enable: true, distance: 130, color: '#c9a96e', opacity: 0.2, width: 1 },
        move: {
            enable: true,
            speed: 2.5,
            direction: 'none',
            random: true,
            straight: false,
            out_mode: 'out',
            bounce: false,
            attract: { enable: true, rotateX: 600, rotateY: 1200 }
        }
    },
    interactivity: {
        detect_on: 'canvas',
        events: {
            onhover: { enable: true, mode: 'grab' },
            onclick: { enable: true, mode: 'push' },
            resize: true
        },
        modes: {
            grab: { distance: 160, line_linked: { opacity: 0.45 } },
            push: { particles_nb: 3 }
        }
    },
    retina_detect: true
};

/* -------------------------------------------------------------------------- */
/* REGISTRO DEL SERVICE WORKER (PWA)                                          */
/* -------------------------------------------------------------------------- */

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then(reg => console.log('SW registered:', reg.scope))
            .catch(err => console.log('SW registration failed:', err));
    });
}

/* -------------------------------------------------------------------------- */
/* PRELOADER: SE EJECUTA AL CARGAR LA VENTANA (FUERA DE DOMCONTENTLOADED)      */
/* -------------------------------------------------------------------------- */

window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.classList.add('preloader-hidden');
        setTimeout(() => preloader.remove(), 600);
    }
});

/* -------------------------------------------------------------------------- */
/* CUANDO EL DOM ESTÁ LISTO                                                   */
/* -------------------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', () => {
    const backToTop = document.getElementById('backToTop');

    function handleScroll() {
        if (backToTop) {
            backToTop.classList.toggle('visible', window.scrollY > 300);
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll();

    /* CLIC EN VOLVER ARRIBA: DESPLAZAMIENTO SUAVE AL INICIO DE LA PÁGINA */
    if (backToTop) {
        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* ANIMACIONES AL HACER SCROLL (AOS) */
    if (typeof AOS !== 'undefined') {
        AOS.init({ duration: 800, once: true, offset: 100 });
    }

    /* ENLACES #ANCLA: SCROLL SUAVE RESPECTANDO LA ALTURA DEL NAVBAR FIJO */
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (!href || href === '#') return;
            const target = document.querySelector(href);
            if (target) {
                e.preventDefault();
                const nav = document.querySelector('.navbar-nmz');
                const navHeight = nav ? nav.offsetHeight : 0;
                window.scrollTo({
                    top: target.offsetTop - navHeight,
                    behavior: 'smooth',
                });
            }
        });
    });

    /* GRILLA MASONRY DEL PORTAFOLIO TRAS CARGAR LAS IMÁGENES */
    if (typeof imagesLoaded !== 'undefined' && typeof Masonry !== 'undefined') {
        const nmzMasonryInstances = [];
        document.querySelectorAll('.portfolio-masonry').forEach((grid) => {
            imagesLoaded(grid, () => {
                const msnry = new Masonry(grid, {
                    itemSelector: '.portfolio-item',
                    columnWidth: '.portfolio-sizer',
                    percentPosition: true,
                    gutter: 20,
                    transitionDuration: '0.4s',
                });
                nmzMasonryInstances.push(msnry);
            });
        });
        let nmzMasonryResizeTimer;
        const nmzMasonryRelayout = () => {
            nmzMasonryInstances.forEach((m) => {
                try {
                    m.layout();
                } catch (e) {
                    /* sin instancia válida */
                }
            });
        };
        window.addEventListener('resize', () => {
            clearTimeout(nmzMasonryResizeTimer);
            nmzMasonryResizeTimer = setTimeout(nmzMasonryRelayout, 150);
        });
        window.addEventListener('orientationchange', () => {
            setTimeout(nmzMasonryRelayout, 280);
        });
    }

    /* GALERÍA LIGHTBOX */
    if (typeof GLightbox !== 'undefined') {
        GLightbox({ selector: '.glightbox', touchNavigation: true, loop: true });
    }

    /* GSAP + SCROLLTRIGGER: PARALLAX EN IMÁGENES Y REVELADO AL SCROLL (omitido si el usuario pide menos movimiento) */
    const nmzReduceMotion =
        typeof window.matchMedia === 'function' &&
        window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (
        !nmzReduceMotion &&
        typeof gsap !== 'undefined' &&
        typeof ScrollTrigger !== 'undefined'
    ) {
        gsap.registerPlugin(ScrollTrigger);

        /* PARALLAX VERTICAL EN ELEMENTOS .PARALLAX-IMG */
        document.querySelectorAll('.parallax-img').forEach((img) => {
            const parent = img.parentElement;
            if (!parent) return;
            gsap.to(img, {
                yPercent: -15,
                ease: 'none',
                scrollTrigger: {
                    trigger: parent,
                    start: 'top bottom',
                    end: 'bottom top',
                    scrub: true,
                },
            });
        });

        /* APARICIÓN DE ELEMENTOS .GSAP-REVEAL */
        gsap.utils.toArray('.gsap-reveal').forEach((el) => {
            gsap.from(el, {
                y: 40,
                opacity: 0,
                duration: 1,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: el,
                    start: 'top 85%',
                    toggleActions: 'play none none none',
                },
            });
        });
    }

    /* OFFCANVAS MÓVIL: CIERRA AL PULSAR UN ENLACE DEL MENÚ */
    const offcanvasEl = document.getElementById('nmzNavbarOffcanvas');
    if (
        offcanvasEl &&
        typeof bootstrap !== 'undefined' &&
        bootstrap.Offcanvas
    ) {
        const bsOffcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
        offcanvasEl.querySelectorAll('.nav-link').forEach((link) => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 992) {
                    bsOffcanvas.hide();
                }
            });
        });
    }

    /* PARTÍCULAS DEL FOOTER (TODAS LAS PÁGINAS) */
    if (typeof particlesJS !== 'undefined' && document.getElementById('footer-particles') && window.nmzParticlesDefaultConfig) {
        particlesJS('footer-particles', window.nmzParticlesDefaultConfig);
    }
});

/* -------------------------------------------------------------------------- */
/* LEE META CSRF DE CODEIGNITER 4 PARA PETICIONES POST DESDE OTROS SCRIPTS     */
/* -------------------------------------------------------------------------- */

function getCsrfData() {
    const metas = document.querySelectorAll('meta');
    for (const meta of metas) {
        if (meta.name && meta.name.startsWith('csrf_')) {
            return { name: meta.name, hash: meta.content };
        }
    }
    return { name: '', hash: '' };
}

