<?php
// personal_info_handler.php
// Purpose: API endpoint to update a logged-in user's personal information.

// Core config
require_once __DIR__ . '/../config.php';
// Start session
session_start();

// Return JSON responses
header('Content-Type: application/json');

// Require authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
    exit;
}

// Read request payload
$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];

// Sanitize and normalize input
$first_name = trim($data['first_name'] ?? '');
$last_name  = trim($data['last_name'] ?? '');
$age        = !empty($data['age']) ? intval($data['age']) : null;
$phone      = trim($data['phone'] ?? '');
$region     = trim($data['region'] ?? '');

try {
    // Update user record in the database
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, age = ?, phone = ?, region = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $age, $phone, $region, $user_id]);

    // Success response
    echo json_encode(['status' => 'success', 'message' => 'Τα προσωπικά στοιχεία ενημερώθηκαν επιτυχώς!']);
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων κατά την αποθήκευση.']);
}
