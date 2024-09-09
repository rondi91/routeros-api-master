<?php
require('../koneksi.php');


$API = new RouterosAPI();
$API->debug = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($API->connect($ip,$user,$pass)) {

        // Hapus scheduler berdasarkan ID
        $response = $API->comm("/system/scheduler/remove", array(
            ".id" => $id
        ));

        if (!isset($response['!trap'])) {
            echo "Schedule deleted successfully.";
        } else {
            echo "Error deleting schedule: " . $response['!trap'][0]['message'];
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to Mikrotik.";
    }
}
?>
<a href="view_schedules.php">Back to schedule list</a>
