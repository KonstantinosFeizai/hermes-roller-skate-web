<?php
// email_config.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Συμπερίληψη των αρχείων του Composer (αν χρησιμοποιείτε Composer)
require_once __DIR__ . '/vendor/autoload.php';

// Δημιουργία αντικειμένου PHPMailer (true = περνάει εξαιρέσεις)
function getMailer()
{
    $mail = new PHPMailer(true);

    // Ρυθμίσεις SMTP (Αυτές θα αλλάξουν όταν ανέβετε στο Plesk/Live)
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com'; // Βάλτε το Host του SMTP σας
    $mail->SMTPAuth = true;
    $mail->Username = 'smtp-username'; // Βάλτε το username του SMTP
    $mail->Password = 'smtp-password'; // Βάλτε τον κωδικό εφαρμογής (όχι τον κανονικό κωδικό!)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // STARTTLS ή SMTPS ανάλογα με τον server
    $mail->Port = 587; // 587 για STARTTLS, 465 για SMTPS

    // Γενικές ρυθμίσεις
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('no-reply@example.com', 'Hermes Roller Skate');

    return $mail;
}
