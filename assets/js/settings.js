(() => {
    const trigger = document.querySelector('[data-delete-trigger]');
    const modal = document.querySelector('[data-delete-modal]');
    const closeButtons = document.querySelectorAll('[data-delete-close]');
    const confirmButton = document.querySelector('[data-delete-confirm]');
    const form = trigger ? trigger.closest('form') : null;

    const openModal = () => {
        if (!modal) {
            return;
        }
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    };

    const closeModal = () => {
        if (!modal) {
            return;
        }
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    };

    if (trigger) {
        trigger.addEventListener('click', openModal);
    }

    closeButtons.forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    if (confirmButton && form) {
        confirmButton.addEventListener('click', () => {
            form.submit();
        });
    }
})();
