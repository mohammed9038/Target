/**
 * Target Management System - UX Enhancements
 * Advanced user experience improvements
 */

class UXEnhancements {
    constructor() {
        this.init();
        this.setupKeyboardShortcuts();
        this.setupAdvancedInteractions();
        this.setupAccessibilityFeatures();
    }

    init() {
        // Add smooth loading transitions
        this.setupPageTransitions();
        
        // Setup advanced tooltips
        this.setupTooltips();
        
        // Setup confirmation dialogs
        this.setupConfirmations();
        
        // Setup auto-complete features
        this.setupAutoComplete();
        
        // Setup offline detection
        this.setupOfflineDetection();
    }

    setupPageTransitions() {
        // Add loading state to page
        document.body.classList.add('page-loading');
        
        // Remove loading after content is ready
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                document.body.classList.remove('page-loading');
                document.body.classList.add('page-loaded');
                
                // Animate elements on scroll
                this.animateElementsOnScroll();
            }, 300);
        });
    }

    setupTooltips() {
        // Initialize Bootstrap tooltips with enhanced options
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                delay: { show: 500, hide: 100 },
                placement: 'auto',
                trigger: 'hover focus'
            });
        });

        // Add smart tooltips for truncated text
        this.addSmartTooltips();
    }

    addSmartTooltips() {
        const elements = document.querySelectorAll('.text-truncate, [data-smart-tooltip]');
        elements.forEach(element => {
            if (element.offsetWidth < element.scrollWidth) {
                element.setAttribute('data-bs-toggle', 'tooltip');
                element.setAttribute('title', element.textContent.trim());
                new bootstrap.Tooltip(element);
            }
        });
    }

    setupConfirmations() {
        // Add confirmation dialogs for destructive actions
        const destructiveActions = document.querySelectorAll('[data-confirm]');
        destructiveActions.forEach(element => {
            element.addEventListener('click', (e) => {
                const message = element.dataset.confirm || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });
    }

    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + S to save
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const saveBtn = document.getElementById('saveAllBtn');
                if (saveBtn && !saveBtn.disabled) {
                    saveBtn.click();
                }
            }

            // Ctrl/Cmd + F to focus search
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                const searchInput = document.getElementById('search_input');
                if (searchInput) {
                    e.preventDefault();
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // Escape to close modals
            if (e.key === 'Escape') {
                const openModals = document.querySelectorAll('.modal.show');
                openModals.forEach(modal => {
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                });
            }

            // Arrow key navigation for table inputs
            this.handleTableNavigation(e);
        });
    }

    handleTableNavigation(e) {
        if (!['ArrowUp', 'ArrowDown', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key)) {
            return;
        }

        const activeElement = document.activeElement;
        if (!activeElement.classList.contains('target-input')) {
            return;
        }

        const table = activeElement.closest('table');
        if (!table) return;

        const currentCell = activeElement.closest('td');
        const currentRow = currentCell.closest('tr');
        const allRows = Array.from(table.querySelectorAll('tbody tr'));
        const cellsInRow = Array.from(currentRow.querySelectorAll('.target-input'));
        const currentRowIndex = allRows.indexOf(currentRow);
        const currentCellIndex = cellsInRow.indexOf(activeElement);

        let nextInput = null;

        switch (e.key) {
            case 'ArrowUp':
                if (currentRowIndex > 0) {
                    const prevRow = allRows[currentRowIndex - 1];
                    const prevRowInputs = prevRow.querySelectorAll('.target-input');
                    nextInput = prevRowInputs[currentCellIndex];
                }
                break;

            case 'ArrowDown':
                if (currentRowIndex < allRows.length - 1) {
                    const nextRow = allRows[currentRowIndex + 1];
                    const nextRowInputs = nextRow.querySelectorAll('.target-input');
                    nextInput = nextRowInputs[currentCellIndex];
                }
                break;

            case 'ArrowLeft':
                if (currentCellIndex > 0) {
                    nextInput = cellsInRow[currentCellIndex - 1];
                }
                break;

            case 'ArrowRight':
            case 'Tab':
                if (currentCellIndex < cellsInRow.length - 1) {
                    nextInput = cellsInRow[currentCellIndex + 1];
                } else if (currentRowIndex < allRows.length - 1) {
                    const nextRow = allRows[currentRowIndex + 1];
                    nextInput = nextRow.querySelector('.target-input');
                }
                break;
        }

        if (nextInput) {
            e.preventDefault();
            nextInput.focus();
            nextInput.select();
        }
    }

    setupAutoComplete() {
        // Add auto-complete for commonly used values
        const targetInputs = document.querySelectorAll('.target-input');
        targetInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.debounce(() => {
                    this.showAutoCompleteHints(e.target);
                }, 300)();
            });
        });
    }

    showAutoCompleteHints(input) {
        const value = input.value;
        if (!value || value.length < 2) return;

        // Simple auto-complete logic - can be enhanced with API calls
        const commonValues = ['1000', '1500', '2000', '2500', '3000', '5000'];
        const matches = commonValues.filter(v => v.includes(value));

        if (matches.length > 0) {
            this.showHintDropdown(input, matches);
        }
    }

    showHintDropdown(input, suggestions) {
        // Remove existing dropdown
        const existingDropdown = document.querySelector('.autocomplete-dropdown');
        if (existingDropdown) {
            existingDropdown.remove();
        }

        // Create new dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'autocomplete-dropdown';
        dropdown.style.cssText = `
            position: absolute;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            min-width: 100px;
            max-height: 200px;
            overflow-y: auto;
        `;

        suggestions.forEach(suggestion => {
            const item = document.createElement('div');
            item.textContent = suggestion;
            item.className = 'autocomplete-item';
            item.style.cssText = `
                padding: 0.5rem 0.75rem;
                cursor: pointer;
                border-bottom: 1px solid #f1f5f9;
            `;
            
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = '#f3f4f6';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = 'white';
            });
            
            item.addEventListener('click', () => {
                input.value = suggestion;
                input.dispatchEvent(new Event('input', { bubbles: true }));
                dropdown.remove();
            });

            dropdown.appendChild(item);
        });

        // Position dropdown
        const rect = input.getBoundingClientRect();
        dropdown.style.left = rect.left + 'px';
        dropdown.style.top = (rect.bottom + 2) + 'px';

        document.body.appendChild(dropdown);

        // Close dropdown when clicking outside
        const closeDropdown = (e) => {
            if (!dropdown.contains(e.target) && e.target !== input) {
                dropdown.remove();
                document.removeEventListener('click', closeDropdown);
            }
        };
        setTimeout(() => document.addEventListener('click', closeDropdown), 100);
    }

    setupOfflineDetection() {
        const showOfflineStatus = () => {
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-warning border-0';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-wifi-off me-2"></i>
                        You're offline. Changes will be saved when connection is restored.
                    </div>
                </div>
            `;
            
            const container = document.getElementById('toast-container') || this.createToastContainer();
            container.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast, { autohide: false });
            bsToast.show();
            
            return toast;
        };

        const showOnlineStatus = (offlineToast) => {
            if (offlineToast) {
                const bsToast = bootstrap.Toast.getInstance(offlineToast);
                if (bsToast) bsToast.hide();
            }

            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-wifi me-2"></i>
                        Connection restored!
                    </div>
                </div>
            `;
            
            const container = document.getElementById('toast-container') || this.createToastContainer();
            container.appendChild(toast);
            
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
        };

        let offlineToast = null;

        window.addEventListener('offline', () => {
            offlineToast = showOfflineStatus();
        });

        window.addEventListener('online', () => {
            showOnlineStatus(offlineToast);
            offlineToast = null;
        });
    }

    setupAccessibilityFeatures() {
        // Add skip links
        this.addSkipLinks();
        
        // Improve focus management
        this.improveFocusManagement();
        
        // Add ARIA labels where needed
        this.addAriaLabels();
        
        // Setup reduced motion detection
        this.setupReducedMotion();
    }

    addSkipLinks() {
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.textContent = 'Skip to main content';
        skipLink.className = 'skip-link visually-hidden-focusable';
        skipLink.style.cssText = `
            position: absolute;
            top: -40px;
            left: 6px;
            background: #007bff;
            color: white;
            padding: 8px;
            text-decoration: none;
            border-radius: 4px;
            z-index: 2000;
        `;
        skipLink.addEventListener('focus', () => {
            skipLink.style.top = '6px';
        });
        skipLink.addEventListener('blur', () => {
            skipLink.style.top = '-40px';
        });

        document.body.insertBefore(skipLink, document.body.firstChild);
    }

    improveFocusManagement() {
        // Trap focus in modals
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.addEventListener('shown.bs.modal', () => {
                const focusableElements = modal.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                if (focusableElements.length > 0) {
                    focusableElements[0].focus();
                }
            });
        });

        // Restore focus when modal closes
        let lastFocusedElement = null;
        document.addEventListener('focusin', (e) => {
            if (!e.target.closest('.modal')) {
                lastFocusedElement = e.target;
            }
        });

        modals.forEach(modal => {
            modal.addEventListener('hidden.bs.modal', () => {
                if (lastFocusedElement) {
                    lastFocusedElement.focus();
                }
            });
        });
    }

    addAriaLabels() {
        // Add missing ARIA labels
        const buttons = document.querySelectorAll('button:not([aria-label]):not([title])');
        buttons.forEach(button => {
            const icon = button.querySelector('i[class*="bi-"]');
            if (icon && !button.textContent.trim()) {
                const iconClass = icon.className;
                if (iconClass.includes('bi-check')) {
                    button.setAttribute('aria-label', 'Save');
                } else if (iconClass.includes('bi-x')) {
                    button.setAttribute('aria-label', 'Close');
                } else if (iconClass.includes('bi-pencil')) {
                    button.setAttribute('aria-label', 'Edit');
                }
            }
        });

        // Add ARIA labels to form controls
        const inputs = document.querySelectorAll('input:not([aria-label]):not([aria-labelledby])');
        inputs.forEach(input => {
            const label = document.querySelector(`label[for="${input.id}"]`);
            if (!label && input.placeholder) {
                input.setAttribute('aria-label', input.placeholder);
            }
        });
    }

    setupReducedMotion() {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.body.classList.add('reduced-motion');
        }
    }

    animateElementsOnScroll() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            observer.observe(el);
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

    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
}

