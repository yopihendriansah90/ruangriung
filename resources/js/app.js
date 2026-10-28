import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Swal = Swal;

document.addEventListener('DOMContentLoaded', () => {
    initMenu();
    initBannerSlider();
    initChipScroller();
});

function initMenu() {
    const menuButton = document.querySelector('[data-public-menu-button]');
    const menu = document.querySelector('[data-public-menu]');

    if (!menuButton || !menu) return;

    const setMenuState = (isOpen) => {
        menu.classList.toggle('hidden', !isOpen);
        menuButton.setAttribute('aria-expanded', String(isOpen));
        const icon = menuButton.querySelector('i');
        icon?.classList.toggle('fa-bars', !isOpen);
        icon?.classList.toggle('fa-xmark', isOpen);
    };

    menuButton.addEventListener('click', () => {
        setMenuState(menuButton.getAttribute('aria-expanded') !== 'true');
    });

    menu.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => setMenuState(false)));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuState(false);
    });
}

// Carousel banner di beranda: panah, dots, autoplay, pause saat hover, swipe sentuhan.
function initBannerSlider() {
    const slider = document.querySelector('[data-banner-slider]');
    if (!slider) return;

    const track = slider.querySelector('[data-banner-track]');
    const dots = [...slider.querySelectorAll('[data-banner-dot]')];
    if (!track || track.children.length === 0) return;

    const interval = Number(slider.dataset.interval) > 0 ? Number(slider.dataset.interval) : 6000;
    const slides = track.children.length;
    let index = 0;
    let timer = null;

    const goTo = (target, offsetPx = 0) => {
        index = ((target % slides) + slides) % slides;
        track.style.transform = offsetPx === 0
            ? `translateX(-${index * 100}%)`
            : `translateX(calc(-${index * 100}% + ${offsetPx}px))`;
        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === index;
            dot.style.width = isActive ? '1.25rem' : '0.5rem';
            dot.classList.toggle('bg-white', isActive);
            dot.classList.toggle('bg-white/50', !isActive);
        });
    };

    const start = () => {
        if (timer !== null) return;
        timer = window.setInterval(() => goTo(index + 1), interval);
    };
    const stop = () => {
        if (timer !== null) {
            window.clearInterval(timer);
            timer = null;
        }
    };

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    goTo(0);
    if (!prefersReducedMotion && slides > 1) start();

    slider.querySelector('[data-banner-prev]')?.addEventListener('click', () => goTo(index - 1));
    slider.querySelector('[data-banner-next]')?.addEventListener('click', () => goTo(index + 1));
    dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => goTo(dotIndex)));

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', () => {
        if (!prefersReducedMotion && slides > 1) start();
    });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stop();
    });

    // Swipe dengan sentuhan: geser horizontal untuk pindah slide,
    // vertikal tetap men-scroll halaman. Klik dibatalkan jika jadi geseran.
    if (slides > 1) {
        let startX = 0;
        let startY = 0;
        let dx = 0;
        let dragging = false;
        let axis = null;
        let moved = false;

        const threshold = () => Math.max(50, slider.clientWidth * 0.15);

        const beginDrag = (x, y) => {
            startX = x;
            startY = y;
            dx = 0;
            dragging = true;
            axis = null;
            moved = false;
            stop();
            track.style.transition = 'none';
        };

        const moveDrag = (x, y) => {
            if (!dragging) return false;
            dx = x - startX;
            if (axis === null && (Math.abs(dx) > 8 || Math.abs(y - startY) > 8)) {
                axis = Math.abs(dx) > Math.abs(y - startY) ? 'x' : 'y';
            }
            if (axis !== 'x') return false;
            moved = true;
            let offset = dx;
            // Tahanan di ujung slide pertama/terakhir agar terasa seperti app native.
            if ((index === 0 && dx > 0) || (index === slides - 1 && dx < 0)) offset *= 0.35;
            goTo(index, offset);
            return true;
        };

        const endDrag = () => {
            if (!dragging) return;
            dragging = false;
            track.style.transition = '';
            if (axis === 'x') {
                if (dx <= -threshold()) goTo(index + 1);
                else if (dx >= threshold()) goTo(index - 1);
                else goTo(index);
            }
            if (!prefersReducedMotion) start();
        };

        slider.addEventListener('touchstart', (event) => {
            if (event.touches.length !== 1) return;
            beginDrag(event.touches[0].clientX, event.touches[0].clientY);
        }, { passive: true });

        slider.addEventListener('touchmove', (event) => {
            if (event.touches.length !== 1) return;
            const handled = moveDrag(event.touches[0].clientX, event.touches[0].clientY);
            if (handled && event.cancelable) event.preventDefault();
        }, { passive: false });

        slider.addEventListener('touchend', endDrag);
        slider.addEventListener('touchcancel', endDrag);

        // Kalau jadinya geseran (bukan tap), batalkan klik pada tautan banner.
        track.addEventListener('click', (event) => {
            if (moved) {
                event.preventDefault();
                event.stopPropagation();
                moved = false;
            }
        }, true);
    }
}

// Gulir baris chips kategori dengan tombol panah.
function initChipScroller() {
    document.querySelectorAll('[data-chip-scroll]').forEach((button) => {
        const container = button.parentElement?.querySelector(':scope > [data-chip-track]') ?? button.parentElement?.querySelector('[data-chip-track]');
        if (!container) return;

        const direction = button.dataset.chipScroll === 'prev' ? -1 : 1;
        button.addEventListener('click', () => {
            container.scrollBy({ left: direction * container.clientWidth * 0.8, behavior: 'smooth' });
        });
    });
}
