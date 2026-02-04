<?php
// activities.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('activities.meta.title');
$pageDescription = t('activities.meta.description');
$pageKeywords = t('activities.meta.keywords');
$pageCss = ['css/activities.css'];
$activePage = 'activities';

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- ACTIVITIES PAGE CONTENT -->
<!-- Intro -->
<header class="intro-section">
  <div class="container">
    <h1><?= htmlspecialchars(t('activities.intro.title')) ?></h1>
    <p>
      <?= htmlspecialchars(t('activities.intro.p1')) ?>
    </p>
    <p>
      <?= htmlspecialchars(t('activities.intro.p2')) ?>
    </p>
  </div>
</header>

<main>
  <!-- Ice Skating section -->
  <section id="ice-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="photo/iceskate1.webp" alt="<?= htmlspecialchars(t('activities.ice.alt')) ?>">

      </figure>
      <div class="activity-content">
        <h2><?= htmlspecialchars(t('activities.ice.title')) ?></h2>
        <p>
          <?= htmlspecialchars(t('activities.ice.p1')) ?>
        </p>
        <div class="learn-goals">
          <h3><?= htmlspecialchars(t('activities.ice.learn_title')) ?></h3>
          <ul>
            <li><?= htmlspecialchars(t('activities.ice.learn_1')) ?></li>
            <li><?= htmlspecialchars(t('activities.ice.learn_2')) ?></li>
            <li><?= htmlspecialchars(t('activities.ice.learn_3')) ?></li>
            <li><?= htmlspecialchars(t('activities.ice.learn_4')) ?></li>
          </ul>
        </div>
        <div class="target-group">
          <h3><?= htmlspecialchars(t('activities.ice.target_title')) ?></h3>
          <p>
            <?= htmlspecialchars(t('activities.ice.target_p')) ?>
          </p>
        </div>
        <div class="meta-info">
          <div><?= htmlspecialchars(t('activities.ice.meta_1')) ?></div>
          <div><?= htmlspecialchars(t('activities.ice.meta_2')) ?></div>
          <div><?= htmlspecialchars(t('activities.ice.meta_3')) ?></div>
        </div>
      </div>
    </div>
  </section>


  <!-- Ski section -->
  <section id="ski-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="photo/ski.webp" alt="<?= htmlspecialchars(t('activities.ski.alt')) ?>">

      </figure>
      <div class="activity-content">
        <h2><?= htmlspecialchars(t('activities.ski.title')) ?></h2>
        <p>
          <?= htmlspecialchars(t('activities.ski.p1')) ?>
        </p>
        <div class="learn-goals">
          <h3><?= htmlspecialchars(t('activities.ski.learn_title')) ?></h3>
          <ul>
            <li><?= htmlspecialchars(t('activities.ski.learn_1')) ?></li>
            <li><?= htmlspecialchars(t('activities.ski.learn_2')) ?></li>
            <li><?= htmlspecialchars(t('activities.ski.learn_3')) ?></li>
            <li><?= htmlspecialchars(t('activities.ski.learn_4')) ?></li>
          </ul>
        </div>
        <div class="target-group">
          <h3><?= htmlspecialchars(t('activities.ski.target_title')) ?></h3>
          <p>
            <?= htmlspecialchars(t('activities.ski.target_p')) ?>
          </p>
        </div>
        <div class="meta-info">
          <div><?= htmlspecialchars(t('activities.ski.meta_1')) ?></div>
          <div><?= htmlspecialchars(t('activities.ski.meta_2')) ?></div>
          <div><?= htmlspecialchars(t('activities.ski.meta_3')) ?></div>
        </div>
      </div>
    </div>
  </section>
</main>

<!-- Call to action -->
<section class="cta-section container">
  <div class="cta-content">
    <p><?= htmlspecialchars(t('activities.cta.text')) ?></p>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLScIWPgULw7AtR9Gsvh3Mm8ma5AXzohL4UAUQsKdyZHTTnmqHg/viewform?usp=sf_link"
      target="_blank" rel="noopener" class="button-link" role="button">
      <?= htmlspecialchars(t('activities.cta.button')) ?>
    </a>
  </div>
</section>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
