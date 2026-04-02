<?php
// Temporary: show all errors to aid debugging (remove in production)
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// 1. Φόρτωση βιβλιοθηκών και ρυθμίσεων
require_once __DIR__ . '/../config.php';       // Για το PROJECT_ROOT, $BASE_URL και $pdo
require_once __DIR__ . '/../vendor/autoload.php'; // Composer autoload (Intervention/Image κ.ά.)

use Intervention\Image\ImageManager;

// Έλεγχος αν η φόρμα στάλθηκε με POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? 'create';

    // 2. Παραλαβή και basic καθαρισμός δεδομένων
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $excerpt  = trim($_POST['excerpt'] ?? $_POST['summary'] ?? '');
    $content  = $_POST['content'] ?? '';
    $author_id = isset($_POST['author_id']) && $_POST['author_id'] !== '' ? (int)$_POST['author_id'] : null;
    $selected_categories = isset($_POST['categories']) && is_array($_POST['categories']) ? $_POST['categories'] : [];
    $language = $_POST['language'] ?? 'el';
    $linked_id = !empty($_POST['translation_id']) ? (int)$_POST['translation_id'] : null;
    // generate slug from title when not provided
    if ($slug === '') {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    }

    $filename = '';

    // 4. Επεξεργασία Εικόνας με Intervention Image (αν υπάρχει)
    // accept either 'featured_image' (new form) or 'post_image' (older)
    $imageField = null;
    if (isset($_FILES['featured_image']) && !empty($_FILES['featured_image']['tmp_name'])) {
        $imageField = 'featured_image';
    } elseif (isset($_FILES['post_image']) && !empty($_FILES['post_image']['tmp_name'])) {
        $imageField = 'post_image';
    }

    if ($imageField !== null && isset($_FILES[$imageField]) && $_FILES[$imageField]['error'] === UPLOAD_ERR_OK) {

        $uploadPath = PROJECT_ROOT . 'assets/uploads/blog/';

        // Βεβαιωνόμαστε ότι ο φάκελος υπάρχει
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
            die('Αδύνατο να δημιουργηθεί ο φάκελος αποθήκευσης εικόνων.');
        }

        // Εύρεση και έλεγχος επέκτασης
        $uploadedName = $_FILES[$imageField]['name'] ?? '';
        $extension = strtolower(pathinfo($uploadedName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($extension, $allowed, true)) {
            die('Μη επιτρεπτός τύπος αρχείου εικόνας.');
        }

        // Ασφαλές όνομα αρχείου
        $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '', $slug) . '.' . $extension;

        // Επιλογή driver: προτίμηση GD, αλλιώς Imagick
        if (extension_loaded('gd')) {
            $manager = ImageManager::gd();
        } elseif (extension_loaded('imagick')) {
            $manager = ImageManager::imagick();
        } else {
            die('Δεν βρέθηκε υποστηριζόμενος image driver (gd ή imagick). Ενεργοποίησε το GD ή το Imagick.');
        }

        try {
            $image = $manager->read($_FILES[$imageField]['tmp_name']);
            // use cover() (crop+resize) with the currently installed library API
            $image->cover(800, 500)->save($uploadPath . $filename, 80);
        } catch (\Exception $e) {
            die('Σφάλμα κατά την επεξεργασία της εικόνας: ' . $e->getMessage());
        }
    }

    // 5. Εισαγωγή στη Βάση Δεδομένων με PDO
    try {
        // detect if table has publish columns
        $hasIsPublished = false;
        try {
            $col = $pdo->query("SHOW COLUMNS FROM blog_posts LIKE 'is_published'")->fetch();
            if ($col) {
                $hasIsPublished = true;
            }
        } catch (Exception $e) {
            $hasIsPublished = false;
        }

        // publish inputs
        $published_at_input = trim($_POST['published_at'] ?? '');
        $is_published = isset($_POST['is_published']) && $_POST['is_published'] ? 1 : 0;
        $published_at = null;
        if ($is_published) {
            if ($published_at_input !== '') {
                $ts = strtotime($published_at_input);
                if ($ts !== false) {
                    $published_at = date('Y-m-d H:i:s', $ts);
                } else {
                    $published_at = date('Y-m-d H:i:s');
                }
            } else {
                $published_at = date('Y-m-d H:i:s');
            }
        }

        if ($action === 'update' && !empty($_POST['id'])) {
            $id = (int)$_POST['id'];

            // 1. Πάρκαρε τη γλώσσα και το translation_id από το POST
            $language = $_POST['language'] ?? 'el';
            // Αν το translation_id είναι κενό, το συνδέουμε με το ίδιο το άρθρο ($id)
            $linked_id = !empty($_POST['translation_id']) ? (int)$_POST['translation_id'] : $id;

            // Load current featured image for deletion
            $stmt_old = $pdo->prepare('SELECT featured_image FROM blog_posts WHERE id = :id');
            $stmt_old->execute([':id' => $id]);
            $oldRow = $stmt_old->fetch(PDO::FETCH_ASSOC);
            $currentFeatured = $oldRow['featured_image'] ?? null;

            // Χτίζουμε το SQL προσεκτικά
            $sql = "UPDATE blog_posts SET 
            title = :title, 
            slug = :slug, 
            content = :content, 
            excerpt = :excerpt, 
            author_id = :author_id, 
            language = :language,          
            translation_id = :translation_id";

            // Προσθήκη εικόνας αν χρειάζεται
            $removeImage = isset($_POST['remove_image']) && $_POST['remove_image'] ? true : false;
            $includeFeaturedInUpdate = ($filename !== '') || $removeImage;
            if ($includeFeaturedInUpdate) {
                $sql .= ', featured_image = :featured_image';
            }

            // Προσθήκη status/date ΜΟΝΟ αν δεν τα έχουμε ήδη βάλει (αφαίρεσα τη διπλή εγγραφή)
            if ($hasIsPublished) {
                $sql .= ', is_published = :is_published, published_at = :published_at';
            }

            $sql .= ' WHERE id = :id';

            $stmt = $pdo->prepare($sql);

            // Βασικά Params
            $params = [
                ':title'          => $title,
                ':slug'           => $slug,
                ':excerpt'        => $excerpt,
                ':content'        => $content,
                ':author_id'      => $author_id,
                ':language'       => $language,
                ':translation_id' => $linked_id,
                ':id'             => $id,
            ];

            // Image Params & File Management
            if ($includeFeaturedInUpdate) {
                if ($filename !== '') {
                    $params[':featured_image'] = $filename;
                    if (!empty($currentFeatured)) {
                        $oldPath = PROJECT_ROOT . 'assets/uploads/blog/' . $currentFeatured;
                        if (is_file($oldPath)) @unlink($oldPath);
                    }
                } else {
                    $params[':featured_image'] = null;
                    if ($removeImage && !empty($currentFeatured)) {
                        $oldPath = PROJECT_ROOT . 'assets/uploads/blog/' . $currentFeatured;
                        if (is_file($oldPath)) @unlink($oldPath);
                    }
                }
            }

            // Status Params
            if ($hasIsPublished) {
                $params[':is_published'] = $is_published;
                $params[':published_at'] = $published_at;
            }

            $stmt->execute($params);

            // 2. ΔΙΑΧΕΙΡΙΣΗ ΚΑΤΗΓΟΡΙΩΝ (Όπως το είχες, είναι σωστό)
            $stmt_del = $pdo->prepare("DELETE FROM post_categories WHERE post_id = :post_id");
            $stmt_del->execute([':post_id' => $id]);

            if (!empty($selected_categories)) {
                $stmt_cat = $pdo->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)");
                foreach ($selected_categories as $cat_id) {
                    $stmt_cat->execute([
                        ':post_id'     => $id,
                        ':category_id' => (int)$cat_id
                    ]);
                }
            }

            header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts&updated=1');
            exit();
        }

        if ($hasIsPublished) {
            $sql = 'INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, is_published, published_at, author_id, language, translation_id) 
                    VALUES (:title, :slug, :excerpt, :content, :featured_image, :is_published, :published_at, :author_id, :language, :translation_id)';
            $stmt = $pdo->prepare($sql);
            $params = [
                ':title'          => $title,
                ':slug'           => $slug,
                ':excerpt'        => $excerpt,
                ':content'        => $content,
                ':featured_image' => $filename,
                ':is_published'   => $is_published,
                ':published_at'   => $published_at,
                ':author_id'      => $author_id,
                ':language'       => $language,
                ':translation_id' => null // Θα το ενημερώσουμε παρακάτω
            ];
        } else {
            $sql = 'INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, author_id, language, translation_id) 
                    VALUES (:title, :slug, :excerpt, :content, :featured_image, :author_id, :language, :translation_id)';
            $stmt = $pdo->prepare($sql);
            $params = [
                ':title'          => $title,
                ':slug'           => $slug,
                ':excerpt'        => $excerpt,
                ':content'        => $content,
                ':featured_image' => $filename,
                ':author_id'      => $author_id,
                ':language'       => $language,
                ':translation_id' => null // Θα το ενημερώσουμε παρακάτω
            ];
        }

        $stmt->execute($params);
        $new_post_id = $pdo->lastInsertId();

        // --- 2. ΛΟΓΙΚΗ ΣΥΝΔΕΣΗΣ ΜΕΤΑΦΡΑΣΗΣ (translation_id) ---

        if ($linked_id === null) {
            // Αν δεν επιλέχθηκε σύνδεση, το άρθρο είναι το "πρωτότυπο"
            // Άρα το translation_id είναι το ίδιο το ID του άρθρου
            $pdo->prepare("UPDATE blog_posts SET translation_id = id WHERE id = :id")
                ->execute([':id' => $new_post_id]);
        } else {
            // Αν επιλέχθηκε σύνδεση (μετάφραση), παίρνουμε το translation_id του γονέα
            $stmt_parent = $pdo->prepare("SELECT translation_id FROM blog_posts WHERE id = :pid");
            $stmt_parent->execute([':pid' => $linked_id]);
            $parent_tid = $stmt_parent->fetchColumn();

            // Αν για κάποιο λόγο ο γονέας δεν έχει translation_id, χρησιμοποιούμε το $linked_id
            $final_tid = $parent_tid ?: $linked_id;

            $pdo->prepare("UPDATE blog_posts SET translation_id = :tid WHERE id = :id")
                ->execute([':tid' => $final_tid, ':id' => $new_post_id]);
        }

        // --- 3. Η ΣΥΝΔΕΣΗ ΜΕ ΤΙΣ ΚΑΤΗΓΟΡΙΕΣ ---

        if (!empty($selected_categories)) {
            $stmt_cat = $pdo->prepare("INSERT INTO post_categories (post_id, category_id) VALUES (:post_id, :category_id)");
            foreach ($selected_categories as $cat_id) {
                $stmt_cat->execute([
                    ':post_id'     => $new_post_id,
                    ':category_id' => (int)$cat_id
                ]);
            }
        }

        header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts&created=1');
        exit();
    } catch (PDOException $e) {
        echo 'Σφάλμα κατά την αποθήκευση: ' . $e->getMessage();
    }
}
