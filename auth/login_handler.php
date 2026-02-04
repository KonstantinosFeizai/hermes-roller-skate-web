<?php
// login_handler.php
// Purpose: Authenticate user credentials and start a session.

session_start();
// Load DB connection ($pdo)
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Μη επιτρεπτή μέθοδος αιτήματος.']);
    exit;
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

try {
    // 1. Basic validation
    if (empty($username) || empty($password)) {
        throw new Exception("Παρακαλώ συμπληρώστε όλα τα πεδία.");
    }

    // 2. Look up user by username or email
    $stmt = $pdo->prepare("SELECT id, username, password, is_active, role, first_name, last_name, age, phone, region FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user) {
        // 3. Verify password hash
        if (password_verify($password, $user['password'])) {

            if ($user['is_active'] == 0) {
                // Account exists but is not activated
                throw new Exception("Ο λογαριασμός σας δεν έχει ενεργοποιηθεί. Ελέγξτε το email σας για τον σύνδεσμο επιβεβαίωσης.");
            }

            // 4. Successful login: set session values
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            // 5. Check profile completeness for regular users
            $profile_incomplete = false;
            if ($user['role'] === 'user') {
                $profile_incomplete =
                    trim($user['first_name'] ?? '') === '' ||
                    trim($user['last_name'] ?? '') === '' ||
                    empty($user['age']) ||
                    trim($user['phone'] ?? '') === '' ||
                    trim($user['region'] ?? '') === '';
            }

            // 6. Success response
            http_response_code(200); // OK
            $response = [
                'status' => 'success',
                'message' => 'Επιτυχής σύνδεση.',
                'username' => $user['username'],
                'profile_incomplete' => $profile_incomplete
            ];

            if ($profile_incomplete) {
                $response['redirect'] = asset('user/profile.php') . '#profile';
            }

            echo json_encode($response);
        } else {
            // Password mismatch
            throw new Exception("Λάθος όνομα χρήστη/email ή κωδικός πρόσβασης.");
        }
    } else {
        // User not found
        throw new Exception("Λάθος όνομα χρήστη/email ή κωδικός πρόσβασης.");
    }
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων. Παρακαλώ δοκιμάστε αργότερα.']);
} catch (Exception $e) {
    // Validation or login error
    http_response_code(401); // Unauthorized (Μη εξουσιοδοτημένος)
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
