<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrackMyBus | Live Bus Tracking</title>

    <link rel="stylesheet"
          href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f3f6fb;
            color: #172033;
        }

        header {
            background: #101d3a;
            color: white;
            padding: 20px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        header h1 {
            font-size: 25px;
        }

        header p {
            color: #b8c6df;
            margin-top: 5px;
            font-size: 13px;
        }

        .header-status {
            color: #6ee7b7;
            font-size: 13px;
        }

        main {
            max-width: 1400px;
            margin: auto;
            padding: 25px;
        }

        .search-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }

        .search-section h2 {
            margin-bottom: 15px;
            font-size: 19px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box input {
            flex: 1;
            min-width: 200px;
            padding: 13px;
            border: 1px solid #d8dfeb;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
        }

        .search-box input:focus {
            border-color: #2563eb;
        }

        button {
            border: none;
            border-radius: 8px;
            padding: 12px 18px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
        }

        .primary-btn {
            background: #2563eb;
            color: white;
        }

        .secondary-btn {
            background: #e8eef9;
            color: #17315d;
        }

        button:hover {
            opacity: .9;
        }

        .message {
            margin-top: 12px;
            font-size: 13px;
            color: #64748b;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .detail-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
        }

        .detail-card small {
            color: #718096;
            display: block;
            margin-bottom: 10px;
        }

        .detail-card h3 {
            font-size: 20px;
            overflow-wrap: anywhere;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #e2e8f0;
            color: #334155;
        }

        .status.on-time {
            background: #dcfce7;
            color: #15803d;
        }

        .status.delayed {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status.moving {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
        }

        .panel h2 {
            font-size: 18px;
            margin-bottom: 15px;
        }

        #map {
            height: 430px;
            width: 100%;
            border-radius: 10px;
            z-index: 1;
        }

        .map-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            font-size: 12px;
            color: #64748b;
        }

        .legend span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }

        .red { background: #ef4444; }
        .blue { background: #2563eb; }
        .green { background: #16a34a; }

        .bus-list {
            max-height: 430px;
            overflow-y: auto;
        }

        .bus-item {
            padding: 14px;
            border: 1px solid #e5eaf2;
            border-radius: 9px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: .2s;
        }

        .bus-item:hover,
        .bus-item.active {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .bus-item strong {
            display: block;
            margin-bottom: 6px;
        }

        .bus-item small {
            color: #64748b;
        }

        .table-panel {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 15px rgba(0,0,0,.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #edf0f5;
            font-size: 13px;
        }

        th {
            color: #64748b;
            background: #f8fafc;
        }

        .empty-message {
            color: #64748b;
            padding: 20px;
            text-align: center;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #718096;
            font-size: 12px;
        }

        @media(max-width: 900px) {
            .details-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 550px) {
            main {
                padding: 15px;
            }

            .details-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .detail-card {
                padding: 14px;
            }

            .detail-card h3 {
                font-size: 16px;
            }

            #map {
                height: 330px;
            }
        }
    </style>
</head>

<body>

<header>
    <div>
        <h1>TrackMyBus</h1>
        <p>Live Bus Tracking System</p>
    </div>
    <div class="header-status" id="connectionStatus">
        ● Connecting...
    </div>
</header>

<main>

    <section class="search-section">
        <h2>Find Your Bus</h2>

        <div class="search-box">
            <input type="text"
                   id="busSearch"
                   placeholder="Enter bus number (Example: 114)">

            <button class="primary-btn" id="searchButton">
                Track Bus
            </button>

            <button class="secondary-btn" id="refreshButton">
                Refresh
            </button>
        </div>

        <p class="message" id="message">
            Enter a bus number to view its live tracking information.
        </p>
    </section>

    <section class="details-grid">

        <div class="detail-card">
            <small>Selected Bus</small>
            <h3 id="busNumber">--</h3>
        </div>

        <div class="detail-card">
            <small>Route</small>
            <h3 id="busRoute">--</h3>
        </div>

        <div class="detail-card">
            <small>Current Status</small>
            <h3><span class="status" id="busStatus">--</span></h3>
        </div>

        <div class="detail-card">
            <small>Estimated Arrival</small>
            <h3 id="busETA">--</h3>
        </div>

    </section>

    <section class="content-grid">

        <div class="panel">
            <h2>Live Bus Location</h2>

            <div id="map"></div>

            <div class="map-actions">
                <button class="secondary-btn" id="locationButton">
                    My Location
                </button>
            </div>

            <div class="legend">
                <span><i class="dot red"></i> Selected Bus</span>
                <span><i class="dot blue"></i> Other Buses</span>
                <span><i class="dot green"></i> Destination</span>
            </div>
        </div>

        <div class="panel">
            <h2>Available Buses</h2>
            <div class="bus-list" id="busList">
                <p class="empty-message">Loading buses...</p>
            </div>
        </div>

    </section>

    <section class="table-panel">
        <h2 style="margin-bottom:15px;">All Bus Details</h2>

        <table>
            <thead>
                <tr>
                    <th>Bus Number</th>
                    <th>Route</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="busTableBody">
                <tr>
                    <td colspan="5" class="empty-message">
                        Loading bus information...
                    </td>
                </tr>
            </tbody>
        </table>
    </section>

</main>

<footer>
    TrackMyBus - Live Bus Tracking System
</footer>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    let map;
    let allBuses = [];
    let currentBusNumber = null;
    let selectedBusMarker = null;
    let allBusMarkers = {};
    let routeLine = null;
    let destinationMarker = null;
    let userMarker = null;

    let busRequestId = 0;
    let routeRequestId = 0;
    let isRefreshing = false;

    const API_URL = '/api/buses';

    function getBusNumber(bus) {
        return String(
            bus.bus_number ??
            bus.number ??
            bus.name ??
            ''
        ).trim();
    }

    function getBusRoute(bus) {
        return bus.route ??
               bus.route_name ??
               bus.destination ??
               'Route information unavailable';
    }

    function getBusStatus(bus) {
        return bus.status ??
               bus.bus_status ??
               'Unknown';
    }

    function getBusLatitude(bus) {
        return Number(
            bus.latitude ??
            bus.lat ??
            bus.current_latitude
        );
    }

    function getBusLongitude(bus) {
        return Number(
            bus.longitude ??
            bus.lng ??
            bus.lon ??
            bus.current_longitude
        );
    }

    function hasValidCoordinates(bus) {
        const lat = getBusLatitude(bus);
        const lng = getBusLongitude(bus);

        return Number.isFinite(lat) &&
               Number.isFinite(lng) &&
               lat >= -90 && lat <= 90 &&
               lng >= -180 && lng <= 180 &&
               !(lat === 0 && lng === 0);
    }

    function escapeHTML(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function unwrapBusResponse(data) {
        if (Array.isArray(data)) return data;

        if (Array.isArray(data.data)) return data.data;
        if (Array.isArray(data.buses)) return data.buses;

        if (data.bus) return data.bus;
        if (data.data && typeof data.data === 'object') return data.data;

        return data;
    }

    function setMessage(message) {
        document.getElementById('message').textContent = message;
    }

    function setConnectionStatus(connected) {
        const element = document.getElementById('connectionStatus');

        element.textContent = connected
            ? '● Connected'
            : '● Connection unavailable';

        element.style.color = connected ? '#6ee7b7' : '#fca5a5';
    }

    function initializeMap() {
        map = L.map('map').setView([12.9716, 77.5946], 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
    }

    async function loadAllBuses() {
        try {
            const response = await fetch(API_URL, {
                headers: {
                    'Accept': 'application/json'
                },
                cache: 'no-store'
            });

            if (!response.ok) {
                throw new Error('Unable to load buses.');
            }

            const data = await response.json();
            const buses = unwrapBusResponse(data);

            if (!Array.isArray(buses)) {
                throw new Error('Invalid bus information received.');
            }

            allBuses = buses;
            setConnectionStatus(true);

            updateAllBusMarkers();
            updateBusList();
            updateBusTable();

            return true;

        } catch (error) {
            console.error('Bus loading error:', error);
            setConnectionStatus(false);
            setMessage('Unable to load bus information. Please try again.');
            return false;
        }
    }

    function updateAllBusMarkers() {
        if (!map) return;

        const busesWithCoordinates = allBuses.filter(hasValidCoordinates);

        const activeNumbers = new Set(
            busesWithCoordinates.map(getBusNumber)
        );

        Object.keys(allBusMarkers).forEach(function(number) {
            if (!activeNumbers.has(number) || number === currentBusNumber) {
                map.removeLayer(allBusMarkers[number]);
                delete allBusMarkers[number];
            }
        });

        busesWithCoordinates.forEach(function(bus) {
            const number = getBusNumber(bus);

            if (!number || number === currentBusNumber) return;

            const lat = getBusLatitude(bus);
            const lng = getBusLongitude(bus);

            const popup =
                '<strong>Bus ' + escapeHTML(number) + '</strong><br>' +
                escapeHTML(getBusRoute(bus)) + '<br>' +
                'Status: ' + escapeHTML(getBusStatus(bus));

            if (allBusMarkers[number]) {
                allBusMarkers[number].setLatLng([lat, lng]);
                allBusMarkers[number].setPopupContent(popup);
            } else {
                allBusMarkers[number] = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup(popup);
            }
        });
    }

    function updateBusList() {
        const container = document.getElementById('busList');

        if (!allBuses.length) {
            container.innerHTML =
                '<p class="empty-message">No buses available.</p>';
            return;
        }

        container.innerHTML = '';

        allBuses.forEach(function(bus) {
            const number = getBusNumber(bus);

            const item = document.createElement('div');
            item.className = 'bus-item' +
                (number === currentBusNumber ? ' active' : '');

            const title = document.createElement('strong');
            title.textContent = 'Bus ' + number;

            const route = document.createElement('small');
            route.textContent = getBusRoute(bus);

            const status = document.createElement('small');
            status.textContent = ' | ' + getBusStatus(bus);

            item.appendChild(title);
            item.appendChild(route);
            item.appendChild(status);

            item.addEventListener('click', function() {
                trackBus(number);
            });

            container.appendChild(item);
        });
    }

    function updateBusTable() {
        const tbody = document.getElementById('busTableBody');

        if (!allBuses.length) {
            tbody.innerHTML =
                '<tr><td colspan="5" class="empty-message">No buses found.</td></tr>';
            return;
        }

        tbody.innerHTML = '';

        allBuses.forEach(function(bus) {
            const number = getBusNumber(bus);

            const row = document.createElement('tr');

            const values = [
                'Bus ' + number,
                getBusRoute(bus),
                bus.driver_name ?? bus.driver?.name ?? 'Not assigned',
                getBusStatus(bus)
            ];

            values.forEach(function(value) {
                const cell = document.createElement('td');
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actionCell = document.createElement('td');
            const button = document.createElement('button');

            button.className = 'primary-btn';
            button.textContent = 'Track';

            button.addEventListener('click', function() {
                trackBus(number);
            });

            actionCell.appendChild(button);
            row.appendChild(actionCell);
            tbody.appendChild(row);
        });
    }

    async function trackBus(busNumber, redrawRoute = true) {
        if (!busNumber) {
            setMessage('Please enter a bus number.');
            return;
        }

        const requestId = ++busRequestId;
        currentBusNumber = String(busNumber).trim();

        setMessage('Fetching bus location...');

        try {
            const response = await fetch(
                API_URL + '/' + encodeURIComponent(currentBusNumber),
                {
                    headers: {
                        'Accept': 'application/json'
                    },
                    cache: 'no-store'
                }
            );

            if (!response.ok) {
                throw new Error('Bus not found.');
            }

            const responseData = await response.json();

            if (requestId !== busRequestId) return;

            const bus = unwrapBusResponse(responseData);

            if (!bus || Array.isArray(bus)) {
                throw new Error('Bus information not found.');
            }

            const returnedNumber = getBusNumber(bus);

            if (returnedNumber) {
                currentBusNumber = returnedNumber;
            }

            const index = allBuses.findIndex(function(item) {
                return getBusNumber(item) === currentBusNumber;
            });

            if (index >= 0) {
                allBuses[index] = {
                    ...allBuses[index],
                    ...bus
                };
            } else {
                allBuses.push(bus);
            }

            displayBusDetails(bus);
            updateBusMarker(bus);
            updateAllBusMarkers();
            updateBusList();
            updateBusTable();

            setMessage('Showing information for Bus ' + currentBusNumber);

            if (redrawRoute) {
                await drawRouteForBus(currentBusNumber);
            }

        } catch (error) {
            if (requestId !== busRequestId) return;

            console.error('Tracking error:', error);
            setMessage(error.message || 'Unable to track this bus.');
        }
    }

    function displayBusDetails(bus) {
        document.getElementById('busNumber').textContent =
            'Bus ' + getBusNumber(bus);

        document.getElementById('busRoute').textContent =
            getBusRoute(bus);

        const statusElement = document.getElementById('busStatus');
        const status = getBusStatus(bus);

        statusElement.textContent = status;
        statusElement.className = 'status';

        const normalizedStatus = String(status).toLowerCase();

        if (normalizedStatus.includes('on time')) {
            statusElement.classList.add('on-time');
        } else if (normalizedStatus.includes('delay')) {
            statusElement.classList.add('delayed');
        } else if (
            normalizedStatus.includes('moving') ||
            normalizedStatus.includes('running')
        ) {
            statusElement.classList.add('moving');
        }

        document.getElementById('busETA').textContent =
            bus.eta ?? bus.estimated_arrival ?? '--';
    }

    function updateBusMarker(bus) {
        if (!map) return;

        if (selectedBusMarker) {
            map.removeLayer(selectedBusMarker);
            selectedBusMarker = null;
        }

        if (!hasValidCoordinates(bus)) {
            return;
        }

        const lat = getBusLatitude(bus);
        const lng = getBusLongitude(bus);

        selectedBusMarker = L.circleMarker([lat, lng], {
            radius: 10,
            color: '#b91c1c',
            fillColor: '#ef4444',
            fillOpacity: 1,
            weight: 3
        }).addTo(map);

        selectedBusMarker.bindPopup(
            '<strong>Selected Bus ' +
            escapeHTML(getBusNumber(bus)) +
            '</strong><br>' +
            escapeHTML(getBusRoute(bus))
        );

        map.setView([lat, lng], 14);
    }

    async function drawRouteForBus(busNumber) {
        const requestId = ++routeRequestId;

        clearRoute(false);

        const bus = allBuses.find(function(item) {
            return getBusNumber(item) === String(busNumber);
        });

        if (!bus || !hasValidCoordinates(bus)) {
            return;
        }

        const start = [
            getBusLatitude(bus),
            getBusLongitude(bus)
        ];

        let destinationLat = Number(
            bus.destination_latitude ??
            bus.destination_lat ??
            bus.end_latitude
        );

        let destinationLng = Number(
            bus.destination_longitude ??
            bus.destination_lng ??
            bus.destination_lng ??
            bus.end_longitude
        );

        if (
            !Number.isFinite(destinationLat) ||
            !Number.isFinite(destinationLng) ||
            destinationLat < -90 || destinationLat > 90 ||
            destinationLng < -180 || destinationLng > 180 ||
            (destinationLat === 0 && destinationLng === 0)
        ) {
            return;
        }

        const destination = [destinationLat, destinationLng];

        try {
            const url =
                'https://router.project-osrm.org/route/v1/driving/' +
                start[1] + ',' + start[0] + ';' +
                destination[1] + ',' + destination[0] +
                '?overview=full&geometries=geojson';

            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Route service unavailable.');
            }

            const data = await response.json();

            if (requestId !== routeRequestId) return;

            if (!data.routes || !data.routes.length) {
                throw new Error('No road route found.');
            }

            const coordinates = data.routes[0].geometry.coordinates.map(
                function(point) {
                    return [point[1], point[0]];
                }
            );

            routeLine = L.polyline(coordinates, {
                color: '#2563eb',
                weight: 5,
                opacity: 0.8
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), {
                padding: [40, 40]
            });

        } catch (error) {
            if (requestId !== routeRequestId) return;

            console.warn('Road route unavailable:', error);

            routeLine = L.polyline([start, destination], {
                color: '#2563eb',
                weight: 4,
                dashArray: '8, 10'
            }).addTo(map);

            map.fitBounds(routeLine.getBounds(), {
                padding: [40, 40]
            });
        }

        if (requestId !== routeRequestId) return;

        destinationMarker = L.marker(destination).addTo(map)
            .bindPopup('Bus destination');
    }

    function clearRoute(invalidate = true) {
        if (invalidate) {
            routeRequestId++;
        }

        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        if (destinationMarker) {
            map.removeLayer(destinationMarker);
            destinationMarker = null;
        }
    }

    async function refreshData() {
        if (isRefreshing) return;

        isRefreshing = true;

        try {
            const selectedNumber = currentBusNumber;

            const loaded = await loadAllBuses();

            if (loaded && selectedNumber) {
                await trackBus(selectedNumber, false);
            }
        } finally {
            isRefreshing = false;
        }
    }

    function showUserLocation() {
        if (!navigator.geolocation) {
            setMessage('Your browser does not support location services.');
            return;
        }

        setMessage('Getting your current location...');

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                if (userMarker) {
                    map.removeLayer(userMarker);
                }

                userMarker = L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup('Your current location');

                userMarker.openPopup();
                map.setView([lat, lng], 15);

                setMessage('Your current location is displayed on the map.');
            },
            function(error) {
                console.error('Location error:', error);
                setMessage('Unable to get your location. Please allow location access.');
            },
            {
                enableHighAccuracy: true,
                timeout: 10000
            }
        );
    }

    document.getElementById('searchButton').addEventListener('click', function() {
        const number = document.getElementById('busSearch').value.trim();

        if (!number) {
            setMessage('Please enter a bus number.');
            return;
        }

        trackBus(number);
    });

    document.getElementById('busSearch').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            document.getElementById('searchButton').click();
        }
    });

    document.getElementById('refreshButton').addEventListener('click', refreshData);

    document.getElementById('locationButton').addEventListener(
        'click',
        showUserLocation
    );

    async function startTrackingApp() {
        initializeMap();

        const loaded = await loadAllBuses();

        const urlParams = new URLSearchParams(window.location.search);
        const requestedBus = urlParams.get('bus');

        if (requestedBus) {
            document.getElementById('busSearch').value = requestedBus;
            await trackBus(requestedBus);
        } else if (allBuses.length > 0) {
            setMessage('Select a bus from the list or enter its number.');
        } else if (!loaded) {
            setMessage('Could not load buses. Please refresh the page.');
        }

        setInterval(refreshData, 30000);
    }

    document.addEventListener('DOMContentLoaded', startTrackingApp);
</script>

</body>
</html>