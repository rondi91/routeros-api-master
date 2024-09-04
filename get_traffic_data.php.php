<?php 

require('network_monitor.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Network Monitor Mikrotik</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['gauge']});
        google.charts.setOnLoadCallback(drawGauges);

        function drawGauges() {
            // Fetch data from the PHP script
            fetch('network_monitor.php')  // Pastikan nama file di sini benar dan hanya satu kali '.php'
                .then(response => response.json())
                .then(data => {
                    console.log('Data fetched:', data); // Debug: log data fetched
                    Object.keys(data).forEach((interface, index) => {
                        drawGauge(interface, data[interface].rx_mbps, data[interface].tx_mbps);
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        function drawGauge(interface, rxMbps, txMbps) {
            var data = google.visualization.arrayToDataTable([
                ['Label', 'Value'],
                ['RX Mbps', rxMbps],
                ['TX Mbps', txMbps]
            ]);

            var options = {
                width: 400, height: 120,
                redFrom: 80, redTo: 100,
                yellowFrom: 60, yellowTo: 80,
                minorTicks: 5,
                max: 100
            };

            // Debug: log chart drawing
            console.log('Drawing gauge for:', interface, 'RX Mbps:', rxMbps, 'TX Mbps:', txMbps);

            var chart = new google.visualization.Gauge(document.getElementById('chart_div_' + interface));
            chart.draw(data, options);
        }

        setInterval(drawGauges, 1000); // Update data setiap 5 detik
    </script>
</head>
<body>
    <h1>Network Monitor Mikrotik</h1>
    <!-- <h2>ether 1</h2>
    <div id="chart_div_ether1" style="width: 400px; height: 120px;"></div> -->
    <h2>ether 2</h2>
    <div id="chart_div_ether2" style="width: 400px; height: 120px;"></div>
    <!-- <h2>ether 3</h2>
    <div id="chart_div_ether3" style="width: 400px; height: 120px;"></div> -->
    <h2>ether 4</h2>
    <div id="chart_div_ether4" style="width: 400px; height: 120px;"></div>
    <h2>ether 5</h2>
    <div id="chart_div_ether5" style="width: 400px; height: 120px;"></div>
</body>
</html>
