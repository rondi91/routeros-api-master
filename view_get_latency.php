<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latency Monitor</title>
    <style>
        #latencyChart {
            max-width: 600px;
            margin: 50px auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;

        function fetchLatency() {
            fetch('get_latency.php')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    updateChart(data);
                })
                .catch(error => {
                    console.error('Error fetching latency data:', error);
                });
        }

        function updateChart(data) {
            const labels = Object.keys(data);
            const latencies = Object.values(data).map(latency => latency.toFixed(2));

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = latencies;
                chart.update();
            } else {
                const ctx = document.getElementById('latencyChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Latency (ms)',
                            data: latencies,
                            backgroundColor: latencies.map(latency => latency < 100 ? 'green' : 'red')
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Latency (ms)'
                                }
                            }
                        }
                    }
                });
            }
        }

        setInterval(fetchLatency, 5000); // Fetch data every 5 seconds
    </script>
</head>
<body>
    <h1 style="text-align: center;">Latency Monitor</h1>
    <canvas id="latencyChart"></canvas>
</body>
</html>
