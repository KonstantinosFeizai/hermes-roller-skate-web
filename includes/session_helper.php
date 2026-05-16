<?php
// includes/session_helper.php
// Purpose: Παράγει και διατηρεί ένα μοναδικό anonymous_session_id για κάθε επισκέπτη.
// Χρησιμοποιείται από το cookie_consents table για να αναγνωρίζουμε anonymous χρήστες.

// Βεβαιώνουμε ότι το session είναι ενεργό
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Επιστρέφει το anonymous session ID του τρέχοντος επισκέπτη.
 * Αν δεν υπάρχει, δημιουργεί ένα νέο και το αποθηκεύει στο session.
 *
 * @return string  Το μοναδικό session ID (64 χαρακτήρες hex)
 */
function getAnonymousSessionId(): string
{
    if (empty($_SESSION['anonymous_session_id'])) {
        $_SESSION['anonymous_session_id'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['anonymous_session_id'];
}

/**
 * Αν ο χρήστης κάνει login/εγγραφή, "μεταφέρουμε" τη συναίνεσή του
 * από τον anonymous session στον λογαριασμό του στο DB.
 * Καλείται από το login_handler.php μετά την επιτυχή σύνδεση.
 *
 * @param  PDO $pdo
 * @param  int $userId
 * @return void
 */
function linkConsentToUser(PDO $pdo, int $userId): void
{
    $sessionId = $_SESSION['anonymous_session_id'] ?? null;

    if (!$sessionId) {
        return;
    }

    $stmt = $pdo->prepare("
        UPDATE cookie_consents 
        SET user_id = :user_id 
        WHERE session_id = :session_id 
          AND user_id IS NULL
    ");

    $stmt->execute([
        ':user_id'    => $userId,
        ':session_id' => $sessionId,
    ]);
}
