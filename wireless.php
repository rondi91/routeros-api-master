<?php
// Load routers from JSON
$jsonFile = 'routers.json';

function getRouters() {
    global $jsonFile;
    $data = file_get_contents($jsonFile);
    return json_decode($data, true);
}

$routers = getRouters();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wireless Registration</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container">
    <h2>Wireless Registration Table</h2>

    <!-- Dropdown untuk memilih router -->
    <div class="form-group">
        <label for="routerSelect">Select Router:</label>
        <select class="form-control" id="routerSelect">
            <option value="">-- Select Router --</option>
            <?php foreach ($routers as $router): ?>
                <option value="<?= $router['id']; ?>"><?= $router['name']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Tempat menampilkan data wireless registration -->
    <div id="wirelessData">
        <p>Please select a router to view wireless registration data.</p>
    </div>
</div>

<!-- Bootstrap JS dan Popper -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Fungsi untuk mengupdate tabel wireless registration setiap 5 detik
    let interval;
    
    function fetchWirelessData(routerId) {
        $.ajax({
            url: 'get_wireless_data.php',
            type: 'POST',
            data: { router_id: routerId },
            success: function(response) {
                $('#wirelessData').html(response);
            },
            error: function() {
                $('#wirelessData').html('<p>Error fetching data.</p>');
            }
        });
    }

    // Ketika router dipilih
    $('#routerSelect').on('change', function() {
        var routerId = $(this).val();
        if (routerId) {
            // Hentikan interval lama jika ada
            if (interval) {
                clearInterval(interval);
            }
            // Jalankan fetchWirelessData pertama kali
            fetchWirelessData(routerId);
            // Jalankan AJAX secara berkala setiap 5 detik
            interval = setInterval(function() {
                fetchWirelessData(routerId);
            }, 1000); // Update setiap 5 detik
        } else {
            $('#wirelessData').html('<p>Please select a router to view wireless registration data.</p>');
            // Hentikan update jika tidak ada router yang dipilih
            clearInterval(interval);
        }
    });
</script>

</body>
</html>
