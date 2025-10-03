<?php
// index.php (Home)

$pageTitle = 'Καλώς ήρθατε στην Hermes Rollerskate Academy | Μαθήματα Πατινιών στην Αθήνα';
$pageDescription = "Hermes Rollerskate Academy - Μάθετε, εξασκηθείτε, και απολαύστε το roller skating με την καθοδήγηση ειδικών";
$pageKeywords = "Μαθήματα πατινιών Αθήνα, μαθήματα roller skating, Hermes Rollerskate Academy, σχολή πατινιών Ζωγράφου, μάθετε πατίνια, πατίνια για παιδιά Αθήνα, μαθήματα για ενήλικες";
$pageCss = [
  'css/homepage.css',
];
$pageScripts = [
  "https://code.jquery.com/jquery-3.6.0.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/typed.js/2.0.12/typed.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/gsap.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/ScrollTrigger.min.js",
  "https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.5/Draggable.min.js",
  'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',


  "js/welcome-overlay.js",
  'js/swiper.js'
];
$activePage = 'Home';


require_once __DIR__ . '/partials/header.php';
?>

<header class="home-hero">
  <div id="welcome-overlay" class="animate__animated">
    <h1 class="animate__animated animate__backInDown" id="welcome-text">
      Καλώς ήρθατε στο Hermes Rollerskate
    </h1>
    <h2 class="animate__animated animate__bounceInUp">
      Μάθε, Ρόλλαρε, Εξελίξου - στο Hermes Rollerskate Academy
    </h2>
  </div>
</header>

