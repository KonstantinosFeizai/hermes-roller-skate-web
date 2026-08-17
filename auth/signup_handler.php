<?php
// signup_handler.php
// Purpose: Create a new account and send a verification email.
//          Αν το email/username υπάρχει αλλά δεν έχει επιβεβαιωθεί → ανανέωση + νέο email.

session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'email_config.php';
require_once PROJECT_ROOT . 'includes/rate_limiter.php';
require_once PROJECT_ROOT . 'includes/lang.php';

use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.method_not_allowed')]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$username         = trim($data['username'] ?? '');
$email            = trim($data['email'] ?? '');
$password         = $data['password'] ?? '';
$confirm_password = $data['confirm_password'] ?? '';
$accepted_terms   = !empty($data['accepted_terms']);

$supportedLangs = ['en', 'el'];
$requestedLang  = strtolower(trim((string)($data['lang'] ?? '')));
$sessionLang    = strtolower((string)($_SESSION['lang'] ?? ($GLOBALS['currentLang'] ?? 'en')));

$user_lang = in_array($requestedLang, $supportedLangs, true)
    ? $requestedLang
    : (in_array($sessionLang, $supportedLangs, true) ? $sessionLang : 'en');

$_SESSION['lang'] = $user_lang;
$GLOBALS['currentLang'] = $user_lang;
$langFile = PROJECT_ROOT . 'lang/' . $user_lang . '.php';
if (file_exists($langFile)) {
    $GLOBALS['translations'] = require $langFile;
}

