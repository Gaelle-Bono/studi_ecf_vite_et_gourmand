import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    toggle() {
        const input = this.element.querySelector('input');
        const icon = this.element.querySelector('i');
        const button = this.element.querySelector('button');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
            button.setAttribute('aria-label', 'Masquer le mot de passe');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
            button.setAttribute('aria-label', 'Afficher le mot de passe');
        }
    }
}