<main>
  <!-- Announcement Section -->
  <section class="announcement">
    <h2>
      Ξεκινήστε το Ταξίδι σας στο Roller Skating στην Αθήνα
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
        Καλώς ήρθατε στην Hermes Rollerskate Academy, όπου παιδιά και ενήλικες μαθαίνουν πατίνια με ασφάλεια και
        διασκέδαση. Μαθήματα για όλα τα επίπεδα — από αρχάριους έως freestyle. Βρισκόμαστε στον Ζωγράφο και δεχόμαστε
        μαθητές από όλη την Αττική.
      </p>

      <p class="info-paragraph">Ελάτε στα μαθήματα πατινιών μας και ανακαλύψτε τη χαρά του πατινιού μαζί μας!</p>

      <p class="info-paragraph">
        Είστε τελείως αρχάριος; Κλείστε ένα ιδιαίτερο μάθημα για αυτοπεποίθηση και τα βασικά — ρολάρισμα, ισορροπία,
        ασφαλή φρεναρίσματα.
      </p>

      <p class="info-paragraph">
        Γνωρίζετε ήδη τα βασικά; Ελάτε στα ομαδικά μαθήματα και εξελίξτε freestyle, ταχύτητα ή χορευτικές κινήσεις.
      </p>

      <p class="info-paragraph">
        Δεν έχετε δικά σας πατίνια ή προστατευτικά; Κανένα πρόβλημα — πείτε μας τα νούμερά σας και τα παρέχουμε εμείς.
      </p>

      <a class="button-link"
        href="https://docs.google.com/forms/d/e/1FAIpQLSeAawGRiqE58WiY_K6jB6JIRDhrlj6ZxK-g9eXLRipInN01IA/viewform"
        target="_blank" rel="noopener">
        📝 Κάντε εγγραφή στην ομάδα
      </a>
      <br>
      <a class="button-link" href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09"
        target="_blank" rel="noopener">
        📝 Κάντε κράτηση ιδιαίτερο μάθημα!
      </a>
      <p class="info-paragraph bold-highlight">
        Ρολλάρετε με αυτοπεποίθηση. Μάθετε με χαρά.
      </p>
    </div>
  </section>

  <!-- Weekly Schedule Grid -->

  <section class="grid-container">

    <!-- Τρίτη -->
    <div class="grid-item">
      <h3>Τρίτη</h3>
      <ul class="class-list">
        <li><span class="time">16:30–18:00</span> <strong class="level-private">Ιδιαίτερο</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-l2">Βασικό Επ. 2</strong></li>
      </ul>
      <div class="location">
        📍 Τοποθεσία: <a href="https://maps.app.goo.gl/Qo2WeKKUf23oGLKi7" target="_blank">ΟΑΚΑ/Μαρούσι &amp; Γέρακας</a>
      </div>
    </div>

    <!-- Τετάρτη -->
    <div class="grid-item">
      <h3>Τετάρτη</h3>
      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-private">Ιδιαίτερο</strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l2">Βασικό Επ. 2</strong></li>
      </ul>
      <div class="location">
        📍 Τοποθεσία: <a href="https://maps.app.goo.gl/P8GLY5GFSRkJiXFv9" target="_blank">Γέρακας</a>
      </div>
    </div>

    <!-- Σάββατο -->
    <div class="grid-item">
      <h3>Σάββατο</h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2">Βασικό Επ. 2</strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3">Προχωρημένων Επ. 3</strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private">Ιδιαίτερο</strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4">Βασικό-Προχωρημένων Επ. 2+3</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed">Μεικτό Ενηλίκων</strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private">Ιδιαίτερο</strong></li>
      </ul>
      <div class="location">
        📍 Τοποθεσία: <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank">Ζωγράφου/Αθήνα</a>
      </div>
    </div>

    <!-- Κυριακή -->
    <div class="grid-item">
      <h3>Κυριακή</h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2">Βασικό Επ. 2</strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3">Προχωρημένων Επ. 3</strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private">Ιδιαίτερο</strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1">Αρχαρίων Επ. 1</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4">Βασικό-Προχωρημένων Επ. 2+3</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed">Μεικτό Ενηλίκων</strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private">Ιδιαίτερο</strong></li>
      </ul>
      <div class="location">
        📍 Τοποθεσία: <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank">Ζωγράφου</a>
      </div>
    </div>
  </section>

  <!-- Latest News -->
  <section class="news-section">
    <div class="news-inner">
      <h2>Τελευταία Νέα</h2>

      <p class="news-sub">
        Μείνετε ενημερωμένοι με τα τελευταία μας νέα και ανακοινώσεις!
      </p>

      <a class="btn-gradient-pill" href="https://www.instagram.com/hermes_rollerskate/" target="_blank" rel="noopener"
        aria-label="Open Hermes Rollerskate Instagram">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        <span>
          Δείτε τα τελευταία στο Instagram
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
            <img src="<?= asset('photo/TshirtHermes.webp') ?>" alt="Card Image" class="card-image">
            <p class="badge">Merch</p>
            <h2>Academy's T-Shirt Hermes</h2>
            <p class="badge price">Price : €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/prostateutika/krani/athlopaidia-kranos-agnistiko-afxomeioumeno-mavro-003.10015/m-p"
            class="card-link">
            <img src="<?= asset('photo/helmet.webp') ?>" alt="Card Image" class="card-image">
            <p class="badge">Protection</p>
            <h2>Helmet Auto-reduction - Black</h2>
            <p class="badge price">Price : €20</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://www.instagram.com/direct/t/17843521326338523" class="card-link">
            <img src="<?= asset('photo/TshirtHermida.webp') ?>" alt="Card Image" class="card-image">
            <p class="badge">Merch</p>
            <h2>Academy's T-Shirt Hermida</h2>
            <p class="badge price">Price : €15</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/skates/paidika-skates/fitness-paidika/blade-runner-phoenix-g-patinia-lefko-fouxia-43.0t1011-p"
            class="card-link">
            <img src="<?= asset('photo/roller2.webp') ?>" alt="Card Image" class="card-image">
            <p class="badge">Skates</p>
            <h2>Blade Runner Phoenix G Skates - Fuchsia</h2>
            <p class="badge price">Price : €100</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/prostateutika/prostateftika/athlopaidia-paidika-prostateftika-003.11359-p"
            class="card-link">
            <img src="<?= asset('photo/protection.webp') ?>" alt="Card Image" class="card-image">
            <p class="badge">Protection</p>
            <h2>Kids Protective Knee & Wrist Pads</h2>
            <p class="badge price">Price : €20</p>
            <button class="card-button material-symbols-rounded">arrow_forward</button>
          </a>
        </li>
        <li class="card-item swiper-slide">
          <a href="https://powerskate.eu/skates/exoplismos/tsantes-skates" class="card-link">
            <img src="<?= asset('photo/bag.webp') ?>" alt="Card Image" class="card-image">
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
    🛍️ Δείτε όλα τα προϊόντα
  </a>

</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>