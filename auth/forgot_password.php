<?php
// forgot_password.php
// Purpose: Password recovery request form.
session_start();
require_once  __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'partials/header.php';

// Alert messages handling from session
$alert_message = $_SESSION['alert_message'] ?? null;
$alert_type = $_SESSION['alert_type'] ?? null;
unset($_SESSION['alert_message']);
unset($_SESSION['alert_type']);
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
    }

    h2 {
        text-align: center;
        color: var(--text);
        margin: 0 0 8px 0;
        font-size: clamp(22px, 2.4vw, 28px);
        letter-spacing: -0.3px;
    }

    .form-card p {
        text-align: center;
        color: var(--muted);
        margin: 0 0 24px 0;
        font-size: clamp(14px, 1.6vw, 16px);
    }

    .form-group {
        margin-bottom: 18px;
    }

    label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: var(--text);
        font-size: 14px;
    }

    input[type="email"] {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: #fff;
        color: var(--text);
        font-size: 15px;
        transition: border-color 0.2s, box-shadow 0.2s, transform 0.05s;
    }

    input[type="email"]:focus {
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

    .alert-success {
        color: #0f5132;
        background-color: #d1e7dd;
        border-color: #badbcc;
    }

    .alert-error,
    .alert-warning,
    .alert-info {
        color: #842029;
        background-color: #f8d7da;
        border-color: #f5c2c7;
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

        <?php
        // Display alert message if it exists
        if ($alert_message): ?>
            <div class="alert alert-<?php echo htmlspecialchars($alert_type); ?>">
                <?php echo htmlspecialchars($alert_message); ?>
            </div>
        <?php endif; ?>

        <h2><?= t('forgot_password.title') ?></h2>
        <p><?= t('forgot_password.subtitle') ?></p>

        <form id="forgotPasswordForm" action="forgot_password_handler.php" method="POST">
            <div class="form-group">
                <label for="email"><?= t('forgot_password.email_label') ?></label>
                <input type="email" id="email" name="email" required>
            </div>

            <div id="forgotPasswordGeneralError" style="color: red; margin-top: 10px; margin-bottom: 10px; text-align: center; display: none;"></div>

            <button type="submit"><?= t('forgot_password.submit') ?></button>
        </form>

    </div>
</div>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>