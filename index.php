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
  'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',


  "js/welcome-overlay.js",
  "js/alerts.js",
  'js/swiper.js'
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
?>

<!-- HOME PAGE CONTENT -->
<header class="home-hero">
  <div id="welcome-overlay" class="animate__animated">
    <h1 class="animate__animated animate__backInDown" id="welcome-text">
      <?= htmlspecialchars(t('home.hero.title')) ?>
    </h1>
    <h2 class="animate__animated animate__bounceInUp">
      <?= htmlspecialchars(t('home.hero.subtitle')) ?>
    </h2>
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

  <section class="grid-container">
    <!-- Tuesday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.index.schedule.days.tuesday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">16:30–18:00</span> <strong class="level-private"><?= htmlspecialchars(t('home.index.schedule.levels.private')) ?></strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.index.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/iwtuXZvQoZDqqgUy9" target="_blank"><?= htmlspecialchars(t('home.index.schedule.locations.oaka_marousi')) ?></a>
      </div>
    </div>

    <!-- Wednesday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.index.schedule.days.wednesday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-private"><?= htmlspecialchars(t('home.index.schedule.levels.private')) ?></strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.index.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/Hdjvv418PZGE3nQU8" target="_blank"><?= htmlspecialchars(t('home.index.schedule.locations.gerakas')) ?></a> <a> & </a> <a href="https://maps.app.goo.gl/DYMoGFQhHgmn7oKb7" target="_blank"><?= htmlspecialchars(t('home.index.schedule.locations.egaleo')) ?></a>
      </div>
    </div>

    <!-- Monday & Thursday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.index.schedule.days.monday_thursday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">16:00–16:50</span> <strong class="level-private"><?= htmlspecialchars(t('home.index.schedule.levels.private')) ?></strong></li>
        <li><span class="time">16:50–17:50</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>

      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.index.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank"><?= htmlspecialchars(t('home.index.schedule.locations.zografou_athens')) ?></a>
      </div>
    </div>

    <!-- Sunday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.index.schedule.days.weekends')) ?></h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.index.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3"><?= htmlspecialchars(t('home.index.schedule.levels.l3')) ?></strong></li>
        <li><span class="time">12:30–14:30</span> <strong class="level-private"><?= htmlspecialchars(t('home.index.schedule.levels.private')) ?></strong></li>
        <li><span class="time">16:00–17:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.index.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.index.schedule.levels.l4')) ?></strong></li>


      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.index.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank"><?= htmlspecialchars(t('home.index.schedule.locations.zografou_athens')) ?></a>
      </div>
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
          <iframe width="560" height="315"
            src="https://www.youtube.com/embed/nECR502WJB8"
            title="<?= htmlspecialchars(t('home.index.news.highlight1.title')) ?>"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
          </iframe>
          <p class="highlight-desc">
            <?= htmlspecialchars(t('home.index.news.highlight1.desc')) ?>
          </p>
        </div>

        <!-- Highlight 2 -->
        <div class="highlight-item">
          <iframe width="560" height="315"
            src="https://www.youtube.com/embed/inaj73_UV0M"
            title="<?= htmlspecialchars(t('home.index.news.highlight2.title')) ?>"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
          </iframe>
          <p class="highlight-desc">
            <?= htmlspecialchars(t('home.index.news.highlight2.desc')) ?>
          </p>
        </div>
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
  <div class="container swiper">
    <div class="card-wrapper">
      <ul class="card-list swiper-wrapper">
        <li class="card-item swiper-slide">
          <a href="https://www.instagram.com/direct/t/17843521326338523/" class="card-link">
            <img src="photo/TshirtHermes.webp" alt="<?= htmlspecialchars(t('home.index.merch.card1.alt')) ?>" class="card-image">
            <p class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></p>
            <h2><?= htmlspecialchars(t('home.index.merch.card1.title')) ?></h2>
            <p class="badge price"><?= htmlspecialchars(t('home.index.merch.price_label')) ?> €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://www.instagram.com/direct/t/17843521326338523" class="card-link">
            <img src="photo/TshirtHermida.webp" alt="<?= htmlspecialchars(t('home.index.merch.card2.alt')) ?>" class="card-image">
            <p class="badge"><?= htmlspecialchars(t('home.index.merch.badge')) ?></p>
            <h2><?= htmlspecialchars(t('home.index.merch.card2.title')) ?></h2>
            <p class="badge price"><?= htmlspecialchars(t('home.index.merch.price_label')) ?> €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
      </ul>

      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
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