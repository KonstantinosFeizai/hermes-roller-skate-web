<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/admin_styles.css">
</head>

<body>
    <div class="container">

        <h1>Admin Dashboard</h1>
        <p>Welcome to your admin dashboard.</p>
        <a href="manage_subscribers.php">Manage Newsletter Subscribers</a>
        <br>
        <a href="manage_contacts.php">Manage Contact Messages</a>
        <br>
        <a href="logout.php">Logout</a>
    </div>
</body>

</html>