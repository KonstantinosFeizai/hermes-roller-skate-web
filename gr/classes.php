<?php
// classes.php

$pageTitle = 'Μαθήματα Roller Skating στην Αθήνα | Ομαδικά & Ιδιαίτερα Μαθήματα – Hermes Rollerskate';
$pageDescription = "Μάθετε roller skating στην Ακαδημία μας στο Ζωγράφου, Αθήνα. Προσφέρουμε ιδιαίτερα και ομαδικά μαθήματα για αρχάριους και προχωρημένους, με ευέλικτο πρόγραμμα για όλες τις ηλικίες.";
$pageKeywords = "μαθήματα roller skating, μαθήματα πατινιών, ιδιαίτερα μαθήματα πατινιών, ομαδικά μαθήματα πατινιών, μαθήματα πατινάζ, σχολή roller skating Αθήνα, Ζωγράφου";
$pageCss = 'css/classes.css';
$activePage = 'classes';

require_once __DIR__ . '/partials/header.php';
?>

<header class="classes__header">
  <div class="container">
    <h1 class="classes__title">
      Μαθήματα Roller Skating για Αρχάριους & Έμπειρους Σκέιτερς
    </h1>
    <p class="classes__intro">
      Γίνετε μέλος της Ακαδημίας Hermes Rollerskate στο Ζωγράφου, στην Αθήνα και ανακαλύψτε τη χαρά του rollerskating.
      Προσφέρουμε οργανωμένα ομαδικά μαθήματα και ευέλικτα ιδιαίτερα μαθήματα για όλες τις ηλικίες και τα επίπεδα.
    </p>
  </div>
</header>

<main>
  <section id="classes" class="classes section">
    <div class="container">
      <div class="classes__grid">

        <article class="class-card">
          <figure class="class-card__media">
            <img src="<?= asset('photo/private lesson.webp') ?>"
              alt="Ιδιαίτερο μάθημα roller skate στο Ζωγράφου, Αθήνα">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              Ιδιαίτερα Μαθήματα Roller Skating
            </h2>
            <p class="class-card__text">
              Ιδανικά για πλήρεις αρχάριους ή για όσους θέλουν να επιταχύνουν την πρόοδό τους. Τα ιδιαίτερα μαθήματα
              προσαρμόζονται στον ρυθμό και στις ανάγκες σας.
            </p>

            <dl class="class-card__details">
              <dt>Προτεινόμενα Ραντεβού</dt>
              <dd>Σάββατο: 12:30–13:30 & 20:00-20:50</dd>
              <dd>Κυριακή: 12:30–13:30 & 20:00-20:50</dd>

              <dt>Τι Περιλαμβάνεται</dt>
              <dd>
                Μάθετε πώς να πέφτετε με ασφάλεια, να σηκώνεστε, να ξεκινάτε και να σταματάτε. Μετά από 1-3 μαθήματα, θα
                είστε έτοιμοι για ένα ομαδικό μάθημα.
              </dd>
            </dl>
          </div>
        </article>

        <article class="class-card">
          <figure class="class-card__media">
            <img src="<?= asset('photo/spot3.webp') ?>"
              alt="Ομαδικό μάθημα rollerskating για παιδιά και εφήβους στην Αθήνα">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              Ομαδικά Μαθήματα Roller Skating
            </h2>
            <p class="class-card__text">
              Τα ομαδικά μας μαθήματα είναι ιδανικά τόσο για αρχάριους όσο και για πιο προχωρημένους σκέιτερς. Κάθε
              μάθημα περιλαμβάνει δραστηριότητες για την ανάπτυξη δεξιοτήτων, παιχνίδια και ομαδική εργασία.
            </p>

            <dl class="class-card__details">
              <dt>Εβδομαδιαίο Πρόγραμμα</dt>
              <dd>Σάββατο: 09:30–10:30, 10:30–11:30, 11:30–12:30, 17:00–18:00 &amp; 18:00–19:00</dd>
              <dd>Κυριακή: 09:30–10:30, 10:30–11:30, 11:30–12:30, 17:00–18:00 &amp; 18:00–19:00</dd>

              <dt>Τι θα Μάθετε</dt>
              <dd>Αρχάριοι: ισορροπία, κίνηση, ασφαλείς πτώσεις, φρενάρισμα</dd>
              <dd>Προχωρημένοι: cross-step, T-stop, άλματα, fish, snake, ασκήσεις ταχύτητας</dd>
              <dd>Όλα τα επίπεδα: δημιουργικά παιχνίδια, στοιχεία ρυθμού & χορού</dd>

              <dt>Δομή Μαθήματος</dt>
              <dd>Πρώτα ζέσταμα όλοι μαζί, μετά χωρισμός σε αρχάριους και προχωρημένους με βάση την εμπειρία.</dd>
            </dl>
          </div>
        </article>

      </div>
    </div>
  </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';
?>