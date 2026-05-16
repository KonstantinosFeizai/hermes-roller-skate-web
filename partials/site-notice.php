<?php
// partials/cookie-banner.php
// Purpose: Cookie consent banner — εμφανίζεται μόνο αν ο χρήστης δεν έχει ήδη απαντήσει.

require_once PROJECT_ROOT . 'includes/cookie_helper.php';
require_once PROJECT_ROOT . 'includes/session_helper.php';

// Αν έχει ήδη δώσει συναίνεση με την τρέχουσα έκδοση → δεν εμφανίζουμε τίποτα
if (hasConsented()) {
    return;
}
?>

<!-- COOKIE CONSENT BANNER -->
<div id="cookie-banner" class="cookie-banner" role="dialog" aria-modal="true"
    aria-label="<?= t('cookies.banner.aria_label') ?>">

    <div class="cookie-banner__inner">

        <!-- Εικονίδιο + Κείμενο -->
        <div class="cookie-banner__text">
            <span class="cookie-banner__icon">🍪</span>
            <div class="cookie-banner__copy">
                <strong class="cookie-banner__title"><?= t('cookies.banner.title') ?></strong>
                <p class="cookie-banner__desc">
                    <?= t('cookies.banner.description') ?>
                    <a href="<?= asset('policies#privacy') ?>" target="_blank" rel="noopener">
                        <?= t('cookies.banner.learn_more') ?>
                    </a>
                </p>
            </div>
        </div>

        <!-- Κουμπιά -->
        <div class="cookie-banner__actions">
            <button id="cookie-accept-all" class="cookie-btn cookie-btn--primary">
                <?= t('cookies.banner.accept_all') ?>
            </button>
            <button id="cookie-reject-all" class="cookie-btn cookie-btn--ghost">
                <?= t('cookies.banner.necessary_only') ?>
            </button>
        </div>

    </div>
</div>