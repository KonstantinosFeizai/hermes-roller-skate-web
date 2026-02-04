<?php
// whoweare.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('whoweare.meta.title');
$pageDescription = t('whoweare.meta.description');
$pageKeywords = t('whoweare.meta.keywords');
$pageCss = 'css/whoweare.css';
$activePage = 'whoweare';

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- WHO WE ARE PAGE CONTENT -->
<main>
  <!-- Intro / Mission -->
  <section id="who-we-are" class="who-we-are section">
    <div class="container">
      <h2 class="section-title"><?= htmlspecialchars(t('whoweare.hero.title')) ?></h2>
      <p class="section-intro">
        <?= htmlspecialchars(t('whoweare.hero.intro')) ?>
      </p>

      <div class="about-content">
        <div class="about-text">
          <div class="about-mission">
            <h3><?= htmlspecialchars(t('whoweare.mission.title')) ?></h3>
            <p>
              <?= htmlspecialchars(t('whoweare.mission.text')) ?>
            </p>
          </div>

          <h3><?= htmlspecialchars(t('whoweare.different.title')) ?></h3>
          <ul class="differentiators">
            <li>
              <i class="fas fa-trophy"></i> <?= htmlspecialchars(t('whoweare.different.d1')) ?>
            </li>
            <li>
              <i class="fas fa-skating fa-lg" style="color: #FFD43B;"></i>
              <?= htmlspecialchars(t('whoweare.different.d2')) ?>
            </li>
            <li>
              <i class="fas fa-graduation-cap"></i> <?= htmlspecialchars(t('whoweare.different.d3')) ?>
            </li>
            <li>
              <i class="fas fa-globe-americas"></i> <?= htmlspecialchars(t('whoweare.different.d4')) ?>
            </li>
          </ul>
        </div>

        <div class="about-image">
          <img src="photo/group2.webp" alt="<?= htmlspecialchars(t('whoweare.alts.hero_image')) ?>">
        </div>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section id="meet-the-team" class="meet-the-team section">
    <div class="container">
      <h2 class="section-title"><?= htmlspecialchars(t('whoweare.team.title')) ?></h2>
      <p class="section-intro">
        <?= htmlspecialchars(t('whoweare.team.intro')) ?>
      </p>

      <div class="staff-list">
        <div class="staff-member">
          <img src="photo/andreas.webp" alt="<?= htmlspecialchars(t('whoweare.alts.member1')) ?>">
          <h4><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars(t('whoweare.team.member1_title')) ?></h4>
          <p><?= htmlspecialchars(t('whoweare.team.member1_text')) ?></p>
        </div>

        <div class="staff-member">
          <img src="photo/georgia.webp" alt="<?= htmlspecialchars(t('whoweare.alts.member2')) ?>">
          <h4><i class="fa-solid fa-graduation-cap"></i> <?= htmlspecialchars(t('whoweare.team.member2_title')) ?></h4>
          <p><?= htmlspecialchars(t('whoweare.team.member2_text')) ?></p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
