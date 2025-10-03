<?php
// index.php (Home)

$pageTitle = 'Welcome to Hermes Rollerskate Academy | Learn to Skate in Athens';
$pageDescription = "Hermes Rollerskate Academy - Learn, practice, and enjoy roller skating with expert guidance";
$pageKeywords = "Roller skate lessons Athens, roller skating classes, Hermes Rollerskate Academy, skating school Zografou, learn to skate, skating for kids Athens, adult skating classes";
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
      Welcome to Hermes Rollerskate
    </h1>
    <h2 class="animate__animated animate__bounceInUp">
      Learn, roll, evolve – at Hermes Rollerskate Academy.
    </h2>
  </div>
</header>

<main>
  <!-- Announcement Section -->
  <section class="announcement">
    <h2>
      Start Your Roller Skating Journey in Athens
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
        Welcome to Hermes Rollerskate Academy, where children and adults learn to skate in a safe and fun
        environment
        in Zografou, Athens.
      </p>

      <p class="info-paragraph">
        Join our roller skating classes in Athens and discover the joy of skating with us!
      </p>

      <p class="info-paragraph">
        Are you a complete beginner? Book a private roller skating lesson to build confidence and master the
        basics—rolling, balance, and safe stops.
      </p>

      <p class="info-paragraph">
        Already know how to skate? Join our group roller skating classes in Athens and develop your freestyle,
        speed,
        or dance skills.
      </p>

      <p class="info-paragraph">
        Don't have your own roller skates or protection? No problem—just let us know your size and we’ll provide
        everything you need.
      </p>

      <a class="button-link"        
        href="https://docs.google.com/forms/d/e/1FAIpQLSeAawGRiqE58WiY_K6jB6JIRDhrlj6ZxK-g9eXLRipInN01IA/viewform"      
        target="_blank" rel="noopener">
        📝 Sign up to the team
      </a>
      <br>
      <a class="button-link" href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09"  
        target="_blank" rel="noopener">
        📝 Book your private lesson!
      </a>
      <p class="info-paragraph bold-highlight">
        Roll with confidence. Learn with joy.
      </p>
    </div>
  </section>

  <!-- Weekly Schedule Grid -->

  <section class="grid-container">
    <!-- Tuesday -->
    <div class="grid-item">
      <h3>Tuesday</h3>
      <ul class="class-list">
        <li><span class="time">16:30–18:00</span> <strong class="level-private">Private</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l1">Beginners L1</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-l2">Basic L2</strong></li>
      </ul>
      <div class="location">
        📍 Location: <a href="https://maps.app.goo.gl/Qo2WeKKUf23oGLKi7" target="_blank">ΟΑΚΑ/Marousi &amp; Gerakas</a>
      </div>
    </div>

    <!-- Wednesday -->
    <div class="grid-item">
      <h3>Wednesday</h3>
      <ul class="class-list">
        <li><span class="time">16:30–17:30</span> <strong class="level-private">Private</strong></li>
        <li><span class="time">17:30–18:30</span> <strong class="level-l1">Beginners L1</strong></li>
        <li><span class="time">18:30–19:30</span> <strong class="level-l2">Basic L2</strong></li>
      </ul>
      <div class="location">
        📍 Location: <a href="https://maps.app.goo.gl/P8GLY5GFSRkJiXFv9" target="_blank">Gerakas</a>
      </div>
    </div>

    <!-- Saturday -->
    <div class="grid-item">
      <h3>Saturday</h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1">Begginers L1</strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2">Basic L2</strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3">Advanced L3</strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private">Private</strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1">Begginers L1</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4">Basic-Advanced L2+L3</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed">Mix Adults</strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private">Private</strong></li>
      </ul>
      <div class="location">
        📍 Location: <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank">Zografou/Athens</a>
      </div>
    </div>

    <!-- Sunday -->
    <div class="grid-item">
      <h3>Sunday</h3>
      <ul class="class-list">
        <li><span class="time">09:30–10:30</span> <strong class="level-l1">Begginers L1</strong></li>
        <li><span class="time">10:30–11:30</span> <strong class="level-l2">Basic L2</strong></li>
        <li><span class="time">11:30–12:30</span> <strong class="level-l3">Advanced L3</strong></li>
        <li><span class="time">12:30–13:30</span> <strong class="level-private">Private</strong></li>
        <li><span class="time">17:00–18:00</span> <strong class="level-l1">Begginers L1</strong></li>
        <li><span class="time">18:00–19:00</span> <strong class="level-l4">Basic-Advanced L2+L3</strong></li>
        <li><span class="time">19:00–20:00</span> <strong class="level-mixed">Mix Adults</strong></li>
        <li><span class="time">20:00–21:00</span> <strong class="level-private">Private</strong></li>
      </ul>
      <div class="location">
        📍 Location: <a href="https://maps.app.goo.gl/4ifyZcivRadWFjKd6" target="_blank">Zografou</a>
      </div>
    </div>
  </section>

  <!-- Latest News -->
  <section class="news-section">
    <div class="news-inner">
      <h2>Latest News</h2>

      <p class="news-sub">
        Stay updated with our latest events and announcements!
      </p>

      <a class="btn-gradient-pill" href="https://www.instagram.com/hermes_rollerskate/" target="_blank" rel="noopener"  
        aria-label="Open Hermes Rollerskate Instagram">
        <i class="fa-brands fa-instagram" aria-hidden="true"></i>
        <span>
          View latest on Instagram
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
    🛍️ View all merchandise
  </a>

</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>