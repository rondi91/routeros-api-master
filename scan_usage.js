// Function to load routers from JSON and populate the select element
function loadRouters() {
    // Fetch routers.json (replace with correct path)
    fetch('routers.json')
        .then(response => response.json())
        .then(data => {
            const routerSelect = document.getElementById('routerSelect');
            routerSelect.innerHTML = ''; // Clear existing options

            // Iterate through the JSON data and create options
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    const router = data[key];
                    const option = document.createElement('option');
                    option.value = router.ip_address; // Set the value to the IP address
                    option.textContent = `${router.name} (${router.ip_address})`; // Display name and IP address
                    routerSelect.appendChild(option); // Add option to select
                }
            }
        })
        .catch(error => console.error('Error fetching router data:', error));
}

// Call the function to load routers when the page loads
document.addEventListener('DOMContentLoaded', loadRouters);


// Function untuk memulai scan dan countdown timer
function startScan() {
    let selectedRouter = document.getElementById('routerSelect').value;
    if (!selectedRouter) {
        alert('Silakan pilih router terlebih dahulu!');
        return;
    }

    // Mulai scan untuk router yang dipilih
    let scanDuration = 30; // Misalnya durasi scan 30 detik
    startCountdown(scanDuration);

    // Jalankan scan dengan AJAX
    fetch('scan_usage.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'ip_address=' + encodeURIComponent(selectedRouter) // Kirim IP address router yang dipilih
    })
    .then(response => response.json())
    .then(data => {
        // Tampilkan hasil scan
        let resultOutput = document.getElementById('resultOutput');
        if (data.error) {
            resultOutput.textContent = 'Error: ' + data.error;
        } else {
            resultOutput.textContent = JSON.stringify(data, null, 2);
        }
    })
    .catch(error => {
        console.error('Error fetching scan results:', error);
    });
}


// Function untuk memulai countdown timer
function startCountdown(duration) {
    let timer = duration;
    let countdownElement = document.getElementById('countdown');

    const interval = setInterval(() => {
        countdownElement.textContent = timer;

        if (timer <= 0) {
            clearInterval(interval);
            document.getElementById('scanStatus').innerHTML = 'Proses scan selesai.';
        }

        timer--;
    }, 1000);
}

// Muat router setelah halaman selesai dimuat
document.addEventListener('DOMContentLoaded', loadRouters);
