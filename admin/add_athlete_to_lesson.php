<?php
// admin/add_athlete_to_lesson.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');
restrict_access('admin');

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$lesson_id  = !empty($data['lesson_id'])  ? (int)$data['lesson_id']  : 0;
$athlete_id = !empty($data['athlete_id']) ? (int)$data['athlete_id'] : 0;

if (!$lesson_id || !$athlete_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρα δεδομένα.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO lesson_athletes (lesson_id, athlete_id, attended)
        VALUES (?, ?, 0)
    ");
    $stmt->execute([$lesson_id, $athlete_id]);

    $stmtLesson = $pdo->prepare("
        SELECT title, lesson_datetime
        FROM lessons
        WHERE id = ?
    ");
    $stmtLesson->execute([$lesson_id]);
    $lesson = $stmtLesson->fetch(PDO::FETCH_ASSOC);

    $stmtAthlete = $pdo->prepare("
        SELECT user_id, parent_id, CONCAT(first_name, ' ', last_name) AS athlete_name
        FROM athletes
        WHERE id = ?
    ");
    $stmtAthlete->execute([$athlete_id]);
    $athlete = $stmtAthlete->fetch(PDO::FETCH_ASSOC);

    if ($lesson && $athlete) {
        $recipientIds = [];
        if (!empty($athlete['user_id'])) $recipientIds[] = (int)$athlete['user_id'];
        if (!empty($athlete['parent_id'])) $recipientIds[] = (int)$athlete['parent_id'];
        $recipientIds = array_values(array_unique(array_filter($recipientIds)));

        $lessonLabel = $lesson['title'] ?: 'νέο class';
        if (!empty($lesson['lesson_datetime'])) {
            $lessonLabel .= ' · ' . date('d/m/Y H:i', strtotime($lesson['lesson_datetime']));
        }

        foreach ($recipientIds as $recipientId) {
            createTranslatedNotification(
                $pdo,
                $recipientId,
                'new_class',
                [
                    'athlete_name' => $athlete['athlete_name'] ?: 'The athlete',
                    'lesson_label' => $lessonLabel,
                ],
                $lesson_id,
                'lessons'
            );
        }
    }

    echo json_encode(['status' => 'success', 'message' => 'Ο αθλητής προστέθηκε.']);
} catch (PDOException $e) {
    error_log('add_athlete_to_lesson error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
