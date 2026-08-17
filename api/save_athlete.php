<?php
// api/save_athlete.php
// Purpose: INSERT ή UPDATE αθλητή.
//          Αν athlete_id υπάρχει → UPDATE, αλλιώς → INSERT.
//          Γονέας: max 2 athletes. Αθλητής: max 1 (ο εαυτός του).

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// ── Ανάγνωση πεδίων ──────────────────────────────────────────
$athlete_id      = !empty($data['athlete_id']) ? (int)$data['athlete_id'] : null;
$first_name      = trim($data['first_name']  ?? '');
$last_name       = trim($data['last_name']   ?? '');
$birth_date      = !empty($data['birth_date']) ? $data['birth_date'] : null;
$phone           = trim($data['phone']       ?? '');
$region          = trim($data['region']      ?? '');
$location_id     = !empty($data['location_id']) ? (int)$data['location_id'] : null;
$shoe_size       = trim($data['shoe_size']   ?? '');
$shirt_size      = trim($data['shirt_size']  ?? '');
$interest_rides   = !empty($data['interest_rides'])   ? 1 : 0;
$interest_races   = !empty($data['interest_races'])   ? 1 : 0;
$interest_ski     = !empty($data['interest_ski'])     ? 1 : 0;
$interest_skating = !empty($data['interest_skating']) ? 1 : 0;
$interest_hockey  = !empty($data['interest_hockey'])  ? 1 : 0;
$amka            = trim($data['amka'] ?? '') ?: null;
$afm             = trim($data['afm']  ?? '') ?: null;

// ── Validation ───────────────────────────────────────────────
if (empty($first_name) || empty($last_name)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Όνομα και επώνυμο είναι υποχρεωτικά.']);
    exit;
}

