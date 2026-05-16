<?php
// api/save_consent.php
// Purpose: Δέχεται POST request από το cookie-banner.js,
//          αποθηκεύει τη συναίνεση στο cookie και στο DB.

// ============================================================
// BOOTSTRAP
// ============================================================

// Session πρώτα
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'includes/cookie_helper.php';
require_once PROJECT_ROOT . 'includes/session_helper.php';

// ============================================================
// ΜΟΝΟ POST ΕΠΙΤΡΕΠΕΤΑΙ
// ============================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// ============================================================
// ΔΙΑΒΑΣΜΑ JSON BODY
// ============================================================

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
    exit;
}

// ============================================================
// VALIDATION
// ============================================================

// Επιτρεπόμενες τιμές: 0 ή 1
$allowedValues = [0, 1, true, false];

$analytics   = isset($data['analytics'])   ? (bool) $data['analytics']   : false;
$third_party = isset($data['third_party']) ? (bool) $data['third_party'] : false;

// ============================================================
// ΑΠΟΘΗΚΕΥΣΗ
// ============================================================

// 1. Αποθήκευση στο browser cookie
saveConsentCookie($analytics, $third_party);

// 2. Αποθήκευση στο DB
$sessionId = getAnonymousSessionId();
$userId    = $_SESSION['user_id'] ?? null;  // null αν είναι anonymous

try {
    saveConsentToDb($pdo, $analytics, $third_party, $sessionId, $userId);
} catch (PDOException $e) {
    // Καταγράφουμε το σφάλμα αλλά ΔΕΝ σπάμε την εμπειρία του χρήστη
    // σε production μπορείς να το στείλεις σε error log
    error_log('cookie_consents DB error: ' . $e->getMessage());
}

// ============================================================
// ΑΠΑΝΤΗΣΗ
// ============================================================

header('Content-Type: application/json');
echo json_encode([
    'success'     => true,
    'analytics'   => $analytics,
    'third_party' => $third_party,
    'version'     => COOKIE_CONSENT_VERSION,
]);
exit;
