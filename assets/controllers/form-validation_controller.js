import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.element.addEventListener('submit', () => {
            this.scrollToFirstError();
        });
    }

    scrollToFirstError() {
        const firstError = this.element.querySelector('.is-invalid');

        if (!firstError) return;

        firstError.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });

        firstError.focus?.();
    }
}