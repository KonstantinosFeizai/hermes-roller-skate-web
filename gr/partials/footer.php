<?php
// partials/footer.php
require_once __DIR__ . '/../../config.php';
?>
<footer>
    <div class="footer-row">
        <div class="footer-column newsletter">
            <h3>Ενημερωτικό Δελτίο</h3>
            <div id="newsletter-message" class="newsletter-message"></div>
            <form class="newsletter-form" aria-label="Φόρμα ενημερωτικού δελτίου">
                <input type="email" name="newsletter_email" placeholder="Εισάγετε το email σας" required
                    aria-label="Διεύθυνση email">
                <button type="submit" aria-label="Εγγραφή">Εγγραφή</button>
            </form>
            <p>
                Εγγραφείτε για να λαμβάνετε τα τελευταία μας νέα και προσφορές.
            </p>
            <div class="social-icons">
                <ul>
                    <li>
                        <a href="https://www.facebook.com/people/Hermes-Rollerskate/61568127231101/" target="_blank"
                            rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο Facebook">
                            <img src="<?= asset('photo/fb.webp') ?>" alt="Facebook">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.instagram.com/hermes_rollerskate/" target="_blank"
                            rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο Instagram">
                            <img src="<?= asset('photo/insta.webp') ?>" alt="Instagram">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.tiktok.com/@hermesrollerskate" target="_blank" rel="noopener noreferrer"
                            aria-label="Ακολουθήστε μας στο TikTok">
                            <img src="<?= asset('photo/tiktok.webp') ?>" alt="TikTok">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.youtube.com/channel/UCT8LRFlRVJduUn0uVeDwPZA" target="_blank"
                            rel="noopener noreferrer" aria-label="Ακολουθήστε μας στο YouTube">
                            <img src="<?= asset('photo/youtube.webp') ?>" alt="YouTube">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.linkedin.com/company/hermes-rollerskate" target="_blank"
                            rel="noopener noreferrer" aria-label="Συνδεθείτε μαζί μας στο LinkedIn">
                            <img src="<?= asset('photo/linkedin.webp') ?>" alt="LinkedIn">
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="footer-column policies">
            <h3><a href="<?= asset('gr/policies.php') ?>" class="heading-link">Πολιτικές & Πληροφορίες</a></h3>
            <ul>
                <li><a href="<?= asset('gr/policies.php#refund') ?>">Πολιτική Επιστροφών & Ακυρώσεων</a></li>
                <li><a href="<?= asset('gr/policies.php#safety') ?>">Κατευθυντήριες Γραμμές Ασφαλείας</a></li>
                <li><a href="<?= asset('gr/policies.php#terms') ?>">Όροι & Προϋποθέσεις</a></li>
                <li><a href="<?= asset('gr/policies.php#privacy') ?>">Πολιτική Απορρήτου</a></li>
                <li><a href="<?= asset('gr/policies.php#FAQ') ?>">Συχνές Ερωτήσεις</a></li>
            </ul>
        </div>
        <div class="footer-column contact">
            <h3><a href="<?= asset('gr/contact.php') ?>" class="heading-link">Επικοινωνία</a></h3>
            <p><strong>📧 Email:</strong><a
                    href="mailto:hermesrollerskate@gmail.com?subject=Inquiry">hermesrollerskate@gmail.com</a></p>
            <p><strong>📍 Τοποθεσία:</strong><a href="https://maps.app.goo.gl/WW9z9DtAkKdPqenU7" target="_blank"
                    rel="noopener noreferrer"> Ζωγράφου,
                    Αθήνα</a></p>
            <p><strong>📍 Τοποθεσία:</strong><a href="https://maps.app.goo.gl/TGBr88W1d2E7BzXSA" target="_blank"
                    rel="noopener noreferrer"> Γέρακας
                </a></p>
            <p><strong>📍 Τοποθεσία:</strong><a href="https://maps.app.goo.gl/tRQuJB6Mh5TqeL8k9" target="_blank"
                    rel="noopener noreferrer"> OAKA,
                    Μαρούσι</a></p>
            <p><strong>📞 Τηλέφωνο:</strong><a href="tel:+306955655189"> +30 6955655189</a></p>
            <p><a href="<?= asset('gr/contact.php') ?>">💬 Στείλτε Μήνυμα</a></p>
        </div>
    </div>

    <!-- Partners Carousel (structure συμβατό με το carousel.js) -->
    <div class="footer-partners-carousel" aria-label="Καρουσέλ Συνεργατών">
        <div class="carousel-wrapper">
            <div class="carousel-track">
                <div class="carousel-item">
                    <a href="https://www.kifissia.gr/" target="_blank" rel="noopener noreferrer">
                        <img src="<?= asset('photo/diimoskifisias.webp') ?>" alt="Δήμος Κηφισιάς">
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
            &copy; 2025 Hermes Rollerskate Academy. Όλα τα δικαιώματα διατηρούνται.
        </p>
    </div>
</footer>

</body>

</html>