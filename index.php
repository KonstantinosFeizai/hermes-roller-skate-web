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
      <?= htmlspecialchars(t('home.announcement.title')) ?>
    </h2>

    <div class="announcement-images">
      <img src="<?= asset('photo/spot3.webp') ?>" alt="Beginner roller skating class in Zografou, Athens"
        loading="lazy">
      <img src="<?= asset('photo/spot2.webp') ?>" alt="Roller skating for children and families in Athens"
        loading="lazy">
      <img src="<?= asset('photo/whoweare.webp') ?>" alt="Hermes Rollerskate Academy outdoor group session"
        loading="lazy">
    </div>

    <div class="homepageinfo">
      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.announcement.p1')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.announcement.p2')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.announcement.p3')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.announcement.p4')) ?>
      </p>

      <p class="info-paragraph">
        <?= htmlspecialchars(t('home.announcement.p5')) ?>
      </p>

      <a class="button-link"        
        href="https://docs.google.com/forms/d/e/1FAIpQLSeAawGRiqE58WiY_K6jB6JIRDhrlj6ZxK-g9eXLRipInN01IA/viewform"      
        target="_blank" rel="noopener">
        <?= htmlspecialchars(t('home.announcement.cta_team')) ?>
      </a>
      <br>
      <a class="button-link" href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09"  
        target="_blank" rel="noopener">
        <?= htmlspecialchars(t('home.announcement.cta_private')) ?>
      </a>
      <p class="info-paragraph bold-highlight">
        <?= htmlspecialchars(t('home.announcement.highlight')) ?>
      </p>
    </div>
  </section>

  <!-- Weekly Schedule Grid -->

  <section class="grid-container">
    <!-- Tuesday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.schedule.days.tuesday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">16:30–18:00</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-l2"><?= htmlspecialchars(t('home.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/Qo2WeKKUf23oGLKi7" target="_blank"><?= htmlspecialchars(t('home.schedule.locations.marousi')) ?></a>
      </div>
    </div>

    <!-- Wednesday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.schedule.days.wednesday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.schedule.levels.l2')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/P8GLY5GFSRkJiXFv9" target="_blank"><?= htmlspecialchars(t('home.schedule.locations.gerakas')) ?></a>
      </div>
    </div>

    <!-- Saturday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.schedule.days.saturday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3"><?= htmlspecialchars(t('home.schedule.levels.l3')) ?></strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.schedule.levels.l4')) ?></strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed"><?= htmlspecialchars(t('home.schedule.levels.mixed')) ?></strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank"><?= htmlspecialchars(t('home.schedule.locations.zografou_athens')) ?></a>
      </div>
    </div>

    <!-- Sunday -->
    <div class="grid-item">
      <h3><?= htmlspecialchars(t('home.schedule.days.sunday')) ?></h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2"><?= htmlspecialchars(t('home.schedule.levels.l2')) ?></strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3"><?= htmlspecialchars(t('home.schedule.levels.l3')) ?></strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1"><?= htmlspecialchars(t('home.schedule.levels.l1')) ?></strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4"><?= htmlspecialchars(t('home.schedule.levels.l4')) ?></strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed"><?= htmlspecialchars(t('home.schedule.levels.mixed')) ?></strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private"><?= htmlspecialchars(t('home.schedule.levels.private')) ?></strong></li>
      </ul>
      <div class="location">
        <?= htmlspecialchars(t('home.schedule.location_label')) ?> <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank"><?= htmlspecialchars(t('home.schedule.locations.zografou')) ?></a>
      </div>
    </div>
  </section>

  <!-- Latest News -->
  <section class="news-section">
    <div class="news-inner">
      <h2><?= htmlspecialchars(t('home.news.title')) ?></h2>

      <p class="news-sub">
        <?= htmlspecialchars(t('home.news.subtitle')) ?>
      </p>

      <a class="btn-gradient-pill" href="https://www.instagram.com/hermes_rollerskate/" target="_blank" rel="noopener"  
        aria-label="Open Hermes Rollerskate Instagram">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        <span>
          <?= htmlspecialchars(t('home.news.cta')) ?>
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
            <img src="photo/TshirtHermes.webp" alt="Card Image" class="card-image">
            <p class="badge">Merch</p>
            <h2>Academy's T-Shirt Hermes</h2>
            <p class="badge price">Price : €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/prostateutika/krani/athlopaidia-kranos-agnistiko-afxomeioumeno-mavro-003.10015/m-p"
            class="card-link">
            <img src="photo/helmet.webp" alt="Card Image" class="card-image">
            <p class="badge">Protection</p>
            <h2>Helmet Auto-reduction - Black</h2>
            <p class="badge price">Price : €20</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://www.instagram.com/direct/t/17843521326338523" class="card-link">
            <img src="photo/TshirtHermida.webp" alt="Card Image" class="card-image">
            <p class="badge">Merch</p>
            <h2>Academy's T-Shirt Hermida</h2>
            <p class="badge price">Price : €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/skates/paidika-skates/fitness-paidika/blade-runner-phoenix-g-patinia-lefko-fouxia-43.0t1011-p"
            class="card-link">
            <img src="photo/roller2.webp" alt="Card Image" class="card-image">
            <p class="badge">Skates</p>
            <h2>Blade Runner Phoenix G Skates - Fuchsia</h2>
            <p class="badge price">Price : €100</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/prostateutika/prostateftika/athlopaidia-paidika-prostateftika-003.11359-p"    
            class="card-link">
            <img src="photo/protection.webp" alt="Card Image" class="card-image">
            <p class="badge">Protection</p>
            <h2>Kids Protective Knee & Wrist Pads</h2>
            <p class="badge price">Price : €20</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/skates/exoplismos/tsantes-skates" class="card-link">
            <img src="photo/bag.webp" alt="Card Image" class="card-image">
            <p class="badge">Accessory</p>
            <h2>Skate Bag - Black </h2>
            <p class="badge price">Price : €13</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
      </ul>

      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  </div>


  <a href="https://powerskate.eu/skates" class="button-link" target="_blank" rel="noopener">
    <?= htmlspecialchars(t('home.merch.cta')) ?>
  </a>

</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>