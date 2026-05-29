/**
 * Genres Search and Filter System
 * Provides real-time search, filtering by status, and sorting capabilities
 */

(() => {
    const init = () => {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const sortBy = document.getElementById('sortBy');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const genresTable = document.getElementById('genresTable');
        const genresTableBody = document.getElementById('genresTableBody');
        const noResults = document.getElementById('noResults');
        const resultsCount = document.getElementById('resultsCount');

        if (!genresTableBody) return;

        // Get all genre rows
        const getGenreRows = () => {
            return Array.from(document.querySelectorAll('.genre-row'));
        };

        // Filter and search logic
        const applyFilters = () => {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const statusValue = statusFilter.value;
            const sortValue = sortBy.value;

            let rows = getGenreRows();

            // Apply search filter
            if (searchTerm) {
                rows = rows.filter(row => {
                    const name = row.dataset.genreName.toLowerCase();
                    const description = row.dataset.genreDescription.toLowerCase();
                    return name.includes(searchTerm) || description.includes(searchTerm);
                });
            }

            // Apply status filter
            if (statusValue) {
                rows = rows.filter(row => row.dataset.genreStatus === statusValue);
            }

            // Apply sorting
            rows.sort((a, b) => {
                switch (sortValue) {
                    case 'name-asc':
                        return a.dataset.genreName.toLowerCase().localeCompare(b.dataset.genreName.toLowerCase(), undefined, { numeric: true, sensitivity: 'base' });
                    case 'name-desc':
                        return b.dataset.genreName.toLowerCase().localeCompare(a.dataset.genreName.toLowerCase(), undefined, { numeric: true, sensitivity: 'base' });
                    case 'date-newest':
                        return new Date(b.dataset.genreDate) - new Date(a.dataset.genreDate);
                    case 'date-oldest':
                        return new Date(a.dataset.genreDate) - new Date(b.dataset.genreDate);
                    default:
                        return 0;
                }
            });

            // Update row visibility and reorder
            const allRows = getGenreRows();
            allRows.forEach(row => row.style.display = 'none');
            
            // Reorder rows in the DOM
            rows.forEach(row => {
                genresTableBody.appendChild(row);
                row.style.display = '';
            });

            // Update visibility of table and no results message
            if (rows.length === 0) {
                genresTable.style.display = 'none';
                noResults.style.display = 'block';
            } else {
                genresTable.style.display = '';
                noResults.style.display = 'none';

                // Update row numbers
                rows.forEach((row, index) => {
                    const noCell = row.querySelector('td[data-label="No."]');
                    if (noCell) {
                        noCell.textContent = index + 1;
                    }
                });
            }

            // Update results count
            updateResultsCount(rows.length);
        };

        // Update results counter
        const updateResultsCount = (count) => {
            const totalRows = getGenreRows().length;
            if (count === totalRows) {
                resultsCount.innerHTML = `Showing <strong>${count}</strong> genre(s)`;
            } else {
                resultsCount.innerHTML = `Showing <strong>${count}</strong> of <strong>${totalRows}</strong> genre(s)`;
            }
        };

        // Clear all filters
        const clearAllFilters = () => {
            searchInput.value = '';
            statusFilter.value = '';
            sortBy.value = 'name-asc';
            applyFilters();
            searchInput.focus();
        };

        // Event listeners
        searchInput.addEventListener('input', applyFilters);
        statusFilter.addEventListener('change', applyFilters);
        sortBy.addEventListener('change', applyFilters);
        clearFiltersBtn.addEventListener('click', clearAllFilters);

        // Allow Enter key in search input
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyFilters();
            }
        });

        // Initialize with default sort
        applyFilters();
    };

    // Run when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
