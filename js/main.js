/* ==========================================================================
   NEXERA Main JavaScript
   Handles UI interactions, animations, and progressive enhancement.
   ========================================================================= */

document.addEventListener('DOMContentLoaded', () => {
    const scrollLinks = document.querySelectorAll('a[href^="#"]');

    scrollLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            const targetId = link.getAttribute('href')?.substring(1);
            if (!targetId) return;

            const target = document.getElementById(targetId);
            if (target) {
                event.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    const particles = document.querySelector('[data-particles]');
    if (particles) {
        createParticles(particles);
    }
});

function createParticles(container) {
    const particleCount = 45;
    for (let i = 0; i < particleCount; i += 1) {
        const particle = document.createElement('span');
        particle.classList.add('particle');
        particle.style.left = `${Math.random() * 100}%`;
        particle.style.top = `${Math.random() * 100}%`;
        particle.style.animationDelay = `${Math.random() * 5}s`;
        particle.style.animationDuration = `${5 + Math.random() * 10}s`;
        container.appendChild(particle);
    }
}

// Modal utilities
export function bindModal(triggerSelector, modalId) {
    const triggers = document.querySelectorAll(triggerSelector);
    const modal = document.getElementById(modalId);
    if (!modal) return;

    const closeModal = () => modal.classList.remove('is-visible');

    modal.addEventListener('click', (event) => {
        if (event.target === modal || event.target.hasAttribute('data-close-modal')) {
            closeModal();
        }
    });

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            modal.classList.add('is-visible');
        });
    });
}

// Role selector logic for login page
export function initRoleSelector() {
    // Initialize all role wrappers (including nested ones)
    function initWrapper(wrapper, autoActivateFirst = true) {
        const buttons = wrapper.querySelectorAll('.role-selector [data-role-select]');
        const panels = wrapper.querySelectorAll('[data-role-panel]');

        if (!buttons.length || !panels.length) return;

        const defaultRole = buttons[0].dataset.roleSelect;

        const activatePanel = (role) => {
            buttons.forEach((btn) => {
                const isActive = btn.dataset.roleSelect === role;
                btn.classList.toggle('is-active', isActive);
                btn.setAttribute('aria-selected', String(isActive));
            });

            panels.forEach((panel) => {
                const panelRole = panel.dataset.rolePanel;
                // Show panel if it matches the selected role
                panel.hidden = panelRole !== role;
            });
        };

        buttons.forEach((button) => {
            button.addEventListener('click', () => {
                const role = button.dataset.roleSelect;
                activatePanel(role);
            });
        });

        if (autoActivateFirst) {
            activatePanel(defaultRole);
        }
        
        return { activatePanel, defaultRole };
    }

    // Initialize primary wrapper
    document.querySelectorAll('[data-role-wrapper]').forEach((wrapper) => {
        const primaryHandler = initWrapper(wrapper);
        
        // If this wrapper contains a nested staff wrapper, initialize it too
        const staffWrapper = wrapper.querySelector('[data-role-wrapper="staff"]');
        if (staffWrapper) {
            const staffHandler = initWrapper(staffWrapper, false);
            
            // When staff panel is shown, activate first staff sub-option
            const staffButton = wrapper.querySelector('[data-role-select="staff"]');
            if (staffButton && staffHandler) {
                staffButton.addEventListener('click', () => {
                    // Small delay to ensure panel is visible first
                    setTimeout(() => {
                        if (staffHandler && staffHandler.defaultRole) {
                            staffHandler.activatePanel(staffHandler.defaultRole);
                        }
                    }, 10);
                });
            }
            
            // Also handle direct clicks on staff sub-buttons
            const staffSubButtons = staffWrapper.querySelectorAll('.role-selector [data-role-select]');
            staffSubButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const role = btn.dataset.roleSelect;
                    if (staffHandler) {
                        staffHandler.activatePanel(role);
                    }
                });
            });
        }
    });
}

// Dashboard placeholders
export function initDashboard() {
    bindModal('[data-open-development]', 'modal-development');
}


