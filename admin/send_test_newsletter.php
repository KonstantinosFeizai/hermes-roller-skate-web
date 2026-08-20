<?php
// admin/send_test_newsletter.php
// Purpose: Send a test newsletter email to the logged-in admin.

header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'email_config.php';

restrict_access(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρη μέθοδος.']);
    exit;
}

$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($subject === '' || $message === '') {
    echo json_encode(['success' => false, 'message' => 'Συμπλήρωσε θέμα και μήνυμα.']);
    exit;
}

// Παίρνουμε το email του συνδεδεμένου Admin από το Session
$adminEmail = $_SESSION['user_email'] ?? $_SESSION['email'] ?? null;

if ((!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) && !empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$_SESSION['user_id']]);
    $adminEmail = $stmt->fetchColumn() ?: null;
}

if (!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Δεν βρέθηκε έγκυρο email Admin στο session.']);
    exit;
}

try {
    $mail = getMailer();
    $mail->addAddress($adminEmail);
    $mail->isHTML(true);
    $mail->Subject = '[TEST] ' . $subject;

    $baseUrl = rtrim(APP_URL, '/');
    $dummyUnsubscribeUrl = $baseUrl . '/api/unsubscribe.php?token=TEST_TOKEN';

    $mail->Body = '
        <div style="background-color: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin-bottom: 15px; text-align: center; font-weight: bold;">
            ⚠️ ΑΥΤΟ ΕΙΝΑΙ ΔΟΚΙΜΑΣΤΙΚΟ EMAIL (TEST SEND)
        </div>
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            ' . nl2br(htmlspecialchars($message)) . '
            <hr style="margin: 2rem 0; border: none; border-top: 1px solid #eee;">
            <p style="font-size: 12px; color: #999; text-align: center;">
                Λαμβάνετε αυτό το email γιατί έχετε εγγραφεί στο newsletter μας.<br>
                <a href="' . $dummyUnsubscribeUrl . '" style="color: #e63946;">κάντε κλικ εδώ για κατάργηση εγγραφής</a>.
            </p>
        </div>
    ';

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Το δοκιμαστικό email στάλθηκε επιτυχώς στο ' . $adminEmail
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Αποτυχία αποστολής: ' . $e->getMessage()
    ]);
}
