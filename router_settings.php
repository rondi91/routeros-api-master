<?php
// router_settings.php

// Base URL untuk navigasi kembali ke dashboard
$base_url = "http://routeros-api-master.test/"; // Ganti dengan URL server atau local development environment Anda

// Proses pengaturan router
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $router_ip = $_POST['router_ip'];
    $router_user = $_POST['router_user'];
    $router_password = $_POST['router_password'];
    
    // Lakukan validasi dan simpan pengaturan router ke database atau file konfigurasi
    // Contoh: simpan ke file JSON
    $config = [
        'router_ip' => $router_ip,
        'router_user' => $router_user,
        'router_password' => $router_password
    ];
    
    file_put_contents('router_config.json', json_encode($config));

    // Redirect atau tampilkan pesan berhasil
    $message = "Router settings saved successfully!";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Router Settings - MikroTik Management</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link ke file CSS terpisah -->
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-heading">Menu</div>
    <a href="<?= $base_url ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
    <a href="pppoe_secrets.php"><i class="fas fa-users"></i> PPPoE Secrets</a>
    <a href="network_monitor.php"><i class="fas fa-network-wired"></i> Network Monitor</a>
    <a href="pppoe_billing.php"><i class="fas fa-file-invoice-dollar"></i> Billing</a>
    <a href="schedule.php"><i class="fas fa-calendar-alt"></i> Scheduler</a>
    <a href="ping_test.php"><i class="fas fa-wifi"></i> Ping Test</a>
    <!-- Dropdown menu untuk Settings -->
    <div class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-cogs"></i> Settings</a>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="router_settings.php">Router Settings</a>
            <a class="dropdown-item" href="user_management.php">User Management</a>
            <a class="dropdown-item" href="interface_settings.php">Interface Settings</a>
        </div>
    </div>
</div>

<!-- Tombol Collapse -->
<span class="collapse-btn" id="collapseBtn"><i class="fas fa-bars"></i></span>

<!-- Konten Utama -->
<div class="content" id="content">
    <h2>Router Settings</h2>

    <!-- Tampilkan pesan sukses jika ada -->
    <?php if (isset($message)): ?>
        <div class="alert alert-success" role="alert">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <!-- Form Pengaturan Router -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="router_ip">Router IP Address</label>
            <input type="text" class="form-control" id="router_ip" name="router_ip" placeholder="Enter Router IP" required>
        </div>
        <div class="form-group">
            <label for="router_user">Router Username</label>
            <input type="text" class="form-control" id="router_user" name="router_user" placeholder="Enter Router Username" required>
        </div>
        <div class="form-group">
            <label for="router_password">Router Password</label>
            <input type="password" class="form-control" id="router_password" name="router_password" placeholder="Enter Router Password" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
        <a href="<?= $base_url ?>" class="btn btn-secondary">Back to Dashboard</a> <!-- Link untuk kembali ke dashboard -->
    </form>
</div>

<!-- Bootstrap JS dan Popper -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- Link ke file JavaScript terpisah -->
<script src="scripts.js"></script>

</body>
</html>
