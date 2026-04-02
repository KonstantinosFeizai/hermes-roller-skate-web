<?php
// admin_send_reply.php
// Purpose: Send a reply to a contact message and store it.
session_start();
header('Content-Type: application/json');

// Catch fatal errors and return JSON instead of an empty response
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err !== null) {
        $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
        if (in_array($err['type'], $fatal_types, true)) {
            error_log('admin_send_reply shutdown fatal: ' . print_r($err, true));
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: application/json');
            }
            echo json_encode(['success' => false, 'message' => 'Server encountered a fatal error.']);
        }
    }
});

// Error/logging settings (log errors so client can get JSON responses)
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

require_once  __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php'; // getMailer()

// Ensure logs directory exists
@mkdir(__DIR__ . '/../logs', 0755, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("admin_send_reply: invalid JSON input: " . json_last_error_msg() . " -- raw: " . $raw);
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Μη έγκυρα δεδομένα εισόδου.']);
            exit;
        }

        $id = $data->id ?? null;
        $replyText = $data->replyText ?? '';
        $recipientEmail = $data->recipientEmail ?? '';

        if (!$id || empty($replyText) || empty($recipientEmail)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Συμπληρώστε όλα τα πεδία.']);
            exit;
        }

        // 1. Send reply email via PHPMailer
        $mail = getMailer();
        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = "Απάντηση στο μήνυμά σας - Hermes Roller Skate";

        // Email body (HTML)
        $mail->Body = "<h3>Γεια σας,</h3><p>" . nl2br(htmlspecialchars($replyText)) . "</p><br><hr><p>Hermes Roller Skate Academy</p>";

        $mail->send();

        // 2. Update DB with reply
        if (!isset($pdo)) {
            throw new Exception('Database connection ($pdo) not available.');
        }

        $stmt = $pdo->prepare("UPDATE contact_messages SET is_replied = 1, reply_content = ?, replied_at = NOW() WHERE id = ?");
        $stmt->execute([$replyText, $id]);

        echo json_encode(['success' => true]);
        exit;
    } catch (Throwable $e) {
        // Log the error server-side for debugging
        error_log('admin_send_reply error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
        exit;
    }
}
