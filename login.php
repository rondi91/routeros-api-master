<?php
session_start();
require('koneksi.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $API = new RouterosAPI();
    $API->debug = false;

    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($API->connect($ip,$user,$pass)) {

        // Ambil PPPoE secret untuk mencocokkan username dan password
        $secrets = $API->comm("/ppp/secret/print");

        $user_found = false;
        foreach ($secrets as $secret) {
            if ($secret['name'] === $username && $secret['password'] === $password) {
                $user_found = true;

                // Set session user
                $_SESSION['username'] = $username;
                // var_dump($secret['profile']);
                // die();

                // Cek apakah user ini adalah admin
                if (isset($secret['profile']) && $secret['profile'] === 'admin') {
                    $_SESSION['role'] = 'admin';
                } else {
                    $_SESSION['role'] = 'user';
                }

                header('Location: pppoe_detail.php?name=' . urlencode($username));
                exit();
            }
        }

        if (!$user_found) {
            $error = 'Invalid username or password';
        }

        $API->disconnect();
    } else {
        $error = 'Unable to connect to MikroTik';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login PPPoE</h2>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
</body>
</html>
