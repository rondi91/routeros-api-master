<?php

require('../koneksi.php');

$API = new RouterosAPI();
$API->debug = false;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    if ($API->connect($ip,$user,$pass)) {

        // Ambil data schedule berdasarkan ID
        $schedule = $API->comm("/system/scheduler/print", array(
            "?.id" => $id
        ));

        if (!empty($schedule)) {
            $schedule = $schedule[0]; // Hanya satu jadwal yang akan diambil
        } else {
            echo "Schedule not found.";
            exit();
        }

        $API->disconnect();
    } else {
        echo "Failed to connect to Mikrotik.";
        exit();
    }
} else {
    echo "No schedule ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mikrotik Schedule</title>
</head>
<body>

<h2>Edit Schedule</h2>

<form method="POST" action="save_schedule.php">
    <input type="hidden" name="id" value="<?= $schedule['.id'] ?>">

    <label for="name">Name:</label><br>
    <input type="text" id="name" name="name" value="<?= $schedule['name'] ?>"><br><br>

    <label for="start_time">Start Time (HH:MM:SS):</label><br>
    <input type="text" id="start_time" name="start_time" value="<?= $schedule['start-time'] ?>"><br><br>

    <label for="interval">Interval:</label><br>
    <input type="text" id="interval" name="interval" value="<?= $schedule['interval'] ?>"><br><br>

    <label for="on_event">On Event:</label><br>
    <textarea id="on_event" name="on_event"><?= htmlspecialchars($schedule['on-event']) ?></textarea><br><br>

    <label for="disabled">Disabled:</label><br>
    <select id="disabled" name="disabled">
        <option value="false" <?= $schedule['disabled'] == 'false' ? 'selected' : '' ?>>No</option>
        <option value="true" <?= $schedule['disabled'] == 'true' ? 'selected' : '' ?>>Yes</option>
    </select><br><br>

    <button type="submit">Save Changes</button>
</form>

</body>
</html>
