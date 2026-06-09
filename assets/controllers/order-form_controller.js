import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    static targets = [
        'step1',
        'step2',
        'step3',
        'step4',
        "stepIndicator1",
        "stepIndicator2",
        "stepIndicator3",
        "stepIndicator4",
        'address',
        'zipCode',
        'city',
        'menuSelect',
        'menuPreview',
        'conditionsWrapper',
        'conditionsCheckbox',
        'menuInfo',
        'people',
        'summary'
    ];

    getFields(stepElement) {
        return stepElement.querySelectorAll('input, select, textarea');
    }

    resetField(field) {
        const container = field.type === 'checkbox'
            ? field.closest('.form-check')
            : field.parentElement;

        // remove invalid class
        if (field.type === 'checkbox') {
            container?.classList.remove('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }

        // remove error message
        container
            ?.querySelectorAll('.invalid-feedback')
            .forEach(el => el.remove());
    }


    getMenuMinPeople() {
        return parseInt(
            this.menuPreviewTarget
                ?.querySelector('.menu-preview')
                ?.dataset
                ?.menuMinPeople
        );
    }


    getMenuTitle() {
        return (
            this.menuPreviewTarget
                ?.querySelector('.menu-preview')
                ?.dataset
                ?.menuTitle
        );
    }


    // JS Validations for each step 
    validateDate(field) {
        if (field.type !== 'date' || !field.value) {
            return null;
        }

        const selectedDate = new Date(field.value + 'T00:00:00');
        const tomorrow = new Date();

        tomorrow.setHours(0, 0, 0, 0);
        tomorrow.setDate(tomorrow.getDate() + 1);

        if (selectedDate < tomorrow) {
            return "La prestation doit être réservée au minimum pour demain";
        }

        return null;
    }


    validateStep(stepElement) {

        const errors = [];

        this.getFields(stepElement).forEach(field => {

            this.resetField(field);

            // required field
            const requiredError = this.validateRequired(field);
            if (requiredError) {
                errors.push({
                    element: field,
                    message: requiredError
                });
            }

            // date rule
            const dateError = this.validateDate(field);
            if (dateError) {
                errors.push({
                    element: field,
                    message: dateError
                });
            }
        });

        return errors;
    }

    validateRequired(field) {
        if (
            field.hasAttribute('required')
            && !field.value.trim()
        ) {
            return field.dataset.errorMessage
                || 'Ce champ est obligatoire';
        }

        return null;
    }


    validateMinimumPeople() {
        const people = parseInt(this.peopleTarget.value);
        const menuMinPeople = this.getMenuMinPeople();

        if (menuMinPeople && people < menuMinPeople) {
            return {
                element: this.peopleTarget,
                message: `${menuMinPeople} personnes minimum pour ce menu`
            };
        }
        return null;
    }

    //Progress bar update
    updateStepper(currentStep) {
        const indicators = [
            this.stepIndicator1Target,
            this.stepIndicator2Target,
            this.stepIndicator3Target,
            this.stepIndicator4Target
        ];

        indicators.forEach((indicator, index) => {
            indicator.classList.toggle(
                "active",
                index + 1 <= currentStep
            );
        });
    }

    // Navigation between steps
    goToStep2() {

        const errors = this.validateStep(this.step1Target);
        if (errors.length > 0) {
            this.showErrors(errors);
            return;
        }

        this.updateStepper(2);
        this.step1Target.classList.add('hidden');
        this.step2Target.classList.remove('hidden');

    }


    goToStep3() {

        const errors = this.validateStep(this.step2Target);

        if (this.hasConditionsCheckboxTarget) {

            if (!this.conditionsCheckboxTarget.checked) {
                this.setError(
                    this.conditionsCheckboxTarget,
                    "Veuillez confirmer que vous avez lu les conditions"
                );
                return;
            }

        }

        if (errors.length > 0) {
            this.showErrors(errors);
            return;
        }

        const menuMinPeople = this.getMenuMinPeople();
        const menuTitle = this.getMenuTitle();

        this.menuInfoTarget.textContent =
    `Pour le menu choisi ${menuTitle}, le nombre minimum de personnes est ${menuMinPeople}`;

        this.updateStepper(3);
        this.step2Target.classList.add('hidden');
        this.step3Target.classList.remove('hidden');
    }

    goToStep4() {

        const errors = this.validateStep(this.step3Target);
        const minimumPeopleError = this.validateMinimumPeople();

        if (minimumPeopleError) {
            this.showErrors([minimumPeopleError]);
            return;
        }

        if (errors.length > 0) {
            this.showErrors(errors);
            return;
        }

        this.updateSummary();

        this.updateStepper(4);
        this.step3Target.classList.add('hidden');
        this.step4Target.classList.remove('hidden');
    }


    returnToStep(stepToHide, stepToShow) {
        stepToHide.classList.add('hidden');
        stepToShow.classList.remove('hidden');
    }


    returnToStep1() {
        this.returnToStep(this.step2Target, this.step1Target);
        this.updateStepper(1);
    }


    returnToStep2() {
        this.returnToStep(this.step3Target, this.step2Target);
        this.updateStepper(2);
    }
    

    returnToStep3() {
        this.returnToStep(this.step4Target, this.step3Target);
        this.updateStepper(3);
    }


    //error handling
    setError(field, message) {

        const container = field.type === 'checkbox'
        ? field.closest('.form-check')
        : field;

        container.classList.add('is-invalid');

        const feedback = document.createElement('div');
        feedback.classList.add('invalid-feedback', 'd-block');
        feedback.textContent = message;

        if (field.type === 'checkbox') {
            container.appendChild(feedback);
        } else {
            field.insertAdjacentElement('afterend', feedback);
        }
    }

    showErrors(errors) {

        errors.forEach(error => {
            this.setError(error.element, error.message);
        });

        const firstError = this.element.querySelector('.is-invalid');

        if (firstError) {
            firstError.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            firstError.focus?.();
        }
    }

    //update order
    async updateMenuPreview() {

        const menuId = this.menuSelectTarget?.value;

        if (!menuId) {
            this.menuPreviewTarget.innerHTML = ''
            return;
        }

        try {
            const response = await fetch(
                this.element.dataset.menuUrl,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        menuId
                    })
                }
            );

            const data = await response.json();
            this.menuPreviewTarget.innerHTML = data.menu_html;

        } catch (error) {
            console.error(error);
            this.menuPreviewTarget.innerHTML =
                '<p class="text-danger">Erreur lors du chargement du menu</p>';
        }
    }


    async updateSummary() {

        const menuId = this.menuSelectTarget?.value;
        const people = parseInt(this.peopleTarget?.value);

        const address = {
            street: this.addressTarget?.value || '',
            zip: this.zipCodeTarget?.value || '',
            city: this.cityTarget?.value || ''
        };

        if (!menuId || !people || !address.street || !address.zip || !address.city) {
            this.summaryTarget.innerHTML =
                '<p class="text-warning">Complétez les informations pour voir le prix</p>';
            return;
        }


        try {
            const response = await fetch(this.element.dataset.summaryUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ menuId, people, address })
            });


            let data;

            try {
                data = await response.json();
            } catch (e) {
                console.error('JSON invalide', e);
                data = null;
            }

            if (!data) {
                this.summaryTarget.innerHTML =
                    '<p class="text-danger">Erreur lors du calcul du prix</p>';
                return;
            }

            this.summaryTarget.innerHTML =
                data.summary_html || '<p class="text-danger">Erreur lors du calcul du prix</p>';

            if (!response.ok) {
                console.error('Erreur HTTP:', data);
            }

        } catch (e) {
            console.error('Erreur réseau:', e);
            this.summaryTarget.innerHTML =
                '<p class="text-danger">Erreur réseau lors du calcul du prix</p>';
        }
    }

}