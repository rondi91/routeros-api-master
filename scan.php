<?php
// Include library RouterOS API
require('koneksi.php');

// Inisialisasi API
$API = new RouterosAPI();

if ($API->connect('172.16.30.3', 'rondi', '21184662')) {
    
    // Menulis perintah scan
    $API->write('/interface/wireless/scan', false);
    $API->write('=number=0', false); // Ganti "wlan1" dengan interface yang valid
    $API->write('=duration=10');  // Durasi scan dalam detik
    
    // Membaca hasil scan
    $scanResult = $API->read();
    var_dump($scanResult);
    
    // Tutup koneksi
    $API->disconnect();
} else {
    die("Tidak dapat terhubung ke RouterOS");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Scan Result</title>
    <!-- CSS styling -->
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
</head>
<body>

    <h2>Wireless Scan Result</h2>
    
    <!-- Tampilkan Hasil Scan -->
    <?php if (!empty($scanResult) && empty($scanResult['!trap'])): ?>
        <table>
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
                <?php foreach ($scanResult as $result): ?>
                    <tr>
                        <td><?php echo isset($result['ssid']) ? $result['ssid'] : 'N/A'; ?></td>
                        <td><?php echo isset($result['channel']) ? $result['channel'] : 'N/A'; ?></td>
                        <td><?php echo isset($result['sig']) ? $result['sig'] : 'N/A'; ?></td>
                        <td><?php echo isset($result['tx-rate']) ? $result['tx-rate'] : 'N/A'; ?></td>
                        <td><?php echo isset($result['rx-rate']) ? $result['rx-rate'] : 'N/A'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Tidak ada hasil scan yang tersedia atau terjadi kesalahan.</p>
        <?php if (!empty($scanResult['!trap'])): ?>
            <p>Error: <?php echo $scanResult['!trap'][0]['message']; ?></p>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>
