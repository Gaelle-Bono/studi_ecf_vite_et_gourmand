import './stimulus_bootstrap.js';

import './styles/app.css';

import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

document.addEventListener('turbo:load', () => {
    document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach((dropdown) => {
        new bootstrap.Dropdown(dropdown);
    });
});

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');