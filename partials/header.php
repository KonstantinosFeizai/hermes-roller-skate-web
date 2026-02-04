<?php
// partials/header.php
// Purpose: Global header (meta tags, assets, navigation, language switcher, auth UI).

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Core config + language helper
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/lang.php';

// Default meta values (can be overridden by pages)
$pageTitle = $pageTitle ?? 'Hermes Rollerskate Academy';
$pageDesc = $pageDescription ?? 'Hermes Rollerskate Academy - Learn, practice, and enjoy roller skating with expert guidance.';
$pageKeywords = $pageKeywords ?? 'roller skating, skating classes, Athens, Hermes Rollerskate, skating lessons, skating academy';
$pageOgImage = $pageOgImage ?? asset('photo/logo.webp');
$activePage = $activePage ?? null;

// Auth state for header UI
$isLoggedIn = isset($_SESSION['user_id']);
$currentUsername = $_SESSION['username'] ?? '';
// Get user role if logged in 
$userRole = $_SESSION['user_role'] ?? 'guest';

// Language handling (?lang=en|el stored in session)
$supportedLangs = ['en', 'el'];
$langParam = $_GET['lang'] ?? null;

if ($langParam && in_array($langParam, $supportedLangs, true)) {
    $_SESSION['lang'] = $langParam;
}

// Normalize per-page CSS list
$pageCss = $pageCss ?? [];
if (is_string($pageCss)) {
    $pageCss = [$pageCss];
}

// Normalize per-page scripts list
$pageScripts = $pageScripts ?? [];
if (is_string($pageScripts)) {
    $pageScripts = [$pageScripts];
}

// Helper to mark active nav item
function isActive(?string $activePage, string $slug): string
{
    return $activePage === $slug ? ' active' : '';
}

// Build language switcher URLs based on current path/query
$currentUrl = $_SERVER['REQUEST_URI'];
$path_only = parse_url($currentUrl, PHP_URL_PATH) ?? '/';
$query_only = parse_url($currentUrl, PHP_URL_QUERY);
$currentLang = $_SESSION['lang'] ?? null;
$isGreek = $currentLang ? $currentLang === 'el' : str_contains($path_only, '/gr/');

$base_path = $path_only;
if (!empty($BASE_URL) && str_starts_with($base_path, $BASE_URL)) {
    $base_path = substr($base_path, strlen($BASE_URL));
}
if (str_starts_with($base_path, '/gr/')) {
    $base_path = substr($base_path, 3);
}
$base_path = ltrim($base_path, '/');

$queryParams = [];
if (!empty($query_only)) {
    parse_str($query_only, $queryParams);
}
unset($queryParams['lang']);

$english_query = http_build_query(array_merge($queryParams, ['lang' => 'en']));
$greek_query = http_build_query(array_merge($queryParams, ['lang' => 'el']));

$english_url = asset($base_path) . ($english_query ? '?' . $english_query : '');
$greek_url = asset($base_path) . ($greek_query ? '?' . $greek_query : '');

?>

