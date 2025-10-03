<?php
// activities.php

// Ρυθμίσεις σελίδας πριν το header
$pageTitle = 'Activities for Skating, Skiing & Ice Skating | Hermes Rollerskate Academy';
$pageDescription = 'Discover our diverse activities at Hermes Rollerskate Academy, including roller skating classes, ice skating lessons, and beginner snow skiing programs. Perfect for all ages in Athens.';
$pageKeywords = 'roller skating classes, ice skating lessons, beginner snow skiing, skating activities Athens, Hermes Rollerskate Academy, skating for all ages, skating programs, skating lessons Athens';
$pageCss = 'css/activities.css';
$activePage = 'activities';

require_once __DIR__ . '/partials/header.php';
?>

<!-- INTRO -->
<header class="intro-section">
  <div class="container">
    <h1>From Roller Skating to Ice Skating and Skiing</h1>
    <p>
      Roller skating is more than fun—it's a smart and fun way to build the foundation for winter sports.
      The balance, edge control, and braking techniques you learn on wheels are directly transferable to ice and snow.
    </p>
    <p>
      Our <strong>roller skating classes</strong> are ideal for preparing beginners for their first steps on ice skates
      or snow skis.
      Whether you're a child or an adult, you'll build confidence before winter hits.
    </p>
  </div>
</header>

<main>
  <section id="ice-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="photo/iceskate1.webp" alt="Ice skating lessons for beginners in Athens">

      </figure>
      <div class="activity-content">
        <h2>⛸️ Ice Skating Lessons for Beginners</h2>
        <p>
          Our ice skating group classes help first-timers build balance, learn edge control, and stop safely—perfect for
          those with or without roller skating experience.
        </p>
        <div class="learn-goals">
          <h3>What You'll Learn</h3>
          <ul>
            <li>Basic balance and posture on ice</li>
            <li>Sliding, turning and stopping techniques</li>
            <li>Edge control and motion awareness</li>
            <li>Fun exercises to boost confidence</li>
          </ul>
        </div>
        <div class="target-group">
          <h3>Who's It For?</h3>
          <p>
            Beginners with or without roller skating experience
          </p>
        </div>
        <div class="meta-info">
          <div>⏱️ Duration: 1 hour</div>
          <div>👥 Group Size: 5–15 students</div>
          <div>📍 Location: Local ice rink (TBA)</div>
        </div>
      </div>
    </div>
  </section>


  <section id="ski-beginners" class="activity-section">
    <div class="container grid grid-2">
      <figure class="activity-photo">
        <img src="photo/ski.webp" alt="Beginner snow skiing lessons in Greece">

      </figure>
      <div class="activity-content">
        <h2>🎿 Snow Skiing Beginner Lessons</h2>
        <p>
          This weekend program is ideal for first-time skiers. In small groups, you'll learn the basics of skiing, from
          movement to slope safety—no experience required!
        </p>
        <div class="learn-goals">
          <h3>What You'll Learn</h3>
          <ul>
            <li>Proper stance and balance on skis</li>
            <li>Basic turns and stopping safely</li>
            <li>Control on beginner slopes</li>
            <li>Ski safety & etiquette</li>
          </ul>
        </div>
        <div class="target-group">
          <h3>Who's It For?</h3>
          <p>
            Adults and kids ready for their first skiing experience
          </p>
        </div>
        <div class="meta-info">
          <div>🕒 Duration: 2 days (5 hours/day)</div>
          <div>👥 Group Size: 4–8 students</div>
          <div>📍 Location: Ski resort (TBA)</div>
        </div>
      </div>
    </div>
  </section>
</main>

<section class="cta-section container">
  <div class="cta-content">
    <p>🎯 Ready to start your winter adventure? Begin with roller skating and join our ice and ski lessons!</p>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLScIWPgULw7AtR9Gsvh3Mm8ma5AXzohL4UAUQsKdyZHTTnmqHg/viewform?usp=sf_link"
      target="_blank" rel="noopener" class="button-link" role="button">
      📩 Declaration of participation
    </a>
  </div>
</section>

<?php
require_once __DIR__ . '/partials/footer.php';
