<?php
// includes/recipient_helper.php
// Shared helper: builds recipient list from filter criteria.

function buildRecipientList(PDO $pdo, array $filters): array
{
    $locations   = array_map('intval', $filters['locations']   ?? []);
    $interests   = $filters['interests']  ?? [];   // π.χ. ['interest_ski','interest_races']
    $roles       = $filters['roles']      ?? [];   // π.χ. ['parent','athlete']
    $manual_ids  = array_map('intval', $filters['manual_ids']  ?? []);
    $select_all  = !empty($filters['all']);

    $allowed_interests = [
        'interest_rides',
        'interest_races',
        'interest_ski',
        'interest_skating',
        'interest_hockey',
    ];

    if ($select_all) {
        $stmt = $pdo->query("SELECT id FROM users WHERE role = 'user' AND is_active = 1");
        return array_column($stmt->fetchAll(), 'id');
    }

    $ids = [];
    $hasCriteria = !empty($locations) || !empty($interests) || !empty($roles);

    if ($hasCriteria) {
        $where   = ["u.role = 'user'", "u.is_active = 1"];
        $joins   = [];
        $params  = [];

        if (!empty($roles)) {
            $placeholders = implode(',', array_fill(0, count($roles), '?'));
            $where[]      = "u.role_type IN ($placeholders)";
            $params       = array_merge($params, $roles);
        }

        if (!empty($locations)) {
            $placeholders = implode(',', array_fill(0, count($locations), '?'));
            $joins[]      = "JOIN athletes athl ON athl.user_id = u.id AND athl.is_active = 1";
            $where[]      = "athl.location_id IN ($placeholders)";
            $params       = array_merge($params, $locations);
        } elseif (!empty($interests)) {
            $joins[] = "JOIN athletes athl ON athl.user_id = u.id AND athl.is_active = 1";
        }

        foreach ($interests as $interest) {
            if (in_array($interest, $allowed_interests, true)) {
                $where[] = "athl.{$interest} = 1";
            }
        }

        $sql = "SELECT DISTINCT u.id FROM users u "
            . implode(' ', $joins)
            . " WHERE " . implode(' AND ', $where);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $ids = array_column($stmt->fetchAll(), 'id');
    }

    if (!empty($manual_ids)) {
        $ids = array_unique(array_merge($ids, $manual_ids));
    }

    return array_values($ids);
}
