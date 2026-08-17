<?php
// includes/createNotification.php

function createNotification(
    PDO $pdo,
    int $userId,
    string $type,
    string $title,
    ?string $body = null,
    ?int $referenceId = null,
    ?string $referenceTable = null
): int {
    $title = trim($title);
    $type = trim($type);
    $body = $body !== null ? trim($body) : null;

    if ($userId <= 0 || $type === '' || $title === '') {
        throw new InvalidArgumentException('Invalid notification data.');
    }

    $stmt = $pdo->prepare("
        INSERT INTO notifications
            (user_id, type, reference_id, reference_table, title, body)
        VALUES
            (:user_id, :type, :reference_id, :reference_table, :title, :body)
    ");

    $stmt->execute([
        ':user_id' => $userId,
        ':type' => $type,
        ':reference_id' => $referenceId,
        ':reference_table' => $referenceTable,
        ':title' => $title,
        ':body' => $body,
    ]);

    return (int)$pdo->lastInsertId();
}

/**
 * Create a translated notification for a user.
 * This function automatically determines the user's language preference
 * and uses the translation keys from the language files.
 */
function createTranslatedNotification(
    PDO $pdo,
    int $userId,
    string $type,
    array $replacements = [],
    ?int $referenceId = null,
    ?string $referenceTable = null
): int {
    // Determine user's language preference (default to 'el')
    $userLang = 'el'; // Default to Greek

    try {
        $stmt = $pdo->prepare("
            SELECT lang FROM users WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$userId]);
        $userRow = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($userRow && !empty($userRow['lang'])) {
            $userLang = in_array($userRow['lang'], ['en', 'el']) ? $userRow['lang'] : 'el';
        }
    } catch (Exception $e) {
        // If lang column doesn't exist or query fails, use default
        error_log('Error fetching user language: ' . $e->getMessage());
    }

    // Load translations for the user's language
    $langFile = PROJECT_ROOT . 'lang/' . $userLang . '.php';
    if (!file_exists($langFile)) {
        throw new InvalidArgumentException("Language file not found: $userLang");
    }
    $translations = require $langFile;

    $notifKey = $translations['notifications'][$type] ?? null;
    if (!$notifKey) {
        throw new InvalidArgumentException("Translation key not found for notification type: $type");
    }

    $title = $notifKey['title'] ?? $type;
    $body = $notifKey['body'] ?? '';

    // Replace placeholders in title and body
    foreach ($replacements as $key => $value) {
        $placeholder = ':' . $key;
        $title = str_replace($placeholder, (string)$value, $title);
        $body = str_replace($placeholder, (string)$value, $body);
    }

    return createNotification(
        $pdo,
        $userId,
        $type,
        $title,
        $body ?: null,
        $referenceId,
        $referenceTable
    );
}

function syncNegativeBalanceNotifications(PDO $pdo, int $athleteId): void
{
    if ($athleteId <= 0) {
        return;
    }

    $stmt = $pdo->prepare("
        SELECT
            a.user_id,
            a.parent_id,
            ab.lessons_remaining
        FROM athletes a
        LEFT JOIN athlete_balance ab ON ab.athlete_id = a.id
        WHERE a.id = ?
        LIMIT 1
    ");
    $stmt->execute([$athleteId]);
    $athlete = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$athlete) {
        return;
    }

    $recipientIds = [];
    if (!empty($athlete['user_id'])) {
        $recipientIds[] = (int)$athlete['user_id'];
    }
    if (!empty($athlete['parent_id'])) {
        $recipientIds[] = (int)$athlete['parent_id'];
    }
    $recipientIds = array_values(array_unique(array_filter($recipientIds)));

    if (!$recipientIds) {
        return;
    }

    $lessonsRemaining = isset($athlete['lessons_remaining']) ? (int)$athlete['lessons_remaining'] : 0;

    foreach ($recipientIds as $recipientId) {
        if ($lessonsRemaining < 0) {
            $stmtExists = $pdo->prepare("
                SELECT id
                FROM notifications
                WHERE user_id = ?
                  AND type = 'negative_balance'
                  AND reference_id = ?
                  AND reference_table = 'athletes'
                ORDER BY id DESC
                LIMIT 1
            ");
            $stmtExists->execute([$recipientId, $athleteId]);
            $existingId = $stmtExists->fetchColumn();

            if (!$existingId) {
                createTranslatedNotification(
                    $pdo,
                    $recipientId,
                    'negative_balance',
                    [],
                    $athleteId,
                    'athletes'
                );
            }
        } else {
            $stmtDelete = $pdo->prepare("
                DELETE FROM notifications
                WHERE user_id = ?
                  AND type = 'negative_balance'
                  AND reference_id = ?
                  AND reference_table = 'athletes'
            ");
            $stmtDelete->execute([$recipientId, $athleteId]);
        }
    }
}
