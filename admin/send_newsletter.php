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
    // Μόνο active subscribers + παίρνουμε και το token για το unsubscribe link
    $stmt = $pdo->query("
        SELECT email, unsubscribe_token 
        FROM newsletter_subscribers 
        WHERE is_active = 1
        ORDER BY subscribed_at DESC
    ");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($subscribers)) {
        echo json_encode(['success' => false, 'message' => 'Δεν υπάρχουν εγγεγραμμένοι.']);
        exit;
    }

    // Κρατάμε μόνο έγκυρα emails
    $validSubscribers = array_values(array_filter(
        $subscribers,
        fn($s) => filter_var($s['email'], FILTER_VALIDATE_EMAIL)
    ));

    if (empty($validSubscribers)) {
        echo json_encode(['success' => false, 'message' => 'Δεν υπάρχουν έγκυρα emails.']);
        exit;
    }

    $sent      = 0;
    $failed    = 0;
    $errors    = [];
    $batchSize = 50;

    // Base URL για το unsubscribe link
    // $baseUrl = rtrim(defined('BASE_URL') ? BASE_URL : 'https://hermesrollerskate.com', '/');
    // $baseUrl = rtrim(defined('BASE_URL') ? BASE_URL : 'http://localhost/hermesrollerskate', '/'); // ← τοπικό για ανάπτυξη
    $baseUrl = rtrim(APP_URL, '/');

    // Δημιουργούμε ΜΟΝΟ ΜΙΑ SMTP σύνδεση για όλα τα emails (αποφεύγουμε rate limiting)
    $mail = getMailer();
    $mail->SMTPKeepAlive = true;

    // Στέλνουμε ξεχωριστό email σε κάθε subscriber
    // ώστε το unsubscribe link να είναι personalized με το δικό του token
    foreach ($validSubscribers as $subscriber) {
        try {
            $mail->clearAddresses();
            $mail->isHTML(true);
            $mail->Subject = $subject;

            $mail->addAddress($subscriber['email']);

            // Unsubscribe link με το μοναδικό token του subscriber
            $unsubscribeUrl = $baseUrl . '/api/unsubscribe?token=' . urlencode($subscriber['unsubscribe_token']);

            // HTML body με unsubscribe link στο footer του email
            $mail->Body = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    ' . nl2br(htmlspecialchars($message)) . '
                    <hr style="margin: 2rem 0; border: none; border-top: 1px solid #eee;">
                    <p style="font-size: 12px; color: #999; text-align: center;">
                        Λαμβάνετε αυτό το email γιατί έχετε εγγραφεί στο newsletter μας.<br>
                        Αν δεν επιθυμείτε να λαμβάνετε άλλα emails, 
                        <a href="' . $unsubscribeUrl . '" style="color: #e63946;">κάντε κλικ εδώ για κατάργηση εγγραφής</a>.
                    </p>
                </div>
            ';

            $mail->AltBody = $message . "\n\n"
                . "---\n"
                . "Για κατάργηση εγγραφής από το newsletter: " . $unsubscribeUrl;

            $mail->send();
            $sent++;
        } catch (Exception $e) {
            $failed++;
            $errors[] = $subscriber['email'] . ': ' . $e->getMessage();
        }
    }

    // Κλείνουμε τη σύνδεση μετά το τέλος του loop
    $mail->smtpClose();

    echo json_encode([
        'success' => $sent > 0,
        'sent'    => $sent,
        'failed'  => $failed,
        'errors'  => array_slice($errors, 0, 5)
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
