<?php
// File JSON yang digunakan untuk menyimpan data router
$jsonFile = 'routers.json';

// Fungsi untuk membaca data dari file JSON
function getRouters() {
    global $jsonFile;
    $data = file_get_contents($jsonFile);
    return json_decode($data, true); // Mengembalikan data dalam bentuk array asosiatif
}

// Fungsi untuk menyimpan data ke file JSON
function saveRouters($routers) {
    global $jsonFile;
    file_put_contents($jsonFile, json_encode($routers, JSON_PRETTY_PRINT));
}

// Ambil data router dari file JSON
$routers = getRouters();

// Proses penambahan router
if (isset($_POST['add_router'])) {
    $name = $_POST['name'];
    $ip_address = $_POST['ip_address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Tambahkan router baru ke array
    $newRouter = [
        "id" => end($routers)['id'] + 1, // Set ID berdasarkan ID terakhir
        "name" => $name,
        "ip_address" => $ip_address,
        "username" => $username,
        "password" => $password
    ];
    $routers[] = $newRouter;

    // Simpan kembali data ke JSON
    saveRouters($routers);
}

// Proses penghapusan router
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Filter array routers untuk menghapus router berdasarkan id
    $routers = array_filter($routers, function($router) use ($id) {
        return $router['id'] != $id;
    });

    // Simpan kembali data ke JSON
    saveRouters($routers);
}

// Proses pengeditan router
if (isset($_POST['edit_router'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $ip_address = $_POST['ip_address'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Edit router yang sesuai dengan ID
    foreach ($routers as &$router) {
        if ($router['id'] == $id) {
            $router['name'] = $name;
            $router['ip_address'] = $ip_address;
            $router['username'] = $username;
            $router['password'] = $password;
            break;
        }
    }

    // Simpan kembali data ke JSON
    saveRouters($routers);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Router Settings - JSON</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

 <!-- Sidebar -->
 <div class="sidebar" id="sidebar">
        <div class="sidebar-heading">Menu</div>
        <a href="#"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
         <!-- Menu baru untuk WIRELESS -->
        
        <a href="wireless.php"><i class="fas fa-users"></i> WIRELESS</a>
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

<div class="content" id="content">
    <h2>Router Settings (Using JSON)</h2>

    

    <!-- Form untuk menambahkan router baru -->
    <form method="POST" action="">
        <div class="form-group">
            <label for="name">Router Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Router Name" required>
        </div>
        <div class="form-group">
            <label for="ip_address">Router IP Address</label>
            <input type="text" class="form-control" id="ip_address" name="ip_address" placeholder="Enter Router IP" required>
        </div>
        <div class="form-group">
            <label for="username">Router Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Enter Router Username" required>
        </div>
        <div class="form-group">
            <label for="password">Router Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Router Password" required>
        </div>
        <button type="submit" class="btn btn-primary" name="add_router">Add Router</button>
    </form>

    <hr>

    <!-- Daftar router yang sudah ditambahkan -->
    <h3>List of Routers</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>IP Address</th>
                <th>Username</th>
                <th>Password</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($routers) > 0): ?>
                <?php $no = 1; foreach($routers as $router): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $router['name']; ?></td>
                        <td><?= $router['ip_address']; ?></td>
                        <td><?= $router['username']; ?></td>
                        <td><?= $router['password']; ?></td>
                        <td>
                            <!-- Tombol Edit -->
                            <button class="btn btn-warning btn-sm" onclick='editRouter(<?= json_encode($router); ?>)'>Edit</button>
                            <!-- Tombol Hapus -->
                            <a href="router_settings.php?delete=<?= $router['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this router?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No routers found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Edit Router -->
<div class="modal" tabindex="-1" role="dialog" id="editRouterModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Router</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id" id="editRouterId">
                    <div class="form-group">
                        <label for="editName">Router Name</label>
                        <input type="text" class="form-control" id="editName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="editIpAddress">Router IP Address</label>
                        <input type="text" class="form-control" id="editIpAddress" name="ip_address" required>
                    </div>
                    <div class="form-group">
                        <label for="editUsername">Router Username</label>
                        <input type="text" class="form-control" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Router Password</label>
                        <input type="password" class="form-control" id="editPassword" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="edit_router">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS dan Popper -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    // Function untuk mengisi data di modal edit router
    function editRouter(router) {
        $('#editRouterId').val(router.id);
        $('#editName').val(router.name);
        $('#editIpAddress').val(router.ip_address);
        $('#editUsername').val(router.username);
        $('#editPassword').val(router.password);
        $('#editRouterModal').modal('show');
    }
</script>

</body>
</html>
