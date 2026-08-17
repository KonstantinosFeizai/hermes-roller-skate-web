<?php
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

$pageTitle = t('activities.meta.title');
$pageDescription = t('activities.meta.description');
$pageKeywords = t('activities.meta.keywords');
$pageCss = ['css/activities.css'];
$activePage = 'activities';

require_once PROJECT_ROOT . 'partials/header.php';
?>

<main class="activities-page">
  <!-- HERO SECTION -->
  <header class="activities-hero" style="background-image: linear-gradient(to top, rgba(10, 25, 46, 0.9), rgba(10, 25, 47, 0.6), rgba(10, 25, 47, 0.4)), url('<?= asset('photo/activities_bg.jpg') ?>');">
    <div class="container">
      <div class="activities-hero__inner">
        <h1><?= htmlspecialchars(t('activities.hero.title')) ?></h1>
        <p><?= htmlspecialchars(t('activities.hero.p1')) ?></p>
        <p><?= htmlspecialchars(t('activities.hero.p2')) ?></p>
      </div>
    </div>
  </header>

  <section class="activities-section">
    <div class="container">
      <div class="activities-feature">
        <div class="activities-media-stack">
          <article class="media-card">
            <img src="<?= asset('photo/iceskating2.webp') ?>" alt="<?= htmlspecialchars(t('activities.ice.alt')) ?>">
          </article>
          <article class="media-card">
            <img src="<?= asset('photo/niarxos.webp') ?>" alt="<?= htmlspecialchars(t('activities.ice.alt_2')) ?>">
          </article>
        </div>

        <article class="feature-panel">
          <h2><img src="<?= asset('photo/ice-skate.png') ?>" alt="" class="section-icon feature-icon"><?= htmlspecialchars(t('activities.ice.title')) ?></h2>
          <p class="feature-panel__lead"><?= htmlspecialchars(t('activities.ice.intro')) ?></p>

          <div class="feature-subsections-grid">
            <div class="feature-subsection">
              <h3><?= htmlspecialchars(t('activities.ice.learn_title')) ?></h3>
              <ul class="bullet-grid">
                <li><?= htmlspecialchars(t('activities.ice.learn_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.ice.learn_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.ice.learn_3')) ?></li>
                <li><?= htmlspecialchars(t('activities.ice.learn_4')) ?></li>
              </ul>
            </div>

            <div class="feature-subsection">
              <h3><?= htmlspecialchars(t('activities.ice.target_title')) ?></h3>
              <p><?= htmlspecialchars(t('activities.ice.target_p')) ?></p>
            </div>
          </div>

          <div class="feature-meta">
            <div class="feature-meta__item">
              <p class="meta-label">
                <img src="<?= asset('photo/clock.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ice.meta_label_1')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ice.meta_1')) ?></span>
              </div>
            </div>

            <div class="feature-meta__item">
              <p class="meta-label">
                <img src="<?= asset('photo/group.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ice.meta_label_2')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ice.meta_2')) ?></span>
              </div>
            </div>

            <div class="feature-meta__item">
              <p class="meta-label">
                <img src="<?= asset('photo/location.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ice.meta_label_3')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ice.meta_3')) ?></span>
              </div>
            </div>
          </div>
          <button class="premium-button activity-btn btn-apply" onclick="openLoginModal()" type="button">
            <span><?= htmlspecialchars(t('activities.ice.cta')) ?></span>
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </article>
      </div>
    </div>
  </section>

  <section class="pricing-section" id="pricing">
    <div class="container">
      <div class="section-heading">
        <h2><img src="<?= asset('photo/sack.png') ?>" alt="" class="section-icon"><?= htmlspecialchars(t('activities.pricing.title')) ?></h2>
        <p><?= htmlspecialchars(t('activities.pricing.subtitle')) ?></p>
      </div>

      <div class="pricing-grid">
        <article class="pricing-card pricing-card--members">
          <div class="card-header">
            <img src="<?= asset('photo/group.png') ?>" alt="" class="card-icon">
            <div>
              <h3><?= htmlspecialchars(t('activities.pricing.members_title')) ?></h3>
            </div>
          </div>
          <div class="pricing-info-box">
            <span><?= htmlspecialchars(t('activities.pricing.members_desc')) ?></span>
          </div>
          <div class="pricing-family-section">
            <h4>
              <?= htmlspecialchars(t('activities.pricing.family_title')) ?>
            </h4>
            <div class="pricing-family-price">
              <span class="price"><?= htmlspecialchars(t('activities.pricing.family_price')) ?></span>
            </div>
          </div>
          <div class="pricing-info-box">
            <img src="<?= asset('photo/information.png') ?>" alt="" class="inline-icon">
            <span><?= htmlspecialchars(t('activities.pricing.note')) ?></span>
          </div>
        </article>

        <article class="pricing-card">
          <div class="card-header">
            <img src="<?= asset('photo/group.png') ?>" alt="" class="card-icon">
            <div>
              <h3><?= htmlspecialchars(t('activities.pricing.nonmembers_title')) ?></h3>
            </div>
          </div>
          <div class="price-table">
            <div class="price-table-header">
              <span><?= htmlspecialchars(t('activities.ski.meta_label_group')) ?></span>
              <span><?= htmlspecialchars(t('activities.ski.meta_label_price')) ?></span>
            </div>
            <?php
            $priceRows = [
              ['label' => t('activities.pricing.nonmembers_label_1'), 'price' => t('activities.pricing.nonmembers_price_1')],
              ['label' => t('activities.pricing.nonmembers_label_2'), 'price' => t('activities.pricing.nonmembers_price_2')],
              ['label' => t('activities.pricing.nonmembers_label_3'), 'price' => t('activities.pricing.nonmembers_price_3')],
              ['label' => t('activities.pricing.nonmembers_label_4'), 'price' => t('activities.pricing.nonmembers_price_4')],
            ];
            foreach ($priceRows as $row):
            ?>
              <div class="price-row"><span><?= htmlspecialchars($row['label']) ?></span><strong><?= htmlspecialchars($row['price']) ?></strong></div>
            <?php endforeach; ?>
          </div>
          <p class="pricing-table-note"><?= htmlspecialchars(t('activities.pricing.group_booking_note', 'Group bookings of 5+ please contact us for special rates.')) ?></p>
        </article>
      </div>
    </div>
  </section>

  <section class="activities-section activities-section--ski">
    <div class="container">
      <div class="ski-panel">
        <figure class="ski-panel__media">
          <img src="<?= asset('photo/ski.jpg') ?>" alt="<?= htmlspecialchars(t('activities.ski.alt')) ?>">
        </figure>
        <article class="ski-panel__content">
          <h2><img src="<?= asset('photo/skiing.png') ?>" alt="" class="section-icon feature-icon"><?= htmlspecialchars(t('activities.ski.title')) ?></h2>

          <p class="feature-panel__lead"><?= htmlspecialchars(t('activities.ski.intro')) ?></p>

          <div class="ski-learn-grid">
            <div class="feature-subsection">
              <h3><?= htmlspecialchars(t('activities.ski.learn_title')) ?></h3>
              <ul class="bullet-grid bullet-grid--compact">
                <li><?= htmlspecialchars(t('activities.ski.learn_1')) ?></li>
                <li><?= htmlspecialchars(t('activities.ski.learn_2')) ?></li>
                <li><?= htmlspecialchars(t('activities.ski.learn_3')) ?></li>
                <li><?= htmlspecialchars(t('activities.ski.learn_4')) ?></li>
              </ul>
            </div>

            <div class="feature-subsection">
              <h3><?= htmlspecialchars(t('activities.ski.target_title')) ?></h3>
              <p><?= htmlspecialchars(t('activities.ski.target_p')) ?></p>
            </div>
          </div>

          <div class="ski-meta-box">
            <div class="ski-meta-item">
              <p class="meta-label">
                <img src="<?= asset('photo/clock.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ski.meta_label_duration', 'Duration')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ski.meta_1')) ?></span>
              </div>
            </div>

            <div class="ski-meta-item">
              <p class="meta-label">
                <img src="<?= asset('photo/group.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ski.meta_label_group', 'Group Size')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ski.meta_2')) ?></span>
              </div>
            </div>

            <div class="ski-meta-item">
              <p class="meta-label">
                <img src="<?= asset('photo/location.png') ?>" alt="" class="meta-icon">
                <?= htmlspecialchars(t('activities.ski.meta_label_location', 'Location')) ?>
              </p>
              <div class="meta-value">
                <span><?= htmlspecialchars(t('activities.ski.meta_3')) ?></span>
              </div>
            </div>
          </div>

          <button class="premium-button activity-btn activity-btn--gold btn-apply" onclick="openLoginModal()" type="button">
            <span><?= htmlspecialchars(t('activities.ski.cta')) ?></span>
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <line x1="5" y1="12" x2="19" y2="12"></line>
              <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
          </button>
        </article>
      </div>
    </div>
  </section>

  <section class="schedule-section" id="schedule">
    <div class="container">
      <div class="section-heading">
        <h2><?= htmlspecialchars(t('activities.program.title')) ?></h2>
      </div>

      <div class="schedule-grid">
        <article class="schedule-card">
          <h3 class="schedule-card__title"><img src="<?= asset('photo/clock.png') ?>" alt="" class="card-icon"><?= htmlspecialchars(t('activities.program.schedule_title')) ?></h3>
          <ol class="timeline">
            <?php for ($i = 1; $i <= 6; $i++): ?>
              <li class="timeline__item">
                <span class="timeline__dot"></span>
                <div class="timeline__body">
                  <span class="timeline__time"><?= htmlspecialchars(t("activities.program.schedule_time_{$i}")) ?></span>
                  <strong><?= htmlspecialchars(t("activities.program.schedule_name_{$i}")) ?></strong>
                  <p><?= htmlspecialchars(t("activities.program.schedule_desc_{$i}")) ?></p>
                </div>
              </li>
            <?php endfor; ?>
          </ol>
          <div class="fees-note-box"><img src="<?= asset('photo/accept.png') ?>" alt="" class="meta-icon">
            <p><?= htmlspecialchars(t('activities.program.schedule_note_1')) ?></p>
          </div>
        </article>

        <article class="schedule-card schedule-card--fees">
          <h3 class="schedule-card__title"><img src="<?= asset('photo/sack.png') ?>" alt="" class="card-icon"><?= htmlspecialchars(t('activities.program.fees_title')) ?></h3>
          <div class="fees-grid">
            <div class="fee-column">
              <h4><img src="<?= asset('photo/group.png') ?>" alt="" class="fee-column-icon"><?= htmlspecialchars(t('activities.program.members_title')) ?></h4>
              <?php
              $membersFees = [
                ['label' => htmlspecialchars(t('activities.program.members_label_1')), 'price' => htmlspecialchars(t('activities.program.members_price_1')), 'per_person' => htmlspecialchars(t('activities.program.members_per_1')), 'discount' => null, 'best' => false],
                ['label' => htmlspecialchars(t('activities.program.members_label_2')), 'price' => htmlspecialchars(t('activities.program.members_price_2')), 'per_person' => htmlspecialchars(t('activities.program.members_per_2')), 'discount' => htmlspecialchars(t('activities.program.members_discount_2')), 'best' => false],
                ['label' => htmlspecialchars(t('activities.program.members_label_3')), 'price' => htmlspecialchars(t('activities.program.members_price_3')), 'per_person' => htmlspecialchars(t('activities.program.members_per_3')), 'discount' => htmlspecialchars(t('activities.program.members_discount_3')), 'best' => false],
                ['label' => htmlspecialchars(t('activities.program.members_label_4')), 'price' => htmlspecialchars(t('activities.program.members_price_4')), 'per_person' => htmlspecialchars(t('activities.program.members_per_4')), 'discount' => htmlspecialchars(t('activities.program.members_discount_4')), 'best' => true],
              ];
              foreach ($membersFees as $fee):
              ?>
                <div class="fee-card<?= $fee['best'] ? ' fee-card--best' : '' ?>">
                  <div class="fee-card__header">
                    <span class="fee-label"><?= htmlspecialchars($fee['label']) ?></span>
                    <?php if ($fee['best']): ?>
                      <span class="fee-badge"><?= htmlspecialchars(t('activities.program.best_value')) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="fee-card__price"><?= htmlspecialchars($fee['price']) ?></div>
                  <div class="fee-card__breakdown">
                    <span class="per-person"><?= htmlspecialchars($fee['per_person']) ?></span>
                    <?php if ($fee['discount']): ?>
                      <span class="discount"><?= htmlspecialchars($fee['discount']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
            <div class="fee-column">
              <h4><img src="<?= asset('photo/group.png') ?>" alt="" class="fee-column-icon"><?= htmlspecialchars(t('activities.program.nonmembers_title')) ?></h4>
              <?php
              $nonmembersFees = [
                ['label' => t('activities.program.nonmembers_label_1'), 'price' => t('activities.program.nonmembers_price_1'), 'per_person' => t('activities.program.nonmembers_per_1'), 'discount' => null, 'best' => false],
                ['label' => t('activities.program.nonmembers_label_2'), 'price' => t('activities.program.nonmembers_price_2'), 'per_person' => t('activities.program.nonmembers_per_2'), 'discount' => t('activities.program.nonmembers_discount_2'), 'best' => false],
                ['label' => t('activities.program.nonmembers_label_3'), 'price' => t('activities.program.nonmembers_price_3'), 'per_person' => t('activities.program.nonmembers_per_3'), 'discount' => t('activities.program.nonmembers_discount_3'), 'best' => false],
                ['label' => t('activities.program.nonmembers_label_4'), 'price' => t('activities.program.nonmembers_price_4'), 'per_person' => t('activities.program.nonmembers_per_4'), 'discount' => t('activities.program.nonmembers_discount_4'), 'best' => false],
              ];
              foreach ($nonmembersFees as $fee):
              ?>
                <div class="fee-card<?= $fee['best'] ? ' fee-card--best' : '' ?>">
                  <div class="fee-card__header">
                    <span class="fee-label"><?= htmlspecialchars($fee['label']) ?></span>
                    <?php if ($fee['best']): ?>
                      <span class="fee-badge"><?= htmlspecialchars(t('activities.program.best_value')) ?></span>
                    <?php endif; ?>
                  </div>
                  <div class="fee-card__price"><?= htmlspecialchars($fee['price']) ?></div>
                  <div class="fee-card__breakdown">
                    <span class="per-person"><?= htmlspecialchars($fee['per_person']) ?></span>
                    <?php if ($fee['discount']): ?>
                      <span class="discount"><?= htmlspecialchars($fee['discount']) ?></span>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="fees-note-box"><img src="<?= asset('photo/accept.png') ?>" alt="" class="meta-icon">
            <p><?= htmlspecialchars(t('activities.program.fees_note')) ?></p>
          </div>
        </article>
      </div>
    </div>
  </section>

  <section class="info-section">
    <div class="container info-container">
      <div class="section-heading">
        <h2><?= htmlspecialchars(t('activities.info.title')) ?></h2>
      </div>

      <div class="cost-grid">
        <article class="cost-card">
          <div class="cost-card__head">
            <h3><?= htmlspecialchars(t('activities.info.costs.mainalo_title')) ?></h3><img src="<?= asset('photo/mountain.png') ?>" alt="" class="cost-card__icon">
          </div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_slopes')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.mainalo_1')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_skiset')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.mainalo_2')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_bus')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.mainalo_3')) ?></strong></div>
        </article>
        <article class="cost-card">
          <div class="cost-card__head">
            <h3><?= htmlspecialchars(t('activities.info.costs.parnassos_title')) ?></h3><img src="<?= asset('photo/mountain.png') ?>" alt="" class="cost-card__icon">
          </div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_slopes')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.parnassos_1')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_skiset')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.parnassos_2')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_bus')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.parnassos_3')) ?></strong></div>
        </article>
        <article class="cost-card">
          <div class="cost-card__head">
            <h3><?= htmlspecialchars(t('activities.info.costs.kalavryta_title')) ?></h3><img src="<?= asset('photo/mountain.png') ?>" alt="" class="cost-card__icon">
          </div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_slopes')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.kalavryta_1')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_skiset')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.kalavryta_2')) ?></strong></div>
          <div class="cost-row"><span><?= htmlspecialchars(t('activities.info.costs.label_bus')) ?></span><strong><?= htmlspecialchars(t('activities.info.costs.kalavryta_3')) ?></strong></div>
        </article>
      </div>

      <div class="info-grid">
        <article class="info-card light-blue-bg">
          <div class="info-card__head">
            <h3><?= htmlspecialchars(t('activities.info.clothing_title')) ?></h3>
            <img src="<?= asset('photo/pants.png') ?>" alt="" class="cost-card__icon">
          </div>
          <ul class="icon-list icon-list--cols">
            <?php for ($i = 1; $i <= 8; $i++): ?>
              <li>
                <img src="<?= asset('photo/check.png') ?>" alt="" class="meta-icon eq-icon" style="flex-shrink: 0;">
                <span class="icon-list__name">
                  <?= htmlspecialchars(t("activities.info.clothing_{$i}")) ?>
                </span>
              </li>
            <?php endfor; ?>
          </ul>
        </article>

        <article class="info-card light-blue-bg">
          <div class="info-card__head">
            <h3><?= htmlspecialchars(t('activities.info.equipment_title')) ?></h3>
            <img src="<?= asset('photo/backpack.png') ?>" alt="" class="cost-card__icon">
          </div>
          <ul class="icon-list icon-list--stacked">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <li>
                <span class="icon-list__name">
                  <img src="<?= asset('photo/check.png') ?>" alt="" display="inline" class="meta-icon eq-icon" style="margin-right: 8px;">
                  <?= htmlspecialchars(t("activities.info.equipment_item_{$i}")) ?>
                </span>
                <span class="icon-list__sub"><?= htmlspecialchars(t("activities.info.equipment_sub_{$i}")) ?></span>
              </li>
            <?php endfor; ?>
          </ul>
        </article>
      </div>
      <p class="activities-quote"><?= htmlspecialchars(t('activities.cta.text')) ?></p>
    </div>
  </section>
</main>

<?php require_once PROJECT_ROOT . 'partials/footer.php'; ?>