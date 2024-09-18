<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Wireless</title>
    <link rel="stylesheet" href="styles.css"> <!-- Tambahkan CSS untuk gaya -->
</head>
<body>

<div id="scanContainer">
    <!-- Select Router -->
    <label for="routerSelect">Pilih Router:</label>
    <select id="routerSelect">
        <option value="">Pilih router</option>
    </select>

    <!-- Tombol Scan -->
    <button id="scanButton" onclick="startScan()">Scan</button>

    <!-- Countdown Timer -->
    <div id="scanStatus">
        <p>Waktu tersisa: <span id="countdown">0</span> detik</p>
    </div>

    <!-- Hasil Scan -->
    <div id="scanResult">
        <h3>Hasil Scan:</h3>
        <pre id="resultOutput">Tidak ada hasil.</pre>
    </div>
</div>


<script src="scan.js"></script> <!-- Tambahkan file JavaScript -->
</body>
</html>
