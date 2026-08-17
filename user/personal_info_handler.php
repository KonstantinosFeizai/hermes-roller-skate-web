<?php
// personal_info_handler.php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'code' => 'METHOD_NOT_ALLOWED']);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'code' => 'UNAUTHORIZED']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];

$first_name  = trim($data['first_name'] ?? '');
$last_name   = trim($data['last_name'] ?? '');
$age         = !empty($data['age']) ? intval($data['age']) : null;
$phone       = trim($data['phone'] ?? '');
$region      = trim($data['region'] ?? '');
$location_id = !empty($data['location_id']) ? intval($data['location_id']) : null;

if ($region === '') {
    $region = null;
}

// 1. Validation: Απαιτούμενα πεδία (ΜΟΝΟ Όνομα, Επώνυμο, Τηλέφωνο)
if ($first_name === '' || $last_name === '' || $phone === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'REQUIRED_FIELDS_MISSING']);
    exit;
}

// 2. Validation: Έγκυρη Ηλικία 
if ($age !== null && ($age < 13 || $age > 120)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'INVALID_DATA']);
    exit;
}

// 3. Validation: Έγκυρος Αριθμός Τηλεφώνου
if (!preg_match('/^[0-9+\s\-]{7,20}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'INVALID_PHONE']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, age = ?, phone = ?, region = ?, location_id = ? WHERE id = ?");
    $stmt->execute([$first_name, $last_name, $age, $phone, $region, $location_id, $user_id]);

    echo json_encode(['status' => 'success', 'code' => 'SUCCESS']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => 'DB_ERROR']);
}
