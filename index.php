<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MikroTik Management</title>
    <!-- Link ke Bootstrap dan FontAwesome -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- Link ke file CSS terpisah -->
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
        <h2>Dashboard MikroTik</h2>
        <p>Selamat datang di dashboard manajemen MikroTik.</p>

        <!-- Tambahkan konten dashboard di sini -->
        <div class="card-deck">
            <div class="card bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">PPPoE Secrets</div>
                <div class="card-body">
                    <h5 class="card-title">Manage Secrets</h5>
                    <p class="card-text">Lihat dan kelola user PPPoE yang aktif atau non-aktif.</p>
                    <a href="pppoe_secrets.php" class="btn btn-primary">Go to PPPoE Secrets</a>
                </div>
            </div>
            <div class="card bg-light mb-3" style="max-width: 18rem;">
                <div class="card-header">Network Monitoring</div>
                <div class="card-body">
                    <h5 class="card-title">Monitor Traffic</h5>
                    <p class="card-text">Pantau trafik jaringan real-time melalui interface ethernet.</p>
                    <a href="network_monitor.php" class="btn btn-primary">Go to Monitor</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS dan Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Link ke file JavaScript terpisah -->
    <script src="scripts.js"></script>
</body>
</html>
