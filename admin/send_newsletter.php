<?php
// send_newsletter.php
// Purpose: Send newsletter email to all subscribers (admin only).
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'email_config.php';

// Admin-only access
restrict_access(['admin']);

// Return JSON responses
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρη μέθοδος.']);
    exit;
}

$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
if ($subject === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Συμπλήρωσε θέμα και μήνυμα.']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT email FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $emails = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($emails)) {
        echo json_encode(['success' => false, 'message' => 'Δεν υπάρχουν εγγεγραμμένοι.']);
        exit;
    }

    // Keep only valid emails
    $validEmails = array_values(array_filter($emails, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL)));

    if (empty($validEmails)) {
        echo json_encode(['success' => false, 'message' => 'Δεν υπάρχουν έγκυρα emails.']);
        exit;
    }

    $sent = 0;
    $failed = 0;
    $errors = [];
    $batchSize = 50;

    // Send in BCC batches to avoid provider limits
    foreach (array_chunk($validEmails, $batchSize) as $batch) {
        try {
            $mail = getMailer();
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = nl2br(htmlspecialchars($message));
            $mail->AltBody = $message;

            // Required visible recipient
            $mail->addAddress('no-reply@hermesrollerskate.gr', 'Hermes Roller Skate');

            foreach ($batch as $email) {
                $mail->addBCC($email);
            }

            $mail->send();
            $sent += count($batch);
        } catch (Exception $e) {
            $failed += count($batch);
            $errors[] = $e->getMessage();
        }
    }

    echo json_encode([
        'success' => $sent > 0,
        'sent' => $sent,
        'failed' => $failed,
        'errors' => array_slice($errors, 0, 3)
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
