<?php
require('../koneksi.php');

$API = new RouterosAPI();

$API->debug = false; // Ubah ke true jika ingin melihat detail request API

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Mikrotik Schedules</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid black;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h2>Mikrotik Schedules</h2>

    <?php
    // Coba hubungkan ke Mikrotik
    if ($API->connect($ip,$user,$pass)){

        // Ambil semua schedule dari Mikrotik
        $schedules = $API->comm("/system/scheduler/print");

        if (!empty($schedules)) {
            // Tampilkan dalam tabel jika ada schedule
            echo '<table>';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Name</th>';
            echo '<th>Start Time</th>';
            echo '<th>Interval</th>';
            echo '<th>On Event</th>';
            echo '<th>Next Run</th>';
            echo '<th>Disabled</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            // Loop untuk setiap schedule dan tampilkan dalam tabel
            foreach ($schedules as $schedule) {
                echo '<tr>';
                echo '<td>' . (isset($schedule['name']) ? $schedule['name'] : 'N/A') . '</td>';
                echo '<td>' . (isset($schedule['start-time']) ? $schedule['start-time'] : 'N/A') . '</td>';
                echo '<td>' . (isset($schedule['interval']) ? $schedule['interval'] : 'N/A') . '</td>';
                echo '<td>' . (isset($schedule['on-event']) ? htmlspecialchars($schedule['on-event']) : 'N/A') . '</td>';
                echo '<td>' . (isset($schedule['next-run']) ? $schedule['next-run'] : 'N/A') . '</td>';
                echo '<td>' . (isset($schedule['disabled']) && $schedule['disabled'] == 'true' ? 'Yes' : 'No') . '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        } else {
            echo "<p>No schedules found on Mikrotik.</p>";
        }

        $API->disconnect(); // Putuskan koneksi ke Mikrotik
    } else {
        echo "<p>Failed to connect to Mikrotik.</p>";
    }
    ?>

</body>
</html>
