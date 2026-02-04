<?php
// contact.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('contact.meta.title');
$pageDescription = t('contact.meta.description');
$pageKeywords = t('contact.meta.keywords');
$pageCss = ["css/contact.css"];
$pageScripts = ["js/contact-validation.js?v=1.0.x"];
$activePage = "contact";

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- CONTACT PAGE CONTENT -->
<section id="contact-section">
  <div class="contact-header">
    <p class="contact-intro">
      <span>
        <?= htmlspecialchars(t('contact.intro.line1')) ?>
      </span><br />
      <span>
        <?= htmlspecialchars(t('contact.intro.line2')) ?>
      </span>
    </p>
  </div>

  <!-- Contact form -->
  <form id="form" class="contact-form" method="post" action="#">
    <div class="input-group">
      <label for="name-input" class="sr-only"><?= htmlspecialchars(t('contact.form.name')) ?></label>
      <input type="text" name="name" id="name-input" placeholder="<?= htmlspecialchars(t('contact.form.name')) ?>" />
    </div>

    <div class="input-group">
      <label for="surname-input" class="sr-only"><?= htmlspecialchars(t('contact.form.surname')) ?></label>
      <input type="text" name="surname" id="surname-input" placeholder="<?= htmlspecialchars(t('contact.form.surname')) ?>" />
    </div>

    <div class="input-group">
      <label for="email-input" class="sr-only"><?= htmlspecialchars(t('contact.form.email')) ?></label>
      <input type="email" name="email" id="email-input" placeholder="<?= htmlspecialchars(t('contact.form.email')) ?>" />
    </div>

    <div class="input-group">
      <label for="phone-input" class="sr-only"><?= htmlspecialchars(t('contact.form.phone')) ?></label>
      <input type="tel" name="phone" id="phone-input" placeholder="<?= htmlspecialchars(t('contact.form.phone')) ?>" />
    </div>

    <div class="input-group">
      <label for="category-input" class="sr-only"><?= htmlspecialchars(t('contact.form.category')) ?></label>
      <select name="category" id="category-input" data-en-placeholder="<?= htmlspecialchars(t('contact.form.category')) ?>">
        <option value="" disabled selected hidden>
          <?= htmlspecialchars(t('contact.form.category_select')) ?>
        </option>
        <option value="general"><?= htmlspecialchars(t('contact.form.category_general')) ?></option>
        <option value="classes"><?= htmlspecialchars(t('contact.form.category_classes')) ?></option>
        <option value="merchandise"><?= htmlspecialchars(t('contact.form.category_merchandise')) ?></option>
        <option value="partnerships"><?= htmlspecialchars(t('contact.form.category_partnerships')) ?></option>
        <option value="feedback"><?= htmlspecialchars(t('contact.form.category_feedback')) ?></option>
        <option value="other"><?= htmlspecialchars(t('contact.form.category_other')) ?></option>
      </select>
    </div>

    <div class="input-group">
      <label for="subject-input" class="sr-only"><?= htmlspecialchars(t('contact.form.subject')) ?></label>
      <input type="text" name="subject" id="subject-input" placeholder="<?= htmlspecialchars(t('contact.form.subject')) ?>" />
    </div>

    <div class="input-group">
      <label for="text-input" class="sr-only"><?= htmlspecialchars(t('contact.form.message')) ?></label>
      <textarea name="message" id="text-input" placeholder="<?= htmlspecialchars(t('contact.form.message')) ?>"></textarea>
    </div>

    <button type="submit"><?= htmlspecialchars(t('contact.form.send')) ?></button>
  </form>

  <!-- Form response (success/error) -->
  <div id="response-container">
    <div id="form-response"></div>
    <div id="redirect-message" class="redirect-text"></div>
    <button id="new-message-btn" class="new-message-btn"><?= htmlspecialchars(t('contact.form.send_another')) ?></button>
  </div>
</section>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>