<?php
// equipment.php

// Core config + language helper
require_once __DIR__ . '/config.php';
require_once PROJECT_ROOT . 'includes/lang.php';

// Page metadata
$pageTitle = t('equipment.meta.title');
$pageDescription = t('equipment.meta.description');
$pageKeywords = t('equipment.meta.keywords');
$pageCss = ['css/equipment.css'];
$activePage = 'equipment';

// Shared header
require_once PROJECT_ROOT . 'partials/header.php';
?>

<!-- EQUIPMENT PAGE CONTENT -->
<!-- Hero Section -->
<section class="eq-hero">
    <div class="eq-hero-bg">
        <div class="eq-hero-blob eq-hero-blob--1"></div>
        <div class="eq-hero-blob eq-hero-blob--2"></div>
    </div>
    <div class="container">
        <h1 class="eq-hero-title"><?= htmlspecialchars(t('equipment.hero.title')) ?></h1>
        <p class="eq-hero-subtitle"><?= htmlspecialchars(t('equipment.hero.subtitle')) ?></p>
    </div>
</section>

<!-- Intro Card -->
<section class="eq-section">
    <div class="container eq-container-sm">
        <div class="eq-intro-card">
            <p>
                <?= htmlspecialchars(t('equipment.intro.text')) ?>
            </p>
        </div>
    </div>
</section>

