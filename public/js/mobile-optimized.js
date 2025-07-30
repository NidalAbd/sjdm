// Mobile-Optimized JavaScript for SJDM

// Mobile-optimized initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize mobile-specific features
    initializeMobileFeatures();
    
    // Initialize touch-friendly interactions
    initializeTouchInteractions();
    
    // Initialize performance optimizations
    initializePerformanceOptimizations();
});

// Mobile-specific feature initialization
function initializeMobileFeatures() {
    // Add mobile-specific classes to body
    if (window.innerWidth <= 768) {
        document.body.classList.add('mobile-device');
    }
    
    // Handle orientation changes
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            // Recalculate layout after orientation change
            window.dispatchEvent(new Event('resize'));
        }, 100);
    });
    
    // Handle viewport changes
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handleViewportChange();
        }, 250);
    });
}

// Touch-friendly interactions
function initializeTouchInteractions() {
    // Add touch feedback to buttons
    const buttons = document.querySelectorAll('.btn, .nav-link, .dropdown-item');
    
    buttons.forEach(button => {
        button.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.95)';
        });
        
        button.addEventListener('touchend', function() {
            this.style.transform = 'scale(1)';
        });
        
        button.addEventListener('touchcancel', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Improve dropdown interactions on mobile
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            // Close other dropdowns when opening a new one
            const openDropdowns = document.querySelectorAll('.dropdown-menu.show');
            openDropdowns.forEach(openDropdown => {
                if (openDropdown !== this.nextElementSibling) {
                    openDropdown.classList.remove('show');
                }
            });
        });
    });
    
    // Add swipe gestures for mobile navigation
    if (window.innerWidth <= 768) {
        initializeSwipeGestures();
    }
}

// Performance optimizations
function initializePerformanceOptimizations() {
    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
    
    // Debounce scroll events
    let scrollTimer;
    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function() {
            // Handle scroll-based features here
        }, 100);
    });
    
    // Optimize animations for mobile
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.body.classList.add('reduced-motion');
    }
}

// Handle viewport changes
function handleViewportChange() {
    const isMobile = window.innerWidth <= 768;
    const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
    
    // Update body classes
    document.body.classList.remove('mobile-device', 'tablet-device', 'desktop-device');
    
    if (isMobile) {
        document.body.classList.add('mobile-device');
    } else if (isTablet) {
        document.body.classList.add('tablet-device');
    } else {
        document.body.classList.add('desktop-device');
    }
    
    // Reinitialize mobile-specific features if needed
    if (isMobile) {
        initializeSwipeGestures();
    }
}

// Swipe gestures for mobile navigation
function initializeSwipeGestures() {
    let startX = 0;
    let startY = 0;
    let endX = 0;
    let endY = 0;
    
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
    });
    
    document.addEventListener('touchend', function(e) {
        endX = e.changedTouches[0].clientX;
        endY = e.changedTouches[0].clientY;
        
        handleSwipe();
    });
    
    function handleSwipe() {
        const diffX = startX - endX;
        const diffY = startY - endY;
        
        // Minimum swipe distance
        const minSwipeDistance = 50;
        
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > minSwipeDistance) {
            // Horizontal swipe
            if (diffX > 0) {
                // Swipe left - could be used for navigation
                console.log('Swipe left detected');
            } else {
                // Swipe right - could be used for navigation
                console.log('Swipe right detected');
            }
        } else if (Math.abs(diffY) > Math.abs(diffX) && Math.abs(diffY) > minSwipeDistance) {
            // Vertical swipe
            if (diffY > 0) {
                // Swipe up
                console.log('Swipe up detected');
            } else {
                // Swipe down
                console.log('Swipe down detected');
            }
        }
    }
}

// Mobile-optimized form handling
function initializeMobileForms() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        // Add mobile-friendly validation
        form.addEventListener('submit', function(e) {
            if (!validateMobileForm(this)) {
                e.preventDefault();
                showMobileAlert('Please check your input and try again.', 'warning');
            }
        });
        
        // Auto-focus on first input
        const firstInput = form.querySelector('input, textarea, select');
        if (firstInput && window.innerWidth <= 768) {
            setTimeout(() => {
                firstInput.focus();
            }, 300);
        }
    });
}

// Mobile form validation
function validateMobileForm(form) {
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.classList.add('error');
        } else {
            input.classList.remove('error');
        }
    });
    
    return isValid;
}

// Mobile-optimized alerts
function showMobileAlert(message, type = 'info') {
    const alert = document.createElement('div');
    alert.className = `mobile-alert alert-${type}`;
    alert.innerHTML = `
        <div class="alert-content">
            <span class="alert-message">${message}</span>
            <button class="alert-close" onclick="this.parentElement.parentElement.remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add styles
    alert.style.cssText = `
        position: fixed;
        top: 20px;
        left: 10px;
        right: 10px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        padding: 1rem;
        border-left: 4px solid var(--${type}-color);
    `;
    
    document.body.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alert.parentElement) {
            alert.remove();
        }
    }, 5000);
}

// Mobile-optimized loading states
function showMobileLoading(element) {
    element.classList.add('loading');
    element.disabled = true;
    
    // Add spinner if not present
    if (!element.querySelector('.spinner')) {
        const spinner = document.createElement('span');
        spinner.className = 'spinner';
        element.appendChild(spinner);
    }
}

function hideMobileLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;
    
    // Remove spinner
    const spinner = element.querySelector('.spinner');
    if (spinner) {
        spinner.remove();
    }
}

// Mobile-optimized navigation
function initializeMobileNavigation() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!navbarToggler.contains(e.target) && !navbarCollapse.contains(e.target)) {
                if (navbarCollapse.classList.contains('show')) {
                    navbarToggler.click();
                }
            }
        });
        
        // Close mobile menu when clicking on a link
        const mobileLinks = navbarCollapse.querySelectorAll('.nav-link');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth <= 991) {
                    navbarToggler.click();
                }
            });
        });
    }
}

// Mobile-optimized scroll handling
function initializeMobileScroll() {
    let lastScrollTop = 0;
    const navbar = document.querySelector('.modern-navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Scrolling down
                navbar.style.transform = 'translateY(-100%)';
            } else {
                // Scrolling up
                navbar.style.transform = 'translateY(0)';
            }
            
            lastScrollTop = scrollTop;
        });
    }
}

// Initialize all mobile features
document.addEventListener('DOMContentLoaded', function() {
    initializeMobileFeatures();
    initializeTouchInteractions();
    initializePerformanceOptimizations();
    initializeMobileForms();
    initializeMobileNavigation();
    initializeMobileScroll();
});

// Export functions for global use
window.mobileUtils = {
    showAlert: showMobileAlert,
    showLoading: showMobileLoading,
    hideLoading: hideMobileLoading,
    validateForm: validateMobileForm
}; 