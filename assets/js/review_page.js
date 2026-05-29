(() => {
    const reviewForm = document.getElementById('reviewForm');
    const submitButton = reviewForm ? reviewForm.querySelector("button[type='submit']") : null;

    window.loadReviews = function loadReviews() {
        const movieIdInput = document.querySelector("input[name='movie_id']");
        if (!movieIdInput) {
            return;
        }
        const movieId = movieIdInput.value;

        fetch(`get_reviews.php?movie_id=${movieId}`)
            .then((res) => res.text())
            .then((data) => {
                const list = document.getElementById('reviewList');
                if (list) {
                    list.innerHTML = data;
                }
            });
    };

    window.deleteReview = function deleteReview(id) {
        if (!confirm('Delete this review?')) return;

        const formData = new FormData();
        formData.append('id', id);

        fetch('delete_review.php', {
            method: 'POST',
            body: formData
        })
            .then((res) => res.text())
            .then(() => window.loadReviews());
    };

    window.editReview = function editReview(id, rating, review) {
        if (!reviewForm) {
            return;
        }
        document.querySelector("input[name='review_id']")?.remove();

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = 'review_id';
        hidden.value = id;
        reviewForm.appendChild(hidden);

        const ratingSelect = document.querySelector("select[name='rating']");
        const reviewText = document.querySelector("textarea[name='review']");

        if (ratingSelect) {
            ratingSelect.value = rating;
        }
        if (reviewText) {
            reviewText.value = review;
        }

        if (submitButton) {
            submitButton.innerText = 'Update Review';
        }
    };

    if (reviewForm) {
        reviewForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const formData = new FormData(reviewForm);
            const reviewId = formData.get('review_id');

            const url = reviewId ? 'update_review.php' : 'add_review.php';

            if (reviewId) {
                formData.append('id', reviewId);
            }

            fetch(url, {
                method: 'POST',
                body: formData
            })
                .then((res) => res.text())
                .then((data) => {
                    if (data === 'exists') {
                        alert('You have already added review for this movie.');
                        return;
                    }
                    reviewForm.reset();
                    document.querySelector("input[name='review_id']")?.remove();
                    if (submitButton) {
                        submitButton.innerText = 'Submit Review';
                    }
                    window.loadReviews();
                });
        });
    }

    window.addEventListener('load', () => {
        window.loadReviews();
    });
})();
