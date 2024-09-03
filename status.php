<?php
require('routeros-api-master/routeros_api.class.php');

$API = new RouterosAPI();

if ($API->connect('192.168.9.1', 'rondi', '21184662')) {
    // Ambil semua PPPoE Secrets
    $API->write('/ppp/secret/print');
    $secrets = $API->read();

    // Ambil semua koneksi PPPoE aktif
    $API->write('/ppp/active/print');
    $activeConnections = $API->read();

    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}

// Buat array untuk memeriksa status aktif
$activeUsers = [];
foreach ($activeConnections as $active) {
    $activeUsers[] = $active['name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar PPPoE Secrets Mikrotik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Daftar PPPoE Secrets Mikrotik</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Service</th>
                    <th>Profile</th>
                    <th>Last Logged Out</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($secrets)) {
                    $no = 1;
                    foreach ($secrets as $secret) {
                        $status = in_array($secret['name'], $activeUsers) ? 'active' : 'off';
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>{$secret['name']}</td>";
                        echo "<td>{$secret['service']}</td>";
                        echo "<td>{$secret['profile']}</td>";
                        echo "<td>{$secret['last-logged-out']}</td>";
                        echo "<td>{$status}</td>";
                        echo "</tr>";
                        $no++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
