<?php
// 1. Ξεκινάμε το Session (ΠΡΕΠΕΙ να είναι πρώτο)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Φορτώνουμε το config.php 
//  το config.php είναι στο root folder (έξω από το includes)
require_once __DIR__ . '/../config.php';

// 3. Ορισμός μεταβλητής $isLoggedIn
// Αν υπάρχει user_id στο session, ο χρήστης είναι συνδεδεμένος
$isLoggedIn = isset($_SESSION['user_id']);
$userId = $_SESSION['user_id'] ?? null;
$userName = $_SESSION['user_name'] ?? null;

// 4. Λογική για το Active Page (για το μενού)
// Βρίσκει το όνομα του αρχείου (π.χ. index, whoweare) χωρίς την κατάληξη .php
$activePage = basename($_SERVER['PHP_SELF'], ".php");

// Συνάρτηση helper για το active class στο HTML
if (!function_exists('isActive')) {
    function isActive($current, $target)
    {
        return $current === $target ? ' active' : '';
    }
}

// 5. Λογική Γλώσσας (Placeholder για να μην σκάει το header.php)
$isGreek = isset($_GET['lang']) && $_GET['lang'] === 'gr'; // Απλό παράδειγμα
$english_url = "?lang=en"; // Προσωρινό
$greek_url = "?lang=gr";   // Προσωρινό
