<?php
header('Content-Type: application/json');

// Get the HTTP referrer URL to determine the language
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$isGreek = strpos($referer, '/gr/') !== false;

// Set messages based on language
$successMessage = $isGreek ? 'Ευχαριστούμε για την εγγραφή σας!' : 'Thank you for subscribing!';
$errorMessage = $isGreek ? 'Δεν ήταν δυνατή η εγγραφή. Παρακαλώ δοκιμάστε ξανά.' : 'Subscription failed. Please try again.';
$alreadySubscribedMessage = $isGreek ? 'Είστε ήδη εγγεγραμμένος!' : 'You are already subscribed!';
$invalidEmailMessage = $isGreek ? 'Εισάγετε μια έγκυρη διεύθυνση email.' : 'Please enter a valid email address.';

// Include the PDO database connection from config.php
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'newsletter_email', FILTER_VALIDATE_EMAIL);

    if ($email) {
        try {
            // Check if email already exists using PDO
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
                // Insert the new email into the database
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
?>