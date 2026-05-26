<?php
// profile.php
// Purpose: User profile settings and personal info page (requires login).

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'includes/lang.php';

restrict_access(['user', 'admin']);

$user_id = $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("SELECT username, email, role, created_at, first_name, last_name, age, phone, region, role_type FROM users WHERE id = ?");
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

$pageCss = ['css/profile.css'];
require_once PROJECT_ROOT . 'partials/header.php';
?>

<div class="profile-container">
    <div class="profile-card">
        <h2><?= htmlspecialchars(t('profile.title')) ?></h2>

        <!-- Tabs header -->
        <div class="tabs-header">
            <button class="tab-btn active" onclick="openTab('settings')"><?= htmlspecialchars(t('profile.tabs.settings')) ?></button>
            <button class="tab-btn" onclick="openTab('profile')"><?= htmlspecialchars(t('profile.tabs.profile')) ?></button>
            <button class="tab-btn" onclick="openTab('role')"><?= htmlspecialchars(t('profile.tabs.role')) ?></button>
            <button class="tab-btn" onclick="openTab('athletes', this)"><?= htmlspecialchars(t('profile.tabs.athletes')) ?></button>
            <button class="tab-btn" onclick="openTab('my-finance')"><?= htmlspecialchars(t('profile.tabs.finance')) ?></button>
            <button class="tab-btn" onclick="openTab('my-classes')"><?= htmlspecialchars(t('profile.tabs.classes')) ?></button>
        </div>

        <!-- ── Settings tab ──────────────────────────────────── -->
        <div id="settings" class="tab-content active">
            <form id="profileUpdateForm">
                <div class="profile-info-group">
                    <label for="username"><?= htmlspecialchars(t('profile.labels.username')) ?></label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>
                </div>
                <div class="profile-info-group">
                    <label for="email"><?= htmlspecialchars(t('profile.labels.email')) ?></label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>
                </div>
                <div class="profile-info-group">
                    <label><?= htmlspecialchars(t('profile.labels.role')) ?></label>
                    <span class="badge badge-<?= $user_data['role'] ?>">
                        <?= $user_data['role'] === 'admin' ? t('profile.labels.role_admin') : t('profile.labels.role_user') ?>
                    </span>
                </div>
                <div id="profileUpdateMessage" style="margin-top:10px;display:none;text-align:center;"></div>
                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.save_changes')) ?></button>
            </form>

            <hr style="margin:30px 0;border:0;border-top:1px solid #eee;">

            <h3><?= htmlspecialchars(t('profile.labels.change_password')) ?></h3>
            <form id="changePasswordForm">
                <div class="profile-info-group"><input type="password" name="current_password" placeholder="<?= htmlspecialchars(t('profile.labels.current_password')) ?>" required></div>
                <div class="profile-info-group"><input type="password" name="new_password" placeholder="<?= htmlspecialchars(t('profile.labels.new_password')) ?>" required></div>
                <div class="profile-info-group"><input type="password" name="confirm_new_password" placeholder="<?= htmlspecialchars(t('profile.labels.confirm_new_password')) ?>" required></div>
                <div id="passwordChangeMessage" style="margin-top:10px;display:none;text-align:center;"></div>
                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.update_password')) ?></button>
            </form>
        </div>

        <!-- ── Personal info tab ──────────────────────────────── -->
        <div id="profile" class="tab-content">
            <p style="color:#888;margin-bottom:20px;"><?= htmlspecialchars(t('profile.labels.profile_info_hint')) ?></p>
            <form id="personalInfoForm">
                <div class="profile-info-group">
                    <label for="first_name"><?= htmlspecialchars(t('profile.labels.first_name')) ?></label>
                    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($user_data['first_name'] ?? '') ?>">
                </div>
                <div class="profile-info-group">
                    <label for="last_name"><?= htmlspecialchars(t('profile.labels.last_name')) ?></label>
                    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($user_data['last_name'] ?? '') ?>">
                </div>
                <div class="profile-info-group">
                    <label for="age"><?= htmlspecialchars(t('profile.labels.age')) ?></label>
                    <input type="number" id="age" name="age" value="<?= htmlspecialchars($user_data['age'] ?? '') ?>">
                </div>
                <div class="profile-info-group">
                    <label for="phone"><?= htmlspecialchars(t('profile.labels.phone')) ?></label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user_data['phone'] ?? '') ?>">
                </div>
                <div class="profile-info-group">
                    <label for="region"><?= htmlspecialchars(t('profile.labels.region')) ?></label>
                    <input type="text" id="region" name="region" value="<?= htmlspecialchars($user_data['region'] ?? '') ?>">
                </div>
                <div id="personalInfoMessage" style="margin-top:10px;display:none;text-align:center;"></div>
                <button type="submit" class="profile-submit-btn"><?= htmlspecialchars(t('profile.labels.save_profile')) ?></button>
            </form>
        </div>

        <!-- ── Role tab ───────────────────────────────────────── -->
        <div id="role" class="tab-content">
            <p class="role-subtitle"><?= htmlspecialchars(t('profile.labels.role_subtitle')) ?></p>

            <div class="ob-role-grid" style="margin-bottom:16px;">
                <label class="ob-role-card">
                    <div class="ob-role-card-inner">
                        <input type="radio" name="profile_role_type" value="athlete" <?= ($user_data['role_type'] ?? '') === 'athlete' ? 'checked' : '' ?>>
                        <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['athlete']) ?>" alt="" aria-hidden="true">
                        <span class="ob-role-text">
                            <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_athlete_name')) ?></span>
                            <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_athlete_desc')) ?></span>
                        </span>
                    </div>
                </label>
                <label class="ob-role-card">
                    <div class="ob-role-card-inner">
                        <input type="radio" name="profile_role_type" value="parent" <?= ($user_data['role_type'] ?? '') === 'parent' ? 'checked' : '' ?>>
                        <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['parent']) ?>" alt="" aria-hidden="true">
                        <span class="ob-role-text">
                            <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_parent_name')) ?></span>
                            <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_parent_desc')) ?></span>
                        </span>
                    </div>
                </label>
                <label class="ob-role-card">
                    <div class="ob-role-card-inner">
                        <input type="radio" name="profile_role_type" value="coach" <?= ($user_data['role_type'] ?? '') === 'coach' ? 'checked' : '' ?>>
                        <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['coach']) ?>" alt="" aria-hidden="true">
                        <span class="ob-role-text">
                            <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_coach_name')) ?></span>
                            <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_coach_desc')) ?></span>
                        </span>
                    </div>
                </label>
                <label class="ob-role-card">
                    <div class="ob-role-card-inner">
                        <input type="radio" name="profile_role_type" value="none" <?= (($user_data['role_type'] ?? '') === 'none' || ($user_data['role_type'] ?? '') === '') ? 'checked' : '' ?>>
                        <img class="ob-role-icon" src="<?= htmlspecialchars($role_icons['none']) ?>" alt="" aria-hidden="true">
                        <span class="ob-role-text">
                            <span class="ob-role-name"><?= htmlspecialchars(t('profile.labels.role_none_name')) ?></span>
                            <span class="ob-role-desc"><?= htmlspecialchars(t('profile.labels.role_none_desc')) ?></span>
                        </span>
                    </div>
                </label>
            </div>

            <div id="roleMessage" style="margin-bottom:12px;"></div>
            <button class="profile-submit-btn" id="saveRoleBtn" onclick="saveRoleType()"><?= htmlspecialchars(t('profile.labels.save_role')) ?></button>
        </div>

        <!-- ── Athletes tab ───────────────────────────────────── -->
        <div id="athletes" class="tab-content">
            <div id="athletes-list"></div>

            <div id="athlete-form-wrap" style="display:none; margin-top:24px; border-top:1px solid #eee; padding-top:20px;">
                <h4 id="athlete-form-title" class="athlete-form-title"><?= htmlspecialchars(t('profile.labels.athlete_form_add')) ?></h4>
                <input type="hidden" id="pf_athlete_id">

                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_first_name')) ?></label>
                        <input type="text" id="pf_first_name" placeholder="π.χ. Νίκος">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_last_name')) ?></label>
                        <input type="text" id="pf_last_name" placeholder="π.χ. Παπαδόπουλος">
                    </div>
                </div>
                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_birth_date')) ?></label>
                        <input type="date" id="pf_birth_date">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_phone')) ?></label>
                        <input type="tel" id="pf_phone" placeholder="69XXXXXXXX">
                    </div>
                </div>
                <div class="profile-info-group">
                    <label><?= htmlspecialchars(t('profile.labels.athlete_location')) ?></label>
                    <select id="pf_location">
                        <option value=""><?= htmlspecialchars(t('profile.labels.athlete_location_placeholder')) ?></option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>"><?= htmlspecialchars($loc['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="pf-field-row">
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_shoe_size')) ?></label>
                        <input type="text" id="pf_shoe_size" placeholder="π.χ. 38">
                    </div>
                    <div class="profile-info-group">
                        <label><?= htmlspecialchars(t('profile.labels.athlete_shirt_size')) ?></label>
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

                <p class="pf-section-label"><?= htmlspecialchars(t('profile.labels.athlete_interests')) ?></p>
                <div class="pf-interests-row">
                    <label class="pf-interest"><input type="checkbox" id="pf_rides"> <?= htmlspecialchars(t('profile.labels.athlete_interest_rides')) ?></label>
                    <label class="pf-interest"><input type="checkbox" id="pf_races"> <?= htmlspecialchars(t('profile.labels.athlete_interest_races')) ?></label>
                    <label class="pf-interest"><input type="checkbox" id="pf_ski"> <?= htmlspecialchars(t('profile.labels.athlete_interest_ski')) ?></label>
                    <label class="pf-interest"><input type="checkbox" id="pf_skating"> <?= htmlspecialchars(t('profile.labels.athlete_interest_skating')) ?></label>
                    <label class="pf-interest"><input type="checkbox" id="pf_hockey"> <?= htmlspecialchars(t('profile.labels.athlete_interest_hockey')) ?></label>
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
                    <button class="profile-submit-btn" id="saveAthleteBtn" onclick="saveAthlete()"><?= htmlspecialchars(t('profile.labels.athlete_save')) ?></button>
                    <button class="profile-submit-btn pf-btn-secondary" onclick="cancelAthleteForm()"><?= htmlspecialchars(t('profile.labels.athlete_cancel')) ?></button>
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

    </div>
