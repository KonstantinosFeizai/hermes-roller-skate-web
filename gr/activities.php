<?php
// activities.php

$pageTitle = 'Δραστηριότητες για Πατίνια, Σκι & Παγοδρόμιο | Ακαδημία Hermes Rollerskate';
$pageDescription = 'Ανακαλύψτε τις δραστηριότητές μας στην Ακαδημία Hermes Rollerskate, όπως μαθήματα roller skating, μαθήματα παγοδρομίας και προγράμματα σκι για αρχάριους. Ιδανικά για όλες τις ηλικίες στην Αθήνα.';
$pageKeywords = 'μαθήματα roller skating, μαθήματα παγοδρομίας, σκι για αρχάριους, δραστηριότητες πατινάζ Αθήνα, Ακαδημία Hermes Rollerskate, πατινάζ για όλες τις ηλικίες, προγράμματα πατινάζ, μαθήματα πατινάζ Αθήνα';
$pageCss = 'css/activities.css';
$activePage = 'activities';

require_once __DIR__ . '/partials/header.php';
?>

<!-- INTRO -->
<header class="intro-section">
  <div class="container">
    <h1>Από το Roller Skating στον Πάγο και το Σκι</h1>
    <p>
      Το roller skating είναι κάτι παραπάνω από διασκέδαση—είναι ένας έξυπνος και ευχάριστος τρόπος να χτίσεις τις
      βάσεις για τα χειμερινά σπορ. Η ισορροπία, ο έλεγχος των άκρων και οι τεχνικές φρεναρίσματος που μαθαίνεις στους
      τροχούς μεταφέρονται απευθείας στον πάγο και το χιόνι.
    </p>
    <p>
      Τα <strong>μαθήματα roller skating</strong> μας είναι ιδανικά για να προετοιμάσουν τους αρχάριους για τα πρώτα
      τους βήματα στα παγοπέδιλα ή στα σκι του χιονιού. Είτε είστε παιδί είτε ενήλικας, θα αποκτήσετε αυτοπεποίθηση πριν
      έρθει ο χειμώνας.
    </p>
  </div>
</header>

<main>
  <section id="ice-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="<?= asset('photo/iceskate1.webp') ?>" alt="Μαθήματα παγοδρομίας για αρχάριους στην Αθήνα">
      </figure>
      <div class="activity-content">
        <h2>⛸️ Μαθήματα Παγοδρομίας για Αρχάριους</h2>
        <p>
          Τα ομαδικά μας μαθήματα παγοδρομίας βοηθούν τους αρχάριους να χτίσουν ισορροπία, να μάθουν τον έλεγχο των
          άκρων και να σταματούν με ασφάλεια—ιδανικά για όσους έχουν ή δεν έχουν εμπειρία με τα roller skates.
        </p>
        <div class="learn-goals">
          <h3>Τι θα Μάθετε</h3>
          <ul>
            <li>Βασική ισορροπία και στάση στον πάγο</li>
            <li>Τεχνικές ολίσθησης, στροφών και ασφαλούς στάσης</li>
            <li>Έλεγχος των άκρων και επίγνωση της κίνησης</li>
            <li>Διασκεδαστικές ασκήσεις για να ενισχύσετε την αυτοπεποίθηση</li>
          </ul>
        </div>
        <div class="target-group">
          <h3>Σε Ποιους Απευθύνεται;</h3>
          <p>
            Αρχάριοι με ή χωρίς εμπειρία στα roller skates
          </p>
        </div>
        <div class="meta-info">
          <div>⏱️ Διάρκεια: 1 ώρα</div>
          <div>👥 Μέγεθος Ομάδας: 5–15 μαθητές</div>
          <div>📍 Τοποθεσία: Τοπικό παγοδρόμιο (θα ανακοινωθεί)</div>
        </div>
      </div>
    </div>
  </section>

  <section id="ski-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="<?= asset('photo/ski.webp') ?>" alt="Μαθήματα σκι για αρχάριους στην Ελλάδα">
      </figure>
      <div class="activity-content">
        <h2>🎿 Μαθήματα Σκι για Αρχάριους</h2>
        <p>
          Αυτό το πρόγραμμα Σαββατοκύριακου είναι ιδανικό για όσους δοκιμάζουν σκι για πρώτη φορά. Σε μικρές ομάδες, θα
          μάθετε τα βασικά του σκι, από την κίνηση μέχρι την ασφάλεια στην πλαγιά—δεν απαιτείται προηγούμενη εμπειρία!
        </p>
        <div class="learn-goals">
          <h3>Τι θα Μάθετε</h3>
          <ul>
            <li>Σωστή στάση και ισορροπία στα σκι</li>
            <li>Βασικές στροφές και ασφαλές σταμάτημα</li>
            <li>Έλεγχος σε αρχάριες πλαγιές</li>
            <li>Ασφάλεια και κανόνες συμπεριφοράς στο σκι</li>
          </ul>
        </div>
        <div class="target-group">
          <h3>Σε Ποιους Απευθύνεται;</h3>
          <p>
            Ενήλικες και παιδιά έτοιμα για την πρώτη τους εμπειρία στο σκι
          </p>
        </div>
        <div class="meta-info">
          <div>🕒 Διάρκεια: 2 ημέρες (5 ώρες/ημέρα)</div>
          <div>👥 Μέγεθος Ομάδας: 4–8 μαθητές</div>
          <div>📍 Τοποθεσία: Χιονοδρομικό κέντρο (θα ανακοινωθεί)</div>
        </div>
      </div>
    </div>
  </section>
</main>

<section class="cta-section container">
  <div class="cta-content">
    <p>🎯 Έτοιμοι να ξεκινήσετε τη χειμερινή σας περιπέτεια; Ξεκινήστε με roller skating και ελάτε στα μαθήματα
      παγοδρομίας και σκι!</p>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLScIWPgULw7AtR9Gsvh3Mm8ma5AXzohL4UAUQsKdyZHTTnmqHg/viewform?usp=sf_link"
      target="_blank" rel="noopener" class="button-link" role="button">
      📩 Δήλωση Συμμετοχής
    </a>
  </div>
</section>

<?php
require_once __DIR__ . '/partials/footer.php';
?>