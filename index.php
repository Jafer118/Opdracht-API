<?php
// 1. De URL van de API die we gaan bezoeken
$url = "https://nominatim.openstreetmap.org/search?format=json&q=Roermond";

// 2. Start een cURL verbinding om de data op te halen
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

// De API eist een 'User-Agent', anders weigert hij de verbinding
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: PHP-Map-App/1.0'
]);

// Voer het verzoek uit en sla het resultaat op
$response = curl_exec($ch);
curl_close($ch);

// 3. Zet de ontvangen JSON-tekst om naar een PHP array (lijst)
$data = json_decode($response, true);

// Standaard coördinaten voor als er even niks terugkomt
$lat = 51.1941;
$lon = 5.9870;
$displayName = "Roermond, Nederland";

// Als de API data teruggeeft, pak dan de eerste locatie uit de lijst
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
    <!-- Leaflet CSS stylesheet voor de opmaak van de kaart -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        #map { height: 450px; width: 100%; margin-top: 20px; border-radius: 8px; }
    </style>
</head>
<body>

    <h1>Locatie van Roermond via API</h1>
    <p>Gevonden: <strong><?php echo htmlspecialchars($displayName); ?></strong></p>

    <!-- Dit blokje wordt de kaart op je website -->
    <div id="map"></div>

    <!-- Leaflet JavaScript bibliotheek voor de kaartfunctionaliteit -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Stop de PHP coördinaten in JavaScript variabelen
        var lat = <?php echo json_encode($lat); ?>;
        var lon = <?php echo json_encode($lon); ?>;
        var displayName = <?php echo json_encode($displayName); ?>;

        // Maak de kaart en zet hem op de juiste coördinaten
        var map = L.map('map').setView([lat, lon], 13);

        // Laad de achtergrondkaart van OpenStreetMap
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Zet een prikker (marker) op de kaart op de juiste plek
        var marker = L.marker([lat, lon]).addTo(map);
        marker.bindPopup("<b>Roermond</b><br>" + displayName).openPopup();
    </script>

</body>
</html>