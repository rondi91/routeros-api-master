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
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        canvas {
            max-width: 100%;
            height: 300px;
        }
        .gauge-container {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
    </style>
    <!-- Load Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let rxDoughnut, txDoughnut;
        let maxSpeed = 1000; // Set max speed for gauge (in Mbps)

        function initializeDoughnut() {
            const ctxRx = document.getElementById('rxDoughnut').getContext('2d');
            const ctxTx = document.getElementById('txDoughnut').getContext('2d');

            rxDoughnut = new Chart(ctxRx, {
                type: 'doughnut',
                data: {
                    labels: ['Used', 'Remaining'],
                    datasets: [{
                        data: [0, maxSpeed],
                        backgroundColor: ['rgba(75, 192, 192, 1)', 'rgba(75, 192, 192, 0.2)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '80%',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' Mbps';
                                }
                            }
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Download (RX)',
                            font: {
                                size: 18
                            }
                        }
                    }
                }
            });

            txDoughnut = new Chart(ctxTx, {
                type: 'doughnut',
                data: {
                    labels: ['Used', 'Remaining'],
                    datasets: [{
                        data: [0, maxSpeed],
                        backgroundColor: ['rgba(255, 99, 132, 1)', 'rgba(255, 99, 132, 0.2)'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    cutout: '80%',
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return tooltipItem.label + ': ' + tooltipItem.raw + ' Mbps';
                                }
                            }
                        },
                        legend: {
                            display: false
                        },
                        title: {
                            display: true,
                            text: 'Upload (TX)',
                            font: {
                                size: 18
                            }
                        }
                    }
                }
            });
        }

        function updateTraffic() {
            let userName = '<?= htmlspecialchars($user_name) ?>';
            fetch('pppoe_traffic.php?name=' + userName)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        // Update nilai doughnut RX dan TX
                        rxDoughnut.data.datasets[0].data[0] = data.rx;
                        rxDoughnut.data.datasets[0].data[1] = maxSpeed - data.rx;

                        txDoughnut.data.datasets[0].data[0] = data.tx;
                        txDoughnut.data.datasets[0].data[1] = maxSpeed - data.tx;

                        document.getElementById('rx-value').textContent = data.rx + ' Mbps';
                        document.getElementById('tx-value').textContent = data.tx + ' Mbps';

                        rxDoughnut.update();
                        txDoughnut.update();
                    }
                })
                .catch(error => console.error('Error fetching traffic data:', error));
        }

        window.onload = function() {
            initializeDoughnut();
            setInterval(updateTraffic, 5000); // Update setiap 5 detik
        };
    </script>
</head>
<body>

<h2>PPPoE User Detail: <?= htmlspecialchars($user_name) ?></h2>

<p><strong>IP Address:</strong> <?= htmlspecialchars($client['address']) ?></p>
<p><strong>Service:</strong> <?= htmlspecialchars($client['service']) ?></p>

<h3>Real-time Traffic (Download/Upload in Mbps)</h3>

<!-- Tempat untuk doughnut trafik -->
<div class="gauge-container">
    <div>
        <canvas id="rxDoughnut"></canvas>
        <div class="traffic-data">Download: <span id="rx-value">0 Mbps</span></div>
    </div>
    <div>
        <canvas id="txDoughnut"></canvas>
        <div class="traffic-data">Upload: <span id="tx-value">0 Mbps</span></div>
    </div>
</div>

</body>
</html>