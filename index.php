<?php
// 1. The URL of the API we are going to request
$url = "https://nominatim.openstreetmap.org/search?format=json&q=Roermond";

// 2. Start a cURL session to fetch the data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// The API requires a 'User-Agent'; otherwise it will reject the request
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: PHP-Map-App/1.0'
]);

// Execute the request and store the result
$response = curl_exec($ch);
curl_close($ch);

// 3. Convert the received JSON text into a PHP array
$data = json_decode($response, true);

// Default coordinates in case the API returns no data
$lat = 51.1941;
$lon = 5.9870;
$displayName = "Roermond, Nederland";

// If the API returns data, use the first result from the list
if (!empty($data) && isset($data[0])) {
    $lat = $data[0]['lat'];
    $lon = $data[0]['lon'];
    $displayName = $data[0]['display_name'];
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Nominatim Kaart</title>
    <!-- Leaflet CSS stylesheet for the map styling -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        #map { height: 450px; width: 100%; margin-top: 20px; border-radius: 8px; }
    </style>
</head>
<body>

    <h1>Locatie van Roermond via API</h1>
    <p>Gevonden: <strong><?php echo htmlspecialchars($displayName); ?></strong></p>

    <!-- This block will render the map on your website -->
    <div id="map"></div>

    <!-- Leaflet JavaScript library for map functionality -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Pass the PHP coordinates to JavaScript variables
        var lat = <?php echo json_encode($lat); ?>;
        var lon = <?php echo json_encode($lon); ?>;
        var displayName = <?php echo json_encode($displayName); ?>;

        // Create the map and center it on the correct coordinates
        var map = L.map('map').setView([lat, lon], 13);

        // Load the OpenStreetMap base map
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Place a marker on the map at the correct location
        var marker = L.marker([lat, lon]).addTo(map);
        marker.bindPopup("<b>Roermond</b><br>" + displayName).openPopup();
    </script>

</body>
</html>