<?php
require('../koneksi.php');
$API = new RouterosAPI();

$API->debug = false; // Aktifkan debug jika diperlukan

if (isset($_GET['name'])) {
    $user_name = $_GET['name'];

    if ($API->connect($ip,$user,$pass)) {

        // Ambil detail dari client PPPoE berdasarkan nama
        $client = $API->comm("/ppp/active/print", array(
            "?name" => $user_name
        ));

        if (!empty($client)) {
            $client = $client[0]; // Ambil detail dari client
        } else {
            echo "User not found.";
            exit();
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to Mikrotik.";
        exit();
    }
} else {
    echo "No user specified.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPPoE User Detail - <?= htmlspecialchars($user_name) ?></title>
    <style>
        .traffic-data {
            font-size: 10px;
            font-weight: bold;
        }
        canvas {
            max-width: 50%;
            height: 100px;
        }
    </style>
    <!-- Load Chart.js dari CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let trafficChart;
        let labels = [];
        let downloadData = [];
        let uploadData = [];

        function initializeChart() {
            const ctx = document.getElementById('trafficChart').getContext('2d');
            trafficChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Download (Mbps)',
                            data: downloadData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: true,
                            tension: 0.1,
                        },
                        {
                            label: 'Upload (Mbps)',
                            data: uploadData,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            fill: true,
                            tension: 0.1,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Fungsi untuk memperbarui data trafik dan grafik
        function updateTraffic() {
            let userName = '<?= htmlspecialchars($user_name) ?>';
            fetch('pppoe_traffic.php?name=' + userName)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        // Tambahkan timestamp dan data ke grafik
                        const now = new Date().toLocaleTimeString();
                        labels.push(now);
                        downloadData.push(data.rx);
                        uploadData.push(data.tx);

                        if (labels.length > 10) {  // Batasi jumlah data di grafik
                            labels.shift();
                            downloadData.shift();
                            uploadData.shift();
                        }

                        // Update chart
                        trafficChart.update();
                    }
                })
                .catch(error => console.error('Error fetching traffic data:', error));
        }

        // Initialize chart saat halaman dimuat
        window.onload = function() {
            initializeChart();
            setInterval(updateTraffic, 1000); // Update setiap 5 detik
        };
    </script>
</head>
<body>

<h2>PPPoE User Detail: <?= htmlspecialchars($user_name) ?></h2>

<p><strong>IP Address:</strong> <?= htmlspecialchars($client['address']) ?></p>
<p><strong>Service:</strong> <?= htmlspecialchars($client['service']) ?></p>

<h3>Real-time Traffic (Download/Upload in Mbps)</h3>

<!-- Tempat untuk chart trafik -->
<canvas id="trafficChart"></canvas>

</body>
</html>