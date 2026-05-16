<?php
// forgot_password_handler.php
// Purpose: Generate a password reset token and send a reset email (securely).

use PHPMailer\PHPMailer\Exception;

// Load DB connection ($pdo) and email configuration
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php';
require_once PROJECT_ROOT . 'includes/rate_limiter.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Return JSON responses
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.method_not_allowed')]);
    exit;
}
// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

try {
    // Validate email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception(t('auth.errors.invalid_email'));
    }

    // Rate limit check (3 requests / 60 λεπτά per IP)
    if (isRateLimited($pdo, 'forgot_password')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'forgot_password');
        http_response_code(429);
        echo json_encode([
            'status'      => 'error',
            'message'     => sprintf(t('auth.errors.rate_limit_forgot'), ceil($retryAfter / 60)),
            'retry_after' => $retryAfter,
        ]);
        exit;
    }
    recordAttempt($pdo, 'forgot_password');

    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => t('auth.errors.forgot_success')]);
        exit;
    }

    // 2. Create a secure random token (64 hex chars) and hash it
    $raw_token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $raw_token);

    // 3. Store token hash + expiry in DB (use MySQL NOW() to avoid PHP/MySQL timezone mismatch)
    $updateStmt = $pdo->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = NOW() + INTERVAL 1 HOUR WHERE id = ?");
    $updateStmt->execute([$token_hash, $user['id']]);

    // 5. Send reset email
    try {
        $mail = getMailer();
        $mail->addAddress($email, $user['username']);

        // Build reset URL (update to production domain if needed)
        // $reset_link = "http://localhost/hermesrollerskate/auth/reset_password.php?token=" . $raw_token;
        $reset_link = APP_URL . "/auth/reset_password.php?token=" . $raw_token;

        $mail->isHTML(true);
        $mail->Subject = 'Ανάκτηση Κωδικού Hermes Roller Skate';
        $mail->Body    = '
            <h2>Αίτημα Αλλαγής Κωδικού</h2>
            <p>Λάβαμε αίτημα για αλλαγή κωδικού για τον λογαριασμό ' . htmlspecialchars($user['username']) . '. Κάντε κλικ στον παρακάτω σύνδεσμο:</p>
            <p><a href="' . $reset_link . '">' . $reset_link . '</a></p>
            <p><strong>Ο σύνδεσμος θα λήξει σε 1 ώρα.</strong></p>
            <p>Αν δεν αιτηθήκατε αυτήν την αλλαγή, μπορείτε να αγνοήσετε αυτό το email.</p>';

        $mail->AltBody = 'Για να αλλάξετε τον κωδικό σας, επισκεφθείτε: ' . $reset_link;

        $mail->send();
    } catch (Exception $mailError) {
        // Log mail errors, but still return success for security
        error_log("PHPMailer Error (Reset): " . $mailError->getMessage());
    }

    // 6. Success response (always generic)
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => t('auth.errors.forgot_success')]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.db_error')]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
