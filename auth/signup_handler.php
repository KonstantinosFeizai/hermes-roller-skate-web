<?php
// signup_handler.php
// Purpose: Create a new account and send a verification email.
//          Αν το email/username υπάρχει αλλά δεν έχει επιβεβαιωθεί → ανανέωση + νέο email.

session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php';
require_once PROJECT_ROOT . 'includes/rate_limiter.php';
require_once PROJECT_ROOT . 'includes/lang.php';

use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.method_not_allowed')]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username         = trim($data['username'] ?? '');
$email            = trim($data['email'] ?? '');
$password         = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';
$accepted_terms   = !empty($data['accepted_terms']);

try {
    // ── 1. Basic validation ───────────────────────────────────
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception("Παρακαλώ συμπληρώστε όλα τα πεδία.");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Μη έγκυρη διεύθυνση email.");
    }
    if ($password !== $confirm_password) {
        throw new Exception("Οι κωδικοί πρόσβασης δεν ταιριάζουν.");
    }
    if (strlen($password) < 6) {
        throw new Exception("Ο κωδικός πρόσβασης πρέπει να είναι τουλάχιστον 6 χαρακτήρες.");
    }
    if (!$accepted_terms) {
        throw new Exception("Πρέπει να αποδεχτείς τους Όρους Χρήσης και την Πολιτική Απορρήτου.");
    }

    // ── 2. Rate limit ─────────────────────────────────────────
    if (isRateLimited($pdo, 'signup')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'signup');
        http_response_code(429);
        echo json_encode([
            'status'      => 'error',
            'message'     => 'Πολλές εγγραφές από αυτή τη σύνδεση. Δοκιμάστε ξανά σε ' . ceil($retryAfter / 60) . ' λεπτά.',
            'retry_after' => $retryAfter,
        ]);
        exit;
    }
    recordAttempt($pdo, 'signup');

    // ── 3. Έλεγχος αν υπάρχει ήδη ο χρήστης ─────────────────
    $stmt = $pdo->prepare("
        SELECT id, is_active, email 
        FROM users 
        WHERE username = ? OR email = ?
        LIMIT 1
    ");
    $stmt->execute([$username, $email]);
    $existing = $stmt->fetch();

    // Κοινά δεδομένα για INSERT και UPDATE
    $hashed_password    = password_hash($password, PASSWORD_BCRYPT);
    $confirmation_token = bin2hex(random_bytes(32));
    $accepted_terms_at  = date("Y-m-d H:i:s");

    if ($existing) {
        if ($existing['is_active'] == 1) {
            // ── Ενεργός λογαριασμός → απαγορεύεται ──────────
            throw new Exception(t('auth.errors.user_exists'));
        }

        // ── Ανενεργός λογαριασμός → ανανέωση ────────────────
        // Ο χρήστης είχε εγγραφεί αλλά δεν επιβεβαίωσε ποτέ.
        // Ανανεώνουμε κωδικό, token και στοιχεία — στέλνουμε νέο email.
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username          = ?,
                email             = ?,
                password          = ?,
                confirm_token     = ?,
                accepted_terms_at = ?,
                created_at        = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $username,
            $email,
            $hashed_password,
            $confirmation_token,
            $accepted_terms_at,
            $existing['id'],
        ]);
    } else {
        // ── Νέος χρήστης → INSERT ────────────────────────────
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, confirm_token, accepted_terms_at)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $hashed_password, $confirmation_token, $accepted_terms_at]);
    }

    // ── 4. Αποστολή verification email ───────────────────────
    // (ίδιο για νέο χρήστη ΚΑΙ για ανανέωση)
    try {
        $mail = getMailer();
        $mail->addAddress($email, $username);

        $verification_link = APP_URL . "/auth/verify.php?token=" . $confirmation_token;

        $mail->isHTML(true);
        $mail->Subject = 'Επιβεβαίωση Λογαριασμού Hermes Roller Skate';
        $mail->Body    = '
            <h2>Καλώς ήρθες, ' . htmlspecialchars($username) . '!</h2>
            <p>Για να ενεργοποιήσεις τον λογαριασμό σου, παρακαλώ κάνε κλικ στον παρακάτω σύνδεσμο:</p>
            <p><a href="' . $verification_link . '">' . $verification_link . '</a></p>
            <p>Αν δεν αιτηθήκατε εγγραφή, μπορείτε να αγνοήσετε αυτό το email.</p>';
        $mail->AltBody = 'Για να ενεργοποιήσετε τον λογαριασμό σας, επισκεφθείτε: ' . $verification_link;

        $mail->send();
    } catch (Exception $mailError) {
        error_log("PHPMailer Error: " . $mailError->getMessage());
    }

    http_response_code(201);
    echo json_encode([
        'status'  => 'success',
        'message' => t('auth.errors.signup_success'),
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.db_error')]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
