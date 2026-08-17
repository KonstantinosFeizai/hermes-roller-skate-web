<?php
// classes.php
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

$pageTitle       = t('classes.meta.title');
$pageDescription = t('classes.meta.description');
$pageKeywords    = t('classes.meta.keywords');
$pageCss         = ['css/classes.css'];
$activePage      = 'classes';

require_once PROJECT_ROOT . 'partials/header.php';
?>


<main>
  <!-- NEW HERO SECTION -->
  <section class="inner-hero" style="background-image: linear-gradient(to top, rgba(10, 25, 47, 0.9), rgba(10, 25, 47, 0.6), rgba(10, 25, 47, 0.4)), url('photo/prices.jpg');">
    <div class="container hero-container">
      <h1 class="hero-title"><?= htmlspecialchars(t('classes.hero.title')) ?></h1>
      <p class="hero-subtitle"><?= htmlspecialchars(t('classes.hero.intro')) ?></p>
    </div>
  </section>

  <section id="classes" class="classes section">
    <div class="container">
      <div class="classes__grid">

        <!-- ① OPEN PRIVATE -->
        <article class="class-card">
          <figure class="class-card__media">
            <img src="<?= asset('photo/private lesson.webp') ?>"
              alt="<?= htmlspecialchars(t('classes.private.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.private.title')) ?></h2>
              <span class="class-card__tag class-card__tag--amber">
                <?= htmlspecialchars(t('classes.private.badge_label')) ?><br>
              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.private.text')) ?></p>

            <div class="class-card__info-block">
              <p class="class-card__info-title">
                <?= htmlspecialchars(t('classes.private.included_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.private.included_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">
                <?= htmlspecialchars(t('classes.private.milestones.title')) ?>
              </p>
              <div class="class-card__milestone-table">
                <div class="milestone-row">
                  <span class="milestone-age"><?= htmlspecialchars(t('classes.private.milestones.age_6_12_label')) ?></span>
                  <span class="milestone-val"><?= htmlspecialchars(t('classes.private.milestones.age_6_12_val')) ?></span>
                </div>
                <div class="milestone-row">
                  <span class="milestone-age"><?= htmlspecialchars(t('classes.private.milestones.age_5_6_label')) ?></span>
                  <span class="milestone-val"><?= htmlspecialchars(t('classes.private.milestones.age_5_6_val')) ?></span>
                </div>
                <div class="milestone-row">
                  <span class="milestone-age"><?= htmlspecialchars(t('classes.private.milestones.age_3_5_label')) ?></span>
                  <span class="milestone-val"><?= htmlspecialchars(t('classes.private.milestones.age_3_5_val')) ?></span>
                </div>
              </div>
              <small class="class-card__note"><?= htmlspecialchars(t('classes.private.milestones.note')) ?></small>
            </div>

            <a href="https://calendly.com/hermesrollerskate/private-lesson-zografou?month=2025-09"
              target="_blank" rel="noopener noreferrer"
              class="premium-button btn-apply class-card__btn-premium">
              <?= htmlspecialchars(t('classes.private.btn_book')) ?>
              <!-- SVG Βελάκι που κινείται στο hover -->
              <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </a>

          </div>
        </article>

        <!-- ② BEGINNER GROUP -->
        <article class="class-card">
          <figure class="class-card__media">
            <img src="photo/spot3.webp"
              alt="<?= htmlspecialchars(t('classes.group.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.group.title')) ?></h2>
              <span class="class-card__tag class-card__tag--amber-dark">
                <?= htmlspecialchars(t('classes.group.badge_label')) ?><br>

              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.group.text')) ?></p>

            <div class="class-card__info-block">
              <p class="class-card__info-title">
                <?= htmlspecialchars(t('classes.group.learn_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.group.learn_beginner')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">
                <?= htmlspecialchars(t('classes.group.structure_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.group.structure_text')) ?></p>
            </div>
          </div>
        </article>

        <!-- ③ BASIC LEVEL -->
        <article class="class-card class-card--basic">
          <figure class="class-card__media">
            <img src="photo/basic_level.webp"
              alt="<?= htmlspecialchars(t('classes.basic.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.basic.title')) ?></h2>
              <span class="class-card__tag class-card__tag--grey">
                <?= htmlspecialchars(t('classes.basic.tag')) ?>
              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.basic.text')) ?></p>

            <p class="class-card__skills-label"><?= htmlspecialchars(t('classes.basic.learn_title')) ?></p>
            <div class="class-card__skills-row">
              <span class="skill-chip"><?= htmlspecialchars(t('classes.basic.skill_1')) ?></span>
              <span class="skill-chip"><?= htmlspecialchars(t('classes.basic.skill_2')) ?></span>
              <span class="skill-chip"><?= htmlspecialchars(t('classes.basic.skill_3')) ?></span>
              <span class="skill-chip"><?= htmlspecialchars(t('classes.basic.skill_4')) ?></span>
            </div>

            <p class="class-card__skills-note">
              <?= htmlspecialchars(t('classes.basic.learn_text')) ?>
            </p>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.basic.opportunities_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.basic.opportunities_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">
                <?= htmlspecialchars(t('classes.basic.structure_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.basic.structure_text')) ?></p>
            </div>
          </div>
        </article>

        <!-- ④ ADVANCED LEVEL -->
        <article class="class-card class-card--advanced">
          <figure class="class-card__media">
            <img src="photo/advanced_action.webp"
              alt="<?= htmlspecialchars(t('classes.advanced.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.advanced.title')) ?></h2>
              <span class="class-card__tag class-card__tag--dark">
                <?= htmlspecialchars(t('classes.advanced.tag')) ?>
              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.advanced.text')) ?></p>

            <div class="class-card__season-grid">
              <div class="season-chip">
                <span class="season-label"><?= htmlspecialchars(t('classes.advanced.season_winter_label')) ?></span>
                <span class="season-val"><?= htmlspecialchars(t('classes.advanced.season_winter')) ?></span>
              </div>
              <div class="season-chip">
                <span class="season-label"><?= htmlspecialchars(t('classes.advanced.season_spring_label')) ?></span>
                <span class="season-val"><?= htmlspecialchars(t('classes.advanced.season_spring')) ?></span>
              </div>
              <div class="season-chip season-chip--full">
                <span class="season-label"><?= htmlspecialchars(t('classes.advanced.season_summer_label')) ?></span>
                <span class="season-val"><?= htmlspecialchars(t('classes.advanced.season_summer')) ?></span>
              </div>
            </div>

            <div class="class-card__info-block--highlight">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.advanced.learn_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.advanced.learn_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.advanced.opportunities_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.advanced.opportunities_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.advanced.structure_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.advanced.structure_text')) ?></p>
            </div>
          </div>
        </article>

        <!-- ⑤ PRE-COMPETITIVE -->
        <article class="class-card class-card--precomp">
          <figure class="class-card__media">
            <img src="photo/pre-comp.jpg"
              alt="<?= htmlspecialchars(t('classes.precomp.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.precomp.title')) ?></h2>
              <span class="class-card__tag class-card__tag--violet">
                <?= htmlspecialchars(t('classes.precomp.tag')) ?>
              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.precomp.intro_1')) ?></p>
            <p class="class-card__text"><?= htmlspecialchars(t('classes.precomp.intro_2')) ?></p>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.precomp.includes_title')) ?>
              </p>
              <ul class="class-card__list">
                <?php for ($i = 1; $i <= 14; $i++): ?>
                  <li><?= htmlspecialchars(t("classes.precomp.skill_{$i}")) ?></li>
                <?php endfor; ?>
              </ul>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.precomp.requirements_title')) ?>
              </p>
              <ul class="class-card__list">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                  <li><?= htmlspecialchars(t("classes.precomp.requirement_{$i}")) ?></li>
                <?php endfor; ?>
              </ul>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.precomp.goal_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.precomp.goal_text')) ?></p>
            </div>
          </div>
        </article>

        <!-- ⑥ MIXED LEVEL -->
        <article class="class-card class-card--mixed">
          <figure class="class-card__media">
            <img src="photo/spot3.webp"
              alt="<?= htmlspecialchars(t('classes.mixed.alt')) ?>">
          </figure>
          <div class="class-card__body">

            <div class="class-card__header-row">
              <h2 class="class-card__title"><?= htmlspecialchars(t('classes.mixed.title')) ?></h2>
              <span class="class-card__tag class-card__tag--green">
                <?= htmlspecialchars(t('classes.mixed.tag')) ?>
              </span>
            </div>

            <p class="class-card__text"><?= htmlspecialchars(t('classes.mixed.text')) ?></p>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.mixed.learn_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.mixed.learn_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.mixed.opportunities_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.mixed.opportunities_text')) ?></p>
            </div>

            <div class="class-card__info-block">
              <p class="class-card__info-title">

                <?= htmlspecialchars(t('classes.mixed.structure_title')) ?>
              </p>
              <p class="class-card__info-text"><?= htmlspecialchars(t('classes.mixed.structure_text')) ?></p>
            </div>
          </div>
        </article>

      </div><!-- /.classes__grid -->
    </div><!-- /.container -->
  </section>
</main>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>