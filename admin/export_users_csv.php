<?php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

$filename = "users_export_" . date('Y-m-d') . ".csv";

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');

// UTF-8 BOM so Excel handles Greek characters correctly
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'ID',
    'Username',
    'Όνομα',
    'Επίθετο',
    'Email',
    'Τηλέφωνο',
    'Ρόλος',
    'Τύπος Ρόλου',
    'Κατάσταση',
    'Περιοχή',
    'Ηλικία',
    'Υπόλοιπο (μαθ.)',
    'Εγγραφή',
    'Αθλητής 1',
    'Α1 Αγοράστηκαν',
    'Α1 Χρησιμ.',
    'Α1 Υπόλοιπο',
    'Α1 Σύνολο (€)',
    'Αθλητής 2',
    'Α2 Αγοράστηκαν',
    'Α2 Χρησιμ.',
    'Α2 Υπόλοιπο',
    'Α2 Σύνολο (€)',
]);

$roleTypeLabels = [
    'athlete' => 'Αθλητής',
    'parent'  => 'Γονέας',
    'coach'   => 'Προπονητής',
    'none'    => '-',
];

// Fetch all athletes with balance data, indexed by user_id
$athleteStmt = $pdo->query("
    SELECT a.user_id,
           ab.athlete_name, ab.lessons_purchased, ab.lessons_used,
           ab.lessons_remaining, ab.total_paid
    FROM athletes a
    JOIN athlete_balance ab ON ab.athlete_id = a.id
    WHERE a.user_id IS NOT NULL AND a.is_active = 1
    ORDER BY a.user_id, a.id ASC
");

$athletesByUser = [];
while ($row = $athleteStmt->fetch(PDO::FETCH_ASSOC)) {
    $athletesByUser[$row['user_id']][] = $row;
}

$stmt = $pdo->query("
    SELECT id, username, first_name, last_name, email,
           phone, role, role_type, is_active, region, age, balance, created_at
    FROM users
    ORDER BY created_at DESC
");

while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $athletes = $athletesByUser[$user['id']] ?? [];
    $a1 = $athletes[0] ?? null;
    $a2 = $athletes[1] ?? null;

    fputcsv($output, [
        $user['id'],
        $user['username'],
        $user['first_name'] ?? '',
        $user['last_name']  ?? '',
        $user['email'],
        $user['phone']      ?? '',
        strtoupper($user['role']),
        $roleTypeLabels[$user['role_type']] ?? ($user['role_type'] ?: '-'),
        $user['is_active'] ? 'Ενεργός' : 'Εκκρεμεί',
        $user['region']    ?? '',
        $user['age']       ?? '',
        $user['balance']   ?? 0,
        date('d/m/Y H:i', strtotime($user['created_at'])),
        // Athlete 1
        $a1 ? $a1['athlete_name']        : '',
        $a1 ? $a1['lessons_purchased']   : '',
        $a1 ? $a1['lessons_used']        : '',
        $a1 ? $a1['lessons_remaining']   : '',
        $a1 ? number_format((float)$a1['total_paid'], 2, '.', '') : '',
        // Athlete 2
        $a2 ? $a2['athlete_name']        : '',
        $a2 ? $a2['lessons_purchased']   : '',
        $a2 ? $a2['lessons_used']        : '',
        $a2 ? $a2['lessons_remaining']   : '',
        $a2 ? number_format((float)$a2['total_paid'], 2, '.', '') : '',
    ]);
}

fclose($output);
