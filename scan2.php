<?php
// Include library RouterOS API
require('koneksi.php');

// Data router (Anda bisa simpan di database atau file JSON)
$routers = [
    [
        'name' => 'Router 1',
        'ip' => '172.16.30.3',
        'username' => 'rondi',
        'password' => '21184662'
    ],
    [
        'name' => 'Router 2',
        'ip' => '192.168.88.2',
        'username' => 'admin',
        'password' => 'password'
    ]
];

// Fungsi untuk menghubungkan dan melakukan scan pada router
function getWirelessScan($ip, $username, $password) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/scan', false);
        $API->write('=number=0', false); // Ganti interface sesuai yang ada di router
        $API->write('=duration=10');  // Durasi scan dalam detik
        $scanResult = $API->read();
        $API->disconnect();
        return $scanResult;
    } else {
        return ['error' => 'Tidak dapat terhubung ke RouterOS'];
    }
}

// Jika ada request AJAX
if (isset($_POST['router_ip'])) {
    $ip = $_POST['router_ip'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dapatkan hasil scan dari router yang dipilih
    $scanResult = getWirelessScan($ip, $username, $password);

    // Kirim hasil scan sebagai JSON
    echo json_encode($scanResult);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Scan Real-Time</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<h2>Wireless Scan Real-Time</h2>

<!-- Dropdown untuk memilih router -->
<select id="routerSelect">
    <option value="">Pilih Router</option>
    <?php foreach ($routers as $router): ?>
        <option value="<?php echo $router['ip']; ?>" data-username="<?php echo $router['username']; ?>" data-password="<?php echo $router['password']; ?>">
            <?php echo $router['name']; ?>
        </option>
    <?php endforeach; ?>
</select>

<!-- Tabel hasil scan -->
<table id="scanResultTable">
    <thead>
        <tr>
            <th>SSID</th>
            <th>Frequency</th>
            <th>Signal Strength</th>
            <th>TX Rate</th>
            <th>RX Rate</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="5">Pilih router untuk melihat hasil scan...</td>
        </tr>
    </tbody>
</table>

<script>
$(document).ready(function () {
    let intervalId;

    // Fungsi untuk memulai scan real-time
    function startScan() {
        const routerIp = $('#routerSelect').val();
        const username = $('#routerSelect option:selected').data('username');
        const password = $('#routerSelect option:selected').data('password');

        if (!routerIp) {
            alert('Pilih router terlebih dahulu!');
            return;
        }

        // Set interval untuk melakukan scan setiap 10 detik
        clearInterval(intervalId);
        intervalId = setInterval(function () {
            $.ajax({
                type: 'POST',
                url: '', // Halaman ini
                data: {
                    router_ip: routerIp,
                    username: username,
                    password: password
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.error);
                        clearInterval(intervalId);
                    } else {
                        updateScanResult(response);
                    }
                },
                error: function () {
                    alert('Gagal mengambil data scan');
                    clearInterval(intervalId);
                }
            });
        }, 10000); // 10 detik sekali
    }

    // Fungsi untuk mengupdate hasil scan pada tabel
    function updateScanResult(scanData) {
        let tableBody = '';

        if (scanData.length > 0) {
            scanData.forEach(function (data) {
                tableBody += `
                    <tr>
                        <td>${data.ssid || 'N/A'}</td>
                        <td>${data.channel || 'N/A'}</td>
                        <td>${data['sig'] || 'N/A'}</td>
                        <td>${data['tx-rate'] || 'N/A'}</td>
                        <td>${data['rx-rate'] || 'N/A'}</td>
                    </tr>
                `;
            });
        } else {
            tableBody = `<tr><td colspan="5">Tidak ada data hasil scan.</td></tr>`;
        }

        $('#scanResultTable tbody').html(tableBody);
    }

    // Event listener ketika router dipilih
    $('#routerSelect').on('change', function () {
        startScan();
    });
});
</script>

</body>
</html>