try {
    // Βρίσκουμε τον ρόλο του χρήστη
    $stmtUser = $pdo->prepare("SELECT role_type FROM users WHERE id = ?");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();
    $roleType = $user['role_type'] ?? 'none';

    // ── INSERT ───────────────────────────────────────────────
    if (!$athlete_id) {

        // Όριο αθλητών ανά ρόλο
        define('MAX_ATHLETES_PARENT',  2);
        define('MAX_ATHLETES_ATHLETE', 1);

        // Role-aware counting to avoid conflating parent children with self-athlete
        if ($roleType === 'athlete') {
            // Count only self-athletes (parent_id IS NULL)
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM athletes WHERE user_id = ? AND parent_id IS NULL AND is_active = 1");
            $stmtCount->execute([$userId]);
            $count = (int)$stmtCount->fetchColumn();
            $max = MAX_ATHLETES_ATHLETE;
        } else {
            // For parent (and other roles) count children where parent_id = userId
            $stmtCount = $pdo->prepare("SELECT COUNT(*) FROM athletes WHERE parent_id = ? AND is_active = 1");
            $stmtCount->execute([$userId]);
            $count = (int)$stmtCount->fetchColumn();
            $max = $roleType === 'parent' ? MAX_ATHLETES_PARENT : MAX_ATHLETES_ATHLETE;
        }

        // If athlete role and an active self-athlete already exists, return 'exists' so frontend can reuse/edit
        if ($roleType === 'athlete' && $count >= $max) {
            $stmtActive = $pdo->prepare("SELECT id FROM athletes WHERE user_id = ? AND parent_id IS NULL AND is_active = 1 LIMIT 1");
            $stmtActive->execute([$userId]);
            $activeId = $stmtActive->fetchColumn() ?: null;
            if ($activeId) {
                echo json_encode([
                    'status'     => 'exists',
                    'athlete_id' => (int)$activeId,
                    'message'    => 'Υπάρχει ήδη ενεργή κάρτα αθλητή για αυτόν τον λογαριασμό.'
                ]);
                exit;
            }

            // Fallback generic error
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Έχετε ήδη καταχωρήσει τον εαυτό σας ως αθλητή.']);
            exit;
        }

        // parent_id: NULL αν ο ίδιος είναι αθλητής, userId αν είναι γονέας
        $parentId = $roleType === 'parent' ? $userId : null;

        // Για αθλητή (parent_id IS NULL): ψάχνουμε για υπάρχον soft-deleted record
        // Για γονέα: ψάχνουμε αν υπάρχει ήδη ενεργός αθλητής με τα ίδια στοιχεία
        $existingId = null;
        if ($roleType === 'athlete') {
            $stmtExisting = $pdo->prepare("SELECT id FROM athletes WHERE user_id = ? AND parent_id IS NULL AND is_active = 0 ORDER BY id ASC LIMIT 1");
            $stmtExisting->execute([$userId]);
            $existingId = $stmtExisting->fetchColumn() ?: null;
        } elseif ($roleType === 'parent') {
            // Ελέγχουμε αν υπάρχει ήδη ενεργός αθλητής με το ίδιο όνομα
            $stmtExisting = $pdo->prepare("SELECT id FROM athletes WHERE user_id = ? AND parent_id = ? AND is_active = 1 AND first_name = ? AND last_name = ? LIMIT 1");
            $stmtExisting->execute([$userId, $userId, $first_name, $last_name]);
            $existingId = $stmtExisting->fetchColumn() ?: null;
        }

        if ($existingId) {
            // Reactivate existing soft-deleted record OR return existing active athlete
            $stmt = $pdo->prepare("
                UPDATE athletes SET
                    first_name = ?, last_name = ?, birth_date = ?, phone = ?,
                    region = ?, location_id = ?, shoe_size = ?, shirt_size = ?,
                    interest_rides = ?, interest_races = ?, interest_ski = ?,
                    interest_skating = ?, interest_hockey = ?,
                    amka = ?, afm = ?, is_active = 1
                WHERE id = ?
            ");
            $stmt->execute([
                $first_name,
                $last_name,
                $birth_date,
                $phone,
                $region,
                $location_id,
                $shoe_size,
                $shirt_size,
                $interest_rides,
                $interest_races,
                $interest_ski,
                $interest_skating,
                $interest_hockey,
                $amka,
                $afm,
                $existingId,
            ]);

            echo json_encode([
                'status'     => 'success',
                'message'    => 'Ο αθλητής υπάρχει ήδη και ενημερώθηκε!',
                'athlete_id' => $existingId,
            ]);
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO athletes
                    (user_id, parent_id, first_name, last_name, birth_date, phone,
                     region, location_id, shoe_size, shirt_size,
                     interest_rides, interest_races, interest_ski,
                     interest_skating, interest_hockey, amka, afm)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ");
            $stmt->execute([
                $userId,
                $parentId,
                $first_name,
                $last_name,
                $birth_date,
                $phone,
                $region,
                $location_id,
                $shoe_size,
                $shirt_size,
                $interest_rides,
                $interest_races,
                $interest_ski,
                $interest_skating,
                $interest_hockey,
                $amka,
                $afm,
            ]);

            $newId = $pdo->lastInsertId();
            echo json_encode([
                'status'     => 'success',
                'message'    => 'Ο αθλητής προστέθηκε επιτυχώς!',
                'athlete_id' => $newId,
            ]);
        }

        // ── UPDATE ───────────────────────────────────────────────
    } else {
        // Βεβαιωνόμαστε ότι ο athlete ανήκει σε αυτόν τον χρήστη
        $stmtCheck = $pdo->prepare("
            SELECT id FROM athletes 
            WHERE id = ? AND user_id = ? AND is_active = 1
        ");
        $stmtCheck->execute([$athlete_id, $userId]);
        if (!$stmtCheck->fetch()) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε πρόσβαση σε αυτόν τον αθλητή.']);
            exit;
        }

        $stmt = $pdo->prepare("
            UPDATE athletes SET
                first_name       = ?, last_name        = ?, birth_date      = ?,
                phone            = ?, region           = ?, location_id     = ?,
                shoe_size        = ?, shirt_size       = ?,
                interest_rides   = ?, interest_races   = ?, interest_ski    = ?,
                interest_skating = ?, interest_hockey  = ?,
                amka             = ?, afm              = ?
            WHERE id = ? AND user_id = ?
        ");
        $stmt->execute([
            $first_name,
            $last_name,
            $birth_date,
            $phone,
            $region,
            $location_id,
            $shoe_size,
            $shirt_size,
            $interest_rides,
            $interest_races,
            $interest_ski,
            $interest_skating,
            $interest_hockey,
            $amka,
            $afm,
            $athlete_id,
            $userId,
        ]);

        echo json_encode([
            'status'  => 'success',
            'message' => 'Τα στοιχεία του αθλητή ενημερώθηκαν!',
        ]);
    }
} catch (PDOException $e) {
    error_log('save_athlete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
