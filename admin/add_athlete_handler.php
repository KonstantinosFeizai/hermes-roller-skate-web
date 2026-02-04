<?php
// add_athlete_handler.php
// Purpose: Admin-only endpoint to create a new athlete user.
require_once __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Μη εξουσιοδοτημένη ενέργεια.',
        'debug_info' => 'Session Role: ' . ($_SESSION['user_role'] ?? 'not set')
    ]);
    exit;
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

// Extract input fields
$first_name = trim($data['first_name'] ?? '');
$last_name  = trim($data['last_name'] ?? '');
$phone      = trim($data['phone'] ?? '');
$age        = !empty($data['age']) ? intval($data['age']) : null;
$region     = $data['region'] ?? '';

// Generate a temporary unique username
$temp_username = "off_" . time() . "_" . rand(100, 999);

// Normalize last name for virtual email
$safe_last_name = strtolower(trim($last_name));

// Create a virtual email: lastname + random digits + internal domain
// Ensures uniqueness even for same last names
$virtual_email = $safe_last_name . rand(1000, 9999) . "@internal.hermes";

try {
    $dummy_password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users 
        (username, email, password, first_name, last_name, phone, age, region, role, is_active) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'user', 1)");

    $stmt->execute([
        $temp_username,
        $virtual_email,   // Το νέο "έξυπνο" email
        $dummy_password,
        $first_name,
        $last_name,
        $phone,
        $age,
        $region
    ]);

    echo json_encode(['status' => 'success', 'message' => 'Ο αθλητής προστέθηκε επιτυχώς!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης: ' . $e->getMessage()]);
}
