<?php
require('koneksi.php');

$API = new RouterosAPI();



$message = '';

if ($API->connect($ip,$user,$pass)) {
    // Jika form di-submit untuk membuat user baru
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $profile = $_POST['profile'];
        $service = $_POST['service'];

        // Buat PPPoE Secret baru
        $API->write('/ppp/secret/add', false);
        $API->write('=name=' . $name, false);
        $API->write('=password=' . $password, false);
        $API->write('=profile=' . $profile, false);
        $API->write('=service=' . $service);
        $response = $API->read();

        if (isset($response['!trap'])) {
            $message = 'Error: ' . $response['!trap'][0]['message'];
        } else {
            $message = 'User PPPoE berhasil dibuat.';
        }
    }

    // Ambil semua profile PPPoE untuk dropdown
    $API->write('/ppp/profile/print');
    $profiles = $API->read();

    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create PPPoE User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Create PPPoE User</h1>

        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="form-group">
                <label for="name">Username</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="service">Service</label>
                <select class="form-control" id="service" name="service" required>
                    <option value="pppoe">pppoe</option>
                    <!-- Tambahkan opsi layanan lain jika diperlukan -->
                </select>
            </div>
            <div class="form-group">
                <label for="profile">Profile</label>
                <select class="form-control" id="profile" name="profile" required>
                    <?php foreach ($profiles as $profile): ?>
                        <option value="<?php echo $profile['name']; ?>"><?php echo $profile['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
