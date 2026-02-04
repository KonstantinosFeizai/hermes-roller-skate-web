<?php
// reset_password.php
// Purpose: Render reset form after validating the reset token.

// Core config + session + header
require_once  __DIR__ . '/../config.php';
session_start();
require_once  PROJECT_ROOT . 'partials/header.php';



$raw_token = $_GET['token'] ?? '';
$token_hash = hash('sha256', $raw_token);
$error_message = '';
$user_id = null;

try {
    // Token validation
    if (empty($raw_token)) {
        throw new Exception(t('reset_password.errors.invalid_link'));
    }
    $stmt = $pdo->prepare("SELECT id, reset_token_expires_at FROM users WHERE reset_token_hash = ?");
    $stmt->execute([$token_hash]);
    $user = $stmt->fetch();
    if (!$user) {
        throw new Exception(t('reset_password.errors.used_link'));
    }
    // Check expiration
    $expires_at = new DateTime($user['reset_token_expires_at']);
    $now = new DateTime();
    if ($expires_at < $now) {
        throw new Exception(t('reset_password.errors.expired_link'));
    }
    $user_id = $user['id'];
} catch (PDOException $e) {
    $error_message = t('reset_password.errors.db_error');
    error_log("Reset Password DB Error: " . $e->getMessage());
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>

<style>
    :root {
        --brand: #2563eb;
        --brand-dark: #1d4ed8;
        --text: #0f172a;
        --muted: #64748b;
        --border: #e2e8f0;
        --bg: #f8fafc;
        --card: #ffffff;
        --ring: rgba(37, 99, 235, 0.35);
        --success: #16a34a;
        --danger: #dc2626;
        --warning: #d97706;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: radial-gradient(1200px 600px at 10% -10%, #e0f2fe 0%, transparent 60%),
            radial-gradient(1000px 500px at 110% 10%, #dbeafe 0%, transparent 60%),
            var(--bg);
    }

    .main-content-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(24px, 4vw, 56px) 16px;
    }

    .form-card {
        width: min(520px, 100%);
        background: var(--card);
        padding: clamp(20px, 4vw, 36px);
        border-radius: 16px;
        border: 1px solid var(--border);
        box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
        backdrop-filter: blur(6px);
        text-align: center;
    }

    h2 {
        text-align: center;
        color: var(--text);
        margin: 0 0 16px 0;
        font-size: clamp(22px, 2.4vw, 28px);
        letter-spacing: -0.3px;
    }

    .form-group {
        margin-bottom: 18px;
        text-align: left;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }

    input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        color: var(--text);
        font-size: 15px;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.05s;
    }

    input[type="password"]:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px var(--ring);
    }

    button[type="submit"] {
        width: 100%;
        padding: 12px 16px;
        background: linear-gradient(135deg, var(--brand), #3b82f6);
        color: #fff;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: transform 0.06s ease, box-shadow 0.2s ease, background 0.2s ease;
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.25);
        margin-top: 6px;
    }

    button[type="submit"]:hover {
        background: linear-gradient(135deg, var(--brand-dark), #2563eb);
        box-shadow: 0 10px 18px rgba(37, 99, 235, 0.3);
    }

    button[type="submit"]:active {
        transform: translateY(1px);
    }

    .alert {
        padding: 12px 14px;
        margin: 0 auto 16px auto;
        border: 1px solid transparent;
        border-radius: 12px;
        text-align: center;
        font-size: 14px;
    }

    .alert-error {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
    }

    .alert a {
        color: var(--brand-dark);
        text-decoration: none;
        font-weight: 600;
    }

    .alert a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .main-content-container {
            padding-top: 32px;
        }

        .form-card {
            border-radius: 14px;
        }
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 18px;
        }

        button[type="submit"] {
            font-size: 14px;
        }
    }
</style>

<div class="main-content-container">
    <div class="form-card">
        <h2><?= t('reset_password.title') ?></h2>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
                <p style="margin-top: 10px;"><a href="forgot_password.php"><?= t('reset_password.restart_link') ?></a></p>
            </div>
        <?php else: ?>
            <form id="resetPasswordForm">

                <input type="hidden" name="token" value="<?php echo htmlspecialchars($raw_token); ?>">

                <div class="form-group">
                    <label for="password"><?= t('reset_password.new_password') ?></label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?= t('reset_password.confirm_password') ?></label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>

                <div id="resetPasswordGeneralError" style="color: red; margin-top: 10px; text-align: center; display: none;"></div>

                <button type="submit"><?= t('reset_password.submit') ?></button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="/js/auth.js"></script>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>