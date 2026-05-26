<?php
// admin_dashboard.php
// Purpose: Admin panel UI for accounts, athletes, classes, finance, contacts, and newsletter.
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

// Protection: only admins can access
restrict_access(['admin']);

try {
    // Query: users for Accounts tab
    $stmt = $pdo->query("SELECT id, username, email, role, role_type, is_active, first_name, last_name, phone, region, age, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();

    // Query: lessons for Classes tab
    $stmt_lessons = $pdo->query("
        SELECT l.id, l.title, l.lesson_type, l.location_id, l.lesson_datetime,
               l.weather_condition, l.temperature, l.status, l.notes,
               loc.name AS location_name,
               COUNT(la.athlete_id) AS participant_count
        FROM lessons l
        LEFT JOIN locations loc ON l.location_id = loc.id
        LEFT JOIN lesson_athletes la ON l.id = la.lesson_id
        GROUP BY l.id
        ORDER BY l.lesson_datetime DESC
    ");
    $lessons = $stmt_lessons->fetchAll();
    // Query: contact messages for Contact tab
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Query: posts for Posts tab 
    $stmt_posts = $pdo->query("
    SELECT 
        bp.*, 
        u.first_name, 
        u.last_name,
        GROUP_CONCAT(c.name SEPARATOR ', ') AS category_names
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        LEFT JOIN post_categories pc ON bp.id = pc.post_id
        LEFT JOIN categories c ON pc.category_id = c.id
        GROUP BY bp.id
        ORDER BY bp.created_at DESC
    ");
    $posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);
    // Query: unread count badge
    $stmt_unread = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_replied = 0");
    $unread_count = $stmt_unread->fetchColumn();

    // Query: athletes for Athletes tab
    $stmt_athletes = $pdo->query("
        SELECT a.id, a.user_id, a.parent_id, a.first_name, a.last_name, a.birth_date, a.phone,
               a.location_id, a.shoe_size, a.shirt_size,
               a.interest_rides, a.interest_races, a.interest_ski,
               a.interest_skating, a.interest_hockey,
               a.amka, a.afm,
               l.name AS location_name,
               u.username AS linked_username,
               CONCAT(p.first_name, ' ', p.last_name) AS parent_full_name,
               p.phone AS parent_phone,
               p.email AS parent_email
        FROM athletes a
        LEFT JOIN locations l ON a.location_id = l.id
        LEFT JOIN users u ON a.user_id = u.id
        LEFT JOIN users p ON a.parent_id = p.id
        WHERE a.is_active = 1
        ORDER BY a.last_name ASC, a.first_name ASC
    ");
    $athletes = $stmt_athletes->fetchAll();

    // Query: locations for filter chips + dropdown
    $stmt_locs = $pdo->query("SELECT id, name FROM locations WHERE is_active = 1 ORDER BY sort_order ASC");
    $locations = $stmt_locs->fetchAll();
} catch (PDOException $e) {
    die("Σφάλμα βάσης: " . $e->getMessage());
}

// Include necessary CSS and JS files
$pageCss = ['css/admin_dashboard.css'];
$pageScripts = [
    'js/accounts.js',
    'js/ui-manager.js',
    'js/athletes.js',
    'js/lessons.js',
    'js/finance.js',
    'js/contact-admin.js',
    'js/table-labels.js',
    'js/newsletter-admin.js'
];

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<div class="admin-wrapper">
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <h3>Admin Panel</h3>
        </div>
        <ul class="sidebar-menu">
            <li class="active" onclick="showTab(event, 'accounts-tab')">Λογαριασμοί</li>
            <li onclick="showTab(event, 'athletes-tab')">Αθλητές</li>
            <li onclick="showTab(event, 'classes-tab')">Τμήματα</li>
            <li onclick="showTab(event, 'posts-tab')">Άρθρα</li>
            <li onclick="showTab(event, 'finance-tab')">Οικονομικά</li>
            <li onclick="showTab(event, 'contact-tab')"><a href="#contact-tab" class="contact-link">
                    Επικοινωνία
                    <?php if ($unread_count > 0): ?>
                        <span class="nav-badge"><?php echo $unread_count; ?></span>
                    <?php endif; ?>
                </a></li>
            <li onclick="showTab(event, 'newsletter-tab')" id="newsletter-tab-link">Newsletter</li>
        </ul>
    </nav>

    <main class="admin-main-content">

        <div id="accounts-tab" class="tab-content active">
            <h2>Διαχείριση Λογαριασμών</h2>
            <p>Έλεγχος πρόσβασης και ρόλων χρηστών.</p>

            <div class="admin-actions-bar">
                <input type="text" id="userSearchInput" placeholder="Αναζήτηση (όνομα, email, username)...">
                <select id="roleFilter">
                    <option value="all">Όλοι οι Ρόλοι</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
                <select id="statusFilter">
                    <option value="all">Κατάσταση</option>
                    <option value="active">Ενεργός</option>
                    <option value="inactive">Εκκρεμεί</option>
                </select>
                <a href="export_users_csv.php" class="action-btn btn-success action-link">📥 Export CSV</a>
            </div>

            <table class="user-table">
                <thead id="userTableHeader">
                    <tr>
                        <th data-sort="number">ID <span></span></th>
                        <th data-sort="string">Χρήστης <span></span></th>
                        <th data-sort="string">Email <span></span></th>
                        <th data-sort="string">Τηλέφωνο <span></span></th>
                        <th data-sort="string">Ρόλος <span></span></th>
                        <th data-sort="string">Status <span></span></th>
                        <th data-sort="date">Εγγραφή <span></span></th>
                        <th>Ενέργειες</th>
                    </tr>
                </thead>
                <tbody id="accounts-table-body">
                    <?php foreach ($users as $user): ?>
                        <?php
                        $userData = json_encode([
                            'id'         => $user['id'],
                            'username'   => $user['username'],
                            'email'      => $user['email'],
                            'first_name' => $user['first_name'] ?? '',
                            'last_name'  => $user['last_name'] ?? '',
                            'phone'      => $user['phone'] ?? '',
                            'region'     => $user['region'] ?? '',
                            'age'        => $user['age'] ?? '',
                            'role'       => $user['role'],
                            'role_type'  => $user['role_type'] ?? '',
                            'is_active'  => (int)$user['is_active'],
                            'created_at' => $user['created_at'],
                        ]);
                        $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                        ?>
                        <tr data-user="<?php echo htmlspecialchars($userData, ENT_QUOTES); ?>">
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <?php if ($fullName): ?>
                                    <span class="user-fullname"><?php echo htmlspecialchars($fullName); ?></span><br>
                                <?php endif; ?>
                                <span class="user-username">@<?php echo htmlspecialchars($user['username']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?? '—'); ?></td>
                            <td>
                                <span class="role-badge role-<?php echo $user['role']; ?>">
                                    <?php echo strtoupper($user['role']); ?>
                                </span>
                                <?php
                                $rtLabels = ['athlete' => 'Αθλητής', 'parent' => 'Γονέας', 'coach' => 'Προπονητής'];
                                $rt = $user['role_type'] ?? '';
                                if (isset($rtLabels[$rt])): ?>
                                    <span class="role-type-badge role-type-<?php echo $rt; ?>"><?php echo $rtLabels[$rt]; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="<?php echo $user['is_active'] ? 'status-confirmed' : 'status-pending'; ?>">
                                    <?php echo $user['is_active'] ? 'Ενεργός' : 'Εκκρεμεί'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="action-btn role-btn" onclick="openUserProfile(this); event.stopPropagation()">👤 Προφίλ</button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>); event.stopPropagation()">Διαγραφή</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="paginationControls" class="pagination-controls"></div>
        </div>

        <div id="athletes-tab" class="tab-content">
            <div class="tab-header">
                <h2>Διαχείριση Αθλητών</h2>
                <button class="action-btn btn-success" onclick="openAddAthleteModal()">+ Προσθήκη Αθλητή</button>
            </div>
            <div class="table-controls table-controls-inline">
                <input type="text" id="athleteSearch" oninput="filterAthletes()" placeholder="Αναζήτηση αθλητή (Όνομα ή Τηλέφωνο)..." class="input-compact">
                <select id="athleteSort" onchange="sortAthletes()">
                    <option value="none">Ταξινόμηση ανά...</option>
                    <option value="name_asc">Όνομα (Α-Ω)</option>
                    <option value="name_desc">Όνομα (Ω-Α)</option>
                    <option value="birth_asc">Ηλικία (Νεότεροι)</option>
                    <option value="birth_desc">Ηλικία (Μεγαλύτεροι)</option>
                    <option value="loc_asc">Περιοχή (Α-Ω)</option>
                </select>
            </div>
            <div class="region-chips">
                <button class="chip active" onclick="filterByRegion(0)">Όλοι</button>
                <?php foreach ($locations as $loc): ?>
                    <button class="chip" onclick="filterByRegion(<?php echo (int)$loc['id']; ?>)"><?php echo htmlspecialchars($loc['name']); ?></button>
                <?php endforeach; ?>
                <button class="chip" onclick="filterByRegion(-1)">Χωρίς Περιοχή</button>
            </div>

            <table class="user-table" id="athletesTable">
                <thead>
                    <tr>
                        <th>Ονοματεπώνυμο</th>
                        <th>Τηλέφωνο</th>
                        <th>Ημ. Γέννησης</th>
                        <th>Περιοχή</th>
                        <th>Λογαριασμός</th>
                        <th>Ενέργειες</th>
                    </tr>
                </thead>
                <tbody id="athletes-table-body">
                    <?php foreach ($athletes as $a):
                        $aData = json_encode([
                            'id'               => $a['id'],
                            'first_name'       => $a['first_name'],
                            'last_name'        => $a['last_name'],
                            'birth_date'       => $a['birth_date'],
                            'phone'            => $a['phone'],
                            'location_id'      => $a['location_id'],
                            'location_name'    => $a['location_name'],
                            'shoe_size'        => $a['shoe_size'],
                            'shirt_size'       => $a['shirt_size'],
                            'interest_rides'   => (bool)$a['interest_rides'],
                            'interest_races'   => (bool)$a['interest_races'],
                            'interest_ski'     => (bool)$a['interest_ski'],
                            'interest_skating' => (bool)$a['interest_skating'],
                            'interest_hockey'  => (bool)$a['interest_hockey'],
                            'amka'             => $a['amka'],
                            'afm'              => $a['afm'],
                            'linked_username'  => $a['linked_username'],
                            'parent_id'        => $a['parent_id'],
                            'parent_full_name' => $a['parent_full_name'],
                            'parent_phone'     => $a['parent_phone'],
                            'parent_email'     => $a['parent_email'],
                        ]);
                    ?>
                        <tr class="athlete-row"
                            data-athlete="<?php echo htmlspecialchars($aData); ?>"
                            data-location-id="<?php echo (int)$a['location_id']; ?>"
                            data-location-name="<?php echo htmlspecialchars($a['location_name'] ?? ''); ?>">
                            <td><?php echo htmlspecialchars($a['first_name'] . ' ' . $a['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($a['phone'] ?? '—'); ?></td>
                            <td><?php echo $a['birth_date'] ? htmlspecialchars($a['birth_date']) : '—'; ?></td>
                            <td>
                                <?php if ($a['location_name']): ?>
                                    <span class="badge-region"><?php echo htmlspecialchars($a['location_name']); ?></span>
                                <?php else: ?>
                                    <span class="badge-region badge-region--none">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($a['linked_username']): ?>
                                    <span class="status-confirmed">@<?php echo htmlspecialchars($a['linked_username']); ?></span>
                                <?php else: ?>
                                    <span class="status-pending">Χωρίς λογαριασμό</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="action-btn btn-info"
                                    onclick="openAthleteProfile(this)">👤 Προφίλ</button>
                                <button class="action-btn delete-btn btn-spaced"
                                    onclick="deleteAthlete(<?php echo $a['id']; ?>, '<?php echo htmlspecialchars(addslashes($a['first_name'] . ' ' . $a['last_name'])); ?>')">
                                    🗑 Διαγραφή
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div id="athletesPagination" class="pagination-controls"></div>
        </div>

        <div id="classes-tab" class="tab-content">
            <div class="tab-header">
                <h2>Διαχείριση Τμημάτων & Προπονήσεων</h2>
                <button class="action-btn btn-success" onclick="openAddClassModal()">+ Νέα Προπόνηση</button>
            </div>

            <div id="classes-container" class="classes-container">
                <?php if (empty($lessons)): ?>
                    <p class="empty-state">Δεν υπάρχουν προγραμματισμένες προπονήσεις.</p>
                <?php else: ?>
                    <?php
                    $typeLabels = [
                        'rollers'  => ['label' => 'Rollers',   'icon' => '🛼'],
                        'iceskate' => ['label' => 'Ice Skate', 'icon' => '⛸️'],
                        'hockey'   => ['label' => 'Hockey',    'icon' => '🏒'],
                        'ski'      => ['label' => 'Ski',       'icon' => '⛷️'],
                        'fitness'  => ['label' => 'Fitness',   'icon' => '🏋️'],
                    ];
                    $statusLabels = [
                        'scheduled'  => ['label' => 'Προγραμματισμένη', 'class' => 'status-scheduled'],
                        'completed'  => ['label' => 'Ολοκληρώθηκε',    'class' => 'status-completed'],
                        'cancelled'  => ['label' => 'Ακυρώθηκε',       'class' => 'status-cancelled'],
                    ];
                    $weatherIcons = [
                        'sunny'   => '☀️',
                        'cloudy' => '☁️',
                        'rainy' => '🌧️',
                        'snowy'   => '❄️',
                        'windy'  => '💨',
                        'foggy' => '🌫️',
                    ];
                    foreach ($lessons as $lesson):
                        $dt       = new DateTime($lesson['lesson_datetime']);
                        $typeInfo = $typeLabels[$lesson['lesson_type']] ?? ['label' => $lesson['lesson_type'], 'icon' => '📋'];
                        $stInfo   = $statusLabels[$lesson['status']]     ?? ['label' => $lesson['status'], 'class' => 'status-scheduled'];
                        $lData    = json_encode([
                            'id'                => $lesson['id'],
                            'title'             => $lesson['title'],
                            'lesson_type'       => $lesson['lesson_type'],
                            'location_id'       => $lesson['location_id'],
                            'location_name'     => $lesson['location_name'],
                            'lesson_datetime'   => $lesson['lesson_datetime'],
                            'weather_condition' => $lesson['weather_condition'],
                            'temperature'       => $lesson['temperature'],
                            'notes'             => $lesson['notes'],
                            'status'            => $lesson['status'],
                        ]);
                    ?>
                        <div class="class-card lesson-type-<?php echo $lesson['lesson_type']; ?>"
                            data-lesson="<?php echo htmlspecialchars($lData, ENT_QUOTES); ?>">
                            <div class="class-card-header">
                                <div class="class-card-type">
                                    <span class="lesson-type-badge lesson-type-<?php echo $lesson['lesson_type']; ?>">
                                        <?php echo $typeInfo['icon'] . ' ' . $typeInfo['label']; ?>
                                    </span>
                                    <span class="lesson-status-badge <?php echo $stInfo['class']; ?>">
                                        <?php echo $stInfo['label']; ?>
                                    </span>
                                </div>
                                <button class="card-delete-btn" onclick="deleteLesson(<?php echo $lesson['id']; ?>)" title="Διαγραφή">✕</button>
                            </div>

                            <?php if ($lesson['title']): ?>
                                <h3 class="class-card-title"><?php echo htmlspecialchars($lesson['title']); ?></h3>
                            <?php endif; ?>

                            <div class="class-card-meta">
                                <div class="meta-row">
                                    <span class="meta-icon">📅</span>
                                    <span><?php echo $dt->format('d/m/Y'); ?></span>
                                    <span class="meta-sep">·</span>
                                    <span class="meta-icon">🕐</span>
                                    <span><?php echo $dt->format('H:i'); ?></span>
                                </div>
                                <?php if ($lesson['location_name']): ?>
                                    <div class="meta-row">
                                        <span class="meta-icon">📍</span>
                                        <span><?php echo htmlspecialchars($lesson['location_name']); ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($lesson['weather_condition'] || $lesson['temperature'] !== null): ?>
                                    <div class="meta-row">
                                        <span class="meta-icon"><?php echo $weatherIcons[$lesson['weather_condition']] ?? '🌤️'; ?></span>
                                        <span>
                                            <?php
                                            $parts = [];
                                            if ($lesson['weather_condition']) $parts[] = ucfirst($lesson['weather_condition']);
                                            if ($lesson['temperature'] !== null) $parts[] = $lesson['temperature'] . '°C';
                                            echo implode(', ', $parts);
                                            ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="class-card-footer">
                                <span class="class-card-count" id="card-count-<?php echo $lesson['id']; ?>">
                                    👥 <?php echo $lesson['participant_count']; ?> αθλητές
                                </span>
                                <div class="card-actions">
                                    <button class="action-btn btn-edit-sm" onclick="editLesson(this)">✏️ Επεξεργασία</button>
                                    <button class="action-btn btn-info" onclick="manageClass(<?php echo $lesson['id']; ?>)">👥 Αθλητές</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── Create / Edit Lesson Modal ─────────────────── -->
        <div id="addClassModal" class="modal-overlay" style="display:none;">
            <div class="modal-box modal-box-md">
                <div class="modal-header">
                    <h3 id="classModalTitle">Νέα Προπόνηση</h3>
                    <button class="modal-close-btn" onclick="closeAddClassModal()">✕</button>
                </div>
                <div class="modal-body">
                    <form id="addClassForm">
                        <input type="hidden" id="cf_lesson_id">

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Τύπος Προπόνησης</label>
                                <select id="cf_lesson_type" class="form-control" required>
                                    <option value="rollers">🛼 Rollers</option>
                                    <option value="iceskate">⛸️ Ice Skate</option>
                                    <option value="hockey">🏒 Hockey</option>
                                    <option value="ski">⛷️ Ski</option>
                                    <option value="fitness">🏋️ Fitness</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Τοποθεσία</label>
                                <select id="cf_location_id" class="form-control">
                                    <option value="">— Επιλογή —</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?php echo $loc['id']; ?>"><?php echo htmlspecialchars($loc['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Τίτλος / Περιγραφή <span class="form-hint">(προαιρετικό)</span></label>
                            <input type="text" id="cf_title" class="form-control" placeholder="π.χ. Προπόνηση Αρχαρίων">
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Ημερομηνία &amp; Ώρα</label>
                                <input type="datetime-local" id="cf_datetime" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Κατάσταση</label>
                                <select id="cf_status" class="form-control">
                                    <option value="scheduled">Προγραμματισμένη</option>
                                    <option value="completed">Ολοκληρώθηκε</option>
                                    <option value="cancelled">Ακυρώθηκε</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Καιρός</label>
                                <select id="cf_weather" class="form-control">
                                    <option value="">— —</option>
                                    <option value="sunny">☀️ Ηλιοφάνεια</option>
                                    <option value="cloudy">☁️ Συννεφιά</option>
                                    <option value="rainy">🌧️ Βροχή</option>
                                    <option value="snowy">❄️ Χιόνι</option>
                                    <option value="windy">💨 Αέρας</option>
                                    <option value="foggy">🌫️ Ομίχλη</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Θερμοκρασία (°C)</label>
                                <input type="number" id="cf_temperature" class="form-control" placeholder="π.χ. 18" min="-30" max="50" step="0.5">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Σημειώσεις <span class="form-hint">(προαιρετικό)</span></label>
                            <textarea id="cf_notes" class="form-control" rows="2" placeholder="Επιπλέον πληροφορίες..."></textarea>
                        </div>

                        <div id="classFormMessage" style="display:none;" class="form-message"></div>

                        <div class="modal-footer-btns">
                            <button type="button" class="action-btn btn-secondary" onclick="closeAddClassModal()">Άκυρο</button>
                            <button type="submit" class="action-btn btn-success">Αποθήκευση</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── Manage Class Modal (Athletes + Attendance) ─── -->
        <div id="manageClassModal" class="modal-overlay" style="display:none;">
            <div class="modal-box modal-box-lg">
                <div class="modal-header">
                    <div>
                        <h3 id="manageClassTitle">Διαχείριση Προπόνησης</h3>
                        <p id="manageClassSubtitle" class="modal-subtitle"></p>
                    </div>
                    <button class="modal-close-btn" onclick="closeManageClassModal()">✕</button>
                </div>
                <div class="modal-body manage-class-body">

                    <!-- Left: search + add athletes -->
                    <div class="manage-col manage-col-search">
                        <h4>Προσθήκη Αθλητή</h4>
                        <div class="athlete-search-wrap">
                            <input type="text" id="athleteSearchInput"
                                placeholder="Αναζήτηση αθλητή..."
                                oninput="searchAthletesForLesson()"
                                class="form-control">
                        </div>
                        <div id="athleteSearchResults" class="athlete-search-results"></div>
                    </div>

                    <!-- Right: enrolled athletes + attendance -->
                    <div class="manage-col manage-col-enrolled">
                        <h4>Εγγεγραμμένοι Αθλητές <span id="enrolledCount" class="count-badge">0</span></h4>
                        <div id="enrolledAthletesList" class="enrolled-list"></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="posts-tab" class="tab-content">
            <div class="tab-header">
                <h2>Διαχείριση Άρθρων</h2>
                <a href="<?= asset('admin/add_post') ?>" class="action-btn btn-success">+ Νέο Άρθρο</a>
            </div>

            <?php if (empty($posts)): ?>
                <p>Δεν υπάρχουν άρθρα.</p>
            <?php else: ?>
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Τίτλος</th>
                            <th>Ημερομηνία</th>
                            <th>Δημοσίευση</th>
                            <th>Γλώσσα</th>
                            <th>Translation ID</th>
                            <th>Συντάκτης</th>
                            <th>Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td><?php echo $post['id']; ?></td>
                                <td><?php
                                    $rawTitle = trim(strip_tags($post['title'] ?? ''));
                                    if ($rawTitle === '') {
                                        echo '';
                                    } else {
                                        $words = preg_split('/\s+/u', $rawTitle);
                                        if (count($words) > 5) {
                                            echo htmlspecialchars(implode(' ', array_slice($words, 0, 5)) . '...');
                                        } else {
                                            echo htmlspecialchars($rawTitle);
                                        }
                                    }
                                    ?></td>
                                <td><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></td>
                                <td>
                                    <?php
                                    if (!empty($post['is_published'])) {
                                        $pubDate = !empty($post['published_at']) ? $post['published_at'] : $post['created_at'];
                                        echo date('d/m/Y', strtotime($pubDate));
                                    } else {
                                        echo 'Not published yet';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($post['language'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($post['translation_id'] ?? '-'); ?></td>
                                <td>
                                    <?php
                                    if (!empty($post['first_name'])) {
                                        echo htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
                                    } else {
                                        echo '<span style="color: #999; font-style: italic;">No Author</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a class="action-btn" href="<?= asset('admin/edit_post') ?>?id=<?php echo $post['id']; ?>">Επεξεργασία</a>
                                    <a class="action-btn delete-btn" href="<?= asset('admin/delete_post') ?>?id=<?php echo $post['id']; ?>">Διαγραφή</a>
                                    <a class="action-btn" target="_blank" href="<?= asset('post') ?>?slug=<?php echo htmlspecialchars($post['slug']); ?>">Προβολή</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div id="finance-tab" class="tab-content">
            <div class="fin-header">
                <h2 class="fin-title">Οικονομικά</h2>
                <div class="fin-header-actions">
                    <button class="action-btn btn-primary" onclick="refreshFinanceTab()">🔄 Ανανέωση</button>
                    <button class="action-btn btn-secondary" onclick="openMonthlyReportModal()">📅 Μηνιαία Αναφορά</button>
                    <a href="export_payments_csv.php" class="action-btn btn-success action-link">📊 Export CSV</a>
                </div>
            </div>

            <!-- Monthly summary bar -->
            <div class="fin-summary-bar" id="finSummaryBar">
                <div class="fin-stat">
                    <span class="fin-stat-label">Εισπράξεις μήνα</span>
                    <span class="fin-stat-val" id="finMonthRevenue">—</span>
                </div>
                <div class="fin-stat">
                    <span class="fin-stat-label">Πακέτα μήνα</span>
                    <span class="fin-stat-val" id="finMonthLessons">—</span>
                </div>
                <div class="fin-stat">
                    <span class="fin-stat-label">Αθλητές με χρέος</span>
                    <span class="fin-stat-val fin-stat-debt" id="finDebtCount">—</span>
                </div>
                <div class="fin-stat">
                    <span class="fin-stat-label">Θετικό υπόλοιπο</span>
                    <span class="fin-stat-val fin-stat-ok" id="finCreditCount">—</span>
                </div>
            </div>

            <!-- Location filter -->
            <div class="fin-search-bar">
                <select id="financeLocationFilter" onchange="filterFinanceCards()" class="form-control fin-location-filter">
                    <option value="">Όλες οι τοποθεσίες</option>
                </select>
            </div>

            <!-- Athlete balance cards -->
            <div id="financeCardsGrid" class="fin-cards-grid">
                <p class="loading-msg">Φόρτωση...</p>
            </div>
        </div>

        <!-- ── Add Payment Modal ──────────────────────── -->
        <div id="paymentModal" class="modal-overlay" style="display:none;">
            <div class="modal-box modal-box-md">
                <div class="modal-header">
                    <div>
                        <h3>Προσθήκη Πληρωμής</h3>
                        <p id="paymentAthleteLabel" class="modal-subtitle"></p>
                    </div>
                    <button class="modal-close-btn" onclick="closePaymentModal()">✕</button>
                </div>
                <div class="modal-body">
                    <form id="paymentForm">
                        <input type="hidden" id="pf_athlete_id">

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Τύπος Πληρωμής</label>
                                <select id="pf_type" class="form-control" onchange="onPayTypeChange()">
                                    <option value="prepaid">💳 Προπληρωμή</option>
                                    <option value="free">🎁 Δωρεάν</option>
                                    <option value="gift">🎀 Δώρο</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Τρόπος Πληρωμής</label>
                                <select id="pf_method" class="form-control">
                                    <option value="cash">💵 Μετρητά</option>
                                    <option value="card">💳 Κάρτα</option>
                                    <option value="transfer">🏦 Τραπεζική Μεταφορά</option>
                                    <option value="other">Άλλο</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Αριθμός Μαθημάτων</label>
                                <input type="number" id="pf_lessons" class="form-control"
                                    value="4" min="1" max="100" oninput="calcPricePerLesson()">
                            </div>
                            <div class="form-group" id="pf_amount_group">
                                <label>Αξία (€) <span id="pf_price_hint" class="form-hint"></span></label>
                                <input type="number" id="pf_amount" class="form-control"
                                    value="100" min="0" step="0.5" oninput="calcPricePerLesson()">
                            </div>
                        </div>

                        <div class="form-row-2">
                            <div class="form-group">
                                <label>Ημερομηνία</label>
                                <input type="date" id="pf_date" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Σημειώσεις <span class="form-hint">(προαιρετικό)</span></label>
                                <input type="text" id="pf_notes" class="form-control" placeholder="π.χ. Αδέρφια, Private">
                            </div>
                        </div>

                        <div id="paymentFormMessage" style="display:none;" class="form-message"></div>

                        <div class="modal-footer-btns">
                            <button type="button" class="action-btn btn-secondary" onclick="closePaymentModal()">Άκυρο</button>
                            <button type="submit" class="action-btn btn-success">💾 Αποθήκευση</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ── Athlete History Modal ──────────────────── -->
        <div id="athleteHistoryModal" class="modal-overlay" style="display:none;">
            <div class="modal-box modal-box-lg">
                <div class="modal-header">
                    <div>
                        <h3 id="historyAthleteName">Καρτέλα Αθλητή</h3>
                        <p id="historySummaryLine" class="modal-subtitle"></p>
                    </div>
                    <button class="modal-close-btn" onclick="closeHistoryModal()">✕</button>
                </div>
                <div class="modal-body">
                    <!-- Balance strip -->
                    <div id="historyBalanceStrip" class="history-balance-strip"></div>

                    <div class="history-cols">
                        <div class="history-col">
                            <h4 class="history-col-title">💳 Πληρωμές</h4>
                            <div id="historyPaymentsList" class="history-list"></div>
                        </div>
                        <div class="history-col">
                            <h4 class="history-col-title">✅ Παρουσίες</h4>
                            <div id="historyAttendanceList" class="history-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Monthly Report Modal ──────────────────── -->
        <div id="monthlyReportModal" class="modal-overlay" style="display:none;">
            <div class="modal-box modal-box-md">
                <div class="modal-header">
                    <h3>📅 Μηνιαία Αναφορά (12 μήνες)</h3>
                    <button class="modal-close-btn" onclick="closeMonthlyReportModal()">✕</button>
                </div>
                <div class="modal-body">
                    <div id="monthlyReportContent">
                        <p class="loading-msg">Φόρτωση...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- contact tab -->
        <div id="contact-tab" class="tab-content">
            <div class="tab-header">
                <h2>Μηνύματα Επικοινωνίας</h2>
            </div>

            <div class="table-controls">
                <input type="text" id="contactSearch" onkeyup="filterMessages()" placeholder="Αναζήτηση (Όνομα, Email, Θέμα)..."
                    class="input-compact">

                <select id="contactCategoryFilter" onchange="filterMessages()">
                    <option value="all">Όλες οι Κατηγορίες</option>
                    <option value="general">General Inquiry</option>
                    <option value="classes">Classes</option>
                    <option value="merchandise">Merchandise</option>
                    <option value="partnerships">Partnerships</option>
                    <option value="feedback">Feedback</option>
                    <option value="other">Other</option>
                </select>

                <select id="contactStatusFilter" onchange="filterMessages()">
                    <option value="all">Όλες οι Καταστάσεις</option>
                    <option value="1">Απαντήθηκε</option>
                    <option value="0">Εκκρεμεί</option>
                </select>
            </div>

            <table class="user-table" id="contactTable">
                <thead>
                    <tr>
                        <th>Ημερομηνία</th>
                        <th>Ονοματεπώνυμο</th>
                        <th>Email / Τηλέφωνο</th>
                        <th>Κατηγορία</th>
                        <th>Θέμα</th>
                        <th>Κατάσταση</th>
                        <th>Ενέργειες</th>
                    </tr>
                </thead>
                <tbody id="contact-table-body">
                    <?php
                    // Υποθέτουμε ότι έχεις κάνει ένα query: $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetchAll();
                    foreach ($messages as $msg):
                    ?>
                        <tr class="message-row" data-category="<?php echo htmlspecialchars($msg['category']); ?>" data-replied="<?php echo $msg['is_replied']; ?>">
                            <td><?php echo date('d/m/y H:i', strtotime($msg['created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($msg['name'] . ' ' . $msg['surname']); ?></td>
                            <td><?php echo htmlspecialchars($msg['email']); ?></td>
                            <td><?php echo htmlspecialchars($msg['category']); ?></td>
                            <td><strong><?php echo htmlspecialchars(mb_strimwidth($msg['subject'], 0, 10, "...")); ?></strong></td>

                            <td>
                                <?php if ($msg['is_replied']): ?>
                                    <span class="status-badge status-replied">Απαντήθηκε</span>
                                <?php else: ?>
                                    <span class="status-badge status-pending">Εκκρεμεί</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <button class="action-btn" onclick='viewMessage(<?php echo json_encode($msg); ?>)'>Προβολή</button>
                                <button class="action-btn delete-btn" onclick="deleteMessage(<?php echo $msg['id']; ?>)">Διαγραφή</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="contactPagination" class="pagination-controls"></div>
        </div>

        <div id="newsletter-tab" class="tab-content">
            <div class="tab-header">
                <h2>Newsletter</h2>
                <button class="action-btn btn-primary" id="refreshNewsletterBtn">🔄 Ανανέωση</button>
            </div>

            <div class="table-controls">
                <input type="text" id="newsletterSearch" onkeyup="filterNewsletterTable()" placeholder="Αναζήτηση email..."
                    class="input-compact">
                <span class="status-badge status-replied" id="newsletterCount">Σύνολο: 0</span>
            </div>

            <table class="user-table" id="newsletterTable">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Ημερομηνία Εγγραφής</th>
                    </tr>
                </thead>
                <tbody id="newsletter-table-body"></tbody>
            </table>

            <div class="finance-card" style="margin-top: 20px;">
                <div class="finance-header">
                    <h2 class="finance-title">Αποστολή Email σε Όλους</h2>
                </div>

                <form id="newsletterSendForm" class="form-stack">
                    <div class="form-group">
                        <label>Θέμα:</label>
                        <input type="text" name="subject" id="newsletterSubject" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label>Μήνυμα:</label>
                        <textarea name="message" id="newsletterMessage" rows="6" class="form-input" required></textarea>
                    </div>
                    <button type="submit" class="action-btn btn-success" id="newsletterSendBtn">Αποστολή</button>
                    <div id="newsletterSendStatus" class="form-message" style="display:none;"></div>
                </form>
            </div>
        </div>
    </main>
    <div id="addAthleteModal" class="modal modal--add-athlete">
        <div class="modal-content modal-content--md modal-content--centered">
            <span class="close" onclick="closeAddAthleteModal()">&times;</span>
            <h3 id="athleteModalTitle">Νέα Καταχώρηση Αθλητή</h3>
            <hr>
            <form id="addAthleteForm" class="form-stack">
                <input type="hidden" id="af_athlete_id">
                <div class="form-row">
                    <input type="text" id="af_first_name" placeholder="Όνομα *" required class="form-input">
                    <input type="text" id="af_last_name" placeholder="Επίθετο *" required class="form-input">
                </div>
                <div class="form-row">
                    <input type="date" id="af_birth_date" class="form-input" placeholder="Ημ. Γέννησης">
                    <input type="text" id="af_phone" placeholder="Τηλέφωνο" class="form-input">
                </div>
                <div class="form-row">
                    <select id="af_location" class="form-input">
                        <option value="">— Περιοχή —</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?php echo (int)$loc['id']; ?>"><?php echo htmlspecialchars($loc['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="text" id="af_shoe_size" placeholder="Νούμερο παπουτσιού" class="form-input">
                </div>
                <div class="form-row">
                    <select id="af_shirt_size" class="form-input">
                        <option value="">— Μέγεθος μπλούζας —</option>
                        <option value="XS">XS</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                        <option value="XL">XL</option>
                        <option value="XXL">XXL</option>
                    </select>
                </div>
                <div class="form-group" style="margin-top:8px;">
                    <label class="profile-info-label" style="margin-bottom:6px;display:block;">Ενδιαφέροντα</label>
                    <div style="display:flex;flex-wrap:wrap;gap:10px;">
                        <label><input type="checkbox" id="af_rides"> 🛼 Βόλτες</label>
                        <label><input type="checkbox" id="af_races"> 🏁 Αγώνες</label>
                        <label><input type="checkbox" id="af_ski"> ⛷️ Σκι</label>
                        <label><input type="checkbox" id="af_skating"> ⛸️ Πατινάζ</label>
                        <label><input type="checkbox" id="af_hockey"> 🏒 Χόκεϊ</label>
                    </div>
                </div>
                <div class="form-row" style="margin-top:8px;">
                    <input type="text" id="af_amka" placeholder="ΑΜΚΑ" class="form-input">
                    <input type="text" id="af_afm" placeholder="ΑΦΜ" class="form-input">
                </div>
                <div id="addAthleteMessage" class="form-message" style="display:none;margin-top:8px;"></div>
                <div class="form-actions" style="margin-top:12px;">
                    <button type="button" class="action-btn btn-muted" onclick="closeAddAthleteModal()">Άκυρο</button>
                    <button type="submit" class="action-btn btn-success">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Athlete Profile Modal -->
    <div id="athleteProfileModal" class="modal" style="display:none;">
        <div class="modal-content modal-content--md">
            <span class="close" onclick="closeAthleteProfileModal()">&times;</span>

            <div class="profile-modal-header">
                <div class="profile-avatar" id="apAvatar">?</div>
                <div>
                    <div class="profile-modal-name" id="apFullName">—</div>
                    <div class="profile-modal-username" id="apAccount">—</div>
                </div>
            </div>

            <hr class="divider">

            <div class="profile-info-grid ap-info-grid">
                <div class="profile-info-item">
                    <span class="profile-info-label">📞 Τηλέφωνο</span>
                    <span id="apPhone" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🎂 Ημ. Γέννησης</span>
                    <span id="apBirth" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">📍 Περιοχή</span>
                    <span id="apLocation" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">👟 Νούμερο παπουτσιού</span>
                    <span id="apShoe" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">👕 Μέγεθος μπλούζας</span>
                    <span id="apShirt" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🪪 ΑΜΚΑ</span>
                    <span id="apAmka" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🧾 ΑΦΜ</span>
                    <span id="apAfm" class="profile-info-value ap-value"></span>
                </div>
                <div class="profile-info-item profile-info-item--full">
                    <span class="profile-info-label">🏅 Ενδιαφέροντα</span>
                    <span id="apInterests" class="profile-info-value ap-value"></span>
                </div>
            </div>

            <!-- Parent section (shown only when parent exists) -->
            <div id="apParentSection" style="display:none;">
                <hr class="divider">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;">
                    <span style="font-weight:700;font-size:0.88rem;color:#374151;">👨‍👧 Στοιχεία Γονέα</span>
                    <button class="action-btn btn-info" style="padding:3px 10px;font-size:0.78rem;" onclick="toggleParentInfo()">Εμφάνιση</button>
                </div>
                <div id="apParentInfo" style="display:none;" class="profile-info-grid ap-info-grid">
                    <div class="profile-info-item">
                        <span class="profile-info-label">👤 Όνομα</span>
                        <span id="apParentName" class="profile-info-value ap-value"></span>
                    </div>
                    <div class="profile-info-item">
                        <span class="profile-info-label">📞 Τηλέφωνο</span>
                        <span id="apParentPhone" class="profile-info-value ap-value"></span>
                    </div>
                    <div class="profile-info-item profile-info-item--full">
                        <span class="profile-info-label">📧 Email</span>
                        <span id="apParentEmail" class="profile-info-value ap-value"></span>
                    </div>
                </div>
            </div>

            <hr class="divider">

            <div class="profile-modal-actions">
                <button class="action-btn role-btn" onclick="editAthleteFromProfile()">✏️ Επεξεργασία</button>
            </div>
        </div>
    </div>


    <!-- User Profile Modal -->
    <div id="userProfileModal" class="modal modal--profile">
        <div class="modal-content modal-content--md modal-content--centered">
            <span class="close" onclick="closeUserProfileModal()">&times;</span>

            <div class="profile-modal-header">
                <div class="profile-avatar" id="profileAvatar"></div>
                <div class="profile-modal-title">
                    <h2 id="profileFullName" class="profile-name"></h2>
                    <span id="profileUsernameDisplay" class="profile-username-tag"></span>
                </div>
            </div>

            <hr class="divider">

            <!-- View Mode -->
            <div id="profileInfoGrid" class="profile-info-grid">
                <div class="profile-info-item">
                    <span class="profile-info-label">📧 Email</span>
                    <span id="profileEmail" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">📞 Τηλέφωνο</span>
                    <span id="profilePhone" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">📍 Περιοχή</span>
                    <span id="profileRegion" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🎂 Ηλικία</span>
                    <span id="profileAge" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🎭 Ρόλος</span>
                    <span id="profileRoleDisplay" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">🏅 Τύπος</span>
                    <span id="profileRoleTypeDisplay" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item">
                    <span class="profile-info-label">✅ Κατάσταση</span>
                    <span id="profileStatusDisplay" class="profile-info-value"></span>
                </div>
                <div class="profile-info-item profile-info-item--full">
                    <span class="profile-info-label">📅 Εγγραφή</span>
                    <span id="profileCreatedAt" class="profile-info-value"></span>
                </div>
            </div>

            <!-- Edit Mode (hidden by default) -->
            <div id="profileEditForm" style="display:none">
                <div class="profile-info-grid">
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editFirstName">Όνομα</label>
                        <input type="text" id="editFirstName" class="form-input form-input--sm" placeholder="Όνομα">
                    </div>
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editLastName">Επίθετο</label>
                        <input type="text" id="editLastName" class="form-input form-input--sm" placeholder="Επίθετο">
                    </div>
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editEmail">📧 Email</label>
                        <input type="email" id="editEmail" class="form-input form-input--sm" placeholder="email@example.com">
                    </div>
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editPhone">📞 Τηλέφωνο</label>
                        <input type="text" id="editPhone" class="form-input form-input--sm" placeholder="69xxxxxxxx">
                    </div>
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editRegion">📍 Περιοχή</label>
                        <select id="editRegion" class="form-input form-input--sm">
                            <option value="">— Χωρίς Περιοχή —</option>
                            <option value="Μαρούσι">Μαρούσι</option>
                            <option value="ΟΑΚΑ">ΟΑΚΑ</option>
                            <option value="Σχολείο">Σχολείο</option>
                            <option value="ΕΚΠΑ">ΕΚΠΑ</option>
                        </select>
                    </div>
                    <div class="profile-info-item">
                        <label class="profile-info-label" for="editAge">🎂 Ηλικία</label>
                        <input type="number" id="editAge" class="form-input form-input--sm" placeholder="π.χ. 25" min="1" max="99">
                    </div>
                </div>
                <div id="profileEditMessage" class="form-message" style="display:none; margin-top:10px;"></div>
            </div>

            <hr class="divider">

            <!-- Athletes Section -->
            <div id="profileAthletesSection">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                    <span style="font-weight:700;font-size:0.9rem;color:#374151;">🏅 Καρτέλες Αθλητή</span>
                    <button class="action-btn btn-info" style="padding:4px 12px;font-size:0.8rem;" onclick="loadUserAthletes()">Εμφάνιση</button>
                </div>
                <div id="profileAthletesList"></div>
            </div>

            <hr class="divider">

            <!-- View Mode Actions -->
            <div id="profileViewActions" class="profile-modal-actions">
                <button class="action-btn role-btn" onclick="enterProfileEditMode()">✏️ Επεξεργασία</button>
                <button class="action-btn btn-info" onclick="sendPasswordResetFromProfile()">🔑 Reset Password</button>
                <button id="profileToggleRoleBtn" class="action-btn btn-secondary" onclick="changeRoleFromProfile()">🔄 Ρόλος</button>
                <button id="profileToggleStatusBtn" class="action-btn btn-warning" onclick="toggleUserStatusFromProfile()">⏸ Απενεργοποίηση</button>
                <button id="profileDeleteBtn" class="action-btn delete-btn" onclick="deleteUserFromProfile()">🗑 Διαγραφή</button>
            </div>

            <!-- Edit Mode Actions (hidden by default) -->
            <div id="profileEditActions" class="profile-modal-actions" style="display:none">
                <button class="action-btn btn-success" onclick="saveProfileEdit()">💾 Αποθήκευση</button>
                <button class="action-btn btn-muted" onclick="exitProfileEditMode()">✗ Άκυρο</button>
            </div>
        </div>
    </div>

    <!-- Contact modal -->
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class=" close" onclick="closeMessageModal()">&times;</span>

            <h2 id="modalSubject" class="modal-subject">
                Θέμα Μηνύματος
            </h2>
            <div id="replyBadge" class="reply-badge"></div>
            <hr>

            <div id="modalDetails" class="modal-details"></div>

            <div class="message-box">
                <p id="modalMessageContent" class="message-text"></p>
            </div>

            <div id="previousReplySection" class="reply-box">
                <strong class="reply-strong">Η απάντησή σας:</strong>
                <p id="modalReplyContent" class="reply-text"></p>
                <small id="modalReplyDate" class="reply-date"></small>
            </div>

            <div id="replyFormSection" class="reply-form">
                <h3>Σύνταξη Απάντησης</h3>
                <textarea id="replyText" class="reply-textarea" placeholder="Γράψτε την απάντησή σας εδώ..."></textarea>
                <div id="sendSpinner" class="send-spinner">✉️ Αποστολή... παρακαλώ περιμένετε.</div>
                <button id="confirmSendBtn" class="action-btn btn-success">Οριστική Αποστολή</button>
            </div>

            <div id="modalActionButtons" class="modal-actions">
                <button id="openReplyBtn" class="action-btn btn-primary">Απάντηση</button>
                <button onclick="closeMessageModal()" class="action-btn btn-muted">Κλείσιμο</button>
            </div>
        </div>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Τρέχουμε τη συνάρτηση από το ui-manager.js για τα Tabs
        // showTabFromStorage();

        // Auto-update lesson statuses on page load
        fetch(BASE_URL + "admin/update_lesson_statuses.php", {
                method: "POST"
            })
            .then(r => r.json())
            .then(data => {
                if (data.updated > 0) {
                    location.reload();
                }
            })
            .catch(() => {});

        // Αν θέλεις να φορτώνουν τα οικονομικά με το που ανοίγει η σελίδα (αν είναι το ενεργό tab)
        if (localStorage.getItem('activeTab') === 'finance-tab') {
            refreshFinanceTable();
        }
    });
</script>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>