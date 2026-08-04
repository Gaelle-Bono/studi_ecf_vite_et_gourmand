import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    menuLoading = false;
    summaryLoading = false;
    dateLoading = false;
    isCompanyClosed = false;


    static targets = [
        'stepIndicator1',
        'stepIndicator2',
        'stepIndicator3',
        'stepIndicator4',
        'step1',
        'firstName',
        'lastName',
        'email',
        'phone',
        'serviceDate',
        'timeBlock',
        'requestedTime',
        'availabilityInfo',
        'address',
        'addressComplement',
        'zipCode',
        'city',
        'deliveryInstructions',
        'continueButtonStep1',
        'step2',
        'menuSelect',
        'menuPreview',
        'conditionsWrapper',
        'conditionsCheckbox',
        'menuInfo',
        'previousButtonStep2',
        'continueButtonStep2',
        'step3',
        'people',
        'summaryLoadingInfo',
        'previousButtonStep3',
        'continueButtonStep3',
        'step4',
        'summary',
        'previousButtonStep4',
        'submitButton'
    ];


    connect() {
        this.showStepContainingError();

        this.scrollToFirstError();

        const dateIsInvalid = this.serviceDateTarget.classList.contains('is-invalid');
        const noDate = !this.serviceDateTarget.value;

        if (noDate || dateIsInvalid) {
            this.requestedTimeTarget.disabled = true;
            this.timeBlockTarget.classList.add('opacity-50');
            return;
        }

        if (this.requestedTimeTarget.classList.contains("is-invalid")) {
                this.loadAvailableTimes();
        }
    }


    onConditionsChange() {
        if (this.conditionsCheckboxTarget.checked) {
            this.clearStimulusErrors(this.conditionsCheckboxTarget);
        }
    }


    getMenuMinPeople() {
        const menuPreview = this.menuPreviewTarget.querySelector('.menu-preview');
        const minPeople = menuPreview.dataset.menuMinPeople;

        return Number(minPeople);
    }

    getMenuTitle() {
        const menuPreview = this.menuPreviewTarget.querySelector('.menu-preview');

        return menuPreview.dataset.menuTitle;
    }

