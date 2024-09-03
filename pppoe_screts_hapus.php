<?php
require('koneksi.php');

$API = new RouterosAPI();

if ($API->connect($ip,$user,$pass)) {
    // Jika form di-submit untuk update profile
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['edit_profile'])) {
            $name = $_POST['name'];
            $newProfile = $_POST['profile'];

            // Hapus user dari active connections jika ada
            $API->write('/ppp/active/print', false);
            $API->write('?name=' . $name);
            $activeUser = $API->read();

            if (!empty($activeUser)) {
                $API->write('/ppp/active/remove', false);
                $API->write('=.id=' . $activeUser[0]['.id']);
                $API->read();
            }

            // Update profile user
            $API->write('/ppp/secret/set', false);
            $API->write('=.id=' . $name, false);
            $API->write('=profile=' . $newProfile);
            $API->read();
        } elseif (isset($_POST['delete_secret'])) {
            $name = $_POST['name'];

            // Hapus user dari active connections jika ada
            $API->write('/ppp/active/print', false);
            $API->write('?name=' . $name);
            $activeUser = $API->read();

            if (!empty($activeUser)) {
                $API->write('/ppp/active/remove', false);
                $API->write('=.id=' . $activeUser[0]['.id']);
                $API->read();
            }

            // Hapus secret user
            $API->write('/ppp/secret/print', false);
            $API->write('?name=' . $name);
            $secret = $API->read();

            if (!empty($secret)) {
                $API->write('/ppp/secret/remove', false);
                $API->write('=.id=' . $secret[0]['.id']);
                $API->read();
            }
        }
    }

    // Ambil semua PPPoE Secrets
    $API->write('/ppp/secret/print');
    $secrets = $API->read();

    // Ambil semua koneksi PPPoE aktif
    $API->write('/ppp/active/print');
    $activeConnections = $API->read();

    // Ambil semua profile PPPoE
    $API->write('/ppp/profile/print');
    $profiles = $API->read();

    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}

// Buat array untuk memeriksa status aktif
$activeUsers = [];
foreach ($activeConnections as $active) {
    $activeUsers[] = $active['name'];
}

// Inisialisasi counter untuk total, active, dan off secrets
$totalSecrets = count($secrets);
$totalActive = 0;
$totalOff = 0;

// Hitung jumlah yang active dan off
foreach ($secrets as $secret) {
    if (in_array($secret['name'], $activeUsers)) {
        $totalActive++;
    } else {
        $totalOff++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar PPPoE Secrets Mikrotik</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .off { color: red; }
        .sortable:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Daftar PPPoE Secrets Mikrotik</h1>
        
        <!-- Tambahkan informasi total secrets, active, dan off -->
        <div class="mb-3">
            <p>Total Secrets: <strong><?php echo $totalSecrets; ?></strong></p>
            <p>Total Active: <strong><?php echo $totalActive; ?></strong></p>
            <p>Total Off: <strong><?php echo $totalOff; ?></strong></p>
        </div>

        <!-- Tambahkan Input Pencarian -->
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by Name, Profile, or Status..." onkeyup="searchTable()">
        </div>

        <table class="table table-bordered" id="pppoeTable">
            <thead>
                <tr>
                    <th>No</th>
                    <th class="sortable" onclick="sortTable(1)">Name</th>
                    <th>Service</th>
                    <th class="sortable" onclick="sortTable(3)">Profile</th>
                    <th>Last Logged Out</th>
                    <th class="sortable" onclick="sortTable(5)">Status</th>
                    <th>Edit</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (isset($secrets)) {
                    $no = 1;
                    foreach ($secrets as $secret) {
                        $status = in_array($secret['name'], $activeUsers) ? 'active' : 'off';
                        $statusClass = $status == 'off' ? 'off' : '';
                        echo "<tr>";
                        echo "<td>{$no}</td>";
                        echo "<td>{$secret['name']}</td>";
                        echo "<td>{$secret['service']}</td>";
                        echo "<td>{$secret['profile']}</td>";
                        echo "<td>{$secret['last-logged-out']}</td>";
                        echo "<td class='{$statusClass}'>{$status}</td>";
                        echo "<td>
                                <button class='btn btn-primary btn-sm' onclick='editProfile(\"{$secret['name']}\", \"{$secret['profile']}\")'>Edit</button>
                              </td>";
                        echo "<td>
                                <button class='btn btn-danger btn-sm' onclick='confirmDelete(\"{$secret['name']}\")'>Delete</button>
                              </td>";
                        echo "</tr>";
                        $no++;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk Edit Profile -->
    <div class="modal" tabindex="-1" role="dialog" id="editProfileModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profile</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="name" id="editName">
                        <div class="form-group">
                            <label for="profile">Profile</label>
                            <select class="form-control" name="profile" id="editProfile">
                                <?php foreach ($profiles as $profile): ?>
                                    <option value="<?php echo $profile['name']; ?>"><?php echo $profile['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="edit_profile">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Form untuk Delete Secret -->
    <form method="post" id="deleteForm" style="display: none;">
        <input type="hidden" name="name" id="deleteName">
        <input type="hidden" name="delete_secret" value="1">
    </form>

    <script>
        // Fungsi Pencarian Tabel
        function searchTable() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.getElementById("pppoeTable");
            tr = table.getElementsByTagName("tr");

            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none"; // Sembunyikan semua baris
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = ""; // Tampilkan baris yang cocok
                            break;
                        }
                    }
                }
            }
        }

        // Fungsi Sortir Tabel
        function sortTable(n) {
            var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("pppoeTable");
            switching = true;
            dir = "asc";
            
            while (switching) {
                switching = false;
                rows = table.rows;
                
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    
                    if (dir == "asc") {
                        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    } else if (dir == "desc") {
                        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                            shouldSwitch = true;
                            break;
                        }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }

        function editProfile(name, profile) {
            document.getElementById('editName').value = name;
            document.getElementById('editProfile').value = profile;
            $('#editProfileModal').modal('show');
        }

        function confirmDelete(name) {
            if (confirm('Are you sure you want to delete this secret?')) {
                document.getElementById('deleteName').value = name;
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
