<?php
    include __DIR__ . '/../server/db_connection.php';
    session_start();

    try {
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE type = 'film' ORDER BY year DESC");
        $stmt->execute();
        $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM movies WHERE type = 'series' ORDER BY year DESC");
        $stmt->execute();
        $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/template/header.html'; ?>
</head>

<body>
    <header>
        <?php include __DIR__ . '/template/navbar.php'; ?>
    </header>

    <main>
        <div class="container">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0"
                        class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"
                        aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"
                        aria-label="Slide 3"></button>
                </div>

                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <a href="movie_details.php">
                            <img src="img/h280_51807236.jpg" alt="Bridgerton" class="d-block w-100" />
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="movie_details.php">
                            <img src="img/h280_52246513.jpg" alt="Kingdom of the Planet of the Apes"
                                class="d-block w-100" />
                        </a>
                    </div>

                    <div class="carousel-item">
                        <a href="movie_details.php">
                            <img src="img/h280_52361467.jpg" alt="Dark Matter" class="d-block w-100" />
                        </a>
                    </div>
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>

        <section class="content">
            <h2>Popular</h2>
            <div class="movie-grid">
                <div class="movie-list">
                    <?php foreach ($films as $film): ?>
                    <div class="movie-card">
                        <a href="movie_details.php?id=<?php echo ($film['id']); ?>">
                            <img src="<?php echo ($film['image']); ?>" alt="<?php echo ($film['title_ukr']); ?>" />
                            <h3><?php echo ($film['title_ukr']); ?></h3>
                            <p><?php echo ($film['title_orig']); ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <h2>Movies</h2>
            <div class="movie-grid">
                <div class="movie-list">
                    <?php foreach ($films as $film): ?>
                    <div class="movie-card">
                        <a href="movie_details.php?id=<?php echo ($film['id']); ?>">
                            <img src="<?php echo ($film['image']); ?>" alt="<?php echo ($film['title_ukr']); ?>" />
                            <h3><?php echo ($film['title_ukr']); ?></h3>
                            <p><?php echo ($film['title_orig']); ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <h2>Serials</h2>
            <div class="movie-grid">
                <div class="movie-list">
                    <?php foreach ($series as $serial): ?>
                    <div class="movie-card">
                        <a href="movie_details.php?id=<?php echo ($serial['id']); ?>">
                            <img src="<?php echo ($serial['image']); ?>" alt="<?php echo ($serial['title_ukr']); ?>" />
                            <h3><?php echo ($serial['title_ukr']); ?></h3>
                            <p><?php echo ($serial['title_orig']); ?></p>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>
    <?php include __DIR__ . '/template/footer.html'; ?>
    <script src="slider.js"></script>
</body>

</html>