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

// Fungsi untuk menghubungkan dan mendapatkan data frequency usage
function getFrequencyUsage($ip, $username, $password) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/frequency-monitor', false);  // Kirim perintah scan
        $API->write('=number=0', false);  // Ganti "wlan1" dengan interface yang valid
        $API->write('=duration=10');  // Durasi scan dalam detik
        $frequencyUsage = $API->read();
        $API->disconnect();
        return $frequencyUsage;
    } else {
        return ['error' => 'Tidak dapat terhubung ke RouterOS'];
    }
}

// Jika ada request AJAX untuk mendapatkan data frequency usage
if (isset($_POST['router_ip'])) {
    $ip = $_POST['router_ip'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Dapatkan hasil penggunaan frekuensi dari router yang dipilih
    $frequencyUsage = getFrequencyUsage($ip, $username, $password);

    // Kirim hasil sebagai JSON
    echo json_encode($frequencyUsage);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Frequency Usage Monitor</title>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<h2>Frequency Usage Monitor</h2>

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
<table id="frequencyUsageTable">
    <thead>
        <tr>
            <th>Frequency</th>
            <th>Usage (%)</th>
            <th>Interference (%)</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3">Pilih router untuk melihat penggunaan frekuensi...</td>
        </tr>
    </tbody>
</table>

<!-- Canvas untuk grafik penggunaan frekuensi -->
<canvas id="frequencyChart" width="400" height="200"></canvas>

<script>
$(document).ready(function () {
    let intervalId;

    // Fungsi untuk memulai monitoring frekuensi real-time
    function startFrequencyMonitor() {
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

    // Fungsi untuk mengupdate tabel penggunaan frekuensi
    function updateFrequencyUsage(frequencyData) {
        let tableBody = '';

        if (frequencyData.length > 0) {
            frequencyData.forEach(function (data) {
                tableBody += `
                    <tr>
                        <td>${data.freq || 'N/A'}</td>
                        <td>${data['use'] || 'N/A'}%</td>
                        <td>${data['nf'] || 'N/A'}%</td>
                    </tr>
                `;
            });
        } else {
            tableBody = `<tr><td colspan="3">Tidak ada data penggunaan frekuensi.</td></tr>`;
        }

        $('#frequencyUsageTable tbody').html(tableBody);

        // Update grafik chart.js
        updateChart(frequencyData);
    }

    // Fungsi untuk mengupdate chart.js
    function updateChart(frequencyData) {
        const labels = frequencyData.map(data => data.freq);
        const usage = frequencyData.map(data => data['use']);
        const interference = frequencyData.map(data => data['nf']);

        const ctx = document.getElementById('frequencyChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Usage (%)',
                        data: usage,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Interference (%)',
                        data: interference,
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Event listener ketika router dipilih
    $('#routerSelect').on('change', function () {
        startFrequencyMonitor();
    });
});
</script>

</body>
</html>
