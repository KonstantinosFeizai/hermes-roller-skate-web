<?php
// verify.php
// Purpose: Verify account using confirmation token and activate user.

// Load DB connection and start session
require_once  __DIR__ . '/../config.php';
session_start();

// Redirect destination after verification
$redirect_page = asset('/');

// 1. Read token from URL (GET)
$token = $_GET['token'] ?? '';

// Validate token format (64 hex chars)
if (empty($token) || strlen($token) !== 64) {
    $_SESSION['alert_message'] = "Σφάλμα: Άκυρος σύνδεσμος επιβεβαίωσης.";
    $_SESSION['alert_type'] = "error";
    header("Location: " . $redirect_page);
    exit;
}

try {
    // 2. Find inactive user by token (prevents re-activation)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE confirm_token = ? AND is_active = 0");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // 3. Activate user and clear token
        $updateStmt = $pdo->prepare("UPDATE users SET is_active = 1, confirm_token = NULL WHERE id = ?");
        $updateStmt->execute([$user['id']]);

        // 4. Success message
        $_SESSION['alert_message'] = "Ο λογαριασμός σας ενεργοποιήθηκε με επιτυχία! Μπορείτε να συνδεθείτε.";
        $_SESSION['alert_type'] = "success";

        // Redirect to home
        header("Location: " . $redirect_page);
        exit;
    } else {
        // Token invalid/expired or account already active
        $_SESSION['alert_message'] = "Σφάλμα: Ο σύνδεσμος επιβεβαίωσης είναι άκυρος ή ο λογαριασμός είναι ήδη ενεργός.";
        $_SESSION['alert_type'] = "warning";
        header("Location: " . $redirect_page);
        exit;
    }
} catch (PDOException $e) {
    // Database error
    error_log("Verification Error: " . $e->getMessage());
    $_SESSION['alert_message'] = "Σφάλμα διακομιστή κατά την ενεργοποίηση.";
    $_SESSION['alert_type'] = "error";
    header("Location: " . $redirect_page);
    exit;
}
