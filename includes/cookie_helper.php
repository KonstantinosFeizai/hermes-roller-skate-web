<?php
// includes/cookie_helper.php
// Purpose: Διαχείριση συναίνεσης cookies (ανάγνωση, αποθήκευση, έλεγχος).

// ============================================================
// ΣΤΑΘΕΡΕΣ
// ============================================================

// Τρέχουσα έκδοση της Cookie Policy
// Αν αλλάξεις την πολιτική, άλλαξε σε '1.1', '2.0' κλπ
// → οι παλιοί χρήστες θα δουν ξανά το banner
define('COOKIE_CONSENT_VERSION', '1.0');

// Όνομα του cookie που αποθηκεύεται στον browser
define('COOKIE_CONSENT_NAME', 'cookie_consent');

// Διάρκεια ζωής cookie: 6 μήνες
define('COOKIE_CONSENT_EXPIRY', 60 * 60 * 24 * 180);

// ============================================================
// ΑΝΑΓΝΩΣΗ ΣΥΝΑΙΝΕΣΗΣ
// ============================================================

/**
 * Διαβάζει την αποθηκευμένη συναίνεση από το browser cookie.
 * Επιστρέφει array ή null αν δεν υπάρχει / είναι παλιά έκδοση.
 *
 * @return array|null
 */
function getConsent(): ?array
{
    if (empty($_COOKIE[COOKIE_CONSENT_NAME])) {
        return null;
    }

    $data = json_decode($_COOKIE[COOKIE_CONSENT_NAME], true);

    // Αν δεν είναι valid JSON ή λείπει η έκδοση
    if (!is_array($data) || empty($data['version'])) {
        return null;
    }

    // Αν η έκδοση της πολιτικής έχει αλλάξει → ζητάμε ξανά συναίνεση
    if ($data['version'] !== COOKIE_CONSENT_VERSION) {
        return null;
    }

    return $data;
}

/**
 * Ελέγχει αν ο χρήστης έχει ήδη απαντήσει στο banner.
 *
 * @return bool
 */
function hasConsented(): bool
{
    return getConsent() !== null;
}

/**
 * Ελέγχει αν μια συγκεκριμένη κατηγορία έχει εγκριθεί.
 * Χρήση: hasConsentFor('analytics'), hasConsentFor('third_party')
 *
 * @param  string $category  'analytics' | 'third_party'
 * @return bool
 */
function hasConsentFor(string $category): bool
{
    $consent = getConsent();

    if ($consent === null) {
        return false;
    }

    return !empty($consent[$category]);
}

// ============================================================
// ΑΠΟΘΗΚΕΥΣΗ ΣΥΝΑΙΝΕΣΗΣ
// ============================================================

/**
 * Αποθηκεύει τη συναίνεση στο browser cookie.
 * Καλείται από το api/save_consent.php μετά το POST του banner.
 *
 * @param  bool $analytics    Αποδοχή analytics cookies (GA)
 * @param  bool $third_party  Αποδοχή third-party cookies (YouTube, Maps)
 * @return void
 */
function saveConsentCookie(bool $analytics, bool $third_party): void
{
    $data = [
        'version'     => COOKIE_CONSENT_VERSION,
        'necessary'   => true,   // πάντα true
        'analytics'   => $analytics,
        'third_party' => $third_party,
        'timestamp'   => time(),
    ];

    // DEV MODE (XAMPP): secure=false, samesite=Lax
    // PRODUCTION: αλλάζουμε secure=true
    setcookie(
        COOKIE_CONSENT_NAME,
        json_encode($data),
        [
            'expires'  => time() + COOKIE_CONSENT_EXPIRY,
            'path'     => '/',
            'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => false,   // false γιατί το διαβάζει και το JS
            'samesite' => 'Lax',
        ]
    );

    // Κάνε διαθέσιμο αμέσως στο $_COOKIE για το τρέχον request
    $_COOKIE[COOKIE_CONSENT_NAME] = json_encode($data);
}

/**
 * Αποθηκεύει τη συναίνεση στο DB (cookie_consents table).
 * Αν υπάρχει ήδη εγγραφή για το session_id → κάνει UPDATE.
 *
 * @param  PDO    $pdo
 * @param  bool   $analytics
 * @param  bool   $third_party
 * @param  string $sessionId    Από getAnonymousSessionId()
 * @param  int|null $userId     Αν είναι logged in
 * @return void
 */
function saveConsentToDb(
    PDO $pdo,
    bool $analytics,
    bool $third_party,
    string $sessionId,
    ?int $userId = null
): void {
    $ip        = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    // Ελέγχουμε αν υπάρχει ήδη εγγραφή για αυτό το session
    $stmt = $pdo->prepare("
        SELECT id FROM cookie_consents 
        WHERE session_id = :session_id 
        LIMIT 1
    ");
    $stmt->execute([':session_id' => $sessionId]);
    $existing = $stmt->fetch();

    if ($existing) {
        // UPDATE υπάρχουσας εγγραφής
        $stmt = $pdo->prepare("
            UPDATE cookie_consents SET
                analytics       = :analytics,
                third_party     = :third_party,
                consent_version = :version,
                user_id         = :user_id,
                ip_address      = :ip,
                user_agent      = :ua,
                updated_at      = NOW()
            WHERE session_id = :session_id
        ");
    } else {
        // INSERT νέας εγγραφής
        $stmt = $pdo->prepare("
            INSERT INTO cookie_consents 
                (user_id, session_id, analytics, third_party, consent_version, ip_address, user_agent)
            VALUES 
                (:user_id, :session_id, :analytics, :third_party, :version, :ip, :ua)
        ");
    }

    $stmt->execute([
        ':user_id'    => $userId,
        ':session_id' => $sessionId,
        ':analytics'  => (int) $analytics,
        ':third_party' => (int) $third_party,
        ':version'    => COOKIE_CONSENT_VERSION,
        ':ip'         => $ip,
        ':ua'         => $userAgent,
    ]);
}
