<?php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'email_config.php';

use PHPMailer\PHPMailer\Exception;

restrict_access(['admin']);

header('Content-Type: application/json');

$data   = json_decode(file_get_contents('php://input'), true);
$userId = (int)($data['user_id'] ?? 0);

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Ο χρήστης δεν βρέθηκε.']);
    exit;
}

// Generate a secure reset token
$rawToken   = bin2hex(random_bytes(32));
$tokenHash  = hash('sha256', $rawToken);

$updateStmt = $pdo->prepare("
    UPDATE users SET reset_token_hash = ?, reset_token_expires_at = NOW() + INTERVAL 1 HOUR
    WHERE id = ?
");
$updateStmt->execute([$tokenHash, $user['id']]);

$resetLink = APP_URL . "/auth/reset_password.php?token=" . $rawToken;

try {
    $mail = getMailer();
    $mail->addAddress($user['email'], $user['username']);
    $mail->isHTML(true);
    $mail->Subject = 'Επαναφορά Κωδικού — Hermes Roller Skate';
    $mail->Body = '
        <h2>Επαναφορά Κωδικού</h2>
        <p>Ο διαχειριστής ξεκίνησε επαναφορά κωδικού για τον λογαριασμό σας
           (<strong>' . htmlspecialchars($user['username']) . '</strong>).</p>
        <p><a href="' . $resetLink . '">Κάντε κλικ εδώ για να ορίσετε νέο κωδικό</a></p>
        <p><strong>Ο σύνδεσμος λήγει σε 1 ώρα.</strong></p>
        <p>Αν δεν γνωρίζατε αυτό το αίτημα, επικοινωνήστε με τον διαχειριστή.</p>';
    $mail->AltBody = 'Επαναφορά κωδικού: ' . $resetLink;
    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Email επαναφοράς εστάλη στο ' . $user['email'] . '.',
    ]);
} catch (Exception $e) {
    error_log("Admin password reset mail error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Αποτυχία αποστολής email. Δοκιμάστε ξανά.']);
}
