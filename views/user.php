<?php
    session_start();
    require_once __DIR__ . '/../server/db_connection.php';

    // Check if the user is logged in
    if (!isset($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }

    // Take user data from the database
    $user_id = $_SESSION['user']['id'];
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($query);

    try {
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if user exists
        if (!$user) {
            throw new Exception("User not found");
        }
    } catch (PDOException $e) {
        echo "Database Error: " . $e->getMessage();
        exit();
    } catch (Exception $e) {
        echo $e->getMessage();
        exit();

    
}
function getUserRatings($pdo, $user_id) {
    $query = "SELECT m.id, m.title_ukr, m.title_orig, m.image, r.rating, r.created_at 
              FROM reviews r 
              JOIN movies m ON r.movie_id = m.id 
              WHERE r.user_id = ? 
              ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserReviews($pdo, $user_id) {
    $query = "SELECT m.id, m.title_ukr, m.title_orig, m.image, r.review, r.rating, r.created_at 
              FROM reviews r 
              JOIN movies m ON r.movie_id = m.id 
              WHERE r.user_id = ? 
              ORDER BY r.created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserMovieList($pdo, $user_id, $list) {
    $query = "SELECT m.id, m.title_ukr, m.title_orig, m.image 
              FROM user_movie_list uml 
              JOIN movies m ON uml.movie_id = m.id 
              WHERE uml.user_id = ? AND uml.list = ?
            ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $list]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserFavorites($pdo, $user_id) {
    $query = "SELECT m.id, m.title_ukr, m.title_orig, m.image 
              FROM liked l 
              JOIN movies m ON l.movie_id = m.id 
              WHERE l.user_id = ? 
              ";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch data for each tab
$ratings = getUserRatings($pdo, $user_id);
$reviews = getUserReviews($pdo, $user_id);
$watched = getUserMovieList($pdo, $user_id, 'watched');
$watching = getUserMovieList($pdo, $user_id, 'watching');
$watchlist = getUserMovieList($pdo, $user_id, 'watchlist');
$favorites = getUserFavorites($pdo, $user_id);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/template/header.html'; ?>
    <style>
    .movie-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: flex-start;
        padding: 20px;
    }

    .movie-grid a {
        text-decoration: none;
    }

    .movie-list {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 15px;
        justify-content: flex-start;
    }

    .movie-card {
        box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
        text-align: center;
        width: 180px;
        padding: 8px;
        margin: 10px 0;
    }

    .movie-grid a:hover {
        filter: grayscale(70%);
        color: blue;
    }

    .movie-card img {
        border-radius: 8px;
        max-width: 100%;
        height: 240px;
        object-fit: cover;
        display: block;
        margin: 0 auto 8px;
    }

    .movie-card h5 {
        font-size: 1em;
        margin: 8px 0 4px;
        color: black;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .movie-card h5:hover {
        color: blue;
        text-decoration: none;
    }

    .movie-card a:hover {
        text-decoration: none;
    }

    .movie-card p {
        font-size: 0.9em;
        color: #bbb;
        margin-bottom: 4px;
    }

    .movie-card .card-text {
        font-size: 0.8em;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    </style>
</head>

<body>
    <header>
        <?php include __DIR__ . '/template/navbar.php'; ?>
    </header>
    <main>

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="nav nav-pills nav-justified" id="v-pills-tab" role="tablist">
                        <a class="nav-link active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home"
                            role="tab" aria-controls="v-pills-home" aria-selected="true"><i
                                class="bi bi-person-fill mr-2"></i> Профіль</a>

                        <a class="nav-link" id="v-pills-rating-tab" data-toggle="pill" href="#v-pills-rating" role="tab"
                            aria-controls="v-pills-rating" aria-selected="false"><i class="bi bi-star-fill mr-2"></i>
                            Оцінки</a>

                        <a class="nav-link" id="v-pills-review-tab" data-toggle="pill" href="#v-pills-review" role="tab"
                            aria-controls="v-pills-review" aria-selected="false"><i
                                class="bi bi-chat-right-dots-fill"></i></i> Відгуки</a>

                        <a class="nav-link" id="v-pills-watched-tab" data-toggle="pill" href="#v-pills-watched"
                            role="tab" aria-controls="v-pills-watched" aria-selected="false"><i
                                class="bi bi-bookmark-check-fill mr-2"></i> Переглянуто</a>

                        <a class="nav-link" id="v-pills-watch-tab" data-toggle="pill" href="#v-pills-watch" role="tab"
                            aria-controls="v-pills-watch" aria-selected="false"><i class="bi bi-eye-fill mr-2"></i> Дивлюся</a>

                        <a class="nav-link" id="v-pills-watchlist-tab" data-toggle="pill" href="#v-pills-watchlist"
                            role="tab" aria-controls="v-pills-watchlist" aria-selected="false"><i
                                class="bi bi-bookmark-plus-fill mr-2"></i> Буду дивитися</a>

                        <a class="nav-link" id="v-pills-fav_f-tab" data-toggle="pill" href="#v-pills-fav_f" role="tab"
                            aria-controls="v-pills-fav_f" aria-selected="false"><i class="bi bi-heart-fill mr-2"></i>
                            Улюблені</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="tab-content mt-3" id="v-pills-tabContent">
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab">
                            <h2><b></b><?php echo ($user['username']); ?>
                            </h2>
                            <h4><b>Email:</b> <?php echo($user['email']);?></h4>
                        </div>

                        <!-- Ratings Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-rating" role="tabpanel" aria-labelledby="v-pills-rating-tab">
                            <h3><i class="bi bi-star mr-2"></i> Оцінки</h3>
                            <div class="movie-list">
                                <?php foreach ($ratings as $item): ?>
                                <a href="movie_details.php?id=<?php echo $item['id']; ?>" class="movie-link">
                                    <div class="movie-card">
                                        <img src="<?php echo ($item['image']); ?>" class="card-img-top"
                                            alt="<?php echo ($item['title_ukr']); ?>">
                                        <div class="card-body">
                                            <h3 class="card-title"><?php echo ($item['title_ukr']); ?></h3>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <?php
                                                    $rating = intval($item['rating']);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= $rating) {
                                                            echo '<i class="bi bi-star-fill text-warning"></i>';
                                                        } else {
                                                            echo '<i class="bi bi-star text-secondary"></i>';
                                                        }
                                                    }
                                                    ?>
                                                </small>
                                            </p>
                                        </div>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-review" role="tabpanel"
                            aria-labelledby="v-pills-review-tab">
                            <h3><i class="bi bi-chat-right-dots"></i> Відгуки</h3>
                            <div class="movie-list">
                                <?php foreach ($reviews as $item): ?>
                                <a href="movie_details.php?id=<?php echo $item['id']; ?>" class="movie-link">
                                <div class="movie-card">
                                    <img src="<?php echo ($item['image']); ?>" class="card-img-top"
                                        alt="<?php echo ($item['title_ukr']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo ($item['title_ukr']); ?></h3>

                                        <h4 class="card-text">
                                            <?php echo (substr($item['review'], 0, 100)); ?>
                                        </h4>
                                    </div>
                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Watched Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-watched" role="tabpanel"
                            aria-labelledby="v-pills-watched-tab">
                            <h3><i class="bi bi-bookmark-check mr-2"></i> Переглянуто</h3>
                            <div class="movie-list">
                                <?php foreach ($watched as $movie): ?>
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="movie-link">
                                <div class="movie-card">
                                    <img src="<?php echo($movie['image']); ?>" class="card-img-top"
                                        alt="<?php echo($movie['title_ukr']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title">
                                            <?php echo($movie['title_ukr']); ?></h3>
                                        <p><?php echo($movie['title_orig']); ?></p>

                                    </div>
                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Watching Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-watch" role="tabpanel"
                            aria-labelledby="v-pills-watch-tab">
                            <h3><i class="bi bi-eye mr-2"></i> Дивлюся</h3>
                            <div class="movie-list">
                                <?php foreach ($watching as $movie): ?>
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="movie-link">
                                <div class="movie-card">
                                    <img src="<?php echo ($movie['image']); ?>" class="card-img-top"
                                        alt="<?php echo ($movie['title_ukr']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo ($movie['title_ukr']); ?>
                                        </h3>
                                        <p><?php echo($movie['title_orig']); ?></p>

                                    </div>
                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Watchlist Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-watchlist" role="tabpanel"
                            aria-labelledby="v-pills-watchlist-tab">
                            <h3><i class="bi bi-bookmark-plus mr-2"></i> Буду дивитися</h3>
                            <div class="movie-list">
                                <?php foreach ($watchlist as $movie): ?>
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="movie-link">
                                <div class="movie-card">
                                    <img src="<?php echo ($movie['image']); ?>" class="card-img-top"
                                        alt="<?php echo ($movie['title_ukr']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo ($movie['title_ukr']); ?>
                                        </h3>
                                        <p><?php echo($movie['title_orig']); ?></p>
                                    </div>
                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Favorites Tab -->
                        <div class="movie-grid tab-pane fade" id="v-pills-fav_f" role="tabpanel"
                            aria-labelledby="v-pills-fav_f-tab">
                            <h3><i class="bi bi-heart mr-2"></i> Улюблені</h3>
                            <div class="movie-list">
                                <?php foreach ($favorites as $movie): ?>
                                <a href="movie_details.php?id=<?php echo $movie['id']; ?>" class="movie-link">
                                <div class="movie-card">
                                    <img src="<?php echo ($movie['image']); ?>" class="card-img-top"
                                        alt="<?php echo ($movie['title_ukr']); ?>">
                                    <div class="card-body">
                                        <h3 class="card-title"><?php echo ($movie['title_ukr']); ?>
                                        </h3>
                                        <p><?php echo($movie['title_orig']); ?></p>
                                    </div>
                                </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php include __DIR__ . '/template/footer.html'; ?>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>