<!DOCTYPE html>
<html lang="<?= $isGreek ? 'el' : 'en' ?>">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>" />
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>" />

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($pageDesc) ?>" />
    <meta property="og:image" content="<?= htmlspecialchars($pageOgImage) ?>" />
    <meta property="og:type" content="website" />

    <!-- Favicon -->
    <link rel="icon" href="<?= asset('photo/logo.webp') ?>">

    <!-- Global styles -->
    <!--  getVersionedAssetUrl() -->
    <link rel="stylesheet" href="<?= getVersionedAssetUrl('css/navigation.css') ?>">
    <link rel="stylesheet" href="<?= getVersionedAssetUrl('css/modal.css') ?>">
    <link rel="stylesheet" href="<?= getVersionedAssetUrl('css/footer.css') ?>">
    <!-- Auth modal HTML -->
    <?php require_once __DIR__ . '/login_modal.php'; ?>

    <!-- Page-specific CSS -->
    <?php foreach ($pageCss as $css): ?>
        <!-- getVersionedAssetUrl() -->
        <link rel="stylesheet" href="<?= getVersionedAssetUrl($css) ?>">
    <?php endforeach; ?>

    <!-- Fonts / Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        xintegrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=arrow_forward" />


    <!-- Global Scripts -->
    <!--  getVersionedAssetUrl() -->
    <script src="<?= getVersionedAssetUrl('js/carousel.js') ?>" defer></script>
    <script src="<?= getVersionedAssetUrl('js/auth.js') ?>" defer></script>
    <script src="<?= getVersionedAssetUrl(path: 'js/scroll-navbar.js') ?>" defer></script>
    <script src="<?= getVersionedAssetUrl('js/newsletter.js') ?>" defer></script>

    <!-- Page-specific Scripts -->
    <?php foreach ($pageScripts as $script): ?>
        <?php
        $isExternal = str_starts_with($script, 'http');
        $scriptUrl = $isExternal ? htmlspecialchars($script) : getVersionedAssetUrl($script);
        ?>
        <script src="<?= $scriptUrl ?>" defer></script>
    <?php endforeach; ?>

</head>

