/**
 * STMIK Enterprise - JavaScript Animations
 * Script untuk mengatur semua animasi di website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi semua animasi
    initSmoothScroll();
    initScrollAnimations();
    initNavbarStickyEffect();
    initParallaxEffects();
    initCounters();
    initTextAnimations();
    initHoverEffects();
    initAnimatedWaves();
    initLogoAnimation();
    initHeroParticles();
});

/**
 * Smooth scroll untuk anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            
            const targetId = this.getAttribute('href');
            
            if (targetId === '#') return;
            
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                window.scrollTo({
                    top: targetElement.offsetTop - 100,
                    behavior: 'smooth'
                });
            }
        });
    });
}

/**
 * Animasi saat scroll - mendeteksi elemen dan menambahkan kelas 'visible'
 */
function initScrollAnimations() {
    const elements = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .zoom-in');
    
    if (elements.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, {
        threshold: 0.1
    });
    
    elements.forEach(element => {
        observer.observe(element);
    });
    
    // Juga animasikan elemen yang sudah visible pada load
    elements.forEach(element => {
        if (isElementInViewport(element)) {
            element.classList.add('visible');
        }
    });
}

/**
 * Efek sticky navbar saat scroll
 */
function initNavbarStickyEffect() {
    const navbar = document.querySelector('.navbar');
    
    if (!navbar) return;
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            navbar.classList.add('sticky', 'animate__animated', 'animate__fadeInDown');
        } else {
            navbar.classList.remove('sticky', 'animate__animated', 'animate__fadeInDown');
        }
    });
}

/**
 * Efek parallax untuk latar belakang
 */
function initParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    if (parallaxElements.length === 0) return;
    
    window.addEventListener('scroll', () => {
        const scrollY = window.scrollY;
        
        parallaxElements.forEach(element => {
            const speed = element.getAttribute('data-speed') || 0.3;
            element.style.transform = `translateY(${scrollY * speed}px)`;
        });
    });
}

/**
 * Animasi counter untuk statistik
 */
function initCounters() {
    const counters = document.querySelectorAll('.counter');
    
    if (counters.length === 0) return;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const countTo = parseInt(target.getAttribute('data-count') || target.innerText, 10);
                
                animateCounter(target, countTo);
                observer.unobserve(target);
            }
        });
    }, {
        threshold: 0.3
    });
    
    counters.forEach(counter => {
        observer.observe(counter);
    });
}

/**
 * Helper: Animasi penghitungan counter
 */
function animateCounter(element, countTo) {
    let currentCount = 0;
    const duration = 2000; // 2 seconds
    const interval = 20; // Update every 20ms
    const steps = duration / interval;
    const increment = countTo / steps;
    
    const timer = setInterval(() => {
        currentCount += increment;
        
        if (currentCount >= countTo) {
            clearInterval(timer);
            element.innerText = countTo;
        } else {
            element.innerText = Math.floor(currentCount);
        }
    }, interval);
}

/**
 * Animasi untuk teks (typing effect, dll)
 */
function initTextAnimations() {
    const typingElements = document.querySelectorAll('.typing-effect');
    
    typingElements.forEach(element => {
        const text = element.getAttribute('data-text') || element.innerText;
        element.innerText = '';
        
        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) {
                typeText(element, text);
                observer.unobserve(element);
            }
        }, {
            threshold: 0.5
        });
        
        observer.observe(element);
    });
}

/**
 * Helper: Efek typing teks
 */
function typeText(element, text, i = 0) {
    if (i < text.length) {
        element.innerText += text.charAt(i);
        setTimeout(() => {
            typeText(element, text, i + 1);
        }, 100);
    }
}

/**
 * Efek hover tambahan
 */
function initHoverEffects() {
    // Tambahkan kelas untuk elemen yang perlu efek hover
    const hoverElements = document.querySelectorAll('.nav-link, .footer-link');
    
    hoverElements.forEach(element => {
        element.classList.add('animated-link');
    });
    
    // Efek hover 3D untuk kartu
    document.querySelectorAll('.card-3d').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
        });
    });
}

/**
 * Animasi gelombang untuk footer
 */
function initAnimatedWaves() {
    const footer = document.querySelector('footer');
    
    if (footer && !footer.querySelector('.waves')) {
        const waves = document.createElement('div');
        waves.classList.add('waves');
        
        const wave1 = document.createElement('div');
        wave1.classList.add('wave', 'wave-1');
        
        const wave2 = document.createElement('div');
        wave2.classList.add('wave', 'wave-2');
        
        const wave3 = document.createElement('div');
        wave3.classList.add('wave', 'wave-3');
        
        waves.appendChild(wave1);
        waves.appendChild(wave2);
        waves.appendChild(wave3);
        
        footer.insertBefore(waves, footer.firstChild);
    }
}

/**
 * Animasi untuk logo
 */
function initLogoAnimation() {
    const logo = document.querySelector('.navbar-brand');
    
    if (logo) {
        logo.addEventListener('mouseenter', () => {
            logo.classList.add('animate__animated', 'animate__pulse');
        });
        
        logo.addEventListener('animationend', () => {
            logo.classList.remove('animate__animated', 'animate__pulse');
        });
    }
}

/**
 * Animasi partikel untuk hero section
 */
function initHeroParticles() {
    const heroSection = document.querySelector('.hero-section');
    
    if (heroSection && typeof particlesJS !== 'undefined') {
        // Load particle.js jika tersedia
        const particlesContainer = document.createElement('div');
        particlesContainer.id = 'particles-js';
        particlesContainer.style.position = 'absolute';
        particlesContainer.style.top = '0';
        particlesContainer.style.left = '0';
        particlesContainer.style.width = '100%';
        particlesContainer.style.height = '100%';
        particlesContainer.style.zIndex = '1';
        
        heroSection.insertBefore(particlesContainer, heroSection.firstChild);
        
        try {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80 },
                    color: { value: '#ffffff' },
                    shape: { type: 'circle' },
                    opacity: { value: 0.5 },
                    size: { value: 3 },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: '#ffffff',
                        opacity: 0.2,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2
                    }
                }
            });
        } catch (e) {
            console.log('particlesJS not available');
        }
    }
}

/**
 * Helper: Cek apakah elemen berada dalam viewport
 */
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

/**
 * Tambahkan delay pada animasi
 */
function addAnimationDelay(elements, delay) {
    if (elements.length === 0) return;
    
    elements.forEach((element, index) => {
        element.style.animationDelay = `${index * delay}s`;
    });
}

/**
 * Animasi preloader
 */
window.addEventListener('load', function() {
    const preloader = document.querySelector('.preloader');
    
    if (preloader) {
        setTimeout(() => {
            preloader.classList.add('fade-out');
            
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 500);
        }, 500);
    }
});