// assets/controllers/menu_filter_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    
    static targets = ['form','results','filterOverlay','submit','reset'];

    connect() {
        this.isRequestPending = false;
    }
    
    submit(event) {
        event.preventDefault();  
        this.updateMenus(this.submitTarget);      
    }

    reset(event) {
        event.preventDefault();

        this.formTarget.reset();
        this.clearErrors();

        this.updateMenus(this.resetTarget);
    }

    clearErrors() {
        this.formTarget.querySelectorAll('.error')
            .forEach(
                element => element.textContent = '' 
            );
    }

    showFilterOverlay() {
        this.filterOverlayTarget.textContent = 'Chargement des menus...';
        this.filterOverlayTarget.classList.add('active');
        this.resultsTarget.classList.add('opacity-25');
    }

    hideFilterOverlay() {
        this.filterOverlayTarget.classList.remove('active');
        this.filterOverlayTarget.textContent = '';
        this.resultsTarget.classList.remove('opacity-25');
    }

    beforeRequest(activeButton) {
        activeButton.disabled = true;

        if (activeButton === this.submitTarget) {
            this.showFilterOverlay();
        }
    }

    afterRequest(activeButton) {
        activeButton.disabled = false;

        if (activeButton === this.submitTarget) {
            this.hideFilterOverlay();
        }
    }

    
    async updateMenus(activeButton) {
        
        if (this.isRequestPending) return; 
        
        this.isRequestPending = true;
        
        this.beforeRequest(activeButton); 

        this.clearErrors(); 

        try {
            const response = await fetch(this.formTarget.dataset.url, {
                method: 'POST',
                body: new FormData(this.formTarget)
            });

            let data;

            try {
                data = await response.json();

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
            this.isRequestPending = false;

            this.afterRequest(activeButton)

        }

    }
}
