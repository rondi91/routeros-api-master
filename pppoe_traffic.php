<?php

// Aktifkan error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);


require('koneksi.php');
$API = new RouterosAPI();

$API->debug = false; // Aktifkan debug jika diperlukan

if (isset($_GET['name'])) {
    $user_name = $_GET['name'];

    if ($API->connect($ip,$user,$pass)) {

        // Ambil semua interface
        $interfaces = $API->comm("/interface/print");

        // Cari interface yang sesuai dengan nama user PPPoE active
        $matching_interface = null;
        foreach ($interfaces as $interface) {
            if (isset($interface['name']) && strpos($interface['name'], $user_name) !== false) {
                // Jika nama interface cocok dengan nama PPPoE user
                $matching_interface = $interface['name'];
                break;
            }
        }

        if ($matching_interface) {
            // Ambil trafik RX dan TX dari interface yang ditemukan
            $traffic_data = $API->comm("/interface/monitor-traffic", array(
                "interface" => $matching_interface,
                "once" => ""
            ));

            if (!empty($traffic_data)) {
                $traffic_data = $traffic_data[0]; // Ambil data trafik
                $rx_bytes = $traffic_data['rx-bits-per-second']; // RX dalam bits per detik
                $tx_bytes = $traffic_data['tx-bits-per-second']; // TX dalam bits per detik

                // Konversi dari bits per detik ke megabits per detik (Mbps)
                $rx_mbps = round($rx_bytes / (1024 * 1024), 2);
                $tx_mbps = round($tx_bytes / (1024 * 1024), 2);

                // Kirim hasil sebagai JSON
                echo json_encode(array(
                    "rx" => $rx_mbps,
                    "tx" => $tx_mbps
                ));
            } else {
                echo json_encode(array(
                    "error" => "Traffic data not available"
                ));
            }
        } else {
            // Jika tidak ada interface yang cocok
            echo json_encode(array(
                "error" => "No matching interface found for user"
            ));
        }

        $API->disconnect();
    } else {
        echo json_encode(array(
            "error" => "Failed to connect to Mikrotik"
        ));
    }
} else {
    echo json_encode(array(
        "error" => "No user specified"
    ));
}
?>
