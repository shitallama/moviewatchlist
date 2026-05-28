(() => {
    const controls = document.querySelector('.community-controls');
    if (!controls) {
        return;
    }

    const cardsContainer = document.querySelector('.community-grid');
    if (!cardsContainer) {
        return;
    }

    const cards = Array.from(cardsContainer.querySelectorAll('.community-card'));
    cards.forEach((card, index) => {
        card.dataset.defaultIndex = String(index);
    });

    const toggleMine = document.querySelector('[data-toggle-mine]');
    const searchInput = document.querySelector('.community-search-input');
    const searchButton = document.querySelector('.community-search-btn');
    const currentUserIdValue = document.body.dataset.currentUserId || '';
    const currentUserId = currentUserIdValue ? parseInt(currentUserIdValue, 10) : null;

    const applySearchFilter = () => {
        if (!searchInput) {
            return;
        }
        const query = searchInput.value.toLowerCase().trim();
        cards.forEach((card) => {
            const title = card.querySelector('h3')?.textContent?.toLowerCase() || '';
            const genre = card.querySelector('.community-genre')?.textContent?.toLowerCase() || '';
            const user = card.querySelector('.community-user')?.textContent?.toLowerCase() || '';
            const matches = query === '' || title.includes(query) || genre.includes(query) || user.includes(query);
            card.dataset.searchMatch = matches ? 'true' : 'false';
        });
    };

    const applyMineFilter = () => {
        const showMine = toggleMine ? toggleMine.getAttribute('aria-pressed') === 'true' : true;
        cards.forEach((card) => {
            const ownerId = parseInt(card.dataset.owner, 10);
            const matchesSearch = card.dataset.searchMatch !== 'false';
            const shouldShow = matchesSearch && (showMine || !currentUserId || ownerId !== currentUserId);
            card.style.display = shouldShow ? '' : 'none';
        });
    };

    const updateToggleLabel = () => {
        if (!toggleMine) {
            return;
        }
        const showMine = toggleMine.getAttribute('aria-pressed') === 'true';
        toggleMine.textContent = showMine ? 'Hide my posts' : 'Show my posts';
    };

    const sortCards = (mode) => {
        const sorted = [...cards].sort((a, b) => {
            const aRating = a.dataset.rating === '' ? null : parseFloat(a.dataset.rating);
            const bRating = b.dataset.rating === '' ? null : parseFloat(b.dataset.rating);
            const aReviews = parseInt(a.dataset.reviews, 10);
            const bReviews = parseInt(b.dataset.reviews, 10);
            const aId = parseInt(a.dataset.movieId, 10);
            const bId = parseInt(b.dataset.movieId, 10);
            const aDefault = parseInt(a.dataset.defaultIndex, 10);
            const bDefault = parseInt(b.dataset.defaultIndex, 10);

            if (mode === 'highest') {
                if (aRating === null && bRating !== null) return 1;
                if (aRating !== null && bRating === null) return -1;
                if (aRating !== bRating) return (bRating ?? 0) - (aRating ?? 0);
                if (aReviews !== bReviews) return bReviews - aReviews;
                return bId - aId;
            }

            if (mode === 'lowest') {
                if (aRating === null && bRating !== null) return 1;
                if (aRating !== null && bRating === null) return -1;
                if (aRating !== bRating) return (aRating ?? 0) - (bRating ?? 0);
                if (aReviews !== bReviews) return bReviews - aReviews;
                return bId - aId;
            }

            if (mode === 'most') {
                if (aReviews !== bReviews) return bReviews - aReviews;
                return bId - aId;
            }

            if (mode === 'latest') {
                return bId - aId;
            }

            return aDefault - bDefault;
        });

        sorted.forEach((card) => cardsContainer.appendChild(card));
        applyMineFilter();
    };

    const setActiveSort = (activeButton) => {
        document.querySelectorAll('.sort-pill').forEach((button) => {
            const isActive = button === activeButton;
            button.classList.toggle('active', isActive);
            button.setAttribute('aria-selected', isActive ? 'true' : 'false');
        });
    };

    const defaultSort = document.querySelector('.sort-pill[data-sort="all"]');
    if (defaultSort) {
        setActiveSort(defaultSort);
    }

    document.querySelectorAll('.sort-pill').forEach((button) => {
        button.addEventListener('click', () => {
            setActiveSort(button);
            sortCards(button.dataset.sort);
        });
    });

    if (searchInput && searchButton) {
        searchButton.addEventListener('click', () => {
            applySearchFilter();
            applyMineFilter();
        });

        searchInput.addEventListener('input', () => {
            applySearchFilter();
            applyMineFilter();
        });
    }

    if (toggleMine) {
        toggleMine.addEventListener('click', () => {
            if (!currentUserId) {
                return;
            }
            const isPressed = toggleMine.getAttribute('aria-pressed') === 'true';
            toggleMine.setAttribute('aria-pressed', isPressed ? 'false' : 'true');
            updateToggleLabel();
            applyMineFilter();
        });
        applySearchFilter();
        updateToggleLabel();
        applyMineFilter();
    }
})();
