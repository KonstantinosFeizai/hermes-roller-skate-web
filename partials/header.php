<?php
// partials/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$pageTitle = $pageTitle ?? 'Hermes Rollerskate Academy';
$pageDesc = $pageDescription ?? 'Hermes Rollerskate Academy - Learn, practice, and enjoy roller skating with expert guidance.';
$pageKeywords = $pageKeywords ?? 'roller skating, skating classes, Athens, Hermes Rollerskate, skating lessons, skating academy';
$pageOgImage = $pageOgImage ?? asset('photo/logo.webp');
$activePage = $activePage ?? null;

$pageCss = $pageCss ?? [];
if (is_string($pageCss)) {
    $pageCss = [$pageCss];
}

$pageScripts = $pageScripts ?? [];
if (is_string($pageScripts)) {
    $pageScripts = [$pageScripts];
}

function isActive(?string $activePage, string $slug): string
{
    return $activePage === $slug ? ' active' : '';
}

// Check the current URL to determine the active language
$currentUrl = $_SERVER['REQUEST_URI'];
$isGreek = str_contains($currentUrl, '/gr/');

$path_only = parse_url($currentUrl, PHP_URL_PATH);
// Remove '/gr/' from the path to get the base page name
$clean_path = str_replace('/gr/', '/', $path_only);
$page_name = basename($clean_path);

// create language switcher URLs
// (root path)
$english_page_uri = $page_name;
if (empty($page_name) || $page_name === '/') {
    $english_page_uri = 'index.php';
}

// (/gr/ path)
$greek_page_uri = 'gr/' . $page_name;
if (empty($page_name) || $page_name === '/') {
    $greek_page_uri = 'gr/index.php';
}

$english_url = asset($english_page_uri);
$greek_url = asset($greek_page_uri);

?>

<!DOCTYPE html>
<html lang="en">

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
    <link rel="stylesheet" href="<?= getVersionedAssetUrl('css/footer.css') ?>">

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
    <script src="<?= getVersionedAssetUrl('js/login-modal.js') ?>" defer></script>
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
            <a href="<?= asset('index.php') ?>">
                <img src="<?= asset('photo/logo.webp') ?>" alt="Hermes Logo" class="nav-button" />
            </a>
        </div>

        <button class="hamburger" id="hamburger-button" aria-label="Toggle navigation menu">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </button>

        <ul class="nav-center">
            <li>
                <a href="<?= asset('whoweare.php') ?>" class="nav-link<?= isActive($activePage, 'whoweare') ?>">
                    Who We Are
                </a>
            </li>
            <li>
                <a href="<?= asset('classes.php') ?>" class="nav-link<?= isActive($activePage, 'classes') ?>">
                    Classes
                </a>
            </li>
            <li>
                <a href="<?= asset('activities.php') ?>" class="nav-link<?= isActive($activePage, 'activities') ?>">
                    Activities
                </a>
            </li>
            <li>
                <a href="<?= asset('prices-policies.php') ?>"
                    class="nav-link<?= isActive($activePage, 'prices-policies') ?>">
                    Prices
                </a>
            </li>
            <li>
                <a href="<?= asset('benefits.php') ?>" class="nav-link<?= isActive($activePage, 'benefits') ?>">
                    Benefits
                </a>
            </li>
        </ul>

        <div class="nav-right">
            <div class="language-selector">
                <button id="language-button" class="language-button" aria-label="Select language">
                    <i class="fa-solid fa-globe"></i>
                </button>
                <div id="language-dropdown" class="language-dropdown">

                    <a href="<?= $english_url ?>" class="lang-option">
                        English
                        <?php if (!$isGreek): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </a>
                    <a href="<?= $greek_url ?>" class="lang-option">
                        Greek
                        <?php if ($isGreek): ?>
                            <i class="fas fa-check"></i>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="mobile-menu" id="mobile-menu">
        <ul class="mobile-menu__links">
            <li>
                <a href="<?= asset('whoweare.php') ?>" class="nav-link<?= isActive($activePage, 'whoweare') ?>">
                    Who We Are
                </a>
            </li>
            <li>
                <a href="<?= asset('classes.php') ?>" class="nav-link<?= isActive($activePage, 'classes') ?>">
                    Classes
                </a>
            </li>
            <li>
                <a href="<?= asset('activities.php') ?>" class="nav-link<?= isActive($activePage, 'activities') ?>">
                    Activities
                </a>
            </li>
            <li>
                <a href="<?= asset('prices-policies.php') ?>"
                    class="nav-link<?= isActive($activePage, 'prices-policies') ?>">
                    Prices
                </a>
            </li>
            <li>
                <a href="<?= asset('benefits.php') ?>" class="nav-link<?= isActive($activePage, 'benefits') ?>">
                    Benefits
                </a>
            </li>
        </ul>

        <hr class="footer-separator">

        <div class="social-icons">
            <ul>
                <li>
                    <a href="https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow us on Facebook">
                        <img src="<?= asset('photo/fb.webp') ?>" alt="Facebook">
                    </a>
                </li>
                <li>
                    <a href="https://www.instagram.com/hermes_rollerskate/" target="_blank" rel="noopener noreferrer"
                        aria-label="Follow us on Instagram">
                        <img src="<?= asset('photo/insta.webp') ?>" alt="Instagram">
                    </a>
                </li>
                <li>
                    <a href="https://www.tiktok.com/@hermesrollerskate" target="_blank" rel="noopener noreferrer"
                        aria-label="Follow us on TikTok">
                        <img src="<?= asset('photo/tiktok.webp') ?>" alt="TikTok">
                    </a>
                </li>
                <li>
                    <a href="https://www.youtube.com/channel/UCT8LRFlRVJduUn0uVeDwPZA" target="_blank"
                        rel="noopener noreferrer" aria-label="Follow us on YouTube">
                        <img src="<?= asset('photo/youtube.webp') ?>" alt="YouTube">
                    </a>
                </li>
                <li>
                    <a href="https://www.linkedin.com/company/hermes-rollerskate" target="_blank"
                        rel="noopener noreferrer" aria-label="Connect with us on LinkedIn">
                        <img src="<?= asset('photo/linkedin.webp') ?>" alt="LinkedIn">
                    </a>
                </li>
            </ul>
        </div>
        <div class="help-links">
            <h3 class="help-heading">Help</h3>
            <a href="<?= asset('policies.php#FAQ') ?>" class="nav-link">
                Help &amp; FAQs
            </a>
            <a href="<?= asset('contact.php') ?>" class="nav-link">
                Contact
            </a>
        </div>
    </div>


    <script>
        window.BASE_URL = "<?= rtrim(asset(''), '/') ?>/";
    </script>