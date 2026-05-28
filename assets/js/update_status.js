(() => {
    const syncWatchStatus = () => {
        const select = document.getElementById('status_id');
        const stateDisplay = document.getElementById('current-state');
        const progressDisplay = document.getElementById('current-progress');
        const progressInput = document.getElementById('progress_percent');
        const progressOutput = document.getElementById('progress-output');
        const watchState = document.getElementById('watch_state');

        if (!select || !stateDisplay || !progressDisplay || !progressInput || !progressOutput || !watchState) {
            return;
        }

        const option = select.options[select.selectedIndex];
        if (option && option.value) {
            const state = option.getAttribute('data-state') || 'plan';
            const progress = option.getAttribute('data-progress') || '0';

            stateDisplay.textContent = state.charAt(0).toUpperCase() + state.slice(1);
            progressDisplay.textContent = `${progress}%`;
            progressInput.value = progress;
            progressOutput.textContent = `${progress}%`;
            watchState.value = state;
        } else {
            stateDisplay.textContent = 'Select a watchlist item';
            progressDisplay.textContent = '0%';
            progressInput.value = 0;
            progressOutput.textContent = '0%';
            watchState.value = 'plan';
        }
    };

    document.addEventListener('DOMContentLoaded', () => {
        syncWatchStatus();
    });

    window.syncWatchStatus = syncWatchStatus;
})();
