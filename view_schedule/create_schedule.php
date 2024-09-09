<?php

require('../koneksi.php');

$API = new RouterosAPI();
$API->debug = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pppoe_user = $_POST['pppoe_user'];
    $new_profile = $_POST['new_profile'];
    $schedule_time = $_POST['schedule_time'];

 
    // Tambahkan detik jika hanya format HH:MM diberikan
    if (strlen($schedule_time) == 5) {
        $schedule_time .= ":00"; // Tambahkan detik (00) untuk format HH:MM menjadi HH:MM:SS
    }

    if ($API->connect($ip,$user,$pass)) {

         // Nama scheduler yang akan ditambahkan
         $scheduler_name = "$pppoe_user";

         // Cek apakah scheduler dengan nama ini sudah ada
         $existing_schedulers = $API->comm("/system/scheduler/print", array(
             "?name" => $scheduler_name
         ));
       
 
         // Jika scheduler dengan nama ini sudah ada, hapus
         if (!empty($existing_schedulers)) {
             $scheduler_id = $existing_schedulers[0]['.id']; // Dapatkan ID scheduler yang ada
             $API->comm("/system/scheduler/remove", array(
                 ".id" => $scheduler_id
             ));
         }

         // Gunakan sprintf untuk format yang lebih bersih
         $on_event_command = sprintf(
            "/log info \"Changing PPPoE profile for %s to %s\"; ".
            "/ppp secret set [find name=\"%s\"] profile=\"%s\"; ".
            "/log info \"Removing active PPPoE connection for %s\"; ".
            "/ppp active remove [find name=\"%s\"]",
            $pppoe_user, $new_profile, $pppoe_user, $new_profile, $pppoe_user, $pppoe_user
        );

        // Buat schedule di Mikrotik
        $response = $API->comm("/system/scheduler/add", array(
            "name" => "$pppoe_user",
            "start-time" => $schedule_time,
            "on-event" => $on_event_command,
            "disabled" => "no"
        ));

        // Check apakah ada response error
        if (!isset($response['!trap'])) {
            echo "Schedule created successfully for $pppoe_user to change profile at $schedule_time.";
        } else {
            echo "Error creating schedule: " . $response['!trap'][0]['message'];
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to Mikrotik.";
    }
}
?>