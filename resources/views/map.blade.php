<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Costume Map with Leaflet</title>

    <link rel="stylesheet" href="{{ asset('/') }}leaflet-1.9.4/leaflet.css">
    <script src="{{ asset('/') }}leaflet-1.9.4/leaflet.js"></script>
    <link rel="stylesheet" href="{{ asset('/') }}leaflet-1.9.4/draw/leaflet-draw.css">
    <script src="{{ asset('/') }}leaflet-1.9.4/draw/leaflet-draw.js"></script>
</head>

<body>
    <div>
        <h1>Costume Map with Leaflet</h1>

        <br>
        <div id="map" style="width: 650px; height: 350px;"></div>

        <br>
        <textarea name="coordinate" id="coordinate" cols="30" rows="5"></textarea>
    </div>

    <script src="{{ asset('/') }}jquery/jquery-3.7.1.js"></script>

    <script>
        let startlat = '-6.175408';
        let startlng = '106.827153'

        function initMap(startlat, startlng) {
            const map = L.map('map').setView([startlat, startlng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'Map data © <a href="https://www.openstreetmap.org">OpenStreetMap</a> contributors'
            }).addTo(map);

            const drawnItems = new L.FeatureGroup().addTo(map);

            new L.Control.Draw({
                edit: {
                    featureGroup: drawnItems
                },
                draw: {
                    marker: true,
                    polyline: true,
                    polygon: true,
                    circlemarker: false,
                    circle: false,
                    rectangle: false
                }
            }).addTo(map);

            let currentMarker = null;
            let currentPolygon = null;
            let currentPolyline = null;

            function updateCoordinate(layer) {
                if (layer instanceof L.Marker) {
                    const {
                        lat,
                        lng
                    } = layer.getLatLng();
                    document.getElementById('coordinate').value = `${lat},${lng}`;
                    layer.bindPopup(`Latitude: ${lat}<br>Longitude: ${lng}`).openPopup();
                } else if (layer instanceof L.Polygon) {
                    const latlngs = layer.getLatLngs()[0];
                    ''
                    if (latlngs.length > 0) {
                        const coords = latlngs.map(latlng => `${latlng.lat},${latlng.lng}`).join(' | ');
                        document.getElementById('coordinate').value = coords;
                        layer.bindPopup(`Polygon<br>Coordinates: ${coords}`).openPopup();
                    } else {
                        document.getElementById('coordinate').value = 'Tidak ada koordinat tersedia';
                        layer.bindPopup(`Polygon<br>Coordinates: Tidak ada koordinat tersedia`).openPopup();
                    }
                } else if (layer instanceof L.Polyline) {
                    const latlngs = layer.getLatLngs();
                    if (latlngs.length > 0) {
                        const coords = latlngs.map(latlng => `${latlng.lat},${latlng.lng}`).join(' | ');
                        document.getElementById('coordinate').value = coords;
                        layer.bindPopup(`Polyline<br>Coordinates: ${coords}`).openPopup();
                    } else {
                        document.getElementById('coordinate').value = 'Tidak ada koordinat tersedia';
                        layer.bindPopup(`Polyline<br>Coordinates: Tidak ada koordinat tersedia`).openPopup();
                    }
                }
            }

            map.on(L.Draw.Event.CREATED, (event) => {
                const {
                    layer
                } = event;

                drawnItems.clearLayers();

                drawnItems.addLayer(layer);
                updateCoordinate(layer);

                if (layer instanceof L.Marker) {
                    currentMarker = layer;
                } else if (layer instanceof L.Polyline) {
                    currentPolyline = layer;
                } else if (layer instanceof L.Polygon) {
                    currentPolygon = layer;
                }
            });

            map.on(L.Draw.Event.EDITED, (event) => {
                event.layers.eachLayer(updateCoordinate);
            });

            map.on(L.Draw.Event.DELETED, (event) => {
                event.layers.eachLayer((layer) => {
                    if (layer === currentMarker) {
                        document.getElementById('coordinate').value = '';
                        currentMarker = null;
                    } else if (layer === currentPolyline) {
                        document.getElementById('coordinate').value = '';
                        currentPolyline = null;
                    } else if (layer === currentPolygon) {
                        document.getElementById('coordinate').value = '';
                        currentPolygon = null;
                    }
                });
            });
        }

        initMap(startlat, startlng);
    </script>

</body>

</html>
