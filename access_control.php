<?php
// access_control.php
// Purpose: Central access gate to protect pages based on authentication and role.

require_once __DIR__ . '/config.php';

/**
 * Checks if the user is logged in and has the required role.
 * If not, redirects the user.
 *
 * @param string|array $required_roles Required role(s) (e.g. 'admin' or ['admin', 'moderator']).
 * @param string $redirect_page Redirect target (e.g. 'index.php' or 'login.php').
 */
function restrict_access($required_roles, $redirect_page = 'index.php')
{
    // 1. Start session (if not already started)
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $current_role = $_SESSION['user_role'] ?? null;

    // Normalize required roles to an array
    if (!is_array($required_roles)) {
        $required_roles = [$required_roles];
    }

    // 2. Check if user is logged in
    if (!$current_role) {
        // Not logged in -> redirect
        header("Location: " . $redirect_page);
        exit;
    }

    // 3. Check if user's role is allowed
    if (!in_array($current_role, $required_roles)) {
        // Not allowed -> redirect with error message
        $_SESSION['alert_message'] = "Δεν έχετε δικαίωμα πρόσβασης σε αυτήν τη σελίδα.";
        $_SESSION['alert_type'] = "error";
        header("Location: " . $redirect_page);
        exit;
    }

    // 4. Force profile completion for regular users
    if ($current_role === 'user') {
        $current_page = basename($_SERVER['PHP_SELF']);

        if ($current_page !== 'profile.php') {
            $user_id = $_SESSION['user_id'] ?? null;

            if ($user_id) {
                global $pdo;
                $stmt = $pdo->prepare("SELECT first_name, last_name, age, phone, region FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $profile = $stmt->fetch();

                $is_incomplete = !$profile
                    || trim($profile['first_name'] ?? '') === ''
                    || trim($profile['last_name'] ?? '') === ''
                    || empty($profile['age'])
                    || trim($profile['phone'] ?? '') === ''
                    || trim($profile['region'] ?? '') === '';

                if ($is_incomplete) {
                    header("Location: " . asset('user/profile.php') . '#profile');
                    exit;
                }
            }
        }
    }

    // If all checks pass, execution continues
}
