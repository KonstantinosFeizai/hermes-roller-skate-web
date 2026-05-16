<?php
// newsletter-subscribe.php
// Purpose: Handle newsletter subscription (public endpoint).
header('Content-Type: application/json');

// Load DB connection and language helper
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/lang.php';
require_once PROJECT_ROOT . 'includes/rate_limiter.php';

// Localized response messages
$successMessage = t('newsletter.subscribe.success');
$errorMessage = t('newsletter.subscribe.error');
$alreadySubscribedMessage = t('newsletter.subscribe.already');
$invalidEmailMessage = t('newsletter.subscribe.invalid_email');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limit check (3 requests / 60 λεπτά per IP)
    if (isRateLimited($pdo, 'newsletter_subscribe')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'newsletter_subscribe');
        echo json_encode([
            'message'     => sprintf(t('newsletter.subscribe.rate_limit'), ceil($retryAfter / 60)),
            'class'       => 'error',
            'retry_after' => $retryAfter,
        ]);
        exit;
    }
    recordAttempt($pdo, 'newsletter_subscribe');

    // Validate email
    $email = filter_input(INPUT_POST, 'newsletter_email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        try {
            // Check if email already exists (active ή inactive)
            $stmt = $pdo->prepare("SELECT id, is_active FROM newsletter_subscribers WHERE email = ?");
            $stmt->execute([$email]);
            $existing = $stmt->fetch();

            if ($existing) {
                if ($existing['is_active'] == 1) {
                    // Ήδη εγγεγραμμένος και ενεργός
                    $response = [
                        'message' => $alreadySubscribedMessage,
                        'class'   => 'warning'
                    ];
                } else {
                    // Είχε κάνει unsubscribe → ξανά-ενεργοποιούμε
                    $token = bin2hex(random_bytes(32));
                    $stmt  = $pdo->prepare("
                        UPDATE newsletter_subscribers 
                        SET is_active = 1, unsubscribe_token = ?, subscribed_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$token, $existing['id']]);

                    $response = [
                        'message' => $successMessage,
                        'class'   => 'success'
                    ];
                }
            } else {
                // Νέος subscriber → παράγουμε token
                $token = bin2hex(random_bytes(32));

                $stmt = $pdo->prepare("
                    INSERT INTO newsletter_subscribers (email, is_active, unsubscribe_token, subscribed_at) 
                    VALUES (?, 1, ?, NOW())
                ");

                if ($stmt->execute([$email, $token])) {
                    $response = [
                        'message' => $successMessage,
                        'class'   => 'success'
                    ];
                } else {
                    $response = [
                        'message' => $errorMessage,
                        'class'   => 'error'
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $response = [
                'message' => $errorMessage,
                'class'   => 'error'
            ];
        }
    } else {
        $response = [
            'message' => $invalidEmailMessage,
            'class'   => 'error'
        ];
    }
} else {
    $response = [
        'message' => $errorMessage,
        'class'   => 'error'
    ];
}

echo json_encode($response);
