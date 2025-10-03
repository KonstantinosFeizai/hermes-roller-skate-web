<?php
require_once 'auth_check.php';
require_once '../config.php';

$search_term = $_GET['search'] ?? '';
$subscribers = [];

try {
    $sql = "SELECT id, email, subscribed_at FROM newsletter_subscribers";
    $params = [];

    // Add search functionality if a search term is provided
    if (!empty($search_term)) {
        $sql .= " WHERE email LIKE ?";
        $params[] = '%' . $search_term . '%';
    }

    $sql .= " ORDER BY subscribed_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $subscribers = $stmt->fetchAll();

} catch (\PDOException $e) {
    echo "Database query failed: " . $e->getMessage();
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subscribers</title>

</head>

<style>
    input[type="text"] {
        width: 300px;
        padding: 8px;
        margin-right: 8px;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    tr:hover {
        background-color: #e9e9e9;
    }

    a {
        text-decoration: none;
        color: #007BFF;
    }

    a:hover {
        text-decoration: underline;
    }

    button {
        padding: 8px 12px;
        background-color: #007BFF;
        color: white;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }
</style>

<body>
    <h1>Newsletter Subscribers</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <br>
    <form method="get" action="manage_subscribers.php">
        <input type="text" name="search" placeholder="Search by email" value="<?= htmlspecialchars($search_term) ?>">
        <button type="submit">Search</button>
    </form>
    <br><br>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Subscribed At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subscribers)): ?>
                <tr>
                    <td colspan="3">No subscribers found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($subscribers as $subscriber): ?>
                    <tr>
                        <td><?= htmlspecialchars($subscriber['id']) ?></td>
                        <td><?= htmlspecialchars($subscriber['email']) ?></td>
                        <td><?= htmlspecialchars($subscriber['subscribed_at']) ?></td>
                        <td>
                            <form action="delete.php" method="post"
                                onsubmit="return confirm('Are you sure you want to delete this subscriber?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($subscriber['id']) ?>">
                                <input type="hidden" name="table" value="newsletter_subscribers">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>