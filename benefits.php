<?php
// benefits.php

$pageTitle = " Roller Skating Benefits & Lifestyle | Hermes Rollerskate Academy ";
$pageDescription = "Discover the numerous benefits of roller skating as a lifestyle choice. From health and fitness to community and travel, learn how Hermes Rollerskate Academy promotes skating as a fun, inclusive, and enriching activity for all ages.";
$pageKeywords = "roller skating benefits, skating lifestyle, health benefits of skating, skating community, skating travel, Hermes Rollerskate Academy, inclusive skating, skating fitness, urban skating culture";
$pageCss = "css/benefits.css";
$activePage = "benefits";

require_once __DIR__ . '/partials/header.php';
?>

<main>
  <!-- 1. European RollerSkate Culture & Sports Tourism -->
  <section class="lifestyle-section">
    <div class="container">
      <h3>
        🌍 Explore Europe on Roller Skates: Culture & Adventure
      </h3>

      <p>
        Discover iconic cities like Nice and Barcelona from a new perspective—on wheels. Roller skating combines
        sightseeing with fitness, letting you roll past landmarks, coastal promenades, and cultural gems.
      </p>

      <p>
        Join guided tours with local skate communities, meet international skaters, and make unforgettable memories
        along the most famous skating boulevards in Europe.
      </p>

      <p>
        Roller tourism is growing—be part of it with Hermes and turn every trip into an adventure.
      </p>

      <!-- Image Gallery -->
      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/nice france.webp') ?>" alt="Roller ride in Nice, France">
          <figcaption>
            Promenade des Anglais, Nice, France
          </figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/barchelona.webp') ?>" alt="Skating along Barcelona beach">
          <figcaption>
            Barceloneta Beach, Barcelona
          </figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 2. Family Activity -->
  <section id="family-activity" class="lifestyle-section">
    <div class="container">
      <h3>
        👨‍👩‍👧‍👦 RollerSkating for the whole Family
      </h3>

      <p>
        Spend quality time with your loved ones while staying active! Our family roller skate sessions offer something
        for everyone—whether it’s your child’s first roll or a nostalgic glide for parents and grandparents.
      </p>

      <p>
        Skating together builds trust, balance, and beautiful shared moments. Perfect for birthdays, weekend outings,
        and active holidays.
      </p>

      <p>
        No gear? No problem—we provide everything. Just bring your smile.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activity.webp') ?>" alt="Family roller skating fun">
          <figcaption>Family Skate Fun</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 3. Learn One, Master Two More -->
  <section id="learn-more" class="lifestyle-section">
    <div class="container">
      <h3> One Skill, Endless Possibilities</h3>

      <p>
        Start with roller skating and unlock new worlds—ice skating and snow skiing become easier when you already
        master balance, control, and movement on wheels.
      </p>

      <p>
        Our method connects skills across disciplines. You’ll progress faster and feel safer on every surface—pavement,
        ice or snow.
      </p>

      <p>
        Interested in more than just basics? Try dance skating, freestyle, racing or aggressive skating and develop
        high-level abilities that impress.
      </p>

      <p>
        With every lesson, you’re not just skating—you’re building a foundation for lifelong mobility and confidence.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/family activityy.webp') ?>" alt="RollerSkating">
          <figcaption>Roller Foundations</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/snowski.webp') ?>" alt="SnowSki">
          <figcaption>Snow Transition</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/iceskate2.webp') ?>" alt="IceSkating">
          <figcaption>Ice Transition</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 4. Healthy Competition & Local Races -->
  <section id="competition" class="lifestyle-section">
    <div class="container">
      <h3>
        🏆 Friendly Competitions & Local Skate Races
      </h3>

      <p>
        Our events are more than just races—they’re celebrations of personal growth and community spirit. Whether you're
        4 or 104, there's a place for you on the starting line.
      </p>

      <p>
        Join time trials, mini-marathons and skill challenges designed for all levels—from first-timers to seasoned
        skaters.
      </p>

      <p>
        Compete with friends, classmates or family members and earn medals, certificates, and confidence.
      </p>

      <p>
        Roller skating events help you set goals, train with purpose and have fun while improving your fitness and
        focus.
      </p>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/racee.webp') ?>" alt="Local Roller Skate Race">
          <figcaption>Community Race Day</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/aponomes.webp') ?>" alt="Awards Ceremony">
          <figcaption>Awards</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 5. Professional Pathways & Coaching -->
  <section id="pathways" class="lifestyle-section">
    <div class="container">
      <h3>
        🎓 Coaching & Career Development through RollerSkating
      </h3>

      <p>
        Do you dream of coaching others, joining performances or becoming a certified athlete? Our structured programs
        guide you step-by-step through skill mastery and professional opportunities.
      </p>

      <p>
        Train with experienced instructors in roller skating, ice skating and skiing. Learn how to teach, lead, and
        inspire others—both kids and adults.
      </p>

      <p>
        Get access to coaching certifications, workshops, event organizing know-how, and real-life mentoring from
        professionals in the field.
      </p>

      <a href="https://docs.google.com/forms/d/e/1FAIpQLSfDa7gGuJDpYOI3_pESB5l4OiF7iAnOBsAQYrINmD19tabiUQ/viewform"
        target="_blank" rel="noopener" class="button-link">
        📩 Coach's application
      </a>

      <div class="lifestyle-images">
        <figure>
          <img src="<?= asset('photo/instractor.webp') ?>" alt="RollerSkate Coach/Instructor">
          <figcaption>Rollerskate Coach/instructor</figcaption>
        </figure>
        <figure>
          <img src="<?= asset('photo/modela.webp') ?>" alt="RollerSkate Models">
          <figcaption>Rollerskate Model</figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- 6. Community & Local Micro-communities -->
  <section id="community" class="lifestyle-section">
    <div class="container">
      <h3>
        🤝 Find Your Skate Community Near You
      </h3>

      <p>
        You’re never alone on wheels. Across Athens and Greece, small skate communities welcome beginners, families, and
        advanced skaters with open arms.
      </p>

      <p>
        Join weekly meetups, night rides, social events, or just train together in a park. You'll improve faster, make
        friends and stay motivated.
      </p>

      <p>
        We collaborate with some of the best local teams to help you find your skating family—right in your
        neighborhood.
      </p>

      <div class="microcommunities">
        <!-- Community 1 -->
        <div class="community-item">
          <a href="https://cityskaters.gr/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/city skaters.webp') ?>" alt="City Skaters Athens">
            <h3>City Skaters Athens</h3>
          </a>
          <p>
            Weekly rides, sessions freestyle and aggressive
          </p>
        </div>

        <!-- Community 2 -->
        <div class="community-item">
          <a href="https://www.patiniasocks.com/" target="_blank" rel="noopener">
            <img src="<?= asset('photo/patinia community.webp') ?>" alt="Patinia Community">
            <h3>Patinia Community</h3>
          </a>
          <p>
            Patinia is the biggest roller skating community in Greece and Cyprus. They rolling around the streets, park
            skating, and dancing
          </p>
        </div>

        <!-- Community 3 -->
        <div class="community-item">
          <a href="https://linktr.ee/zoepatini?fbclid=PAZXh0bgNhZW0CMTEAAac56C1Fqan6f3URctJhYhLQ5Wk2q_jXSYiZwVbMMBvVbwZGCEehrdpPoOGTug_aem_Z3TUDktI_a0xBFl9eTetSg"
            target="_blank" rel="noopener">
            <img src="<?= asset('photo/zoepatini.webp') ?>" alt="Zoe Patini">
            <h3>Zoe Patini</h3>
          </a>
          <p>
            The team Zoi Patini, inspired by the Greek phrase -you’ve turned my life into a roller skate-, brings the
            high-energy rollerdancing class Oh my quad to Thessaloniki and Athens!
          </p>
        </div>
      </div>
    </div>
  </section>
</main>

<?php
require_once __DIR__ . '/partials/footer.php';
?>