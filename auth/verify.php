<?php
// verify.php
// Purpose: Verify account using confirmation token and activate user.

// Load DB connection and start session
require_once __DIR__ . '/../config.php';
session_start();

// Redirect destination after verification
$redirect_page = asset('/');

// 1. Read token from URL (GET)
$token = $_GET['token'] ?? '';

// Validate token format (64 hex chars)
if (empty($token) || strlen($token) !== 64) {
    $_SESSION['alert_message'] = 'auth.verify.invalid_link';
    $_SESSION['alert_type'] = "error";
    header("Location: " . $redirect_page);
    exit;
}

try {
    // 2. Αναζήτηση χρήστη βάσει του token (ανεξάρτητα αν είναι ενεργός ή όχι, για να βρούμε τη γλώσσα του)
    $stmt = $pdo->prepare("SELECT id, is_active, lang FROM users WHERE confirm_token = ? OR confirm_token IS NULL AND id IN (SELECT id FROM users WHERE confirm_token = ?)");
    // Απλούστερη και πιο ακριβής αναζήτηση μόνο με το token:
    $stmt = $pdo->prepare("SELECT id, is_active, lang FROM users WHERE confirm_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // ✨ Ορίζουμε τη γλώσσα της συνεδρίας (session) στη γλώσσα που είχε επιλέξει ο χρήστης κατά την εγγραφή!
        if (!empty($user['lang'])) {
            $_SESSION['lang'] = $user['lang'];
        }

        if ($user['is_active'] == 0) {
            // 3. Ενεργοποίηση χρήστη και διαγραφή του token
            $updateStmt = $pdo->prepare("UPDATE users SET is_active = 1, confirm_token = NULL WHERE id = ?");
            $updateStmt->execute([$user['id']]);

            // Success message
            $_SESSION['alert_message'] = 'auth.verify.success';
            $_SESSION['alert_type'] = "success";
        } else {
            // Ο λογαριασμός είναι ήδη ενεργός
            $_SESSION['alert_message'] = 'auth.verify.already_active_or_invalid';
            $_SESSION['alert_type'] = "warning";
        }

        header("Location: " . $redirect_page);
        exit;
    } else {
        // Τo token δεν βρέθηκε καθόλου (άκυρος σύνδεσμος ή έχει ήδη ενεργοποιηθεί και μηδενιστεί το token)
        $_SESSION['alert_message'] = 'auth.verify.already_active_or_invalid';
        $_SESSION['alert_type'] = "warning";
        header("Location: " . $redirect_page);
        exit;
    }
} catch (PDOException $e) {
    // Database error
    error_log("Verification Error: " . $e->getMessage());
    $_SESSION['alert_message'] = 'auth.verify.server_error';
    $_SESSION['alert_type'] = "error";
    header("Location: " . $redirect_page);
    exit;
}
