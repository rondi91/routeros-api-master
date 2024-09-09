<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Router Client - Real-time</title>
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
            text-align: center;
        }
        .ping-status {
            font-weight: bold;
        }
    </style>
    <script>
        // Fungsi untuk melakukan ping IP secara real-time
        function pingIPRealTime(ip) {
            let pingStatusElement = document.getElementById('ping-status-' + ip);
            pingStatusElement.textContent = 'Pinging...';
            pingStatusElement.style.color = 'blue';

            // Lakukan ping ke server menggunakan AJAX
            fetch('ping_clients.php?ip=' + ip)
                .then(response => response.body.getReader())
                .then(reader => {
                    // Baca data secara stream untuk real-time updates
                    let decoder = new TextDecoder();
                    function processPing() {
                        return reader.read().then(({ done, value }) => {
                            if (done) {
                                return;
                            }
                            let text = decoder.decode(value);
                            let pingResult = JSON.parse(text.trim());

                            // Update status ping
                            if (pingResult.status == 'success') {
                                pingStatusElement.textContent = 'Ping success (' + pingResult.time + ' ms)';
                                pingStatusElement.style.color = 'green';
                            } else {
                                pingStatusElement.textContent = 'Ping failed';
                                pingStatusElement.style.color = 'red';
                            }
                            return processPing(); // Recursively call to process further pings
                        });
                    }
                    return processPing();
                })
                .catch(error => {
                    console.error('Error:', error);
                    pingStatusElement.textContent = 'Ping failed (Error)';
                    pingStatusElement.style.color = 'red';
                });
        }
    </script>
</head>
<body>

    <h2>Ping Router Client - Real-time</h2>

    <!-- Tabel untuk daftar IP client -->
    <h3>Client IP List</h3>
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Ping</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil daftar IP client dari database atau file
            $clients = file('clients.txt', FILE_IGNORE_NEW_LINES);
            foreach ($clients as $client_ip): ?>
            <tr>
                    <td><?= $client_ip ?></td>
                    <td><button type="button" onclick="pingIPRealTime('<?= $client_ip ?>')">Ping</button></td>
                    <td id="ping-status-<?= $client_ip ?>" class="ping-status">N/A</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
