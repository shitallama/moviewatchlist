<?php
$basePath = '../';
require_once $basePath . 'includes/db.php';
require_once $basePath . 'genres_management/Genre.php';

$status = $_GET['status'] ?? '';
$message = '';
if ($status === 'added') {
    $message = 'Genre added successfully.';
} elseif ($status === 'updated') {
    $message = 'Genre updated successfully.';
} elseif ($status === 'deleted') {
    $message = 'Genre deleted successfully.';
}

$repository = new GenreRepository($pdo);
$categories = $repository->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Genres | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/categories.css">
</head>
<body class="categories-page">
<?php require_once $basePath . 'includes/header.php'; ?>

<div class="container">
    <div class="page-header">
        <h2 class="section-title">Genres List</h2>
        <div class="header-actions">
            <a href="add_genre.php" class="btn-view">Add Genre</a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="message-banner success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if (empty($categories)): ?>
        <div class="message-banner">
            No genres found yet. Use the button above to add your first genre.
        </div>
    <?php else: ?>
        <!-- Search and Filter Section -->
        <div class="search-filter-section">
            <div class="search-box">
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search genres by name or description..." 
                    class="search-input"
                    aria-label="Search genres"
                >
                <span class="search-icon">🔍</span>
            </div>
            
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="statusFilter" class="filter-label">Status:</label>
                    <select id="statusFilter" class="filter-select" aria-label="Filter by status">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sortBy" class="filter-label">Sort by:</label>
                    <select id="sortBy" class="filter-select" aria-label="Sort genres">
                        <option value="name-asc">Name (A-Z)</option>
                        <option value="name-desc">Name (Z-A)</option>
                        <option value="date-newest">Newest First</option>
                        <option value="date-oldest">Oldest First</option>
                    </select>
                </div>

                <button id="clearFilters" class="btn-clear-filters" title="Clear all filters">
                    ✕ Clear Filters
                </button>
            </div>

            <div class="results-info">
                <span id="resultsCount">Showing <strong><?php echo count($categories); ?></strong> genre(s)</span>
            </div>
        </div>

        <table id="genresTable">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="genresTableBody">
                <?php $rowNumber = 1; ?>
                <?php foreach ($categories as $genre): ?>
                    <tr class="genre-row" 
                        data-genre-name="<?php echo htmlspecialchars($genre->name); ?>" 
                        data-genre-description="<?php echo htmlspecialchars($genre->description ?? ''); ?>"
                        data-genre-status="<?php echo $genre->is_active == 1 ? 'active' : 'inactive'; ?>"
                        data-genre-date="<?php echo htmlspecialchars($genre->created_at); ?>">
                        <td data-label="No."><?php echo $rowNumber++; ?></td>
                        <td data-label="Name" class="genre-name"><?php echo htmlspecialchars($genre->name); ?></td>
                        <td data-label="Description" class="genre-description"><?php echo htmlspecialchars($genre->description); ?></td>
                        <td data-label="Created Date" class="genre-date"><?php echo htmlspecialchars(date('M j, Y', strtotime($genre->created_at))); ?></td>
                        <td data-label="Status">
                            <span class="status-pill <?php echo $genre->is_active == 1 ? 'active' : 'inactive'; ?>">
                                <?php echo $genre->is_active == 1 ? 'Active' : 'Inactive'; ?>
                            </span>
                        </td>
                        <td class="table-actions" data-label="Actions">
                            <a href="edit_genre.php?id=<?php echo urlencode($genre->genre_id); ?>">Edit</a>
                            <a href="delete_genre.php?id=<?php echo urlencode($genre->genre_id); ?>" class="btn-secondary" onclick="return confirm('Delete this genre?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="noResults" class="message-banner" style="display: none;">
            No genres match your search criteria. Try adjusting your filters.
        </div>
    <?php endif; ?>
</div><?php require_once $basePath . 'includes/footer.php'; ?>

<script src="<?php echo $basePath; ?>assets/js/genres_filter.js"></script>
</body>
</html>