///////////////////////////////////ERROR HANDLING /////////////////////////////

     setError(field, message) {

        field.classList.add('is-invalid');

        const feedback = document.createElement('div');
        feedback.classList.add('invalid-feedback', 'd-block', 'stimulus-error');
        feedback.textContent = message;

        if (field.type === 'checkbox') {
            field.closest('.form-check').after(feedback);
        } else {
            field.insertAdjacentElement('afterend', feedback);
        }
    }

    showErrors(errors) {
        this.clearAllStimulusErrors();

        errors.forEach(error => {
            this.setError(error.element, error.message);
        });

        this.scrollToFirstError();
    }


    clearStimulusErrors(field) {

        field.classList.remove('is-invalid');

        let errorContainer;

        if (field.type === 'checkbox') {
            errorContainer = field.closest('.form-check').nextElementSibling;
        } else {
            errorContainer = field.nextElementSibling;
        }

        if (errorContainer && errorContainer.classList.contains('stimulus-error')) {
            errorContainer.remove();
        }
    }

    clearAllStimulusErrors() {
        this.element.querySelectorAll('.stimulus-error').forEach(error => error.remove());
    }

    clearInvalidState(field){
        if (field.type === 'checkbox') {
            field.closest('.form-check').classList.remove('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    }

    clearBackendErrors(field) {
        let container = field.parentElement;

        if (field.type === 'checkbox') {
            container = field.closest('.form-check');
        }

        container.querySelectorAll('.invalid-feedback')
            .forEach(error => error.remove());
    }

    clearAllErrors() {
        this.element.querySelectorAll('.is-invalid')
            .forEach(el => el.classList.remove('is-invalid'));

        this.element.querySelectorAll('.invalid-feedback')
            .forEach(el => el.remove());
    }


    renderError(container, message) {
        container.innerHTML = `
            <div class="alert alert-danger">
                ${message}
            </div>
        `;
    }


    showStepContainingError(){
        const firstError = this.element.querySelector('.is-invalid');

        if (!firstError) {
            return;
        }

        if (this.step1Target.contains(firstError)) {
            this.showStep(1);
            return;
        }

        if (this.step2Target.contains(firstError)) {
            this.showStep(2);
            return;
        }

        if (this.step3Target.contains(firstError)) {
            this.showStep(3);
            return;
        }

        if (this.step4Target.contains(firstError)) {
            this.showStep(4);
        }
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


    ////////////////////VALIDATIONS //////////////////////////////////////////////////

    validateRequired(field) {
        if (
            field.hasAttribute('required') 
            && !field.disabled 
            && !field.value.trim()) {
                return field.dataset.errorMessage || 'Ce champ est obligatoire';
        }
        return null;
    }


    validateMinimumServiceDate(field) {
        if (!field.value) {
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


    validateConditions() {
        if (this.hasConditionsCheckboxTarget && !this.conditionsCheckboxTarget.checked) {
            return "Veuillez confirmer que vous avez lu les conditions";
        }
        return null;
    }   

    
    validateMinimumPeople() {
        const people = Number(this.peopleTarget.value);
        const menuMinPeople = this.getMenuMinPeople();

        if (people < menuMinPeople) {
            return `${menuMinPeople} personnes minimum pour ce menu`;
        }
        return null;
    }


    getStep1ClientErrors() {

        const errors = [];

        const fields = [
            this.firstNameTarget,
            this.lastNameTarget,
            this.emailTarget,
            this.phoneTarget,
            this.serviceDateTarget,
            this.requestedTimeTarget,
            this.addressTarget,
            this.zipCodeTarget,
            this.cityTarget
        ];

        fields.forEach(field => {

            const requiredError = this.validateRequired(field);

            if (requiredError) {
                errors.push({
                    element: field,
                    message: requiredError
                });

                return;
            }

            if (field === this.serviceDateTarget) {

                const dateError = this.validateMinimumServiceDate(field);

                if (dateError) {
                    errors.push({
                        element: field,
                        message: dateError
                    });
                }
            }
        });

        return errors;
    }



    getStep2ClientError() {

        const requiredError = this.validateRequired(this.menuSelectTarget);

        if (requiredError) {
            return {
                element: this.menuSelectTarget,
                message: requiredError
            };
        }

        const conditionsError = this.validateConditions();

        if (conditionsError) {
            return {
                element: this.conditionsCheckboxTarget,
                message: conditionsError
            };
        }

        return null;
    }



    getStep3ClientError() {

        const requiredError = this.validateRequired(this.peopleTarget);

        if (requiredError) {
            return {
                element: this.peopleTarget,
                message: requiredError
            };
        }

        const minimumPeopleError = this.validateMinimumPeople();

        if (minimumPeopleError) {
            return {
                element: this.peopleTarget,
                message: minimumPeopleError
            };
        }

        return null;
    }


/////////////////////////////////NAVIGATION ///////////////////////////

   //Progress bar update
    updateStepper(currentStep) {
        const step1 = this.stepIndicator1Target;
        const step2 = this.stepIndicator2Target;
        const step3 = this.stepIndicator3Target;
        const step4 = this.stepIndicator4Target;

        step1.classList.toggle("active", currentStep >= 1);
        step2.classList.toggle("active", currentStep >= 2);
        step3.classList.toggle("active", currentStep >= 3);
        step4.classList.toggle("active", currentStep >= 4);
    }


    showStep(step) {
        this.step1Target.classList.add('hidden');
        this.step2Target.classList.add('hidden');
        this.step3Target.classList.add('hidden');
        this.step4Target.classList.add('hidden');

        switch (step) {
            case 1:
                this.step1Target.classList.remove('hidden');
                break;

            case 2:
                this.step2Target.classList.remove('hidden');
                break;

            case 3:
                this.step3Target.classList.remove('hidden');
                break;

            case 4:
                this.step4Target.classList.remove('hidden');
                break;
        }

        this.updateStepper(step);
    }


    goToStep2() {
        
        const clientErrors = this.getStep1ClientErrors();

        if (clientErrors.length > 0) {
            this.showErrors(clientErrors);
            return;
        }

        if (this.isCompanyClosed) {
            return;
        }

        this.showStep(2);
        
    }


    async goToStep3() {

        const clientError = this.getStep2ClientError();

         if (clientError) {
            this.showErrors([clientError]);
            return;
        }

        const menuAvailable = await this.validateMenuAvailability();

        if (!menuAvailable) {
            return;
        }

        const menuMinPeople = this.getMenuMinPeople();
        const menuTitle = this.getMenuTitle();

        this.menuInfoTarget.textContent =
            `Pour le menu choisi "${menuTitle}", le nombre minimum de personnes est de ${menuMinPeople}.`;

        this.showStep(3);

    }


    async goToStep4() {
    
        const clientError = this.getStep3ClientError();

        if (clientError) {
            this.showErrors([clientError]);
            return;
        }

        this.setLoadingState(true, [this.previousButtonStep3Target, this.continueButtonStep3Target]);

        await this.updateSummary();

        this.setLoadingState(false, [
            this.previousButtonStep3Target,
            this.continueButtonStep3Target
        ]);

        this.showStep(4);

    }

    returnToStep1() {
        this.showStep(1);
    }

    returnToStep2() {
        this.showStep(2);
    }
    
    returnToStep3() {
        this.showStep(3);
    }


    ///////////////////////////////////LOCK NAVIGATION ////////////////////////////
    setLoadingState(isLoading, buttons = []) {

        buttons.forEach(button => {
            button.disabled = isLoading;
        });
    }



    ////////////////////////////////AJAX DATE AND TIME HANDLER //////////////////////////////////


    async loadAvailableTimes() {

        const serviceDate = this.serviceDateTarget.value;

        try {
            const response = await fetch(this.element.dataset.timesUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ serviceDate })
            });

            const data = await response.json();

            if (!response.ok) {
                console.error('Erreur backend', data);
                this.renderError(
                    this.availabilityInfoTarget,
                    "Impossible de charger les horaires. Veuillez réessayer plus tard"
                );
                return;
            }
            
            // company is open this day : show ranges of delivery hours
            this.availabilityInfoTarget.textContent = "Créneaux disponibles : " + data.openingHoursText;
            this.requestedTimeTarget.disabled = false;
            this.timeBlockTarget.classList.remove("opacity-50");
            this.requestedTimeTarget.setAttribute('required', 'required');
        
        } catch (error) {
            console.error('Erreur réseau', error);
            this.renderError(
                this.availabilityInfoTarget,
                "Impossible de charger les horaires. Veuillez réessayer plus tard"
            );
        } 
    }


    onTimeChange() {
        this.clearInvalidState(this.requestedTimeTarget);
        this.clearStimulusErrors(this.requestedTimeTarget);
        this.clearBackendErrors(this.requestedTimeTarget);
    }


    resetTimeSelection() {

        this.clearInvalidState(this.requestedTimeTarget);
        this.clearStimulusErrors(this.requestedTimeTarget);
        this.clearBackendErrors(this.requestedTimeTarget);

        this.availabilityInfoTarget.textContent = '';
        this.requestedTimeTarget.value = '';
        this.requestedTimeTarget.disabled = true;
        this.timeBlockTarget.classList.add("opacity-50");
        this.requestedTimeTarget.removeAttribute('required');
    }


    async onDateChange() {

        this.isCompanyClosed = false;

        this.clearInvalidState(this.serviceDateTarget);
        this.clearStimulusErrors(this.serviceDateTarget);
        this.clearBackendErrors(this.serviceDateTarget);

        this.resetTimeSelection();

        const serviceDate = this.serviceDateTarget.value;

        if (!serviceDate) {
            return;
        }

        if (this.dateLoading) {
            return;
        }

        this.dateLoading = true;
        
        this.setLoadingState(true, [this.continueButtonStep1Target]);

        this.availabilityInfoTarget.textContent = 'Recherche des créneaux disponibles...';
        
        try {

            const response = await fetch(this.element.dataset.timesUrl, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ serviceDate })
            });

            const data = await response.json();
            
            if (!response.ok || !data.success) {

                if (data.isClosed) {
                    this.isCompanyClosed = true;
                    this.availabilityInfoTarget.textContent = "";

                    this.showErrors([{
                        element: this.serviceDateTarget,
                        message: data.message
                    }]);

                    return;
                }

                this.renderError(
                    this.availabilityInfoTarget,
                    data.message ?? "Impossible de charger les horaires. Veuillez réessayer plus tard"
                );

                return;
            }

            // company is open this day : show ranges of delivery hours
            this.availabilityInfoTarget.textContent = "Créneaux disponibles : " + data.openingHoursText;
            this.requestedTimeTarget.disabled = false;
            this.timeBlockTarget.classList.remove("opacity-50");
            this.requestedTimeTarget.setAttribute('required', 'required');
        
        } catch (error) {
            console.error('Erreur réseau', error);
            this.renderError(
                this.availabilityInfoTarget,
                "Impossible de charger les horaires. Veuillez réessayer plus tard"
            );
        
        } finally {
            this.dateLoading = false;
            this.setLoadingState(false, [this.continueButtonStep1Target]);
        }  
    }




    /////////////////////////////AJAX MENU ////////////////////////////////

    async onMenuChange() {

        this.clearStimulusErrors(this.menuSelectTarget);

        const menuId = this.menuSelectTarget.value;

        if (!menuId) {
            this.menuPreviewTarget.innerHTML = '';
            return;
        }

        if (this.menuLoading) return;

        this.menuLoading = true;
        this.setLoadingState(true, [this.previousButtonStep2Target, this.continueButtonStep2Target]);

        this.menuPreviewTarget.innerHTML = `
            <div class="text-muted">
                Chargement du menu...
            </div>
        `;

        try {
            const response = await fetch(
                this.element.dataset.menuUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({menuId})
                });

            const data = await response.json();
            
            if (!response.ok || !data.success) {
                this.renderError(this.menuPreviewTarget, data.message ?? "Erreur lors du chargement du menu");
                return;
            }

            this.menuPreviewTarget.innerHTML = data.menu_html;

        } catch (error) {
            console.error(error);
            this.renderError(this.menuPreviewTarget, "Impossible de charger le menu");
        
        } finally {
            this.menuLoading = false;
            this.setLoadingState(false, [this.previousButtonStep2Target, this.continueButtonStep2Target]);
        }

    }

    /////////////////////////////AJAX VALIDATE MENU AVAILABILITY ////////////////////////////////

    async validateMenuAvailability() {

        try {
            const response = await fetch(this.element.dataset.validateMenuAvailabilityUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    menuId: this.menuSelectTarget.value
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                this.showErrors([{
                    element: this.menuSelectTarget,
                    message: data.message
                }]);

                return false;
            }

            return true;

        } catch (error) {
            console.error(error);

           this.renderError(
                this.menuPreviewTarget,
                "Impossible de vérifier la disponibilité du menu. Veuillez réessayer."
            );

            return false;
        }
    }

    /////////////////////////////AJAX SUMMARY ////////////////////////////////

    async updateSummary() {

        if (this.summaryLoading) {
            return;
        }

        this.summaryLoading = true;
        this.submitButtonTarget.disabled = true;

        this.peopleTarget.disabled = true;
        this.peopleTarget.classList.add('opacity-50');
        this.summaryLoadingInfoTarget.classList.remove('hidden');

        const customer = {
            firstName: this.firstNameTarget.value,
            lastName: this.lastNameTarget.value,
            email: this.emailTarget.value,
            phone: this.phoneTarget.value
        };

        const address = {
            street: this.addressTarget.value,
            complement: this.addressComplementTarget.value,
            zip: this.zipCodeTarget.value,
            city: this.cityTarget.value
        };

        const deliveryInstructions = this.deliveryInstructionsTarget.value;

        const serviceDate = this.serviceDateTarget.value;
        const requestedTime = this.requestedTimeTarget.value;
        const menuId = this.menuSelectTarget.value;
        const people = Number(this.peopleTarget.value);


        try {
            const response = await fetch(this.element.dataset.summaryUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    menuId,
                    people,
                    address,
                    deliveryInstructions,
                    customer,
                    serviceDate,
                    requestedTime
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                this.renderError(this.summaryTarget, data.message ?? "Erreur lors du calcul du prix");
                return;
            }

            this.summaryTarget.innerHTML = data.summary_html;
            this.submitButtonTarget.disabled = false;

        } catch (e) {
            console.error(e);
            this.renderError(this.summaryTarget, "Impossible de calculer le prix");

        } finally {
            this.peopleTarget.disabled = false;
            this.peopleTarget.classList.remove('opacity-50');
            this.summaryLoadingInfoTarget.classList.add('hidden');
            this.summaryLoading = false;
        }

    }


    //////////////////////////////////////ORDERING//////////////////////////////////////

 
    onSubmit(event) {
        if (this.submitted || this.summaryLoading) {
            event.preventDefault();
            return;
        }

        this.submitted = true;

        this.setLoadingState(true, [
            this.previousButtonStep4Target,
            this.submitButtonTarget
        ]);
    }

}