<?php
include __DIR__ . '/../server/db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

try {
    if (!empty($search_query)) {
        $stmt = $pdo->prepare("
            SELECT * FROM movies 
            WHERE title_ukr LIKE :search 
            OR title_orig LIKE :search
            ORDER BY title_ukr
        ");
        $stmt->bindValue(':search', "%$search_query%", PDO::PARAM_STR);
        $stmt->execute();
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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

    <main class="container mt-4">
        <h3>Результати пошуку за запитом "<?php echo($search_query); ?>"</h3>
        
        <?php if (empty($search_query)): ?>
            <p>Будь ласка, введіть запит для пошуку.</p>
        <?php elseif (empty($search_results)): ?>
            <p>За вашим запитом нічого не знайдено.</p>
        <?php else: ?>
            <div class="movie-grid">
                <?php foreach ($search_results as $movie): ?>
                    <div class="movie-card">
                    <a href="movie_details.php?id=<?php echo $movie['id']; ?>">
                        
                            <img src="<?php echo ($movie['image']); ?>" class="card-img-top" alt="<?php echo ($movie['title_ukr']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo ($movie['title_ukr']); ?></h5>
                                <p class="card-text"><?php echo ($movie['title_orig']); ?></p>
                                
                            
                        </div></a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/template/footer.html'; ?>
</body>
</html>