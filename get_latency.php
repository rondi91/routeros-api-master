<?php

require('koneksi.php');

$API = new RouterosAPI();


if ($API->connect($ip,$user,$pass)) {
    $targets = ['8.8.8.8', 'youtube.com', 'facebook.com','192.168.8.7']; // IP atau domain tujuan
    $results = [];

    foreach ($targets as $target) {
        $API->write('/ping', false);
        $API->write('=address=' . $target, false);
        $API->write('=count=5'); // Ping sebanyak 5 kali
        $response = $API->read();

        $latencies = [];
        foreach ($response as $r) {
            if (isset($r['time'])) {
                $latencies[] = (float)$r['time'];
            }
        }

        $average_latency = count($latencies) > 0 ? array_sum($latencies) / count($latencies) : 0;
        $results[$target] = $average_latency;
    }

    $API->disconnect();

    echo json_encode($results);
} else {
    echo json_encode(['error' => 'Could not connect to Mikrotik router']);
}
?>

