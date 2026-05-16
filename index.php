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
  "js/alerts.js",
  "js/schedule.js"
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
<div id="welcome-overlay">
  <div class="welcome-logo-wrap">

    <?php include __DIR__ . '/photo/welcome-intro.php'; ?>

    <div id="logo-appear-wrapper" class="animate__animated animate__fadeIn">
      <img src="<?= asset('photo/hermes_logo.png') ?>" id="logo-center">
    </div>

  </div>
</div>
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

    </div>
  </section>

  <!-- Weekly Schedule -->

  <script>
    window.SCHED_LABELS = {
      /* levels */
      basic: '<?= addslashes(t('home.index.schedule.levels.l1',   'Basic')) ?>',
      advanced: '<?= addslashes(t('home.index.schedule.levels.l2',   'Advanced')) ?>',
      beginners: '<?= addslashes(t('home.index.schedule.levels.l3',   'Beginners')) ?>',
      mixed: '<?= addslashes(t('home.index.schedule.levels.l4',   'Mixed / Pre-competitive')) ?>',
      /* ui */
      mapsLabel: '<?= addslashes(t('home.index.schedule.maps_cta',    'View on Google Maps')) ?>',
      locationTbc: '<?= addslashes(t('home.index.schedule.location_tbc', 'Location TBC')) ?>',
      /* season */
      winterBadge: '<?= addslashes(t('home.index.schedule.season.winter', 'Winter Schedule')) ?>',
      summerBadge: '<?= addslashes(t('home.index.schedule.season.summer', 'Summer Schedule')) ?>',
      winterRange: '<?= addslashes(t('home.index.schedule.season.winter_range', 'Active: 24 October – 12 May')) ?>',
      summerRange: '<?= addslashes(t('home.index.schedule.season.summer_range', 'Active: 13 May – 23 October')) ?>',
      /* toggle */
      previewWin: '<?= addslashes(t('home.index.schedule.toggle.preview_winter', 'Preview Winter Schedule')) ?>',
      previewSum: '<?= addslashes(t('home.index.schedule.toggle.preview_summer', 'Preview Summer Schedule')) ?>',
      returnCur: '<?= addslashes(t('home.index.schedule.toggle.return', 'Return to Current Schedule')) ?>',
      /* days */
      satSun: '<?= addslashes(t('home.index.schedule.days.saturday_sunday', 'Saturday & Sunday')) ?>',
      saturday: '<?= addslashes(t('home.index.schedule.days.saturday',        'Saturday')) ?>',
      sunday: '<?= addslashes(t('home.index.schedule.days.sunday',          'Sunday')) ?>',
      tuesday: '<?= addslashes(t('home.index.schedule.days.tuesday',         'Tuesday')) ?>',
      wednesday: '<?= addslashes(t('home.index.schedule.days.wednesday',       'Wednesday')) ?>',
      multipleDays: '<?= addslashes(t('home.index.schedule.days.multiple',        'Multiple Days')) ?>',
      /* location sub-labels */
      subUniversity: '<?= addslashes(t('home.index.schedule.location_subs.panepistimioupoli', 'University Campus')) ?>',
      subPolytechnic: '<?= addslashes(t('home.index.schedule.location_subs.polytexneioupoli',  'Polytechnic Campus')) ?>',
      /* location names */
      locZografou: '<?= addslashes(t('home.index.schedule.locations.zografou',    'Zografou')) ?>',
      locOaka: '<?= addslashes(t('home.index.schedule.locations.oaka_marousi', 'OAKA / Marousi')) ?>',
      locGerakas: '<?= addslashes(t('home.index.schedule.locations.gerakas',     'Gerakas')) ?>',
      locEgaleo: '<?= addslashes(t('home.index.schedule.locations.egaleo',      'Egaleo')) ?>',
      locVrilissia: '<?= addslashes(t('home.index.schedule.locations.vrilissia',   'Vrilissia')) ?>',
      locMegalopolis: '<?= addslashes(t('home.index.schedule.locations.megalopolis', 'Megalopolis')) ?>',
      locKalamata: '<?= addslashes(t('home.index.schedule.locations.kalamata',    'Kalamata')) ?>',
      locIlioupoli: '<?= addslashes(t('home.index.schedule.locations.ilioupoli',   'Ilioupoli')) ?>',
    };
  </script>

  <section class="schedule-section" id="schedule-root"
    aria-label="<?= htmlspecialchars(t('home.index.schedule.aria_label', 'Weekly Schedule')) ?>">

    <header class="schedule-header">
      <div class="schedule-season-badge" id="schedule-season-badge" aria-live="polite"></div>
      <h2 class="schedule-title">
        <?= htmlspecialchars(t('home.index.schedule.title', 'Weekly Schedule')) ?>
      </h2>
      <p class="schedule-subtitle" id="schedule-season-subtitle" aria-live="polite"></p>
    </header>

    <div class="sched-toggle-wrap">
      <button class="sched-toggle-btn" id="sched-toggle-btn" type="button" aria-pressed="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 12a9 9 0 1 1-6.219-8.56" />
        </svg>
        <span id="sched-toggle-label"></span>
      </button>
    </div>

    <div class="sched-legend"
      aria-label="<?= htmlspecialchars(t('home.index.schedule.legend_aria', 'Level legend')) ?>">
      <div class="sched-legend-item">
        <span class="sched-legend-dot" style="background:var(--sched-l1)"></span>
        <?= htmlspecialchars(t('home.index.schedule.levels.l1', 'Basic')) ?>
      </div>
      <div class="sched-legend-item">
        <span class="sched-legend-dot" style="background:var(--sched-l2)"></span>
        <?= htmlspecialchars(t('home.index.schedule.levels.l2', 'Advanced')) ?>
      </div>
      <div class="sched-legend-item">
        <span class="sched-legend-dot" style="background:var(--sched-l3)"></span>
        <?= htmlspecialchars(t('home.index.schedule.levels.l3', 'Beginners')) ?>
      </div>
      <div class="sched-legend-item">
        <span class="sched-legend-dot"
          style="background:linear-gradient(120deg,var(--sched-l4-a) 45%,var(--sched-l4-b) 45%)"></span>
        <?= htmlspecialchars(t('home.index.schedule.levels.l4', 'Mixed / Pre-competitive')) ?>
      </div>
    </div>

    <div class="season-panel" id="panel-winter" aria-label="Winter Schedule"></div>
    <div class="season-panel hidden" id="panel-summer" aria-label="Summer Schedule"></div>

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