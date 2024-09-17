<?php
require('koneksi.php');

function getFrequencyUsage($ip, $username, $password, $interface) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/frequency-monitor', false);  // Kirim perintah scan
        $API->write('=number=0', false);  // Ganti "wlan1" dengan interface yang valid
        $API->write('=duration=60');  // Durasi scan dalam detik
        $frequencyUsage = $API->read();
        $API->disconnect();
        return $frequencyUsage;
    } else {
        return ['error' => 'Tidak dapat terhubung ke RouterOS'];
    }
}

function findBestFrequency($frequencyData) {
    $bestFrequency = null;
    $lowestUsage = 100;

    foreach ($frequencyData as $data) {
        if (isset($data['use']) && isset($data['nf'])) {
            if ($data['use'] < $lowestUsage && $data['nf'] < $lowestUsage) {
                $bestFrequency = $data['freq'];
                $lowestUsage = $data['use'];
            }
        }
    }

    return $bestFrequency;
}

function setFrequency($ip, $username, $password, $interface, $newFrequency) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/set', false);
        $API->write('=numbers=' . $interface, false);
        $API->write('=frequency=' . $newFrequency);
        $API->read();
        $API->disconnect();

        return "Frekuensi berhasil diubah ke {$newFrequency} MHz";
    } else {
        return "Gagal terhubung ke router untuk mengubah frekuensi.";
    }
}

// Proses
$ip = '172.16.30.3'; // IP Router
$username = 'rondi';  // Username Router
$password = '21184662';  // Password Router
$interface = 'wlan1';  // Interface yang akan diubah frekuensinya

// 1. Scan frequency usage
$frequencyUsage = getFrequencyUsage($ip, $username, $password, $interface);

// 2. Cari frekuensi terbaik
$bestFrequency = findBestFrequency($frequencyUsage);
var_dump($bestFrequency);

// 3. Jika ditemukan frekuensi terbaik, ubah setting router
if ($bestFrequency) {
    $result = setFrequency($ip, $username, $password, $interface, $bestFrequency);
    echo $result;
} else {
    echo "Tidak ditemukan frekuensi terbaik.";
}
?>
