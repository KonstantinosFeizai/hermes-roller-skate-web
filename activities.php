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
        <img src="<?= asset('photo/iceskating2.webp') ?>" alt="<?= htmlspecialchars(t('activities.ice.alt')) ?>">
        <img src="<?= asset('photo/niarxos.webp') ?>" alt="<?= htmlspecialchars(t('activities.ice.alt_niarxos')) ?>">
      </figure>
      <div class="activity-content">
        <h2><?= htmlspecialchars(t('activities.ice.title')) ?></h2>
        <p>
          <?= htmlspecialchars(t('activities.ice.p1')) ?>
        </p>
        <div class="learn-goals">
          <h3><?= htmlspecialchars(t('activities.ice.learn_title')) ?></h3>
          <ul>




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

  <section class="pricing-section">
    <div class="container">
      <h2 class="section-title"><?= htmlspecialchars(t('activities.pricing.title')) ?></h2>

      <div class="pricing-layout">
        <div class="pricing-sidebar">
          <div class="info-box">
            <div class="info-content">
              <div class="info-text">
                <strong><?= htmlspecialchars(t('activities.pricing.members_title')) ?>:</strong>
                <?= htmlspecialchars(t('activities.pricing.members_desc')) ?>
              </div>
            </div>
          </div>

          <div class="info-box">
            <div class="info-content">
              <div class="info-text">
                <strong><?= htmlspecialchars(t('activities.pricing.family_title')) ?>:</strong>
                <span class="price-tag"><?= htmlspecialchars(t('activities.pricing.family_price')) ?></span>
              </div>
            </div>
          </div>
        </div>

        <div class="price-table-card">
          <div class="card-header">
            <h3><?= htmlspecialchars(t('activities.pricing.nonmembers_title')) ?></h3>
          </div>

          <ul class="price-rows">
            <li>
              <span><?= htmlspecialchars(t('activities.pricing.nonmembers_label_1')) ?></span>
              <strong><?= htmlspecialchars(t('activities.pricing.nonmembers_price_1')) ?></strong>
            </li>
            <li>
              <span><?= htmlspecialchars(t('activities.pricing.nonmembers_label_2')) ?></span>
              <strong><?= htmlspecialchars(t('activities.pricing.nonmembers_price_2')) ?></strong>
            </li>
            <li>
              <span><?= htmlspecialchars(t('activities.pricing.nonmembers_label_3')) ?></span>
              <strong><?= htmlspecialchars(t('activities.pricing.nonmembers_price_3')) ?></strong>
            </li>
            <li>
              <span><?= htmlspecialchars(t('activities.pricing.nonmembers_label_4')) ?></span>
              <strong><?= htmlspecialchars(t('activities.pricing.nonmembers_price_4')) ?></strong>
            </li>
          </ul>
        </div>
      </div>

      <p class="pricing-note">
        <?= htmlspecialchars(t('activities.pricing.note')) ?>
      </p>
    </div>
  </section>

  <!-- Ski section -->
  <section id="ski-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="<?= asset('photo/ski.webp') ?>" alt="<?= htmlspecialchars(t('activities.ski.alt')) ?>">
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

  <section class="program-section">
    <div class="container">
      <h2 class="section-title"><?= htmlspecialchars(t('activities.program.title')) ?></h2>

      <div class="program-layout">
        <div class="program-sidebar">
          <div class="info-box">
            <div class="info-content">
              <div class="info-text">
                <strong><?= htmlspecialchars(t('activities.program.schedule_title')) ?>:</strong>
                <ul class="program-list">
                  <li><?= htmlspecialchars(t('activities.program.schedule_1')) ?></li>
                  <li><?= htmlspecialchars(t('activities.program.schedule_2')) ?></li>
                  <li><?= htmlspecialchars(t('activities.program.schedule_3')) ?></li>
                  <li><?= htmlspecialchars(t('activities.program.schedule_4')) ?></li>
                  <li><?= htmlspecialchars(t('activities.program.schedule_5')) ?></li>
                  <li><?= htmlspecialchars(t('activities.program.schedule_6')) ?></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="info-box">
            <div class="info-content">
              <div class="info-text">
                <p><?= htmlspecialchars(t('activities.program.schedule_note_1')) ?></p>
                <p><?= htmlspecialchars(t('activities.program.schedule_note_2')) ?></p>
              </div>
            </div>
          </div>
        </div>

        <div class="price-table-card">
          <div class="card-header">
            <h3><?= htmlspecialchars(t('activities.program.fees_title')) ?></h3>
          </div>

          <div class="fees-group">
            <h4><?= htmlspecialchars(t('activities.program.members_title')) ?></h4>
            <ul class="program-rows">
              <li><?= htmlspecialchars(t('activities.program.members_1')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.members_2')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.members_3')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.members_4')) ?></li>
            </ul>
          </div>

          <div class="fees-group">
            <h4><?= htmlspecialchars(t('activities.program.nonmembers_title')) ?></h4>
            <ul class="program-rows">
              <li><?= htmlspecialchars(t('activities.program.nonmembers_1')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.nonmembers_2')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.nonmembers_3')) ?></li>
              <li><?= htmlspecialchars(t('activities.program.nonmembers_4')) ?></li>
            </ul>
          </div>
        </div>
      </div>

      <p class="pricing-note">
        <?= htmlspecialchars(t('activities.program.fees_note')) ?>
      </p>
    </div>
  </section>

  <section class="info-section">
    <div class="container">
      <h2 class="section-title"><?= htmlspecialchars(t('activities.info.title')) ?></h2>

      <div class="info-layout">
        <div class="info-text">
          <strong><?= htmlspecialchars(t('activities.info.extra_costs_title')) ?>:</strong>
          <p><?= htmlspecialchars(t('activities.info.extra_costs_desc')) ?></p>
        </div>
        <div class="info-box">

          <div class="cost-grid">
            <div class="cost-card">
              <h4><?= htmlspecialchars(t('activities.info.costs.mainalo_title')) ?></h4>
              <ul>
                <li><?= htmlspecialchars(t('activities.info.costs.mainalo_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.mainalo_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.mainalo_3')) ?></li>
              </ul>
            </div>

            <div class="cost-card">
              <h4><?= htmlspecialchars(t('activities.info.costs.parnassos_title')) ?></h4>
              <ul>
                <li><?= htmlspecialchars(t('activities.info.costs.parnassos_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.parnassos_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.parnassos_3')) ?></li>
              </ul>
            </div>

            <div class="cost-card">
              <h4><?= htmlspecialchars(t('activities.info.costs.kalavryta_title')) ?></h4>
              <ul>
                <li><?= htmlspecialchars(t('activities.info.costs.kalavryta_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.kalavryta_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.costs.kalavryta_3')) ?></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="info-box">
          <div class="info-columns">
            <div class="info-card-section">
              <h3><?= htmlspecialchars(t('activities.info.clothing_title')) ?></h3>
              <ul class="check-list">
                <li><?= htmlspecialchars(t('activities.info.clothing_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_3')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_4')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_5')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_6')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_7')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_8')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.clothing_9')) ?></li>
              </ul>
            </div>

            <div class="info-card-section">
              <h3><?= htmlspecialchars(t('activities.info.equipment_title')) ?></h3>
              <ul class="check-list">
                <li><?= htmlspecialchars(t('activities.info.equipment_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.equipment_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.equipment_3')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.equipment_4')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.equipment_5')) ?></li>
                <li><?= htmlspecialchars(t('activities.info.equipment_6')) ?></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>


<!-- Call to action -->
<section class="cta-section container">
  <div class="cta-content">
    <p><?= htmlspecialchars(t('activities.cta.text')) ?></p>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLSeAawGRiqE58WiY_K6jB6JIRDhrlj6ZxK-g9eXLRipInN01IA/viewform"
      target="_blank" rel="noopener" class="button-link" role="button">
      <?= htmlspecialchars(t('activities.cta.button')) ?>
    </a>
  </div>
</section>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
