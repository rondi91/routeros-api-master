<?php
// File JSON atau database untuk menyimpan informasi router
$jsonFile = 'routers.json';

// Fungsi untuk mendapatkan daftar router dari JSON
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
     <!-- Sidebar -->
     <div class="sidebar" id="sidebar">
        <div class="sidebar-heading">Menu</div>
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="pppoe_secrets.php"><i class="fas fa-users"></i> PPPoE Secrets</a>
        <a href="network_monitor.php"><i class="fas fa-network-wired"></i> Network Monitor</a>
        <a href="pppoe_billing.php"><i class="fas fa-file-invoice-dollar"></i> Billing</a>
        <a href="schedule.php"><i class="fas fa-calendar-alt"></i> Scheduler</a>
        <a href="ping_test.php"><i class="fas fa-wifi"></i> Ping Test</a>
        <!-- Dropdown menu untuk Settings -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fas fa-cogs"></i> Settings</a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="router_settings.php">Router Settings</a>
                <a class="dropdown-item" href="user_management.php">User Management</a>
                <a class="dropdown-item" href="interface_settings.php">Interface Settings</a>
            </div>
        </div>
    </div>

    <!-- Tombol Collapse -->
    <span class="collapse-btn" id="collapseBtn"><i class="fas fa-bars"></i></span>

    <!-- Konten Utama -->
    <div class="content" id="content">


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
    // Ketika router dipilih, ambil data wireless registration via AJAX
    $('#routerSelect').on('change', function() {
        var routerId = $(this).val();
        if (routerId) {
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
        } else {
            $('#wirelessData').html('<p>Please select a router to view wireless registration data.</p>');
        }
    });
</script>

</body>
</html>
