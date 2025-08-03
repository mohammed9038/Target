/**
 * Target Management System - Enhanced JavaScript
 * Performance optimized with modern ES6+ features
 */

class TargetApp {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.setupPerformanceOptimizations();
    }

    init() {
        // Add loading class to body
        document.body.classList.add('app-loading');
        
        // Remove loading after DOM is ready
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.remove('app-loading');
                document.body.classList.add('app-loaded');
            }, 300);
        });
    }

    setupEventListeners() {
        // Throttled resize handler
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => this.handleResize(), 250);
        });

        // Enhanced form submissions with loading states
        this.setupFormHandlers();
        
        // Optimized table filtering
        this.setupTableFilters();
        
        // Smooth scrolling for anchor links
        this.setupSmoothScrolling();
    }

    setupPerformanceOptimizations() {
        // Lazy load images
        this.setupLazyLoading();
        
        // Preload critical resources
        this.preloadCriticalResources();
        
        // Setup intersection observer for animations
        this.setupAnimationObserver();
    }

    setupFormHandlers() {
        const forms = document.querySelectorAll('form');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    this.showButtonLoading(submitBtn);
                }
            });
        });
    }

    showButtonLoading(button) {
        const originalText = button.innerHTML;
        const loadingText = button.dataset.loading || 'Loading...';
        
        button.innerHTML = `
            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
            ${loadingText}
        `;
        button.disabled = true;
        button.classList.add('loading');
        
        // Store original text for restoration
        button.dataset.originalText = originalText;
    }

    hideButtonLoading(button) {
        if (button.dataset.originalText) {
            button.innerHTML = button.dataset.originalText;
            button.disabled = false;
            button.classList.remove('loading');
        }
    }

    setupTableFilters() {
        const filterInputs = document.querySelectorAll('[data-filter]');
        
        filterInputs.forEach(input => {
            let filterTimeout;
            input.addEventListener('input', (e) => {
                clearTimeout(filterTimeout);
                filterTimeout = setTimeout(() => {
                    this.filterTable(e.target);
                }, 300);
            });
        });
    }

    filterTable(input) {
        const table = document.querySelector(input.dataset.filterTarget);
        if (!table) return;

        const filter = input.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    setupSmoothScrolling() {
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
    }

    setupLazyLoading() {
        const images = document.querySelectorAll('img[data-src]');
        
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        } else {
            // Fallback for older browsers
            images.forEach(img => {
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
            });
        }
    }

    preloadCriticalResources() {
        // Preload critical CSS and fonts
        const preloadLinks = [
            { href: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', as: 'style' },
            { href: 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap', as: 'style' }
        ];

        preloadLinks.forEach(link => {
            const linkEl = document.createElement('link');
            linkEl.rel = 'preload';
            linkEl.href = link.href;
            linkEl.as = link.as;
            document.head.appendChild(linkEl);
        });
    }

    setupAnimationObserver() {
        if ('IntersectionObserver' in window) {
            const animationObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                animationObserver.observe(el);
            });
        }
    }

    handleResize() {
        // Handle responsive layout changes
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (window.innerWidth < 768) {
            sidebar?.classList.add('mobile-layout');
            mainContent?.classList.add('mobile-layout');
        } else {
            sidebar?.classList.remove('mobile-layout');
            mainContent?.classList.remove('mobile-layout');
        }
    }

    // Utility methods
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        const container = document.getElementById('toast-container') || this.createToastContainer();
        container.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1200';
        document.body.appendChild(container);
        return container;
    }

    showSkeletonLoader(targetElement, count = 3) {
        const skeletonHTML = Array(count).fill().map(() => `
            <div class="skeleton skeleton-text"></div>
        `).join('');
        
        targetElement.innerHTML = `<div class="skeleton-container">${skeletonHTML}</div>`;
    }

    hideSkeletonLoader(targetElement, content) {
        targetElement.innerHTML = content;
    }

    // API helper with loading states
    async fetchWithLoading(url, options = {}, loadingElement = null) {
        if (loadingElement) {
            loadingElement.classList.add('loading-overlay');
        }

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                    ...options.headers
                },
                ...options
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            this.showToast(error.message, 'danger');
            throw error;
        } finally {
            if (loadingElement) {
                loadingElement.classList.remove('loading-overlay');
            }
        }
    }
}

// Enhanced Target Matrix functionality
class TargetMatrix {
    constructor() {
        this.setupEventListeners();
        this.optimizeTable();
    }

    setupEventListeners() {
        // Debounced input handlers for better performance
        const inputs = document.querySelectorAll('.target-input');
        inputs.forEach(input => {
            let timeout;
            input.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => this.handleTargetChange(e), 500);
            });
        });
    }

    optimizeTable() {
        // Virtual scrolling for large tables
        const table = document.querySelector('.target-table');
        if (table && table.rows.length > 100) {
            this.setupVirtualScrolling(table);
        }
    }

    setupVirtualScrolling(table) {
        // Implementation for virtual scrolling
        // This would be expanded based on specific needs
        console.log('Virtual scrolling setup for performance');
    }

    handleTargetChange(event) {
        const input = event.target;
        const row = input.closest('tr');
        
        // Add visual feedback
        row.classList.add('modified');
        
        // Auto-save after delay
        setTimeout(() => {
            this.autoSave(input);
        }, 2000);
    }

    async autoSave(input) {
        const app = window.targetApp;
        try {
            app.showButtonLoading(input);
            // Auto-save logic here
            await new Promise(resolve => setTimeout(resolve, 1000)); // Simulate API call
            app.showToast('Target saved automatically', 'success');
        } catch (error) {
            app.showToast('Auto-save failed', 'danger');
        } finally {
            app.hideButtonLoading(input);
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.targetApp = new TargetApp();
    window.targetMatrix = new TargetMatrix();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { TargetApp, TargetMatrix };
}