<?php
$basePath = '../';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Login/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$movie_id = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;
if ($movie_id <= 0) {
    echo 'Invalid movie selected.';
    exit();
}

require_once __DIR__ . '/../includes/db.php';
$movieStmt = $pdo->prepare('SELECT title FROM Movies WHERE movie_id = ?');
$movieStmt->execute([$movie_id]);
$movie = $movieStmt->fetch(PDO::FETCH_ASSOC);
if (!$movie) {
    echo 'Movie not found.';
    exit();
}
$movieTitle = $movie['title'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Movie Reviews | CineList</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/style.css">
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/review_style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
<?php require_once $basePath . 'includes/header.php'; ?>

<section class="review-page">
    <div class="review-panel">
        <div class="review-header">
            <div>
                <span class="badge">Reviews</span>
                <h2>Review: <?= htmlspecialchars($movieTitle) ?></h2>
            </div>
            <div>
                <a href="../movie_management/view_movies.php" class="review-back-button">← Back to movie list</a>
            </div>
            <p class="review-subtitle">Share your rating and feedback for this movie.</p>
        </div>

        <form id="reviewForm" class="review-form">
            <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
            <input type="hidden" name="movie_id" value="<?= htmlspecialchars($movie_id) ?>">

            <label for="rating">Rating</label>
            <select id="rating" name="rating" required>
                <option value="">Select</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>

            <label for="review">Review</label>
            <textarea id="review" name="review" placeholder="Write your review..." required></textarea>

            <button type="submit">Submit Review</button>
        </form>

        <div id="reviewList" class="review-list"></div>
    </div>
</section>

<?php require_once $basePath . 'includes/footer.php'; ?>

<script>
// LOAD REVIEWS
function loadReviews() {
    let movie_id = document.querySelector("input[name='movie_id']").value;

    fetch("get_reviews.php?movie_id=" + movie_id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("reviewList").innerHTML = data;
        });
}

// DELETE REVIEW
function deleteReview(id) {
    if (!confirm("Delete this review?")) return;

    let formData = new FormData();
    formData.append("id", id);

    fetch("delete_review.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => loadReviews());
}

// EDIT REVIEW
function editReview(id, rating, review) {
    let form = document.getElementById("reviewForm");

    document.querySelector("input[name='review_id']")?.remove();

    let hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "review_id";
    hidden.value = id;
    form.appendChild(hidden);

    document.querySelector("select[name='rating']").value = rating;
    document.querySelector("textarea[name='review']").value = review;

    submitButton.innerText = "Update Review";
}

// ADD / UPDATE REVIEW
const reviewForm = document.getElementById("reviewForm");
const submitButton = reviewForm.querySelector("button[type='submit']");

reviewForm.addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let review_id = formData.get("review_id");

    let url = review_id ? "update_review.php" : "add_review.php";

    if (review_id) {
        formData.append("id", review_id);
    }

    fetch(url, {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        reviewForm.reset();
        document.querySelector("input[name='review_id']")?.remove();
        submitButton.innerText = "Submit Review";
        loadReviews();
    });
});

window.onload = loadReviews;
</script>
</body>
</html>
