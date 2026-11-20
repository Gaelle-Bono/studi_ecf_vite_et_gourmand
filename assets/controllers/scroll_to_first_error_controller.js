import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.scrollToFirstError();
    }

    scrollToFirstError() {
        const firstError = this.element.querySelector('.is-invalid');

        if (!firstError) {
            return;
        }

        firstError.scrollIntoView({
            behavior: "smooth",
            block: "center"
        });

        let field = firstError;

        if (!firstError.matches('input, select, textarea')) {
            field = firstError.querySelector('input, select, textarea');
        }

        if (field) {
            field.focus();
        }
    }
}