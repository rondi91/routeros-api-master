<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ping Router Client</title>
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
        function pingIP(ip) {
            // Function to send AJAX request to server to ping the IP
            fetch('ping_clients.php?ip=' + ip)
                .then(response => response.json())
                .then(data => {
                    let statusElement = document.getElementById('ping-status-' + ip);
                    if (data.status == 'success') {
                        statusElement.textContent = 'Ping success (' + data.time + ' ms)';
                        statusElement.style.color = 'green';
                    } else {
                        statusElement.textContent = 'Ping failed';
                        statusElement.style.color = 'red';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }
    </script>
</head>
<body>

    <h2>Ping Router Client</h2>

    <!-- Form untuk menambah IP client -->
    <form method="POST" action="manage_clients.php">
        <label for="new_ip">Add New Client IP:</label>
        <input type="text" id="new_ip" name="new_ip" placeholder="Enter IP address" required>
        <button type="submit" name="action" value="add">Add IP</button>
    </form>

    <!-- Tabel untuk daftar IP client -->
    <h3>Client IP List</h3>
    <table>
        <thead>
            <tr>
                <th>IP Address</th>
                <th>Ping</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Ambil daftar IP client dari database atau file
            $clients = file('clients.txt', FILE_IGNORE_NEW_LINES);
            foreach ($clients as $client_ip): ?>
            <tr>
                <td><?= $client_ip ?></td>
                <td><button type="button" onclick="pingIP('<?= $client_ip ?>')">Ping</button></td>
                <td id="ping-status-<?= $client_ip ?>" class="ping-status">N/A</td>
                <td>
                    <form method="POST" action="manage_clients.php" style="display:inline;">
                        <input type="hidden" name="ip" value="<?= $client_ip ?>">
                        <button type="submit" name="action" value="delete">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
