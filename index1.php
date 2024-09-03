<?php
require('routeros-api-master/routeros_api.class.php');

$API = new RouterosAPI();

if ($API->connect('192.168.9.1', 'rondi', '21184662')) {
    $API->write('/ppp/active/print');
    $connections = $API->read();

    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Koneksi PPPoE Aktif</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Daftar Koneksi PPPoE Aktif</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>User</th>
                    <th>Address</th>
                    <th>Uptime</th>
                    <th>Session ID</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($connections)) {
                    $no = 1;
                    foreach ($connections as $connection) {
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>{$connection['name']}</td>";
                        echo "<td>{$connection['address']}</td>";
                        echo "<td>{$connection['uptime']}</td>";
                        echo "<td>{$connection['session-id']}</td>";
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
