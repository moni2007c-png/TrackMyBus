<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Live Bus Tracking - TrackMyBus</title>

    <!-- Leaflet CSS -->
    <link
        rel="stylesheet"
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    >

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        nav {
            background: #0f172a;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav h2 {
            margin: 0;
            color: white;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            margin-bottom: 8px;
            color: #0f172a;
        }

        .header p {
            color: #64748b;
        }

        .search-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .search-box label {
            display: block;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .search-row {
            display: flex;
            gap: 10px;
        }

        .search-row input {
            flex: 1;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 16px;
        }

        .search-row button {
            padding: 12px 22px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        .search-row button:hover {
            background: #1d4ed8;
        }

        .error {
            display: none;
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .bus-card {
            display: none;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }

        .bus-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .bus-header h2 {
            margin: 0;
            color: #0f172a;
        }

        .status {
            padding: 7px 14px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .status.on-time {
            background: #dcfce7;
            color: #166534;
        }

        .status.delayed {
            background: #fee2e2;
            color: #991b1b;
        }

        .route {
            color: #475569;
            margin: 10px 0 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }

        .info-box {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
        }

        .info-box small {
            display: block;
            color: #64748b;
            margin-bottom: 6px;
        }

        .info-box strong {
            font-size: 16px;
        }

        .eta-box {
            margin-top: 20px;
            padding: 18px;
            border-radius: 10px;
            background: #eff6ff;
            text-align: center;
        }

        .eta-box small {
            color: #475569;
        }

        .eta-box strong {
            display: block;
            font-size: 28px;
            color: #2563eb;
            margin-top: 5px;
        }

        .updated {
            text-align: right;
            color: #64748b;
            font-size: 13px;
            margin-top: 15px;
        }

        #map {
            height: 500px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .map-title {
            margin-bottom: 12px;
        }

        .legend {
            background: white;
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .legend span {
            margin-right: 20px;
        }

        @media (max-width: 700px) {

            nav {
                padding: 15px;
            }

            nav a {
                margin-left: 10px;
            }

            .search-row {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .bus-header {
                flex-direction: column;
                align-items: flex-start;
            }

            #map {
                height: 400px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVIGATION
    ========================== -->

    <nav>

        <h2>🚌 TrackMyBus</h2>

        <div>
            <a href="/">Home</a>
            <a href="/routes">Routes</a>
        </div>

    </nav>


    <!-- =========================
         MAIN CONTENT
    ========================== -->

    <div class="container">

        <div class="header">

            <h1>Live Bus Tracking</h1>

            <p>
                Enter a bus number to view its current location
                and estimated arrival time.
            </p>

        </div>


        <!-- =========================
             BUS SEARCH
        ========================== -->

        <div class="search-box">

            <label for="busNumber">
                Bus Number
            </label>

            <div class="search-row">

                <input
                    type="text"
                    id="busNumber"
                    placeholder="Example: 501"
                >

                <button onclick="trackBus()">
                    Track Bus
                </button>

            </div>

        </div>


        <!-- =========================
             ERROR MESSAGE
        ========================== -->

        <div
            id="errorMessage"
            class="error">
        </div>


        <!-- =========================
             BUS INFORMATION
        ========================== -->

        <div
            id="busCard"
            class="bus-card">

            <div class="bus-header">

                <h2>
                    🚌 Bus
                    <span id="displayBusNumber">
                        -
                    </span>
                </h2>

                <span
                    id="statusBadge"
                    class="status">
                    -
                </span>

            </div>


            <p class="route">

                <strong>
                    Route:
                </strong>

                <span id="route">
                    -
                </span>

            </p>


            <div class="info-grid">

                <div class="info-box">

                    <small>
                        Latitude
                    </small>

                    <strong id="latitude">
                        -
                    </strong>

                </div>


                <div class="info-box">

                    <small>
                        Longitude
                    </small>

                    <strong id="longitude">
                        -
                    </strong>

                </div>


                <div class="info-box">

                    <small>
                        Last Updated
                    </small>

                    <strong id="lastUpdated">
                        -
                    </strong>

                </div>

            </div>


            <!-- ETA -->

            <div class="eta-box">

                <small>
                    ⏱️ Estimated Arrival
                </small>

                <strong>

                    <span id="eta">
                        -
                    </span>

                    minutes

                </strong>

            </div>


            <div class="updated">

                🔄 Automatically refreshes
                every 30 seconds

            </div>

        </div>


        <!-- =========================
             MAP
        ========================== -->

        <h2 class="map-title">

            📍 Bus Location

        </h2>


        <div id="map"></div>


        <!-- =========================
             MAP LEGEND
        ========================== -->

        <div class="legend">

            <span>
                🚌 <strong>Bus</strong>
            </span>

            <span>
                📍 <strong>Your Location</strong>
            </span>

        </div>

    </div>


    <!-- Leaflet JavaScript -->

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
    </script>


    <!-- =========================
         TRACKING JAVASCRIPT
    ========================== -->

    <script>

        let map = null;

        let busMarker = null;

        let userMarker = null;

        let currentBusNumber = null;


        // ========================================
        // INITIALIZE MAP
        // ========================================

        function initializeMap() {

            map = L.map('map').setView(
                [13.0827, 80.2707],
                11
            );


            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    attribution:
                        '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);

        }


        // ========================================
        // TRACK BUS
        // ========================================

        async function trackBus() {

            const input =
                document.getElementById(
                    'busNumber'
                );


            const busNumber =
                input.value.trim();


            if (!busNumber) {

                showError(
                    'Please enter a bus number.'
                );

                return;
            }


            currentBusNumber =
                busNumber;


            // Update URL

            const newUrl =
                window.location.pathname +
                '?bus=' +
                encodeURIComponent(
                    busNumber
                );


            window.history.pushState(
                {},
                '',
                newUrl
            );


            await loadBus(
                busNumber
            );

        }


        // ========================================
        // LOAD BUS
        // ========================================

        async function loadBus(
            busNumber
        ) {

            try {

                const response =
                    await fetch(
                        `/api/buses/${encodeURIComponent(busNumber)}`
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    showError(
                        data.message ||
                        'Bus not found.'
                    );

                    return;
                }


                hideError();


                // Show bus card

                document.getElementById(
                    'busCard'
                ).style.display =
                    'block';


                // Bus number

                document.getElementById(
                    'displayBusNumber'
                ).textContent =
                    data.bus_number;


                // Route

                document.getElementById(
                    'route'
                ).textContent =
                    data.route;


                // Latitude

                document.getElementById(
                    'latitude'
                ).textContent =
                    data.latitude ??
                    'Not available';


                // Longitude

                document.getElementById(
                    'longitude'
                ).textContent =
                    data.longitude ??
                    'Not available';


                // Last updated

                document.getElementById(
                    'lastUpdated'
                ).textContent =
                    data.location_updated_at ??
                    'Not available';


                // ETA

                document.getElementById(
                    'eta'
                ).textContent =
                    data.eta_minutes ??
                    'N/A';


                // Status

                const statusBadge =
                    document.getElementById(
                        'statusBadge'
                    );


                statusBadge.textContent =
                    data.status;


                statusBadge.className =
                    'status ' +
                    (
                        data.status === 'On Time'
                            ? 'on-time'
                            : 'delayed'
                    );


                // Update marker

                updateBusMarker(
                    data
                );

            }

            catch (error) {

                console.error(
                    'Bus loading error:',
                    error
                );


                showError(
                    'Unable to connect to the server.'
                );

            }

        }


        // ========================================
        // UPDATE BUS MARKER
        // ========================================

        function updateBusMarker(
            data
        ) {

            if (
                data.latitude === null ||
                data.longitude === null
            ) {

                return;
            }


            const lat =
                parseFloat(
                    data.latitude
                );


            const lng =
                parseFloat(
                    data.longitude
                );


            // Bus icon

            const busIcon =
                L.divIcon({

                    className: '',


                    html: `

                        <div style="
                            background:#2563eb;
                            color:white;
                            width:44px;
                            height:44px;
                            border-radius:50%;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            font-size:22px;
                            border:3px solid white;
                            box-shadow:
                                0 2px 8px
                                rgba(0,0,0,0.35);
                        ">

                            🚌

                        </div>

                    `,


                    iconSize: [
                        44,
                        44
                    ],


                    iconAnchor: [
                        22,
                        22
                    ]

                });


            // Popup

            const popup = `

                <strong>
                    🚌 Bus ${data.bus_number}
                </strong>

                <br>

                ${data.route}

                <br>

                Status:
                ${data.status}

                <br>

                ETA:
                ${data.eta_minutes ?? 'N/A'}
                minutes

            `;


            if (busMarker) {

                busMarker.setLatLng(
                    [lat, lng]
                );


                busMarker.setPopupContent(
                    popup
                );

            }

            else {

                busMarker =
                    L.marker(
                        [lat, lng],
                        {
                            icon: busIcon
                        }
                    )
                    .addTo(map)
                    .bindPopup(
                        popup
                    );

            }


            // Center map on bus

            map.setView(
                [lat, lng],
                14
            );

        }


        // ========================================
        // USER LOCATION
        // ========================================

        function showUserLocation() {

            if (
                !navigator.geolocation
            ) {

                console.log(
                    'Geolocation is not supported.'
                );

                return;
            }


            navigator.geolocation.watchPosition(

                function(position) {

                    const lat =
                        position.coords.latitude;


                    const lng =
                        position.coords.longitude;


                    // User icon

                    const userIcon =
                        L.divIcon({

                            className: '',


                            html: `

                                <div style="
                                    background:#16a34a;
                                    width:24px;
                                    height:24px;
                                    border-radius:50%;
                                    border:4px solid white;
                                    box-shadow:
                                        0 2px 7px
                                        rgba(0,0,0,0.35);
                                ">

                                </div>

                            `,


                            iconSize: [
                                24,
                                24
                            ],


                            iconAnchor: [
                                12,
                                12
                            ]

                        });


                    if (userMarker) {

                        userMarker.setLatLng(
                            [lat, lng]
                        );

                    }

                    else {

                        userMarker =
                            L.marker(
                                [lat, lng],
                                {
                                    icon: userIcon
                                }
                            )
                            .addTo(map)
                            .bindPopup(
                                '📍 Your current location'
                            );

                    }

                },


                function(error) {

                    console.log(
                        'Passenger location unavailable:',
                        error.message
                    );

                }

            );

        }


        // ========================================
        // ERROR
        // ========================================

        function showError(
            message
        ) {

            const error =
                document.getElementById(
                    'errorMessage'
                );


            error.textContent =
                message;


            error.style.display =
                'block';


            document.getElementById(
                'busCard'
            ).style.display =
                'none';

        }


        function hideError() {

            document.getElementById(
                'errorMessage'
            ).style.display =
                'none';

        }


        // ========================================
        // START PAGE
        // ========================================

        initializeMap();

        showUserLocation();


        // ========================================
        // READ BUS FROM URL
        // ========================================

        const params =
            new URLSearchParams(
                window.location.search
            );


        const busFromUrl =
            params.get('bus');


        if (busFromUrl) {

            const cleanBusNumber =
                busFromUrl.trim();


            document.getElementById(
                'busNumber'
            ).value =
                cleanBusNumber;


            currentBusNumber =
                cleanBusNumber;


            loadBus(
                cleanBusNumber
            );

        }


        // ========================================
        // AUTO REFRESH
        // ========================================

        setInterval(

            function() {

                if (currentBusNumber) {

                    loadBus(
                        currentBusNumber
                    );

                }

            },

            30000

        );

    </script>

</body>

</html>