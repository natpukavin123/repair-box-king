import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

// Defer Alpine.start() so inline @push('scripts') can register components first
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
