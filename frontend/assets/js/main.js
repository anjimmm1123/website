/**
 * STMIK Enterprise - Main JavaScript
 * Script utama untuk fungsi-fungsi umum website
 */

document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi elemen-elemen umum
    initScrollToTop();
    initMobileMenu();
    initDropdowns();
    initTooltips();
    initTabs();
    initFormValidation();
    initAccordions();
    initPreloader();
    initSearchBox();
    initGalleryLightbox();
    initAjaxLinks();
    initToasts();
    initBackToTop();
});

/**
 * Tombol scroll to top
 */
function initScrollToTop() {
    const backToTopButton = document.querySelector('.back-to-top');
    
    if (!backToTopButton) {
        // Buat tombol jika belum ada
        const button = document.createElement('a');
        button.classList.add('back-to-top');
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        document.body.appendChild(button);
        
        button.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                button.classList.add('visible');
            } else {
                button.classList.remove('visible');
            }
        });
    }
}

/**
 * Menu mobile (hamburger)
 */
function initMobileMenu() {
    const hamburgerButton = document.querySelector('.hamburger-menu');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (hamburgerButton && mobileMenu) {
        hamburgerButton.addEventListener('click', () => {
            hamburgerButton.classList.toggle('active');
            mobileMenu.classList.toggle('active');
        });
        
        // Close menu on click outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !hamburgerButton.contains(e.target)) {
                hamburgerButton.classList.remove('active');
                mobileMenu.classList.remove('active');
            }
        });
    }
}

/**
 * Dropdown menus
 */
function initDropdowns() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const parent = toggle.parentElement;
            const dropdown = parent.querySelector('.dropdown-menu');
            
            // Close other dropdowns
            document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.remove('active');
                }
            });
            
            dropdown.classList.toggle('active');
        });
    });
    
    // Close dropdowns on click outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown-menu.active').forEach(menu => {
            menu.classList.remove('active');
        });
    });
}

/**
 * Tooltips
 */
function initTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', () => {
            const tooltipText = element.getAttribute('data-tooltip');
            
            const tooltip = document.createElement('div');
            tooltip.classList.add('tooltip');
            tooltip.textContent = tooltipText;
            
            document.body.appendChild(tooltip);
            
            const elementRect = element.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            
            const top = elementRect.top - tooltipRect.height - 10;
            const left = elementRect.left + (elementRect.width / 2) - (tooltipRect.width / 2);
            
            tooltip.style.top = `${top + window.scrollY}px`;
            tooltip.style.left = `${left}px`;
            
            tooltip.classList.add('visible');
            
            element.addEventListener('mouseleave', () => {
                tooltip.remove();
            });
        });
    });
}

/**
 * Custom tabs
 */
function initTabs() {
    const tabGroups = document.querySelectorAll('.tabs');
    
    tabGroups.forEach(tabGroup => {
        const tabLinks = tabGroup.querySelectorAll('.tab-link');
        const tabContents = tabGroup.querySelectorAll('.tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Deactivate all tabs
                tabLinks.forEach(l => l.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Activate clicked tab
                link.classList.add('active');
                
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    target.classList.add('active');
                }
            });
        });
        
        // Activate first tab by default if none active
        if (!tabGroup.querySelector('.tab-link.active')) {
            const firstTab = tabGroup.querySelector('.tab-link');
            if (firstTab) {
                firstTab.click();
            }
        }
    });
}

/**
 * Form validation
 */
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            let isValid = true;
            
            // Check required fields
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    showFieldError(field, 'Field ini harus diisi');
                } else {
                    clearFieldError(field);
                }
            });
            
            // Check email fields
            const emailFields = form.querySelectorAll('[type="email"]');
            emailFields.forEach(field => {
                if (field.value.trim() && !isValidEmail(field.value)) {
                    isValid = false;
                    showFieldError(field, 'Format email tidak valid');
                }
            });
            
            // Check password confirmation
            const passwordField = form.querySelector('[name="password"]');
            const confirmField = form.querySelector('[name="confirm_password"]');
            
            if (passwordField && confirmField) {
                if (passwordField.value !== confirmField.value) {
                    isValid = false;
                    showFieldError(confirmField, 'Password tidak sama');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Helper: Show form field error
 */
function showFieldError(field, message) {
    // Clear existing error
    clearFieldError(field);
    
    field.classList.add('is-invalid');
    
    const errorElement = document.createElement('div');
    errorElement.classList.add('invalid-feedback');
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
}

/**
 * Helper: Clear form field error
 */
function clearFieldError(field) {
    field.classList.remove('is-invalid');
    
    const errorElement = field.parentNode.querySelector('.invalid-feedback');
    if (errorElement) {
        errorElement.remove();
    }
}

/**
 * Helper: Validate email
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Accordions
 */
function initAccordions() {
    const accordionToggles = document.querySelectorAll('.accordion-toggle');
    
    accordionToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const content = toggle.nextElementSibling;
            
            toggle.classList.toggle('active');
            
            if (toggle.classList.contains('active')) {
                content.style.maxHeight = content.scrollHeight + 'px';
            } else {
                content.style.maxHeight = '0';
            }
        });
    });
    
    // Open first accordion by default
    const firstToggle = document.querySelector('.accordion-toggle');
    if (firstToggle && !document.querySelector('.accordion-toggle.active')) {
        firstToggle.click();
    }
}

