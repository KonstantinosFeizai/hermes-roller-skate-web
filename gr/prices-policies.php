<?php
$pageTitle = "Τιμοκατάλογος & Εκπτώσεις | Ακαδημία Hermes Rollerskate";
$pageDescription = "Ανακαλύψτε οικονομικά μαθήματα roller skating στην Ακαδημία Hermes Rollerskate στην Αθήνα. Δείτε τις τιμές μας, τις εκπτώσεις για φοιτητές και οικογένειες, και τις τιμές για ειδικά μαθήματα.";
$pageKeywords = "τιμές roller skating, κόστος μαθημάτων πατινιών, τιμοκατάλογος Hermes Rollerskate, εκπτώσεις πατινιών Αθήνα, οικογενειακές τιμές πατινάζ, φοιτητικές εκπτώσεις πατινιών, ιδιαίτερα μαθήματα πατινιών, ειδικά μαθήματα πατινιών Αθήνα";
$pageCss = ['css/prices.css'];
$activePage = 'prices-policies';

require_once __DIR__ . '/partials/header.php';
?>

<main>
  <div class="container">

    <!-- Κάρτα 1: Τιμολόγηση Μαθημάτων -->
    <div class="card">
      <h2>Τιμολόγηση Μαθημάτων</h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span>Κατηγορία</span>
          <span>Έκπτωση</span>
          <span>Τιμή/ 4 Μαθήματα</span>
          <span>Λεπτομέρειες</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Κατηγορία">Ατομικό</span>
          <span class="cell" data-label="Έκπτωση">—</span>
          <span class="cell" data-label="Τιμή/ 4 Μαθήματα">25€</span>
          <span class="cell" data-label="Λεπτομέρειες">Ένα άτομο, όλες οι ηλικίες</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Κατηγορία">Φοιτητής/Μαθητής</span>
          <span class="cell" data-label="Έκπτωση">20%</span>
          <span class="cell" data-label="Τιμή/ 4 Μαθήματα">20€</span>
          <span class="cell" data-label="Λεπτομέρειες">Μαθητές/Φοιτητές 18-24</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Κατηγορία">2ο Μέλος Οικογένειας</span>
          <span class="cell" data-label="Έκπτωση">20%</span>
          <span class="cell" data-label="Τιμή/ 4 Μαθήματα">40€</span>
          <span class="cell" data-label="Λεπτομέρειες">Συγγενής 1ου βαθμού</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Κατηγορία">3ο Μέλος Οικογένειας</span>
          <span class="cell" data-label="Έκπτωση">30%</span>
          <span class="cell" data-label="Τιμή / 4 Μαθήματα">62€</span>
          <span class="cell" data-label="Λεπτομέρειες">Συγγενής 1ου βαθμού</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Κατηγορία">Έκπτωση Σύστασης</span>
          <span class="cell" data-label="Έκπτωση">-5€</span>
          <span class="cell" data-label="Τιμή / 4 Μαθήματα">Μια φορά</span>
          <span class="cell" data-label="Λεπτομέρειες">Μόνο ενεργή σύσταση</span>
        </div>
      </div>
      <div class="note">
        Σημείωση: Και τα 4 μαθήματα πρέπει να προπληρωθούν. 3 μη εμφανίσεις αφαιρούν 1 μάθημα.
      </div>
    </div>

    <!-- Κάρτα 2: Τιμολόγηση Ιδιαίτερων Μαθημάτων -->
    <div class="card">
      <h2>Τιμολόγηση Ιδιαίτερων Μαθημάτων</h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span>Συμμετέχοντες</span>
          <span>Τιμή / Μάθημα</span>
          <span>Έκπτωση</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">1</span>
          <span class="cell" data-label="Τιμή / Μάθημα">15€</span>
          <span class="cell" data-label="Έκπτωση">Καμία</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">2</span>
          <span class="cell" data-label="Τιμή / Μάθημα">25€</span>
          <span class="cell" data-label="Έκπτωση">17%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">3</span>
          <span class="cell" data-label="Τιμή / Μάθημα">30€</span>
          <span class="cell" data-label="Έκπτωση">33%</span>
        </div>
      </div>
      <div class="note">
        Σημείωση: Προγραμματισμός μέσω τηλεφώνου. Πληρωμή μετά το μάθημα.
      </div>
    </div>

    <!-- Κάρτα 3: Τιμολόγηση Ειδικών Εργαστηρίων -->
    <div class="card">
      <h2>Τιμολόγηση Ειδικών Μαθημάτων</h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span>Συμμετέχοντες</span>
          <span>Τιμή/ Μάθημα</span>
          <span>Ποσοστό Επιστροφής</span>
          <span>Λεπτομέρειες</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">Ενεργό Μέλος</span>
          <span class="cell" data-label="Τιμή/ Μάθημα">10€</span>
          <span class="cell" data-label="Ποσοστό Επιστροφής">75%-50%</span>
          <span class="cell" data-label="Λεπτομέρειες">≥ Υπολειπόμενο Μάθημα</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">Ανενεργό Μέλος</span>
          <span class="cell" data-label="Τιμή/ Μάθημα">15€</span>
          <span class="cell" data-label="Ποσοστό Επιστροφής">75%-50%</span>
          <span class="cell" data-label="Λεπτομέρειες">Κανένα Υπολειπόμενο Μάθημα</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Συμμετέχοντες">Εξωτερικός</span>
          <span class="cell" data-label="Τιμή/ Μάθημα">20€</span>
          <span class="cell" data-label="Ποσοστό Επιστροφής">75%-50%</span>
          <span class="cell" data-label="Λεπτομέρειες">-</span>
        </div>
      </div>
      <div class="note">
        Η κράτηση θέσης είναι προπληρωμένη. Επιστροφή 75% έως 4 ημέρες πριν. Επιστροφή 50% έως 2 ημέρες πριν. Καμία
        επιστροφή εντός 48 ωρών.
      </div>
    </div>

    <!-- Κάρτα 4: Τιμολόγηση για Σχολεία -->
    <div class="card">
      <h2>Τιμολόγηση για Σχολεία</h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span>Αριθμός Παιδιών</span>
          <span>Τιμή/ Παιδί</span>
          <span>Ελάχιστη χρέωση/ μήνα</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Αριθμός Παιδιών">5</span>
          <span class="cell" data-label="Τιμή/ Τμήμα">120€</span>
          <span class="cell" data-label="Ελάχιστη χρέωση/ Μήνα">120€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Αριθμός Παιδιών">10</span>
          <span class="cell" data-label="Τιμή/ Τμήμα">220€</span>
          <span class="cell" data-label="Ελάχιστη χρέωση/ Μήνα">150€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Αριθμός Παιδιών">15</span>
          <span class="cell" data-label="Τιμή/ Τμήμα">300€</span>
          <span class="cell" data-label="Ελάχιστη χρέωση/ Μήνα">180€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Αριθμός Παιδιών">20</span>
          <span class="cell" data-label="Τιμή/Τμήμα">360€</span>
          <span class="cell" data-label="Ελάχιστη χρέωση/Μήνα">200€</span>
        </div>
      </div>
      <div class="note">
        Σημείωση: Το παρουσιολόγιο κοινοποιείται στην ομάδα μας για ακριβή κοστολόγηση.
      </div>
    </div>

    <!-- Κάρτα 5: Καιρός & Ακυρώσεις -->
    <div class="card">
      <h2>
        🌦️ Καιρός & Ακυρώσεις ❌
      </h2>
      <div class="cancellation-reasons-container">
        <div class="cancellation-pill">
          <span class="icon">☔</span>
          <span>Βροχερά ή βρεγμένα γήπεδα</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥶</span>
          <span>Κάτω από 5°C</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥵</span>
          <span>Πάνω από 35°C</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🚨</span>
          <span>Αδυναμία συμμετοχής προπονητή</span>
        </div>
      </div>
      <div class="note">
        Οι ενημερώσεις αναρτώνται στην ομαδική συνομιλία του Viber.
      </div>
    </div>
  </div>
</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>