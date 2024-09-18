<?php
require('koneksi.php');

function getFrequencyUsage($ip, $username, $password, $interface) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        $API->write('/interface/wireless/frequency-monitor', false);
        $API->write('=number=0');
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
        if (isset($data['usage-percentage']) && isset($data['interference-percentage'])) {
            if ($data['usage-percentage'] < $lowestUsage && $data['interference-percentage'] < $lowestInterference) {
                $bestFrequency = $data['frequency'];
                $lowestUsage = $data['usage-percentage'];
                $lowestInterference = $data['interference-percentage'];
                $reason = "Frekuensi {$data['frequency']} MHz dipilih karena memiliki persentase penggunaan terendah ({$data['usage-percentage']}%) dan tingkat interferensi rendah ({$data['interference-percentage']}%).";
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
$ip = '192.168.88.1'; // IP Router
$username = 'admin';  // Username Router
$password = 'password';  // Password Router
$interface = 'wlan1';  // Interface yang akan diubah frekuensinya

// 1. Scan frequency usage
$frequencyUsage = getFrequencyUsage($ip, $username, $password, $interface);

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
