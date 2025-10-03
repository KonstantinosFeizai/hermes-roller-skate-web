<?php
session_start();
require_once '../config.php';
// Redirect to dashboard if already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        // Find the user in the database
        $sql = "SELECT id, password FROM admin_users WHERE username = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Check if user exists and if the password matches the hash
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_in'] = true;
            header('Location: dashboard.php');
            exit;
        } else {
            $error_message = 'Λάθος όνομα χρήστη ή κωδικός.';
        }
    } catch (\PDOException $e) {
        $error_message = 'Πρόβλημα σύνδεσης στη βάση δεδομένων.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/admin_styles.css">
</head>

<body>
    <div class="container">

        <h1>Σύνδεση Διαχειριστή</h1>
        <?php if ($error_message): ?>
            <p style="color: red;"><?= $error_message ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div>
                <label for="username">Όνομα χρήστη:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <br>
            <div>
                <label for="password">Κωδικός:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <br>
            <button type="submit">Σύνδεση</button>
        </form>
    </div>
</body>

</html>