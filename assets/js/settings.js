(() => {
    // Delete account modal handler
    const deleteTrigger = document.querySelector('[data-delete-trigger]');
    const deleteModal = document.querySelector('[data-delete-modal]');
    const deleteCloseButtons = document.querySelectorAll('[data-delete-close]');
    const deleteConfirmButton = document.querySelector('[data-delete-confirm]');
    const deleteForm = deleteTrigger ? deleteTrigger.closest('form') : null;

    const openDeleteModal = () => {
        if (!deleteModal) {
            return;
        }
        deleteModal.classList.add('is-open');
        deleteModal.setAttribute('aria-hidden', 'false');
    };

    const closeDeleteModal = () => {
        if (!deleteModal) {
            return;
        }
        deleteModal.classList.remove('is-open');
        deleteModal.setAttribute('aria-hidden', 'true');
    };

    if (deleteTrigger) {
        deleteTrigger.addEventListener('click', openDeleteModal);
    }

    deleteCloseButtons.forEach((button) => {
        button.addEventListener('click', closeDeleteModal);
    });

    if (deleteConfirmButton && deleteForm) {
        deleteConfirmButton.addEventListener('click', () => {
            deleteForm.submit();
        });
    }

    // Deactivate account modal handler
    const deactivateTrigger = document.querySelector('[data-deactivate-trigger]');
    const deactivateModal = document.querySelector('[data-deactivate-modal]');
    const deactivateCloseButtons = document.querySelectorAll('[data-deactivate-close]');
    const deactivateConfirmButton = document.querySelector('[data-deactivate-confirm]');
    const deactivateForm = deactivateTrigger ? deactivateTrigger.closest('form') : null;

    const openDeactivateModal = () => {
        if (!deactivateModal) {
            return;
        }
        deactivateModal.classList.add('is-open');
        deactivateModal.setAttribute('aria-hidden', 'false');
    };

    const closeDeactivateModal = () => {
        if (!deactivateModal) {
            return;
        }
        deactivateModal.classList.remove('is-open');
        deactivateModal.setAttribute('aria-hidden', 'true');
    };

    if (deactivateTrigger) {
        deactivateTrigger.addEventListener('click', openDeactivateModal);
    }

    deactivateCloseButtons.forEach((button) => {
        button.addEventListener('click', closeDeactivateModal);
    });

    if (deactivateConfirmButton && deactivateForm) {
        deactivateConfirmButton.addEventListener('click', () => {
            deactivateForm.submit();
        });
    }
})();
