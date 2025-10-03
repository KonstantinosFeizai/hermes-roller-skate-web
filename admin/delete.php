<?php
require_once 'auth_check.php';
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $table = $_POST['table'] ?? null;

    if ($id && $table) {
        // --- SECURITY CHECK ---
        // Validate that the table name is one we expect. This is CRITICAL.
        $allowed_tables = ['contact_messages', 'newsletter_subscribers'];
        if (!in_array($table, $allowed_tables)) {
            echo "Error: Invalid table name.";
            exit;
        }

        try {
            // Use a prepared statement to securely delete the record
            $sql = "DELETE FROM " . $table . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id]);

            // Redirect back to the appropriate management page
            if ($table === 'contact_messages') {
                header('Location: manage_contacts.php?deleted=true');
            } elseif ($table === 'newsletter_subscribers') {
                header('Location: manage_subscribers.php?deleted=true');
            }
            exit;

        } catch (\PDOException $e) {
            echo "Database error: " . $e->getMessage();
            exit;
        }
    }
}

// Redirect back if accessed directly without POST or with missing parameters
header('Location: dashboard.php');
exit;
?>