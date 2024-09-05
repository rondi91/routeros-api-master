<?php
// File: ping_client.php

require('../koneksi.php');
$API = new RouterosAPI();
$API->debug = false;

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];

    if ($API->connect($ip,$user,$pass)) {
        // Lakukan ping menggunakan API Mikrotik
        $ping_result = $API->comm("/ping", array(
            "address" => $ip,
            "count" => 4
        ));

        if (!empty($ping_result) && isset($ping_result[0]['time'])) {
            $ping_time = $ping_result[0]['time'];
            echo json_encode([
                'status' => 'success',
                'time' => $ping_time
            ]);
        } else {
            echo json_encode([
                'status' => 'failed'
            ]);
        }

        $API->disconnect();
    }
}
?>
