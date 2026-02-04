<?php
// login_modal.php
// Purpose: Auth modal (login, signup, success state).

// Core config + language helper
if (!defined('PROJECT_ROOT')) {
    require_once __DIR__ . '/../config.php';
}
require_once PROJECT_ROOT . 'includes/lang.php';

?>

<!-- AUTH MODAL -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <!-- Login form -->
        <form id="loginForm" class="auth-form">
            <h2 class="modal-title"><?= t('auth.login.title') ?></h2>

            <input type="text" id="loginUsername" name="username" placeholder="<?= t('auth.login.username_placeholder') ?>" novalidate>
            <p class="error-msg" id="loginUsernameError"></p>

            <input type="password" id="loginPassword" name="password" placeholder="<?= t('auth.login.password_placeholder') ?>" novalidate>
            <p class="error-msg" id="loginPasswordError"></p>

            <p class="error-msg general-error-msg" id="loginGeneralError"></p>

            <button type="submit" class="login-submit" id="loginBtn"><?= t('auth.login.submit') ?></button>
            <div id="loginFormGeneralError" style="color: red; margin-top: 10px; display: none;">
            </div>
            <div class="login-links">
                <a href="<?= asset('auth/forgot_password.php') ?>"><?= t('auth.login.forgot') ?></a>
            </div>

            <div class="login-divider"><span><?= t('auth.login.divider') ?></span></div>

            <p class="login-bottom">
                <?= t('auth.login.no_account') ?> <a href="#" id="openSignupLink"><?= t('auth.login.signup') ?></a>
            </p>
        </form>

        <!-- Signup form -->
        <form id="signupForm" class="auth-form hidden" novalidate>
            <div class="signup-header">
                <button type="button" class="back-btn" id="openLoginLink"><?= t('auth.signup.back') ?></button>
            </div>

            <h2 class="modal-title"><?= t('auth.signup.title') ?></h2>

            <input type="text" id="signupUsername" name="username" placeholder="<?= t('auth.signup.username_placeholder') ?>" novalidate required>
            <p class="error-msg" id="signupUsernameError"></p>

            <input type="email" id="signupEmail" name="email" placeholder="<?= t('auth.signup.email_placeholder') ?>" novalidate required>
            <p class="error-msg" id="signupEmailError"></p>

            <input type="password" id="signupPassword" name="password" placeholder="<?= t('auth.signup.password_placeholder') ?>" novalidate required>
            <p class="error-msg" id="signupPasswordError"></p>

            <input type="password" id="signupConfirmPassword" name="confirm_password" placeholder="<?= t('auth.signup.confirm_password_placeholder') ?>" novalidate required>
            <p class="error-msg" id="signupConfirmPasswordError"></p>

            <p class="error-msg general-error-msg" id="signupGeneralError"></p>

            <button type="submit" class="login-submit" id="signupBtn"><?= t('auth.signup.submit') ?></button>

            <div class="login-divider"><span><?= t('auth.signup.divider') ?></span></div>

            <p class="login-bottom">
                <?= t('auth.signup.have_account') ?> <a href="#" id="openLoginLinkBottom"><?= t('auth.signup.login') ?></a>
            </p>
        </form>

        <!-- Signup success state -->
        <div id="signupSuccess" class="auth-form hidden">
            <h2 class="modal-title"><?= t('auth.success.title') ?></h2>
            <p><?= t('auth.success.message') ?> <strong id="successEmail"></strong>.
                <?= t('auth.success.verify') ?></p>
            <button type="button" class="login-submit" id="closeSuccessBtn"><?= t('auth.success.close') ?></button>
        </div>

        <!-- Close button -->
        <span class="close" id="closeModalBtn">&times;</span>
    </div>
</div>