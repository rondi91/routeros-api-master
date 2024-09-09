<?php
// File: ping_client.php

require('../koneksi.php');
$API = new RouterosAPI();
$API->debug = false;

if (isset($_GET['ip'])) {
    $ip = $_GET['ip'];

    if ($API->connect($ip,$user,$pass)) {
        // Lakukan ping menggunakan API Mikrotik
       // Hitung ping terus menerus selama 10 detik (atau sesuai keinginan)
       $ping_count = 0;

       // Lakukan ping menggunakan API Mikrotik
       while ($ping_count < 10) {
           $ping_result = $API->comm("/ping", array(
               "address" => $ip,
               "count" => 1 // Lakukan satu ping pada setiap iterasi
           ));

           // Kirim hasil ping dalam format JSON secara real-time
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

           // Flush output buffer agar bisa mengirim data secara langsung
           ob_flush();
           flush();
           
           // Delay selama 1 detik sebelum ping berikutnya
           sleep(1);
           $ping_count++;
       }

       $API->disconnect();
   }
}
?>