// Enhanced form validation
class FormEnhancements {
    constructor() {
        this.setupAdvancedValidation();
        this.setupFormProgress();
    }

    setupAdvancedValidation() {
        const forms = document.querySelectorAll('form[data-enhanced-validation]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });

            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => this.validateField(input));
                input.addEventListener('input', () => this.clearErrors(input));
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        // Required field validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required';
        }

        // Email validation
        if (field.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                message = 'Please enter a valid email address';
            }
        }

        // Number validation
        if (field.type === 'number' && value) {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);

            if (isNaN(numValue)) {
                isValid = false;
                message = 'Please enter a valid number';
            } else if (min && numValue < parseFloat(min)) {
                isValid = false;
                message = `Value must be at least ${min}`;
            } else if (max && numValue > parseFloat(max)) {
                isValid = false;
                message = `Value must be at most ${max}`;
            }
        }

        this.showFieldValidation(field, isValid, message);
        return isValid;
    }

    showFieldValidation(field, isValid, message) {
        field.classList.remove('is-valid', 'is-invalid');
        
        if (isValid) {
            field.classList.add('is-valid');
        } else {
            field.classList.add('is-invalid');
        }

        // Show/hide error message
        let errorElement = field.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            field.parentNode.appendChild(errorElement);
        }
        
        errorElement.textContent = message;
    }

    clearErrors(field) {
        field.classList.remove('is-invalid');
    }

    setupFormProgress() {
        const forms = document.querySelectorAll('form[data-show-progress]');
        forms.forEach(form => {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            const progressBar = this.createProgressBar();
            form.insertBefore(progressBar, form.firstChild);

            const updateProgress = () => {
                const completed = Array.from(inputs).filter(input => input.value.trim()).length;
                const percentage = Math.round((completed / inputs.length) * 100);
                progressBar.querySelector('.progress-bar').style.width = percentage + '%';
                progressBar.querySelector('.progress-bar').textContent = percentage + '%';
            };

            inputs.forEach(input => {
                input.addEventListener('input', updateProgress);
            });

            updateProgress();
        });
    }

    createProgressBar() {
        const container = document.createElement('div');
        container.className = 'mb-3';
        container.innerHTML = `
            <small class="text-muted">Form completion:</small>
            <div class="progress mt-1" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 0%"></div>
            </div>
        `;
        return container;
    }
}

// Initialize when DOM loads
document.addEventListener('DOMContentLoaded', () => {
    window.uxEnhancements = new UXEnhancements();
    window.formEnhancements = new FormEnhancements();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { UXEnhancements, FormEnhancements };
}