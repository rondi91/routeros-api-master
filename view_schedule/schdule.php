<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule PPPoE Profile Change - Mikrotik</title>
</head>
<body>

    <h2>Create Schedule for PPPoE Profile Change</h2>

    <form method="POST" action="create_schedule.php">
        <label for="pppoe_user">Select PPPoE User:</label>
        <select id="pppoe_user" name="pppoe_user" required>
            <?php
            require('../koneksi.php');

            $API = new RouterosAPI();
            
            
            if ($API->connect($ip,$user,$pass)) {
                // Fetch PPPoE secrets
                $pppoe_secrets = $API->comm("/ppp/secret/print");
                foreach ($pppoe_secrets as $secret) {
                    echo "<option value=\"{$secret['name']}\">{$secret['name']}</option>";
                }
                $API->disconnect();
            }
            ?>
        </select><br><br>

        <label for="new_profile">Select New Profile:</label>
        <select id="new_profile" name="new_profile" required>
            <?php
            if ($API->connect($ip,$user,$pass)) {
                // Fetch profiles for PPPoE
                $pppoe_profiles = $API->comm("/ppp/profile/print");
                foreach ($pppoe_profiles as $profile) {
                    echo "<option value=\"{$profile['name']}\">{$profile['name']}</option>";
                }
                $API->disconnect();
            }
            ?>
        </select><br><br>

        <label for="schedule_time">Schedule Time (HH:MM):</label>
        <input type="time" id="schedule_time" name="schedule_time" required><br><br>

        <button type="submit">Create Schedule</button>
    </form>

</body>
</html>
