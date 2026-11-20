import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['checkbox', 'includedEquipmentDescription', 'description'];

    connect() {
        this.toggle();
    }

    toggle() {
        if (this.checkboxTarget.checked) {
            this.descriptionTarget.hidden = false;
        } else {
            this.descriptionTarget.hidden = true;
        }

        if (!this.checkboxTarget.checked) {
            this.includedEquipmentDescriptionTarget.value = '';
        }
    }
    
}