<!-- Budget Skates Section -->
<section class="eq-section eq-section--muted">
    <div class="container eq-container">
        <div class="eq-section-header">
            <span class="eq-section-label"><?= htmlspecialchars(t('equipment.sections.budget.label')) ?></span>
            <h2 class="eq-section-title"><?= htmlspecialchars(t('equipment.sections.budget.title')) ?></h2>
            <p class="eq-section-desc"><?= htmlspecialchars(t('equipment.sections.budget.desc')) ?></p>
        </div>

        <div class="eq-products-grid">

            <!-- OXELO Play 3 -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/oxelo_p3_pink.jpg') ?>" alt="OXELO Play 3" id="product-1-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">OXELO Play 3</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.play3.subtitle')) ?></p>
                    <span class="eq-product-price">€25</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--pink active" data-image="<?= asset(path: 'photo/oxelo_p3_pink.jpg') ?>" data-target="product-1-img" title="<?= htmlspecialchars(t('equipment.products.play3.color_pink')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--blue" data-image="<?= asset(path: 'photo/oxelo_p3_blue.jpg') ?>" data-target="product-1-img" title="<?= htmlspecialchars(t('equipment.products.play3.color_blue')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- OXELO Play 5 -->
            <div class="eq-product-card eq-product-card--recommended">
                <div class="eq-recommended-badge"><?= htmlspecialchars(t('equipment.products.play5.badge')) ?></div>
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/oxelo_p5.jpg') ?>" alt="OXELO Play 5" id="product-2-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">OXELO Play 5</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.play5.subtitle')) ?></p>
                    <span class="eq-product-price">€35</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--lightblue active" data-image="<?= asset(path: 'photo/oxelo_p5.jpg') ?>" data-target="product-2-img" title="<?= htmlspecialchars(t('equipment.products.play5.color_light_blue')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--bluelemon" data-image="<?= asset(path: 'photo/oxelo_p5_gray.jpg') ?>" data-target="product-2-img" title="<?= htmlspecialchars(t('equipment.products.play5.color_blue_lemon')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- OXELO Fit3 -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/oxelo_fit3_gray.jpg') ?>" alt="OXELO Fit3" id="product-3-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">OXELO Fit3</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.fit3.subtitle')) ?></p>
                    <span class="eq-product-price">€45</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--gray active" data-image="<?= asset(path: 'photo/oxelo_fit3_gray.jpg') ?>" data-target="product-3-img" title="<?= htmlspecialchars(t('equipment.products.fit3.color_gray')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--blackolive" data-image="<?= asset(path: 'photo/oxelo_fit3_black.jpg') ?>" data-target="product-3-img" title="<?= htmlspecialchars(t('equipment.products.fit3.color_black_olive')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--grayred" data-image="<?= asset(path: 'photo/oxelo_fit3_grayred.jpg') ?>" data-target="product-3-img" title="<?= htmlspecialchars(t('equipment.products.fit3.color_gray_red')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- OXELO FIT100 -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/oxelo_fit100_black.jpg') ?>" alt="OXELO FIT100" id="product-4-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">OXELO FIT100</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.fit100.subtitle')) ?></p>
                    <span class="eq-product-price">€50</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--black active" data-image="<?= asset(path: 'photo/oxelo_fit100_black.jpg') ?>" data-target="product-4-img" title="<?= htmlspecialchars(t('equipment.products.fit100.color_black')) ?>"></button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Premium Skates Section -->
<section class="eq-section">
    <div class="container eq-container">
        <div class="eq-section-header">
            <span class="eq-section-label eq-section-label--secondary"><?= htmlspecialchars(t('equipment.sections.premium.label')) ?></span>
            <h2 class="eq-section-title"><?= htmlspecialchars(t('equipment.sections.premium.title')) ?></h2>
            <p class="eq-section-desc"><?= htmlspecialchars(t('equipment.sections.premium.desc')) ?></p>
        </div>

        <div class="eq-products-grid">

            <!-- MICRO Cosmo ID Candy -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/micro_cosmo_bluewhite.jpg') ?>" alt="MICRO Cosmo ID Candy" id="product-5-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">MICRO Cosmo ID Candy</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.cosmo_candy.subtitle')) ?></p>
                    <span class="eq-product-price eq-product-price--premium">€74.90</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--bluewhite active" data-image="<?= asset(path: 'photo/micro_cosmo_bluewhite.jpg') ?>" data-target="product-5-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_candy.color_blue_white')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--pinkwhite" data-image="<?= asset(path: 'photo/micro_cosmo_pinkwhite.jpg') ?>" data-target="product-5-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_candy.color_pink_white')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--whitepurple" data-image="<?= asset(path: 'photo/micro_cosmo_whitepurple.jpg') ?>" data-target="product-5-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_candy.color_white_purple')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- MICRO Cosmo ID Joy Max -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/micro_cosmo_redwhite.jpg') ?>" alt="MICRO Cosmo ID Joy Max" id="product-6-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">MICRO Cosmo ID Joy Max</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.cosmo_joy.subtitle')) ?></p>
                    <span class="eq-product-price eq-product-price--premium">€99.90</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--redwhite active" data-image="<?= asset(path: 'photo/micro_cosmo_redwhite.jpg') ?>" data-target="product-6-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_joy.color_red_white')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--bluewhite" data-image="<?= asset(path: 'photo/micro_cosmo_blue_yellow.jpg') ?>" data-target="product-6-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_joy.color_blue_white')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--lightblue" data-image="<?= asset(path: 'photo/micro_cosmo_lightblue.jpg') ?>" data-target="product-6-img" title="<?= htmlspecialchars(t('equipment.products.cosmo_joy.color_light_blue')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- MICRO Discovery -->
            <div class="eq-product-card">
                <div class="eq-product-image">
                    <img src="<?= asset(path: 'photo/micro_discovery_black.jpg') ?>" alt="MICRO Discovery" id="product-7-img">
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name">MICRO Discovery</h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.discovery.subtitle')) ?></p>
                    <span class="eq-product-price eq-product-price--premium">€119.90</span>
                    <div class="eq-color-buttons">
                        <button class="eq-color-btn eq-color-btn--black active" data-image="<?= asset(path: 'photo/micro_discovery_black.jpg') ?>" data-target="product-7-img" title="<?= htmlspecialchars(t('equipment.products.discovery.color_black')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--blueblack" data-image="<?= asset(path: 'photo/micro_discovery_blueblack.jpg') ?>" data-target="product-7-img" title="<?= htmlspecialchars(t('equipment.products.discovery.color_blue_black')) ?>"></button>
                        <button class="eq-color-btn eq-color-btn--pink" data-image="<?= asset(path: 'photo/micro_discovery_pink.jpg') ?>" data-target="product-7-img" title="<?= htmlspecialchars(t('equipment.products.discovery.color_pink')) ?>"></button>
                    </div>
                </div>
            </div>

            <!-- Adults - FutureSkate -->
            <div class="eq-product-card">
                <div class="eq-product-image eq-product-image--logo">
                    <a href="https://www.futureskate.gr/el/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset(path: 'photo/futureskate.png') ?>" alt="FutureSkate">
                    </a>
                </div>
                <div class="eq-product-body">
                    <div class="eq-product-icon"></div>
                    <h3 class="eq-product-name"><?= htmlspecialchars(t('equipment.products.futureskate.title')) ?></h3>
                    <p class="eq-product-subtitle"><?= htmlspecialchars(t('equipment.products.futureskate.subtitle')) ?></p>
                    <a href="https://www.futureskate.gr/el/" target="_blank" rel="noopener noreferrer" class="eq-futureskate-btn">
                        <?= htmlspecialchars(t('equipment.products.futureskate.button')) ?>
                    </a>
                </div>
            </div>

        </div>

        <!-- Discount Alert -->
        <div class="eq-discount-alert">
            <p><?= t('equipment.discount.text') ?></p>
        </div>
    </div>
</section>

<!-- FAQ Sections -->
<section class="eq-section eq-section--muted">
    <div class="container eq-container-sm">
        <h2 class="eq-section-title text-center"><?= htmlspecialchars(t('equipment.faq.title')) ?></h2>

        <div class="eq-faq-container">

            <!-- Protection FAQ -->
            <div class="eq-faq-item">
                <button class="eq-faq-trigger" onclick="toggleFaq(this)">
                    <span class="eq-faq-title">
                        <span class="eq-faq-icon"><i class="fa-solid fa-shield-halved" style="color: #eb0f0f;"></i></span>
                        <?= htmlspecialchars(t('equipment.faq.protection.title')) ?>
                    </span>
                    <span class="eq-faq-plus">+</span>
                </button>
                <div class="eq-faq-content">
                    <p><?= htmlspecialchars(t('equipment.faq.protection.intro')) ?></p>

                    <div class="eq-protection-grid">
                        <div class="eq-protection-item">
                            <div class="eq-protection-emoji">🖐️</div>
                            <div class="eq-protection-name"><?= htmlspecialchars(t('equipment.faq.protection.wrist')) ?></div>
                        </div>
                        <div class="eq-protection-item">
                            <div class="eq-protection-emoji">💪</div>
                            <div class="eq-protection-name"><?= htmlspecialchars(t('equipment.faq.protection.elbow')) ?></div>
                        </div>
                        <div class="eq-protection-item">
                            <div class="eq-protection-emoji">🦵</div>
                            <div class="eq-protection-name"><?= htmlspecialchars(t('equipment.faq.protection.knee')) ?></div>
                        </div>
                    </div>

                    <p><?= t('equipment.faq.protection.types_intro') ?></p>

                    <div class="eq-protection-types">
                        <div class="eq-protection-type eq-protection-type--primary">
                            <div class="eq-protection-type-container">
                                <h4><?= htmlspecialchars(t('equipment.faq.protection.slip_title')) ?></h4>
                                <p><?= htmlspecialchars(t('equipment.faq.protection.slip_desc')) ?></p>
                                <p class="eq-pros"><?= htmlspecialchars(t('equipment.faq.protection.slip_pros')) ?></p>
                                <p class="eq-cons"><?= htmlspecialchars(t('equipment.faq.protection.slip_cons')) ?></p>
                            </div>
                            <div class="eq-protection-image">
                                <img src="<?= asset(path: 'photo/sleeve.png') ?>" alt="sleeve protection">
                            </div>
                        </div>
                        <div class="eq-protection-type eq-protection-type--secondary">
                            <div class="eq-protection-type-container">
                                <h4><?= htmlspecialchars(t('equipment.faq.protection.velcro_title')) ?></h4>
                                <p><?= htmlspecialchars(t('equipment.faq.protection.velcro_desc')) ?></p>
                                <p class="eq-pros"><?= htmlspecialchars(t('equipment.faq.protection.velcro_pros')) ?></p>
                                <p class="eq-cons"><?= htmlspecialchars(t('equipment.faq.protection.velcro_cons')) ?></p>
                            </div>
                            <div class="eq-protection-image">
                                <img src="<?= asset(path: 'photo/velco.png') ?>" alt="velco protection">
                            </div>
                        </div>
                    </div>

                    <div class="eq-helmet-box">
                        <h4><?= htmlspecialchars(t('equipment.faq.protection.helmet_title')) ?></h4>
                        <p><?= t('equipment.faq.protection.helmet_text') ?></p>
                    </div>
                </div>
            </div>

            <!-- Skate Types FAQ -->
            <div class="eq-faq-item">
                <button class="eq-faq-trigger" onclick="toggleFaq(this)">
                    <span class="eq-faq-title">
                        <span class="eq-faq-icon"><i class="fas fa-skating " style="color: #FFD43B;"></i></span>
                        <?= htmlspecialchars(t('equipment.faq.skates.title')) ?>
                    </span>
                    <span class="eq-faq-plus">+</span>
                </button>
                <div class="eq-faq-content">
                    <h4 class="eq-type-badge eq-type-badge--primary"><?= htmlspecialchars(t('equipment.faq.skates.inline_title')) ?></h4>
                    <p><?= t('equipment.faq.skates.inline_desc') ?></p>

                    <div class="eq-types-grid">
                        <div class="eq-type-item eq-type-item--recommended">
                            <div class="eq-type-name"><?= htmlspecialchars(t('equipment.faq.skates.type_fitness')) ?></div>
                            <div class="eq-type-desc"><?= htmlspecialchars(t('equipment.faq.skates.type_fitness_desc')) ?></div>
                            <div class="eq-type-rec"><?= htmlspecialchars(t('equipment.faq.skates.type_fitness_rec')) ?></div>
                        </div>
                        <div class="eq-type-item">
                            <div class="eq-type-name"><?= htmlspecialchars(t('equipment.faq.skates.type_freestyle')) ?></div>
                            <div class="eq-type-desc"><?= htmlspecialchars(t('equipment.faq.skates.type_freestyle_desc')) ?></div>
                        </div>
                        <div class="eq-type-item">
                            <div class="eq-type-name"><?= htmlspecialchars(t('equipment.faq.skates.type_speed')) ?></div>
                            <div class="eq-type-desc"><?= htmlspecialchars(t('equipment.faq.skates.type_speed_desc')) ?></div>
                        </div>
                        <div class="eq-type-item">
                            <div class="eq-type-name"><?= htmlspecialchars(t('equipment.faq.skates.type_aggressive')) ?></div>
                            <div class="eq-type-desc"><?= htmlspecialchars(t('equipment.faq.skates.type_aggressive_desc')) ?></div>
                        </div>
                        <div class="eq-type-item">
                            <div class="eq-type-name"><?= htmlspecialchars(t('equipment.faq.skates.type_urban')) ?></div>
                            <div class="eq-type-desc"><?= htmlspecialchars(t('equipment.faq.skates.type_urban_desc')) ?></div>
                        </div>
                    </div>

                    <div class="eq-quad-box">
                        <h4><?= htmlspecialchars(t('equipment.faq.skates.quad_title')) ?></h4>
                        <p><?= t('equipment.faq.skates.quad_desc') ?></p>
                    </div>
                </div>
            </div>

            <!-- Components FAQ -->
            <div class="eq-faq-item">
                <button class="eq-faq-trigger" onclick="toggleFaq(this)">
                    <span class="eq-faq-title">
                        <span class="eq-faq-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <?= htmlspecialchars(t('equipment.faq.components.title')) ?>
                    </span>
                    <span class="eq-faq-plus">+</span>
                </button>
                <div class="eq-faq-content">
                    <p><?= htmlspecialchars(t('equipment.faq.components.intro')) ?></p>

                    <div class="eq-components-overview">
                        <div class="eq-component-icon-item">
                            <div class="eq-component-emoji">🥾</div>
                            <div class="eq-component-name"><?= htmlspecialchars(t('equipment.faq.components.boot')) ?></div>
                        </div>
                        <div class="eq-component-icon-item">
                            <div class="eq-component-emoji">🔩</div>
                            <div class="eq-component-name"><?= htmlspecialchars(t('equipment.faq.components.frame')) ?></div>
                        </div>
                        <div class="eq-component-icon-item">
                            <div class="eq-component-emoji">🛞</div>
                            <div class="eq-component-name"><?= htmlspecialchars(t('equipment.faq.components.wheels')) ?></div>
                        </div>
                        <div class="eq-component-icon-item">
                            <div class="eq-component-emoji">⚙️</div>
                            <div class="eq-component-name"><?= htmlspecialchars(t('equipment.faq.components.bearings')) ?></div>
                        </div>
                        <div class="eq-component-icon-item">
                            <div class="eq-component-emoji">📏</div>
                            <div class="eq-component-name"><?= htmlspecialchars(t('equipment.faq.components.sizing')) ?></div>
                        </div>
                    </div>

                    <div class="eq-component-details">
                        <div class="eq-component-detail eq-component-detail--primary">
                            <h5><?= htmlspecialchars(t('equipment.faq.components.boot_title')) ?></h5>
                            <p><?= htmlspecialchars(t('equipment.faq.components.boot_text')) ?></p>
                        </div>
                        <div class="eq-component-detail eq-component-detail--secondary">
                            <h5><?= htmlspecialchars(t('equipment.faq.components.frame_title')) ?></h5>
                            <p><?= htmlspecialchars(t('equipment.faq.components.frame_text')) ?></p>
                        </div>
                        <div class="eq-component-detail eq-component-detail--accent">
                            <h5><?= htmlspecialchars(t('equipment.faq.components.wheels_title')) ?></h5>
                            <p><?= htmlspecialchars(t('equipment.faq.components.wheels_text')) ?></p>
                        </div>
                        <div class="eq-component-detail eq-component-detail--muted">
                            <h5><?= htmlspecialchars(t('equipment.faq.components.sizing_title')) ?></h5>
                            <p><?= htmlspecialchars(t('equipment.faq.components.sizing_text')) ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="eq-cta">
    <div class="container eq-container-sm text-center">
        <h2 class="eq-cta-title"><?= htmlspecialchars(t('equipment.cta.title')) ?></h2>
        <p class="eq-cta-subtitle"><?= htmlspecialchars(t('equipment.cta.subtitle')) ?></p>
        <p class="eq-cta-footer"><?= htmlspecialchars(t('equipment.cta.footer')) ?></p>
    </div>
</section>

<script>
    /* Color variant switcher */
    document.querySelectorAll('.eq-color-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const newImage = this.dataset.image;
            const targetImg = document.getElementById(targetId);

            this.parentElement.querySelectorAll('.eq-color-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            targetImg.style.opacity = '0';
            setTimeout(() => {
                targetImg.src = newImage;
                targetImg.style.opacity = '1';
            }, 150);
        });
    });

    /* FAQ toggle */
    function toggleFaq(trigger) {
        const item = trigger.parentElement;
        const content = item.querySelector('.eq-faq-content');
        const isOpen = item.classList.contains('open');

        // Close others
        document.querySelectorAll('.eq-faq-item').forEach(i => {
            i.classList.remove('open');
            i.querySelector('.eq-faq-trigger').setAttribute('aria-expanded', 'false');
        });

        if (!isOpen) {
            item.classList.add('open');
            trigger.setAttribute('aria-expanded', 'true');
        }
    }
</script>

<?php
// Shared footer
require_once PROJECT_ROOT . 'partials/footer.php';
?>