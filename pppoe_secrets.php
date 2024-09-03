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

// Inisialisasi counter untuk total, active, dan off secrets
$totalSecrets = count($secrets);
$totalActive = 0;
$totalOff = 0;

// Hitung jumlah yang active dan off
foreach ($secrets as $secret) {
    if (in_array($secret['name'], $activeUsers)) {
        $totalActive++;
    } else {
        $totalOff++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar PPPoE Secrets Mikrotik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .off { color: red; }
        .sortable:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Daftar PPPoE Secrets Mikrotik</h1>
        
        <!-- Tambahkan informasi total secrets, active, dan off -->
        <div class="mb-3">
            <p>Total Secrets: <strong><?php echo $totalSecrets; ?></strong></p>
            <p>Total Active: <strong><?php echo $totalActive; ?></strong></p>
            <p>Total Off: <strong><?php echo $totalOff; ?></strong></p>
        </div>

        <table class="table table-bordered" id="pppoeTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th class="sortable" onclick="sortTable(1)">Name</th>
                    <th>Service</th>
                    <th class="sortable" onclick="sortTable(3)">Profile</th>
                    <th>Last Logged Out</th>
                    <th class="sortable" onclick="sortTable(5)">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($secrets)) {
                    $no = 1;
                    foreach ($secrets as $secret) {
                        $status = in_array($secret['name'], $activeUsers) ? 'active' : 'off';
                        $statusClass = $status == 'off' ? 'off' : '';
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td class='{$statusClass}'>{$secret['name']}</td>";
                        echo "<td>{$secret['service']}</td>";
                        echo "<td>{$secret['profile']}</td>";
                        echo "<td>{$secret['last-logged-out']}</td>";
                        echo "<td class='{$statusClass}'>{$status}</td>";
                        echo "</tr>";
                        $no++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    
    <script>
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("pppoeTable");
            switching = true;
            dir = "asc";
            
            while (switching) {
                switching = false;
                rows = table.rows;
                
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</body>
</html>
