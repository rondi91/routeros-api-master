<?php

require('koneksi.php');

$API = new RouterosAPI();

$API->debug = false;

if ($API->connect($ip,$user,$pass))  {
    if (isset($_POST['selected_secrets']) && isset($_POST['new_profile'])) {
        $selected_secrets = $_POST['selected_secrets']; // Secret yang dipilih
        $new_profile = $_POST['new_profile']; // Profile baru yang dipilih

        foreach ($selected_secrets as $secret_name) {
            // Mengubah profile untuk setiap secret yang dipilih
            $API->comm("/ppp/secret/set", array(
                "numbers" => $secret_name,
                "profile" => $new_profile
            ));
            
        }
        
        echo "Profile updated successfully.";
    } else {
        echo "No secrets selected or no profile chosen.";
    }

    $API->disconnect();
} else {
    echo "Unable to connect to the Mikrotik router.";
}
?>
