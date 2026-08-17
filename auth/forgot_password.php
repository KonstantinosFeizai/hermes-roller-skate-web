<?php
// forgot_password.php
// Purpose: Password recovery request form with enhanced UI/UX.
session_start();
require_once  __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'partials/header.php';
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

    h2 {
        text-align: center;
        color: var(--text);
        margin: 0 0 8px 0;
        font-size: clamp(22px, 2.4vw, 26px);
        font-weight: 700;
        letter-spacing: -0.4px;
    }

    .form-card p {
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

    .input-wrapper i {
        position: absolute;
        left: 14px;
        color: var(--muted);
        font-size: 15px;
        transition: color 0.2s ease;
    }

    input[type="email"] {
        width: 100%;
        padding: 12px 14px 12px 42px;
        border: 1px solid var(--border);
        border-radius: 12px;
        background: #fff;
        color: var(--text);
        font-size: 15px;
        transition: all 0.2s ease;
    }

    input[type="email"]:focus {
        outline: none;
        border-color: var(--brand);
        box-shadow: 0 0 0 4px var(--ring);
    }

    .input-wrapper:focus-within i {
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
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-align: center;
    }

    .alert-error,
    .alert-warning,
    .alert-info {
        color: var(--error-text);
        background-color: var(--error-bg);
        border: 1px solid var(--error-border);
    }

    .alert-success {
        color: var(--success-text);
        background-color: var(--success-bg);
        border: 1px solid var(--success-border);
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

    /* Success Panel */
    .success-icon-box {
        width: 64px;
        height: 64px;
        background: var(--success-bg);
        color: #16a34a;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin: 0 auto 20px auto;
        border: 1px solid var(--success-border);
    }

    .back-link--btn {
        margin-top: 16px;
        padding: 12px 20px;
        background: var(--brand);
        color: #fff;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .back-link--btn:hover {
        background: var(--brand-dark);
        color: #fff;
    }

    @media (max-width: 480px) {
        .form-card {
            padding: 22px 18px;
        }
    }
</style>

<div class="main-content-container">
    <div class="form-card">

        <!-- ── Success panel ── -->
        <div id="forgotSuccessPanel" style="display:none;">
            <div class="success-icon-box">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <h2><?= t('forgot_password.success_title') ?></h2>
            <p class="success-msg"><?= t('forgot_password.success_body') ?></p>
            <a href="<?= asset('') ?>" class="back-link back-link--btn">
                <i class="fa-solid fa-arrow-left"></i> <?= t('forgot_password.back_to_login') ?>
            </a>
        </div>

        <!-- ── Request form ── -->
        <div id="forgotFormPanel">
            <div class="form-header-icon">
                <i class="fa-solid fa-key"></i>
            </div>
            <h2><?= t('forgot_password.title') ?></h2>
            <p><?= t('forgot_password.subtitle') ?></p>

            <div id="forgotPasswordAlert" class="alert" style="display:none;"></div>

            <form id="forgotPasswordForm">
                <div class="form-group">
                    <label for="email"><?= t('forgot_password.email_label') ?></label>
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required
                            placeholder="you@example.com" autocomplete="email">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                </div>

                <button type="submit" id="forgotSubmitBtn">
                    <span id="forgotBtnText"><?= t('forgot_password.submit') ?></span>
                    <span id="forgotBtnSpinner" style="display:none;">
                        <i class="fa-solid fa-circle-notch fa-spin"></i>
                    </span>
                </button>
            </form>

            <a href="<?= asset('') ?>" class="back-link">
                <i class="fa-solid fa-arrow-left"></i> <?= t('forgot_password.back_to_login') ?>
            </a>
        </div>

    </div>
</div>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>