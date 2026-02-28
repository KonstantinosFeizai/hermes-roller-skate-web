<?php
// signup_handler.php
// Purpose: Create a new account and send a verification email.

session_start();
// Load DB connection ($pdo)
require_once __DIR__ . '/../config.php';

// Load email configuration (PHPMailer)
require_once PROJECT_ROOT . 'email_config.php';

use PHPMailer\PHPMailer\Exception;

// Return JSON responses
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Μη επιτρεπτή μέθοδος αιτήματος.']);
    exit;
}

// Read JSON payload from Fetch/Ajax
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';

try {
    // 1. Basic validation
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

    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);

    if ($stmt->fetch()) {
        // Username or email already in use
        throw new Exception("Το όνομα χρήστη ή το email χρησιμοποιείται ήδη. Παρακαλώ δοκιμάστε με διαφορετικά στοιχεία.");
    }

    // 3. Hash password and generate confirmation token
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $confirmation_token = bin2hex(random_bytes(32));

    // 4. Insert user record
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, confirm_token) 
                           VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password, $confirmation_token]);

    // -------- Send verification email --------
    try {
        $mail = getMailer();
        // Προς ποιον στέλνουμε
        $mail->addAddress($email, $username);

        // Build verification link using APP_URL from config
        $base_url = rtrim($APP_URL, '/');
        $verification_link = $base_url . "/auth/verify.php?token=" . $confirmation_token;

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
        // Log mail errors but keep registration success
        error_log("PHPMailer Error: " . $mailError->getMessage());
    }

    // Success response
    http_response_code(201); // Created
    echo json_encode(['status' => 'success', 'message' => 'Η εγγραφή σας ολοκληρώθηκε! Έχει σταλεί email επιβεβαίωσης.']);
} catch (PDOException $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
