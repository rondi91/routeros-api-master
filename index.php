<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MikroTik Management</title>
    <!-- Link ke Bootstrap untuk styling cepat -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css"> <!-- File CSS custom -->
    <style>
        /* Style untuk sidebar */
        body {
            font-family: "Arial", sans-serif;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            height: 100vh;
            width: 240px;
            position: fixed;
            background-color: #1f2937;
            padding-top: 20px;
            transition: 0.3s;
            overflow: hidden;
        }

        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 16px;
            color: #f1f1f1;
            display: block;
        }

        .sidebar a:hover {
            background-color: #374151;
            color: #fff;
        }

        .sidebar .sidebar-heading {
            padding: 10px;
            font-size: 18px;
            text-transform: uppercase;
            color: #d1d5db;
        }

        .sidebar.collapsed {
            width: 60px;
        }

        .content {
            margin-left: 240px;
            padding: 20px;
            transition: 0.3s;
        }

        .content.collapsed {
            margin-left: 60px;
        }

        /* Style untuk tombol expand/collapse */
        .collapse-btn {
            position: fixed;
            top: 15px;
            left: 250px;
            color: #f1f1f1;
            font-size: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .collapse-btn.collapsed {
            left: 70px;
        }

        /* Tambahkan sedikit style hover */
        .collapse-btn:hover {
            color: #818181;
        }

        /* Untuk mobile view */
        @media screen and (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            .content {
                margin-left: 60px;
            }
            .collapse-btn {
                left: 70px;
            }
        }
    </style>
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
        <a href="#"><i class="fas fa-cogs"></i> Settings</a>
    </div>

    <!-- Tombol untuk Collapse Sidebar -->
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
            <!-- Tambahkan card lainnya jika diperlukan -->
        </div>
    </div>

    <!-- Script untuk mengatur sidebar collapse -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const collapseBtn = document.getElementById('collapseBtn');

        collapseBtn.onclick = function () {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('collapsed');
            collapseBtn.classList.toggle('collapsed');
        }
    </script>

    <!-- Bootstrap JS dan Popper -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
