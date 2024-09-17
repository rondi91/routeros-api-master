<?php 

require('koneksi.php');

$API = new RouterosAPI();
$ip = '172.16.30.3';
$user = 'rondi';
$pass = '21184662';



if ($API->connect($ip,$user,$pass)) {

// Menulis perintah scan
$API->write('/interface/wireless/frequency-monitor', false);  // Kirim perintah scan
$API->write('=number=0', false);  // Ganti "wlan1" dengan interface yang valid
$API->write('=duration=10');  // Durasi scan dalam detik

// Membaca hasil scan
$scanResult = $API->read();

// Menampilkan hasil scan
print_r($scanResult);

    


    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}
