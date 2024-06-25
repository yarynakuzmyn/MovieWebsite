<?php
    include __DIR__ . '/../server/db_connection.php';
    session_start();
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo "Invalid movie ID.";
        exit;
    }

    $movie_id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT m.id, m.title_ukr, m.title_orig, m.description, m.image, m.year, GROUP_CONCAT(DISTINCT c.name SEPARATOR ', ') AS countries, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') AS genres, m.trailer_url
                            FROM movies m
                            LEFT JOIN movie_country mc ON m.id = mc.movie_id
                            LEFT JOIN countries c ON mc.country_id = c.id
                            LEFT JOIN movie_genre mg ON m.id = mg.movie_id
                            LEFT JOIN genre g ON mg.genre_id = g.id
                            WHERE m.id = :id
                            GROUP BY m.id");
        $stmt->execute([':id' => $movie_id]);
        $movie = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$movie) {
            echo "Movie not found.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit;
    }

    $rating_query = "SELECT AVG(rating) as avg_rating FROM reviews WHERE movie_id = ?";
    $rating_stmt = $pdo->prepare($rating_query);
    $rating_stmt->execute([$movie_id]);
    $avg_rating = $rating_stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'];

    $reviews_query = "SELECT r.*, u.username FROM reviews r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.movie_id = ?
                  ORDER BY r.created_at DESC";
    $reviews_stmt = $pdo->prepare($reviews_query);
    $reviews_stmt->execute([$movie_id]);
    $reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <?php include __DIR__ . '/template/header.html'; ?>
    <style>
    .stills {
        display: flex;
        flex-wrap: wrap;
    }

    .stills img {
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .movie-details {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 30px;
    }

    .movie-details .poster {
        flex: 0 0 200px;
        margin-right: 20px;
    }

    .movie-details .poster img {
        border-radius: 8px;
    }

    .movie-details .info {
        flex: 1;
    }

    .rating .stars i {
        color: gold;
    }

    .media {
        text-align: center;
    }

    #trailer iframe {
        width: 100%;
        height: 500px;
    }

    #add-to-favorites {
        cursor: pointer;
        transition: color 0.3s ease;
    }

    .btn.active {
        background-color: #333;
        color: #fff;
        border-color: #333;
    }

    .reviews {
        margin-top: 30px;
    }

    .review-frame {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        background-color: #f9f9f9;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .review-date {
        font-size: 0.9em;
        color: #777;
    }

    .review-rating {
        margin-bottom: 10px;
    }

    .review-content {
        margin-bottom: 0;
    }
    </style>
</head>

<body>
    <header>
        <?php include __DIR__ . '/template/navbar.php'; ?>
    </header>
    <main>
        <div class="container">
            <div class="movie-details">
                <div class="poster">
                    <img src="<?php echo ($movie['image']); ?>" alt="<?php echo ($movie['title_ukr']); ?>"
                        class="img-fluid" />
                </div>
                <div class="info">
                    <h1 id="movie-title" data-movie-id="<?php echo ($movie_id); ?>">
                        <?php echo ($movie['title_ukr']); ?> <i class="bi bi-heart" id="add-to-favorites"
                            type="button"></i>
                        <span class="average-rating" style="float: right; ">
                            <p
                                style="padding: 5px 10px; font-size: 24px; color:white; background-color: #0d6efd; border-radius: 8px;">
                                <?php echo number_format($avg_rating, 1); ?></p>
                        </span>
                    </h1>
                    <p><?php echo ($movie['title_orig']); ?></p>

                    <p id="movie-year"><strong>Рік:</strong> <?php echo ($movie['year']); ?></p>
                    <p id="movie-genre"><strong>Жанр:</strong> <?php echo ($movie['genres']); ?></p>
                    <p id="movie-genre"><strong>Країна:</strong> <?php echo ($movie['countries']); ?></p>
                    <p id="movie-description"><strong>Опис:</strong>
                        <?php echo ($movie['description']); ?></p>

                    <div class="actions">
                        <button class="btn btn-primary" id="add-to-watchlist">Буду дивитися</button>
                        <button class="btn btn-primary" id="mark-as-watching">Дивлюся</button>
                        <button class="btn btn-primary" id="mark-as-watched">Переглянуто</button>
                    </div>
                    <div class="rating mt-3" style="display: none;">
                        <span><strong>Оцінка</strong></span>
                        <div class="stars">
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                            <i class="bi bi-star"></i>
                        </div>
                    </div>
                    <div class="review mt-3" style="display: none;">
                        <textarea id="review-text" class="form-control" placeholder="Залиште свій відгук..."></textarea>
                        <button class="btn btn-primary mt-2" id="submit-review">Залишити відгук</button>
                    </div>
                </div>
            </div>
            <div class="media mt-5">
                <div id="trailer" class="embed-responsive embed-responsive-16by9 mx-auto">
                    <iframe src="<?php echo ($movie['trailer_url']); ?>" frameborder="0" allowfullscreen
                        class="embed-responsive-item"></iframe>
                </div>
            </div>
            <div class="reviews">
                <h3>Відгуки</h3>
                <?php foreach ($reviews as $review): ?>
                <div class="review-frame">
                    <div class="review-header">
                        <strong><?php echo($review['username']); ?></strong>
                        <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                    </div>
                    <div class="review-rating">
                        <?php
                            $rating = intval($review['rating']);
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $rating) {
                                    echo '<i class="bi bi-star-fill text-warning"></i>';
                                } else {
                                    echo '<i class="bi bi-star text-secondary"></i>';
                                }
                            }
                        ?>
                    </div>
                    <p class="review-content"><?php echo($review['review']); ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/template/footer.html'; ?>
    <script src="../assets/js/movie.js"></script>
</body>
</html>