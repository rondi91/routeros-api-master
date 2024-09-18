<?php
// Load the MikroTik RouterOS PHP API
require('koneksi.php');

// Cek jika ada IP Address yang diterima dari form
if (isset($_POST['ip_address']) && !empty($_POST['ip_address'])) {
    $ip_address = $_POST['ip_address'];

    // Router login details (replace with your credentials or get from JSON config)
    $username = 'rondi';  // Replace with your username
    $password = '21184662';  // Replace with your password
    $port = 8728;  // Default API port

    // Inisialisasi API Mikrotik
    $API = new RouterosAPI();
    $API->debug = false;

    // Coba untuk terhubung ke router dengan API Mikrotik
    if ($API->connect($ip_address, $username, $password, $port)) {
        
        // Menjalankan perintah scan pada interface wireless (pastikan untuk mengganti ".id" sesuai dengan interface wireless Anda)
        $scanResult = $API->comm("/interface/wireless/scan", array(
            ".id" => "*1", // Ganti dengan ID interface yang valid
            "duration" => 30 // Durasi scan, misalnya 30 detik
        ));

        // Jika hasil scan berhasil diambil, kirimkan kembali sebagai JSON
        if (!isset($scanResult['!trap'])) {
            // Kembalikan hasil scan sebagai JSON
            echo json_encode($scanResult);
        } else {
            // Jika terjadi error pada scan
            echo json_encode(array('error' => 'Scan failed: ' . $scanResult['!trap'][0]['message']));
        }

        // Disconnect API
        $API->disconnect();
    } else {
        // Jika gagal terhubung ke router
        echo json_encode(array('error' => 'Failed to connect to router: ' . $ip_address));
    }
} else {
    // Jika IP Address tidak dikirim
    echo json_encode(array('error' => 'No IP address provided.'));
}
?>
