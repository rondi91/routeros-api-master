<?php
require('../koneksi.php');

$API = new RouterosAPI();
$API->debug = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $start_time = $_POST['start_time'];
    $interval = $_POST['interval'];
    $on_event = $_POST['on_event'];
    $disabled = $_POST['disabled'];

    if ($API->connect($ip,$user,$pass)) {

        // Update scheduler berdasarkan ID
        $response = $API->comm("/system/scheduler/set", array(
            ".id" => $id,
            "name" => $name,
            "start-time" => $start_time,
            "interval" => $interval,
            "on-event" => $on_event,
            "disabled" => $disabled
        ));

        if (!isset($response['!trap'])) {
            echo "Schedule updated successfully.";
        } else {
            echo "Error updating schedule: " . $response['!trap'][0]['message'];
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to Mikrotik.";
    }
}
?>
<a href="view_schedules.php">Back to schedule list</a>
