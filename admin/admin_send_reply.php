<?php
// admin_send_reply.php
// Purpose: Send a reply to a contact message and store it.
session_start();
header('Content-Type: application/json');

require_once  __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php'; // getMailer()

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'));
    $id = $data->id ?? null;
    $replyText = $data->replyText ?? '';
    $recipientEmail = $data->recipientEmail ?? '';

    if (!$id || empty($replyText) || empty($recipientEmail)) {
        echo json_encode(['success' => false, 'message' => 'Συμπληρώστε όλα τα πεδία.']);
        exit;
    }

    try {
        // 1. Send reply email via PHPMailer
        $mail = getMailer();
        $mail->addAddress($recipientEmail);
        $mail->isHTML(true);
        $mail->Subject = "Απάντηση στο μήνυμά σας - Hermes Roller Skate";

        // Email body (HTML)
        $mail->Body = "<h3>Γεια σας,</h3><p>" . nl2br(htmlspecialchars($replyText)) . "</p><br><hr><p>Hermes Roller Skate Academy</p>";

        $mail->send();

        // 2. Update DB with reply
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_replied = 1, reply_content = ?, replied_at = NOW() WHERE id = ?");
        $stmt->execute([$replyText, $id]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Σφάλμα Mailer: ' . $e->getMessage()]);
    }
}
