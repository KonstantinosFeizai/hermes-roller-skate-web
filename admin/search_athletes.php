<?php
// search_athletes.php
// Purpose: Search athletes by name for class enrollment (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$query = $_GET['q'] ?? '';
$class_id = $_GET['class_id'] ?? 0;

try {
    // Search by first or last name
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, region 
        FROM users 
        WHERE (first_name LIKE ? OR last_name LIKE ?) 
        AND role = 'user' 
        AND id NOT IN (
            SELECT user_id FROM lesson_enrollments WHERE lesson_id = ?
        )
        LIMIT 10
    ");

    $searchTerm = "%$query%";
    $stmt->execute([$searchTerm, $searchTerm, $class_id]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'results' => $results]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
