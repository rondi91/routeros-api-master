<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latency Monitor</title>
    <style>
        table {
            width: 50%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .low-latency {
            color: green;
        }
        .high-latency {
            color: red;
        }
    </style>
    <script>
        function fetchLatency() {
            fetch('get_latency.php')
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    updateTable(data);
                })
                .catch(error => {
                    console.error('Error fetching latency data:', error);
                });
        }

        function updateTable(data) {
            const table = document.getElementById('latencyTable');
            table.innerHTML = ''; // Clear existing table rows

            for (const [site, latency] of Object.entries(data)) {
                const row = document.createElement('tr');
                const siteCell = document.createElement('td');
                const latencyCell = document.createElement('td');

                siteCell.textContent = site;
                latencyCell.textContent = latency.toFixed(2) + ' ms';

                // Apply color based on latency
                if (latency < 100) {
                    latencyCell.className = 'low-latency';
                } else {
                    latencyCell.className = 'high-latency';
                }

                row.appendChild(siteCell);
                row.appendChild(latencyCell);
                table.appendChild(row);
            }
        }

        setInterval(fetchLatency, 1000); // Fetch data every 5 seconds
    </script>
</head>
<body>
    <h1 style="text-align: center;">Latency Monitor</h1>
    <table id="latencyTable">
        <tr>
            <th>Site</th>
            <th>Latency</th>
        </tr>
    </table>
</body>
</html>