</div>

<script>
    window.BASE_URL = "<?= rtrim(asset(''), '/') ?>/";
    window.USER_ROLE_TYPE = "<?= htmlspecialchars($user_data['role_type'] ?? 'none') ?>";
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
                    'athlete_form_edit'  => '✏️ ' . t('profile.labels.athlete_form_edit'),
                ], JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="<?= getVersionedAssetUrl('js/auth.js') ?>"></script>
<script src="<?= getVersionedAssetUrl('js/profile.js') ?>"></script>

<script>
    function openTab(tabName, triggerEl = null) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById(tabName).classList.add('active');
        const btn = triggerEl || (typeof event !== 'undefined' ? event.currentTarget : null) ||
            document.querySelector(`.tab-btn[onclick*="'${tabName}'"]`);
        if (btn) btn.classList.add('active');

        if (tabName === 'athletes') loadAthletes();
        if (tabName === 'my-finance') loadMyFinance();
        if (tabName === 'my-classes') loadMyClasses();
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (window.location.hash === '#profile') openTab('profile');
        if (window.location.hash === '#athletes') openTab('athletes');
        if (window.location.hash === '#my-finance') openTab('my-finance');
        if (window.location.hash === '#my-classes') openTab('my-classes');
    });

    // ── Finance tab ───────────────────────────────────────────────
    async function loadMyFinance() {
        const el = document.getElementById('myFinanceContent');
        el.innerHTML = '<p class="loading-msg">Φόρτωση...</p>';
        try {
            const res = await fetch('get_my_finance.php');
            const result = await res.json();
            if (result.status !== 'success') {
                el.innerHTML = '<p style="color:red">Σφάλμα.</p>';
                return;
            }
            renderMyFinance(result.data);
        } catch {
            el.innerHTML = '<p style="color:red">Σφάλμα φόρτωσης.</p>';
        }
    }

    function renderMyFinance(data) {
        const el = document.getElementById('myFinanceContent');
        if (!data.length) {
            el.innerHTML = `<p class="pprofile-empty">${PT.no_athletes}</p>`;
            return;
        }
        const typeL = {
            prepaid: PT.type_prepaid,
            free: PT.type_free,
            gift: PT.type_gift
        };
        const methodL = {
            cash: PT.method_cash,
            card: PT.method_card,
            transfer: PT.method_transfer,
            other: PT.method_other
        };

        el.innerHTML = data.map((d, i) => {
            const b = d.balance;
            const rem = parseInt(b.lessons_remaining || 0);
            const cls = rem > 0 ? 'pbal-pos' : rem < 0 ? 'pbal-neg' : 'pbal-zero';

            const payRows = d.payments.length ?
                d.payments.map(p => {
                    const dt = new Date(p.payment_date).toLocaleDateString('el-GR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric'
                    });
                    const isFree = p.payment_type !== 'prepaid';
                    return `<div class="ppr-row">
                        <div class="ppr-top">
                            <span class="ppr-lessons">+${p.lessons_purchased} μαθήματα</span>
                            <span class="ppr-amount">${isFree ? PT.fin_free : Number(p.amount).toFixed(2) + ' €'}</span>
                        </div>
                        <div class="ppr-meta">📅 ${dt} · ${escH(typeL[p.payment_type]||p.payment_type)} · ${escH(methodL[p.payment_method]||p.payment_method)}</div>
                    </div>`;
                }).join('') :
                `<p class="pprofile-empty">${PT.no_payments}</p>`;

            return `
                ${i > 0 ? '<hr class="pprofile-divider">' : ''}
                <div class="pprofile-athlete-block">
                    <h4 class="pprofile-athlete-name">👤 ${escH(d.athlete_name)}</h4>
                    <div class="pprofile-bal-strip">
                        <div class="pbs-item"><span>${PT.purchased}</span><strong>${b.lessons_purchased||0}</strong></div>
                        <div class="pbs-item"><span>${PT.used}</span><strong>${b.lessons_used||0}</strong></div>
                        <div class="pbs-item"><span>${PT.balance}</span><strong class="${cls}">${rem>0?'+':''}${rem}</strong></div>
                        <div class="pbs-item"><span>${PT.total}</span><strong>${Number(b.total_paid||0).toLocaleString('el-GR',{minimumFractionDigits:2})} €</strong></div>
                    </div>
                    <div class="pprofile-pay-list">${payRows}</div>
                </div>`;
        }).join('');
    }

    // ── Classes tab ───────────────────────────────────────────────
    async function loadMyClasses() {
        const el = document.getElementById('myClassesContent');
        el.innerHTML = '<p class="loading-msg">Φόρτωση...</p>';
        try {
            const res = await fetch('get_my_classes.php');
            const result = await res.json();
            if (result.status !== 'success') {
                el.innerHTML = '<p style="color:red">Σφάλμα.</p>';
                return;
            }
            renderMyClasses(result.data);
        } catch {
            el.innerHTML = '<p style="color:red">Σφάλμα φόρτωσης.</p>';
        }
    }

    function renderMyClasses(data) {
        const el = document.getElementById('myClassesContent');
        if (!data.length) {
            el.innerHTML = `<p class="pprofile-empty">${PT.no_athletes}</p>`;
            return;
        }
        const typeIcons = {
            rollers: '🛼',
            iceskate: '⛸️',
            hockey: '🏒',
            ski: '⛷️',
            fitness: '🏋️'
        };
        const statusL = {
            scheduled: PT.status_scheduled,
            completed: PT.status_completed,
            cancelled: PT.status_cancelled
        };
        const statusCls = {
            scheduled: 'pcls-sched',
            completed: 'pcls-done',
            cancelled: 'pcls-cancel'
        };

        function lessonRow(l) {
            const dt = new Date(l.lesson_datetime).toLocaleDateString('el-GR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const tm = new Date(l.lesson_datetime).toLocaleTimeString('el-GR', {
                hour: '2-digit',
                minute: '2-digit'
            });
            const icon = typeIcons[l.lesson_type] || '📋';
            return `<div class="pcls-row">
                <span class="pcls-icon">${icon}</span>
                <div class="pcls-info">
                    <span class="pcls-date">${dt} ${tm}</span>
                    ${l.location_name ? `<span class="pcls-loc">📍 ${escH(l.location_name)}</span>` : ''}
                </div>
                <div class="pcls-right">
                    <span class="pcls-badge ${statusCls[l.status]||''}">${statusL[l.status]||l.status}</span>
                    ${l.attended ? `<span class="pcls-attended">${PT.attended}</span>` : ''}
                </div>
            </div>`;
        }

        el.innerHTML = data.map((d, i) => {
            const upHtml = d.upcoming.length ? d.upcoming.map(lessonRow).join('') : `<p class="pprofile-empty">${PT.no_upcoming}</p>`;
            const pastHtml = d.past.length ? d.past.map(lessonRow).join('') : `<p class="pprofile-empty">${PT.no_history}</p>`;
            return `
                ${i > 0 ? '<hr class="pprofile-divider">' : ''}
                <div class="pprofile-athlete-block">
                    <h4 class="pprofile-athlete-name">👤 ${escH(d.athlete_name)}</h4>
                    <h5 class="pprofile-sub-heading">${PT.upcoming}</h5>
                    ${upHtml}
                    <h5 class="pprofile-sub-heading" style="margin-top:16px;">${PT.history}</h5>
                    ${pastHtml}
                </div>`;
        }).join('');
    }

    function escH(s) {
        return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }
</script>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>