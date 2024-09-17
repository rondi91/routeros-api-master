<?php
// File JSON atau database untuk menyimpan informasi router
$jsonFile = 'routers.json';

// Fungsi untuk mendapatkan daftar router dari JSON
function getRouters() {
    global $jsonFile;
    $data = file_get_contents($jsonFile);
    return json_decode($data, true);
}

// Fungsi untuk mengambil data wireless dari router menggunakan API Mikrotik
function getWirelessRegistrations($ip, $username, $password) {
    require_once 'koneksi.php';
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        // Fetch wireless registration-table
        $registrations = $API->comm('/interface/wireless/registration-table/print');
        $API->disconnect();
        return $registrations;
    } else {
        return null;
    }
}

$routers = getRouters();
$wirelessData = [];

// Loop untuk mendapatkan data dari semua router
foreach ($routers as $router) {
    $data = getWirelessRegistrations($router['ip_address'], $router['username'], $router['password']);
    if ($data !== null) {
        $wirelessData[$router['name']] = $data;
    } else {
        $wirelessData[$router['name']] = "Error connecting to the router.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Registration</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
     <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
        <div class="sidebar-heading">Menu</div>
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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

    <div class="content" id="content">
    <h2>Wireless Registration Table</h2>

    <!-- Looping untuk menampilkan data wireless dari setiap router -->
    <?php foreach ($wirelessData as $routerName => $data): ?>
        <h3>Router: <?= $routerName; ?></h3>
        <?php if (is_array($data)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>MAC Address</th>
                        <th>Interface</th>
                        <th>Signal Strength</th>
                        <th>Radio name</th>
                        <th>TX Rate</th>
                        <th>RX Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- <?php var_dump($data); ?> -->
                    <?php foreach ($data as $client): ?>
                        <tr>
                            <td><?= $client['mac-address']; ?></td>
                            <td><?= $client['interface']; ?></td>
                            <td><?= $client['signal-strength']; ?></td>
                            <td><?= $client['radio-name']; ?></td>
                            <td><?= $client['tx-rate']; ?></td>
                            <td><?= $client['rx-rate']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?= $data; ?></p> <!-- Menampilkan error jika tidak bisa koneksi ke router -->
        <?php endif; ?>
        <hr>
    <?php endforeach; ?>
</div>

<!-- Bootstrap JS dan Popper -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
   <!-- Link ke file JavaScript terpisah -->
   <script src="scripts.js"></script>
</body>
</html>
