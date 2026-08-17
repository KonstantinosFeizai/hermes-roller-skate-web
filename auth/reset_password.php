<?php
// reset_password.php
// Purpose: Render reset form after validating the reset token with enhanced UI/UX.

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
        --ring: rgba(37, 99, 235, 0.2);
        --success-bg: #f0fdf4;
        --success-border: #bbf7d0;
        --success-text: #166534;
        --error-bg: #fef2f2;
        --error-border: #fecaca;
        --error-text: #991b1b;
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
        min-height: calc(100vh - 120px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: clamp(24px, 4vw, 56px) 16px;
    }

    .form-card {
        width: min(460px, 100%);
        background: var(--card);
        padding: clamp(28px, 5vw, 40px);
        border-radius: 20px;
        border: 1px solid var(--border);
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-header-icon {
        width: 56px;
        height: 56px;
        background: #eff6ff;
        color: var(--brand);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 20px auto;
        box-shadow: inset 0 0 0 1px rgba(37, 99, 235, 0.1);
    }

    .form-header-icon.error-icon {
        background: var(--error-bg);
        color: var(--error-text);
        box-shadow: inset 0 0 0 1px rgba(220, 38, 38, 0.1);
    }

    h2 {
        text-align: center;
        color: var(--text);
        margin: 0 0 8px 0;
        font-size: clamp(22px, 2.4vw, 26px);
        font-weight: 700;
        letter-spacing: -0.4px;
    }

    .form-card p.subtitle {
        text-align: center;
        color: var(--muted);
        margin: 0 0 28px 0;
        font-size: 14px;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-wrapper .input-icon {
        position: absolute;
        left: 14px;
        color: var(--muted);
        font-size: 15px;
        transition: color 0.2s ease;
        pointer-events: none;
    }

    .input-wrapper .toggle-pwd {
        position: absolute;
        right: 14px;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        padding: 4px;
        transition: color 0.2s ease;
    }

    .input-wrapper .toggle-pwd:hover {
        color: var(--brand);
    }

    input[type="password"],
    input[type="text"].pwd-input {
        width: 100%;
        padding: 12px 42px 12px 42px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        color: var(--text);
        font-size: 15px;
        transition: all 0.2s ease;
    }

    input[type="password"]:focus,
    input[type="text"].pwd-input:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px var(--ring);
    }

    .input-wrapper:focus-within .input-icon {
        color: var(--brand);
    }

    button[type="submit"] {
        width: 100%;
        padding: 13px 16px;
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        font-size: 15px;
        font-weight: 600;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 8px;
    }

    button[type="submit"]:hover {
        background: var(--brand-dark);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        transform: translateY(-1px);
    }

    button[type="submit"]:active {
        transform: translateY(0);
    }

    button[type="submit"]:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .alert {
        padding: 12px 16px;
        margin-bottom: 20px;
        border-radius: 12px;
        font-size: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-align: center;
    }

    .alert-error {
        color: var(--error-text);
        background-color: var(--error-bg);
        border: 1px solid var(--error-border);
    }

    .alert a {
        color: var(--brand);
        text-decoration: none;
        font-weight: 600;
    }

    .alert a:hover {
        text-decoration: underline;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        margin-top: 22px;
        font-size: 14px;
        color: var(--muted);
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s ease;
    }

    .back-link:hover {
        color: var(--brand);
    }

    .back-link i {
        transition: transform 0.2s ease;
    }

    .back-link:hover i {
        transform: translateX(-4px);
    }

    /* Success Modal */
    #resetSuccessOverlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .success-modal {
        background: #fff;
        border-radius: 20px;
        padding: 36px 28px;
        width: min(400px, 100%);
        text-align: center;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        animation: modalScale 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes modalScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .success-modal-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: var(--success-bg);
        border: 1px solid var(--success-border);
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 20px;
    }

    .success-modal h3 {
        margin: 0 0 8px;
        font-size: 22px;
        font-weight: 700;
        color: var(--text);
    }

    .success-modal p {
        margin: 0 0 24px;
        color: var(--muted);
        font-size: 14px;
        line-height: 1.5;
    }

    .success-modal-btn {
        width: 100%;
        padding: 12px 20px;
        background: var(--brand);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
    }

    .success-modal-btn:hover {
        background: var(--brand-dark);
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 22px 18px;
        }
    }
</style>

<!-- Success Modal -->
<div id="resetSuccessOverlay">
    <div class="success-modal">
        <div class="success-modal-icon">
            <i class="fa-solid fa-check"></i>
        </div>
        <h3><?= t('reset_password.success_title') ?></h3>
        <p id="resetSuccessMsg"><?= t('reset_password.success_body') ?></p>
        <button id="resetSuccessBtn" class="success-modal-btn"><?= t('reset_password.success_confirm') ?></button>
    </div>
</div>

<div class="main-content-container">
    <div class="form-card">
        <?php if ($error_message): ?>
            <div class="form-header-icon error-icon">
                <i class="fa-solid fa-shield-cat"></i>
            </div>
            <h2><?= t('reset_password.title') ?></h2>
            <p class="subtitle"><?= t('reset_password.subtitle_error') ?? 'Δεν ήταν δυνατή η ολοκλήρωση της αίτησης.' ?></p>

            <div class="alert alert-error">
                <div><?php echo htmlspecialchars($error_message); ?></div>
                <div style="margin-top: 6px;">
                    <a href="forgot_password.php"><i class="fa-solid fa-rotate-right"></i> <?= t('reset_password.restart_link') ?></a>
                </div>
            </div>
        <?php else: ?>
            <div class="form-header-icon">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2><?= t('reset_password.title') ?></h2>
            <p class="subtitle"><?= t('reset_password.subtitle') ?? 'Εισάγετε τον νέο σας κωδικό πρόσβασης.' ?></p>

            <div id="resetPasswordAlert" class="alert alert-error" style="display:none;"></div>

            <form id="resetPasswordForm">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($raw_token); ?>">

                <div class="form-group">
                    <label for="password"><?= t('reset_password.new_password') ?></label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="pwd-input" required
                            autocomplete="new-password" placeholder="<?= t('reset_password.min_length') ?>">
                        <i class="fa-solid fa-lock input-icon"></i>
                        <i class="fa-solid fa-eye toggle-pwd" onclick="togglePasswordVisibility('password', this)"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><?= t('reset_password.confirm_password') ?></label>
                    <div class="input-wrapper">
                        <input type="password" id="confirm_password" name="confirm_password" class="pwd-input" required
                            autocomplete="new-password">
                        <i class="fa-solid fa-shield-halved input-icon"></i>
                        <i class="fa-solid fa-eye toggle-pwd" onclick="togglePasswordVisibility('confirm_password', this)"></i>
                    </div>
                </div>

                <button type="submit" id="resetSubmitBtn">
                    <span id="resetBtnText"><?= t('reset_password.submit') ?></span>
                    <span id="resetBtnSpinner" style="display:none;">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </span>
                </button>
            </form>

            <a href="<?= asset('') ?>" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> <?= t('forgot_password.back_to_login') ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, icon) {
        const input = document.getElementById(inputId);
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

<script src="<?= getVersionedAssetUrl('js/auth.js') ?>"></script>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>