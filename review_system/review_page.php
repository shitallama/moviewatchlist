<!DOCTYPE html>
<html>
<head>
    <title>Review System</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        textarea { width: 300px; height: 80px; }
        .review { border: 1px solid #ccc; padding: 10px; margin-top: 10px; }
    </style>
</head>
<body>

<h2>Movie Review System</h2>

<!-- REVIEW FORM -->
<form id="reviewForm">
    <input type="hidden" name="user_id" value="1">
    <input type="hidden" name="movie_id" value="1">

    <label>Rating:</label>
    <select name="rating" required>
        <option value="">Select</option>
        <option value="1">1 ⭐</option>
        <option value="2">2 ⭐</option>
        <option value="3">3 ⭐</option>
        <option value="4">4 ⭐</option>
        <option value="5">5 ⭐</option>
    </select>

    <br><br>

    <textarea name="review" placeholder="Write your review..." required></textarea>

    <br><br>

    <button type="submit">Submit Review</button>
</form>

<hr>

<!-- REVIEW LIST -->
<div id="reviewList"></div>

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

    // remove old hidden input if exists
    document.querySelector("input[name='review_id']")?.remove();

    let hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "review_id";
    hidden.value = id;
    form.appendChild(hidden);

    document.querySelector("select[name='rating']").value = rating;
    document.querySelector("textarea[name='review']").value = review;

    document.querySelector("button").innerText = "Update Review";
}

// ADD / UPDATE REVIEW
document.getElementById("reviewForm").addEventListener("submit", function(e) {
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
        this.reset();
        document.querySelector("button").innerText = "Submit Review";
        loadReviews();
    });
});

// LOAD ON PAGE START
window.onload = loadReviews;
</script>

</body>
</html>