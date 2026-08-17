<?php
// api/send_admin_message.php
// Purpose: Αποστολή μηνύματος από admin σε επιλεγμένους χρήστες με μορφοποιημένο HTML email.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php';
require_once PROJECT_ROOT . 'includes/recipient_helper.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';
require_once PROJECT_ROOT . 'includes/lang.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

$data       = json_decode(file_get_contents('php://input'), true);
$subject    = trim($data['subject']    ?? '');
$body       = trim($data['body']       ?? '');
$filters    = $data['filters']         ?? [];
$send_email = !empty($data['send_email']) ? 1 : 0;
$admin_id   = $_SESSION['user_id'];

if (empty($subject) || empty($body)) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Θέμα και μήνυμα είναι υποχρεωτικά.']));
}

// ── Χτίζουμε το query για τους παραλήπτες ────────────────────
try {
    $recipient_ids = buildRecipientList($pdo, $filters);

    if (empty($recipient_ids)) {
        exit(json_encode(['status' => 'error', 'message' => 'Δεν βρέθηκαν παραλήπτες με αυτά τα φίλτρα.']));
    }

    $pdo->beginTransaction();

    // ── INSERT στο admin_messages ─────────────────────────────
    $stmt = $pdo->prepare("
        INSERT INTO admin_messages (subject, body, filters, send_email, recipient_count, sent_by)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $subject,
        $body,
        json_encode($filters),
        $send_email,
        count($recipient_ids),
        $admin_id,
    ]);
    $message_id = (int)$pdo->lastInsertId();

    // ── INSERT recipients ─────────────────────────────────────
    $insertRecipient = $pdo->prepare("
        INSERT IGNORE INTO admin_message_recipients (message_id, user_id)
        VALUES (?, ?)
    ");
    foreach ($recipient_ids as $uid) {
        $insertRecipient->execute([$message_id, $uid]);
    }

    // ── Notifications ─────────────────────────────────────────
    foreach ($recipient_ids as $uid) {
        createTranslatedNotification(
            $pdo,
            (int)$uid,
            'new_message',
            [],
            $message_id,
            'admin_messages'
        );
    }

    $pdo->commit();

    // ── Αποστολή email (αν ζητήθηκε) ─────────────────────────
    $email_sent = 0;
    $email_failed = 0;

    if ($send_email) {
        // Φέρνουμε emails, ονόματα και γλώσσα των παραληπτών
        $placeholders = implode(',', array_fill(0, count($recipient_ids), '?'));
        $stmtEmails = $pdo->prepare("
            SELECT email, COALESCE(first_name, username) AS name, COALESCE(lang, 'el') AS lang
            FROM users
            WHERE id IN ($placeholders) AND email IS NOT NULL
        ");
        $stmtEmails->execute($recipient_ids);
        $emailRecipients = $stmtEmails->fetchAll();

        // Assets & Links
        $logo_path           = PROJECT_ROOT . 'photo/hermes_logo.png';
        $instagram_icon_path = PROJECT_ROOT . 'photo/insta.webp';
        $facebook_icon_path  = PROJECT_ROOT . 'photo/fb.webp';
        $linkedin_icon_path  = PROJECT_ROOT . 'photo/linkedin.webp';

        $social_facebook  = 'https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/';
        $social_instagram = 'https://www.instagram.com/hermes_rollerskate_academy/';
        $social_linkedin  = 'https://www.linkedin.com/company/hermes-rollerskate';

        $safe_facebook  = htmlspecialchars($social_facebook, ENT_QUOTES, 'UTF-8');
        $safe_instagram = htmlspecialchars($social_instagram, ENT_QUOTES, 'UTF-8');
        $safe_linkedin  = htmlspecialchars($social_linkedin, ENT_QUOTES, 'UTF-8');

        $safeSubject    = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $formattedBody  = nl2br(htmlspecialchars($body, ENT_QUOTES, 'UTF-8'));

        foreach ($emailRecipients as $recipient) {
            try {
                $userLang = ($recipient['lang'] === 'en') ? 'en' : 'el';

                // Κείμενα ανάλογα με τη γλώσσα χρήστη
                $greetingText = ($userLang === 'en')
                    ? "Hello, " . htmlspecialchars($recipient['name'], ENT_QUOTES, 'UTF-8') . "!"
                    : "Γεια σου, " . htmlspecialchars($recipient['name'], ENT_QUOTES, 'UTF-8') . "!";

                $followUsText = ($userLang === 'en') ? "Follow us" : "Ακολουθήστε μας";

                $mail = getMailer();
                $mail->addAddress($recipient['email'], $recipient['name']);
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = $subject;

                // Unique CIDs ανά παραλήπτη/mail instance
                $logo_cid      = 'hermes_logo_cid';
                $instagram_cid = 'social_instagram_cid';
                $facebook_cid  = 'social_facebook_cid';
                $linkedin_cid  = 'social_linkedin_cid';

                if (is_file($logo_path)) {
                    $mail->addEmbeddedImage($logo_path, $logo_cid, 'hermes_logo.png');
                }
                if (is_file($instagram_icon_path)) {
                    $mail->addEmbeddedImage($instagram_icon_path, $instagram_cid, 'insta.webp');
                }
                if (is_file($facebook_icon_path)) {
                    $mail->addEmbeddedImage($facebook_icon_path, $facebook_cid, 'fb.webp');
                }
                if (is_file($linkedin_icon_path)) {
                    $mail->addEmbeddedImage($linkedin_icon_path, $linkedin_cid, 'linkedin.webp');
                }

                $logo_src = 'cid:' . $logo_cid;

                $instagramIconTag = is_file($instagram_icon_path)
                    ? '<img src="cid:' . $instagram_cid . '" alt="Instagram" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
                    : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#E1306C; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">IG</span>';

                $facebookIconTag = is_file($facebook_icon_path)
                    ? '<img src="cid:' . $facebook_cid . '" alt="Facebook" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
                    : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#1877F2; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">FB</span>';

                $linkedinIconTag = is_file($linkedin_icon_path)
                    ? '<img src="cid:' . $linkedin_cid . '" alt="LinkedIn" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
                    : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#0A66C2; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">IN</span>';

                // HTML Template
                $mail->Body = '
                <!DOCTYPE html>
                <html lang="' . $userLang . '">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                </head>
                <body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: \'Segoe UI\', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                        <tr>
                            <td align="center" style="padding: 40px 15px;">
                                
                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 550px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
                                    
                                    <!-- Header / Brand -->
                                    <tr>
                                        <td align="center" style="padding: 28px 20px; background-color: #111827;">
                                            <img src="' . $logo_src . '" alt="Hermes Roller Skate Logo" width="78" style="display:block; margin: 0 auto 12px auto; width:78px; height:auto; border-radius: 12px;" />
                                            <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">
                                                Hermes Roller Skate
                                            </h1>
                                        </td>
                                    </tr>

                                    <!-- Content Area -->
                                    <tr>
                                        <td style="padding: 35px 30px; text-align: left;">
                                            <h2 style="color: #111827; margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">
                                                ' . $greetingText . ' 👋
                                            </h2>
                                            
                                            <div style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                                                ' . $formattedBody . '
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Social Links -->
                                    <tr>
                                        <td style="padding: 0 30px 25px 30px; border-top: 1px solid #f3f4f6;">
                                            <p style="color: #6b7280; font-size: 13px; margin: 20px 0 10px 0; text-align:center;">
                                                ' . $followUsText . '
                                            </p>
                                            <table border="0" cellpadding="0" cellspacing="0" role="presentation" align="center" style="margin:0 auto;">
                                                <tr>
                                                    <td style="padding-right: 8px;">
                                                        <a href="' . $safe_instagram . '" target="_blank" style="display:inline-block; text-decoration:none;">' . $instagramIconTag . '</a>
                                                    </td>
                                                    <td style="padding-right: 8px;">
                                                        <a href="' . $safe_facebook . '" target="_blank" style="display:inline-block; text-decoration:none;">' . $facebookIconTag . '</a>
                                                    </td>
                                                    <td>
                                                        <a href="' . $safe_linkedin . '" target="_blank" style="display:inline-block; text-decoration:none;">' . $linkedinIconTag . '</a>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>

                                    <!-- Footer -->
                                    <tr>
                                        <td align="center" style="padding: 20px 30px; background-color: #f9fafb; border-top: 1px solid #f3f4f6;">
                                            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                                © ' . date('Y') . ' Hermes Roller Skate. All rights reserved.
                                            </p>
                                        </td>
                                    </tr>

                                </table>

                            </td>
                        </tr>
                    </table>
                </body>
                </html>';

                $mail->AltBody = $greetingText . "\n\n" . $body . "\n\n" . $followUsText . ":\nInstagram: " . $social_instagram . "\nFacebook: " . $social_facebook . "\nLinkedIn: " . $social_linkedin;

                $mail->send();
                $email_sent++;
            } catch (Exception $e) {
                error_log('Admin message email error: ' . $e->getMessage());
                $email_failed++;
            }
        }
    }

    echo json_encode([
        'status'        => 'success',
        'message_id'    => $message_id,
        'recipients'    => count($recipient_ids),
        'email_sent'    => $email_sent,
        'email_failed'  => $email_failed,
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('send_admin_message error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
