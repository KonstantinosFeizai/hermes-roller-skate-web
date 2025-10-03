<?php
// contact.php

$pageTitle = "Επικοινωνία | Ακαδημία Hermes Rollerskate";
$pageDescription = "Επικοινωνήστε με την Ακαδημία Hermes Rollerskate για ερωτήσεις σχετικά με μαθήματα, συνεργασίες ή εκδηλώσεις στο Ζωγράφου, Αθήνα.";
$pageKeywords = "επικοινωνία πατίνια, μαθήματα πατινιών Αθήνα, επικοινωνία Hermes Rollerskate";
$pageCss = "css/contact.css";
$pageScripts = ["js/contact-validation.js?v=1.0.x"];
$activePage = "contact";

require_once __DIR__ . '/partials/header.php';
?>

<section id="contact-section">
  <div class="contact-header">
    <p class="contact-intro">
      <span>
        Θα θέλαμε να ακούσουμε νέα σας!
      </span><br />
      <span>
        Μη διστάσετε να επικοινωνήσετε μαζί μας για οποιαδήποτε ερώτηση ή σχόλιο.
      </span>
    </p>
  </div>

  <form id="form" class="contact-form" method="post" action="#">
    <div class="input-group">
      <label for="name-input" class="sr-only">Όνομα</label>
      <input type="text" name="name" id="name-input" placeholder="Όνομα" />
    </div>

    <div class="input-group">
      <label for="surname-input" class="sr-only">Επώνυμο</label>
      <input type="text" name="surname" id="surname-input" placeholder="Επώνυμο" />
    </div>

    <div class="input-group">
      <label for="email-input" class="sr-only">Email</label>
      <input type="email" name="email" id="email-input" placeholder="Email" />
    </div>

    <div class="input-group">
      <label for="phone-input" class="sr-only">Τηλέφωνο</label>
      <input type="tel" name="phone" id="phone-input" placeholder="Τηλέφωνο" />
    </div>

    <div class="input-group">
      <label for="category-input" class="sr-only">Κατηγορία</label>
      <select name="category" id="category-input">
        <option value="" disabled selected hidden>
          Επιλέξτε Κατηγορία
        </option>
        <option value="general">Γενική Ερώτηση</option>
        <option value="classes">Τμήματα</option>
        <option value="merchandise">Προϊόντα</option>
        <option value="partnerships">Συνεργασίες</option>
        <option value="feedback">Σχόλια</option>
        <option value="other">Άλλο</option>
      </select>
    </div>

    <div class="input-group">
      <label for="subject-input" class="sr-only">Θέμα</label>
      <input type="text" name="subject" id="subject-input" placeholder="Θέμα" />
    </div>

    <div class="input-group">
      <label for="text-input" class="sr-only">Μήνυμα</label>
      <textarea name="message" id="text-input" placeholder="Μήνυμα"></textarea>
    </div>

    <button type="submit">Αποστολή Μηνύματος</button>
  </form>

  <div id="response-container">
    <div id="form-response"></div>
    <div id="redirect-message" class="redirect-text"></div>
    <button id="new-message-btn" class="new-message-btn">Αποστολή νέου μηνύματος</button>
  </div>
</section>
<?php
require_once __DIR__ . '/partials/footer.php';
?>