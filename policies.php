<?php
// policies.php

$pageTitle = " Terms & Policies | Hermes Rollerskate Academy ";
$pageDescription = "Learn about the terms of participation, safety guidelines, refund policy, and privacy practices at Hermes Rollerskate Academy for a safe and enjoyable skating experience.";
$pageKeywords = "terms of participation, safety guidelines, refund policy, privacy practices, skating academy policies, Hermes Rollerskate terms, skating safety rules, refund conditions, data privacy skating";
$pageCss = ['css/policies.css'];
$activePage = 'policies';


require_once __DIR__ . '/partials/header.php';
?>
<!-- Main Content -->
<main>
  <section id="intro" class="policy-section">
    <div class="policy-container">
      <h1>
        📜 Policies & Information
      </h1>
      <p>
        This page outlines our academy’s terms, safety rules, refund policy and privacy practices.
      </p>
    </div>
  </section>

  <section class="policy-section" id="terms">
    <div class="policy-container">
      <h2>
        📑 Terms & Conditions</h2>
      <p>
        By joining Hermes Rollerskate Academy you agree to the terms below that ensure a safe and respectful training
        environment.
      </p>

      <ul class="policy-list">
        <li>
          Lesson Participation — All lessons must be booked and prepaid to secure attendance and smooth scheduling.
        </li>
        <li>
          Respectful Behavior — Maintain a positive attitude and show respect to instructors and fellow skaters.
        </li>
        <li>
          Liability & First Aid — We are not liable for injuries; first-aid kit and basic first-aid knowledge are
          available.
        </li>
        <li>Mandatory Equipment — Helmet, knee/elbow pads and wrist guards are required during practice.
        </li>
        <li>Arrival Time — Arrive 10 minutes earlier to prepare and wear equipment properly.
        </li>
      </ul>

      <p>
        📩 Questions about our terms? Email us at hermesrollerskate@gmail.com
      </p>
    </div>
  </section>

  <section class="policy-section" id="privacy">
    <div class="policy-container">
      <h2>🔐 Privacy Policy</h2>
      <p>
        We protect your personal data and are transparent about how we collect, use and store it.
      </p>

      <ul class="policy-list">
        <li>What we collect — Name/guardian, contact details, sizes, date of birth, preferred days, city.
        </li>
        <li>Photos — Event/class photos may be used for academy history & promotion (consent requested).
        </li>
        <li>How we use — Strictly for academy operations (communication, planning). No sharing/selling.
        </li>
        <li>
          Storage & duration — Google Forms; secure storage; deletion upon request or after 2 years of inactivity.
        </li>
        <li>Your rights — Access, update, correct or delete your data any time.
        </li>
      </ul>
    </div>
  </section>

  <section class="policy-section" id="refund">
    <div class="policy-container">
      <h2>
        💰 Refund & Cancellation Policy
      </h2>
      <p>
        How we handle cancellations, no-shows and refunds for lessons/workshops.
      </p>

      <ul class="policy-list">
        <li>All lessons/activities must be prepaid to secure a spot.
        </li>
        <li>Special lessons: up to 4 days before — 75% refund.
        </li>
        <li>Special lessons: up to 2 days before — 50% refund.
        </li>
        <li>Less than 48h — no refund.</li>
        <li>3 no-notice absences deduct 1 prepaid session.
        </li>
        <li>Discounts are not cumulative.</li>
        <li>Referral discount applies while the referred friend remains active.
        </li>
      </ul>

      <p>
        For cancellations, please inform us as early as possible (phone or email).
      </p>
    </div>
  </section>

  <section class="policy-section" id="safety">
    <div class="policy-container">
      <h2>🛡️ Safety Guidelines</h2>
      <ul class="policy-list">
        <li>Helmet, knee/elbow pads and wrist guards are mandatory.
        </li>
        <li>Skates must be in good condition (brakes/wheels).
        </li>
        <li>Inform the coach for health issues or discomfort.
        </li>
        <li>First-aid trained coaches with medical kit on site.
        </li>
        <li>Lessons may be postponed with rain/wet courts or extreme temperatures (
          <5°C />35°C).
        </li>
        <li>Follow the coach’s instructions at all times.
        </li>
      </ul>
    </div>
  </section>

  <section class="policy-section" id="FAQ">
    <div class="policy-container">
      <h2>❓ Frequently Asked Questions</h2>
      <dl class="faq-list">
        <dt>Do I need to bring my own skates?</dt>
        <dd>No problem if you don’t have any! We provide equipment or guide you on sizes and level.</dd>

        <dt>What if I miss a lesson?</dt>
        <dd>If you notify us in time, we try to reschedule within the month.</dd>

        <dt>Can adults join the academy?</dt>
        <dd>Absolutely — all ages are welcome.</dd>

        <dt>Are private lessons mandatory for beginners?</dt>
        <dd>For brand-new skaters, private lessons are required before joining the group class.</dd>

        <dt>How do I register or ask more?</dt>
        <dd>Fill in our interest form or contact us by phone/email.</dd>
      </dl>
    </div>
  </section>

</main>

<!-- Footer -->
<?php
require_once __DIR__ . '/partials/footer.php';
?>