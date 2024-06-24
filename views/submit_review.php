<?php
session_start();
require_once __DIR__ . '/../server/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['movieId']) || !isset($input['review']) || !isset($input['rating'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    $userId = $_SESSION['user']['id'];
    $movieId = $input['movieId'];
    $review = $input['review'];
    $rating = $input['rating'];

    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (user_id, movie_id, review, rating) VALUES (:userId, :movieId, :review, :rating)");
        $stmt->execute([
            ':userId' => $userId,
            ':movieId' => $movieId,
            ':review' => $review,
            ':rating' => $rating
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}