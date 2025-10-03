<?php
// contact.php

$pageTitle = "Contact Us | Hermes Rollerskate Academy";
$pageDescription = "Get in touch with Hermes Rollerskate Academy for inquiries about classes, partnerships, or events in Zografou, Athens.";
$pageKeywords = "contact rollerskate, skating classes Athens, Hermes Rollerskate contact";
$pageCss = "css/contact.css";
$pageScripts = ["js/contact-validation.js?v=1.0.x"];
$activePage = "contact";

require_once __DIR__ . '/partials/header.php';
?>

<section id="contact-section">
  <div class="contact-header">
    <p class="contact-intro">
      <span>
        We'd love to hear from you!
      </span><br />
      <span>
        Feel free to reach out with any questions or feedback.
      </span>
    </p>
  </div>

  <form id="form" class="contact-form" method="post" action="#">
    <div class="input-group">
      <label for="name-input" class="sr-only">Name</label>
      <input type="text" name="name" id="name-input" placeholder="Name" />
    </div>

    <div class="input-group">
      <label for="surname-input" class="sr-only">Surname</label>
      <input type="text" name="surname" id="surname-input" placeholder="Surname" />
    </div>

    <div class="input-group">
      <label for="email-input" class="sr-only">Email</label>
      <input type="email" name="email" id="email-input" placeholder="Email" />
    </div>

    <div class="input-group">
      <label for="phone-input" class="sr-only">Phone</label>
      <input type="tel" name="phone" id="phone-input" placeholder="Phone" />
    </div>

    <div class="input-group">
      <label for="category-input" class="sr-only">Category</label>
      <select name="category" id="category-input" data-en-placeholder="Category">
        <option value="" disabled selected hidden>
          Select Category
        </option>
        <option value="general">General Inquiry</option>
        <option value="classes">Classes</option>
        <option value="merchandise">Merchandise</option>
        <option value="partnerships">Partnerships</option>
        <option value="feedback">Feedback</option>
        <option value="other">Other</option>
      </select>
    </div>

    <div class="input-group">
      <label for="subject-input" class="sr-only">Subject</label>
      <input type="text" name="subject" id="subject-input" placeholder="Subject" />
    </div>

    <div class="input-group">
      <label for="text-input" class="sr-only">Message</label>
      <textarea name="message" id="text-input" placeholder="Message"></textarea>
    </div>

    <button type="submit">Send Message</button>
  </form>

  <div id="response-container">
    <div id="form-response"></div>
    <div id="redirect-message" class="redirect-text"></div>
    <button id="new-message-btn" class="new-message-btn">Send another message</button>
  </div>
</section>
<?php
require_once __DIR__ . '/partials/footer.php';
?>