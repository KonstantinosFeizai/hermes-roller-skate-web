<?php
// index.php (Home)

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('home.meta.title');
$pageDescription = t('home.meta.description');
$pageKeywords = t('home.meta.keywords');
$pageCss = [
  'css/homepage.css',
];
// Page scripts
$pageScripts = [
  "https://code.jquery.com/jquery-3.6.0.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/ScrollTrigger.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/Draggable.min.js",
  "js/welcome-overlay.js",
  "js/alerts.js"
];
// Active nav item
$activePage = 'Home';


// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
// One-time alert message (from previous request)
$alert_message = null;
$alert_type = null;

// Check if there is an alert message from a previous page
if (isset($_SESSION['alert_message'])) {
  $alert_message = $_SESSION['alert_message'];
  $alert_type = $_SESSION['alert_type'] ?? 'info'; // default info

  // Clear session so the message is not shown again
  unset($_SESSION['alert_message']);
  unset($_SESSION['alert_type']);
}

// Keep overlay hero text in English to preserve consistent typography.
$enTranslations = require PROJECT_ROOT . 'lang/en.php';
$getEnglishText = static function (string $key, string $fallback = '') use ($enTranslations): string {
  $value = $enTranslations;

  foreach (explode('.', $key) as $segment) {
    if (!is_array($value) || !array_key_exists($segment, $value)) {
      return $fallback !== '' ? $fallback : $key;
    }
    $value = $value[$segment];
  }

  return is_string($value) ? $value : ($fallback !== '' ? $fallback : $key);
};

$overlayTitle = $getEnglishText('home.hero.title', t('home.hero.title'));
$overlaySubtitle = $getEnglishText('home.hero.subtitle', t('home.hero.subtitle'));
?>

<!-- HOME PAGE CONTENT -->
<header class="home-hero">
  <div
    id="welcome-overlay"
    class="animate__animated"
    style="position:fixed;inset:0;z-index:9999;display:flex;flex-direction:column;justify-content:center;align-items:center;background-color:#05070b;">
    <div class="welcome-logo-wrap">
      <svg class="welcome-ring-text" viewBox="-20 -20 540 540" aria-hidden="true" focusable="false">
        <defs>
          <path id="welcome-top-arc" d="M 10 250 A 240 240 0 0 1 490 250" />
          <path id="welcome-bottom-arc" d="M 10 250 A 240 240 0 0 0 490 250" />
        </defs>
        <text class="ring-title ring-enter-top">
          <textPath href="#welcome-top-arc" startOffset="50%" text-anchor="middle">
            <?= htmlspecialchars($overlayTitle) ?>
          </textPath>
        </text>
        <text class="ring-subtitle ring-enter-bottom">
          <textPath href="#welcome-bottom-arc" startOffset="50%" text-anchor="middle">
            <?= htmlspecialchars($overlaySubtitle) ?>
          </textPath>
        </text>
      </svg>
      <img
        class="welcome-logo animate__animated animate__fadeIn"
        src="<?= asset('photo/hermes_logo.png') ?>"
        alt="Hermes Roller Skate logo"
        loading="eager"
        decoding="async">
    </div>
    <h1 id="welcome-text" class="sr-only"><?= htmlspecialchars($overlayTitle) ?></h1>
    <h2 id="welcome-subtext" class="sr-only"><?= htmlspecialchars($overlaySubtitle) ?></h2>
  </div>
</header>
<!-- One-time alert message -->
<?php if ($alert_message): ?>
  <div class="alert alert-<?php echo htmlspecialchars($alert_type); ?>" role="alert">
    <?php echo htmlspecialchars($alert_message); ?>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
<?php endif; ?>

