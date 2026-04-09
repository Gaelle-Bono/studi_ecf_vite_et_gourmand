// assets/controllers/menu_filter_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    static targets = ['form','results','loading','submit'];

    
    submit(event) {
        event.preventDefault();  // Prevent default form submission
        this.updateMenus();      // Fetch and display filtered menus
    }

    reset(event) {
        event.preventDefault();
        this.formTarget.reset(); // Clear the form fields
        this.clearErrors();      // Remove any error messages
        this.updateMenus();      // Reload menus with default values
    }

    // Clear all error messages in the form
    clearErrors() {
        this.formTarget.querySelectorAll('.error')
            .forEach(
                element => element.textContent = '' 
            );
    }

    // Fetch menus based on filters and update the page
    updateMenus() {
        this.loadingTarget.classList.remove('hidden'); // Show loading indicator

        if (this.hasSubmitTarget) {
            this.submitTarget.disabled = true; // Disable submit button while loading
        }

        this.clearErrors(); // Clear previous errors

        // Send the form data via POST
        fetch(this.formTarget.dataset.url, {
            method: 'POST',
            body: new FormData(this.formTarget)
        })
        .then(response => response.json())  // Parse JSON response
        .then(data => {
            // Update menus list in the page
            this.resultsTarget.innerHTML = data.menus_list;

            // Display validation errors returned by backend
            for (const field in data.errors) {
                const msg = data.errors[field];
                const errorEl = this.formTarget.querySelector(`[data-menu-filter-target="${field}"]`);
                if (errorEl) {
                    errorEl.textContent = msg; 
                }
            }
        })
        .catch(() => {
            // Show a generic error message if fetch fails
            this.resultsTarget.innerHTML = '<p>Erreur lors de la récupération des menus.</p>';
        })
        .finally(() => {
            this.loadingTarget.classList.add('hidden'); // Hide loading indicator
            if (this.hasSubmitTarget) {
                this.submitTarget.disabled = false; // Re-enable submit button
            }
        });
    }
}