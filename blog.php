<?php
// Handle language switch from ?lang= param before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$supportedLangs = ['en', 'el'];
$langParam = $_GET['lang'] ?? null;
if ($langParam && in_array($langParam, $supportedLangs, true)) {
    $_SESSION['lang'] = $langParam;
}

require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page meta
$pageTitle = t('blog.meta.title');
$pageDescription = t('blog.meta.description');
$pageKeywords = t('blog.meta.keywords');
$pageCss = ['css/blog.css'];
$pageScripts = ['js/blog-filter.js'];
$activePage = 'blog';

// Take current language from session or default to Greek
$currentLang = $_SESSION['lang'] ?? 'el';

// Try to select only published posts; fallback if schema differs
try {
    // 1. Τροποποιημένο Query για τα άρθρα: Προσθέσαμε το "AND bp.language = :lang"
    $stmt = $pdo->prepare("
        SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.featured_image, 
               bp.published_at, u.first_name, u.last_name 
        FROM blog_posts bp
        LEFT JOIN users u ON bp.author_id = u.id
        WHERE bp.is_published = 1 
          AND bp.language = :lang  
        ORDER BY bp.published_at DESC
    ");
    // Εκτέλεση με τη γλώσσα του session
    $stmt->execute([':lang' => $currentLang]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Query για τις κατηγορίες των posts
    $stmt_cats = $pdo->query("
    SELECT pc.post_id, c.name, c.slug 
    FROM post_categories pc
    JOIN categories c ON pc.category_id = c.id
    -- Φέρνουμε μόνο κατηγορίες που αντιστοιχούν στη γλώσσα του άρθρου
    WHERE c.language = '$currentLang' 
");
    $all_post_categories = $stmt_cats->fetchAll(PDO::FETCH_GROUP);

    // 3. Λίστα όλων των κατηγοριών για τα φίλτρα
    // Εδώ προαιρετικά μπορείς να προσθέσεις φίλτρο γλώσσας αν έχεις βάλει language και στις κατηγορίες
    $stmt_allcats = $pdo->prepare("SELECT id, name, slug FROM categories WHERE language = :lang ORDER BY name ASC");
    $stmt_allcats->execute([':lang' => $currentLang]);
    $categories = $stmt_allcats->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Σφάλμα ανάκτησης δεδομένων: " . $e->getMessage());
}

require_once PROJECT_ROOT . 'partials/header.php';
?>


<main class="site-main container">
    <section class="blog-hero">
        <h1><?= htmlspecialchars(t('blog.hero.title')) ?></h1>
        <p class="lead"><?= htmlspecialchars(t('blog.hero.lead')) ?></p>
    </section>

    <!-- Filters & Search (under hero) -->
    <div class="blog-controls">
        <div class="category-filters">
            <button class="cat-filter active" data-slug="all"><?= htmlspecialchars(t('blog.filters.all')) ?></button>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <button class="cat-filter" data-slug="<?php echo htmlspecialchars($cat['slug']); ?>"><?php echo htmlspecialchars($cat['name']); ?></button>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="blog-search">
            <input id="blogSearch" type="search" placeholder="<?= htmlspecialchars(t('blog.filters.search_placeholder')) ?>">
        </div>
    </div>

    <section class="blog-list">
        <?php if (empty($posts)): ?>
            <p><?= htmlspecialchars(t('blog.empty')) ?></p>
        <?php else: ?>
            <?php
            // helper: first N words
            function first_words($text, $n = 10)
            {
                $text = trim(strip_tags($text));
                if ($text === '') return '';
                $words = preg_split('/\s+/u', $text);
                if (count($words) <= $n) return implode(' ', $words);
                return implode(' ', array_slice($words, 0, $n)) . '...';
            }

            foreach ($posts as $post):
                $cats = $all_post_categories[$post['id']] ?? [];
                $cat_slugs = array_map(function ($c) {
                    return $c['slug'];
                }, $cats);
                $data_cats = htmlspecialchars(implode('|', $cat_slugs));
                $data_title = htmlspecialchars(mb_strtolower($post['title'] ?? ''));
            ?>
                <article class="card" data-cats="<?php echo $data_cats; ?>" data-title="<?php echo $data_title; ?>" data-href="<?php echo asset('post') . '?slug=' . urlencode($post['slug']); ?>">
                    <div class="card-media">
                        <?php if (!empty($post['featured_image'])): ?>
                            <img src="<?= asset('assets/uploads/blog/') ?><?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        <?php else: ?>
                            <img src="<?= asset('photo/logo.webp') ?>" alt="no image">
                        <?php endif; ?>
                        <?php if (!empty($cats)): ?>
                            <div class="card-badges">
                                <?php
                                $totalCats = count($cats);
                                $show = array_slice($cats, 0, 2);
                                foreach ($show as $c): ?>
                                    <span class="cat-badge"><?php echo htmlspecialchars($c['name']); ?></span>
                                <?php endforeach; ?>
                                <?php if ($totalCats > 2): ?>
                                    <span class="cat-badge">+<?php echo $totalCats - 2; ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                        <div class="card-meta"><?php echo date('d/m/Y', strtotime($post['published_at'])); ?></div>
                        <p class="card-excerpt"><?php echo htmlspecialchars(first_words($post['excerpt'] ?? $post['title'], 10)); ?></p>
                        <div class="card-actions">
                            <a class="read-more" href="<?= asset('post') ?>?slug=<?php echo urlencode($post['slug']); ?>">
                                <?= htmlspecialchars(t('blog.read_more')) ?> <i class="fa-solid fa-arrow-right fa-xs" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <div id="blogPagination" class="pagination-controls"></div>
</main>
<script>
    (function() {
        // Make whole card clickable, but ignore clicks on inner links/buttons
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.card').forEach(function(card) {
                card.addEventListener('click', function(e) {
                    if (e.target.closest('a') || e.target.closest('button')) return;
                    var dest = card.getAttribute('data-href');
                    if (dest) window.location.href = dest;
                });
            });
        });
    })();
</script>

<?php require_once PROJECT_ROOT . 'partials/footer.php';
