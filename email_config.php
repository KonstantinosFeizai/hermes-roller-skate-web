<?php
// email_config.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Συμπερίληψη των αρχείων του Composer (αν χρησιμοποιείτε Composer)
require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables if present
$env = [];
if (file_exists(__DIR__ . '/.env')) {
    $env = parse_ini_file(__DIR__ . '/.env');
}

// Expose parsed env to the global scope so getMailer() can access it
$GLOBALS['env'] = $env;

// Δημιουργία αντικειμένου PHPMailer (true = περνάει εξαιρέσεις)
function getMailer()
{
    $mail = new PHPMailer(true);

    // Ρυθμίσεις SMTP (Αυτές θα αλλάξουν όταν ανέβετε στο Plesk/Live)
    $mail->isSMTP();
    $mail->Host = isset($GLOBALS['env']['SMTP_HOST']) ? $GLOBALS['env']['SMTP_HOST'] : 'smtp.gmail.com'; // Gmail SMTP
    $mail->SMTPAuth = true;
    $mail->Username = isset($GLOBALS['env']['SMTP_USER']) ? $GLOBALS['env']['SMTP_USER'] : 'hermesrollerskate@gmail.com';
    $mail->Password = isset($GLOBALS['env']['SMTP_PASS']) ? $GLOBALS['env']['SMTP_PASS'] : '';

    $secure = strtolower(isset($GLOBALS['env']['SMTP_SECURE']) ? $GLOBALS['env']['SMTP_SECURE'] : 'tls');
    if ($secure === 'ssl') {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    } else {
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    }

    $mail->Port = (int)(isset($GLOBALS['env']['SMTP_PORT']) ? $GLOBALS['env']['SMTP_PORT'] : 587);

    // Γενικές ρυθμίσεις
    $mail->CharSet = 'UTF-8';
    $fromEmail = isset($GLOBALS['env']['SMTP_FROM']) ? $GLOBALS['env']['SMTP_FROM'] : 'hermesrollerskate@gmail.com';
    $fromName = isset($GLOBALS['env']['SMTP_FROM_NAME']) ? $GLOBALS['env']['SMTP_FROM_NAME'] : 'Hermes Roller Skate';
    $mail->setFrom($fromEmail, $fromName);

    return $mail;
}