/**
 * Preloader
 */
function initPreloader() {
    if (!document.querySelector('.preloader')) {
        // Buat preloader jika belum ada
        const preloader = document.createElement('div');
        preloader.classList.add('preloader');
        
        const spinner = document.createElement('div');
        spinner.classList.add('spinner');
        
        preloader.appendChild(spinner);
        document.body.appendChild(preloader);
    }
}

/**
 * Search box
 */
function initSearchBox() {
    const searchToggle = document.querySelector('.search-toggle');
    const searchBox = document.querySelector('.search-box');
    
    if (searchToggle && searchBox) {
        searchToggle.addEventListener('click', (e) => {
            e.preventDefault();
            searchBox.classList.toggle('active');
            
            if (searchBox.classList.contains('active')) {
                searchBox.querySelector('input').focus();
            }
        });
        
        // Close search box on click outside
        document.addEventListener('click', (e) => {
            if (!searchBox.contains(e.target) && !searchToggle.contains(e.target)) {
                searchBox.classList.remove('active');
            }
        });
    }
}

/**
 * Gallery lightbox
 */
function initGalleryLightbox() {
    const galleryItems = document.querySelectorAll('.gallery-item a');
    
    if (galleryItems.length > 0) {
        // Include required libraries if not already included
        if (typeof GLightbox === 'undefined') {
            // Use CSS and JS from CDN
            const glightboxCSS = document.createElement('link');
            glightboxCSS.rel = 'stylesheet';
            glightboxCSS.href = 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/css/glightbox.min.css';
            document.head.appendChild(glightboxCSS);
            
            const glightboxJS = document.createElement('script');
            glightboxJS.src = 'https://cdn.jsdelivr.net/npm/glightbox@3.2.0/dist/js/glightbox.min.js';
            glightboxJS.onload = initGLightbox;
            document.body.appendChild(glightboxJS);
        } else {
            initGLightbox();
        }
    }
}

/**
 * Helper: Initialize GLightbox
 */
function initGLightbox() {
    if (typeof GLightbox !== 'undefined') {
        const lightbox = GLightbox({
            selector: '.gallery-item a',
            touchNavigation: true,
            loop: true,
            autoplayVideos: true
        });
    }
}

/**
 * Ajax link handling (SPA-like experience)
 */
function initAjaxLinks() {
    const ajaxLinks = document.querySelectorAll('a[data-ajax]');
    
    ajaxLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            const url = link.getAttribute('href');
            const target = link.getAttribute('data-target') || '#main-content';
            
            loadContent(url, target);
        });
    });
}

/**
 * Helper: Load content via AJAX
 */
async function loadContent(url, target) {
    const targetEl = typeof target === 'string' ? document.querySelector(target) : target;
    
    if (!targetEl) return;
    
    // Show loading
    showLoading();
    
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        const content = doc.querySelector(target);
        
        if (content) {
            // Update content with fade transition
            targetEl.style.opacity = '0';
            
            setTimeout(() => {
                targetEl.innerHTML = content.innerHTML;
                targetEl.style.opacity = '1';
                
                // Update page title if possible
                const title = doc.querySelector('title');
                if (title) {
                    document.title = title.textContent;
                }
                
                // Re-initialize components in new content
                initDropdowns();
                initTooltips();
                initTabs();
                initFormValidation();
                initAccordions();
                
                // Update URL without reloading page
                window.history.pushState({}, '', url);
                
                // Hide loading
                hideLoading();
            }, 300);
        } else {
            throw new Error('Target content not found in response');
        }
    } catch (error) {
        console.error('Error loading content:', error);
        showToast('Error loading content', 'error');
        hideLoading();
    }
}

/**
 * Toast notifications
 */
function initToasts() {
    if (!document.querySelector('.toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.classList.add('toast-container');
        document.body.appendChild(toastContainer);
    }
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const container = document.querySelector('.toast-container');
    
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.classList.add('toast', `toast-${type}`);
    toast.innerText = message;
    
    container.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        
        toast.addEventListener('transitionend', () => {
            toast.remove();
        });
    }, 3000);
}

/**
 * Loading overlay
 */
function showLoading() {
    let loadingOverlay = document.querySelector('.loading-overlay');
    
    if (!loadingOverlay) {
        loadingOverlay = document.createElement('div');
        loadingOverlay.classList.add('loading-overlay');
        
        const spinner = document.createElement('div');
        spinner.classList.add('loading-spinner');
        
        loadingOverlay.appendChild(spinner);
        document.body.appendChild(loadingOverlay);
    }
    
    loadingOverlay.classList.add('active');
    document.body.classList.add('loading');
}

/**
 * Hide loading overlay
 */
function hideLoading() {
    const loadingOverlay = document.querySelector('.loading-overlay');
    
    if (loadingOverlay) {
        loadingOverlay.classList.remove('active');
        document.body.classList.remove('loading');
    }
}

/**
 * Back to top button
 */
function initBackToTop() {
    const backToTop = document.querySelector('.back-to-top');
    
    if (!backToTop) {
        const button = document.createElement('button');
        button.classList.add('back-to-top');
        button.innerHTML = '<i class="fas fa-arrow-up"></i>';
        document.body.appendChild(button);
        
        button.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Show/hide based on scroll position
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                button.classList.add('visible');
            } else {
                button.classList.remove('visible');
            }
        });
    }
}