<?php
// policies.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('policies.meta.title');
$pageDescription = t('policies.meta.description');
$pageKeywords = t('policies.meta.keywords');
$pageCss = ['css/policies.css'];
$activePage = 'policies';


// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>
<!-- POLICIES PAGE CONTENT -->
<main>
  <!-- Intro -->
  <section id="intro" class="policy-section">
    <div class="policy-container">
      <h1>
        <?= htmlspecialchars(t('policies.intro.title')) ?>
      </h1>
      <p>
        <?= htmlspecialchars(t('policies.intro.text')) ?>
      </p>
    </div>
  </section>

  <!-- Terms & Conditions -->
  <section class="policy-section" id="terms">
    <div class="policy-container">
      <h2>
        <?= htmlspecialchars(t('policies.terms.title')) ?></h2>
      <p>
        <?= htmlspecialchars(t('policies.terms.intro')) ?>
      </p>

      <ul class="policy-list">
        <li><?= htmlspecialchars(t('policies.terms.items.0')) ?></li>
        <li><?= htmlspecialchars(t('policies.terms.items.1')) ?></li>
        <li><?= htmlspecialchars(t('policies.terms.items.2')) ?></li>
        <li><?= htmlspecialchars(t('policies.terms.items.3')) ?></li>
        <li><?= htmlspecialchars(t('policies.terms.items.4')) ?></li>
      </ul>

      <p>
        <?= htmlspecialchars(t('policies.terms.contact')) ?>
      </p>
    </div>
  </section>

  <!-- Privacy Policy -->
  <section class="policy-section" id="privacy">
    <div class="policy-container">
      <h2><?= htmlspecialchars(t('policies.privacy.title')) ?></h2>
      <p>
        <?= htmlspecialchars(t('policies.privacy.intro')) ?>
      </p>

      <ul class="policy-list">
        <li><?= htmlspecialchars(t('policies.privacy.items.0')) ?></li>
        <li><?= htmlspecialchars(t('policies.privacy.items.1')) ?></li>
        <li><?= htmlspecialchars(t('policies.privacy.items.2')) ?></li>
        <li><?= htmlspecialchars(t('policies.privacy.items.3')) ?></li>
        <li><?= htmlspecialchars(t('policies.privacy.items.4')) ?></li>
      </ul>
    </div>
  </section>

  <!-- Refund & Cancellation -->
  <section class="policy-section" id="refund">
    <div class="policy-container">
      <h2>
        <?= htmlspecialchars(t('policies.refund.title')) ?>
      </h2>
      <p>
        <?= htmlspecialchars(t('policies.refund.intro')) ?>
      </p>

      <ul class="policy-list">
        <li><?= htmlspecialchars(t('policies.refund.items.0')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.1')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.2')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.3')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.4')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.5')) ?></li>
        <li><?= htmlspecialchars(t('policies.refund.items.6')) ?></li>
      </ul>

      <p>
        <?= htmlspecialchars(t('policies.refund.note')) ?>
      </p>
    </div>
  </section>

  <!-- Safety Guidelines -->
  <section class="policy-section" id="safety">
    <div class="policy-container">
      <h2><?= htmlspecialchars(t('policies.safety.title')) ?></h2>
      <ul class="policy-list">
        <li><?= htmlspecialchars(t('policies.safety.items.0')) ?></li>
        <li><?= htmlspecialchars(t('policies.safety.items.1')) ?></li>
        <li><?= htmlspecialchars(t('policies.safety.items.2')) ?></li>
        <li><?= htmlspecialchars(t('policies.safety.items.3')) ?></li>
        <li><?= htmlspecialchars(t('policies.safety.items.4')) ?></li>
        <li><?= htmlspecialchars(t('policies.safety.items.5')) ?></li>
      </ul>
    </div>
  </section>

  <!-- FAQ -->
  <section class="policy-section" id="FAQ">
    <div class="policy-container">
      <h2><?= htmlspecialchars(t('policies.faq.title')) ?></h2>
      <dl class="faq-list">
        <dt><?= htmlspecialchars(t('policies.faq.items.0.q')) ?></dt>
        <dd><?= htmlspecialchars(t('policies.faq.items.0.a')) ?></dd>

        <dt><?= htmlspecialchars(t('policies.faq.items.1.q')) ?></dt>
        <dd><?= htmlspecialchars(t('policies.faq.items.1.a')) ?></dd>

        <dt><?= htmlspecialchars(t('policies.faq.items.2.q')) ?></dt>
        <dd><?= htmlspecialchars(t('policies.faq.items.2.a')) ?></dd>

        <dt><?= htmlspecialchars(t('policies.faq.items.3.q')) ?></dt>
        <dd><?= htmlspecialchars(t('policies.faq.items.3.a')) ?></dd>

        <dt><?= htmlspecialchars(t('policies.faq.items.4.q')) ?></dt>
        <dd><?= htmlspecialchars(t('policies.faq.items.4.a')) ?></dd>
      </dl>
    </div>
  </section>

</main>

<!-- Shared footer -->
<?php
require_once PROJECT_ROOT . 'partials/footer.php';
?>