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

// Fungsi untuk mendapatkan penggunaan frekuensi
function getFrequencyUsage($ip, $username, $password) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/frequency-monitor', false);  // Kirim perintah scan
        $API->write('=number=0', false);  // Ganti "wlan1" dengan interface yang valid
        $API->write('=duration=10');  // Durasi scan dalam detik
        $usageResult = $API->read();
        $API->disconnect();
        return $usageResult;
    } else {
        return ['error' => 'Tidak dapat terhubung ke RouterOS'];
    }
}

// Jika ada request AJAX
if (isset($_POST['router_ip'])) {
    $ip = $_POST['router_ip'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dapatkan hasil penggunaan frekuensi dari router yang dipilih
    $usageResult = getFrequencyUsage($ip, $username, $password);

    // Kirim hasil penggunaan frekuensi sebagai JSON
    echo json_encode($usageResult);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penggunaan Frekuensi Wireless</title>
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

<h2>Penggunaan Frekuensi Wireless</h2>

<!-- Dropdown untuk memilih router -->
<select id="routerSelect">
    <option value="">Pilih Router</option>
    <?php foreach ($routers as $router): ?>
        <option value="<?php echo $router['ip']; ?>" data-username="<?php echo $router['username']; ?>" data-password="<?php echo $router['password']; ?>">
            <?php echo $router['name']; ?>
        </option>
    <?php endforeach; ?>
</select>

<!-- Tabel hasil penggunaan frekuensi -->
<table id="freqUsageTable">
    <thead>
        <tr>
            <th>Frequency</th>
            <th>Usage</th>
            <th>nf</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2">Pilih router untuk melihat penggunaan frekuensi...</td>
        </tr>
    </tbody>
</table>

<script>
$(document).ready(function () {
    let intervalId;

    // Fungsi untuk memulai monitoring penggunaan frekuensi
    function startMonitoring() {
        const routerIp = $('#routerSelect').val();
        const username = $('#routerSelect option:selected').data('username');
        const password = $('#routerSelect option:selected').data('password');

        if (!routerIp) {
            alert('Pilih router terlebih dahulu!');
            return;
        }

        // Set interval untuk mendapatkan data penggunaan frekuensi setiap 10 detik
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
                        updateFrequencyUsage(response);
                    }
                },
                error: function () {
                    alert('Gagal mengambil data penggunaan frekuensi');
                    clearInterval(intervalId);
                }
            });
        }, 10000); // 10 detik sekali
    }

    // Fungsi untuk mengupdate hasil penggunaan frekuensi pada tabel
    function updateFrequencyUsage(usageData) {
        let tableBody = '';

        if (usageData.length > 0) {
            usageData.forEach(function (data) {
                tableBody += `
                    <tr>
                        <td>${data.freq || 'N/A'}</td>
                        <td>${data.use || 'N/A'}</td>
                        <td>${data.nf || 'N/A'}</td>
                    </tr>
                `;
            });
        } else {
            tableBody = `<tr><td colspan="2">Tidak ada data penggunaan frekuensi.</td></tr>`;
        }

        $('#freqUsageTable tbody').html(tableBody);
    }

    // Event listener ketika router dipilih
    $('#routerSelect').on('change', function () {
        startMonitoring();
    });
});
</script>

</body>
</html>
