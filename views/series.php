<!-- @format -->
<?php 
    include __DIR__ . '/../server/db_connection.php';
    try {
        // Years
        $stmt = $pdo->prepare("SELECT DISTINCT year FROM movies WHERE type = 'series' ORDER BY year DESC ");
        $stmt->execute();
        $years = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Genres
        $stmt = $pdo->prepare("SELECT id, name FROM genre ORDER BY name");
        $stmt->execute();
        $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Countries
        $stmt = $pdo->prepare("SELECT id, name FROM countries ORDER BY name");
        $stmt->execute();
        $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    $sql = "SELECT m.id, m.title_ukr, m.title_orig, m.description, m.image, m.year, c.name AS country, GROUP_CONCAT(g.name) AS genres
            FROM movies m
            LEFT JOIN movie_country mc ON m.id = mc.movie_id
            LEFT JOIN countries c ON mc.country_id = c.id
            LEFT JOIN movie_genre mg ON m.id = mg.movie_id
            LEFT JOIN genre g ON mg.genre_id = g.id
            WHERE m.type = 'series'";

    $params = [];

    // Apply filters
    if (!empty($_GET['year'])) {
        $sql .= " AND m.year = :year";
        $params[':year'] = $_GET['year'];
    }
    if (!empty($_GET['genre'])) {
        $sql .= " AND g.name = :genre";
        $params[':genre'] = $_GET['genre'];
    }
    if (!empty($_GET['country'])) {
        $sql .= " AND c.name = :country";
        $params[':country'] = $_GET['country'];
    }
    if (!empty($_GET['search'])) {
        $sql .= " AND (m.title_ukr LIKE :search OR m.title_orig LIKE :search)";
        $params[':search'] = '%' . $_GET['search'] . '%';
    }

    $sql .= " GROUP BY m.id";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <h1>Серіали</h1>
            <div class="row">
                <div class="col-12">
                <?php include 'filtering_form.php'; ?>
                </div>
            </div>
            <div class="movie-grid">
                <?php foreach ($movies as $movie): ?>
                <div class="movie-card">
                    <a href="movie_details.php?id=<?php echo ($movie['id']); ?>">
                        <img src="<?php echo ($movie['image']); ?>" alt="<?php echo ($movie['title_ukr']); ?>" />
                        <h3><?php echo ($movie['title_ukr']); ?></h3>
                        <p><?php echo ($movie['title_orig']); ?></p>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>

        </div>
    </main>
    <?php include __DIR__ . '/template/footer.html'; ?>
</body>

</html>