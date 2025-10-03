<?php
// benefits.php

$pageTitle = "Οφέλη & Lifestyle με Πατίνια | Ακαδημία Hermes Rollerskate ";
$pageDescription = "Ανακαλύψτε τα πολυάριθμα οφέλη του roller skating ως επιλογή lifestyle. Από την υγεία και τη φυσική κατάσταση μέχρι την κοινότητα και τα ταξίδια, μάθετε πώς η Ακαδημία Hermes Rollerskate προωθεί το πατινάζ ως μια διασκεδαστική, περιεκτική και εμπλουτιστική δραστηριότητα για όλες τις ηλικίες.";
$pageKeywords = "οφέλη πατινιών, lifestyle πατινάζ, οφέλη πατινιών για την υγεία, κοινότητα πατινιών, ταξίδια με πατίνια, Ακαδημία Hermes Rollerskate, πατινάζ για όλες τις ηλικίες, περιεκτικό πατινάζ, φυσική κατάσταση με πατίνια, αστική κουλτούρα πατινάζ";
$pageCss = "css/benefits.css";
$activePage = "benefits";

require_once __DIR__ . '/partials/header.php';
?>

<main>
  <section class="lifestyle-section">
    <div class="container">
      <h3>
        🌍 Γνωρίστε την Ευρώπη με Πατίνια: Πολιτισμός & Περιπέτεια
      </h3>

      <p>
        Ανακαλύψτε εμβληματικές πόλεις όπως η Νίκαια και η Βαρκελώνη από μια νέα οπτική—με πατίνια. Τα πατίνια
        συνδυάζουν τουρισμό, γυμναστική και πολιτισμό, δίνοντάς σας την ευκαιρία να δείτε τα αξιοθέατα... ρολάροντας!
      </p>

      <p>
        Συμμετάσχετε σε οργανωμένες βόλτες με τοπικές κοινότητες πατινιών, γνωρίστε διεθνείς σκέιτερς από όλη την Ευρώπη
        και ζήστε αξέχαστες εμπειρίες σε διάσημες παραλιακές διαδρομές.
      </p>

      <p>
        Ο αθλοτουρισμός με πατίνια αναπτύσσεται—ζήστε τον με την ομάδα μας και κάντε κάθε ταξίδι μια περιπέτεια.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/nice france.webp') ?>" alt="Βόλτα με πατίνια στη Νίκαια, Γαλλία">
          <figcaption>
            Promenade des Anglais, Νίκαια, Γαλλία
          </figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/barchelona.webp') ?>" alt="Πατινάζ στην παραλία της Βαρκελώνης">
          <figcaption>
            La Barceloneta, Βαρκελώνη, Ισπανία
          </figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section id="family-activity" class="lifestyle-section">
    <div class="container">
      <h3>
        👨‍👩‍👧‍👦 Πατίνια για όλη την Οικογένεια
      </h3>

      <p>
        Περάστε ποιοτικό χρόνο με την οικογένειά σας, παραμένοντας δραστήριοι! Τα οικογενειακά μαθήματα πατινιών
        απευθύνονται σε όλους—από το πρώτο βήμα ενός παιδιού, μέχρι μια νοσταλγική βόλτα για τους γονείς.
      </p>

      <p>
        Μαθαίνουμε ισορροπία, εμπιστοσύνη και απολαμβάνουμε κοινές στιγμές. Ιδανικό για γενέθλια, εκδρομές και ενεργές
        διακοπές.
      </p>

      <p>
        Δεν έχετε εξοπλισμό; Κανένα πρόβλημα—σας τα παρέχουμε εμείς. Φέρτε μόνο το χαμόγελό σας.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activity.webp') ?>" alt="Διασκεδαστικό πατινάζ με την οικογένεια">
          <figcaption>Οικογενειακή Διασκέδαση</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section id="learn-more" class="lifestyle-section">
    <div class="container">
      <h3>
        Μια Δεξιότητα, Άπειρες Δυνατότητες
      </h3>

      <p>
        Ξεκινήστε με πατίνια και ξεκλειδώστε νέους κόσμους—το πατινάζ στον πάγο και το σκι γίνονται ευκολότερα όταν
        έχετε ήδη μάθει ισορροπία, έλεγχο και κίνηση πάνω σε ρόδες.
      </p>

      <p>
        Η προσέγγισή μας συνδέει δεξιότητες από διαφορετικά αθλήματα. Θα εξελιχθείτε πιο γρήγορα και με περισσότερη
        αυτοπεποίθηση, είτε βρίσκεστε στην άσφαλτο, στον πάγο ή στο χιόνι.
      </p>

      <p>
        Θέλετε κάτι παραπάνω από τα βασικά; Δοκιμάστε χορό, ελεύθερου τύπου, αγωνιστικό ή επιθετικό στιλ και αποκτήστε
        εντυπωσιακές δεξιότητες υψηλού επιπέδου.
      </p>

      <p>
        Με κάθε μάθημα, δεν απλώς κάνετε πατίνια—χτίζετε βάσεις για μια ζωή γεμάτη κίνηση και αυτοπεποίθηση.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activityy.webp') ?>" alt="Πατίνια">
          <figcaption>Βάσεις με Πατίνια</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/snowski.webp') ?>" alt="Σκι στο χιόνι">
          <figcaption>Μετάβαση στο Σκι</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/iceskate2.webp') ?>" alt="Πατινάζ στον πάγο">
          <figcaption>Μετάβαση στον Πάγο</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section id="competition" class="lifestyle-section">
    <div class="container">
      <h3>
        🏆 Φιλικοί Αγώνες & Τοπικές Διοργανώσεις Rollerskate
      </h3>

      <p>
        Οι αγώνες μας δεν είναι απλώς διαγωνιστικοί—είναι γιορτές προσωπικής εξέλιξης και ομαδικού πνεύματος. Είστε 4 ή
        104 χρονών; Σας περιμένει θέση στη γραμμή εκκίνησης.
      </p>

      <p>
        Συμμετάσχετε σε χρονομετρημένες δοκιμές, mini-marathons και διαγωνισμούς δεξιοτήτων για όλα τα επίπεδα—από
        αρχάριους μέχρι έμπειρους skaters.
      </p>

      <p>
        Αγωνιστείτε μαζί με φίλους, συμμαθητές ή την οικογένειά σας και κερδίστε μετάλλια, βεβαιώσεις και αυτοπεποίθηση.
      </p>

      <p>
        Οι διοργανώσεις rollerskate σας βοηθούν να θέσετε στόχους, να προπονείστε με σκοπό και να διασκεδάζετε ενώ
        βελτιώνετε τη φυσική σας κατάσταση και τη συγκέντρωση.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/racee.webp') ?>" alt="Τοπικός Αγώνας Πατινιών">
          <figcaption>Ημέρα Αγώνα Κοινότητας</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/aponomes.webp') ?>" alt="Απονομή βραβείων">
          <figcaption>Απονομές</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section id="pathways" class="lifestyle-section">
    <div class="container">
      <h3>
        🎓 Προπόνηση & Επαγγελματική Εξέλιξη Μέσα από το Rollerskate
      </h3>

      <p>
        Ονειρεύεστε να προπονείτε άλλους, να συμμετέχετε σε shows ή να πιστοποιηθείτε; Τα οργανωμένα μας προγράμματα σας
        καθοδηγούν βήμα-βήμα στην τεχνική και την επαγγελματική εξέλιξη.
      </p>

      <p>
        Προπονηθείτε με έμπειρους δασκάλους στο rollerskate, το πατινάζ και το σκι. Μάθετε να διδάσκετε, να ηγείστε και
        να εμπνέετε παιδιά και ενήλικες.
      </p>

      <p>
        Αποκτήστε πρόσβαση σε πιστοποιήσεις προπονητικής, workshops, δεξιότητες οργάνωσης εκδηλώσεων και καθοδήγηση από
        έμπειρους επαγγελματίες.
      </p>

      <a href="https://docs.google.com/forms/d/e/1FAIpQLSfDa7gGuJDpYOI3_pESB5l4OiF7iAnOBsAQYrINmD19tabiUQ/viewform"
        target="_blank" rel="noopener" class="button-link">
        📩 Αίτηση προπονητή
      </a>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/instractor.webp') ?>" alt="Προπονητής/Δάσκαλος Πατινιών">
          <figcaption>Προπονητής/Δάσκαλος Πατινιών</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/modela.webp') ?>" alt="Μοντέλα με Πατίνια">
          <figcaption>Μοντέλο με Πατίνια</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <section id="community" class="lifestyle-section">
    <div class="container">
      <h3>
        🤝 Βρες την Κοινότητα Πατινιών στην Περιοχή σου
      </h3>

      <p>
        Δεν είστε ποτέ μόνοι σας πάνω σε ρόδες. Σε όλη την Αθήνα και την Ελλάδα, μικρές κοινότητες πατινιών υποδέχονται
        αρχάριους, οικογένειες και προχωρημένους με ανοιχτή αγκαλιά.
      </p>

      <p>
        Συμμετείχετε σε εβδομαδιαίες βόλτες, βραδινές βόλτες, κοινωνικές εκδηλώσεις ή απλές προπονήσεις στο πάρκο. Θα
        εξελιχθείτε, θα γνωρίσετε κόσμο και θα διασκεδάσετε.
      </p>

      <p>
        Συνεργαζόμαστε με τις πιο δραστήριες ομάδες της πόλης για να βρείτε την πατινο-οικογένειά σας, κοντά στο σπίτι
        σας.
      </p>

      <div class="microcommunities">
        <div class="community-item">
          <a href="https://cityskaters.gr/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/city skaters.webp') ?>" alt="City Skaters Athens">
            <h3>City Skaters Athens</h3>
          </a>
          <p>
            Εβδομαδιαίες βόλτες και προπονητικές συναντήσεις ελεύθερου τύπου & επιθετικού
          </p>
        </div>

        <div class="community-item">
          <a href="https://www.patiniasocks.com/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/patinia community.webp') ?>" alt="Patinia Community">
            <h3>Patinia Community</h3>
          </a>
          <p>
            Τα Patinia είναι η μεγαλύτερη κοινότητα σκέιτερς πατινιών στην Ελλάδα και την Κύπρο. Kυλούν στους δρόμους,
            ρολλάρουν στα πάρκα και χορεύουν
          </p>
        </div>

        <div class="community-item">
          <a href="https://linktr.ee/zoepatini?fbclid=PAZXh0bgNhZW0CMTEAAac56C1Fqan6f3URctJhYhLQ5Wk2q_jXSYiZwVbMMBvVbwZGCEehrdpPoOGTug_aem_Z3TUDktI_a0xBFl9eTetSg"
            target="_blank" rel="noopener">
            <img src="<?= asset('photo/zoepatini.webp') ?>" alt="Zoe Patini">
            <h3>Ζωή Πατίνι</h3>
          </a>
          <p>
            Η ομάδα Ζωή Πατίνι, εμπνευσμένη από το μότο «μου έχεις κάνει τη ζωή πατίνι», φέρνει στην Θεσσαλονίκη και την
            Αθήνα το πιο ξεσηκωτικό μάθημα rollerdancing!
          </p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>