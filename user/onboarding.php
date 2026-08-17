<?php
// user/onboarding.php
// Purpose: Step-by-step wizard για νέους χρήστες.

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'includes/lang.php';

restrict_access(['user', 'admin']);

$user_id = $_SESSION['user_id'];

// Φέρνουμε τα δεδομένα του χρήστη
try {
    $stmt = $pdo->prepare("
        SELECT username, email, first_name, last_name, age, phone, region, role_type, onboarding_completed
        FROM users WHERE id = ?
    ");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user) {
        header('Location: ' . asset('index'));
        exit;
    }

    // Redirect users who already completed onboarding
    if (!empty($user['onboarding_completed'])) {
        header('Location: ' . asset('user/profile'));
        exit;
    }

    // Φέρνουμε τις τοποθεσίες για το dropdown
    $locations = $pdo->query("SELECT id, name FROM locations WHERE is_active = 1 ORDER BY sort_order")->fetchAll();
} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

$role_icons = [
    'athlete' => asset('photo/athlete.png'),
    'parent'  => asset('photo/parents.png'),
    'coach'   => asset('photo/coach_2.png'),
    'none'    => asset('photo/user_1.png'),
];

$pageTitle = t('onboarding.page_title');
$pageCss   = ['css/onboarding.css'];
?>
<!DOCTYPE html>
<html lang="<?= $GLOBALS['currentLang'] ?? 'el' ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" href="<?= asset('photo/hermes_logo.png') ?>">
    <link rel="stylesheet" href="<?= getVersionedAssetUrl('css/onboarding.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"> -->
</head>

