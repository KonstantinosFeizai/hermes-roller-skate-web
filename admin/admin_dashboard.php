<?php
// admin_dashboard.php
// Purpose: Admin panel UI for accounts, athletes, classes, finance, contacts, and newsletter.
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

// Protection: only admins can access
restrict_access(['admin']);

try {
    // Query: users for Accounts tab
    $stmt = $pdo->query("SELECT id, username, email, role, is_active, first_name, last_name, phone, region, age, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();

    // Query: lessons for Classes tab
    $stmt_lessons = $pdo->query("
    SELECT l.*, COUNT(le.id) as participant_count 
    FROM lessons l 
    LEFT JOIN lesson_enrollments le ON l.id = le.lesson_id 
    GROUP BY l.id 
    ORDER BY l.lesson_datetime DESC
");
    $lessons = $stmt_lessons->fetchAll();
    // Query: contact messages for Contact tab
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Query: unread count badge
    $stmt_unread = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_replied = 0");
    $unread_count = $stmt_unread->fetchColumn();
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
                <input type="text" id="userSearchInput" placeholder="Αναζήτηση...">
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
            </div>

            <table class="user-table">
                <thead id="userTableHeader">
                    <tr>
                        <th data-sort="number">ID <span></span></th>
                        <th data-sort="string">Username <span></span></th>
                        <th data-sort="string">Email <span></span></th>
                        <th data-sort="string">Ρόλος <span></span></th>
                        <th data-sort="string">Status <span></span></th>
                        <th data-sort="date">Ημερ/νία <span></span></th>
                        <th>Ενέργειες</th>
                    </tr>
                </thead>
                <tbody id="accounts-table-body">
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><strong><?php echo strtoupper($user['role']); ?></strong></td>
                            <td>
                                <span class="<?php echo $user['is_active'] ? 'status-confirmed' : 'status-pending'; ?>">
                                    <?php echo $user['is_active'] ? 'Ενεργός' : 'Εκκρεμεί'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="action-btn role-btn" onclick="changeRole(<?php echo $user['id']; ?>, '<?php echo $user['role']; ?>')">Ρόλος</button>
                                <button class="action-btn delete-btn" onclick="deleteUser(<?php echo $user['id']; ?>)">Διαγραφή</button>
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
                <input type="text" id="athleteSearch" onkeyup="searchAthletes()" placeholder="Αναζήτηση αθλητή (Όνομα ή Τηλέφωνο)..."
                    class="input-compact">

                <select id="athleteSort" onchange="sortAthletes()">
                    <option value="none">Ταξινόμηση ανά...</option>
                    <option value="name_asc">Όνομα (Α-Ω)</option>
                    <option value="name_desc">Όνομα (Ω-Α)</option>
                    <option value="age_asc">Ηλικία (Μικρότεροι)</option>
                    <option value="age_desc">Ηλικία (Μεγαλύτεροι)</option>
                </select>
            </div>
            <div class="region-chips">
                <button class="chip active" onclick="filterByRegion('all')">Όλοι</button>
                <button class="chip" onclick="filterByRegion('Μαρούσι')">Μαρούσι</button>
                <button class="chip" onclick="filterByRegion('ΟΑΚΑ')">ΟΑΚΑ</button>
                <button class="chip" onclick="filterByRegion('Σχολείο')">Σχολείο</button>
                <button class="chip" onclick="filterByRegion('ΕΚΠΑ')">ΕΚΠΑ</button>
            </div>

            <table class="user-table" id="athletesTable">
                <thead>
                    <tr>
                        <th>Ονοματεπώνυμο</th>
                        <th>Τηλέφωνο</th>
                        <th>Ηλικία</th>
                        <th>Περιοχή</th>
                        <th>Ενέργειες</th>
                    </tr>
                </thead>
                <tbody id="athletes-table-body">
                    <?php foreach ($users as $user): ?>
                        <?php if ($user['role'] !== 'admin'): ?> <tr class="athlete-row" data-region="<?php echo htmlspecialchars($user['region']); ?>">
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['age'] ?? '-'); ?></td>
                                <td><span class="badge-region"><?php echo htmlspecialchars($user['region'] ?? 'Χωρίς Περιοχή'); ?></span></td>
                                <td>
                                    <button class="action-btn role-btn" onclick="editAthlete(<?php echo $user['id']; ?>)">Επεξεργασία</button>
                                    <button class="action-btn delete-btn btn-spaced"
                                        onclick="deleteAthlete(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>')">
                                        Διαγραφή
                                    </button>
                                </td>
                            </tr>
                        <?php endif; ?>
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
                    <p>Δεν υπάρχουν προγραμματισμένες προπονήσεις.</p>
                <?php else: ?>
                    <?php foreach ($lessons as $lesson): ?>
                        <div class="class-card">
                            <h3><?php echo htmlspecialchars($lesson['title']); ?></h3>
                            <p><strong>Περιοχή:</strong> <?php echo htmlspecialchars($lesson['location']); ?></p>

                            <?php
                            // Format date for display (e.g. 24/01/2026 12:00)
                            $date = new DateTime($lesson['lesson_datetime']);
                            ?>
                            <p><strong>Ημερομηνία:</strong> <?php echo $date->format('d/m/Y'); ?></p>
                            <p><strong>Ώρα:</strong> <?php echo $date->format('H:i'); ?></p>

                            <div class="class-card-footer">
                                <span id="card-count-<?php echo $lesson['id']; ?>" class="class-card-count">
                                    Συμμετοχές: <?php echo $lesson['participant_count']; ?>
                                </span>
                                <button class="action-btn role-btn" onclick="manageClass(<?php echo $lesson['id']; ?>)">Διαχείριση</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div id="finance-tab" class="tab-content">
            <div class="finance-card">
                <div class="finance-header">
                    <h2 class="finance-title">Οικονομική Διαχείριση</h2>
                    <div class="finance-actions">
                        <button class="action-btn btn-primary" onclick="refreshFinanceTable()">
                            🔄 Ανανέωση
                        </button>

                        <a href="export_payments_csv.php" class="action-btn btn-success action-link">
                            📊 Εξαγωγή σε Excel
                        </a>
                    </div>

                </div>
                <div class="finance-search">
                    <input type="text" id="financeSearch" onkeyup="filterFinanceTable()" placeholder="Αναζήτηση αθλητή (Όνομα ή Επώνυμο)..."
                        class="finance-search-input">
                </div>
                <table class="user-table finance-table">
                    <thead>
                        <tr>
                            <th>Αθλητής</th>
                            <th>Πληρωμένα</th>
                            <th>Εκτελεσμένα</th>
                            <th>Υπόλοιπο</th>
                            <th>Ενέργειες</th>
                        </tr>
                    </thead>
                    <tbody id="finance-table-body">
                    </tbody>
                </table>
                <div id="pagination-controls" class="pagination-controls pagination-controls--wide">
                    <button onclick="prevPage()" id="btn-prev" class="pagination-btn">Προηγούμενη</button>
                    <span id="page-info" class="page-info">Σελίδα 1</span>
                    <button onclick="nextPage()" id="btn-next" class="pagination-btn">Επόμενη</button>
                </div>
                <div class="finance-balance-note">
                    <h4>💡 Πώς διαβάζεται το Υπόλοιπο:</h4>
                    <div class="finance-balance-grid">
                        <div class="finance-balance-item">
                            <span class="finance-balance-label positive">Θετικό (+)</span>
                            <span>Ο αθλητής έχει προπληρωμένα μαθήματα "στην κάβα" (π.χ. +3 σημαίνει 3 απομένουν).</span>
                        </div>
                        <div class="finance-balance-item">
                            <span class="finance-balance-label neutral">Μηδέν (0)</span>
                            <span>Ο αθλητής έχει καταναλώσει όλα τα πληρωμένα του μαθήματα. Πρέπει να πληρώσει σήμερα.</span>
                        </div>
                        <div class="finance-balance-item">
                            <span class="finance-balance-label negative">Αρνητικό (-)</span>
                            <span>Ο αθλητής χρωστάει μαθήματα (π.χ. -1 σημαίνει ήρθε σε ένα μάθημα χωρίς να έχει πληρώσει).</span>
                        </div>
                    </div>
                    <hr>
                    <p>
                        * Το υπόλοιπο υπολογίζεται αυτόματα: <strong>Πληρωμένα Μαθήματα</strong> (από καταχωρήσεις) μείον <strong>Παρουσίες</strong> (όπου έχετε τσεκάρει "Ήρθε").
                    </p>
                </div>
            </div>

            <div id="paymentModal" class="modal modal--payment">
                <div class="modal-content modal-content--sm modal-content--centered">
                    <span class="close" onclick="closePaymentModal()">&times;</span>
                    <h3>Προσθήκη Πληρωμής</h3>
                    <p id="paymentStudentName" class="payment-student-name"></p>

                    <div class="payment-form">
                        <label>Ποσό (€):</label>
                        <input type="number" id="payAmount" value="25" class="form-input">

                        <label>Μαθήματα Πακέτου:</label>
                        <input type="number" id="payLessons" value="4" class="form-input">

                        <label>Σημειώσεις (προαιρετικό):</label>
                        <input type="text" id="payNotes" placeholder="π.χ. Αδέρφια, Private" class="form-input">

                        <button class="action-btn btn-success btn-block" onclick="submitPayment()">
                            Καταχώρηση Πληρωμής
                        </button>
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
            <h3>Νέα Καταχώρηση Αθλητή</h3>
            <hr>
            <form id="addAthleteForm" class="form-stack">
                <div class="form-row">
                    <input type="text" name="first_name" placeholder="Όνομα" required class="form-input">
                    <input type="text" name="last_name" placeholder="Επίθετο" required class="form-input">
                </div>
                <input type="text" name="phone" placeholder="Τηλέφωνο Επικοινωνίας" class="form-input">
                <div class="form-row">
                    <input type="number" name="age" placeholder="Ηλικία" class="form-input">
                    <select name="region" required class="form-input">
                        <option value="">Επιλογή Περιοχής</option>
                        <option value="Μαρούσι">Μαρούσι</option>
                        <option value="ΟΑΚΑ">ΟΑΚΑ</option>
                        <option value="Σχολείο">Σχολείο</option>
                        <option value="ΕΚΠΑ">ΕΚΠΑ</option>
                    </select>
                </div>
                <button type="submit" class="action-btn btn-success btn-block">Αποθήκευση</button>
                <form id="addAthleteForm">
                    <input type="hidden" id="athlete_id" name="athlete_id">

                    <h3 id="modalTitle">Νέα Καταχώρηση Αθλητή</h3>
                </form>
            </form>
            <div id="addAthleteMessage" class="form-message"></div>
        </div>
    </div>

    <!-- Class Modal -->
    <div id="addClassModal" class="modal modal--add-class">
        <div class="modal-content modal-content--sm modal-content--centered">
            <h3>Δημιουργία Νέας Προπόνησης</h3>
            <form id="addClassForm">
                <div class="form-group">
                    <label>Τίτλος Τμήματος:</label><br>
                    <input type="text" name="title" placeholder="π.χ. Αρχάριοι Α1" required class="form-input">
                </div>
                <div class="form-group">
                    <label>Περιοχή:</label><br>
                    <select name="location" required class="form-input">
                        <option value="Μαρούσι">Μαρούσι</option>
                        <option value="ΟΑΚΑ">ΟΑΚΑ</option>
                        <option value="Σχολείο">Σχολείο</option>
                        <option value="ΕΚΠΑ">ΕΚΠΑ</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ημερομηνία & Ώρα:</label><br>
                    <input type="datetime-local" name="lesson_datetime" required class="form-input">
                </div>
                <div class="form-actions">
                    <button type="button" class="action-btn btn-muted" onclick="closeAddClassModal()">Άκυρο</button>
                    <button type="submit" class="action-btn btn-success">Δημιουργία</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Manage Class Modal -->
    <div id="manageClassModal" class="modal modal--manage">
        <div class="modal-content modal-content--lg">
            <span class="close" onclick="closeManageClassModal()">&times;</span>

            <div class="modal-header-row">
                <div id="editableHeader">
                    <div class="modal-title-group">
                        <h2 id="modalClassTitle" class="modal-title"></h2>
                        <span class="icon-edit" onclick="toggleEditMode(true)" title="Επεξεργασία">✏️</span>
                    </div>
                    <p id="modalClassDetails" class="modal-subtitle"></p>
                </div>

                <button class="btn-danger-outline" onclick="deleteCurrentLesson()">
                    <span>🗑</span> Διαγραφή
                </button>
            </div>

            <div id="editFieldsContainer" class="edit-fields">
                <div class="form-grid">
                    <div>
                        <label class="form-label">Τίτλος:</label>
                        <input type="text" id="editTitle" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Περιοχή:</label>
                        <select id="editLocation" class="form-input">
                            <option value="Μαρούσι">Μαρούσι</option>
                            <option value="ΟΑΚΑ">ΟΑΚΑ</option>
                            <option value="Σχολείο">Σχολείο</option>
                            <option value="ΕΚΠΑ">ΕΚΠΑ</option>
                        </select>
                    </div>
                    <div class="form-grid-span">
                        <label class="form-label">Ημερομηνία & Ώρα:</label>
                        <input type="datetime-local" id="editDatetime" class="form-input">
                    </div>
                </div>
                <div class="form-actions">
                    <button class="action-btn btn-muted" onclick="toggleEditMode(false)">Άκυρο</button>
                    <button class="action-btn btn-primary" onclick="saveClassChanges()">Αποθήκευση</button>
                </div>
            </div>

            <hr class="divider">

            <div class="dual-column">
                <div class="dual-column-left">
                    <h3>Προσθήκη Αθλητών</h3>
                    <input type="text" id="memberSearch" placeholder="Αναζήτηση αθλητή..." class="form-input">
                    <div id="suggestedAthletes" class="list-box">
                        <p class="muted-text">Φόρτωση προτάσεων...</p>
                    </div>
                </div>

                <div>
                    <h3>Συμμετέχοντες (<span id="participantCount">0</span>)</h3>
                    <div id="currentParticipants" class="list-scroll">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- finance tab , athlete card modal -->
    <div id="athleteHistoryModal" class="modal modal--history">
        <div class="modal-content modal-content--xl">
            <span class="close" onclick="closeHistoryModal()">&times;</span>

            <h2 id="historyStudentName" class="history-title">Καρτέλα Αθλητή</h2>
            <p id="historySummary" class="history-summary"></p>

            <div class="history-grid">
                <div>
                    <h4 class="history-heading history-heading--primary">🗓️ Ιστορικό Παρουσιών</h4>
                    <div id="attendanceList" class="history-list"></div>
                </div>

                <div>
                    <h4 class="history-heading history-heading--success">💰 Ιστορικό Πληρωμών</h4>
                    <div id="paymentsList" class="history-list"></div>
                </div>
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

        // Αν θέλεις να φορτώνουν τα οικονομικά με το που ανοίγει η σελίδα (αν είναι το ενεργό tab)
        if (localStorage.getItem('activeTab') === 'finance-tab') {
            refreshFinanceTable();
        }
    });
</script>

<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>