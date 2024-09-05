<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latency Monitor</title>
    <style>
        #latencyChart {
            max-width: 1000px;
            max-height: 600px;
            width: 100%;
            height: auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let chart;
        let timestamps = [];
        let latencyData = {
            google: [],
            youtube: [],
            facebook: []
        };

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
            const now = new Date().toLocaleTimeString();

            timestamps.push(now);
            if (timestamps.length > 20) {
                timestamps.shift(); // Keep only the last 20 timestamps
            }

            latencyData.google.push(data['8.8.8.8']);
            latencyData.youtube.push(data['youtube.com']);
            latencyData.facebook.push(data['facebook.com']);

            if (latencyData.google.length > 20) {
                latencyData.google.shift();
                latencyData.youtube.shift();
                latencyData.facebook.shift();
            }

            if (chart) {
                chart.update();
            } else {
                const ctx = document.getElementById('latencyChart').getContext('2d');
                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: timestamps,
                        datasets: [
                            {
                                label: 'Google (8.8.8.8)',
                                data: latencyData.google,
                                borderColor: 'red',
                                fill: false
                            },
                            {
                                label: 'YouTube',
                                data: latencyData.youtube,
                                borderColor: 'blue',
                                fill: false
                            },
                            {
                                label: 'Facebook',
                                data: latencyData.facebook,
                                borderColor: 'green',
                                fill: false
                            }
                        ]
                    },
                    options: {
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Time'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Latency (ms)'
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            }
                        }
                    }
                });
            }
        }

        setInterval(fetchLatency, 1000); // Fetch data every 5 seconds
    </script>
</head>
<body>
    <h1 style="text-align: center;">Latency Monitor</h1>
    <canvas id="latencyChart"></canvas>
</body>
</html>
