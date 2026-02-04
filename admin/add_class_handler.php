<?php
// add_class_handler.php
// Purpose: Create a new lesson (admin only).
require_once __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// 1. Require admin session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη ενέργεια.']);
    exit;
}

// 2. Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

$title = trim($data['title'] ?? '');
$location = trim($data['location'] ?? '');
$lesson_datetime = $data['lesson_datetime'] ?? '';

// 3. Basic validation
if (empty($title) || empty($location) || empty($lesson_datetime)) {
    echo json_encode(['status' => 'error', 'message' => 'Παρακαλώ συμπληρώστε όλα τα πεδία.']);
    exit;
}

try {
    // 4. Insert into lessons table
    $stmt = $pdo->prepare("INSERT INTO lessons (title, location, lesson_datetime) VALUES (?, ?, ?)");
    $stmt->execute([$title, $location, $lesson_datetime]);

    echo json_encode(['status' => 'success', 'message' => 'Η προπόνηση δημιουργήθηκε επιτυχώς!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης: ' . $e->getMessage()]);
}
