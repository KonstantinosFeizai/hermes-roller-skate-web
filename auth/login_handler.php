<?php
// login_handler.php
// Purpose: Authenticate user credentials and start a session.

session_start();
// Load DB connection ($pdo)
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/session_helper.php';
require_once __DIR__ . '/../includes/lang.php';
require_once PROJECT_ROOT . 'includes/rate_limiter.php';

header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.method_not_allowed')]);
    exit;
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? '';

try {
    // 1. Basic validation
    if (empty($username) || empty($password)) {
        throw new Exception(t('auth.errors.fill_all_fields'));
    }

    // 2. Rate limit check (5 αποτυχίες / 15 λεπτά per IP)
    if (isRateLimited($pdo, 'login')) {
        $retryAfter = getRateLimitRetryAfter($pdo, 'login');
        http_response_code(429);
        echo json_encode([
            'status'      => 'error',
            'message'     => sprintf(t('auth.errors.rate_limit_login'), ceil($retryAfter / 60)),
            'retry_after' => $retryAfter,
        ]);
        exit;
    }

    // 3. Look up user by username or email
    $stmt = $pdo->prepare("SELECT id, username, password, is_active, role, first_name, last_name, age, phone, region, accepted_terms_at FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user) {
        // 3. Verify password hash
        if (password_verify($password, $user['password'])) {

            if ($user['is_active'] == 0) {
                throw new Exception(t('auth.errors.account_not_active'));
            }

            // 4. Successful login: set session values
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            // 4b. Συνδέουμε τυχόν anonymous cookie consent με τον χρήστη
            linkConsentToUser($pdo, $user['id']);

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

            // 6. Έλεγχος αποδοχής όρων ← νέο
            $needs_terms = empty($user['accepted_terms_at']);

            // 7. Success response
            http_response_code(200);
            $response = [
                'status'               => 'success',
                'message'              => t('auth.errors.login_success'),
                'username'             => $user['username'],
                'profile_incomplete'   => $profile_incomplete,
                'needs_terms_acceptance' => $needs_terms,
            ];

            if ($profile_incomplete && !$needs_terms) {
                $response['redirect'] = asset('user/profile.php') . '#profile';
            }

            // Successful login: clear failed-attempt history
            resetRateLimit($pdo, 'login');
            echo json_encode($response);
        } else {
            // Password mismatch – count as failed attempt
            recordAttempt($pdo, 'login');
            throw new Exception(t('auth.errors.invalid_credentials'));
        }
    } else {
        // User not found – count as failed attempt
        recordAttempt($pdo, 'login');
        throw new Exception(t('auth.errors.invalid_credentials'));
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => t('auth.errors.db_error')]);
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