<body>
    <div class="onboarding-page">
        <div class="onboarding-card">

            <!-- Progress bar -->
            <div class="onboarding-progress">
                <div class="onboarding-progress-fill" id="progressFill" style="width: 25%"></div>
            </div>

            <!-- Header -->
            <div class="onboarding-header">
                <div class="onboarding-step-label" id="stepLabel"><?= htmlspecialchars(t('onboarding.step1_label')) ?></div>
                <h1 class="onboarding-title" id="stepTitle"><?= htmlspecialchars(t('onboarding.step1_title')) ?></h1>
                <p class="onboarding-subtitle" id="stepSubtitle"><?= htmlspecialchars(t('onboarding.step1_subtitle')) ?></p>
            </div>

            <!-- Body -->
            <div class="onboarding-body">

                <!-- ── STEP 1: Βασικά Στοιχεία ─────────────────── -->
                <div class="onboarding-step active" id="step-1">
                    <div class="ob-field-row">
                        <div class="ob-field">
                            <label for="ob_first_name"><?= htmlspecialchars(t('onboarding.first_name')) ?></label>
                            <input type="text" id="ob_first_name" placeholder="<?= htmlspecialchars(t('onboarding.first_name_placeholder')) ?>"
                                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>">
                        </div>
                        <div class="ob-field">
                            <label for="ob_last_name"><?= htmlspecialchars(t('onboarding.last_name')) ?></label>
                            <input type="text" id="ob_last_name" placeholder="<?= htmlspecialchars(t('onboarding.last_name_placeholder')) ?>"
                                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="ob-field-row">
                        <div class="ob-field">
                            <label for="ob_phone"><?= htmlspecialchars(t('onboarding.phone')) ?></label>
                            <input type="tel" id="ob_phone" placeholder="69XXXXXXXX"
                                value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="ob-field">
                            <label for="ob_age"><?= htmlspecialchars(t('onboarding.age')) ?></label>
                            <input type="number" id="ob_age" placeholder="<?= htmlspecialchars(t('onboarding.age_placeholder')) ?>" min="1" max="120"
                                value="<?= htmlspecialchars($user['age'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="ob-field">
                        <label for="ob_region"><?= htmlspecialchars(t('onboarding.region')) ?></label>
                        <input type="text" id="ob_region" placeholder="<?= htmlspecialchars(t('onboarding.region_placeholder')) ?>"
                            value="<?= htmlspecialchars($user['region'] ?? '') ?>">
                    </div>
                    <div class="ob-error" id="step1-error"></div>
                </div>

                <!-- ── STEP 2: Ρόλος ───────────────────────────── -->
                <div class="onboarding-step" id="step-2">
                    <div class="ob-role-grid">
                        <label class="ob-role-card">
                            <div class="ob-role-card-inner">
                                <input type="radio" name="role_type" value="athlete">
                                <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['athlete']) ?>" alt="" aria-hidden="true">
                                <span class="ob-role-text">
                                    <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_athlete_name')) ?></span>
                                    <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_athlete_desc')) ?></span>
                                </span>
                            </div>
                        </label>
                        <label class="ob-role-card">
                            <div class="ob-role-card-inner">
                                <input type="radio" name="role_type" value="parent">
                                <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['parent']) ?>" alt="" aria-hidden="true">
                                <span class="ob-role-text">
                                    <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_parent_name')) ?></span>
                                    <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_parent_desc')) ?></span>
                                </span>
                            </div>
                        </label>
                        <label class="ob-role-card">
                            <div class="ob-role-card-inner">
                                <input type="radio" name="role_type" value="coach">
                                <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['coach']) ?>" alt="" aria-hidden="true">
                                <span class="ob-role-text">
                                    <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_coach_name')) ?></span>
                                    <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_coach_desc')) ?></span>
                                </span>
                            </div>
                        </label>
                        <label class="ob-role-card">
                            <div class="ob-role-card-inner">
                                <input type="radio" name="role_type" value="none">
                                <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['none']) ?>" alt="" aria-hidden="true">
                                <span class="ob-role-text">
                                    <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_none_name')) ?></span>
                                    <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_none_desc')) ?></span>
                                </span>
                            </div>
                        </label>
                    </div>
                    <div class="ob-error" id="step2-error"></div>
                </div>

                <!-- ── STEP 3a: Αθλητής (ο ίδιος) ────────────── -->
                <div class="onboarding-step" id="step-3-athlete">
                    <p class="ob-section-title"><?= htmlspecialchars(t('onboarding.section_athlete_info')) ?></p>

                    <!-- Banner shown when an active self-athlete already exists: Edit or reuse -->
                    <div id="ob-athlete-exists-banner" style="display:none; margin-bottom:12px;"></div>
                    <input type="hidden" id="ob_athlete_id" value="">

                    <div class="ob-field-row">
                        <div class="ob-field">
                            <label for="ob_birth_date"><?= htmlspecialchars(t('onboarding.birth_date')) ?></label>
                            <input type="date" id="ob_birth_date">
                        </div>
                        <div class="ob-field">
                            <label for="ob_athlete_phone"><?= htmlspecialchars(t('onboarding.contact_phone')) ?></label>
                            <input type="tel" id="ob_athlete_phone" placeholder="69XXXXXXXX">
                        </div>
                    </div>

                    <div class="ob-field">
                        <label for="ob_location"><?= htmlspecialchars(t('onboarding.location')) ?></label>
                        <select id="ob_location">
                            <option value=""><?= htmlspecialchars(t('onboarding.location_placeholder')) ?></option>
                            <?php foreach ($locations as $loc): ?>
                                <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="ob-field-row">
                        <div class="ob-field">
                            <label for="ob_shoe_size"><?= htmlspecialchars(t('onboarding.shoe_size')) ?></label>
                            <input type="text" id="ob_shoe_size" placeholder="<?= htmlspecialchars(t('onboarding.shoe_size_placeholder')) ?>">
                        </div>
                        <div class="ob-field">
                            <label for="ob_shirt_size"><?= htmlspecialchars(t('onboarding.shirt_size')) ?></label>
                            <select id="ob_shirt_size">
                                <option value=""><?= htmlspecialchars(t('onboarding.shirt_size_select')) ?></option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                        </div>
                    </div>

                    <p class="ob-section-title"><?= htmlspecialchars(t('onboarding.section_interests')) ?></p>
                    <div class="ob-interests-grid">
                        <label class="ob-interest-item">
                            <input type="checkbox" id="ob_interest_rides">
                            <div class="ob-interest-inner">
                                <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_rides')) ?></span>
                            </div>
                        </label>
                        <label class="ob-interest-item">
                            <input type="checkbox" id="ob_interest_races">
                            <div class="ob-interest-inner">
                                <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_races')) ?></span>
                            </div>
                        </label>
                        <label class="ob-interest-item">
                            <input type="checkbox" id="ob_interest_ski">
                            <div class="ob-interest-inner">
                                <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_ski')) ?></span>
                            </div>
                        </label>
                        <label class="ob-interest-item">
                            <input type="checkbox" id="ob_interest_skating">
                            <div class="ob-interest-inner">
                                <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_skating')) ?></span>
                            </div>
                        </label>
                        <label class="ob-interest-item">
                            <input type="checkbox" id="ob_interest_hockey">
                            <div class="ob-interest-inner">
                                <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_hockey')) ?></span>
                            </div>
                        </label>
                    </div>

                    <p class="ob-section-title"><?= htmlspecialchars(t('onboarding.section_optional')) ?></p>
                    <div class="ob-field-row">
                        <div class="ob-field">
                            <label for="ob_amka"><?= htmlspecialchars(t('onboarding.amka')) ?></label>
                            <input type="text" id="ob_amka" placeholder="<?= htmlspecialchars(t('onboarding.amka_placeholder')) ?>" maxlength="11">
                        </div>
                        <div class="ob-field">
                            <label for="ob_afm"><?= htmlspecialchars(t('onboarding.afm')) ?></label>
                            <input type="text" id="ob_afm" placeholder="<?= htmlspecialchars(t('onboarding.afm_placeholder')) ?>" maxlength="9">
                        </div>
                    </div>
                    <div class="ob-error" id="step3a-error"></div>
                </div>

                <!-- ── STEP 3b: Γονέας → Προσθήκη Παιδιού ─────── -->
                <div class="onboarding-step" id="step-3-parent">
                    <p style="color:#6b7280; font-size:0.9rem; margin-bottom:16px;">
                        <?= t('onboarding.parent_hint') ?>
                    </p>

                    <!-- Προστεθέντες αθλητές (Badges) -->
                    <div id="ob-athletes-added"></div>
                    <div class="ob-athlete-count" id="ob-athlete-count"></div>

                    <!-- Κουμπί για εμφάνιση φόρμας 2ου παιδιού (αρχικά κρυφό) -->
                    <button type="button" id="ob-btn-show-second-child" class="ob-btn ob-btn-ghost" style="display:none; margin: 15px auto; border: 1px dashed #e5e7eb; width: 100%;">
                        <i class="fa-solid fa-plus"></i> <?= htmlspecialchars(t('onboarding.btn_add_another')) ?>
                    </button>

                    <!-- Φόρμα παιδιού -->
                    <div id="parent-athlete-form">
                        <div class="ob-field-row">
                            <div class="ob-field">
                                <label for="ob_child_first_name"><?= htmlspecialchars(t('onboarding.child_first_name')) ?></label>
                                <input type="text" id="ob_child_first_name" placeholder="<?= htmlspecialchars(t('onboarding.child_first_name_placeholder')) ?>">
                            </div>
                            <div class="ob-field">
                                <label for="ob_child_last_name"><?= htmlspecialchars(t('onboarding.child_last_name')) ?></label>
                                <input type="text" id="ob_child_last_name" placeholder="<?= htmlspecialchars(t('onboarding.child_last_name_placeholder')) ?>">
                            </div>
                        </div>
                        <div class="ob-field-row">
                            <div class="ob-field">
                                <label for="ob_child_birth_date"><?= htmlspecialchars(t('onboarding.birth_date')) ?></label>
                                <input type="date" id="ob_child_birth_date">
                            </div>
                            <div class="ob-field">
                                <label for="ob_child_phone"><?= htmlspecialchars(t('onboarding.child_phone')) ?></label>
                                <input type="tel" id="ob_child_phone" placeholder="69XXXXXXXX">
                            </div>
                        </div>
                        <div class="ob-field">
                            <label for="ob_child_location"><?= htmlspecialchars(t('onboarding.location')) ?></label>
                            <select id="ob_child_location">
                                <option value=""><?= htmlspecialchars(t('onboarding.location_placeholder')) ?></option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="ob-field-row">
                            <div class="ob-field">
                                <label for="ob_child_shoe_size"><?= htmlspecialchars(t('onboarding.shoe_size')) ?></label>
                                <input type="text" id="ob_child_shoe_size" placeholder="π.χ. 32">
                            </div>
                            <div class="ob-field">
                                <label for="ob_child_shirt_size"><?= htmlspecialchars(t('onboarding.shirt_size')) ?></label>
                                <select id="ob_child_shirt_size">
                                    <option value=""><?= htmlspecialchars(t('onboarding.shirt_size_select')) ?></option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                </select>
                            </div>
                        </div>

                        <p class="ob-section-title"><?= htmlspecialchars(t('onboarding.section_interests')) ?></p>
                        <div class="ob-interests-grid">
                            <label class="ob-interest-item">
                                <input type="checkbox" id="ob_child_rides">
                                <div class="ob-interest-inner">
                                    <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_rides')) ?></span>
                                </div>
                            </label>
                            <label class="ob-interest-item">
                                <input type="checkbox" id="ob_child_races">
                                <div class="ob-interest-inner">
                                    <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_races')) ?></span>
                                </div>
                            </label>
                            <label class="ob-interest-item">
                                <input type="checkbox" id="ob_child_ski">
                                <div class="ob-interest-inner">
                                    <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_ski')) ?></span>
                                </div>
                            </label>
                            <label class="ob-interest-item">
                                <input type="checkbox" id="ob_child_skating">
                                <div class="ob-interest-inner">
                                    <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_skating')) ?></span>
                                </div>
                            </label>
                            <label class="ob-interest-item">
                                <input type="checkbox" id="ob_child_hockey">
                                <div class="ob-interest-inner">
                                    <span class="ob-interest-label"><?= htmlspecialchars(t('onboarding.interest_hockey')) ?></span>
                                </div>
                            </label>
                        </div>

                        <p class="ob-section-title" style="margin-top: 16px;"><?= htmlspecialchars(t('onboarding.section_optional')) ?></p>
                        <div class="ob-field-row">
                            <div class="ob-field">
                                <label for="ob_child_amka"><?= htmlspecialchars(t('onboarding.amka')) ?></label>
                                <input type="text" id="ob_child_amka" placeholder="<?= htmlspecialchars(t('onboarding.amka_placeholder')) ?>" maxlength="11">
                            </div>
                            <div class="ob-field">
                                <label for="ob_child_afm"><?= htmlspecialchars(t('onboarding.afm')) ?></label>
                                <input type="text" id="ob_child_afm" placeholder="<?= htmlspecialchars(t('onboarding.afm_placeholder')) ?>" maxlength="9">
                            </div>
                        </div>
                    </div>

                    <div class="ob-error" id="step3b-error"></div>
                </div>

                <!-- ── STEP 4: Success ─────────────────────────── -->
                <div class="onboarding-step" id="step-success">
                    <div class="ob-success">
                        <div class="ob-success-icon">🎉</div>
                        <h3><?= htmlspecialchars(t('onboarding.success_title')) ?></h3>
                        <p><?= htmlspecialchars(t('onboarding.success_message')) ?></p>
                    </div>
                </div>

            </div><!-- /onboarding-body -->

            <!-- Footer / Buttons -->
            <div class="onboarding-footer" id="onboarding-footer">
                <button class="ob-btn ob-btn-ghost" id="ob-btn-back" style="display:none"><i class="fa-solid fa-arrow-left"></i> <?= htmlspecialchars(t('onboarding.btn_back')) ?></button>
                <button class="ob-btn ob-btn-primary" id="ob-btn-next"><?= htmlspecialchars(t('onboarding.btn_next')) ?> <i class="fa-solid fa-arrow-right"></i></button>
            </div>

        </div><!-- /onboarding-card -->
    </div>

    <script>
        window.BASE_URL = "<?= rtrim(asset(''), '/') ?>/";
        window.OB_STRINGS = {
            step1_label: <?= json_encode(t('onboarding.step1_label')) ?>,
            step1_title: <?= json_encode(t('onboarding.step1_title')) ?>,
            step1_subtitle: <?= json_encode(t('onboarding.step1_subtitle')) ?>,
            step2_label: <?= json_encode(t('onboarding.step2_label')) ?>,
            step2_title: <?= json_encode(t('onboarding.step2_title')) ?>,
            step2_subtitle: <?= json_encode(t('onboarding.step2_subtitle')) ?>,
            step3a_label: <?= json_encode(t('onboarding.step3a_label')) ?>,
            step3a_title: <?= json_encode(t('onboarding.step3a_title')) ?>,
            step3a_subtitle: <?= json_encode(t('onboarding.step3a_subtitle')) ?>,
            step3b_label: <?= json_encode(t('onboarding.step3b_label')) ?>,
            step3b_title: <?= json_encode(t('onboarding.step3b_title')) ?>,
            step3b_subtitle: <?= json_encode(t('onboarding.step3b_subtitle')) ?>,
            step3c_label: <?= json_encode(t('onboarding.step3c_label')) ?>,
            step3c_title: <?= json_encode(t('onboarding.step3c_title')) ?>,
            step3c_subtitle: <?= json_encode(t('onboarding.step3c_subtitle')) ?>,
            step3d_label: <?= json_encode(t('onboarding.step3d_label')) ?>,
            step3d_title: <?= json_encode(t('onboarding.step3d_title')) ?>,
            step3d_subtitle: <?= json_encode(t('onboarding.step3d_subtitle')) ?>,
            success_label: <?= json_encode(t('onboarding.success_label')) ?>,
            btn_next: <?= json_encode(t('onboarding.btn_next')) ?>,
            btn_home: <?= json_encode(t('onboarding.btn_home')) ?>,
            btn_finish: <?= json_encode(t('onboarding.btn_finish')) ?>,
            btn_save_continue: <?= json_encode(t('onboarding.btn_save_continue')) ?>,
            btn_add_another: <?= json_encode(t('onboarding.btn_add_another')) ?>,
            btn_finish_without: <?= json_encode(t('onboarding.btn_finish_without')) ?>,
            loading: <?= json_encode(t('onboarding.loading')) ?>,
            pinfo_invalid_phone: <?= json_encode(t('profile.labels.pinfo_invalid_phone')) ?>,
            pinfo_invalid_data: <?= json_encode(t('profile.labels.pinfo_invalid_data')) ?>,
            error_required_step1: <?= json_encode(t('onboarding.error_required_step1')) ?>,
            error_required_role: <?= json_encode(t('onboarding.error_required_role')) ?>,
            error_required_child: <?= json_encode(t('onboarding.error_required_child')) ?>,
            error_connection: <?= json_encode(t('onboarding.error_connection')) ?>,
            error_save: <?= json_encode(t('onboarding.error_save')) ?>,
            athletes_added_count: <?= json_encode(t('onboarding.athletes_added_count')) ?>,
            error_required_location: <?= json_encode(t('onboarding.error_required_location')) ?>,
            error_invalid_phone: <?= json_encode(t('onboarding.error_invalid_phone')) ?>,
            error_required_phone: <?= json_encode(t('onboarding.error_required_phone')) ?>,
        };
    </script>
    <script src="<?= getVersionedAssetUrl('js/onboarding.js') ?>"></script>
</body>

</html>