<body>

    <!-- NAVIGATION -->
    <nav class="navbar" id="navbar">
        <div class="nav-left">
            <a href="<?= asset('') ?>">
                <img src="<?= asset('photo/logo.webp') ?>" alt="Hermes Logo" class="nav-button" />
            </a>
        </div>

        <button class="hamburger" id="hamburger-button" aria-label="Toggle navigation menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>

        <!-- Desktop nav links -->
        <ul class="nav-center">
            <li>
                <a href="<?= asset('whoweare') ?>" class="nav-link<?= isActive($activePage, 'whoweare') ?>">
                    <?= t('header.nav.who_we_are') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('classes') ?>" class="nav-link<?= isActive($activePage, 'classes') ?>">
                    <?= t('header.nav.classes') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('equipment') ?>" class="nav-link<?= isActive($activePage, 'equipment') ?>">
                    <?= t('header.nav.equipment') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('activities') ?>" class="nav-link<?= isActive($activePage, 'activities') ?>">
                    <?= t('header.nav.activities') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('prices-policies') ?>"
                    class="nav-link<?= isActive($activePage, 'prices-policies') ?>">
                    <?= t('header.nav.prices') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('benefits') ?>" class="nav-link<?= isActive($activePage, 'benefits') ?>">
                    <?= t('header.nav.benefits') ?>
                </a>
            </li>
        </ul>

        <!-- Right side: auth + language -->
        <div class="nav-right">
            <!-- Auth section (login or user menu) -->
            <div class="auth-section">
                <?php if ($isLoggedIn): ?>
                    <div class="user-dropdown">
                        <button id="user-menu-button" class="nav-button login-button user-button">
                            <i class="fa-solid fa-user fa-sm"></i>
                            <span class="username-text"><?= htmlspecialchars($currentUsername) ?></span>
                        </button>

                        <div id="user-menu-dropdown-content" class="dropdown-content">

                            <?php if ($userRole === 'admin'): ?>
                                <a href="<?= asset('admin/admin_dashboard') ?>" class="dropdown-item admin-link"><?= t('header.auth.admin_dashboard') ?></a>
                            <?php endif; ?>

                            <a href="<?= asset('user/profile') ?>" class="dropdown-item profile-link"><?= t('header.auth.profile') ?></a>

                            <button id="logout-button" class="dropdown-item logout-link"><?= t('header.auth.logout') ?></button>
                        </div>
                    </div>
                <?php else: ?>
                    <button id="login-modal-open-button" class="nav-button login-button">
                        <i class="fa-solid fa-user fa-sm"></i>
                        <span class="login-text"><?= t('header.auth.login') ?></span>
                    </button>
                <?php endif; ?>
            </div>

            <!-- Language selector -->
            <div class="language-selector">
                <button id="language-button" class="language-button" aria-label="<?= t('header.language.button') ?>">
                    <i class="fa-solid fa-globe"></i>
                </button>
                <div id="language-dropdown" class="language-dropdown">

                    <a href="<?= $english_url ?>" class="lang-option">
                        <?= t('header.language.english') ?>
                        <?php if (!$isGreek): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </a>
                    <a href="<?= $greek_url ?>" class="lang-option">
                        <?= t('header.language.greek') ?>
                        <?php if ($isGreek): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile menu -->
    <div class="mobile-menu" id="mobile-menu">
        <!-- Mobile nav links -->
        <ul class="mobile-menu__links">
            <li>
                <a href="<?= asset('whoweare') ?>" class="nav-link<?= isActive($activePage, 'whoweare') ?>">
                    <?= t('header.nav.who_we_are') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('classes') ?>" class="nav-link<?= isActive($activePage, 'classes') ?>">
                    <?= t('header.nav.classes') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('equipment') ?>" class="nav-link<?= isActive($activePage, 'equipment') ?>">
                    <?= t('header.nav.equipment') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('activities') ?>" class="nav-link<?= isActive($activePage, 'activities') ?>">
                    <?= t('header.nav.activities') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('prices-policies') ?>"
                    class="nav-link<?= isActive($activePage, 'prices-policies') ?>">
                    <?= t('header.nav.prices') ?>
                </a>
            </li>
            <li>
                <a href="<?= asset('benefits') ?>" class="nav-link<?= isActive($activePage, 'benefits') ?>">
                    <?= t('header.nav.benefits') ?>
                </a>
            </li>
        </ul>

        <hr class="footer-separator">

        <!-- Social links -->
        <div class="social-icons">
            <ul>
                <li>
                    <a href="https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/" target="_blank"
                        rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.facebook') ?>">
                        <img src="<?= asset('photo/fb.webp') ?>" alt="Facebook">
                    </a>
                </li>
                <li>
                    <a href="https://www.instagram.com/hermes_rollerskate/" target="_blank" rel="noopener noreferrer"
                        aria-label="<?= t('footer.social_labels.instagram') ?>">
                        <img src="<?= asset('photo/insta.webp') ?>" alt="Instagram">
                    </a>
                </li>
                <li>
                    <a href="https://www.tiktok.com/@hermesrollerskate" target="_blank" rel="noopener noreferrer"
                        aria-label="<?= t('footer.social_labels.tiktok') ?>">
                        <img src="<?= asset('photo/tiktok.webp') ?>" alt="TikTok">
                    </a>
                </li>
                <li>
                    <a href="https://www.youtube.com/channel/UCT8LRFlRVJduUn0uVeDwPZA" target="_blank"
                        rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.youtube') ?>">
                        <img src="<?= asset('photo/youtube.webp') ?>" alt="YouTube">
                    </a>
                </li>
                <li>
                    <a href="https://www.linkedin.com/company/hermes-rollerskate" target="_blank"
                        rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.linkedin') ?>">
                        <img src="<?= asset('photo/linkedin.webp') ?>" alt="LinkedIn">
                    </a>
                </li>
            </ul>
        </div>
        <!-- Help links -->
        <div class="help-links">
            <h3 class="help-heading"><?= t('header.mobile.help') ?></h3>
            <a href="<?= asset('policies#FAQ') ?>" class="nav-link">
                <?= t('header.mobile.help_faq') ?>
            </a>
            <a href="<?= asset('contact') ?>" class="nav-link">
                <?= t('header.mobile.contact') ?>
            </a>
        </div>
    </div>


    <!-- Expose base URL for JS -->
    <script>
        window.BASE_URL = "<?= rtrim(asset(''), '/') ?>/";
    </script>