<?php
// admin/add_post.php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

// only admins
restrict_access(['admin']);

$pageCss = ['css/admin_dashboard.css', 'css/blog_admin.css'];
// Προσθήκη JS για να δουλεύει το "αναβόσβημα" των κατηγοριών
$pageScripts = ['js/ui-manager.js'];

require_once PROJECT_ROOT . 'partials/header.php';

// --- ΦΟΡΤΩΣΗ ΔΕΔΟΜΕΝΩΝ ---
try {
    // 1. Φέρνουμε ΟΛΕΣ τις κατηγορίες ξεχωριστά ανά γλώσσα
    $stmt_el = $pdo->query("SELECT * FROM categories WHERE language = 'el' ORDER BY name ASC");
    $categories_el = $stmt_el->fetchAll(PDO::FETCH_ASSOC);

    $stmt_en = $pdo->query("SELECT * FROM categories WHERE language = 'en' ORDER BY name ASC");
    $categories_en = $stmt_en->fetchAll(PDO::FETCH_ASSOC);

    // 2. Φέρνουμε όλα τα άρθρα για τη "Σύνδεση με άλλο άρθρο" (Dropdown)
    $stmt_all_posts = $pdo->query("SELECT id, title, language FROM blog_posts ORDER BY created_at DESC");
    $all_posts = $stmt_all_posts->fetchAll(PDO::FETCH_ASSOC);

    // 3. Φέρνουμε τους διαχειριστές (Συγγραφείς)
    $stmt_authors = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'admin' ORDER BY last_name ASC");
    $authors = $stmt_authors->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Σφάλμα φόρτωσης δεδομένων: " . $e->getMessage());
}
?>

