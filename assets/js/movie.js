document.addEventListener('DOMContentLoaded', function() {
    const movieId = document.getElementById('movie-title').getAttribute('data-movie-id');
    const addToFavoritesBtn = document.getElementById('add-to-favorites');
    const addToWatchlistBtn = document.getElementById('add-to-watchlist');
    const markAsWatchingBtn = document.getElementById('mark-as-watching');
    const markAsWatchedBtn = document.getElementById('mark-as-watched');
    const submitReviewBtn = document.getElementById('submit-review');
    const starIcons = document.querySelectorAll('.stars i');
    let currentRating = 0;

    function sendRequest(action, list = null) {
        const data = { action, movieId };
        if (list !== null) data.list = list;

        fetch('update_list.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (action === 'toggle_favorite') {
                        toggleFavoriteIcon(data.isFavorite);
                    } else if (action === 'update_list') {
                        updateButtonStates(data.currentList);
                    }
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    function updateButtonStates(currentList) {
        console.log('Updating button states:', currentList); // For debugging

        [addToWatchlistBtn, markAsWatchingBtn, markAsWatchedBtn].forEach(btn => {
            btn.classList.remove('active');
        });

        if (currentList) {
            const activeBtn = {
                'watchlist': addToWatchlistBtn,
                'watching': markAsWatchingBtn,
                'watched': markAsWatchedBtn
            }[currentList];

            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        document.querySelector('.review').style.display = currentList === 'watched' ? 'block' : 'none';
        document.querySelector('.rating').style.display = currentList === 'watched' ? 'block' : 'none';
    }

    function toggleFavoriteIcon(isFavorite) {
        if (isFavorite) {
            addToFavoritesBtn.classList.remove('bi-heart');
            addToFavoritesBtn.classList.add('bi-heart-fill');
            addToFavoritesBtn.style.color = 'red';
        } else {
            addToFavoritesBtn.classList.remove('bi-heart-fill');
            addToFavoritesBtn.classList.add('bi-heart');
            addToFavoritesBtn.style.color = '';
        }
    }

    addToFavoritesBtn.addEventListener('click', function() {
        sendRequest('toggle_favorite');
    });

    addToWatchlistBtn.addEventListener('click', function() {
        sendRequest('update_list', this.classList.contains('active') ? null : 'watchlist');
    });

    markAsWatchingBtn.addEventListener('click', function() {
        sendRequest('update_list', this.classList.contains('active') ? null : 'watching');
    });

    markAsWatchedBtn.addEventListener('click', function() {
        sendRequest('update_list', this.classList.contains('active') ? null : 'watched');
    });

    submitReviewBtn.addEventListener('click', function() {
        const reviewText = document.getElementById('review-text').value;

        fetch('submit_review.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    movieId: movieId,
                    review: reviewText,
                    rating: currentRating
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully');
                } else {
                    console.error('Error:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    // Star rating functionality
    function setRating(rating) {
        currentRating = rating;
        highlightStars(rating);
    }

    function highlightStars(rating) {
        starIcons.forEach((star, index) => {
            if (index < rating) {
                star.classList.remove('bi-star');
                star.classList.add('bi-star-fill');
            } else {
                star.classList.remove('bi-star-fill');
                star.classList.add('bi-star');
            }
        });
    }

    starIcons.forEach((star, index) => {
        star.addEventListener('click', function() {
            setRating(index + 1);
        });

        star.addEventListener('mouseover', function() {
            highlightStars(index + 1);
        });

        star.addEventListener('mouseout', function() {
            highlightStars(currentRating);
        });
    });

    // Fetch initial state
    fetch(`get_movie_state.php?id=${movieId}`)
        .then(response => response.json())
        .then(data => {
            console.log('Initial state:', data); // For debugging

            if (data.is_favorite) {
                toggleFavoriteIcon(true);
            }

            if (data.current_list) {
                updateButtonStates(data.current_list);
            }
        })
        .catch(error => console.error('Error:', error));
});