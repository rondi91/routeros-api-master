<?php
require('../koneksi.php');
$API = new RouterosAPI();

$API->debug = false; // Aktifkan debug jika diperlukan

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPPoE Active Users</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Active PPPoE Users</h2>

<?php
// Coba hubungkan ke Mikrotik
if ($API->connect($ip,$user,$pass)) {

    // Ambil semua client PPPoE yang aktif
    $active_clients = $API->comm("/ppp/active/print");

    if (!empty($active_clients)) {
        // Tampilkan dalam tabel jika ada client
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>Username</th>';
        echo '<th>IP Address</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        // Loop untuk setiap client yang aktif
        foreach ($active_clients as $client) {
            echo '<tr>';
            echo '<td>' . (isset($client['name']) ? $client['name'] : 'N/A') . '</td>';
            echo '<td>' . (isset($client['address']) ? $client['address'] : 'N/A') . '</td>';
            echo '<td><a href="pppoe_detail.php?name=' . $client['name'] . '">View Traffic</a></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo "<p>No active PPPoE clients found.</p>";
    }

    $API->disconnect(); // Putuskan koneksi ke Mikrotik
} else {
    echo "<p>Failed to connect to Mikrotik.</p>";
}
?>

</body>
</html>
