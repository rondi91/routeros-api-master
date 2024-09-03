<?php
require('koneksi.php');

$API = new RouterosAPI();


$interface = 'ether5'; // Ganti dengan nama interface yang ingin Anda monitor

if ($API->connect($ip,$user,$pass)) {
    // Ambil data interface
    $API->write('/interface/monitor-traffic', false);
    $API->write('=interface=' . $interface, false);
    $API->write('=once=', true);
    $interfaceStats = $API->read();
    $API->disconnect();

    // Inisialisasi variabel untuk menyimpan data
    $rxBytes = 0;
    $txBytes = 0;

    if (!empty($interfaceStats)) {
        $rxBytes = $interfaceStats[0]['rx-bits-per-second'];
        $txBytes = $interfaceStats[0]['tx-bits-per-second'];
    }
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Monitor</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['Download', <?php echo $rxBytes; ?>],
                ['Upload', <?php echo $txBytes; ?>]
            ]);

            var options = {
                width: 400, height: 120,
                redFrom: 900000, redTo: 1000000,
                yellowFrom: 500000, yellowTo: 900000,
                minorTicks: 5,
                max: 1000000
            };

            var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

            chart.draw(data, options);

            // Auto refresh every 2 seconds
            setInterval(function() {
                window.location.reload();
            }, 2000);
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1>Network Monitor - Interface: <?php echo $interface; ?></h1>
        <div id="chart_div" style="width: 400px; height: 120px;"></div>
    </div>
</body>
</html>
