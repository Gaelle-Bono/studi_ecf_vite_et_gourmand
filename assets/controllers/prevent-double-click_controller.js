import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    disable(event) {
        if (this.element.dataset.clicked) {
            event.preventDefault();
            return;
        }

        this.element.dataset.clicked = true;
    }
}