<?php
    include __DIR__ . '/../server/db_connection.php';
    session_start();

    try {
        $stmt = $pdo->prepare("SELECT * FROM movies WHERE type = 'film' ORDER BY RAND() DESC LIMIT 5");
        $stmt->execute();
        $films = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare("SELECT * FROM movies WHERE type = 'series' ORDER BY RAND() DESC LIMIT 5");
        $stmt->execute();
        $series = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Fetch 3 random movies for the carousel
    try {
        $stmt = $pdo->prepare("SELECT id, title_ukr, carousel FROM movies ORDER BY RAND() LIMIT 3");
        $stmt->execute();
        $carouselMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <?php foreach ($carouselMovies as $index => $movie): ?>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="<?php echo $index; ?>"
                <?php echo $index === 0 ? 'class="active" aria-current="true"' : ''; ?>
                aria-label="Slide <?php echo $index + 1; ?>"></button>
        <?php endforeach; ?>
    </div>

    <div class="carousel-inner">
        <?php foreach ($carouselMovies as $index => $movie): ?>
            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                    <img src="<?php echo htmlspecialchars($movie['carousel']); ?>" 
                         alt="<?php echo htmlspecialchars($movie['title_ukr']); ?>" 
                         class="d-block w-100" />
                </a>
            </div>
        <?php endforeach; ?>
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