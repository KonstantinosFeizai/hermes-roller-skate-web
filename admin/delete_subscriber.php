<?php
// admin/delete_subscriber.php
// Purpose: Delete a newsletter subscriber (Admin only).

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρη μέθοδος.']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρο email.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM newsletter_subscribers WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Ο συνδρομητής διαγράφηκε επιτυχώς.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Ο συνδρομητής δεν βρέθηκε.']);
    }
} catch (PDOException $e) {
    error_log("Delete Subscriber Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
