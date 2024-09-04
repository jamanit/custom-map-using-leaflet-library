<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Costume Map using Leaflet Library</title>

    <link rel="stylesheet" href="{{ asset('/') }}leaflet-1.9.4/leaflet.css">
    <script src="{{ asset('/') }}leaflet-1.9.4/leaflet.js"></script>
    <link rel="stylesheet" href="{{ asset('/') }}leaflet-1.9.4/draw/leaflet-draw.css">
    <script src="{{ asset('/') }}leaflet-1.9.4/draw/leaflet-draw.js"></script>
</head>

<body>
    <div>
        <h1>Costume Map using Leaflet Library</h1>

        <br>
        <div id="map" style="width: 650px; height: 350px;"></div>

        <br>
        <textarea name="coordinate" id="coordinate" cols="30" rows="5"></textarea>
    </div>

    <script src="{{ asset('/') }}jquery/jquery-3.7.1.js"></script>

 <script>
    // Mendefinisikan variabel latitude dan longitude untuk lokasi awal peta
    let startlat = '-6.175408';
    let startlng = '106.827153';

    // Fungsi untuk menginisialisasi peta dengan latitude dan longitude awal
    function initMap(startlat, startlng) {
        // Membuat objek peta dengan ID 'map', diatur untuk menampilkan lokasi awal pada zoom level 15
        const map = L.map('map').setView([startlat, startlng], 15);

        // Menambahkan lapisan peta dari OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data © <a href="https://www.openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Membuat grup fitur untuk menampung item yang digambar dan menambahkannya ke peta
        const drawnItems = new L.FeatureGroup().addTo(map);

        // Menambahkan kontrol gambar ke peta (untuk menggambar marker, polyline, dan polygon)
        new L.Control.Draw({
            edit: {
                featureGroup: drawnItems // Mengatur grup fitur yang bisa diedit
            },
            draw: {
                marker: true, // Mengizinkan penggambaran marker
                polyline: true, // Mengizinkan penggambaran polyline
                polygon: true, // Mengizinkan penggambaran polygon
                circlemarker: false, // Tidak mengizinkan penggambaran circlemarker
                circle: false, // Tidak mengizinkan penggambaran circle
                rectangle: false // Tidak mengizinkan penggambaran rectangle
            }
        }).addTo(map);

        // Mendeklarasikan variabel untuk menyimpan item yang sedang dipilih
        let currentMarker = null;
        let currentPolygon = null;
        let currentPolyline = null;

        // Fungsi untuk memperbarui koordinat di elemen input dan popup peta
        function updateCoordinate(layer) {
            if (layer instanceof L.Marker) {
                // Jika layer adalah marker, ambil koordinatnya dan tampilkan di input dan popup
                const { lat, lng } = layer.getLatLng();
                document.getElementById('coordinate').value = `${lat},${lng}`;
                layer.bindPopup(`Latitude: ${lat}<br>Longitude: ${lng}`).openPopup();
            } else if (layer instanceof L.Polygon) {
                // Jika layer adalah polygon, ambil koordinatnya dan tampilkan di input dan popup
                const latlngs = layer.getLatLngs()[0];
                if (latlngs.length > 0) {
                    const coords = latlngs.map(latlng => `${latlng.lat},${latlng.lng}`).join(' | ');
                    document.getElementById('coordinate').value = coords;
                    layer.bindPopup(`Polygon<br>Coordinates: ${coords}`).openPopup();
                } else {
                    document.getElementById('coordinate').value = 'Tidak ada koordinat tersedia';
                    layer.bindPopup(`Polygon<br>Coordinates: Tidak ada koordinat tersedia`).openPopup();
                }
            } else if (layer instanceof L.Polyline) {
                // Jika layer adalah polyline, ambil koordinatnya dan tampilkan di input dan popup
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

        // Menambahkan event listener untuk saat item digambar di peta
        map.on(L.Draw.Event.CREATED, (event) => {
            const { layer } = event;

            // Menghapus semua layer yang digambar sebelumnya
            drawnItems.clearLayers();

            // Menambahkan layer baru yang digambar ke grup fitur
            drawnItems.addLayer(layer);
            // Memperbarui koordinat dari layer yang baru digambar
            updateCoordinate(layer);

            // Menyimpan layer yang baru digambar ke dalam variabel sesuai jenisnya
            if (layer instanceof L.Marker) {
                currentMarker = layer;
            } else if (layer instanceof L.Polyline) {
                currentPolyline = layer;
            } else if (layer instanceof L.Polygon) {
                currentPolygon = layer;
            }
        });

        // Menambahkan event listener untuk saat item yang digambar diedit
        map.on(L.Draw.Event.EDITED, (event) => {
            // Memperbarui koordinat untuk setiap layer yang diedit
            event.layers.eachLayer(updateCoordinate);
        });

        // Menambahkan event listener untuk saat item yang digambar dihapus
        map.on(L.Draw.Event.DELETED, (event) => {
            // Menghapus item yang dihapus dari peta dan memperbarui variabel yang sesuai
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

    // Memanggil fungsi initMap dengan latitude dan longitude awal
    initMap(startlat, startlng);
</script>

</body>

</html>
