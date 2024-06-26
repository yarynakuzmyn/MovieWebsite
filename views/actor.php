<!-- actor.php -->
<?php
include __DIR__ . '/../server/db_connection.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $actorId = $_GET['id'];
    
    try {
        // Actor details
        $stmt = $pdo->prepare("SELECT * FROM cast WHERE id = ?");
        $stmt->execute([$actorId]);
        $actor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$actor) {
            throw new Exception("Actor not found.");
        }

        // Movies of the actor with roles
        $stmt = $pdo->prepare("SELECT m.id, m.title_ukr, m.title_orig, m.image, mc.role FROM movies m
                               INNER JOIN movie_cast mc ON m.id = mc.movie_id
                               WHERE mc.cast_id = ?");
        $stmt->execute([$actorId]);
        $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid actor ID.";
    exit;
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
            
            <div class="row">
                <div class="col-md-3">
                    <img src="<?php echo ($actor['image']); ?>" alt="<?php echo ($actor['name']); ?>" class="img-fluid" style="border-radius:8px;">
                    <h2 style="text-align:center;"><?php echo ($actor['name']); ?></h2>
                </div>
                <div class="col-md-8">
                    <h3>Фільми та серіали</h3>
                    <div class="movie-grid" style="float: left;">
                        <?php foreach ($movies as $movie): ?>
                        <div class="movie-card">
                            <a href="movie_details.php?id=<?php echo ($movie['id']); ?>"><img
                                    src="<?php echo ($movie['image']); ?>" alt="<?php echo ($movie['title_ukr']); ?>">
                                <h3><?php echo ($movie['title_ukr']); ?></h3>
                                <p><?php echo ($movie['title_orig']); ?></p>
                                <h6><?php echo ($movie['role']); ?></h6>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    
    <?php include __DIR__ . '/template/footer.html'; ?>
</body>
</html>
