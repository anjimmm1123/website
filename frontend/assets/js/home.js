/**
 * Home page specific JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize counters if they exist
    initCounters();
    
    // Function to animate counter numbers from 0 to their target value
    function initCounters() {
        // Make sure the animateCounter function from animations.js is available
        if (typeof animateCounter !== 'function') {
            console.warn('animateCounter function not found');
            return;
        }
        
        const counterElements = document.querySelectorAll('.counter-number');
        
        // Handle counter animations on scroll
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                rootMargin: '0px',
                threshold: 0.1
            });
            
            counterElements.forEach(counter => {
                observer.observe(counter);
            });
        } else {
            // Fallback for browsers that don't support IntersectionObserver
            counterElements.forEach(counter => {
                animateCounter(counter);
            });
        }
    }
    
    // Program card hover effects
    const programCards = document.querySelectorAll('.program-card');
    programCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.program-img');
            if (image) {
                image.style.transform = 'scale(1.05)';
                image.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.program-img');
            if (image) {
                image.style.transform = 'scale(1)';
            }
        });
    });
    
    // CTA button pulse effect
    const ctaButton = document.querySelector('.cta-section .btn');
    if (ctaButton) {
        setInterval(() => {
            ctaButton.classList.add('pulse');
            setTimeout(() => {
                ctaButton.classList.remove('pulse');
            }, 1000);
        }, 3000);
    }
});