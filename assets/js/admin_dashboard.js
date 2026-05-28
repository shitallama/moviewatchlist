(() => {
    const userSearch = document.getElementById('userSearch');
    const movieSearch = document.getElementById('movieSearch');
    const genreFilter = document.getElementById('genreFilter');
    const ratingFilter = document.getElementById('ratingFilter');

    const filterUsers = (searchTerm) => {
        const userRows = document.querySelectorAll('.user-row');
        let visibleCount = 0;

        userRows.forEach((row) => {
            const username = row.getAttribute('data-username') || '';
            const email = row.getAttribute('data-email') || '';
            const matches = username.includes(searchTerm) || email.includes(searchTerm);

            if (matches) {
                row.classList.remove('hidden-row');
                visibleCount++;
            } else {
                row.classList.add('hidden-row');
            }
        });

        const count = document.getElementById('userCount');
        if (count) {
            count.textContent = String(visibleCount);
        }
    };

    const filterMovies = () => {
        const searchTerm = movieSearch ? movieSearch.value.toLowerCase().trim() : '';
        const genreValue = genreFilter ? genreFilter.value.toLowerCase().trim() : '';
        const ratingValue = ratingFilter ? ratingFilter.value : '';
        const movieRows = document.querySelectorAll('.movie-row');
        let visibleCount = 0;

        movieRows.forEach((row) => {
            const title = row.getAttribute('data-title') || '';
            const genre = row.getAttribute('data-genre') || '';
            const rating = parseInt(row.getAttribute('data-rating') || '0', 10);

            const matchesSearch = title.includes(searchTerm);
            const matchesGenre = !genreValue || genre === genreValue;
            let matchesRating = true;

            if (ratingValue !== '') {
                const filterRating = parseInt(ratingValue, 10);
                matchesRating = filterRating === 0 ? rating === 0 : rating >= filterRating;
            }

            if (matchesSearch && matchesGenre && matchesRating) {
                row.classList.remove('hidden-row');
                visibleCount++;
            } else {
                row.classList.add('hidden-row');
            }
        });

        const count = document.getElementById('movieCount');
        if (count) {
            count.textContent = String(visibleCount);
        }
    };

    if (userSearch) {
        userSearch.addEventListener('input', (e) => {
            const value = e.target.value.toLowerCase().trim();
            filterUsers(value);
        });
    }

    if (movieSearch) {
        movieSearch.addEventListener('input', filterMovies);
    }
    if (genreFilter) {
        genreFilter.addEventListener('change', filterMovies);
    }
    if (ratingFilter) {
        ratingFilter.addEventListener('change', filterMovies);
    }

    document.querySelectorAll('.stat-card').forEach((card) => {
        card.addEventListener('mouseenter', () => {
            card.style.boxShadow = '0 12px 35px rgba(0, 0, 0, 0.15)';
        });
        card.addEventListener('mouseleave', () => {
            card.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
        });
    });
})();
