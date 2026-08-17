<?php
// api/send_reply.php
// Purpose: Στέλνει ένα reply σε ένα private thread (message_id, recipient_id).
//          Καλείται είτε από τον recipient (χρήστη) είτε από τον admin.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');

$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

$data         = json_decode(file_get_contents('php://input'), true);
$message_id   = (int)($data['message_id']   ?? 0);
$recipient_id = (int)($data['recipient_id'] ?? 0);
$body         = trim($data['body'] ?? '');

if (!$message_id || !$recipient_id || $body === '') {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπουν απαραίτητα στοιχεία.']));
}

$is_admin_user = ($_SESSION['user_role'] ?? '') === 'admin';

try {
    // ── Έλεγχος εξουσιοδότησης ────────────────────────────────
    // Επιτρέπεται μόνο: (α) ο ίδιος ο recipient γράφει στο δικό του thread,
    //                   ή (β) ένας admin γράφει σε οποιοδήποτε thread.
    if (!$is_admin_user && $logged_in_id !== $recipient_id) {
        http_response_code(403);
        exit(json_encode(['status' => 'error', 'message' => 'Δεν έχετε πρόσβαση σε αυτό το thread.']));
    }

    // Επιβεβαιώνουμε ότι ο recipient όντως ανήκει σε αυτό το message
    $check = $pdo->prepare("
        SELECT 1 FROM admin_message_recipients 
        WHERE message_id = ? AND user_id = ?
    ");
    $check->execute([$message_id, $recipient_id]);
    if (!$check->fetch()) {
        http_response_code(404);
        exit(json_encode(['status' => 'error', 'message' => 'Το thread δεν βρέθηκε.']));
    }

    // ── INSERT reply ──────────────────────────────────────────
    $stmt = $pdo->prepare("
        INSERT INTO message_replies (message_id, recipient_id, sender_id, is_from_admin, body)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $message_id,
        $recipient_id,
        $logged_in_id,
        $is_admin_user ? 1 : 0,
        $body,
    ]);
    $reply_id = (int)$pdo->lastInsertId();

    // ── Notification στον "άλλο" συμμετέχοντα ────────────────
    if ($is_admin_user) {
        // Ο admin απάντησε → ειδοποίηση στον recipient
        createTranslatedNotification(
            $pdo,
            $recipient_id,
            'message_reply',
            [],
            $message_id,
            'admin_messages'
        );
    }
    // Σημείωση: αν θες ειδοποίηση admin όταν απαντά ο χρήστης,
    // θα χρειαστεί ξεχωριστή λογική (π.χ. admin_notifications table ή email)

    echo json_encode([
        'status'   => 'success',
        'reply_id' => $reply_id,
    ]);
} catch (PDOException $e) {
    error_log('send_reply error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
