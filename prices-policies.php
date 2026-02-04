<?php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('prices.meta.title');
$pageDescription = t('prices.meta.description');
$pageKeywords = t('prices.meta.keywords');
$pageCss = ['css/prices.css'];
$activePage = 'prices-policies';

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>


<!-- PRICES & POLICIES PAGE CONTENT -->
<main>
  <div class="container">

    <!-- Card 1: Lesson Pricing -->
    <div class="card">
      <h2><?= htmlspecialchars(t('prices.cards.lesson.title')) ?></h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span><?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.lesson.head_discount')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?></span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.row_individual')) ?></span>
          <span class="cell" data-label="Discount">—</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?>">25€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.detail_individual')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.row_student')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_discount')) ?>">20%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?>">20€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.detail_student')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.row_family2')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_discount')) ?>">20%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?>">40€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.detail_family')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.row_family3')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_discount')) ?>">30%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?>">62€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.detail_family')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_category')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.row_referral')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_discount')) ?>">-5€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_price')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.price_referral')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.lesson.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.lesson.detail_referral')) ?></span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.lesson.note')) ?>
      </div>
    </div>

    <!-- Card 2: Private Lessons Pricing -->
    <div class="card">
      <h2><?= htmlspecialchars(t('prices.cards.private.title')) ?></h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span><?= htmlspecialchars(t('prices.cards.private.head_participants')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.private.head_price')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.private.head_discount')) ?></span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_participants')) ?>">1</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_price')) ?>">15€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_discount')) ?>"><?= htmlspecialchars(t('prices.cards.private.discount_none')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_participants')) ?>">2</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_price')) ?>">25€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_discount')) ?>">17%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_participants')) ?>">3</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_price')) ?>">30€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.private.head_discount')) ?>">33%</span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.private.note')) ?>
      </div>
    </div>

    <!-- Card 3: Special Workshops Pricing -->
    <div class="card">
      <h2><?= htmlspecialchars(t('prices.cards.workshops.title')) ?></h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span><?= htmlspecialchars(t('prices.cards.workshops.head_participants')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.workshops.head_price')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.workshops.head_refund')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.workshops.head_details')) ?></span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_participants')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.row_active')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_price')) ?>">10€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_refund')) ?>">75%-50%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.detail_active')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_participants')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.row_inactive')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_price')) ?>">12€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_refund')) ?>">75%-50%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.detail_inactive')) ?></span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_participants')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.row_external')) ?></span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_price')) ?>">15€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_refund')) ?>">75%-50%</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.workshops.head_details')) ?>"><?= htmlspecialchars(t('prices.cards.workshops.detail_external')) ?></span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.workshops.note')) ?>
      </div>
    </div>

    <!-- Card 4: Schools Pricing -->
    <div class="card">
      <h2><?= htmlspecialchars(t('prices.cards.schools.title')) ?></h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span><?= htmlspecialchars(t('prices.cards.schools.head_kids')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.schools.head_price')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.schools.head_min')) ?></span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_kids')) ?>">5</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_price')) ?>">120€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_min')) ?>">120€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_kids')) ?>">10</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_price')) ?>">220€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_min')) ?>">150€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_kids')) ?>">15</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_price')) ?>">300€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_min')) ?>">180€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_kids')) ?>">20</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_price')) ?>">360€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.schools.head_min')) ?>">200€</span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.schools.note')) ?>
      </div>
    </div>

    <!-- Card 5: Equipment Rental Pricing -->
    <div class="card">
      <h2><?= htmlspecialchars(t('prices.cards.rental.title')) ?></h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span><?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?></span>
          <span><?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?></span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?>">2 Days</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?>">500€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?>">100%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?>">9 Days</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?>">750€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?>">90%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?>">18 Days</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?>">1200€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?>">80%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?>">27 Days</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?>">1550€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?>">70%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_duration')) ?>">36 Days</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_cost')) ?>">1900€</span>
          <span class="cell" data-label="<?= htmlspecialchars(t('prices.cards.rental.head_advance')) ?>">60%</span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.rental.note1')) ?>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.rental.note2')) ?>
      </div>
    </div>

    <!-- Card 6: Weather & Cancellations -->
    <div class="card">
      <h2>
        <?= htmlspecialchars(t('prices.cards.weather.title')) ?>
      </h2>
      <div class="cancellation-reasons-container">
        <div class="cancellation-pill">
          <span class="icon">☔</span>
          <span><?= htmlspecialchars(t('prices.cards.weather.rain')) ?></span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥶</span>
          <span><?= htmlspecialchars(t('prices.cards.weather.cold')) ?></span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥵</span>
          <span><?= htmlspecialchars(t('prices.cards.weather.hot')) ?></span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🚨</span>
          <span><?= htmlspecialchars(t('prices.cards.weather.coach')) ?></span>
        </div>
      </div>
      <div class="note">
        <?= htmlspecialchars(t('prices.cards.weather.note')) ?>
      </div>
    </div>


  </div>
</main>

<!-- Shared footer -->
<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>