<?php
// File: manage_clients.php

// File yang digunakan untuk menyimpan daftar IP client
$client_file = 'clients.txt';

// Tambah IP
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add' && isset($_POST['new_ip'])) {
        $new_ip = $_POST['new_ip'];
        // Cek apakah IP sudah ada dalam daftar
        $clients = file($client_file, FILE_IGNORE_NEW_LINES);
        if (!in_array($new_ip, $clients)) {
            // Tambahkan IP baru ke file
            file_put_contents($client_file, $new_ip . PHP_EOL, FILE_APPEND);
        }
    }

    // Hapus IP
    if ($_POST['action'] == 'delete' && isset($_POST['ip'])) {
        $ip_to_delete = $_POST['ip'];
        $clients = file($client_file, FILE_IGNORE_NEW_LINES);
        $clients = array_filter($clients, function($ip) use ($ip_to_delete) {
            return $ip != $ip_to_delete;
        });
        // Tulis ulang file tanpa IP yang dihapus
        file_put_contents($client_file, implode(PHP_EOL, $clients) . PHP_EOL);
    }

    // Redirect kembali ke halaman utama
    header("Location: ping_clients.php");
    exit();
}
?>
