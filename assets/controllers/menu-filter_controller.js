// assets/controllers/menu_filter_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    static targets = ['form','results','loading','submit'];

    
    submit(event) {
        event.preventDefault();  
        this.updateMenus();      
    }


    reset(event) {
        event.preventDefault();
        this.formTarget.reset(); 
        this.clearErrors();      
        this.updateMenus();      
    }


    clearErrors() {
        this.formTarget.querySelectorAll('.error')
            .forEach(
                element => element.textContent = '' 
            );
    }


    async updateMenus() {
        this.loadingTarget.classList.remove('hidden'); 

        if (this.hasSubmitTarget) {
            this.submitTarget.disabled = true; 
        }
        this.clearErrors(); 

        try {
            const response = await fetch(this.formTarget.dataset.url, {
                method: 'POST',
                body: new FormData(this.formTarget)
            });

            console.log('Status HTTP:', response.status);

            let data;

            try {
                data = await response.json();
                console.log('Réponse JSON:', data);

            } catch (e) {
                console.error('JSON invalide', e);
                this.resultsTarget.innerHTML =
                    '<p class="text-danger">Erreur lors de la récupération des menus</p>';
                return;
            }

            if (!data) {
                this.resultsTarget.innerHTML =
                    '<p class="text-danger">Erreur lors de la récupération des menus</p>';
                return;
            }

            if (!response.ok) {
                for (const field in data.errors) {
                    const errorEl = this.formTarget.querySelector(
                        `[data-menu-filter-target="${field}"]`
                    );

                    if (errorEl) {
                        errorEl.textContent = data.errors[field];
                    }
                }
                return;
            }
            
            this.resultsTarget.innerHTML = data.menus_list;

        } catch (e) {
            console.error('Erreur réseau:', e);
            this.resultsTarget.innerHTML =
                '<p class="text-danger">Erreur lors de la récupération des menus</p>';

        } finally {
            this.loadingTarget.classList.add('hidden');

            if (this.hasSubmitTarget) {
                this.submitTarget.disabled = false;
            }
        }
    }
}
