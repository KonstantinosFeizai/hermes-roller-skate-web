<?php
// newsletter-subscribe.php
// Purpose: Handle newsletter subscription (public endpoint).
header('Content-Type: application/json');

// Load DB connection and language helper
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/lang.php';

// Localized response messages
$successMessage = t('newsletter.subscribe.success');
$errorMessage = t('newsletter.subscribe.error');
$alreadySubscribedMessage = t('newsletter.subscribe.already');
$invalidEmailMessage = t('newsletter.subscribe.invalid_email');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate email
    $email = filter_input(INPUT_POST, 'newsletter_email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT email FROM `newsletter_subscribers` WHERE email = ?");
            $stmt->execute([$email]);
            $existingEmail = $stmt->fetchColumn();

            if ($existingEmail) {
                // Email already subscribed
                $response = [
                    'message' => $alreadySubscribedMessage,
                    'class' => 'warning'
                ];
            } else {
                // Insert new subscriber
                $insert_stmt = $pdo->prepare("INSERT INTO `newsletter_subscribers` (email, subscribed_at) VALUES (?, NOW())");

                if ($insert_stmt->execute([$email])) {
                    $response = [
                        'message' => $successMessage,
                        'class' => 'success'
                    ];
                } else {
                    $response = [
                        'message' => $errorMessage,
                        'class' => 'error'
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            $response = [
                'message' => $errorMessage,
                'class' => 'error'
            ];
        }
    } else {
        $response = [
            'message' => $invalidEmailMessage,
            'class' => 'error'
        ];
    }
} else {
    $response = [
        'message' => $errorMessage,
        'class' => 'error'
    ];
}

echo json_encode($response);
