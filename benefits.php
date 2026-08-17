<?php
// benefits.php

require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

$pageTitle = t('benefits.meta.title');
$pageDescription = t('benefits.meta.description');
$pageKeywords = t('benefits.meta.keywords');
$pageCss = ["css/benefits.css"];
$activePage = "benefits";

require_once PROJECT_ROOT . 'partials/header.php';
?>

<main class="benefits-page">

  <!-- HERO SECTION -->
  <section class="benefits-hero" style="background-image: linear-gradient(to top, rgba(10, 25, 47, 0.9), rgba(10, 25, 47, 0.6), rgba(10, 25, 47, 0.4)), url('<?= asset('photo/Nice-france.jpg') ?>');">
    <div class="hero-container">
      <div class="hero-badge">
        <i class="fas fa-star"></i> <?= htmlspecialchars(t('benefits.meta.hero') ?? 'WHY SKATE WITH US') ?>
      </div>
      <h1><?= htmlspecialchars(t('benefits.meta.title_hero') ?? 'More Than a Sport, a Whole Lifestyle') ?></h1>
      <p><?= htmlspecialchars(t('benefits.meta.description_hero') ?? 'From European travel and family fun to competition, careers and community, roller skating opens doors far beyond the rink.') ?></p>
    </div>
  </section>

  <!-- SECTION 2: EUROPE -->
  <section class="lifestyle-section split-layout">
    <div class="container split-container">
      <div class="text-side">
        <div class="section-badge"><i class="fas fa-globe-europe"></i> <?= htmlspecialchars(t('benefits.sections.europe.badge')) ?></div>
        <h3><?= htmlspecialchars(t('benefits.sections.europe.title')) ?></h3>
        <p><?= htmlspecialchars(t('benefits.sections.europe.p1')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.europe.p2')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.europe.p3')) ?></p>
      </div>
      <div class="image-side gallery-grid-2">
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/Nice-france.jpg') ?>" alt="Nice"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.europe.fig1')) ?></figcaption>
        </figure>
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/barcelona.jpg') ?>" alt="Barcelona"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.europe.fig2')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- SECTION 3: FAMILY -->
  <section id="family-activity" class="lifestyle-section split-layout reverse">
    <div class="container split-container">
      <div class="text-side">
        <div class="section-badge"><i class="fas fa-users"></i> <?= htmlspecialchars(t('benefits.sections.family.badge')) ?></div>
        <h3><?= htmlspecialchars(t('benefits.sections.family.title')) ?></h3>
        <p><?= htmlspecialchars(t('benefits.sections.family.p1')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.family.p2')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.family.p3')) ?></p>
      </div>
      <div class="image-side single-image">
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/family_02.jpg') ?>" alt="Family Skate"></div>
          <!-- <figcaption><?= htmlspecialchars(t('benefits.sections.family.fig1')) ?></figcaption> -->
        </figure>
      </div>
    </div>
  </section>

  <!-- SECTION 4: LEARN -->
  <section id="learn-more" class="lifestyle-section split-layout">
    <div class="container split-container">
      <div class="text-side">
        <div class="section-badge"><i class="fas fa-layer-group"></i> <?= htmlspecialchars(t('benefits.sections.learn.badge')) ?></div>
        <h3><?= htmlspecialchars(t('benefits.sections.learn.title')) ?></h3>
        <p><?= htmlspecialchars(t('benefits.sections.learn.p1')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.learn.p2')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.learn.p3')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.learn.p4')) ?></p>
      </div>
      <div class="image-side complex-grid">
        <figure class="modern-card main-feature">
          <div class="img-wrapper"><img src="<?= asset('photo/family_03.jpg') ?>" alt="Roller"></div>
          <!-- <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig1')) ?></figcaption> -->
        </figure>
        <div class="sub-grid">
          <figure class="modern-card">
            <div class="img-wrapper"><img src="<?= asset('photo/snowski.jpg') ?>" alt="Snow"></div>
            <!-- <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig2')) ?></figcaption> -->
          </figure>
          <figure class="modern-card">
            <div class="img-wrapper"><img src="<?= asset('photo/ice-skating.jpg') ?>" alt="Ice"></div>
            <!-- <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig3')) ?></figcaption> -->
          </figure>
        </div>
      </div>
    </div>
  </section>

  <!-- SECTION 5: COMPETITION  -->
  <section id="competition" class="lifestyle-section split-layout reverse">
    <div class="container split-container">
      <div class="text-side">
        <div class="section-badge"><i class="fas fa-trophy"></i> <?= htmlspecialchars(t('benefits.sections.competition.badge')) ?></div>
        <h3><?= htmlspecialchars(t('benefits.sections.competition.title')) ?></h3>
        <p><?= htmlspecialchars(t('benefits.sections.competition.p1')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.competition.p2')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.competition.p3')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.competition.p4')) ?></p>
      </div>
      <div class="image-side gallery-grid-2">
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/racee.webp') ?>" alt="Race"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.competition.fig1')) ?></figcaption>
        </figure>
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/aponomes.webp') ?>" alt="Awards"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.competition.fig2')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- SECTION 6: PATHWAYS  -->
  <section id="pathways" class="lifestyle-section split-layout">
    <div class="container split-container">
      <div class="text-side">
        <div class="section-badge"><i class="fas fa-graduation-cap"></i> <?= htmlspecialchars(t('benefits.sections.pathways.badge')) ?></div>
        <h3><?= htmlspecialchars(t('benefits.sections.pathways.title')) ?></h3>
        <p><?= htmlspecialchars(t('benefits.sections.pathways.p1')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.pathways.p2')) ?></p>
        <p><?= htmlspecialchars(t('benefits.sections.pathways.p3')) ?></p>

        <a href="https://docs.google.com/forms/d/e/1FAIpQLSfDa7gGuJDpYOI3_pESB5l4OiF7iAnOBsAQYrINmD19tabiUQ/viewform"
          target="_blank" rel="noopener" class="button">
          <!-- Outer orbiting border light -->
          <span class="dots_border"></span>

          <!-- Text and FontAwesome icon wrapper -->
          <span class="text_button">
            <?= htmlspecialchars(str_replace("📩", "", t('benefits.sections.pathways.cta'))) ?>
            <i class="fas fa-arrow-up-right-from-square"></i>
          </span>
        </a>
      </div>
      <div class="image-side gallery-grid-2">
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/rollers_coaching.jpg') ?>" alt="Instructor"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.pathways.fig1')) ?></figcaption>
        </figure>
        <figure class="modern-card">
          <div class="img-wrapper"><img src="<?= asset('photo/rollers-modeling.jpg') ?>" alt="Model"></div>
          <figcaption><?= htmlspecialchars(t('benefits.sections.pathways.fig2')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- SECTION 7: COMMUNITY -->
  <section id="community" class="lifestyle-section">
    <div class="container">
      <div class="section-badge"><i class="fas fa-heart"></i> <?= htmlspecialchars(t('benefits.sections.community.badge')) ?></div>
      <h3><?= htmlspecialchars(t('benefits.sections.community.title')) ?></h3>
      <p><?= htmlspecialchars(t('benefits.sections.community.p1')) ?></p>
      <p><?= htmlspecialchars(t('benefits.sections.community.p2')) ?></p>
      <p><?= htmlspecialchars(t('benefits.sections.community.p3')) ?></p>

      <div class="microcommunities">
        <div class="community-item">
          <a href="https://cityskaters.gr/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/city skaters.webp') ?>" alt="City Skaters">
            <h3>City Skaters Athens</h3>
          </a>
          <p><?= htmlspecialchars(t('benefits.sections.community.c1_p')) ?></p>
        </div>
        <div class="community-item">
          <a href="https://www.patiniasocks.com/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/patinia community.webp') ?>" alt="Patinia">
            <h3>Patinia Community</h3>
          </a>
          <p><?= htmlspecialchars(t('benefits.sections.community.c2_p')) ?></p>
        </div>
        <div class="community-item">
          <a href="https://linktr.ee/zoepatini" target="_blank" rel="noopener">
            <img src="<?= asset('photo/zoepatini.webp') ?>" alt="Zoe Patini">
            <h3><?= htmlspecialchars(t('benefits.sections.community.c3_title')) ?></h3>
          </a>
          <p><?= htmlspecialchars(t('benefits.sections.community.c3_p')) ?></p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>