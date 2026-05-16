<?php
// process_contact.php
// Purpose: Store contact form submissions.
ob_start(); // Buffer output to prevent notices/warnings from corrupting JSON
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Email sending support
require_once PROJECT_ROOT . 'email_config.php'; // provides getMailer()
require_once PROJECT_ROOT . 'includes/rate_limiter.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Rate limit check (5 requests / 60 λεπτά per IP)
    if (isRateLimited($pdo, 'contact')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'contact');
        http_response_code(429);
        ob_end_clean();
        echo json_encode([
            'success'     => false,
            'message'     => 'Πολλές αιτήσεις επικοινωνίας. Δοκιμάστε ξανά σε ' . ceil($retryAfter / 60) . ' λεπτά.',
            'retry_after' => $retryAfter,
        ]);
        exit;
    }
    recordAttempt($pdo, 'contact');

    // Read raw JSON body
    $json_data = file_get_contents('php://input');

    // Decode JSON
    $data = json_decode($json_data);

    // Validate JSON decode
    if ($data === null) {
        $response['success'] = false;
        $response['message'] = 'Invalid JSON received.';
        echo json_encode($response);
        exit;
    }

    // Map fields
    $name = $data->name ?? '';
    $surname = $data->surname ?? '';
    $email = $data->email ?? '';
    $phone = $data->phone ?? '';
    $category = $data->category ?? '';
    $subject = $data->subject ?? '';
    $message = $data->message ?? '';

    $name = trim($name);
    $surname = trim($surname);
    $email = trim($email);
    $phone = trim($phone);
    $category = trim($category);
    $subject = trim($subject);
    $message = trim($message);

    // Basic validation for required fields
    if (empty($name) || empty($surname) || empty($email) || empty($category) || empty($subject) || empty($message)) {
        $response['success'] = false;
        $response['message'] = 'All required fields are needed.';
        echo json_encode($response);
        exit;
    }

    $nameLength = mb_strlen($name);
    $surnameLength = mb_strlen($surname);
    if ($nameLength < 2 || $nameLength > 50 || $surnameLength < 2 || $surnameLength > 50) {
        $response['success'] = false;
        $response['message'] = 'Name and surname must be 2–50 characters.';
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['success'] = false;
        $response['message'] = 'Invalid email format.';
        echo json_encode($response);
        exit;
    }

    if (!empty($phone)) {
        $phonePattern = '/^[+()\d\s.-]{7,20}$/';
        if (!preg_match($phonePattern, $phone)) {
            $response['success'] = false;
            $response['message'] = 'Invalid phone number.';
            echo json_encode($response);
            exit;
        }
    }

    try {
        // Use prepared statements to prevent SQL injection
        $sql = "INSERT INTO contact_messages (name, surname, email, phone, category, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $surname, $email, $phone, $category, $subject, $message]);

        // Try to send notification email (do not fail the request if mail fails)
        try {
            $mail = getMailer();
            // Send to configured recipient (Mailtrap will capture outgoing mails)
            $mail->addAddress(CONTACT_RECIPIENT_EMAIL);
            $mail->isHTML(true);
            $mail->Subject = "Νέο μήνυμα επικοινωνίας: " . ($subject ?: 'Χωρίς θέμα');

            $bodyHtml = "<h3>Νέο μήνυμα από τη φόρμα επικοινωνίας</h3>" .
                "<p><strong>Όνομα:</strong> " . htmlspecialchars($name) . " " . htmlspecialchars($surname) . "</p>" .
                "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>" .
                (!empty($phone) ? "<p><strong>Τηλέφωνο:</strong> " . htmlspecialchars($phone) . "</p>" : "") .
                "<p><strong>Κατηγορία:</strong> " . htmlspecialchars($category) . "</p>" .
                "<p><strong>Θέμα:</strong> " . htmlspecialchars($subject) . "</p>" .
                "<p><strong>Μήνυμα:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>";

            $mail->Body = $bodyHtml;
            $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $bodyHtml));

            $mail->send();
        } catch (\Exception $e) {
            error_log("Contact form mail error: " . $e->getMessage());
            // don't change response: keep success true so UX isn't broken
        }

        $response['success'] = true;
        ob_end_clean();
        echo json_encode($response);
        exit;
    } catch (\PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred. Please try again later.';
        error_log("Database error: " . $e->getMessage());
        ob_end_clean();
        echo json_encode($response);
        exit;
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
    ob_end_clean();
    echo json_encode($response);
}
