<?php
// admin/edit_post.php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
restrict_access(['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts');
    exit();
}

try {
    // 1. Φόρτωση των βασικών στοιχείων του Post
    $stmt = $pdo->prepare('SELECT * FROM blog_posts WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        header('Location: ' . $BASE_URL . '/admin/admin_dashboard.php?tab=posts');
        exit();
    }

    // 2. Κατηγορίες ανά γλώσσα (ίδια λογική με add_post)
    $stmt_el = $pdo->query("SELECT id, name FROM categories WHERE language = 'el' ORDER BY name ASC");
    $categories_el = $stmt_el->fetchAll(PDO::FETCH_ASSOC);

    $stmt_en = $pdo->query("SELECT id, name FROM categories WHERE language = 'en' ORDER BY name ASC");
    $categories_en = $stmt_en->fetchAll(PDO::FETCH_ASSOC);

    // 3. Ήδη συνδεδεμένες κατηγορίες για αυτό το post (με γλώσσα)
    $stmt_post_cats = $pdo->prepare("
        SELECT c.id, c.name, c.language
        FROM categories c
        INNER JOIN post_categories pc ON c.id = pc.category_id
        WHERE pc.post_id = :post_id
    ");
    $stmt_post_cats->execute([':post_id' => $id]);
    $current_categories = $stmt_post_cats->fetchAll(PDO::FETCH_ASSOC);

    // 4. Φόρτωση των Admins (Author select)
    $stmt_authors = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'admin' ORDER BY last_name ASC");
    $authors = $stmt_authors->fetchAll(PDO::FETCH_ASSOC);

    // 5. Φόρτωση όλων των άλλων άρθρων για το "Σύνδεση με" (Link with)
    $stmt_all_posts = $pdo->prepare("SELECT id, title, language FROM blog_posts WHERE id != :id ORDER BY title ASC");
    $stmt_all_posts->execute([':id' => $id]);
    $all_other_posts = $stmt_all_posts->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Σφάλμα βάσης: ' . $e->getMessage());
}

// include page-specific css for blog admin
$pageCss = ['css/admin_dashboard.css', 'css/blog_admin.css'];
require_once PROJECT_ROOT . 'partials/header.php';
?>

<div class="admin-wrapper">
    <main class="admin-main-content">
        <div class="tab-header">
            <a href="<?= asset('admin/admin_dashboard.php') ?>" class="action-btn btn-primary">← Επιστροφή</a>
        </div>

        <form action="<?= asset('admin/process_post.php') ?>" method="post" enctype="multipart/form-data" class="form-stack">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
            <input type="hidden" id="is_published" name="is_published" value="<?= !empty($post['is_published']) ? '1' : '0' ?>">

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
                            <input type="text" name="title" required class="form-input" value="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Excerpt</label>
                            <input type="text" name="excerpt" class="form-input" value="<?php echo htmlspecialchars($post['excerpt']); ?>">
                        </div>

                        <div class="form-group">
                            <label>Slug</label>
                            <div style="display:flex;gap:8px;align-items:center;">
                                <input id="post-slug" type="text" name="slug" placeholder="enter-post-slug-here" class="form-input" style="flex:1" value="<?php echo htmlspecialchars($post['slug']); ?>">
                                <button type="button" id="generate-slug-btn" class="action-btn">Generate</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Content</label>
                            <textarea id="open-source-plugins" name="content" rows="12" class="form-input"><?php echo htmlspecialchars($post['content']); ?></textarea>
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
                                    <option value="<?= (int)$author['id'] ?>" <?= (!empty($post['author_id']) && $post['author_id'] == $author['id']) ? 'selected' : '' ?>>
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
                                <option value="<?= (int)$cat['id'] ?>" data-lang="el"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($categories_en as $cat): ?>
                                <option value="<?= (int)$cat['id'] ?>" data-lang="en"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <div id="categories-selected-container" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:6px;">
                            <?php foreach ($current_categories as $cc): ?>
                                <span class="category-tag" style="display:inline-flex;align-items:center;gap:8px;padding:6px 8px;background:#eef7f6;border:1px solid #e0efec;border-radius:6px;font-size:14px;color:#2d5a52;margin-right:6px;"><?= htmlspecialchars($cc['name']) ?> <span style="cursor:pointer; color:gray; font-weight:bold; margin-left:6px;" onclick="removeCategory(this, '<?= (int)$cc['id'] ?>')">&times;</span></span>
                            <?php endforeach; ?>
                        </div>

                        <div id="hidden-inputs-container">
                            <?php foreach ($current_categories as $cc): ?>
                                <input type="hidden" name="categories[]" id="hidden-cat-<?= (int)$cc['id'] ?>" value="<?= (int)$cc['id'] ?>">
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Γλώσσα Άρθρου</label>
                        <select name="language" id="language" class="form-input" required>
                            <option value="el" <?= ($post['language'] == 'el') ? 'selected' : '' ?>>Ελληνικά (EL)</option>
                            <option value="en" <?= ($post['language'] == 'en') ? 'selected' : '' ?>>English (EN)</option>
                        </select>

                        <div style="margin-top:10px;">
                            <label for="translation_id">Σύνδεση με (Μετάφραση)</label>
                            <select name="translation_id" id="translation_id" class="form-input">
                                <option value="">Κανένα (Αυτόνομο)</option>
                                <?php foreach ($all_other_posts as $p): ?>
                                    <option value="<?= (int)$p['id'] ?>" <?= (!empty($post['translation_id']) && $post['translation_id'] == $p['id']) ? 'selected' : '' ?>>
                                        [<?= strtoupper(htmlspecialchars($p['language'] ?? '')) ?>] <?= htmlspecialchars($p['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small style="color: gray;">Αν αυτό το άρθρο είναι μετάφραση κάποιου άλλου, επίλεξε το αρχικό εδώ.</small>
                        </div>

                        <?php
                        // Ψάχνουμε αν υπάρχει ήδη η "αντίθετη" γλώσσα συνδεδεμένη
                        $stmt_check = $pdo->prepare("SELECT id, title, slug FROM blog_posts WHERE translation_id = :tid AND id != :current_id LIMIT 1");
                        $stmt_check->execute([':tid' => $post['translation_id'], ':current_id' => $post['id']]);
                        $linked_post = $stmt_check->fetch(PDO::FETCH_ASSOC);

                        if ($linked_post): ?>
                            <div style="margin-top: 10px; padding: 10px; background: #e3f2fd; border: 1px solid #90caf9; border-radius: 6px; font-size: 13px;">
                                <strong style="color: #1976d2;">ℹ️ Υπάρχει μετάφραση:</strong><br>
                                <a href="edit_post.php?id=<?= (int)$linked_post['id'] ?>" style="text-decoration: none; color: #0d47a1; font-weight: bold;">
                                    <?= htmlspecialchars($linked_post['title']) ?> →
                                </a>
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 10px; padding: 10px; background: #fff3e0; border: 1px solid #ffcc80; border-radius: 6px; font-size: 13px; color: #e65100;">
                                ⚠️ Δεν έχει βρεθεί συνδεδεμένη μετάφραση.
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label>Publish Date</label>
                        <input type="date" name="published_at" class="form-input" value="<?= !empty($post['published_at']) ? date('Y-m-d', strtotime($post['published_at'])) : '' ?>">
                    </div>



                    <div class="form-group">
                        <label>Featured Image (file)</label>
                        <input type="file" name="featured_image" accept="image/*" class="form-input">
                        <div class="image-preview" style="margin-top:8px;">
                            <?php if (!empty($post['featured_image'])): ?>
                                <img id="featured-preview" src="<?= asset('assets/uploads/blog/') ?><?php echo htmlspecialchars($post['featured_image']); ?>" alt="Featured" style="max-width:100%;height:auto;border:1px solid #ddd;padding:4px;" />
                            <?php else: ?>
                                <img id="featured-preview" src="<?= asset('assets/img/placeholder.png') ?>" alt="No image" style="max-width:100%;height:auto;border:1px solid #ddd;padding:4px;" />
                            <?php endif; ?>
                        </div>
                        <div style="margin-top:8px;">
                            <label style="display:inline-block;margin-right:8px;"><input type="checkbox" name="remove_image" value="1"> Αφαίρεση υπάρχουσας εικόνας</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Status</label>
                        <div style="padding:8px;background:#fafafa;border-radius:6px;border:1px solid var(--admin-border);"><?= !empty($post['is_published']) ? 'Published' : 'Draft' ?></div>
                    </div>

                    <div class="form-group">
                        <label>Ενέργειες</label>
                        <div class="form-actions">
                            <button type="submit" id="bottom-save-btn" class="action-btn btn-primary">Αποθήκευση</button>
                            <button type="button" id="cancel-btn" class="action-btn btn-primary">Ακύρωση</button>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </main>
</div>

<!-- TinyMCE (matches add_post) -->
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

    document.addEventListener('DOMContentLoaded', function() {
        const genBtn = document.getElementById('generate-slug-btn');
        const titleInput = document.querySelector('input[name="title"]');
        const slugInput = document.getElementById('post-slug');

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



        // --- ΔΙΑΧΕΙΡΙΣΗ ΚΑΤΗΓΟΡΙΩΝ & ΓΛΩΣΣΑΣ (same logic as add_post) ---
        const langSelect = document.getElementById('language');
        const catSelect = document.getElementById('category-select');
        const container = document.getElementById('categories-selected-container');
        const hiddenContainer = document.getElementById('hidden-inputs-container');

        const allOptions = Array.from(catSelect.options).slice(1).map(opt => ({
            value: opt.value,
            text: opt.text.trim(),
            lang: opt.getAttribute('data-lang')
        }));

        // filterCategories: always re-filters the dropdown.
        // clearSelected=true wipes existing chips (on language change).
        // clearSelected=false keeps server-rendered chips (on initial load).
        function filterCategories(clearSelected) {
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
            if (clearSelected) {
                container.innerHTML = '';
                hiddenContainer.innerHTML = '';
            }
        }

        window.removeCategory = function(element, catId) {
            element.parentElement.remove();
            const hidden = document.getElementById('hidden-cat-' + catId);
            if (hidden) hidden.remove();
        };

        if (langSelect && catSelect) {
            langSelect.addEventListener('change', () => filterCategories(true));

            catSelect.addEventListener('change', function() {
                const catId = this.value;
                const catName = this.options[this.selectedIndex].text;
                if (catId === '') return;
                if (document.getElementById('hidden-cat-' + catId)) {
                    this.value = '';
                    return;
                }
                const tag = document.createElement('span');
                tag.className = 'category-tag';
                tag.style.cssText = 'display:inline-flex; align-items:center; gap:8px; padding:6px 8px; background:#eef7f6; border:1px solid #e0efec; border-radius:6px; cursor:default; font-size:14px; color:#2d5a52; margin-right:5px; margin-bottom:5px;';
                tag.innerHTML = `${catName} <span style="cursor:pointer; color:gray; font-weight:bold;" onclick="removeCategory(this, '${catId}')">&times;</span>`;
                container.appendChild(tag);

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'categories[]';
                hiddenInput.value = catId;
                hiddenInput.id = 'hidden-cat-' + catId;
                hiddenContainer.appendChild(hiddenInput);

                this.value = '';
            });

            // On initial load: filter dropdown to current language but keep pre-populated chips
            filterCategories(false);
        }

        // Save Draft / Publish button behaviour
        const saveBtn = document.getElementById('save-draft');
        const publishBtn = document.getElementById('publish-btn');
        const isPublishedInput = document.getElementById('is_published');
        const formEl = document.querySelector('form.form-stack');

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

        // Bottom save confirmation
        const bottomSaveBtn = document.getElementById('bottom-save-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        if (bottomSaveBtn && formEl) {
            bottomSaveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const ok = confirm('Επιβεβαίωση: Θέλετε να αποθηκεύσετε τις αλλαγές;');
                if (ok) formEl.submit();
            });
        }
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const ok = confirm('Επιβεβαίωση: Θέλετε να ακυρώσετε; Οι αλλαγές δεν θα αποθηκευτούν.');
                if (ok) {
                    // keep the user on the same edit page — reload to restore original values
                    location.reload();
                }
            });
        }

        // Featured image preview
        const featuredInputs = document.querySelectorAll('input[name="featured_image"]');
        featuredInputs.forEach(function(featuredInput) {
            const preview = document.getElementById('featured-preview');
            let featuredObjectUrl = null;
            featuredInput.addEventListener('change', function() {
                const f = this.files && this.files[0];
                if (featuredObjectUrl) {
                    URL.revokeObjectURL(featuredObjectUrl);
                    featuredObjectUrl = null;
                }
                if (f && f.type && f.type.indexOf('image') === 0) {
                    featuredObjectUrl = URL.createObjectURL(f);
                    if (preview) preview.src = featuredObjectUrl;
                }
            });
        });

        // Handle "remove existing image" checkbox: toggle preview to placeholder when checked
        const removeImageCheckbox = document.querySelector('input[name="remove_image"]');
        if (removeImageCheckbox) {
            removeImageCheckbox.addEventListener('change', function() {
                const preview = document.getElementById('featured-preview');
                if (!preview) return;
                if (this.checked) {
                    // store original src so we can restore if unchecked
                    if (!preview.dataset.origSrc) preview.dataset.origSrc = preview.src;
                    preview.src = '<?= asset('assets/img/placeholder.png') ?>';
                } else {
                    if (preview.dataset.origSrc) preview.src = preview.dataset.origSrc;
                }
            });
        }
    });

    tinymce.init({
        license_key: 'gpl',
        selector: 'textarea#open-source-plugins',
        plugins: [
            'accordion', 'advlist', 'anchor', 'autolink', 'autosave', 'charmap', 'code',
            'codesample', 'directionality', 'emoticons', 'fullscreen', 'help', 'image',
            'importcss', 'insertdatetime', 'link', 'lists', 'media',
            'nonbreaking', 'pagebreak', 'preview', 'quickbars', 'save', 'searchreplace',
            'table', 'visualblocks', 'visualchars', 'wordcount',
        ],
        editimage_cors_hosts: ['picsum.photos'],
        menubar: 'file edit view insert format tools table help',
        toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
        autosave_ask_before_unload: true,
        autosave_interval: '30s',
        autosave_prefix: '{path}{query}-{id}-',
        autosave_restore_when_empty: false,
        autosave_retention: '2m',
        image_advtab: true,
        link_list: [{
            title: 'My page 1',
            value: 'https://www.tiny.cloud'
        }, {
            title: 'My page 2',
            value: 'http://www.moxiecode.com'
        }],
        image_list: [{
            title: 'My page 1',
            value: 'https://www.tiny.cloud'
        }, {
            title: 'My page 2',
            value: 'http://www.moxiecode.com'
        }],
        image_class_list: [{
            title: 'None',
            value: ''
        }, {
            title: 'Some class',
            value: 'class-name'
        }],
        importcss_append: true,
        images_upload_url: "<?= rtrim($BASE_URL, '/') . '/admin/tinymce_upload.php' ?>",
        automatic_uploads: true,
        paste_data_images: true,
        images_upload_handler: function(blobInfo, success, failure) {
            const formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());

            fetch("<?= rtrim($BASE_URL, '/') . '/admin/tinymce_upload.php' ?>", {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data && data.location) {
                        success(data.location);
                    } else if (data && data.error) {
                        failure(data.error);
                    } else {
                        failure('Upload failed');
                    }
                })
                .catch(err => failure('Upload error: ' + err.message));
        },
        resize: true,
        file_picker_types: 'file image media',
        file_picker_callback: (callback, value, meta) => {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            if (meta.filetype === 'image') {
                input.setAttribute('accept', 'image/*');
            }
            input.onchange = async function() {
                const file = this.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('file', file);

                try {
                    const resp = await fetch("<?= rtrim($BASE_URL, '/') . '/admin/tinymce_upload.php' ?>", {
                        method: 'POST',
                        body: formData,
                        credentials: 'same-origin'
                    });
                    if (!resp.ok) throw new Error('HTTP ' + resp.status);
                    const data = await resp.json();
                    if (data && data.location) {
                        callback(data.location, {
                            alt: file.name
                        });
                    } else if (data && data.error) {
                        alert('Upload error: ' + data.error);
                    } else {
                        alert('Άγνωστο σφάλμα κατά το ανέβασμα.');
                    }
                } catch (err) {
                    alert('Σφάλμα κατά την αποστολή: ' + err.message);
                }
            };
            input.click();
        },
        height: 800,
        image_caption: true,
        quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
        noneditable_class: 'mceNonEditable',
        toolbar_mode: 'sliding',
        contextmenu: 'link image table',
        skin: useDarkMode ? 'oxide-dark' : 'oxide',
        content_css: useDarkMode ? 'dark' : 'default',
        content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
    });
</script>

<?php require_once PROJECT_ROOT . 'partials/footer.php';
