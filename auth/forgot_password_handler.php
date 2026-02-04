<?php
// forgot_password_handler.php
// Purpose: Generate a password reset token and send a reset email (securely).

use PHPMailer\PHPMailer\Exception;

// Load DB connection ($pdo) and email configuration
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php';

// Return JSON responses
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Μη επιτρεπτή μέθοδος.']);
    exit;
}
// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$email = trim($data['email'] ?? '');

try {
    // Validate email format
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Μη έγκυρη διεύθυνση email.");
    }

    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        // IMPORTANT: Do not reveal whether the email exists.
        // Always return success to prevent account enumeration.

        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Αν το email υπάρχει στο σύστημά μας, θα λάβετε σύνδεσμο ανάκτησης.']);
        exit;
    }

    // 2. Create a secure random token (64 hex chars) and hash it
    $raw_token = bin2hex(random_bytes(32));
    $token_hash = hash('sha256', $raw_token);

    // 3. Set expiration time (24 hours)
    $expires_at = date('Y-m-d H:i:s', time() + (24 * 60 * 60)); // 24 ώρες

    // 4. Store token hash + expiry in DB (never store raw token)
    $updateStmt = $pdo->prepare("UPDATE users SET reset_token_hash = ?, reset_token_expires_at = ? WHERE id = ?");
    $updateStmt->execute([$token_hash, $expires_at, $user['id']]);

    // 5. Send reset email
    try {
        $mail = getMailer();
        $mail->addAddress($email, $user['username']);

        // Build reset URL (update to production domain if needed)
        $reset_link = "http://localhost/hermesrollerskate/auth/reset_password.php?token=" . $raw_token;

        $mail->isHTML(true);
        $mail->Subject = 'Ανάκτηση Κωδικού Hermes Roller Skate';
        $mail->Body    = '
            <h2>Αίτημα Αλλαγής Κωδικού</h2>
            <p>Λάβαμε αίτημα για αλλαγή κωδικού για τον λογαριασμό ' . htmlspecialchars($user['username']) . '. Κάντε κλικ στον παρακάτω σύνδεσμο:</p>
            <p><a href="' . $reset_link . '">' . $reset_link . '</a></p>
            <p><strong>Ο σύνδεσμος θα λήξει σε 30 λεπτά.</strong></p>
            <p>Αν δεν αιτηθήκατε αυτήν την αλλαγή, μπορείτε να αγνοήσετε αυτό το email.</p>';

        $mail->AltBody = 'Για να αλλάξετε τον κωδικό σας, επισκεφθείτε: ' . $reset_link;

        $mail->send();
    } catch (Exception $mailError) {
        // Log mail errors, but still return success for security
        error_log("PHPMailer Error (Reset): " . $mailError->getMessage());
    }

    // 6. Success response (always generic)
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Αν το email υπάρχει στο σύστημά μας, θα λάβετε σύνδεσμο ανάκτησης.']);
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()]);
} catch (Exception $e) {
    // Validation or input error
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
