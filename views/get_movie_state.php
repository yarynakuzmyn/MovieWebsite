<?php
session_start();
require_once __DIR__ . '/../server/db_connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user']['id']) || !isset($_GET['id'])) {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

$userId = $_SESSION['user']['id'];
$movieId = $_GET['id'];

try {
    // Check if movie is favorited
    $stmt = $pdo->prepare("SELECT * FROM liked WHERE user_id = :userId AND movie_id = :movieId");
    $stmt->execute([':userId' => $userId, ':movieId' => $movieId]);
    $isFavorite = $stmt->fetch() ? true : false;

    // Get current list status
    $stmt = $pdo->prepare("SELECT list FROM user_movie_list WHERE user_id = :userId AND movie_id = :movieId");
    $stmt->execute([':userId' => $userId, ':movieId' => $movieId]);
    $listStatus = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentList = $listStatus ? $listStatus['list'] : null;

    echo json_encode([
        'is_favorite' => $isFavorite,
        'current_list' => $currentList
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}