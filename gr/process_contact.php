<?php
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $surname = $_POST['surname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $category = $_POST['category'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    // Basic validation for required fields
    if (empty($name) || empty($surname) || empty($email) || empty($category) || empty($subject) || empty($message)) {
        $_SESSION['contact_message'] = ['text' => 'All required fields are needed.', 'class' => 'error'];
        header('Location: ' . asset('contact.php'));
        exit;
    }

    try {
        // Use a prepared statement to prevent SQL injection
        $sql = "INSERT INTO contact_messages (name, surname, email, phone, category, subject, message) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $surname, $email, $phone, $category, $subject, $message]);

        $_SESSION['contact_message'] = ['text' => 'Thank you for your message! We will get back to you soon.', 'class' => 'success'];
        header('Location: ' . asset('contact.php'));
        exit;

    } catch (\PDOException $e) {
        $_SESSION['contact_message'] = ['text' => 'An error occurred. Please try again later.', 'class' => 'error'];
        header('Location: ' . asset('contact.php'));
        exit;
    }
} else {
    header('Location: ' . asset('contact.php'));
    exit;
}
?>