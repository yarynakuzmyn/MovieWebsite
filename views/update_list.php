<?php
session_start();
require_once __DIR__ . '/../server/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['action']) || !isset($input['movieId'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $movieId = $input['movieId'];
    $action = $input['action'];

    try {
        if ($action === 'toggle_favorite') {
            $stmt = $pdo->prepare("SELECT * FROM liked WHERE user_id = :userId AND movie_id = :movieId");
            $stmt->execute([':userId' => $userId, ':movieId' => $movieId]);
            $exists = $stmt->fetch();

            if ($exists) {
                $stmt = $pdo->prepare("DELETE FROM liked WHERE user_id = :userId AND movie_id = :movieId");
            } else {
                $stmt = $pdo->prepare("INSERT INTO liked (user_id, movie_id) VALUES (:userId, :movieId)");
            }
            $stmt->execute([':userId' => $userId, ':movieId' => $movieId]);

            echo json_encode(['success' => true, 'isFavorite' => !$exists]);
        } elseif ($action === 'update_list') {
    $list = $input['list'] ?? null;

    // Remove from all lists
    $stmt = $pdo->prepare("DELETE FROM user_movie_list WHERE user_id = :userId AND movie_id = :movieId");
    $stmt->execute([
        ':userId' => $userId,
        ':movieId' => $movieId
    ]);

    if ($list) {
        // Add to specified list
        $stmt = $pdo->prepare("INSERT INTO user_movie_list (user_id, movie_id, list) VALUES (:userId, :movieId, :list)");
        $stmt->execute([
            ':userId' => $userId,
            ':movieId' => $movieId,
            ':list' => $list
        ]);
    }

    echo json_encode(['success' => true, 'currentList' => $list]);
}
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}