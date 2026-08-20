<?php
// profile.php
// Purpose: User profile settings and personal info page (requires login).

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'includes/lang.php';

restrict_access(['user', 'admin', 'coach']);

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT username, email, role, created_at, first_name, last_name, age, phone, region, location_id, role_type FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if (!$user_data) throw new Exception("Ο χρήστης δεν βρέθηκε.");

    $locations = $pdo->query("SELECT id, name FROM locations WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
} catch (Exception $e) {
    die("Σφάλμα: " . $e->getMessage());
}

$role_icons = [
    'athlete' => asset('photo/rollers.png'),
    'parent' => asset('photo/family.png'),
    'coach' => asset('photo/coach.png'),
    'none' => asset('photo/user.png'),
];

$pageCss = ['css/profile.css', 'css/thread.css'];
require_once PROJECT_ROOT . 'partials/header.php';
?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header-bar">
            <h2><?= htmlspecialchars(t('profile.title')) ?></h2>

            <div class="notification-wrap">
                <button type="button" id="notificationBellBtn" class="notification-bell-btn" aria-label="Notifications">
                    <span class="notification-bell-icon"><i class="fa-solid fa-bell" style="color: rgb(255, 212, 59);"></i></span>
                    <span id="notificationCount" class="notification-count" style="display:none;">0</span>
                </button>

                <div id="notificationDropdown" class="notification-dropdown" style="display:none;">
                    <div class="notification-dropdown-head">
                        <strong><?= htmlspecialchars(t('notifications.label')) ?></strong>
                        <div class="notification-actions">
                            <button type="button" id="markAllReadBtn" class="notification-link"><?= htmlspecialchars(t('notifications.mark_all_read')) ?></button>
                            <button type="button" id="clearAllNotificationsBtn" class="notification-link"><?= htmlspecialchars(t('notifications.clear_all')) ?></button>
                        </div>
                    </div>
                    <div id="notificationList" class="notification-list">
                        <p class="loading-msg"><?= htmlspecialchars(t('notifications.loading')) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs header -->
        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab('settings')"><i class="fa-solid fa-gear"></i> <?= htmlspecialchars(t('profile.tabs.settings')) ?></button>
            <button class="tab-btn" onclick="openTab('profile')"><i class="fa-regular fa-user"></i> <?= htmlspecialchars(t('profile.tabs.profile')) ?></button>
            <button class="tab-btn" onclick="openTab('role')"><i class="fa-solid fa-user-gear"></i> <?= htmlspecialchars(t('profile.tabs.role')) ?></button>
            <button class="tab-btn" onclick="openTab('athletes', this)"><i class="fa-regular fa-address-card"></i> <?= htmlspecialchars(t('profile.tabs.athletes')) ?></button>
            <button class="tab-btn" onclick="openTab('my-finance')"><i class="fa-solid fa-wallet"></i> <?= htmlspecialchars(t('profile.tabs.finance')) ?></button>
            <button class="tab-btn" onclick="openTab('my-classes')"><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars(t('profile.tabs.classes')) ?></button>
            <button class="tab-btn" onclick="openTab('inbox')"><i class="fa-solid fa-inbox"></i> <?= htmlspecialchars(t('profile.labels.inbox')) ?></button>
        </div>

        <!-- ── Settings tab ──────────────────────────────────── -->
        <div id="settings" class="tab-content active pset-tab-container">

            <div class="pset-main-card">
                <div class="pset-card-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="pset-header-icon-circle">
                            <i class="fa-solid fa-gear"></i>
                        </div>
                        <div class="pset-header-text">
                            <h3 class="pset-card-title"><?= htmlspecialchars(t('profile.labels.settings') ?? 'Account Settings') ?></h3>
                            <p class="pset-subtitle"><?= htmlspecialchars(t('profile.labels.settings_hint') ?? 'Manage your login and contact details.') ?></p>
                        </div>
                    </div>
                    <button type="button" id="toggleSettingsBtn" class="pset-edit-toggle-btn" title="Edit Settings">
                        <i class="fa-regular fa-pen-to-square" id="settingsEditIcon"></i>
                    </button>
                </div>

                <div class="pset-divider"></div>

                <form id="profileUpdateForm" class="pset-column-form" novalidate>
                    <div class="profile-info-group">
                        <label for="username"><?= htmlspecialchars(t('profile.labels.username')) ?></label>
                        <input type="text" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" disabled required>
                    </div>

                    <div class="profile-info-group">
                        <label for="email"><?= htmlspecialchars(t('profile.labels.email')) ?></label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" disabled required>
                    </div>

                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.role')) ?></label>
                        <div class="pset-role-badge-container">
                            <span class="pset-role-badge pset-badge-<?= htmlspecialchars($user_data['role']) ?>">
                                <i class="fa-solid <?= $user_data['role'] === 'admin' ? 'fa-user-shield' : 'fa-user' ?>"></i>
                                <?= $user_data['role'] === 'admin' ? t('profile.labels.role_admin') : ($user_data['role'] === 'coach' ? t('profile.labels.role_coach') : t('profile.labels.role_user')) ?>
                            </span>
                        </div>
                    </div>

                    <div id="profileUpdateMessage" style="margin-top:10px;display:none;text-align:center;"></div>

                    <button type="submit" id="settingsSubmitBtn" class="pset-submit-btn-navy" style="display: none;"><?= htmlspecialchars(t('profile.labels.save_changes')) ?></button>
                </form>
            </div>


            <div class="pset-main-card">
                <div class="pset-card-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="pset-header-icon-circle">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div class="pset-header-text">
                            <h3 class="pset-card-title"><?= htmlspecialchars(t('profile.labels.change_password')) ?></h3>
                            <p class="pset-subtitle"><?= htmlspecialchars(t('profile.labels.change_password_hint') ?? 'Keep your account secure.') ?></p>
                        </div>
                    </div>
                    <button type="button" id="togglePasswordBtn" class="pset-edit-toggle-btn" title="Change Password">
                        <i class="fa-regular fa-pen-to-square" id="passwordEditIcon"></i>
                    </button>
                </div>

                <div class="pset-divider"></div>

                <form id="changePasswordForm" class="pset-column-form">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.current_password')) ?></label>
                        <input type="password" id="current_password" name="current_password" placeholder="<?= htmlspecialchars(t('profile.labels.current_password')) ?>" disabled required>
                    </div>

                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.new_password')) ?></label>
                        <input type="password" id="new_password" name="new_password" placeholder="<?= htmlspecialchars(t('profile.labels.new_password')) ?>" disabled required>
                    </div>

                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.confirm_new_password')) ?></label>
                        <input type="password" id="confirm_new_password" name="confirm_new_password" placeholder="<?= htmlspecialchars(t('profile.labels.confirm_new_password')) ?>" disabled required>
                    </div>

                    <div id="passwordChangeMessage" style="margin-top:10px;display:none;text-align:center;"></div>

                    <button type="submit" id="passwordSubmitBtn" class="pset-submit-btn-navy" style="display: none;"><?= htmlspecialchars(t('profile.labels.update_password')) ?></button>
                </form>
            </div>

            <!-- ── Delete Account Section Card ──────────────────────── -->
            <div class="pset-main-card">
                <div class="pset-card-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="pset-header-icon-circle pset-danger-icon-circle">
                            <i class="fa-solid fa-user-xmark"></i>
                        </div>
                        <div class="pset-header-text">
                            <h3 class="pset-card-title"><?= htmlspecialchars(t('profile.labels.delete_account_title')) ?></h3>
                            <p class="pset-subtitle"><?= htmlspecialchars(t('profile.labels.delete_account_desc')) ?></p>
                        </div>
                    </div>

                    <button type="button" class="delete-account-btn" onclick="openDeleteAccountModal()">
                        <i class="fa-solid fa-user-xmark"></i>
                        <span><?= htmlspecialchars(t('profile.labels.delete_account_btn')) ?></span>
                    </button>
                </div>
            </div>

            <!-- ── Delete Account Modal ────────────────── -->
            <div id="deleteAccountModal" class="modal-overlay" style="display:none;">
                <div class="modal-box delete-account-modal-box">
                    <div class="modal-header">
                        <h3>
                            <i class="fa-solid fa-triangle-exclamation modal-danger-icon"></i>
                            <?= htmlspecialchars(t('profile.labels.delete_account_title')) ?>
                        </h3>
                        <button class="modal-close-btn" onclick="closeDeleteAccountModal()" aria-label="Close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Warning Alert Box -->
                        <div class="delete-account-alert-box">
                            <p class="delete-account-warning"><?= htmlspecialchars(t('profile.labels.delete_account_warning')) ?></p>
                            <ul class="delete-account-list">
                                <li><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars(t('profile.labels.delete_item_profile')) ?></li>
                                <li><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars(t('profile.labels.delete_item_athletes')) ?></li>
                                <li><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars(t('profile.labels.delete_item_payments')) ?></li>
                                <li><i class="fa-solid fa-circle-xmark"></i> <?= htmlspecialchars(t('profile.labels.delete_item_messages')) ?></li>
                            </ul>
                        </div>

                        <div class="profile-info-group" style="margin-top:20px;">
                            <label for="deleteAccountPassword"><?= htmlspecialchars(t('profile.labels.delete_confirm_password')) ?></label>
                            <input type="password" id="deleteAccountPassword" class="modal-input"
                                placeholder="<?= htmlspecialchars(t('profile.labels.current_password')) ?>">
                        </div>

                        <div id="deleteAccountError" class="delete-account-error" style="display:none;"></div>

                        <div class="delete-account-modal-actions">
                            <button type="button" class="pset-submit-btn-navy" onclick="closeDeleteAccountModal()">
                                <?= htmlspecialchars(t('profile.labels.cancel')) ?>
                            </button>
                            <button type="button" class="delete-account-confirm-btn" id="deleteAccountConfirmBtn" onclick="confirmDeleteAccount()">
                                <i class="fa-solid fa-user-minus"></i>
                                <span><?= htmlspecialchars(t('profile.labels.delete_account_confirm')) ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <!-- ── Personal info tab ──────────────────────────────── -->
        <div id="profile" class="tab-content">
            <div class="pinfo-main-card">

                <div class="pinfo-card-header">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <div class="pinfo-header-icon-circle">
                            <i class="fa-regular fa-user"></i>
                        </div>
                        <div class="pinfo-header-text">
                            <h3 class="pinfo-card-title"><?= htmlspecialchars(t('profile.tabs.profile')) ?></h3>
                            <p class="pinfo-subtitle"><?= htmlspecialchars(t('profile.labels.profile_info_hint')) ?></p>
                        </div>
                    </div>
                    <button type="button" id="toggleEditBtn" class="pinfo-edit-toggle-btn" title="Edit Profile">
                        <i class="fa-regular fa-pen-to-square" id="editIcon"></i>
                    </button>
                </div>

                <div class="pinfo-divider"></div>

                <form id="personalInfoForm" novalidate>
                    <div class="pinfo-field-row">
                        <div class="profile-info-group">
                            <label for="first_name"><?= htmlspecialchars(t('profile.labels.first_name')) ?></label>
                            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user_data['first_name'] ?? '') ?>" disabled required>
                        </div>
                        <div class="profile-info-group">
                            <label for="last_name"><?= htmlspecialchars(t('profile.labels.last_name')) ?></label>
                            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user_data['last_name'] ?? '') ?>" disabled required>
                        </div>
                    </div>

                    <div class="pinfo-field-row">
                        <div class="profile-info-group">
                            <label for="age"><?= htmlspecialchars(t('profile.labels.age')) ?></label>
                            <input type="number" id="age" name="age" value="<?= htmlspecialchars($user_data['age'] ?? '') ?>" disabled required>
                        </div>
                        <div class="profile-info-group">
                            <label for="phone"><?= htmlspecialchars(t('profile.labels.phone')) ?></label>
                            <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>" disabled required>
                        </div>
                    </div>

                    <div class="pinfo-field-row">
                        <div class="profile-info-group">
                            <label for="region"><?= htmlspecialchars(t('onboarding.region')) ?></label>
                            <input type="text" id="region" name="region" value="<?= htmlspecialchars($user_data['region'] ?? '') ?>" disabled>
                        </div>
                        <div class="profile-info-group">
                            <label for="location_id"><?= htmlspecialchars(t('profile.labels.region')) ?></label>
                            <div class="pinfo-select-wrapper">
                                <select id="location_id" name="location_id" disabled onchange="document.getElementById('pinfo-mock-text').innerText = this.options[this.selectedIndex].text">
                                    <option value=""><?= htmlspecialchars(t('profile.labels.select_region')) ?></option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= (int)$loc['id'] ?>" <?= ((int)($user_data['location_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '') ?>>
                                            <?= htmlspecialchars($loc['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="pinfo-select-display">
                                    <?php
                                    $selected_name = t('profile.labels.select_region');
                                    foreach ($locations as $loc) {
                                        if ((int)($user_data['location_id'] ?? 0) === (int)$loc['id']) {
                                            $selected_name = $loc['name'];
                                            break;
                                        }
                                    }
                                    ?>
                                    <span id="pinfo-mock-text"><?= htmlspecialchars($selected_name) ?></span>
                                    <i class="fa-solid fa-chevron-down pinfo-select-arrow"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="personalInfoMessage" style="margin-top:10px;display:none;text-align:center;"></div>

                    <button type="submit" id="submitBtn" class="pinfo-submit-btn-navy" style="display: none;"><?= htmlspecialchars(t('profile.labels.save_profile')) ?></button>
                </form>
            </div>
        </div>

        <!-- ── Role tab ───────────────────────────────────────── -->
        <div id="role" class="tab-content">
            <!-- Main Card Body Container Wrapper -->
            <div class="prole-main-card">

                <!-- Header Text Section Block & Edit Toggle Button -->
                <div class="prole-card-header-wrapper">
                    <div class="prole-card-header">
                        <h3 class="prole-card-title"><?= htmlspecialchars(t('profile.title')) ?></h3>
                        <p class="role-subtitle"><?= htmlspecialchars(t('profile.labels.role_subtitle')) ?></p>
                    </div>

                    <!-- Edit / Cancel Toggle Button -->
                    <button type="button" class="pinfo-edit-toggle-btn" id="toggleRoleEditBtn" title="Edit Role">
                        <i class="fa-solid fa-pen-to-square" id="roleEditIcon"></i>
                    </button>
                </div>

                <div class="prole-divider"></div>

                <!-- 2-Column Selectable Cards Grid Layout -->
                <div class="ob-role-grid">

                    <!-- Card Option: Athlete -->
                    <label class="ob-role-card">
                        <div class="ob-role-card-inner">
                            <div class="prole-radio-custom">
                                <input type="radio" name="profile_role_type" value="athlete" <?= ($user_data['role_type'] ?? '') === 'athlete' ? 'checked' : '' ?> disabled>
                                <div class="prole-radio-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                            <div class="prole-icon-avatar">
                                <i class="fa-solid fa-child-reaching"></i>
                            </div>
                            <span class="ob-role-text">
                                <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_athlete_name')) ?></span>
                                <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_athlete_desc')) ?></span>
                            </span>
                        </div>
                    </label>

                    <!-- Card Option: Parent -->
                    <label class="ob-role-card">
                        <div class="ob-role-card-inner">
                            <div class="prole-radio-custom">
                                <input type="radio" name="profile_role_type" value="parent" <?= ($user_data['role_type'] ?? '') === 'parent' ? 'checked' : '' ?> disabled>
                                <div class="prole-radio-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                            <div class="prole-icon-avatar">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <span class="ob-role-text">
                                <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_parent_name')) ?></span>
                                <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_parent_desc')) ?></span>
                            </span>
                        </div>
                    </label>

                    <!-- Card Option: Coach -->
                    <label class="ob-role-card">
                        <div class="ob-role-card-inner">
                            <div class="prole-radio-custom">
                                <input type="radio" name="profile_role_type" value="coach" <?= ($user_data['role_type'] ?? '') === 'coach' ? 'checked' : '' ?> disabled>
                                <div class="prole-radio-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                            <div class="prole-icon-avatar">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <span class="ob-role-text">
                                <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_coach_name')) ?></span>
                                <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_coach_desc')) ?></span>
                            </span>
                        </div>
                    </label>

                    <!-- Card Option: None -->
                    <label class="ob-role-card">
                        <div class="ob-role-card-inner">
                            <div class="prole-radio-custom">
                                <input type="radio" name="profile_role_type" value="none" <?= (($user_data['role_type'] ?? '') === 'none' || ($user_data['role_type'] ?? '') === '') ? 'checked' : '' ?> disabled>
                                <div class="prole-radio-circle"><i class="fa-solid fa-check"></i></div>
                            </div>
                            <div class="prole-icon-avatar">
                                <i class="fa-regular fa-user"></i>
                            </div>
                            <span class="ob-role-text">
                                <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_none_name')) ?></span>
                                <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_none_desc')) ?></span>
                            </span>
                        </div>
                    </label>

                </div>

                <!-- Form Submission Footer Actions Row -->
                <div id="roleMessage" style="margin-bottom:12px; display:none;"></div>
                <button class="prole-submit-btn-orange" id="saveRoleBtn" onclick="saveRoleType()" style="display: none;">
                    <?= htmlspecialchars(t('profile.labels.save_role')) ?>
                </button>

            </div>
        </div>

        <!-- ── Athletes tab ───────────────────────────────────── -->
        <div id="athletes" class="tab-content">
            <div id="athletes-list"></div>

            <div id="athlete-form-wrap" style="display:none;">
                <div class="pf-card-header">
                    <div class="pf-header-icon-circle">
                        <i class="fa-solid fa-pen"></i>
                    </div>
                    <h4 id="athlete-form-title" class="athlete-form-title"><?= htmlspecialchars(t('profile.labels.athlete_form_add')) ?></h4>
                </div>

                <input type="hidden" id="pf_athlete_id">

                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_first_name')) ?> <span class="pf-required">*</span></label>
                        <input type="text" id="pf_first_name" placeholder="π.χ. Νίκος">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_last_name')) ?> <span class="pf-required">*</span></label>
                        <input type="text" id="pf_last_name" placeholder="π.χ. Παπαδόπουλος">
                    </div>
                </div>

                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_birth_date')) ?> <span class="pf-required">*</span></label>
                        <input type="date" id="pf_birth_date">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_phone')) ?></label>
                        <input type="tel" id="pf_phone" placeholder="69XXXXXXXX">
                    </div>
                </div>

                <div class="profile-info-group single-column-group">
                    <label><?= htmlspecialchars(t('profile.labels.athlete_location')) ?></label>
                    <div class="pf-select-wrapper">
                        <select id="pf_location">
                            <option value=""><?= htmlspecialchars(t('profile.labels.athlete_location_placeholder')) ?></option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_shoe_size')) ?></label>
                        <input type="text" id="pf_shoe_size" placeholder="π.χ. 38">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_shirt_size')) ?></label>
                        <div class="pf-select-wrapper">
                            <select id="pf_shirt_size">
                                <option value="">Επιλέξτε...</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                        </div>
                    </div>
                </div>

                <p class="pf-section-label"><?= htmlspecialchars(t('profile.labels.athlete_interests')) ?></p>
                <div class="pf-interests-row">
                    <label class="pf-interest-pill">
                        <input type="checkbox" id="pf_rides">
                        <span><?= htmlspecialchars(t('profile.labels.athlete_interest_rides')) ?></span>
                    </label>
                    <label class="pf-interest-pill">
                        <input type="checkbox" id="pf_races">
                        <span><?= htmlspecialchars(t('profile.labels.athlete_interest_races')) ?></span>
                    </label>
                    <label class="pf-interest-pill">
                        <input type="checkbox" id="pf_ski">
                        <span><?= htmlspecialchars(t('profile.labels.athlete_interest_ski')) ?></span>
                    </label>
                    <label class="pf-interest-pill">
                        <input type="checkbox" id="pf_skating">
                        <span><?= htmlspecialchars(t('profile.labels.athlete_interest_skating')) ?></span>
                    </label>
                    <label class="pf-interest-pill">
                        <input type="checkbox" id="pf_hockey">
                        <span><?= htmlspecialchars(t('profile.labels.athlete_interest_hockey')) ?></span>
                    </label>
                </div>

                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_amka')) ?></label>
                        <input type="text" id="pf_amka" placeholder="11 ψηφία" maxlength="11">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_afm')) ?></label>
                        <input type="text" id="pf_afm" placeholder="9 ψηφία" maxlength="9">
                    </div>
                </div>

                <div id="athleteFormMsg" style="margin-bottom:10px;"></div>

                <div class="pf-btn-row">
                    <button class="pf-btn-primary" id="saveAthleteBtn" onclick="saveAthlete()">
                        <i class="fa-regular fa-floppy-disk"></i> <?= htmlspecialchars(t('profile.labels.athlete_save')) ?>
                    </button>
                    <button class="pf-btn-secondary" onclick="cancelAthleteForm()">
                        <i class="fa-solid fa-xmark"></i> <?= htmlspecialchars(t('profile.labels.athlete_cancel')) ?>
                    </button>
                </div>
            </div>

            <button class="profile-submit-btn" id="addAthleteBtn" style="margin-top:20px;" onclick="showAthleteForm()"><?= htmlspecialchars(t('profile.labels.athletes_add')) ?></button>
        </div>

        <!-- ── My Finance tab ─────────────────────────────────── -->
        <div id="my-finance" class="tab-content">
            <div id="myFinanceContent">
                <p class="loading-msg">Φόρτωση...</p>
            </div>
        </div>

        <!-- ── My Classes tab ─────────────────────────────────── -->
        <div id="my-classes" class="tab-content">
            <div id="myClassesContent">
                <p class="loading-msg">Φόρτωση...</p>
            </div>
        </div>

        <!-- ── Inbox tab ─────────────────────────────────────── -->
        <div id="inbox" class="tab-content">
            <div id="inboxContent" class="inbox-split-container">
                <div class="inbox-sidebar">
                    <div class="inbox-sidebar-header">
                        <div class="messages-title-row">
                            <div class="messages-icon-title">
                                <div class="messages-icon-wrapper">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                        <polyline points="22,6 12,13 2,6" />
                                    </svg>
                                </div>
                                <div>
                                    <h2><?= htmlspecialchars(t('profile.labels.inbox')) ?></h2>
                                    <span id="unread-count-badge" class="unread-count-txt">0 <?= htmlspecialchars(t('profile.labels.unread_messages')) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="search-wrapper">
                            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="inbox-search" placeholder="<?= htmlspecialchars(t('profile.labels.search_placeholder')) ?>" oninput="filterMessages()">
                        </div>
                    </div>
                    <div id="inbox-list-target" class="inbox-list">
                    </div>
                </div>

                <div id="inbox-reading-pane" class="inbox-view-pane">
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    window.BASE_URL = "<?= rtrim(asset(''), '/') ?>/";
    window.USER_ROLE_TYPE = "<?= htmlspecialchars($user_data['role_type'] ?? 'none') ?>";
    window.CURRENT_USER_ID = <?= (int)$user_id ?>;
    window.PT = <?= json_encode([
                    'no_athletes'        => t('profile.labels.fin_no_athletes'),
                    'purchased'          => t('profile.labels.fin_purchased'),
                    'used'               => t('profile.labels.fin_used'),
                    'balance'            => t('profile.labels.fin_balance'),
                    'total'              => t('profile.labels.fin_total'),
                    'no_payments'        => t('profile.labels.fin_no_payments'),
                    'fin_free'           => t('profile.labels.fin_free'),
                    'type_prepaid'       => t('profile.labels.pay_type_prepaid'),
                    'type_free'          => t('profile.labels.pay_type_free'),
                    'type_gift'          => t('profile.labels.pay_type_gift'),
                    'method_cash'        => t('profile.labels.pay_method_cash'),
                    'method_card'        => t('profile.labels.pay_method_card'),
                    'method_transfer'    => t('profile.labels.pay_method_transfer'),
                    'method_other'       => t('profile.labels.pay_method_other'),
                    'upcoming'           => t('profile.labels.cls_upcoming'),
                    'history'            => t('profile.labels.cls_history'),
                    'no_upcoming'        => t('profile.labels.cls_no_upcoming'),
                    'no_history'         => t('profile.labels.cls_no_history'),
                    'status_scheduled'   => t('profile.labels.cls_status_scheduled'),
                    'status_completed'   => t('profile.labels.cls_status_completed'),
                    'status_cancelled'   => t('profile.labels.cls_status_cancelled'),
                    'attended'           => t('profile.labels.cls_attended'),
                    'athletes_edit'      => t('profile.labels.athletes_edit'),
                    'athletes_delete'    => t('profile.labels.athletes_delete'),
                    'athlete_form_edit'  => t('profile.labels.athlete_form_edit'),
                    'athlete_no_card_required' => t('profile.labels.athlete_no_card_required'),
                    'athlete_not_added'  => t('profile.labels.athlete_not_added'),
                    'athlete_saved_success'    => t('profile.labels.saved_success'),
                    'athlete_updated_success'  => t('profile.labels.athlete_updated_success'),
                    'athlete_delete_confirm'   => t('profile.labels.athlete_delete_confirm'),
                    'val_athlete_name_req'     => t('profile.labels.val_athlete_name_req'),
                    'val_athlete_loc_req'      => t('profile.labels.val_athlete_loc_req'),
                    'val_athlete_phone_inv'    => t('profile.labels.pinfo_invalid_phone'),
                    'athlete_interest_rides'   => t('profile.labels.athlete_interest_rides'),
                    'athlete_interest_races'   => t('profile.labels.athlete_interest_races'),
                    'athlete_interest_ski'     => t('profile.labels.athlete_interest_ski'),
                    'athlete_interest_skating' => t('profile.labels.athlete_interest_skating'),
                    'athlete_interest_hockey'  => t('profile.labels.athlete_interest_hockey'),
                    'receipt_download'   => t('receipts.download'),
                    'reply'              => t('profile.labels.reply'),
                    'success'             => t('profile.labels.success'),
                    'transactions_plural' => t('profile.labels.transactions_plural'),
                    'transactions_singular' => t('profile.labels.transactions_singular'),
                    'receipt'            => t('profile.labels.receipt'),
                    'send'               => t('profile.labels.send'),
                    'inbox'             => t('profile.labels.inbox'),
                    'select_message' => t('profile.labels.select_message'),
                    'unread'             => t('profile.labels.unread_messages'),
                    'view_message'         => t('profile.labels.view_message'),
                    'from'               => t('profile.labels.from'),
                    'inbox_empty'          => t('profile.labels.inbox_empty'),
                    'load_more'          => t('profile.labels.load_more'),
                    'cancel'             => t('profile.labels.cancel'),
                    'write_reply'        => t('profile.labels.write_reply'),
                    'select_athlete'     => t('profile.labels.select_athlete'),
                    'sessions_plural'           => t('profile.labels.sessions_plural'),
                    'sessions_singular'           => t('profile.labels.sessions_singular'),
                    'total_sessions'     => t('profile.labels.total_sessions'),
                    'this_period'        => t('profile.labels.this_period'),
                    'remaining_sessions' => t('profile.labels.remaining_sessions'),
                    'lifetime_value'     => t('profile.labels.lifetime_value'),
                    'payment_history'    => t('profile.labels.payment_history'),
                    'no_notifications'   => t('notifications.no_notifications'),
                    'caught_up'          => t('notifications.caught_up'),
                    'select_role_required' => t('profile.labels.select_role_required'),
                    'saved_success'        => t('profile.labels.saved_success'),
                    'error_generic'        => t('profile.labels.error_generic'),
                    'connection_error'     => t('profile.labels.connection_error'),
                    'pinfo_success'         => t('profile.labels.pinfo_success'),
                    'pinfo_required_fields' => t('profile.labels.pinfo_required_fields'),
                    'pinfo_invalid_data'    => t('profile.labels.pinfo_invalid_data'),
                    'pinfo_invalid_phone'   => t('profile.labels.pinfo_invalid_phone'),
                    'pinfo_db_error'        => t('profile.labels.pinfo_db_error'),
                    'pinfo_missing_location' => t('profile.labels.pinfo_missing_location'),
                    'settings_required_fields' => t('profile.labels.settings_required_fields'),
                    'settings_email_exists'     => t('profile.labels.settings_email_exists'),
                    'settings_invalid_email'    => t('profile.labels.settings_invalid_email'),
                    'settings_username_exists'  => t('profile.labels.settings_username_exists'),
                    'settings_success'          => t('profile.labels.settings_success'),
                    'pass_all_fields_required'  => t('profile.labels.pass_all_fields_required'),
                    'pass_incorrect_current'    => t('profile.labels.pass_incorrect_current'),
                    'pass_mismatch'             => t('profile.labels.pass_mismatch'),
                    'pass_min_length'           => t('profile.labels.pass_min_length'),
                    'pass_success'              => t('profile.labels.pass_success'),
                ], JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= getVersionedAssetUrl('js/auth.js') ?>"></script>
<script src="<?= getVersionedAssetUrl('js/thread.js') ?>"></script>
<script src="<?= getVersionedAssetUrl('js/profile.js') ?>"></script>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>