try {
    // ── 1. Basic validation ───────────────────────────────────
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        throw new Exception(t('auth.errors.fill_all_fields'));
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception(t('auth.errors.invalid_email'));
    }
    if ($password !== $confirm_password) {
        throw new Exception(t('auth.errors.passwords_mismatch'));
    }
    if (strlen($password) < 8) {
        throw new Exception(t('auth.errors.password_too_short'));
    }
    if (!$accepted_terms) {
        throw new Exception(t('auth.errors.terms_accept_required'));
    }

    // ── 2. Rate limit ─────────────────────────────────────────
    if (isRateLimited($pdo, 'signup')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'signup');
        http_response_code(429);
        echo json_encode([
            'status'      => 'error',
            'message'     => sprintf(t('auth.errors.rate_limit_signup'), (int) ceil($retryAfter / 60)),
            'retry_after' => $retryAfter,
        ]);
        exit;
    }
    recordAttempt($pdo, 'signup');

    // ── 3. Έλεγχος αν υπάρχει ήδη ο χρήστης ─────────────────
    // α) Έλεγχος για EMAIL
    $stmtEmail = $pdo->prepare("SELECT id, is_active FROM users WHERE email = ? LIMIT 1");
    $stmtEmail->execute([$email]);
    $existingEmail = $stmtEmail->fetch();

    // β) Έλεγχος για USERNAME
    $stmtUser = $pdo->prepare("SELECT id, is_active FROM users WHERE username = ? LIMIT 1");
    $stmtUser->execute([$username]);
    $existingUser = $stmtUser->fetch();

    // ── Α. Αν το EMAIL ανήκει σε ΕΝΕΡΓΟ χρήστη
    if ($existingEmail && $existingEmail['is_active'] == 1) {
        throw new Exception(t('auth.errors.email_exists') ?? 'Αυτό το email χρησιμοποιείται ήδη.');
    }

    // ── Β. Αν το USERNAME ανήκει σε ΕΝΕΡΓΟ χρήστη
    if ($existingUser && $existingUser['is_active'] == 1) {
        throw new Exception(t('auth.errors.username_exists') ?? 'Αυτό το όνομα χρήστη χρησιμοποιείται ήδη.');
    }

    // ── Γ. Αν το EMAIL ή το USERNAME ανήκουν σε ΔΙΑΦΟΡΕΤΙΚΟΥΣ ανενεργούς χρήστες
    if ($existingEmail && $existingUser && $existingEmail['id'] !== $existingUser['id']) {
        throw new Exception(t('auth.errors.user_exists') ?? 'Τα στοιχεία χρησιμοποιούνται από εκκρεμείς εγγραφές.');
    }

    // Κοινά δεδομένα για INSERT και UPDATE
    $hashed_password    = password_hash($password, PASSWORD_BCRYPT);
    $confirmation_token = bin2hex(random_bytes(32));
    $accepted_terms_at  = date("Y-m-d H:i:s");

    // Βρίσκουμε αν υπάρχει ανενεργός λογαριασμός (είτε από το email είτε από το username)
    $targetPending = $existingEmail ?: $existingUser;

    if ($targetPending) {
        // ── Ανανέωση ανενεργού λογαριασμού ────────────────
        $stmt = $pdo->prepare("
            UPDATE users 
            SET username          = ?,
                email             = ?,
                password          = ?,
                confirm_token     = ?,
                accepted_terms_at = ?,
                lang              = ?,
                created_at        = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $username,
            $email,
            $hashed_password,
            $confirmation_token,
            $accepted_terms_at,
            $user_lang,
            $targetPending['id'],
        ]);
    } else {
        // ── Νέος χρήστης → INSERT ────────────────────────────
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, confirm_token, accepted_terms_at, lang)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $hashed_password, $confirmation_token, $accepted_terms_at, $user_lang]);
    }

    // ── 4. Αποστολή verification email ───────────────────────
    try {
        $mail = getMailer();
        $mail->addAddress($email, $username);

        $verification_link = APP_URL . "/auth/verify.php?token=" . $confirmation_token;
        $safe_verification_link = htmlspecialchars($verification_link, ENT_QUOTES, 'UTF-8');
        $logo_cid = 'hermes_logo_cid';
        $logo_path = PROJECT_ROOT . 'photo/hermes_logo.png';
        $logo_src = 'cid:' . $logo_cid;
        $emailLangAttr = $user_lang === 'el' ? 'el' : 'en';

        $social_facebook = 'https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/';
        $social_instagram = 'https://www.instagram.com/hermes_rollerskate_academy/';
        $social_linkedin = 'https://www.linkedin.com/company/hermes-rollerskate';

        $instagram_cid = 'social_instagram_cid';
        $facebook_cid = 'social_facebook_cid';
        $linkedin_cid = 'social_linkedin_cid';

        $instagram_icon_path = PROJECT_ROOT . 'photo/insta.webp';
        $facebook_icon_path = PROJECT_ROOT . 'photo/fb.webp';
        $linkedin_icon_path = PROJECT_ROOT . 'photo/linkedin.webp';

        $safe_facebook = htmlspecialchars($social_facebook, ENT_QUOTES, 'UTF-8');
        $safe_instagram = htmlspecialchars($social_instagram, ENT_QUOTES, 'UTF-8');
        $safe_linkedin = htmlspecialchars($social_linkedin, ENT_QUOTES, 'UTF-8');

        $safeGreeting = htmlspecialchars(
            sprintf(t('auth.verification_email.greeting', 'Welcome, %s!'), $username),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeIntro = htmlspecialchars(
            t('auth.verification_email.intro', 'Thank you for signing up. Please verify your email address by clicking the button below:'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeCta = htmlspecialchars(
            t('auth.verification_email.cta', 'Verify Account'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeIgnore = htmlspecialchars(
            t('auth.verification_email.ignore', 'If you did not create this account, you can safely ignore this email.'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeFallback = htmlspecialchars(
            t('auth.verification_email.fallback', 'If the button does not work, copy and paste this URL into your browser:'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeFooter = htmlspecialchars(
            sprintf(t('auth.verification_email.footer_rights', '© %d Hermes Roller Skate. All rights reserved.'), (int) date('Y')),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeLogoAlt = htmlspecialchars(
            t('auth.verification_email.logo_alt', 'Hermes Roller Skate Logo'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeFollowUs = htmlspecialchars(
            t('auth.verification_email.follow_us', 'Follow us'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeInstagramLabel = htmlspecialchars(
            t('auth.verification_email.social_instagram', 'Instagram'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeFacebookLabel = htmlspecialchars(
            t('auth.verification_email.social_facebook', 'Facebook'),
            ENT_QUOTES,
            'UTF-8'
        );
        $safeLinkedinLabel = htmlspecialchars(
            t('auth.verification_email.social_linkedin', 'LinkedIn'),
            ENT_QUOTES,
            'UTF-8'
        );

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = t('auth.verification_email.subject', 'Account Verification - Hermes Roller Skate');

        // Embed logo so it renders in hosted inbox previews (e.g. Mailtrap) and real clients.
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

        $instagramIconTag = is_file($instagram_icon_path)
            ? '<img src="cid:' . $instagram_cid . '" alt="' . $safeInstagramLabel . '" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
            : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#E1306C; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">IG</span>';

        $facebookIconTag = is_file($facebook_icon_path)
            ? '<img src="cid:' . $facebook_cid . '" alt="' . $safeFacebookLabel . '" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
            : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#1877F2; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">FB</span>';

        $linkedinIconTag = is_file($linkedin_icon_path)
            ? '<img src="cid:' . $linkedin_cid . '" alt="' . $safeLinkedinLabel . '" width="34" height="34" style="display:block; width:34px; height:34px; border-radius:999px;" />'
            : '<span style="display:inline-block; width:34px; height:34px; line-height:34px; text-align:center; border-radius:999px; background:#0A66C2; color:#ffffff; font-size:11px; font-weight:700; text-decoration:none;">IN</span>';

        // Μοντέρνο & Καθαρό HTML Template
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="' . $emailLangAttr . '">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; background-color: #f4f6f8; font-family: \'Segoe UI\', Arial, sans-serif; -webkit-font-smoothing: antialiased;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
                <tr>
                    <td align="center" style="padding: 40px 15px;">
                        
                        <!-- Main Card -->
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 550px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); overflow: hidden;">
                            
                            <!-- Header / Brand -->
                            <tr>
                                <td align="center" style="padding: 28px 20px; background-color: #111827;">
                                    <img src="' . $logo_src . '" alt="' . $safeLogoAlt . '" width="78" style="display:block; margin: 0 auto 12px auto; width:78px; height:auto; border-radius: 12px;" />
                                    <h1 style="color: #ffffff; margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase;">
                                        Hermes Roller Skate
                                    </h1>
                                </td>
                            </tr>

                            <!-- Content Area -->
                            <tr>
                                <td style="padding: 35px 30px; text-align: left;">
                                    <h2 style="color: #111827; margin: 0 0 16px 0; font-size: 20px; font-weight: 600;">
                                        ' . $safeGreeting . ' 👋
                                    </h2>
                                    
                                    <p style="color: #4b5563; font-size: 15px; line-height: 1.6; margin: 0 0 25px 0;">
                                        ' . $safeIntro . '
                                    </p>

                                    <!-- CTA Button -->
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                        <tr>
                                            <td align="center" style="padding: 10px 0 25px 0;">
                                                <a href="' . $safe_verification_link . '" target="_blank" style="background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 15px; display: inline-block;">
                                                    ' . $safeCta . '
                                                </a>
                                            </td>
                                        </tr>
                                    </table>

                                    <p style="color: #6b7280; font-size: 14px; line-height: 1.5; margin: 0;">
                                        ' . $safeIgnore . '
                                    </p>
                                </td>
                            </tr>

                            <!-- Plain Link Fallback -->
                            <tr>
                                <td style="padding: 0 30px 25px 30px; border-top: 1px solid #f3f4f6;">
                                    <p style="color: #9ca3af; font-size: 12px; line-height: 1.4; word-break: break-all; margin-top: 20px;">
                                        ' . $safeFallback . '<br>
                                        <a href="' . $safe_verification_link . '" style="color: #2563eb; text-decoration: underline;">' . $safe_verification_link . '</a>
                                    </p>
                                    <p style="color: #6b7280; font-size: 13px; margin: 14px 0 10px 0; text-align:center;">
                                        ' . $safeFollowUs . '
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
                                    <p style="color: #9ca3af; font-size: 11px; line-height: 1.6; margin: 10px 0 0 0; text-align:center;">
                                        <a href="' . $safe_instagram . '" style="color:#6b7280; text-decoration:none;">' . $safeInstagramLabel . '</a> ·
                                        <a href="' . $safe_facebook . '" style="color:#6b7280; text-decoration:none;">' . $safeFacebookLabel . '</a> ·
                                        <a href="' . $safe_linkedin . '" style="color:#6b7280; text-decoration:none;">' . $safeLinkedinLabel . '</a>
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td align="center" style="padding: 20px 30px; background-color: #f9fafb; border-top: 1px solid #f3f4f6;">
                                    <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                                        ' . $safeFooter . '
                                    </p>
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>
            </table>
        </body>
        </html>';

        // Plain Text έκδοση για mail clients που δεν διαβάζουν HTML
        $mail->AltBody = sprintf(t('auth.verification_email.alt_greeting', 'Welcome, %s!'), $username) . "\n\n" .
            t('auth.verification_email.alt_intro', 'To activate your account, visit the link below:') . "\n" .
            $verification_link . "\n\n" .
            t('auth.verification_email.alt_ignore', 'If you did not request this sign up, ignore this email.') . "\n\n" .
            t('auth.verification_email.follow_us', 'Follow us') . ":\n" .
            t('auth.verification_email.social_instagram', 'Instagram') . ': ' . $social_instagram . "\n" .
            t('auth.verification_email.social_facebook', 'Facebook') . ': ' . $social_facebook . "\n" .
            t('auth.verification_email.social_linkedin', 'LinkedIn') . ': ' . $social_linkedin;

        $mail->send();
    } catch (Exception $mailError) {
        error_log("PHPMailer Error: " . $mailError->getMessage());
    }

    http_response_code(201);
    echo json_encode([
        'status'  => 'success',
        'message' => t('auth.errors.signup_success'),
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.db_error')]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
