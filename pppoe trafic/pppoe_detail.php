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
        }
    </style>
    <script>
        // Fungsi untuk memperbarui data trafik
        function updateTraffic() {
            let userName = '<?= htmlspecialchars($user_name) ?>';
            fetch('pppoe_traffic.php?name=' + userName)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                    } else {
                        document.getElementById('download-traffic').textContent = data.rx + ' Mbps';
                        document.getElementById('upload-traffic').textContent = data.tx + ' Mbps';
                    }
                })
                .catch(error => console.error('Error fetching traffic data:', error));
        }

        // Update setiap 5 detik
        setInterval(updateTraffic, 1000);

        // Panggil saat halaman dimuat
        window.onload = updateTraffic;
    </script>
</head>
<body>

<h2>PPPoE User Detail: <?= htmlspecialchars($user_name) ?></h2>

<p><strong>IP Address:</strong> <?= htmlspecialchars($client['address']) ?></p>
<p><strong>Service:</strong> <?= htmlspecialchars($client['service']) ?></p>

<h3>Real-time Traffic</h3>
<p class="traffic-data">Download: <span id="download-traffic">0 Mbps</span></p>
<p class="traffic-data">Upload: <span id="upload-traffic">0 Mbps</span></p>

</body>
</html>
