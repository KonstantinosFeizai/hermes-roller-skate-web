<?php
// profile.php
// Purpose: User profile settings and personal info page (requires login).

// Core config + access control + language helper
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Access control: only logged-in users and admins
restrict_access(['user', 'admin']);

// Fetch current user data
$user_id = $_SESSION['user_id'];
$user_data = null;

// Fetch user data from the database
try {
    // 1. Fetch user profile data (including extended fields)
    $stmt = $pdo->prepare("SELECT username, email, role, created_at, first_name, last_name, age, phone, region FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data) {
        throw new Exception("Ο χρήστης δεν βρέθηκε.");
    }
} catch (Exception $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// Page styling
$pageCss = ['css/profile.css'];
// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- PROFILE PAGE CONTENT -->
<div class="profile-container">
    <div class="profile-card">
        <h2><?= htmlspecialchars(t('profile.title')) ?></h2>

        <!-- Tabs header -->
        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab('settings')"><?= htmlspecialchars(t('profile.tabs.settings')) ?></button>
            <button class="tab-btn" onclick="openTab('profile')"><?= htmlspecialchars(t('profile.tabs.profile')) ?></button>
        </div>

        <!-- Settings tab (username/email + password) -->
        <div id="settings" class="tab-content active">
            <form id="profileUpdateForm">
                <div class="profile-info-group">
                    <label for="username"><?= htmlspecialchars(t('profile.labels.username')) ?></label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
                </div>

                <div class="profile-info-group">
                    <label for="email"><?= htmlspecialchars(t('profile.labels.email')) ?></label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>

                <div class="profile-info-group">
                    <label><?= htmlspecialchars(t('profile.labels.role')) ?></label>
                    <span class="badge badge-<?php echo $user_data['role']; ?>">
                        <?php echo $user_data['role'] === 'admin' ? t('profile.labels.role_admin') : t('profile.labels.role_user'); ?>
                    </span>
                </div>

                <div id="profileUpdateMessage" style="margin-top: 10px; display: none; text-align: center;"></div>

                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.save_changes')) ?></button>
            </form>

            <hr style="margin: 30px 0; border: 0; border-top: 1px solid #eee;">

            <h3><?= htmlspecialchars(t('profile.labels.change_password')) ?></h3>
            <form id="changePasswordForm">
                <div class="profile-info-group"><input type="password" name="current_password" placeholder="<?= htmlspecialchars(t('profile.labels.current_password')) ?>" required></div>
                <div class="profile-info-group"><input type="password" name="new_password" placeholder="<?= htmlspecialchars(t('profile.labels.new_password')) ?>" required></div>
                <div class="profile-info-group"><input type="password" name="confirm_new_password" placeholder="<?= htmlspecialchars(t('profile.labels.confirm_new_password')) ?>" required></div>
                <div id="passwordChangeMessage" style="margin-top: 10px; display: none; text-align: center;"></div>
                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.update_password')) ?></button>
            </form>
        </div>

        <!-- Personal info tab -->
        <div id="profile" class="tab-content">
            <p style="color: #888; margin-bottom: 20px;"><?= htmlspecialchars(t('profile.labels.profile_info_hint')) ?></p>
            <form id="personalInfoForm">
                <div class="profile-info-group">
                    <label for="first_name"><?= htmlspecialchars(t('profile.labels.first_name')) ?></label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user_data['first_name'] ?? ''); ?>">
                </div>

                <div class="profile-info-group">
                    <label for="last_name"><?= htmlspecialchars(t('profile.labels.last_name')) ?></label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user_data['last_name'] ?? ''); ?>">
                </div>

                <div class="profile-info-group">
                    <label for="age"><?= htmlspecialchars(t('profile.labels.age')) ?></label>
                    <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user_data['age'] ?? ''); ?>">
                </div>

                <div class="profile-info-group">
                    <label for="phone"><?= htmlspecialchars(t('profile.labels.phone')) ?></label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                </div>

                <div class="profile-info-group">
                    <label for="region"><?= htmlspecialchars(t('profile.labels.region')) ?></label>
                    <select id="region" name="region">
                        <option value=""><?= htmlspecialchars(t('profile.labels.select_region')) ?></option>
                        <option value="Μαρούσι" <?php echo ($user_data['region'] == 'Μαρούσι') ? 'selected' : ''; ?>>Μαρούσι</option>
                        <option value="ΟΑΚΑ" <?php echo ($user_data['region'] == 'ΟΑΚΑ') ? 'selected' : ''; ?>>ΟΑΚΑ</option>
                        <option value="Σχολείο" <?php echo ($user_data['region'] == 'Σχολείο') ? 'selected' : ''; ?>>Σχολείο</option>
                        <option value="ΕΚΠΑ" <?php echo ($user_data['region'] == 'ΕΚΠΑ') ? 'selected' : ''; ?>>ΕΚΠΑ</option>
                    </select>
                </div>

                <div id="personalInfoMessage" style="margin-top: 10px; display: none; text-align: center;"></div>
                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.save_profile')) ?></button>
            </form>
        </div>

    </div>
</div>

<script>
    // Tabs toggle logic
    function openTab(tabName, triggerEl = null) {
        // Hide all tab contents
        const contents = document.querySelectorAll('.tab-content');
        contents.forEach(content => content.classList.remove('active'));

        // Remove active state from all buttons
        const buttons = document.querySelectorAll('.tab-btn');
        buttons.forEach(btn => btn.classList.remove('active'));

        // Show selected tab
        document.getElementById(tabName).classList.add('active');
        const currentTarget = triggerEl || (typeof event !== 'undefined' ? event.currentTarget : null);
        if (currentTarget) {
            currentTarget.classList.add('active');
        } else {
            const fallbackButton = document.querySelector(`.tab-btn[onclick*="${tabName}"]`);
            if (fallbackButton) {
                fallbackButton.classList.add('active');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Auto-open profile tab when coming from redirect
        if (window.location.hash === '#profile') {
            openTab('profile');
        }
    });
</script>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>