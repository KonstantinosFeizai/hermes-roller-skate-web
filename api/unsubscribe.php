<?php
// api/unsubscribe.php
// Purpose: Χειρίζεται το unsubscribe link από τα newsletter emails.
//          Δέχεται GET με ?token=xxxxx και κάνει is_active=0.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// ── Διαβάζουμε το token από το URL ──────────────────────────
$token = trim($_GET['token'] ?? '');

// ── Βασική σελίδα (HTML response — όχι JSON) ────────────────
// Ο χρήστης έρχεται από link σε email, πρέπει να δει σελίδα
$pageTitle       = t('newsletter.unsubscribe.page_title');
$pageDescription = '';
$pageKeywords    = '';
$pageCss         = [];
$activePage      = '';

// ── Validation ───────────────────────────────────────────────
if (empty($token) || strlen($token) !== 64) {
    $status  = 'error';
    $message = t('newsletter.unsubscribe.invalid_token');
} else {
    try {
        // Βρίσκουμε τον subscriber με αυτό το token
        $stmt = $pdo->prepare("
            SELECT id, email, is_active 
            FROM newsletter_subscribers 
            WHERE unsubscribe_token = ? 
            LIMIT 1
        ");
        $stmt->execute([$token]);
        $subscriber = $stmt->fetch();

        if (!$subscriber) {
            // Token δεν υπάρχει
            $status  = 'error';
            $message = t('newsletter.unsubscribe.invalid_token');
        } elseif ($subscriber['is_active'] == 0) {
            // Έχει ήδη κάνει unsubscribe
            $status  = 'already';
            $message = t('newsletter.unsubscribe.already_done');
        } else {
            // ✅ Κάνουμε unsubscribe → is_active = 0
            $update = $pdo->prepare("
                UPDATE newsletter_subscribers 
                SET is_active = 0 
                WHERE id = ?
            ");
            $update->execute([$subscriber['id']]);

            $status  = 'success';
            $message = t('newsletter.unsubscribe.success');
        }
    } catch (PDOException $e) {
        error_log('Unsubscribe error: ' . $e->getMessage());
        $status  = 'error';
        $message = t('newsletter.unsubscribe.error');
    }
}

// ── HTML Response ─────────────────────────────────────────────
require_once PROJECT_ROOT . 'partials/header.php';
?>

<main style="min-height: 60vh; display: flex; align-items: center; justify-content: center;">
    <div style="max-width: 480px; width: 90%; text-align: center; padding: 2rem;">

        <?php if ($status === 'success'): ?>
            <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">
                <?= htmlspecialchars(t('newsletter.unsubscribe.success_title')) ?>
            </h1>
            <p style="color: #555; line-height: 1.6;">
                <?= htmlspecialchars($message) ?>
            </p>

        <?php elseif ($status === 'already'): ?>
            <div style="font-size: 3rem; margin-bottom: 1rem;">ℹ️</div>
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">
                <?= htmlspecialchars(t('newsletter.unsubscribe.already_title')) ?>
            </h1>
            <p style="color: #555; line-height: 1.6;">
                <?= htmlspecialchars($message) ?>
            </p>

        <?php else: ?>
            <div style="font-size: 3rem; margin-bottom: 1rem;">❌</div>
            <h1 style="font-size: 1.5rem; margin-bottom: 1rem;">
                <?= htmlspecialchars(t('newsletter.unsubscribe.error_title')) ?>
            </h1>
            <p style="color: #555; line-height: 1.6;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <a href="<?= asset('index') ?>"
            style="display: inline-block; margin-top: 2rem; padding: 10px 24px;
                  background: #e63946; color: #fff; border-radius: 6px;
                  text-decoration: none; font-weight: 600;">
            <?= htmlspecialchars(t('newsletter.unsubscribe.back_home')) ?>
        </a>

    </div>
</main>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>