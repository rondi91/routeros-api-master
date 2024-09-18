<?php
require('koneksi.php');

function getFrequencyUsage($ip, $username, $password) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/frequency-monitor', false);
        $API->write('=number=0',false);
        $API->write('=duration=180');  // Durasi scan dalam detik
        $frequencyUsage = $API->read();
        $API->disconnect();
        return $frequencyUsage;
    } else {
        return ['error' => 'Tidak dapat terhubung ke RouterOS'];
    }
}

function findBestFrequencyWithReason($frequencyData) {
    $bestFrequency = null;
    $lowestUsage = 100;
    $lowestInterference = 100;
    $reason = "";

    foreach ($frequencyData as $data) {
        if (isset($data['use']) && isset($data['nf'])) {
            if ($data['use'] < $lowestUsage && $data['nf'] < $lowestInterference) {
                $bestFrequency = $data['freq'];
                $lowestUsage = $data['use'];
                $lowestInterference = $data['nf'];
                $reason = "Frekuensi {$data['freq']} MHz dipilih karena memiliki persentase penggunaan terendah ({$data['use']}%) dan tingkat interferensi rendah ({$data['nf']}%).";
            }
        }
    }

    return [
        'frequency' => $bestFrequency,
        'reason' => $reason
    ];
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
// var_dump($frequencyUsage);
// die();

// 2. Cari frekuensi terbaik dengan alasan
$bestFrequencyData = findBestFrequencyWithReason($frequencyUsage);

if ($bestFrequencyData['frequency']) {
    // 3. Ubah setting router
    $result = setFrequency($ip, $username, $password, $interface, $bestFrequencyData['frequency']);
    echo $result . "<br>";
    echo "Alasan: " . $bestFrequencyData['reason'];
} else {
    echo "Tidak ditemukan frekuensi terbaik.";
}
?>
