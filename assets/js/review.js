// Load reviews when page loads
window.onload = function () {
    loadReviews();
};

// 🔄 Load all reviews
function loadReviews() {
    let movie_id = document.querySelector("input[name='movie_id']").value;

    fetch("get_reviews.php?movie_id=" + movie_id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("reviewList").innerHTML = data;
        });
}

// ❌ DELETE REVIEW
function deleteReview(id) {
    if (!confirm("Are you sure you want to delete this review?")) return;

    let formData = new FormData();
    formData.append("id", id);

    fetch("delete_review.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        if (data === "deleted") {
            alert("Review deleted!");
            loadReviews();
        } else {
            alert("Error deleting review");
        }
    });
}

// ✏️ EDIT REVIEW (fills form)
function editReview(id, rating, review) {
    document.querySelector("input[name='review_id']")?.remove();

    let hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.name = "review_id";
    hidden.value = id;

    document.getElementById("reviewForm").appendChild(hidden);

    document.querySelector("select[name='rating']").value = rating;
    document.querySelector("textarea[name='review']").value = review;

    document.querySelector("#reviewForm button").innerText = "Update Review";
}

// ➕ ADD or UPDATE REVIEW
document.getElementById("reviewForm").addEventListener("submit", function (e) {
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
    .then(data => {
        if (data === "success" || data === "updated") {
            alert(review_id ? "Review updated!" : "Review added!");

            this.reset();
            document.querySelector("#reviewForm button").innerText = "Submit Review";

            loadReviews();
        } else {
            alert("Something went wrong");
        }
    });
});