<?php
// classes.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('classes.meta.title');
$pageDescription = t('classes.meta.description');
$pageKeywords = t('classes.meta.keywords');
$pageCss = ['css/classes.css'];
$activePage = 'classes';

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- CLASSES PAGE CONTENT -->
<header class="classes__header">
  <div class="container">
    <h1 class="classes__title">
      <?= htmlspecialchars(t('classes.hero.title')) ?>
    </h1>
    <p class="classes__intro">
      <?= htmlspecialchars(t('classes.hero.intro')) ?>
    </p>
  </div>
</header>

<main>
  <!-- Classes list -->
  <section id="classes" class="classes section">
    <div class="container">
      <div class="classes__grid">

        <article class="class-card">
          <figure class="class-card__media">
            <img src="<?= asset('photo/private lesson.webp') ?>" alt="<?= htmlspecialchars(t('classes.private.alt')) ?>">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              <?= htmlspecialchars(t('classes.private.title')) ?>
            </h2>
            <p class="class-card__text">
              <?= htmlspecialchars(t('classes.private.text')) ?>
            </p>

            <dl class="class-card__details">

              <dt><?= htmlspecialchars(t('classes.private.included_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.private.included_text')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.private.milestones.title')) ?></dt>
              <dd>• <?= htmlspecialchars(t('classes.private.milestones.age_6_12')) ?></dd>
              <dd>• <?= htmlspecialchars(t('classes.private.milestones.age_5_6')) ?></dd>
              <dd>• <?= htmlspecialchars(t('classes.private.milestones.age_3_5')) ?></dd>
              <small style="display: block; margin-top: 5px; font-style: italic; opacity: 0.8;">
                <?= htmlspecialchars(t('classes.private.milestones.note')) ?>
              </small>
            </dl>
          </div>
        </article>

        <article class="class-card">
          <figure class="class-card__media">
            <img src="photo/spot3.webp" alt="<?= htmlspecialchars(t('classes.group.alt')) ?>">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              <?= htmlspecialchars(t('classes.group.title')) ?>
            </h2>
            <p class="class-card__text">
              <?= htmlspecialchars(t('classes.group.text')) ?>
            </p>

            <dl class="class-card__details">

              <dt><?= htmlspecialchars(t('classes.group.learn_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.group.learn_beginner')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.group.structure_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.group.structure_text')) ?></dd>
            </dl>
          </div>
        </article>
        <article class="class-card class-card--basic">
          <figure class="class-card__media">
            <img src="photo/basic_level.webp" alt="<?= htmlspecialchars(t('classes.basic.alt')) ?>">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              <?= htmlspecialchars(t('classes.basic.title')) ?>
            </h2>
            <p class="class-card__text">
              <?= htmlspecialchars(t('classes.basic.text')) ?>
            </p>

            <dl class="class-card__details">
              <dt><?= htmlspecialchars(t('classes.basic.learn_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.basic.learn_text')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.basic.opportunities_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.basic.opportunities_text')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.basic.structure_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.basic.structure_text')) ?></dd>
            </dl>
          </div>
        </article>
        <article class="class-card class-card--advanced">
          <figure class="class-card__media">
            <img src="photo/advanced_action.webp" alt="<?= htmlspecialchars(t('classes.advanced.alt')) ?>">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              <?= htmlspecialchars(t('classes.advanced.title')) ?>
            </h2>
            <p class="class-card__text">
              <?= htmlspecialchars(t('classes.advanced.text')) ?>
            </p>

            <dl class="class-card__details">
              <dt><?= htmlspecialchars(t('classes.advanced.learn_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.advanced.learn_text')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.advanced.opportunities_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.advanced.opportunities_text')) ?></dd>

              <dt><?= htmlspecialchars(t('classes.advanced.structure_title')) ?></dt>
              <dd><?= htmlspecialchars(t('classes.advanced.structure_text')) ?></dd>
            </dl>
          </div>
        </article>

      </div>
    </div>
  </section>
</main>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
