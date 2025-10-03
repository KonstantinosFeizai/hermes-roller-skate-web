<?php
// whoweare.php

$pageTitle = 'About Hermes Rollerskate Academy | Our Vision & Values';
$pageDescription = 'Discover the mission, values, and dedicated team behind Hermes Rollerskate Academy. Learn how we promote rollerskating as a fun, safe, and educational activity for all ages in Athens.';
$pageKeywords = 'Hermes Rollerskate Academy, rollerskating Athens, skating classes, skating team, skating instructors, skating mission, skating values, family skating, skating community';
$pageCss = 'css/whoweare.css';
$activePage = 'whoweare';

require_once __DIR__ . '/partials/header.php';
?>

<!-- WHO WE ARE SECTION -->
<main>
  <section id="who-we-are" class="who-we-are section">
    <div class="container">
      <h2 class="section-title">Who We Are</h2>
      <p class="section-intro">
        Welcome to <strong>Hermes Roller Skating Academy</strong>. We are a community that promotes the love
        of <strong>rollerskating</strong> and related sports (snowski & iceskating) for all ages. We are
        gradually inviting external coaches in various disciplines (dancing, freestyle, racing) and organize
        sports trips focusing on these sports.
      </p>

      <div class="about-content">
        <div class="about-text">
          <div class="about-mission">
            <h3>Our Mission</h3>
            <p>
              Our mission is to promote a culture of entertainment, free expression, and safety in
              traffic through rollerskating. We believe that rollerskating offers a quality choice for
              family activities and is a means to encourage the creation of safe learning and
              transportation infrastructures. We learn to fall so we know how to get up – not only on
              skates, but in life.
            </p>
          </div>

          <h3>What Makes Us Different?</h3>
          <ul class="differentiators">
            <li>
              <i class="fas fa-trophy"></i> <strong>Pedagogical Approach</strong>: Our coaches set
              clear disciplinary rules and boundaries, while giving children the opportunity to
              express themselves and promote creative thinking.
            </li>
            <li>
              <i class="fas fa-skating fa-lg" style="color: #FFD43B;"></i>
              <strong>Variety of Classes</strong>: In addition to
              basic and racing training, we collaborate with external instructors for specialized
              sessions of freestyle, aggressive, and dancing.
            </li>
            <li>
              <i class="fas fa-graduation-cap"></i> <strong>Progressive Training</strong>:
              Rollerskates can substitute for both snow-skiing and ice-skating when conditions are not
              ideal, providing a versatile experience.
            </li>
            <li>
              <i class="fas fa-globe-americas"></i> <strong>Community &amp; Events</strong>: We are
              committed to strengthening our community by offering the best services prices,
              organizing unforgettable events, and creating unique family sports tourism programs.
            </li>
          </ul>
        </div>

        <div class="about-image">
          <img src="photo/group2.webp" alt="Hermes Academy Photo">
        </div>
      </div>
    </div>
  </section>

  <section id="meet-the-team" class="meet-the-team section">
    <div class="container">
      <h2 class="section-title">Meet Our Team</h2>
      <p class="section-intro">
        Our dedicated team consists of passionate professionals who make skating safe, fun, and educational.
        Our team consists of trainers and educators with teaching experience on rollerskates, ensuring that
        learning remains safe, fun, and educational.
      </p>

      <div class="staff-list">
        <div class="staff-member">
          <img src="photo/andreas.webp" alt="Instructor Andreas Kountouras">
          <h4><i class="fa-solid fa-graduation-cap"></i> Instructor: Andreas
            Kountouras</h4>
          <p>Graduate of Sport Science (UoA), snowski instructor L1/L2, and certified in first aid.</p>
        </div>

        <div class="staff-member">
          <img src="photo/georgia.webp" alt="Instructor Georgia Papazoglou">
          <h4><i class="fa-solid fa-graduation-cap"></i> Instructor: Georgia
            Papazoglou</h4>
          <p>Graduate in English Literature, experienced in teaching children, actively involved with
            rollerskating since 2020.</p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
require_once __DIR__ . '/partials/footer.php';
