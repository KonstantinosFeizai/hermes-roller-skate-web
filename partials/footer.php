<?php
// partials/footer.php
// Purpose: Global footer (newsletter, links, contact, partners carousel).

// Core config + language helper
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/lang.php';
?>

<!-- FOOTER CONTENT -->
<footer>
    <!-- Newsletter + social links -->
    <div class="footer-row">
        <div class="footer-column newsletter">
            <h3><?= t('footer.newsletter_title') ?></h3>
            <div id="newsletter-message" class="newsletter-message"></div>
            <form class="newsletter-form" aria-label="Newsletter form">
                <input type="email" name="newsletter_email" placeholder="<?= t('footer.newsletter_placeholder') ?>" required
                    aria-label="Email address">
                <button type="submit" aria-label="Subscribe"><?= t('footer.newsletter_subscribe') ?></button>
            </form>
            <p>
                <?= t('footer.newsletter_text') ?>
            </p>
            <div class="social-icons">
                <ul>
                    <li>
                        <a href="https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/" target="_blank"
                            rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.facebook') ?>">
                            <img src="<?= asset('photo/fb.webp') ?>" alt="Facebook">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/hermes_rollerskate/" target="_blank"
                            rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.instagram') ?>">
                            <img src="<?= asset('photo/insta.webp') ?>" alt="Instagram">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.tiktok.com/@hermesrollerskate" target="_blank" rel="noopener noreferrer"
                            aria-label="<?= t('footer.social_labels.tiktok') ?>">
                            <img src="<?= asset('photo/tiktok.webp') ?>" alt="TikTok">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/channel/UCT8LRFlRVJduUn0uVeDwPZA" target="_blank"
                            rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.youtube') ?>">
                            <img src="<?= asset('photo/youtube.webp') ?>" alt="YouTube">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.linkedin.com/company/hermes-rollerskate" target="_blank"
                            rel="noopener noreferrer" aria-label="<?= t('footer.social_labels.linkedin') ?>">
                            <img src="<?= asset('photo/linkedin.webp') ?>" alt="LinkedIn">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Policies quick links -->
        <div class="footer-column policies">
            <h3><a href="<?= asset('policies.php') ?>" class="heading-link"><?= t('footer.policies_title') ?></a></h3>
            <ul>
                <li><a href="<?= asset('policies.php#refund') ?>"><?= t('footer.policy_refund') ?></a></li>
                <li><a href="<?= asset('policies.php#safety') ?>"><?= t('footer.policy_safety') ?></a></li>
                <li><a href="<?= asset('policies.php#terms') ?>"><?= t('footer.policy_terms') ?></a></li>
                <li><a href="<?= asset('policies.php#privacy') ?>"><?= t('footer.policy_privacy') ?></a></li>
                <li><a href="<?= asset('policies.php#FAQ') ?>"><?= t('footer.policy_faq') ?></a></li>
            </ul>
        </div>
        <!-- Contact info -->
        <div class="footer-column contact">
            <h3><a href="<?= asset('contact.php') ?>" class="heading-link"><?= t('footer.contact_title') ?></a></h3>
            <p><strong><?= t('footer.email_label') ?></strong><a
                    href="mailto:hermesrollerskate@gmail.com?subject=Inquiry">hermesrollerskate@gmail.com</a></p>
            <p><strong><?= t('footer.location_label') ?></strong><a href="https://maps.app.goo.gl/WW9z9DtAkKdPqenU7" target="_blank"
                    rel="noopener noreferrer"> <?= t('footer.locations.zografou_1') ?></a></p>
            <p><strong><?= t('footer.location_label') ?></strong><a href="https://maps.app.goo.gl/QbrQcmkWropaxx1H6" target="_blank"
                    rel="noopener noreferrer"> <?= t('footer.locations.zografou_2') ?></a>
            </p>
            <p><strong><?= t('footer.location_label') ?></strong><a href="https://maps.app.goo.gl/Hdjvv418PZGE3nQU8" target="_blank"
                    rel="noopener noreferrer"> <?= t('footer.locations.gerakas') ?>
                </a></p>
            <p><strong><?= t('footer.location_label') ?></strong><a href="https://maps.app.goo.gl/tRQuJB6Mh5TqeL8k9" target="_blank"
                    rel="noopener noreferrer"> <?= t('footer.locations.oaka') ?></a></p>
            <p><strong><?= t('footer.location_label') ?></strong><a href="https://maps.app.goo.gl/4EZwXij5o2mXq1Lc8" target="_blank"
                    rel="noopener noreferrer"> <?= t('footer.locations.egaleo') ?></a></p>
            <p><strong><?= t('footer.phone_label') ?></strong><a href="tel:+306955655189"> +30 6955655189</a></p>
            <p><a href="<?= asset('contact.php') ?>"><?= t('footer.send_message') ?></a></p>
        </div>
    </div>

    <!-- Partners Carousel -->
    <div class="footer-partners-carousel" aria-label="Partners carousel">
        <div class="carousel-wrapper">
            <div class="carousel-track">
                <div class="carousel-item">
                    <a href="https://www.kifissia.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/diimoskifisias.webp') ?>" alt="<?= t('footer.partner_alts.kifisia') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://powerskate.eu/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/powerskate.webp') ?>" alt="<?= t('footer.partner_alts.powerskate') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://prismaconsulting.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/prisma.webp') ?>" alt="<?= t('footer.partner_alts.prisma') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://ivoluntry.com/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/ivoluntryy.webp') ?>" alt="<?= t('footer.partner_alts.ivoluntry') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://bewellmovement.com/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/bewell.png') ?>" alt="<?= t('footer.partner_alts.bewell') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://www.futureskate.gr/el/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/futureskate.jpg') ?>" alt="<?= t('footer.partner_alts.futureskate') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://kidot.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/kidot.webp') ?>" alt="<?= t('footer.partner_alts.kidot') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://kidot.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/inaction.png') ?>" alt="<?= t('footer.partner_alts.inaction') ?>">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://www.vamvakourevival.org/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/revival.png') ?>" alt="<?= t('footer.partner_alts.revival') ?>">
                    </a>
                </div>

            </div>
        </div>
    </div>


    <hr class="footer-separator">

    <!-- Copyright -->
    <div class="footer-bottom">
        <p>
            <?= t('footer.copyright') ?>
        </p>
    </div>
</footer>

</body>

</html>