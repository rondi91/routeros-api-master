<?php
require_once 'koneksi.php';

// Fungsi untuk mendapatkan daftar router dari JSON
function getRouters() {
    $jsonFile = 'routers.json';
    $data = file_get_contents($jsonFile);
    return json_decode($data, true);
}

// Fungsi untuk mengambil data wireless dari router menggunakan API Mikrotik
function getWirelessRegistrations($ip, $username, $password) {
    $API = new RouterosAPI();

    if ($API->connect($ip, $username, $password)) {
        // Fetch wireless registration-table
        $registrations = $API->comm('/interface/wireless/registration-table/print');
        $API->disconnect();
        return $registrations;
    } else {
        return null;
    }
}

// Proses permintaan AJAX
if (isset($_POST['router_id'])) {
    $routerId = $_POST['router_id'];
    $routers = getRouters();

    // Cari router berdasarkan ID
    foreach ($routers as $router) {
        if ($router['id'] == $routerId) {
            $wirelessData = getWirelessRegistrations($router['ip_address'], $router['username'], $router['password']);
            
            // Jika data tersedia, buat tabel
            if ($wirelessData !== null) {
                echo '<table class="table table-bordered">';
                echo '<thead>
                        <tr>
                            
                            <th>Signal Strength</th>
                            <th>Radio name</th>
                            <th>TX/RX CCQ</th>
                            <th>TX Rate</th>
                            <th>RX Rate</th>
                        </tr>
                      </thead>';
                echo '<tbody>';
                // var_dump($wirelessData);
                foreach ($wirelessData as $client) {
                    echo '<tr>';
                    // echo '<td>' . $client['mac-address'] . '</td>';
                    // echo '<td>' . $client['interface'] . '</td>';
                    echo '<td>' . $client['signal-strength'] . '</td>';
                    echo '<td>' . $client['radio-name'] . '</td>';
                    echo '<td>' . $client['rx-ccq'] .'/'.$client['tx-ccq']. '</td>';
                    echo '<td>' . $client['tx-rate'] . '</td>';
                    echo '<td>' . $client['rx-rate'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody>';
                echo '</table>';
            } else {
                echo '<p>Error connecting to the router.</p>';
            }
            break;
        }
    }
}
?>
