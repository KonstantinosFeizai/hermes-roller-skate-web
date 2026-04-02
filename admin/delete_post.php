<?php
// admin/delete_post.php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
restrict_access(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts');
    exit();
}

try {
    // 1. Φέρνουμε τα στοιχεία του post πριν το σβήσουμε
    $stmt = $pdo->prepare('SELECT featured_image FROM blog_posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // 2. Διαγραφή της εικόνας από τον φάκελο uploads
        if (!empty($row['featured_image'])) {
            $path = PROJECT_ROOT . 'assets/uploads/blog/' . $row['featured_image'];
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        // 3. (ΠΡΟΑΙΡΕΤΙΚΟ ΑΛΛΑ ΣΩΣΤΟ) Καθαρισμός των κατηγοριών από τον ενδιάμεσο πίνακα
        $del_cats = $pdo->prepare("DELETE FROM post_categories WHERE post_id = :id");
        $del_cats->execute([':id' => $id]);

        // 4. ΠΟΛΥΓΛΩΣΣΙΚΟ FIX: Αν υπάρχουν άλλα άρθρα που είναι συνδεδεμένα με αυτό, 
        // τα ξεσυνδέουμε θέτοντας το translation_id τους ίσο με το δικό τους ID.
        $reset_translations = $pdo->prepare("UPDATE blog_posts SET translation_id = id WHERE translation_id = :id");
        $reset_translations->execute([':id' => $id]);

        // 5. Τελική διαγραφή του άρθρου
        $del = $pdo->prepare('DELETE FROM blog_posts WHERE id = :id');
        $del->execute([':id' => $id]);
    }

    header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts&deleted=1');
    exit();
} catch (PDOException $e) {
    die('Σφάλμα βάσης: ' . $e->getMessage());
}
