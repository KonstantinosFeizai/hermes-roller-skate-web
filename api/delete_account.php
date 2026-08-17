<?php
// api/delete_account.php
// Purpose: Hard delete του λογαριασμού χρήστη.
//          1. Επαλήθευση κωδικού
//          2. Διαγραφή PDF αποδείξεων από filesystem
//          3. DELETE users → cascade όλα τα υπόλοιπα
//          4. Unsubscribe από newsletter
//          5. session_destroy

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// ── Auth ─────────────────────────────────────────────────────
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

$data     = json_decode(file_get_contents('php://input'), true);
$password = $data['password'] ?? '';

if (empty($password)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Παρακαλώ εισάγετε τον κωδικό σας.']));
}

try {
    // ── 1. Επαλήθευση κωδικού ────────────────────────────────
    $stmt = $pdo->prepare("SELECT password, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        http_response_code(401);
        exit(json_encode(['status' => 'error', 'message' => 'Λάθος κωδικός πρόσβασης.']));
    }

    // ── 2. Βρίσκουμε PDF αποδείξεις για διαγραφή ─────────────
    // (μέσω athletes του user)
    $stmtReceipts = $pdo->prepare("
        SELECT p.receipt_file_path
        FROM payments p
        JOIN athletes a ON p.athlete_id = a.id
        WHERE a.user_id = ?
          AND p.receipt_file_path IS NOT NULL
    ");
    $stmtReceipts->execute([$user_id]);
    $receiptFiles = $stmtReceipts->fetchAll(PDO::FETCH_COLUMN);

    // ── 3. DELETE user → cascade τα πάντα ────────────────────
    // Cascade: athletes, lesson_athletes, payments, cookie_consents,
    //          admin_message_recipients, message_replies, notifications
    $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$user_id]);

    // ── 4. Newsletter unsubscribe ─────────────────────────────
    $pdo->prepare("
        UPDATE newsletter_subscribers SET is_active = 0 WHERE email = ?
    ")->execute([$user['email']]);

    // ── 5. Διαγραφή PDF αρχείων από filesystem ───────────────
    $receiptsDir = PROJECT_ROOT . 'assets/uploads/receipts/';
    foreach ($receiptFiles as $filename) {
        $path = $receiptsDir . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // ── 6. Session destroy ────────────────────────────────────
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    session_destroy();

    echo json_encode([
        'status'   => 'success',
        'redirect' => asset('index'),
    ]);
} catch (PDOException $e) {
    error_log('delete_account error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων. Παρακαλώ δοκιμάστε ξανά.']);
}
