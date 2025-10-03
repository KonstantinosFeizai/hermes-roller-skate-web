<?php
// classes.php

$pageTitle = 'Roller Skating Classes in Athens | Group & Private Lessons – Hermes Rollerskate';
$pageDescription = "Learn roller skating at our Academy in Zografou, Athens. We offer private and group lessons for beginners and advanced skaters, with a flexible schedule for all ages.";
$pageKeywords = "roller skating classes, roller skating lessons, private skating lessons, group skating classes, skating academy Athens, Zografou";
$pageCss = 'css/classes.css';
$activePage = 'classes';

require_once __DIR__ . '/partials/header.php';
?>

<header class="classes__header">
  <div class="container">
    <h1 class="classes__title">
      Roller Skating Classes for Beginners &amp; Experienced Skaters
    </h1>
    <p class="classes__intro">
      Join Hermes Rollerskate Academy in Zografou, Athens and discover the joy of rollerskating.
      We offer structured group lessons and flexible private sessions for all ages and levels.
    </p>
  </div>
</header>

<main>
  <section id="classes" class="classes section">
    <div class="container">
      <div class="classes__grid">

        <article class="class-card">
          <figure class="class-card__media">
            <img src="photo/private lesson.webp" alt="Private roller skate coaching in Zografou, Athens">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              Private Roller Skating Lessons
            </h2>
            <p class="class-card__text">
              Perfect for complete beginners or those who want to fast-track progress. Private lessons are
              tailored to your pace and needs.
            </p>

            <dl class="class-card__details">
              <dt>Suggested Time Slots</dt>
              <dd>Saturday: 12:30–13:30 &amp; 20:00-20:50</dd>
              <dd>Sunday: 12:30–13:30 &amp; 20:00-20:50</dd>

              <dt>What’s Included</dt>
              <dd>
                Learn how to fall safely, get up, start rolling and stop. After 1–3 sessions, you’ll be ready for a
                group
                class.
              </dd>
            </dl>
          </div>
        </article>

        <article class="class-card">
          <figure class="class-card__media">
            <img src="photo/spot3.webp" alt="Group rollerskating lesson for children and teens in Athens">
          </figure>
          <div class="class-card__body">
            <h2 class="class-card__title">
              Group Roller Skating Lessons
            </h2>
            <p class="class-card__text">
              Our group lessons are ideal for both beginners and intermediate skaters. Each class includes
              skill-building activities, games, and teamwork.
            </p>

            <dl class="class-card__details">
              <dt>Weekly Schedule</dt>
              <dd>Saturday: 09:30–10:30, 10:30–11:30, 11:30–12:30, 17:00–18:00 &amp; 18:00–19:00</dd>
              <dd>Sunday: 09:30–10:30, 10:30–11:30, 11:30–12:30, 17:00–18:00 &amp; 18:00–19:00</dd>

              <dt>What You'll Learn</dt>
              <dd>Beginner: balance, rolling, safe falls, braking</dd>
              <dd>Advanced: cross-step, T-stop, jumps, fish, snake, speed drills</dd>
              <dd>All levels: creative games, rhythm &amp; dance elements</dd>

              <dt>Class Structure</dt>
              <dd>First warm-up together , then split into beginner and advanced based on experience.</dd>
            </dl>
          </div>
        </article>

      </div>
    </div>
  </section>
</main>
<?php
require_once __DIR__ . '/partials/footer.php';