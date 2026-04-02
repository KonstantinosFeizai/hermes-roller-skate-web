<?php

// 1. Session must start before any output, to check language
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// 2. Language handling: Check if 'lang' parameter is set and valid, then store in session
$supportedLangs = ['en', 'el'];
if (isset($_GET['lang']) && in_array($_GET['lang'], $supportedLangs)) {
    $_SESSION['lang'] = $_GET['lang'];
}

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if ($slug === '') {
    header("Location: " . asset('blog'));
    exit();
}


try {
    // 3. Φέρνουμε το Post βάσει slug (ανεξαρτήτως γλώσσας αρχικά)
    $stmt = $pdo->prepare("
        SELECT bp.*, u.first_name, u.last_name 
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        WHERE bp.slug = :slug AND bp.is_published = 1
        LIMIT 1
    ");
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post) {
        $currentLang = $_SESSION['lang'] ?? 'el';

        // 4. ΛΟΓΙΚΗ REDIRECT: Αν η γλώσσα του άρθρου διαφέρει από το session
        if ($post['language'] !== $currentLang) {

            // Ψάχνουμε αν υπάρχει μετάφραση στη γλώσσα του session
            $stmt_alt = $pdo->prepare("SELECT slug FROM blog_posts WHERE translation_id = :tid AND language = :lang AND is_published = 1 LIMIT 1");
            $stmt_alt->execute([
                ':tid' => $post['translation_id'],
                ':lang' => $currentLang
            ]);
            $alt_post = $stmt_alt->fetch();

            if ($alt_post) {
                // Αν βρέθηκε μετάφραση, τον στέλνουμε εκεί
                header("Location: post.php?slug=" . $alt_post['slug']);
                exit();
            } else {
                // Αν ΔΕΝ υπάρχει μετάφραση, αναγκάζουμε το session να γυρίσει στη γλώσσα του άρθρου
                // για να μπορεί ο χρήστης να το διαβάσει στη γλώσσα που είναι διαθέσιμο.
                $_SESSION['lang'] = $post['language'];
            }
        }
    } else {
        header("HTTP/1.0 404 Not Found");
        include('404.php');
        exit();
    }

    // QUERY 2: Κατηγορίες
    $stmt_cats = $pdo->prepare("
        SELECT c.id, c.name, c.slug 
        FROM categories c
        INNER JOIN post_categories pc ON c.id = pc.category_id
        WHERE pc.post_id = :post_id
            AND c.language = :lang
");
    $stmt_cats->execute([
        ':post_id' => $post['id'],
        ':lang'    => $currentLang // Η γλώσσα από το session/URL
    ]);
    $post_categories = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα: " . $e->getMessage());
}

// --- Recommended posts: find up to 3 posts that share categories (same language) ---
$recommendedPosts = [];
$catIds = array_column($post_categories, 'id');
if (!empty($catIds)) {
    $placeholders = implode(',', array_fill(0, count($catIds), '?'));
    $sqlRec = "SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, bp.published_at, COUNT(*) AS common_count
               FROM blog_posts bp
               JOIN post_categories pc ON bp.id = pc.post_id
               WHERE pc.category_id IN ($placeholders)
                 AND bp.id != ?
                 AND bp.is_published = 1
                 AND bp.language = ?
               GROUP BY bp.id
               ORDER BY common_count DESC, bp.published_at DESC
               LIMIT 3";
    $stmtRec = $pdo->prepare($sqlRec);
    $params = array_merge($catIds, [(int)$post['id'], $currentLang]);
    $stmtRec->execute($params);
    $recommendedPosts = $stmtRec->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmtRec = $pdo->prepare("SELECT id, title, slug, excerpt, featured_image, published_at FROM blog_posts WHERE language = :lang AND id != :id AND is_published = 1 ORDER BY published_at DESC LIMIT 3");
    $stmtRec->execute([':lang' => $currentLang, ':id' => $post['id']]);
    $recommendedPosts = $stmtRec->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch first category name for each recommended post (for badge overlay)
$recCategories = [];
if (!empty($recommendedPosts)) {
    $recIds = array_column($recommendedPosts, 'id');
    $recPlaceholders = implode(',', array_fill(0, count($recIds), '?'));
    $stmtRecCats = $pdo->prepare(
        "SELECT pc.post_id, c.name FROM post_categories pc JOIN categories c ON pc.category_id = c.id WHERE pc.post_id IN ($recPlaceholders) AND c.language = ?"
    );
    $stmtRecCats->execute(array_merge($recIds, [$currentLang]));
    foreach ($stmtRecCats->fetchAll(PDO::FETCH_ASSOC) as $rc) {
        if (!isset($recCategories[$rc['post_id']])) {
            $recCategories[$rc['post_id']] = $rc['name'];
        }
    }
}

// 1. Έλεγχος αν τελικά έχουμε post (αν δεν μας πέταξε έξω το 404 πριν)
if (!$post) {
    $pageTitle = 'Άρθρο δεν βρέθηκε';
    require_once PROJECT_ROOT . 'partials/header.php';
?>
    <main class="site-main container">
        <h1>Άρθρο δεν βρέθηκε</h1>
        <p>Το άρθρο που ζητήσατε δεν υπάρχει ή έχει διαγραφεί.</p>
        <p><a href="<?= asset('blog') ?>">Επιστροφή στο blog</a></p>
    </main>
<?php
    require_once PROJECT_ROOT . 'partials/footer.php';
    exit();
}

// 2. Ρύθμιση Meta Tags για το υπάρχον post
$pageTitle = $post['title']; // Παίρνει τον τίτλο από τη βάση (στη σωστή γλώσσα)
$pageCss = ['css/blog.css', 'css/post.css'];
$pageScripts = ['js/post_scroll.js'];

// 3. Φόρτωση του Header (εδώ θα εμφανιστούν τα σωστά μενού/γλώσσες)
require_once PROJECT_ROOT . 'partials/header.php';
?>

<main class="site-main container">
    <article class="post-single">
        <header>
            <h1 class="custom-post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
            <?php if (!empty($post_categories)): ?>
                <div class="custom-post-categories">
                    <?php foreach ($post_categories as $cat): ?>
                        <span class="custom-post-category-badge"><?php echo htmlspecialchars($cat['name']); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (!empty($post['featured_image'])): ?>
            <div class="custom-post-image-wrapper">
                <img class="custom-post-image" src="<?= asset('assets/uploads/blog/') ?><?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" />
            </div>
        <?php endif; ?>

        <?php
        // υπολογισμός χρόνου ανάγνωσης (περίπου 200 λέξεις/λεπτό)
        $plain = strip_tags($post['content']);
        $words_array = preg_split('/\s+/u', trim($plain));
        $words_count = count($words_array);
        $wpm = 200;
        $minutes = (int)ceil($words_count / $wpm);
        if ($minutes < 1) $minutes = 1;

        $minutes_label = $minutes === 1 ? "~1 λεπτό" : "~{$minutes} λεπτά";
        ?>

        <div class="post-layout">
            <aside class="post-meta-column">
                <div class="post-meta-box author-box">
                    <span class="meta-label"><?php echo t('post.author'); ?></span>
                    <span class="meta-author-name">
                        <span class="meta-author-first"><?php echo htmlspecialchars($post['first_name']); ?></span>
                        <?php if (!empty($post['last_name'])): ?>
                            <span class="meta-author-last"><?php echo htmlspecialchars($post['last_name']); ?></span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="post-meta-box">
                    <span class="meta-label"><?php echo t('post.date'); ?></span>
                    <span class="meta-small"><?php echo date('d/m/Y', strtotime($post['published_at'] ?? $post['created_at'])); ?></span>
                </div>
                <div class="post-meta-box">
                    <span class="meta-label"><?php echo t('post.read_time'); ?></span>
                    <span class="meta-small"><?php echo $minutes_label; ?></span>
                </div>
                <div class="post-meta-box">
                    <a class="back-to-blog" href="blog.php">← <?php echo t('post.back_to_blog'); ?></a>
                </div>
            </aside>

            <div class="custom-post-content-wrapper">
                <div class="custom-post-content">
                    <?php
                    // content was created with TinyMCE (HTML) — render as-is
                    $content_html = $post['content'];
                    // Fix common image path issues: ensure assets path includes BASE_URL
                    if (!empty($content_html)) {
                        $base = rtrim($BASE_URL, '/');
                        // patterns to replace: src="/assets..., src="../assets..., src="assets/uploads/...
                        $content_html = str_replace(
                            [
                                'src="/assets',
                                "src='../assets",
                                'src="../assets',
                                'src="assets/uploads',
                                "src='assets/uploads",
                            ],
                            [
                                'src="' . $base . '/assets',
                                "src='" . $base . '/assets',
                                'src="' . $base . '/assets',
                                'src="' . $base . '/assets',
                                "src='" . $base . '/assets',
                            ],
                            $content_html
                        );
                    }
                    echo $content_html;
                    ?>
                </div>
                <div class="custom-post-footer">
                    <a class="back-to-blog" href="blog.php">← <?php echo t('post.back_to_blog'); ?></a>
                </div>
            </div>
        </div>

    </article>
    <?php if (!empty($recommendedPosts)): ?>
        <section class="recommended-posts">
            <h2><?= htmlspecialchars(t('post.recommended_heading') ?? 'Recommended Articles') ?></h2>
            <div class="recommended-grid">
                <?php foreach ($recommendedPosts as $r): ?>
                    <article class="rec-card">
                        <a class="rec-link" href="<?= asset('post') ?>?slug=<?php echo urlencode($r['slug']); ?>&lang=<?php echo $currentLang; ?>">
                            <div class="rec-media">
                                <?php if (!empty($r['featured_image'])): ?>
                                    <img src="<?= asset('assets/uploads/blog/') ?><?php echo htmlspecialchars($r['featured_image']); ?>" alt="<?php echo htmlspecialchars($r['title']); ?>">
                                <?php else: ?>
                                    <img src="<?= asset('photo/logo.webp') ?>" alt="no image">
                                <?php endif; ?>
                                <?php if (!empty($recCategories[$r['id']])): ?>
                                    <span class="rec-badge"><?php echo htmlspecialchars($recCategories[$r['id']]); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="rec-body">
                                <h3 class="rec-title"><?php echo htmlspecialchars($r['title']); ?></h3>
                                <p class="rec-excerpt"><?php echo htmlspecialchars(mb_strimwidth(strip_tags($r['excerpt'] ?? $r['title']), 0, 120, '...')); ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>

<?php require_once PROJECT_ROOT . 'partials/footer.php';
?>
<!-- Back to top button -->
<button id="back-to-top" class="back-to-top" aria-label="<?php echo t('post.back_to_top_aria'); ?>" title="<?php echo t('post.back_to_top_title'); ?>">
    <span class="material-symbols-rounded">arrow_upward</span>
</button>