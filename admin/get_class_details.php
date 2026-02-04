<?php
// get_class_details.php
// Purpose: Return lesson details, participants, and suggested athletes.
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$class_id = $_GET['id'] ?? 0;

try {
    // 1. Fetch lesson details
    $stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
    $stmt->execute([$class_id]);
    $lesson = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$lesson) {
        echo json_encode(['status' => 'error', 'message' => 'Το μάθημα δεν βρέθηκε.']);
        exit;
    }

    // 2. Fetch already enrolled athletes
    $stmt = $pdo->prepare("
        SELECT u.id, u.first_name, u.last_name, le.attended, le.payment_status 
        FROM lesson_enrollments le
        JOIN users u ON le.user_id = u.id
        WHERE le.lesson_id = ?
    ");
    $stmt->execute([$class_id]);
    $participants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Fetch suggested athletes (same region, not already enrolled)
    $stmt = $pdo->prepare("
        SELECT id, first_name, last_name, region 
        FROM users 
        WHERE region = ? AND role = 'user' AND id NOT IN (
            SELECT user_id FROM lesson_enrollments WHERE lesson_id = ?
        )
        LIMIT 10
    ");
    $stmt->execute([$lesson['location'], $class_id]);
    $suggestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'lesson' => $lesson,
        'participants' => $participants,
        'suggestions' => $suggestions
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
