<?php
// whoweare.php - Greek Version

$pageTitle = 'Σχετικά με την Ακαδημία Roller Skating Hermes | Το Όραμα & Οι Αξίες μας';
$pageDescription = 'Ανακαλύψτε την αποστολή, τις αξίες και την αφοσιωμένη ομάδα πίσω από την Ακαδημία Hermes Rollerskating. Μάθετε πώς προωθούμε το rollerskating ως μια διασκεδαστική, ασφαλή και εκπαιδευτική δραστηριότητα για όλες τις ηλικίες στην Αθήνα.';
$pageKeywords = 'Ακαδημία Hermes Rollerskating, rollerskating Αθήνα, μαθήματα πατινιών, ομάδα πατινιών, εκπαιδευτές πατινιών, αποστολή πατινιών, αξίες πατινιών, οικογενειακό πατινάζ, κοινότητα πατινάζ';
$pageCss = 'css/whoweare.css';
$activePage = 'whoweare';

require_once __DIR__ . '/partials/header.php';
?>

<main>
  <section id="who-we-are" class="who-we-are section">
    <div class="container">
      <h2 class="section-title">Ποιοι Είμαστε</h2>
      <p class="section-intro">
        Καλώς ήρθατε στην <strong>Ακαδημία Roller Skating Hermes</strong>. Είμαστε μια κοινότητα που προάγει
        την αγάπη για το <strong>rollerskating</strong> και τα συναφή αθλήματα (snowski & iceskating)
        για όλες τις ηλικίες. Σταδιακά προσκαλούμε εξωτερικούς προπονητές σε διάφορα αθλήματα
        (dancing, freestyle, racing) και οργανώνουμε αθλητικές εκδρομές με επίκεντρο αυτά τα σπορ.
      </p>

      <div class="about-content">
        <div class="about-text">
          <div class="about-mission">
            <h3>Η Αποστολή μας</h3>
            <p>
              Αποστολή μας είναι να προωθήσουμε μια κουλτούρα ψυχαγωγίας, ελεύθερης έκφρασης και
              ασφάλειας στην κυκλοφορία μέσα από το rollerskating. Πιστεύουμε ότι το rollerskating
              προσφέρει μια ποιοτική επιλογή για οικογενειακές δραστηριότητες και είναι ένα μέσο
              για την ενθάρρυνση της δημιουργίας ασφαλών υποδομών μάθησης και μετακίνησης.
              Μαθαίνουμε να πέφτουμε για να ξέρουμε πώς να σηκωνόμαστε – όχι μόνο πάνω στα πατίνια,
              αλλά και στη ζωή.
            </p>
          </div>

          <h3>Τι Μας Κάνει Διαφορετικούς;</h3>
          <ul class="differentiators">
            <li>
              <i class="fas fa-trophy"></i> <strong>Παιδαγωγική Προσέγγιση</strong>: Οι προπονητές μας
              θέτουν σαφείς πειθαρχικούς κανόνες και όρια, ενώ ταυτόχρονα δίνουν στα παιδιά
              την ευκαιρία να εκφραστούν και να προωθήσουν την δημιουργική σκέψη.
            </li>
            <li>
              <i class="fas fa-skating fa-lg" style="color: #FFD43B;"></i>
              <strong>Ποικιλία Μαθημάτων</strong>: Εκτός από τη βασική και αγωνιστική προπόνηση,
              συνεργαζόμαστε με εξωτερικούς εκπαιδευτές για εξειδικευμένες προπονήσεις
              freestyle, aggressive, και dancing.
            </li>
            <li>
              <i class="fas fa-graduation-cap"></i> <strong>Προοδευτική Εκπαίδευση</strong>:
              Τα rollerskate μπορούν να αντικαταστήσουν τόσο το snow-skiing όσο και το ice-skating,
              όταν οι συνθήκες δεν είναι ιδανικές, παρέχοντας μια ευέλικτη εμπειρία.
            </li>
            <li>
              <i class="fas fa-globe-americas"></i> <strong>Κοινότητα & Εκδηλώσεις</strong>:
              Δεσμευόμαστε να ενισχύσουμε την κοινότητα μας προσφέροντας τις καλύτερες
              τιμές υπηρεσιών, οργανώνοντας αξέχαστες εκδηλώσεις, και δημιουργώντας
              μοναδικά οικογενειακά προγράμματα αθλητικού τουρισμού.
            </li>
          </ul>
        </div>

        <div class="about-image">
          <img src="<?= asset('photo/group2.webp') ?>" alt="Φωτογραφία Ακαδημίας Hermes">
        </div>
      </div>
    </div>
  </section>

  <section id="meet-the-team" class="meet-the-team section">
    <div class="container">
      <h2 class="section-title">Γνωρίστε την Ομάδα μας</h2>
      <p class="section-intro">
        Η αφοσιωμένη ομάδα μας αποτελείται από παθιασμένους επαγγελματίες που κάνουν το πατινάζ ασφαλές,
        διασκεδαστικό και εκπαιδευτικό. Η ομάδα μας αποτελείται από προπονητές και παιδαγωγούς με
        διδακτική εμπειρία σε rollerskate, εξασφαλίζοντας ότι η μάθηση παραμένει ασφαλής, διασκεδαστική και
        εκπαιδευτική.
      </p>

      <div class="staff-list">
        <div class="staff-member">
          <img src="<?= asset('photo/andreas.webp') ?>" alt="Εκπαιδευτής Ανδρέας Κουντούρας">
          <h4><i class="fa-solid fa-graduation-cap"></i> Εκπαιδευτής: Ανδρέας Κουντούρας</h4>
          <p>Πτυχιούχος ΤΕΦΑΑ, εκπαιδευτής snowski L1/L2, και πιστοποιημένος στις πρώτες βοήθειες.</p>
        </div>

        <div class="staff-member">
          <img src="<?= asset('photo/georgia.webp') ?>" alt="Εκπαιδευτής Γεωργία Παπάζογλου">
          <h4><i class="fa-solid fa-graduation-cap"></i> Εκπαιδευτής: Γεωργία Παπάζογλου</h4>
          <p>Πτυχιούχος Αγγλικής Φιλολογίας, με εμπειρία στη διδασκαλία παιδιών, ενεργά
            εμπλεκόμενη με το rollerskating από το 2020.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>