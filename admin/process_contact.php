<?php
// process_contact.php
// Purpose: Store contact form submissions.
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

        $response['success'] = true;
        echo json_encode($response);
        exit;
    } catch (\PDOException $e) {
        $response['success'] = false;
        $response['message'] = 'An error occurred. Please try again later.';
        // Log the error for debugging
        error_log("Database error: " . $e->getMessage());
        echo json_encode($response);
        exit;
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
    echo json_encode($response);
}
