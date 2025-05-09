// Counter Animation
function animateCounter(element) {
    const target = parseInt(element.textContent);
    const duration = 2000; // 2 seconds
    const step = target / (duration / 16); // 60fps
    let current = 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Intersection Observer for Counter Animation
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const counter = entry.target.querySelector('.counter');
            if (counter) {
                animateCounter(counter);
            }
            counterObserver.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.5
});

// Observe all stat items
document.querySelectorAll('.stat-item').forEach(item => {
    counterObserver.observe(item);
});

// Smooth Scroll for Navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Parallax Effect for Hero Section
window.addEventListener('scroll', () => {
    const heroSection = document.querySelector('.about-hero-section');
    if (heroSection) {
        const scrolled = window.pageYOffset;
        heroSection.style.backgroundPositionY = scrolled * 0.5 + 'px';
    }
});

// Initialize AOS
AOS.init({
    duration: 1000,
    once: true,
    offset: 100
});

// Team Member Hover Effect
document.querySelectorAll('.team-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-10px)';
        card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
    });
});

// Certificate Card Hover Effect
document.querySelectorAll('.certificate-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-5px)';
        card.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
    });
});

// Partner Logo Hover Effect
document.querySelectorAll('.partner-logo').forEach(logo => {
    logo.addEventListener('mouseenter', () => {
        logo.style.transform = 'scale(1.05)';
        logo.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        logo.querySelector('img').style.filter = 'grayscale(0%)';
    });

    logo.addEventListener('mouseleave', () => {
        logo.style.transform = 'scale(1)';
        logo.style.boxShadow = '0 5px 15px rgba(0,0,0,0.05)';
        logo.querySelector('img').style.filter = 'grayscale(100%)';
    });
}); 