<?php
// partials/footer.php
require_once __DIR__ . '/../config.php';
?>
<footer>
    <div class="footer-row">
        <div class="footer-column newsletter">
            <h3>Newsletter</h3>
            <div id="newsletter-message" class="newsletter-message"></div>
            <form class="newsletter-form" aria-label="Newsletter form">
                <input type="email" name="newsletter_email" placeholder="Enter your email" required
                    aria-label="Email address">
                <button type="submit" aria-label="Subscribe">Subscribe</button>
            </form>
            <p>
                Subscribe to receive our latest news and offers.
            </p>
            <div class="social-icons">
                <ul>
                    <li>
                        <a href="https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/" target="_blank"
                            rel="noopener noreferrer" aria-label="Follow us on Facebook">
                            <img src="<?= asset('photo/fb.webp') ?>" alt="Facebook">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/hermes_rollerskate/" target="_blank"
                            rel="noopener noreferrer" aria-label="Follow us on Instagram">
                            <img src="<?= asset('photo/insta.webp') ?>" alt="Instagram">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.tiktok.com/@hermesrollerskate" target="_blank" rel="noopener noreferrer"
                            aria-label="Follow us on TikTok">
                            <img src="<?= asset('photo/tiktok.webp') ?>" alt="TikTok">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/channel/UCT8LRFlRVJduUn0uVeDwPZA" target="_blank"
                            rel="noopener noreferrer" aria-label="Follow us on YouTube">
                            <img src="<?= asset('photo/youtube.webp') ?>" alt="YouTube">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.linkedin.com/company/hermes-rollerskate" target="_blank"
                            rel="noopener noreferrer" aria-label="Connect with us on LinkedIn">
                            <img src="<?= asset('photo/linkedin.webp') ?>" alt="LinkedIn">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-column policies">
            <h3><a href="<?= asset('policies.php') ?>" class="heading-link">Policies & Information</a></h3>
            <ul>
                <li><a href="<?= asset('policies.php#refund') ?>">Refund & Cancellation Policy</a></li>
                <li><a href="<?= asset('policies.php#safety') ?>">Safety Guidelines</a></li>
                <li><a href="<?= asset('policies.php#terms') ?>">Terms & Conditions</a></li>
                <li><a href="<?= asset('policies.php#privacy') ?>">Privacy Policy</a></li>
                <li><a href="<?= asset('policies.php#FAQ') ?>">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-column contact">
            <h3><a href="<?= asset('contact.php') ?>" class="heading-link">Contact Us</a></h3>
            <p><strong>📧 Email:</strong><a
                    href="mailto:hermesrollerskate@gmail.com?subject=Inquiry">hermesrollerskate@gmail.com</a></p>
            <p><strong>📍 Location:</strong><a href="https://maps.app.goo.gl/WW9z9DtAkKdPqenU7" target="_blank"
                    rel="noopener noreferrer"> Zografou,
                    Athens</a></p>
            <p><strong>📍 Location:</strong><a href="https://maps.app.goo.gl/TGBr88W1d2E7BzXSA" target="_blank"
                    rel="noopener noreferrer"> Gerakas
                </a></p>
            <p><strong>📍 Location:</strong><a href="https://maps.app.goo.gl/tRQuJB6Mh5TqeL8k9" target="_blank"
                    rel="noopener noreferrer"> OAKA,
                    Marousi</a></p>
            <p><strong>📞 Phone:</strong><a href="tel:+306955655189"> +30 6955655189</a></p>
            <p><a href="<?= asset('contact.php') ?>">💬 Send a
                    Message</a></p>
        </div>
    </div>

    <!-- Partners Carousel -->
    <div class="footer-partners-carousel" aria-label="Partners carousel">
        <div class="carousel-wrapper">
            <div class="carousel-track">
                <div class="carousel-item">
                    <a href="https://www.kifissia.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/diimoskifisias.webp') ?>" alt="Municipality of Kifisia">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://powerskate.eu/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/powerskate.webp') ?>" alt="PowerSkate">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://prismaconsulting.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/prisma.webp') ?>" alt="Prisma Consulting">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://ivoluntry.com/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/ivoluntryy.webp') ?>" alt="Ivoluntry">
                    </a>
                </div>
                <div class="carousel-item">
                    <a href="https://bewellmovement.com/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/bewell.png') ?>" alt="Ivoluntry">
                    </a>
                </div>

            </div>
        </div>
    </div>


    <hr class="footer-separator">

    <div class="footer-bottom">
        <p>
            &copy; 2025 Hermes Rollerskate Academy. All Rights Reserved.
        </p>
    </div>
</footer>

</body>

</html>