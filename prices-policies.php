<?php

$pageTitle = "Roller Skating Prices & Discounts | Hermes Rollerskate Academy ";
$pageDescription = "Discover affordable roller skating lessons at Hermes Rollerskate Academy in Athens. Check out our pricing, discounts for students and families, and special workshop rates.";
$pageKeywords = "roller skating prices, skating lesson costs, Hermes Rollerskate pricing, skating discounts Athens, family skating rates, student skating discounts, private skating lessons, skating workshops Athens";
$pageCss = ['css/prices.css'];
$activePage = 'prices-policies';

require_once __DIR__ . '/partials/header.php';
?>


<main>
  <div class="container">

    <!-- Card 1: Lesson Pricing -->
    <div class="card">
      <h2>Lesson Pricing</h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span>Category</span>
          <span>Discount</span>
          <span>Price per 4 Courses</span>
          <span>Details</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Category">Individual</span>
          <span class="cell" data-label="Discount">—</span>
          <span class="cell" data-label="Price per 4 Courses">25€</span>
          <span class="cell" data-label="Details">One person, all ages</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Category">Student</span>
          <span class="cell" data-label="Discount">20%</span>
          <span class="cell" data-label="Price per 4 Courses">20€</span>
          <span class="cell" data-label="Details">Students 18-24</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Category">2nd Family Member</span>
          <span class="cell" data-label="Discount">20%</span>
          <span class="cell" data-label="Price per 4 Courses">40€</span>
          <span class="cell" data-label="Details">1st-degree relative</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Category">3rd Family Member</span>
          <span class="cell" data-label="Discount">30%</span>
          <span class="cell" data-label="Price per 4 Courses">62€</span>
          <span class="cell" data-label="Details">1st-degree relative</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Category">Referral Discount</span>
          <span class="cell" data-label="Discount">-5€</span>
          <span class="cell" data-label="Price per 4 Courses">One-time</span>
          <span class="cell" data-label="Details">Active referral only</span>
        </div>
      </div>
      <div class="note">
        Note: All 4 courses must be prepaid; 3 no-shows deduct 1 course.
      </div>
    </div>

    <!-- Card 2: Private Lessons Pricing -->
    <div class="card">
      <h2>Private Lessons Pricing</h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span>Participants</span>
          <span>Price / Lesson</span>
          <span>Discount</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">1</span>
          <span class="cell" data-label="Price / Lesson">15€</span>
          <span class="cell" data-label="Discount">None</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">2</span>
          <span class="cell" data-label="Price / Lesson">25€</span>
          <span class="cell" data-label="Discount">17%</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">3</span>
          <span class="cell" data-label="Price / Lesson">30€</span>
          <span class="cell" data-label="Discount">33%</span>
        </div>
      </div>
      <div class="note">
        Note: Schedule by phone; payment after lesson.
      </div>
    </div>

    <!-- Card 3: Special Workshops Pricing -->
    <div class="card">
      <h2>Special Workshops Pricing</h2>
      <div class="table table-4-cols">
        <div class="table-header" role="row">
          <span>Participants</span>
          <span>Price per Lesson</span>
          <span>Refund Rate</span>
          <span>Details</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">Active Member</span>
          <span class="cell" data-label="Price per Lesson">10€</span>
          <span class="cell" data-label="Refund Rate">75%-50%</span>
          <span class="cell" data-label="Details">≥1 lesson left</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">Inactive Member</span>
          <span class="cell" data-label="Price per Lesson">15€</span>
          <span class="cell" data-label="Refund Rate">75%-50%</span>
          <span class="cell" data-label="Details">No lessons left</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Participants">External</span>
          <span class="cell" data-label="Price per Lesson">20€</span>
          <span class="cell" data-label="Refund Rate">75%-50%</span>
          <span class="cell" data-label="Details">Outside team</span>
        </div>
      </div>
      <div class="note">
        Seat booking is prepaid. 75% refund up to 4 days before; 50% up to 2 days. No refund within 48 hours.
      </div>
    </div>

    <!-- Card 4: Schools Pricing -->
    <div class="card">
      <h2>Schools Pricing</h2>
      <div class="table table-3-cols">
        <div class="table-header" role="row">
          <span>Number of Kids</span>
          <span>Price per Child</span>
          <span>Min Fee per month</span>
        </div>

        <div class="table-row" role="row">
          <span class="cell" data-label="Number of Kids">5</span>
          <span class="cell" data-label="Price per Child">120€</span>
          <span class="cell" data-label="Min Fee per month">120€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Number of Kids">10</span>
          <span class="cell" data-label="Price per Child">220€</span>
          <span class="cell" data-label="Min Fee per month">150€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Number of Kids">15</span>
          <span class="cell" data-label="Price per Child">300€</span>
          <span class="cell" data-label="Min Fee per month">180€</span>
        </div>
        <div class="table-row" role="row">
          <span class="cell" data-label="Number of Kids">20</span>
          <span class="cell" data-label="Price per Child">360€</span>
          <span class="cell" data-label="Min Fee per month">200€</span>
        </div>
      </div>
      <div class="note">
        Note: The attendance record is shared with our team to ensure accurate costing.
      </div>
    </div>

    <!-- Card 5: Weather & Cancellations -->
    <!-- Card 5: Weather & Cancellations -->
    <div class="card">
      <h2>
        🌦️ Weather & Cancellations ❌
      </h2>
      <div class="cancellation-reasons-container">
        <div class="cancellation-pill">
          <span class="icon">☔</span>
          <span>Rainy or wet courts</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥶</span>
          <span>Below 5°C</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🥵</span>
          <span>Above 35°C</span>
        </div>
        <div class="cancellation-pill">
          <span class="icon">🚨</span>
          <span>Coach emergency unavailability</span>
        </div>
      </div>
      <div class="note">
        Updates are posted in the Viber group chat.
      </div>
    </div>


  </div>
</main>

<!-- Footer -->
<?php
require_once __DIR__ . '/partials/footer.php';
?>