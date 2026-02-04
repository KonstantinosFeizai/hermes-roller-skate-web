<?php
// benefits.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('benefits.meta.title');
$pageDescription = t('benefits.meta.description');
$pageKeywords = t('benefits.meta.keywords');
$pageCss = ["css/benefits.css"];
$activePage = "benefits";

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- BENEFITS PAGE CONTENT -->
<main>
  <!-- 1. European RollerSkate Culture & Sports Tourism -->
  <section class="lifestyle-section">
    <div class="container">
      <h3>
        <?= htmlspecialchars(t('benefits.sections.europe.title')) ?>
      </h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.europe.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.europe.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.europe.p3')) ?>
      </p>

      <!-- Image Gallery -->
      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/nice france.webp') ?>" alt="Roller ride in Nice, France">
          <figcaption>
            <?= htmlspecialchars(t('benefits.sections.europe.fig1')) ?>
          </figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/barchelona.webp') ?>" alt="Skating along Barcelona beach">
          <figcaption>
            <?= htmlspecialchars(t('benefits.sections.europe.fig2')) ?>
          </figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 2. Family Activity -->
  <section id="family-activity" class="lifestyle-section">
    <div class="container">
      <h3>
        <?= htmlspecialchars(t('benefits.sections.family.title')) ?>
      </h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.family.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.family.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.family.p3')) ?>
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activity.webp') ?>" alt="Family roller skating fun">
          <figcaption><?= htmlspecialchars(t('benefits.sections.family.fig1')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 3. Learn One, Master Two More -->
  <section id="learn-more" class="lifestyle-section">
    <div class="container">
      <h3><?= htmlspecialchars(t('benefits.sections.learn.title')) ?></h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.learn.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.learn.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.learn.p3')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.learn.p4')) ?>
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activityy.webp') ?>" alt="RollerSkating">
          <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig1')) ?></figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/snowski.webp') ?>" alt="SnowSki">
          <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig2')) ?></figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/iceskate2.webp') ?>" alt="IceSkating">
          <figcaption><?= htmlspecialchars(t('benefits.sections.learn.fig3')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 4. Healthy Competition & Local Races -->
  <section id="competition" class="lifestyle-section">
    <div class="container">
      <h3>
        <?= htmlspecialchars(t('benefits.sections.competition.title')) ?>
      </h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.competition.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.competition.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.competition.p3')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.competition.p4')) ?>
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/racee.webp') ?>" alt="Local Roller Skate Race">
          <figcaption><?= htmlspecialchars(t('benefits.sections.competition.fig1')) ?></figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/aponomes.webp') ?>" alt="Awards Ceremony">
          <figcaption><?= htmlspecialchars(t('benefits.sections.competition.fig2')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 5. Professional Pathways & Coaching -->
  <section id="pathways" class="lifestyle-section">
    <div class="container">
      <h3>
        <?= htmlspecialchars(t('benefits.sections.pathways.title')) ?>
      </h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.pathways.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.pathways.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.pathways.p3')) ?>
      </p>

      <a href="https://docs.google.com/forms/d/e/1FAIpQLSfDa7gGuJDpYOI3_pESB5l4OiF7iAnOBsAQYrINmD19tabiUQ/viewform"
        target="_blank" rel="noopener" class="button-link">
        <?= htmlspecialchars(t('benefits.sections.pathways.cta')) ?>
      </a>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/instractor.webp') ?>" alt="RollerSkate Coach/Instructor">
          <figcaption><?= htmlspecialchars(t('benefits.sections.pathways.fig1')) ?></figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/modela.webp') ?>" alt="RollerSkate Models">
          <figcaption><?= htmlspecialchars(t('benefits.sections.pathways.fig2')) ?></figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 6. Community & Local Micro-communities -->
  <section id="community" class="lifestyle-section">
    <div class="container">
      <h3>
        <?= htmlspecialchars(t('benefits.sections.community.title')) ?>
      </h3>

      <p>
        <?= htmlspecialchars(t('benefits.sections.community.p1')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.community.p2')) ?>
      </p>

      <p>
        <?= htmlspecialchars(t('benefits.sections.community.p3')) ?>
      </p>

      <div class="microcommunities">
        <!-- Community 1 -->
        <div class="community-item">
          <a href="https://cityskaters.gr/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/city skaters.webp') ?>" alt="City Skaters Athens">
            <h3>City Skaters Athens</h3>
          </a>
          <p>
            <?= htmlspecialchars(t('benefits.sections.community.c1_p')) ?>
          </p>
        </div>

        <!-- Community 2 -->
        <div class="community-item">
          <a href="https://www.patiniasocks.com/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/patinia community.webp') ?>" alt="Patinia Community">
            <h3>Patinia Community</h3>
          </a>
          <p>
            <?= htmlspecialchars(t('benefits.sections.community.c2_p')) ?>
          </p>
        </div>

        <!-- Community 3 -->
        <div class="community-item">
          <a href="https://linktr.ee/zoepatini?fbclid=PAZXh0bgNhZW0CMTEAAac56C1Fqan6f3URctJhYhLQ5Wk2q_jXSYiZwVbMMBvVbwZGCEehrdpPoOGTug_aem_Z3TUDktI_a0xBFl9eTetSg"
            target="_blank" rel="noopener">
            <img src="<?= asset('photo/zoepatini.webp') ?>" alt="Zoe Patini">
            <h3><?= htmlspecialchars(t('benefits.sections.community.c3_title')) ?></h3>
          </a>
          <p>
            <?= htmlspecialchars(t('benefits.sections.community.c3_p')) ?>
          </p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>