<div class="admin-wrapper">
    <main class="admin-main-content">
        <div class="tab-header">
            <a href="<?= asset('admin/admin_dashboard.php') ?>" class="action-btn btn-primary">← Επιστροφή</a>
        </div>

        <form action="<?= asset('admin/process_post.php') ?>" method="post" enctype="multipart/form-data" class="form-stack">
            <input type="hidden" name="action" value="create">
            <input type="hidden" id="is_published" name="is_published" value="0">

            <div class="form-grid">
                <div class="form-column">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <div style="display:flex;gap:8px;align-items:center;">
                            <h3 style="margin:0">Blog Post Editor</h3>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="button" id="save-draft" class="action-btn">Save Draft</button>
                            <button type="button" id="publish-btn" class="action-btn btn-success">Publish</button>
                        </div>
                    </div>

                    <div style="background:var(--admin-card);padding:18px;border-radius:12px;border:1px solid var(--admin-border);">
                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label>Excerpt</label>
                            <input type="text" name="excerpt" required class="form-input">
                        </div>

                        <div class="form-group">
                            <label>Slug</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input id="post-slug" type="text" name="slug" placeholder="enter-post-slug-here" class="form-input" style="flex:1">
                                <button type="button" id="generate-slug-btn" class="action-btn">Generate</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <textarea id="open-source-plugins" name="content" rows="12" class="form-input"></textarea>
                        </div>
                    </div>
                </div>

                <aside class="sidebar">
                    <h4 style="margin-top:0">Post Settings</h4>

                    <div class="form-group">
                        <label>Author</label>
                        <select name="author_id" class="form-input" required>
                            <option value="">Select author</option>
                            <?php if (!empty($authors)): ?>
                                <?php foreach ($authors as $author): ?>
                                    <option value="<?= (int)$author['id'] ?>">
                                        <?= htmlspecialchars($author['first_name'] . ' ' . $author['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Categories</label>
                        <select id="category-select" class="form-input">
                            <option value="">Select category</option>
                            <?php foreach ($categories_el as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" data-lang="el">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                            <?php foreach ($categories_en as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" data-lang="en">
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <div id="categories-selected-container" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;"></div>
                        <div id="hidden-inputs-container"></div>
                    </div>

                    <div class="form-group">
                        <label for="language">Γλώσσα Άρθρου</label>
                        <select name="language" id="language" class="form-input">
                            <option value="el" selected>Ελληνικά (EL)</option>
                            <option value="en">English (EN)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="translation_id">Σύνδεση με υπάρχον άρθρο (Μετάφραση)</label>
                        <select name="translation_id" id="translation_id" class="form-input">
                            <option value="">Κανένα (Νέο άρθρο)</option>
                            <?php
                            try {
                                $stmt_all = $pdo->query("SELECT id, title, language FROM blog_posts ORDER BY created_at DESC");
                                while ($row = $stmt_all->fetch(PDO::FETCH_ASSOC)) {
                                    $displayTitle = htmlspecialchars($row['title']);
                                    $lang = strtoupper(htmlspecialchars($row['language'] ?? ''));
                                    echo '<option value="' . (int)$row['id'] . '">[' . $lang . '] ' . $displayTitle . '</option>';
                                }
                            } catch (Exception $e) {
                                // ignore - leave only the default empty option
                            }
                            ?>
                        </select>
                        <small>Αν γράφετε την αγγλική έκδοση ενός ελληνικού άρθρου, επιλέξτε το ελληνικό από τη λίστα.</small>
                    </div>

                    <div class="form-group">
                        <label>Publish Date</label>
                        <input type="date" name="published_at" class="form-input">
                    </div>



                    <div class="form-group">
                        <label>Featured Image (file)</label>
                        <input type="file" name="featured_image" accept="image/*" class="form-input">
                        <div id="featured-preview-container" style="margin-top:8px;display:none;">
                            <img id="featured-preview-img" src="" alt="Preview" style="max-width:100%;max-height:160px;border-radius:8px;display:block;border:1px solid var(--admin-border);padding:4px;background:#fff;">
                        </div>
                    </div>

                    <!-- featured_image_url removed; only file upload is used -->

                    <div class="form-group">
                        <label>Status</label>
                        <div style="padding:8px;background:#fafafa;border-radius:6px;border:1px solid var(--admin-border);">Draft</div>
                    </div>
                </aside>
            </div>
        </form>
    </main>

</div>

<!-- TinyMCE (basic) -->
<script src="<?= getVersionedAssetUrl('assets/tinymce/tinymce.min.js') ?>" referrerpolicy="origin"></script>
<script>
    const useDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

    // Slug generator: create URL-friendly slug from title
    function slugify(text) {
        const map = {
            'Α': 'A',
            'Β': 'V',
            'Γ': 'G',
            'Δ': 'D',
            'Ε': 'E',
            'Ζ': 'Z',
            'Η': 'I',
            'Θ': 'TH',
            'Ι': 'I',
            'Κ': 'K',
            'Λ': 'L',
            'Μ': 'M',
            'Ν': 'N',
            'Ξ': 'X',
            'Ο': 'O',
            'Π': 'P',
            'Ρ': 'R',
            'Σ': 'S',
            'Τ': 'T',
            'Υ': 'Y',
            'Φ': 'F',
            'Χ': 'CH',
            'Ψ': 'PS',
            'Ω': 'O',
            'α': 'a',
            'β': 'v',
            'γ': 'g',
            'δ': 'd',
            'ε': 'e',
            'ζ': 'z',
            'η': 'i',
            'θ': 'th',
            'ι': 'i',
            'κ': 'k',
            'λ': 'l',
            'μ': 'm',
            'ν': 'n',
            'ξ': 'x',
            'ο': 'o',
            'π': 'p',
            'ρ': 'r',
            'σ': 's',
            'ς': 's',
            'τ': 't',
            'υ': 'y',
            'φ': 'f',
            'χ': 'ch',
            'ψ': 'ps',
            'ω': 'o'
        };

        let out = '';
        for (let i = 0; i < text.length; i++) {
            const ch = text[i];
            out += (map[ch] !== undefined) ? map[ch] : ch;
        }

        return out
            .toString()
            .normalize('NFKD')
            .replace(/\p{Diacritic}/gu, '')
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    }

    // Global συνάρτηση για αφαίρεση κατηγορίας (χρησιμοποιείται από το onclick στο HTML του tag)
    function removeCategory(element, catId) {
        element.parentElement.remove(); // Αφαίρεση visual tag
        const hiddenInp = document.getElementById('hidden-cat-' + catId);
        if (hiddenInp) hiddenInp.remove(); // Αφαίρεση hidden input
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- ΣΤΟΙΧΕΙΑ SLUG & FORM ---
        const genBtn = document.getElementById('generate-slug-btn');
        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.getElementById('post-slug');
        const saveBtn = document.getElementById('save-draft');
        const publishBtn = document.getElementById('publish-btn');
        const isPublishedInput = document.getElementById('is_published');
        const formEl = document.querySelector('form.form-stack');

        if (genBtn && titleInput && slugInput) {
            genBtn.addEventListener('click', function() {
                const title = titleInput.value || '';
                if (!title) {
                    alert('Παρακαλώ γράψε πρώτα τον τίτλο.');
                    return;
                }
                slugInput.value = slugify(title);
            });
        }

        if (saveBtn && formEl && isPublishedInput) {
            saveBtn.addEventListener('click', function() {
                isPublishedInput.value = '0';
                formEl.submit();
            });
        }
        if (publishBtn && formEl && isPublishedInput) {
            publishBtn.addEventListener('click', function() {
                isPublishedInput.value = '1';
                formEl.submit();
            });
        }

        // --- FEATURED IMAGE PREVIEW ---
        const featuredInput = document.querySelector('input[name="featured_image"]');
        const previewContainer = document.getElementById('featured-preview-container');
        const previewImg = document.getElementById('featured-preview-img');
        let featuredObjectUrl = null;
        if (featuredInput && previewContainer && previewImg) {
            featuredInput.addEventListener('change', function() {
                const f = this.files && this.files[0];
                if (featuredObjectUrl) {
                    URL.revokeObjectURL(featuredObjectUrl);
                }
                if (f && f.type && f.type.indexOf('image') === 0) {
                    featuredObjectUrl = URL.createObjectURL(f);
                    previewImg.src = featuredObjectUrl;
                    previewContainer.style.display = 'block';
                } else {
                    previewContainer.style.display = 'none';
                }
            });
        }

        // --- ΔΙΑΧΕΙΡΙΣΗ ΚΑΤΗΓΟΡΙΩΝ & ΓΛΩΣΣΑΣ ---
        const langSelect = document.getElementById('language');
        const catSelect = document.getElementById('category-select');
        const container = document.getElementById('categories-selected-container');
        const hiddenContainer = document.getElementById('hidden-inputs-container');

        // Κρατάμε τις κατηγορίες στη μνήμη για το φιλτράρισμα
        const allOptions = Array.from(catSelect.options).slice(1).map(opt => ({
            value: opt.value,
            text: opt.text,
            lang: opt.getAttribute('data-lang')
        }));

        function filterCategories() {
            const selectedLang = langSelect.value;
            catSelect.innerHTML = '<option value="">Select category</option>';

            allOptions.forEach(opt => {
                if (opt.lang === selectedLang) {
                    const newOpt = document.createElement('option');
                    newOpt.value = opt.value;
                    newOpt.text = opt.text;
                    newOpt.setAttribute('data-lang', opt.lang);
                    catSelect.appendChild(newOpt);
                }
            });

            // Καθαρισμός επιλεγμένων αν αλλάξει η γλώσσα
            container.innerHTML = '';
            hiddenContainer.innerHTML = '';
        }

        if (langSelect && catSelect) {
            langSelect.addEventListener('change', filterCategories);

            catSelect.addEventListener('change', function() {
                const catId = this.value;
                const catName = this.options[this.selectedIndex].text;
                if (catId === "") return;

                // Έλεγχος αν υπάρχει ήδη
                if (document.getElementById('hidden-cat-' + catId)) {
                    this.value = "";
                    return;
                }

                // 1. Δημιουργία Visual Tag
                const tag = document.createElement('span');
                tag.className = 'category-tag';
                tag.style.cssText = 'display:inline-flex; align-items:center; gap:8px; padding:6px 8px; background:#eef7f6; border:1px solid #e0efec; border-radius:6px; cursor:default; font-size:14px; color:#2d5a52; margin-right:5px; margin-bottom:5px;';
                tag.innerHTML = `${catName} <span style="cursor:pointer; color:gray; font-weight:bold;" onclick="removeCategory(this, '${catId}')">&times;</span>`;
                container.appendChild(tag);

                // 2. Δημιουργία Hidden Input
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'categories[]';
                hiddenInput.value = catId;
                hiddenInput.id = 'hidden-cat-' + catId;
                hiddenContainer.appendChild(hiddenInput);

                this.value = ""; // Reset το select
            });

            filterCategories(); // Αρχικό φιλτράρισμα
        }
    });

    // --- TINYMCE INIT ---
    tinymce.init({
        license_key: 'gpl',
        selector: 'textarea#open-source-plugins',
        plugins: [
            'accordion', 'advlist', 'anchor', 'autolink', 'autosave', 'charmap', 'code',
            'codesample', 'directionality', 'emoticons', 'fullscreen', 'help', 'image',
            'importcss', 'insertdatetime', 'link', 'lists', 'media',
            'nonbreaking', 'pagebreak', 'preview', 'quickbars', 'save', 'searchreplace',
            'table', 'visualblocks', 'visualchars', 'wordcount'
        ],
        toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
        autosave_ask_before_unload: true,
        image_advtab: true,
        automatic_uploads: true,
        images_upload_url: "<?= rtrim($BASE_URL, '/') . '/admin/tinymce_upload.php' ?>",
        paste_data_images: true,
        height: 800,
        image_caption: true,
        skin: useDarkMode ? 'oxide-dark' : 'oxide',
        content_css: useDarkMode ? 'dark' : 'default',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });
</script>

<?php require_once PROJECT_ROOT . 'partials/footer.php';
