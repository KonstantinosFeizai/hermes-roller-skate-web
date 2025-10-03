<?php
require_once 'auth_check.php';
require_once '../config.php';

$search_term = $_GET['search'] ?? '';
$submissions = [];

try {
    // Base SQL query
    $sql = "SELECT id, name, surname, email, phone, category, subject, message, created_at FROM contact_messages";
    $params = [];

    // Add search functionality if a search term is provided
    if (!empty($search_term)) {
        $sql .= " WHERE name LIKE ? OR surname LIKE ? OR email LIKE ? OR message LIKE ? OR subject LIKE ? OR phone LIKE ?";
        $params[] = '%' . $search_term . '%';
        $params[] = '%' . $search_term . '%';
        $params[] = '%' . $search_term . '%';
        $params[] = '%' . $search_term . '%';
        $params[] = '%' . $search_term . '%';
        $params[] = '%' . $search_term . '%';
    }

    $sql .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $submissions = $stmt->fetchAll();

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
    <title>Manage Contact Messages</title>
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

    .no-messages {
        text-align: center;
        font-style: italic;
        color: #777;
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
    <h1>Contact Form Submissions</h1>
    <a href="dashboard.php">Back to Dashboard</a>
    <br>
    <form method="get" action="manage_contacts.php">
        <input type="text" name="search" placeholder="Search by name, email, phone or message"
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>
    <br>
    <br>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Surname</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Category</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Submitted At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)): ?>
                <tr>
                    <td colspan="9">No contact messages found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($submissions as $submission): ?>
                    <tr>
                        <td><?= htmlspecialchars($submission['id']) ?></td>
                        <td><?= htmlspecialchars($submission['name']) ?></td>
                        <td><?= htmlspecialchars($submission['surname']) ?></td>
                        <td><?= htmlspecialchars($submission['email']) ?></td>
                        <td><?= htmlspecialchars($submission['phone']) ?></td>
                        <td><?= htmlspecialchars($submission['category']) ?></td>
                        <td><?= htmlspecialchars($submission['subject']) ?></td>
                        <td><?= htmlspecialchars($submission['message']) ?></td>
                        <td><?= htmlspecialchars($submission['created_at']) ?></td>
                        <td>
                            <form action="delete.php" method="post"
                                onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($submission['id']) ?>">
                                <input type="hidden" name="table" value="contact_messages">
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