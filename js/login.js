import { initRoleSelector, bindModal } from './main.js';

document.addEventListener('DOMContentLoaded', () => {
    initRoleSelector();
    bindModal('[data-open-registration="non-teaching"]', 'modal-non-teaching-info');
});


