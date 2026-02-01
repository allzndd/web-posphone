/**
 * TABLE ACTION DROPDOWN UTILITY
 * Reusable dropdown menu logic untuk action buttons di table
 * 
 * Usage:
 * new TableActionDropdown({
 *     dropdownSelector: '#actionDropdown',
 *     buttonSelector: '.btn-actions-menu',
 *     editMenuSelector: '#editMenuItem',
 *     deleteMenuSelector: '#deleteMenuItem',
 *     zoomFactor: 0.9
 * });
 */

class TableActionDropdown {
    constructor(options = {}) {
        this.dropdownSelector = options.dropdownSelector || '#actionDropdown';
        this.buttonSelector = options.buttonSelector || '.btn-actions-menu';
        this.editMenuSelector = options.editMenuSelector || '#editMenuItem';
        this.deleteMenuSelector = options.deleteMenuSelector || '#deleteMenuItem';
        this.zoomFactor = options.zoomFactor || 1;
        this.confirmDeleteMessage = options.confirmDeleteMessage || 'Apakah Anda yakin ingin menghapus item ini?';
        this.onEditCallback = options.onEditCallback || null;
        this.onDeleteCallback = options.onDeleteCallback || null;
        
        this.dropdown = document.querySelector(this.dropdownSelector);
        this.editMenuItem = document.querySelector(this.editMenuSelector);
        this.deleteMenuItem = document.querySelector(this.deleteMenuSelector);
        
        this.currentEditUrl = null;
        this.currentDestroyUrl = null;
        this.currentButton = null;
        
        // Bind methods to preserve context
        this.handleDocumentClick = this.handleDocumentClick.bind(this);
        this.handleDocumentKeydown = this.handleDocumentKeydown.bind(this);
        this.handleButtonClick = this.handleButtonClick.bind(this);
        
        if (this.dropdown) {
            this.init();
        }
    }
    
    init() {
        // Use event delegation - attach to parent or document
        // This prevents memory leaks from attaching listeners to each button
        document.addEventListener('click', this.handleDocumentClick);
        document.addEventListener('keydown', this.handleDocumentKeydown);
        
        // Setup menu item handlers (only 2, not one per button)
        if (this.editMenuItem) {
            this.editMenuItem.addEventListener('click', () => this.handleEdit());
        }
        
        if (this.deleteMenuItem) {
            this.deleteMenuItem.addEventListener('click', () => this.handleDelete());
        }
    }
    
    handleDocumentClick(e) {
        // Check if clicked element is an action button
        const button = e.target.closest(this.buttonSelector);
        
        if (button) {
            e.preventDefault();
            e.stopPropagation();
            this.handleButtonClick(button);
            return;
        }
        
        // Check if clicked inside dropdown
        if (!e.target.closest(this.dropdownSelector)) {
            this.closeDropdown();
        }
    }
    
    handleDocumentKeydown(e) {
        if (e.key === 'Escape') {
            this.closeDropdown();
        }
    }
    
    handleButtonClick(btn) {
        const editUrl = btn.dataset.roleEdit || btn.dataset.editUrl;
        const destroyUrl = btn.dataset.roleDestroy || btn.dataset.destroyUrl;
        
        this.currentEditUrl = editUrl;
        this.currentDestroyUrl = destroyUrl;
        this.currentButton = btn;
        
        this.positionDropdown(btn);
        this.dropdown.classList.add('show');
    }
    
    positionDropdown(btn) {
        const rect = btn.getBoundingClientRect();
        const dropdownWidth = 140;
        
        // Adjust for zoom factor (e.g., 90% zoom = 0.9)
        const adjustedLeft = rect.left / this.zoomFactor;
        const adjustedTop = rect.top / this.zoomFactor;
        
        // Position dropdown to the left of button
        this.dropdown.style.position = 'fixed';
        this.dropdown.style.top = adjustedTop + 'px';
        this.dropdown.style.left = (adjustedLeft - dropdownWidth) + 'px';
        this.dropdown.style.zIndex = '1001';
    }
    
    closeDropdown() {
        this.dropdown.classList.remove('show');
        this.currentButton = null;
    }
    
    handleEdit() {
        if (this.currentEditUrl) {
            if (this.onEditCallback) {
                this.onEditCallback(this.currentEditUrl);
            } else {
                window.location.href = this.currentEditUrl;
            }
        }
        this.closeDropdown();
    }
    
    handleDelete() {
        if (confirm(this.confirmDeleteMessage)) {
            if (this.onDeleteCallback) {
                this.onDeleteCallback(this.currentDestroyUrl);
            } else {
                this.submitDeleteForm();
            }
        }
        this.closeDropdown();
    }
    
    submitDeleteForm() {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = this.currentDestroyUrl;
        form.style.display = 'none';
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = csrfToken.content;
            form.appendChild(token);
        }
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
        
        // Cleanup
        setTimeout(() => form.remove(), 100);
    }
    
    // Cleanup method to prevent memory leaks
    destroy() {
        document.removeEventListener('click', this.handleDocumentClick);
        document.removeEventListener('keydown', this.handleDocumentKeydown);
        
        if (this.editMenuItem) {
            this.editMenuItem.replaceWith(this.editMenuItem.cloneNode(true));
        }
        
        if (this.deleteMenuItem) {
            this.deleteMenuItem.replaceWith(this.deleteMenuItem.cloneNode(true));
        }
        
        this.currentButton = null;
        this.dropdown = null;
    }
}
