<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="main.php">UAKino</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="films.php">Фільми</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="series.php">Серіали</a>
                </li>
            </ul>
            <form class="d-flex me-3">
                <div class="input-group">
                    <input class="form-control" type="search" placeholder="Пошук" aria-label="Search" />
                    <button class="btn btn-outline-light" type="submit">
                        Пошук
                    </button>
                </div>
            </form>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="user.php"><i class="fas fa-user"></i> Профіль</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Вийти</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php"><i class="fas fa-user"></i> Увійти</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>