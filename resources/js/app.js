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

// Carousel banner di beranda: panah, dots, autoplay, pause saat hover.
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

    const goTo = (target) => {
        index = ((target % slides) + slides) % slides;
        track.style.transform = `translateX(-${index * 100}%)`;
        dots.forEach((dot, dotIndex) => {
            const isActive = dotIndex === index;
            dot.style.width = isActive ? '1.25rem' : '0.5rem';
            dot.classList.toggle('bg-white', isActive);
            dot.classList.toggle('bg-white/50', !isActive);
        });
    };

    const start = () => {
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
    if (!prefersReducedMotion) start();

    slider.querySelector('[data-banner-prev]')?.addEventListener('click', () => goTo(index - 1));
    slider.querySelector('[data-banner-next]')?.addEventListener('click', () => goTo(index + 1));
    dots.forEach((dot, dotIndex) => dot.addEventListener('click', () => goTo(dotIndex)));

    slider.addEventListener('mouseenter', stop);
    slider.addEventListener('mouseleave', () => {
        if (!prefersReducedMotion) start();
    });
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) stop();
    });
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
