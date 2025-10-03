<?php
session_start();
header('Content-Type: application/json');

require_once 'config.php';

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Διαβάζουμε το περιεχόμενο του request body (που περιέχει το JSON)
    $json_data = file_get_contents('php://input');

    // Μετατρέπουμε το JSON σε PHP αντικείμενο
    $data = json_decode($json_data);

    // Ελέγχουμε αν η μετατροπή ήταν επιτυχής
    if ($data === null) {
        $response['success'] = false;
        $response['message'] = 'Invalid JSON received.';
        echo json_encode($response);
        exit;
    }

    // Αντιστοιχούμε τα δεδομένα σε μεταβλητές
    $name = $data->name ?? '';
    $surname = $data->surname ?? '';
    $email = $data->email ?? '';
    $phone = $data->phone ?? '';
    $category = $data->category ?? '';
    $subject = $data->subject ?? '';
    $message = $data->message ?? '';

    // Basic validation for required fields
    if (empty($name) || empty($surname) || empty($email) || empty($category) || empty($subject) || empty($message)) {
        $response['success'] = false;
        $response['message'] = 'All required fields are needed.';
        echo json_encode($response);
        exit;
    }

    try {
        // Use a prepared statement to prevent SQL injection
        $sql = "INSERT INTO contact_messages (name, surname, email, phone, category, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $surname, $email, $phone, $category, $subject, $message]);

        $response['success'] = true;
        echo json_encode($response);
        exit;

    } catch (\PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred. Please try again later.';
        error_log("Database error: " . $e->getMessage()); // Log the error for debugging
        echo json_encode($response);
        exit;
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
}
?>