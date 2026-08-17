<?php
// api/get_notifications.php

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            type,
            reference_id,
            reference_table,
            title,
            body,
            is_read,
            read_at,
            created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY is_read ASC, created_at DESC
        LIMIT 50
    ");
    $stmt->execute([$userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtUnread = $pdo->prepare("
        SELECT COUNT(*)
        FROM notifications
        WHERE user_id = ? AND is_read = 0
    ");
    $stmtUnread->execute([$userId]);
    $unreadCount = (int)$stmtUnread->fetchColumn();

    echo json_encode([
        'status' => 'success',
        'unread_count' => $unreadCount,
        'notifications' => $notifications,
    ]);
} catch (PDOException $e) {
    error_log('get_notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
