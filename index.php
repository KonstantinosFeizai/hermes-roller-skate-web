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
  "js/alerts.js",
  "js/schedule.js"
];
// Active nav item
$activePage = 'Home';

// blog db query
$currentLang = $_SESSION['lang'] ?? 'el';
$latestPost = null;
$latestPostCats = [];

try {
  // 1. Φέρνουμε μόνο το 1 πιο πρόσφατο δημοσιευμένο άρθρο στη σωστή γλώσσα[cite: 3]
  $stmt_latest = $pdo->prepare("
        SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at 
        FROM blog_posts bp
        WHERE bp.is_published = 1 
          AND bp.language = :lang  
        ORDER BY bp.published_at DESC
        LIMIT 1
    ");
  $stmt_latest->execute([':lang' => $currentLang]);
  $latestPost = $stmt_latest->fetch(PDO::FETCH_ASSOC);

  // 2. Αν βρέθηκε άρθρο, φέρνουμε τις κατηγορίες του[cite: 3]
  if ($latestPost) {
    $stmt_post_cats = $pdo->prepare("
            SELECT c.name, c.slug 
            FROM post_categories pc
            JOIN categories c ON pc.category_id = c.id
            WHERE pc.post_id = :post_id AND c.language = :lang
        ");
    $stmt_post_cats->execute([
      ':post_id' => $latestPost['id'],
      ':lang' => $currentLang
    ]);
    $latestPostCats = $stmt_post_cats->fetchAll(PDO::FETCH_ASSOC);
  }
} catch (PDOException $e) {
  // Fallback αν κάτι πάει στραβά
  $latestPost = null;
}

// Helper για τις πρώτες λέξεις[cite: 3]
if (!function_exists('first_words')) {
  function first_words($text, $n = 10)
  {
    $text = trim(strip_tags($text));
    if ($text === '') return '';
    $words = preg_split('/\s+/u', $text);
    if (count($words) <= $n) return implode(' ', $words);
    return implode(' ', array_slice($words, 0, $n)) . '...';
  }
}


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

<!-- One-time alert message -->
<?php if ($alert_message): ?>
  <?php
  $alert_title = ($alert_type === 'success') ? t('auth.verify.success_title') : t('auth.verify.warning_title');
  $icon_path = ($alert_type === 'success') ? asset('photo/accept_01.png') : asset('photo/reject.png');
  ?>
  <div class="custom-modal-overlay" id="alertModalOverlay">
    <div class="custom-modal-card" role="dialog" aria-modal="true">
      <button type="button" class="custom-modal-close-icon" onclick="closeAlertModal()" aria-label="<?= htmlspecialchars(t('auth.verify.close')) ?>">&times;</button>

      <div class="custom-modal-icon-badge custom-modal-icon-badge--<?php echo htmlspecialchars($alert_type); ?>">
        <img src="<?php echo $icon_path; ?>" alt="Status Icon">
      </div>

      <h3 class="custom-modal-title"><?php echo htmlspecialchars($alert_title); ?></h3>
      <p class="custom-modal-text"><?php echo htmlspecialchars(t($alert_message)); ?></p>

      <div class="custom-modal-actions">
        <button type="button" class="custom-modal-btn custom-modal-btn--<?php echo htmlspecialchars($alert_type); ?>" onclick="closeAlertModal()">
          <?= htmlspecialchars(t('auth.verify.ok_btn')) ?>
        </button>
      </div>
    </div>
  </div>
<?php endif; ?>
<!-- 1. HERO SECTION -->
<header class="homepage-hero" style="background-image: linear-gradient(to top, rgba(10, 25, 47, 0.95), rgba(10, 25, 47, 0.65), rgba(10, 25, 47, 0.45)), url('<?= asset('photo/prices.jpg') ?>');">
  <div class="container">
    <div class="homepage-hero__inner">
      <h1><?= htmlspecialchars(t('home.hero.title')) ?></h1>
      <p><?= htmlspecialchars(t('home.hero.subtitle')) ?></p>
    </div>
  </div>
</header>

<main>
  <!-- 2. SPLIT SECTION: INTRO TEXT & GALLERY SIDE-BY-SIDE -->
  <section class="intro-split-section">
    <div class="container split-container">


      <div class="split-left-content">
        <h2 class="split-heading"><?= htmlspecialchars(t('home.announcement.title')) ?></h2>
        <p class="info-paragraph"><?= htmlspecialchars(t('home.announcement.p1')) ?></p>
        <p class="info-paragraph"><?= htmlspecialchars(t('home.announcement.p2')) ?></p>
      </div>

      <div class="split-right-gallery">
        <div class="gallery-col">
          <img src="<?= asset('photo/hallowed.webp') ?>" alt="<?= htmlspecialchars(t('home.index.announcement.alts.img1')) ?>" loading="lazy">
        </div>
        <div class="gallery-col">
          <img src="<?= asset('photo/rollerskate.webp') ?>" alt="<?= htmlspecialchars(t('home.index.announcement.alts.img2')) ?>" loading="lazy">
        </div>
      </div>

    </div>
  </section>

  <!-- 3. CHOOSE YOUR LEVEL & ACTION ZONE (3-Column Cards χωρίς Emojis) -->
  <section class="levels-section">
    <div class="container">
      <h2 class="levels-title"><?= htmlspecialchars(t('home.intro.levels')) ?></h2>

      <div class="levels-grid">
        <!-- Card 1: Beginners -->
        <div class="level-card">
          <h3 class="card-headline"><?= htmlspecialchars(t('home.intro.beginner')) ?></h3>
          <p><?= htmlspecialchars(t('home.intro.beginner_desc')) ?></p>
        </div>

        <!-- Card 2: Intermediate & Advanced -->
        <div class="level-card">
          <h3 class="card-headline"><?= htmlspecialchars(t('home.intro.intermediate')) ?></h3>
          <p><?= htmlspecialchars(t('home.intro.intermediate_desc')) ?></p>
        </div>

        <!-- Card 3: No Gear, No Problem (Μετατράπηκε σε Card) -->
        <div class="level-card highlight-card">
          <h3 class="card-headline"><?= htmlspecialchars(t('home.intro.no_gear')) ?></h3>
          <p><?= htmlspecialchars(t('home.intro.no_gear_desc')) ?></p>
        </div>
      </div>

      <!-- Action Zone: Ready to Roll -->
      <div class="action-zone">
        <h3 class="action-zone__title"><?= htmlspecialchars(t('home.announcement.highlight')) ?></h3>
        <div class="action-buttons">

          <!-- Button 1: Apply for Private -->
          <a class="premium-button btn-apply" href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09" target="_blank" rel="noopener">
            <span><?= htmlspecialchars(t('home.announcement.apply')) ?></span>
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </a>

          <!-- Button 2: Sign Up Guide -->
          <a class="premium-button btn-guide" href="<?= asset('docs/signup-guide.pdf') ?>" download>
            <span><?= htmlspecialchars(t('home.announcement.signup')) ?></span>
            <svg class="icon icon-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
          </a>

        </div>
      </div>

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
      precomp: '<?= addslashes(t('home.index.schedule.levels.l5',   'Pre-competitive')) ?>',
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
      locHalandri: '<?= addslashes(t('home.index.schedule.locations.halandri',   'Halandri')) ?>',
    };
  </script>

  <section class="schedule-section" id="schedule-root"
    aria-label="<?= htmlspecialchars(t('home.index.schedule.aria_label', 'Weekly Schedule')) ?>">

    <div class="schedule-header">

      <!-- 1. Καθαρός Τίτλος με το Primary Color -->
      <h2 class="schedule-main-title"><?= htmlspecialchars(t('home.index.schedule.title', 'Weekly Schedule')) ?></h2>

      <!-- 2. Διακριτικός Υπότιτλος Ημερομηνίας (θα αλλάζει δυναμικά μέσω JS) -->
      <p id="schedule-season-subtitle" class="schedule-sub-date"></p>

      <!-- 3. Το Νέο Premium Segmented Toggle Switch -->
      <div class="schedule-toggle-wrapper">
        <div class="sched-toggle-container" id="sched-toggle-container">
          <!-- Το background "μαξιλαράκι" που γλιστράει (sliding pill) -->
          <div class="sched-toggle-pill" id="sched-toggle-pill"></div>

          <!-- Option: Summer (Αριστερά) -->
          <button type="button" class="sched-toggle-opt" data-season="summer">
            <svg class="toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <circle cx="12" cy="12" r="5" />
              <line x1="12" y1="1" x2="12" y2="3" />
              <line x1="12" y1="21" x2="12" y2="23" />
              <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
              <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
              <line x1="1" y1="12" x2="3" y2="12" />
              <line x1="21" y1="12" x2="23" y2="12" />
              <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
              <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
            </svg>
            <span><?= htmlspecialchars(t('home.index.schedule.summer', 'Summer')) ?></span>
          </button>

          <!-- Option: Winter (Δεξιά) -->
          <button type="button" class="sched-toggle-opt" data-season="winter">
            <i class="fa-solid fa-snowflake toggle-icon-fa"></i>
            <span><?= htmlspecialchars(t('home.index.schedule.winter', 'Winter')) ?></span>
          </button>
        </div>
      </div>

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
      <div class="sched-legend-item">
        <span class="sched-legend-dot" style="background:var(--sched-l5)"></span>
        <?= htmlspecialchars(t('home.index.schedule.levels.l5', 'Pre-competitive')) ?>
      </div>
    </div>

    <div class="season-panel" id="panel-winter" aria-label="Winter Schedule"></div>
    <div class="season-panel hidden" id="panel-summer" aria-label="Summer Schedule"></div>

  </section>

  <!-- Latest News / Highlights Section -->
  <section class="news-section">
    <div class="news-inner">

      <!-- Section Header -->
      <div class="news-header">
        <h2><?= htmlspecialchars(t('home.index.news.title')) ?></h2>
        <p class="news-sub"><?= htmlspecialchars(t('home.index.news.subtitle')) ?></p>
      </div>

      <!-- Split Grid: Video Left / Blog Right -->
      <div class="news-grid">

        <!-- Left Column: Video Card -->
        <div class="news-col-video">
          <div class="video-card">
            <div class="video-card-media">
              <div class="video-wrapper">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/hgv8rrNYaxk?si=3RVEwLvqZQVf_k1h" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
              </div>
            </div>
            <div class="video-card-body">
              <span class="video-date">YOUTUBE HIGHLIGHT</span>
              <h3 class="video-card-title"><?= htmlspecialchars(t('home.index.news.highlight1.desc')) ?></h3>
              <div class="video-card-actions">
                <a class="video-watch-more" href="https://www.youtube.com/@HermesRollerskate" target="_blank" rel="noopener">
                  Δείτε το κανάλι μας <i class="fa-brands fa-youtube" style="color: #ff0000; margin-left: 5px;"></i>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Latest Blog Post -->
        <div class="news-col-blog">
          <?php if ($latestPost): ?>
            <article class="latest-blog-card" onclick="window.location.href='<?= asset('post') . '?slug=' . urlencode($latestPost['slug']) ?>'">
              <div class="blog-card-media">
                <?php if (!empty($latestPost['featured_image'])): ?>
                  <img src="<?= asset('assets/uploads/blog/') . htmlspecialchars($latestPost['featured_image']) ?>" alt="<?= htmlspecialchars($latestPost['title']) ?>">
                <?php else: ?>
                  <img src="<?= asset('photo/logo.webp') ?>" alt="no image">
                <?php endif; ?>

                <?php if (!empty($latestPostCats)): ?>
                  <div class="blog-card-badges">
                    <?php foreach (array_slice($latestPostCats, 0, 2) as $c): ?>
                      <span class="blog-cat-badge"><?= htmlspecialchars($c['name']) ?></span>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>

              <div class="blog-card-body">
                <span class="blog-date"><?= date('d/m/Y', strtotime($latestPost['published_at'])) ?></span>
                <h3 class="blog-card-title"><?= htmlspecialchars($latestPost['title']) ?></h3>
                <p class="blog-card-excerpt"><?= htmlspecialchars(first_words($latestPost['excerpt'] ?? $latestPost['title'], 14)) ?></p>

                <div class="blog-card-actions">
                  <a class="blog-read-more" href="<?= asset('post') ?>?slug=<?= urlencode($latestPost['slug']) ?>">
                    <?= htmlspecialchars(t('blog.read_more', 'Read More')) ?> <i class="fa-solid fa-arrow-right fa-xs" aria-hidden="true"></i>
                  </a>
                </div>
              </div>
            </article>
          <?php else: ?>
            <!-- Fallback αν δεν υπάρχει άρθρο -->
            <div class="no-news-fallback">
              <p><?= htmlspecialchars(t('blog.empty', 'No news found.')) ?></p>
            </div>
          <?php endif; ?>
        </div>

      </div>

      <!-- Follow Button Section -->
      <div class="artboard-wrapper">
        <div class="artboard">
          <div class="button">

            <!-- Τα 4 Social Icons που αποκαλύπτονται στο hover -->
            <div class="links">
              <a class="fab fa-2x fa-facebook-f" target="_blank" rel="noopener" href="https://www.facebook.com/profile.php?id=61568127231101" aria-label="Follow us on Facebook"></a>
              <a class="fab fa-2x fa-youtube" target="_blank" rel="noopener" href="https://www.youtube.com/@HermesRollerskate" aria-label="Follow us on YouTube"></a>
              <a class="fab fa-2x fa-tiktok" target="_blank" rel="noopener" href="https://www.tiktok.com/@hermesrollerskate" aria-label="Follow us on TikTok"></a>
              <a class="fab fa-2x fa-instagram" target="_blank" rel="noopener" href="https://www.instagram.com/hermes_rollerskate_academy/" aria-label="Follow us on Instagram"></a>
            </div>

            <!-- Το αρχικό Overlay που κρύβει τα icons -->
            <div class="overlay">
              <span>Follow us</span>
            </div>

          </div>
        </div>
      </div>

    </div>
  </section>


  <!-- Suggested Products Section -->
  <section class="merch-section">
    <div class="container">

      <!-- Section Header -->
      <div class="merch-header">
        <h2 class="merch-title"><?= htmlspecialchars(t('home.index.merch.section_title', 'Academy Merch')) ?></h2>
        <span class="merch-subtitle"><?= htmlspecialchars(t('home.index.merch.section_subtitle', 'LOOK COOL ON WHEELS')) ?></span>
      </div>

      <div class="card-wrapper">
        <ul class="card-list">

          <!-- Card 1: Hermes T-Shirt -->
          <li class="card-item">
            <a href="https://www.instagram.com/direct/t/17850152298606126/" class="card-link" target="_blank" rel="noopener">
              <div class="card-image-wrapper">
                <img src="<?= asset('photo/TshirtHermes.png') ?>" alt="<?= htmlspecialchars(t('home.index.merch.card1.alt')) ?>" class="card-image">
              </div>
              <span class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></span>
              <h3 class="card-title"><?= htmlspecialchars(t('home.index.merch.card1.title')) ?></h3>
              <button class="card-button" aria-label="View Product">
                <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </a>
          </li>

          <!-- Card 2: Hermida T-Shirt -->
          <li class="card-item">
            <a href="https://www.instagram.com/direct/t/17850152298606126/" class="card-link" target="_blank" rel="noopener">
              <div class="card-image-wrapper">
                <img src="<?= asset('photo/TshirtHermida.png') ?>" alt="<?= htmlspecialchars(t('home.index.merch.card2.alt')) ?>" class="card-image">
              </div>
              <span class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></span>
              <h3 class="card-title"><?= htmlspecialchars(t('home.index.merch.card2.title')) ?></h3>
              <button class="card-button" aria-label="View Product">
                <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </a>
          </li>

          <!-- Card 3: Race T-Shirt -->
          <li class="card-item">
            <a href="https://www.instagram.com/direct/t/17850152298606126/" class="card-link" target="_blank" rel="noopener">
              <div class="card-image-wrapper">
                <img src="<?= asset('photo/jersey_front.png') ?>" alt="<?= htmlspecialchars(t('home.index.merch.card3.alt')) ?>" class="card-image">
              </div>
              <span class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></span>
              <h3 class="card-title"><?= htmlspecialchars(t('home.index.merch.card3.title')) ?></h3>
              <button class="card-button" aria-label="View Product">
                <svg class="arrow-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                  <polyline points="12 5 19 12 12 19"></polyline>
                </svg>
              </button>
            </a>
          </li>

        </ul>
      </div>
    </div>
  </section>

</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>