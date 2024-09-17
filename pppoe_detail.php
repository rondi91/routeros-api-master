<?php

session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Ambil username yang login
$current_user = $_SESSION['username'];
$role = $_SESSION['role'];

if (!isset($_GET['name'])) {
    echo "No PPPoE user specified.";
    exit();
}

// // Hanya user admin yang bisa akses semua, user biasa hanya bisa akses detail dirinya
// if ($role !== 'admin' && $current_user !== $_GET['name']) {
//     echo "Access denied. You can only view your own PPPoE details.";
//     exit();
// }

// Bagian PHP lainnya untuk mengambil detail PPPoE user
require('koneksi.php');
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

            // Ambil profil PPPoE user
            $profile = isset($client['profile']) ? $client['profile'] : 'default';
            
            // Ambil data profil berdasarkan nama profil user
            $profileData = $API->comm("/ppp/secret/print", ["?name" =>$user_name]);
            $maxSpeed = 15; // Default jika tidak ada data di profil
            $profileData = $profileData[0]['profile'];
// var_dump(intval($profileData));
// die();
           
           

            // Cek apakah ada data profil yang valid dan ambil nilai kecepatan dari comment (misalnya)
            if (!empty($profileData) ) {
                    $maxSpeed = intval($profileData); 
            }
        } else {
            echo "User not found.";
            exit();
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to MikroTik.";
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let rxGauge, txGauge;
        const maxSpeed = <?= $maxSpeed ?>; // Set max speed for gauge (in Mbps)
        console.log(maxSpeed);
        function initializeGauge() {
            const ctxRx = document.getElementById('rxGauge').getContext('2d');
            const ctxTx = document.getElementById('txGauge').getContext('2d');

            rxGauge = new Chart(ctxRx, {
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
                            text: 'Upload (TX)',
                            font: {
                                size: 18
                            }
                        }
                    }
                }
            });

            txGauge = new Chart(ctxTx, {
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
                            text: 'Download (RX)',
                            font: {
                                size: 18
                            }
                        }
                    }
                }
            });
        }

        function updateTraffic() {
            const userName = '<?= htmlspecialchars($user_name) ?>';
            fetch('pppoe_traffic.php?name=' + userName)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        // Update nilai gauge RX dan TX
                        rxGauge.data.datasets[0].data[0] = data.rx;
                        rxGauge.data.datasets[0].data[1] = maxSpeed - data.rx;

                        txGauge.data.datasets[0].data[0] = data.tx;
                        txGauge.data.datasets[0].data[1] = maxSpeed - data.tx;

                        document.getElementById('rx-value').textContent = data.rx + ' Mbpss';
                        document.getElementById('tx-value').textContent = data.tx + ' Mbps';

                        rxGauge.update();
                        txGauge.update();
                    }
                })
                .catch(error => console.error('Error fetching traffic data:', error));
        }

        window.onload = function() {
            initializeGauge();
            setInterval(updateTraffic, 1000); // Update setiap 5 detik
        };
    </script>
</head>
<body>

<h2>PPPoE User Detail: <?= htmlspecialchars($user_name) ?></h2>

<p><strong>IP Address:</strong> <?= htmlspecialchars($client['address']) ?></p>
<p><strong>Service:</strong> <?= htmlspecialchars($client['service']) ?></p>
<p><strong>Paket:</strong> <?= htmlspecialchars($profileData) ?></p>

<h3>Real-time Traffic (Upload/Download in Mbps)</h3>

<!-- Tempat untuk gauge trafik -->
<div class="gauge-container">
    <div>
        <canvas id="rxGauge"></canvas>
        <div class="traffic-data">Upload: <span id="rx-value">0 Mbps</span></div>
    </div>
    <div>
        <canvas id="txGauge"></canvas>
        <div class="traffic-data">Download: <span id="tx-value">0 Mbps</span></div>
    </div>
</div>

</body>
</html>
