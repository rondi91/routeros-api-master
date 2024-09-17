<?php
require('koneksi.php');

$API = new RouterosAPI();

$interfacesData = [];

if ($API->connect($ip,$user,$pass)) {
    // Ambil data untuk beberapa interface Ethernet
    $interfaces = ['ether2'];

    foreach ($interfaces as $interface) {
        $API->write('/interface/monitor-traffic', false);
        $API->write('=interface=' . $interface, false);
        $API->write('=once=', true);
        $read = $API->read();

        print_r($read);
        
        if (isset($read[0])) {
            $rx_bps = isset($read[0]['rx-bits-per-second']) ? $read[0]['rx-bits-per-second'] : 0;
            $tx_bps = isset($read[0]['tx-bits-per-second']) ? $read[0]['tx-bits-per-second'] : 0;
            // Konversi ke Mbps
            $interfacesData[$interface] = [
                'rx_mbps' => round($rx_bps / (1024 * 1024), 2),
                'tx_mbps' => round($tx_bps / (1024 * 1024), 2),
            ];
        } else {
            $interfacesData[$interface] = [
                'rx_mbps' => 0,
                'tx_mbps' => 0,
            ];
        }
    }

    $API->disconnect();
} else {
    die('Tidak dapat terhubung ke router Mikrotik.');
}

echo json_encode($interfacesData);
?>