<main>
  <!-- Announcement Section -->
  <section class="announcement">
    <h2>
      <?= htmlspecialchars(t('home.index.announcement.title')) ?>
    </h2>

    <div class="announcement-images">
      <img src="<?= asset('photo/hallowed.webp') ?>" alt="<?= htmlspecialchars(t('home.index.announcement.alts.img1')) ?>"
        loading="lazy">
      <img src="<?= asset('photo/rollerskate.webp') ?>" alt="<?= htmlspecialchars(t('home.index.announcement.alts.img2')) ?>"
        loading="lazy">
      <img src="<?= asset('photo/hallowed2.webp') ?>" alt="<?= htmlspecialchars(t('home.index.announcement.alts.img3')) ?>"
        loading="lazy">
    </div>

    <div class="homepageinfo">
      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.index.announcement.p1')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.index.announcement.p2')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.index.announcement.p3')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.index.announcement.p4')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.index.announcement.p5')) ?>
      </p>

      <a class="button-link"        
        href="https://docs.google.com/forms/d/e/1FAIpQLSeAawGRiqE58WiY_K6jB6JIRDhrlj6ZxK-g9eXLRipInN01IA/viewform"      
        target="_blank" rel="noopener">
        <?= htmlspecialchars(t('home.index.announcement.cta_team')) ?>
      </a>
      <br>
      <a class="button-link" href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09"  
        target="_blank" rel="noopener">
        <?= htmlspecialchars(t('home.index.announcement.cta_private')) ?>
      </a>
      <p class="info-paragraph bold-highlight">
        <?= htmlspecialchars(t('home.index.announcement.winter_schedule')) ?>
      </p>
    </div>
  </section>

  <!-- Weekly Schedule Grid -->

  <!-- Weekly Schedule Grid -->

  <section class="grid-container">


    <!-- Zografou 1 (Saturday & Sunday) -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.zografou,panepistimioupoli') ?></a>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3"><?= htmlspecialchars(t('home.index.schedule.levels.l3')) ?></strong></li>
        <li><span class="time">16:00–17:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.saturday') ?> & <?= t('profile.labels.sunday') ?>
      </div>
    </div>

    <!-- Zografou 2 (Saturday) -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.zografou,polutexneioupoli') ?></a>
      </div>

      <ul class="class-list">
        <li><span class="time">18:30–19:30</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.saturday') ?>
      </div>
    </div>

    <!-- Gerakas (Saturday) -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/Hdjvv418PZGE3nQU8" target="_blank" rel="noopener noreferrer">
          <?= htmlspecialchars(t('home.index.schedule.locations.gerakas')) ?>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">14:00–15:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.saturday') ?>
      </div>
    </div>

    <!-- Vrilissia (Sunday) -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/AbqNkvtueDurwayW8" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.vrilissia') ?>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">14:00–15:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.sunday') ?>
      </div>
    </div>

    <!-- Megalopolis -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/glgYHxkCwQp5NURGv78" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.megalopoli') ?>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.tuesday') ?>
      </div>
    </div>

    <!-- Kalamata -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/AbqNkvtueDurwayW8" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.kalamata') ?>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>
      </ul>

      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.wednesday') ?>
      </div>
    </div>
    <!-- Egaleo -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/mR3nCq1aDrtMANZ69" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.egaleo') ?>
        </a>
      </div>

      <ul class="class-list">
        <li><span class="time">16:00–17:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.wednesday') ?>
      </div>
    </div>
    <!-- OAKA/Marousi -->
    <div class="grid-item">
      <div class="location">
        <a href="https://maps.app.goo.gl/BtULHH8qoyCsTo3v9" target="_blank" rel="noopener noreferrer">
          <?= t('footer.locations.oaka') ?>
        </a>

      </div>

      <ul class="class-list">
        <li><span class="time">10:00–11:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">11:00–12:00</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="day-label">
        <?= t('profile.labels.calendar') ?> <?= t('profile.labels.sunday') ?>
      </div>

  </section>

  <!-- Latest News / Highlights Section -->
  <section class="news-section">
    <div class="news-inner">
      <h2><?= htmlspecialchars(t('home.index.news.title')) ?></h2>
      <p class="news-sub"><?= htmlspecialchars(t('home.index.news.subtitle')) ?></p>

      <div class="highlights">
        <!-- Highlight 1 -->
        <div class="highlight-item">
          <iframe width="560" height="315" src="https://www.youtube.com/embed/hgv8rrNYaxk?si=3RVEwLvqZQVf_k1h" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
          <p class="highlight-desc">
            <?= htmlspecialchars(t('home.index.news.highlight1.desc')) ?>
          </p>
        </div>

        <!-- Highlight 2 -->

      </div>

      <a class="btn-gradient-pill" href="https://www.instagram.com/hermes_rollerskate_academy/" target="_blank" rel="noopener" aria-label="<?= htmlspecialchars(t('home.index.news.instagram_aria')) ?>">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        <span>
          <?= htmlspecialchars(t('home.index.news.instagram_cta')) ?>
        </span>
      </a>
    </div>
  </section>


  <!-- Suggested Products Section -->
  <div class="container">
    <div class="card-wrapper">
      <ul class="card-list">
        <li class="card-item">
          <a href="https://www.instagram.com/direct/t/17850152298606126/" class="card-link">
            <img src="photo/TshirtHermes.webp" alt="<?= htmlspecialchars(t('home.index.merch.card1.alt')) ?>" class="card-image">
            <p class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></p>
            <h2><?= htmlspecialchars(t('home.index.merch.card1.title')) ?></h2>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item">
          <a href="https://www.instagram.com/direct/t/17850152298606126/" class="card-link">
            <img src="photo/TshirtHermida.webp" alt="<?= htmlspecialchars(t('home.index.merch.card2.alt')) ?>" class="card-image">
            <p class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></p>
            <h2><?= htmlspecialchars(t('home.index.merch.card2.title')) ?></h2>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
      </ul>
    </div>
  </div>


  <a href="https://www.instagram.com/hermes_rollerskate_academy/" class="button-link" target="_blank" rel="noopener">
    <?= htmlspecialchars(t('home.merch.cta')) ?>
  </a>

</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>