<?php
// logout.php
// Purpose: Destroy the session and log the user out.

// 1. Start session
session_start();

header('Content-Type: application/json');

// 2. Clear session data
$_SESSION = array();

// 3. Destroy session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}
session_destroy();

// 4. Return success response (JSON)
http_response_code(200); // OK
echo json_encode(['status' => 'success', 'message' => 'Αποσύνδεση επιτυχής.']);

exit;
