<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>TrackMyBus - Admin Dashboard</title>

    <!-- Leaflet -->
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
            background: #f1f5f9;
            color: #1e293b;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            background: #0f172a;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            width: 92%;
            max-width: 1300px;
            margin: 30px auto;
        }

        /* =========================
           SUMMARY
        ========================= */

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .summary-card h3 {
            margin-top: 0;
            color: #64748b;
        }

        .summary-card p {
            font-size: 30px;
            font-weight: bold;
            margin-bottom: 0;
            color: #2563eb;
        }

        /* =========================
           SECTION
        ========================= */

        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .section h2 {
            margin-top: 0;
        }

        /* =========================
           FORM
        ========================= */

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        input,
        select {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        button {
            cursor: pointer;
        }

        .add-btn {
            margin-top: 15px;
            padding: 11px 20px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: bold;
        }

        #addBusMessage {
            margin-top: 15px;
            font-weight: bold;
        }

        /* =========================
           BUS LIST
        ========================= */

        .bus-list {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .bus-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            background: #f8fafc;
        }

        .bus-card h3 {
            margin-top: 0;
            color: #2563eb;
        }

        .bus-card p {
            margin: 8px 0;
        }

        .status {
            font-weight: bold;
        }

        .status-on-time {
            color: #16a34a;
        }

        .status-delayed {
            color: #dc2626;
        }

        .button-row {
            margin-top: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .edit-btn {
            padding: 8px 13px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 6px;
        }

        .delete-btn {
            padding: 8px 13px;
            background: #dc2626;
            color: white;
            border: none;
            border-radius: 6px;
        }

        .assign-btn {
            padding: 8px 13px;
            background: #7c3aed;
            color: white;
            border: none;
            border-radius: 6px;
        }

        /* =========================
           MAP
        ========================= */

        #map {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            overflow: hidden;
        }

        .error {
            color: #dc2626;
            font-weight: bold;
        }

        /* =========================
           MODAL
        ========================= */

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.55);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 500px;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.25);
        }

        .modal-content h2 {
            margin-top: 0;
            color: #0f172a;
        }

        .modal-field {
            margin-bottom: 15px;
        }

        .modal-field label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .save-btn {
            flex: 1;
            padding: 11px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .cancel-btn {
            flex: 1;
            padding: 11px;
            background: #64748b;
            color: white;
            border: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .modal-message {
            margin-top: 12px;
            font-weight: bold;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 800px) {

            .summary {
                grid-template-columns: 1fr;
            }

            .bus-list {
                grid-template-columns: 1fr;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .navbar {
                flex-direction: column;
                gap: 10px;
            }

        }

    </style>

</head>


<body>

    <!-- =========================
         NAVBAR
    ========================= -->

    <div class="navbar">

        <h2>🚌 TrackMyBus Admin</h2>

        <div>

            <a href="/">Home</a>

            <a href="/routes">Routes</a>

            <a href="/admin">Admin</a>

            <form
                method="POST"
                action="{{ route('logout') }}"
                style="display:inline;"
            >

                @csrf

                <button
                    type="submit"
                    style="
                        background:none;
                        border:none;
                        color:white;
                        margin-left:20px;
                        cursor:pointer;
                        font-size:15px;
                    "
                >
                    Logout
                </button>

            </form>

        </div>

    </div>


    <!-- =========================
         MAIN
    ========================= -->

    <div class="container">

        <h1>Admin Dashboard</h1>


        <!-- =========================
             SUMMARY
        ========================= -->

        <div class="summary">

            <div class="summary-card">

                <h3>Total Buses</h3>

                <p id="totalBuses">0</p>

            </div>


            <div class="summary-card">

                <h3>On Time</h3>

                <p id="onTimeBuses">0</p>

            </div>


            <div class="summary-card">

                <h3>Delayed</h3>

                <p id="delayedBuses">0</p>

            </div>

        </div>


        <!-- =========================
             ADD BUS
        ========================= -->

        <div class="section">

            <h2>➕ Add New Bus</h2>

            <form id="addBusForm">

                <div class="form-grid">

                    <input
                        type="text"
                        id="newBusNumber"
                        placeholder="Bus Number"
                        required
                    >

                    <input
                        type="text"
                        id="newBusRoute"
                        placeholder="Route"
                        required
                    >

                    <select id="newBusStatus">

                        <option value="On Time">
                            On Time
                        </option>

                        <option value="Delayed">
                            Delayed
                        </option>

                    </select>

                    <input
                        type="number"
                        step="any"
                        id="newBusLatitude"
                        placeholder="Current Latitude"
                    >

                    <input
                        type="number"
                        step="any"
                        id="newBusLongitude"
                        placeholder="Current Longitude"
                    >

                    <input
                        type="number"
                        step="any"
                        id="destinationLatitude"
                        placeholder="Destination Latitude"
                    >

                    <input
                        type="number"
                        step="any"
                        id="destinationLongitude"
                        placeholder="Destination Longitude"
                    >

                </div>


                <button
                    type="submit"
                    class="add-btn"
                >
                    ➕ Add Bus
                </button>


                <div id="addBusMessage"></div>

            </form>

        </div>


        <!-- =========================
             BUSES
        ========================= -->

        <div class="section">

            <h2>🚌 Buses</h2>

            <div
                id="busList"
                class="bus-list"
            >
                Loading buses...
            </div>

        </div>


        <!-- =========================
             MAP
        ========================= -->

        <div class="section">

            <h2>🗺️ Live Bus Map</h2>

            <div id="map"></div>

        </div>

    </div>


    <!-- =========================
         EDIT MODAL
    ========================= -->

    <div
        id="editModal"
        class="modal"
    >

        <div class="modal-content">

            <h2>✏️ Edit Bus</h2>

            <input
                type="hidden"
                id="editBusNumber"
            >


            <div class="modal-field">

                <label>
                    Bus Number
                </label>

                <input
                    type="text"
                    id="editBusNumberDisplay"
                    readonly
                >

            </div>


            <div class="modal-field">

                <label>
                    Route
                </label>

                <input
                    type="text"
                    id="editRoute"
                    required
                >

            </div>


            <div class="modal-field">

                <label>
                    Status
                </label>

                <select id="editStatus">

                    <option value="On Time">
                        On Time
                    </option>

                    <option value="Delayed">
                        Delayed
                    </option>

                </select>

            </div>


            <div class="modal-field">

                <label>
                    Destination Latitude
                </label>

                <input
                    type="number"
                    step="any"
                    id="editDestinationLatitude"
                >

            </div>


            <div class="modal-field">

                <label>
                    Destination Longitude
                </label>

                <input
                    type="number"
                    step="any"
                    id="editDestinationLongitude"
                >

            </div>


            <div
                id="editMessage"
                class="modal-message"
            ></div>


            <div class="modal-buttons">

                <button
                    type="button"
                    class="save-btn"
                    onclick="saveEditedBus()"
                >
                    💾 Save Changes
                </button>

                <button
                    type="button"
                    class="cancel-btn"
                    onclick="closeEditModal()"
                >
                    Cancel
                </button>

            </div>

        </div>

    </div>


    <!-- =========================
         ASSIGN DRIVER MODAL
    ========================= -->

    <div
        id="assignModal"
        class="modal"
    >

        <div class="modal-content">

            <h2>👤 Assign Driver</h2>

            <input
                type="hidden"
                id="assignBusNumber"
            >


            <div class="modal-field">

                <label>
                    Bus Number
                </label>

                <input
                    type="text"
                    id="assignBusNumberDisplay"
                    readonly
                >

            </div>


            <div class="modal-field">

                <label>
                    Driver User ID
                </label>

                <input
                    type="number"
                    id="driverId"
                    placeholder="Enter driver User ID"
                    required
                >

            </div>


            <p style="font-size:13px;color:#64748b;">
                Enter the User ID of a user whose role is
                <strong>driver</strong>.
            </p>


            <div
                id="assignMessage"
                class="modal-message"
            ></div>


            <div class="modal-buttons">

                <button
                    type="button"
                    class="save-btn"
                    onclick="saveDriverAssignment()"
                >
                    👤 Assign Driver
                </button>

                <button
                    type="button"
                    class="cancel-btn"
                    onclick="closeAssignModal()"
                >
                    Cancel
                </button>

            </div>

        </div>

    </div>


    <!-- Leaflet JS -->

    <script
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    ></script>


    <script>

        /* =====================================================
           MAP
        ===================================================== */

        const map = L.map('map').setView(
            [13.0827, 80.2707],
            10
        );


        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                attribution:
                    '&copy; OpenStreetMap contributors'
            }
        ).addTo(map);


        const busMarkers = {};


        /* =====================================================
           CSRF TOKEN
        ===================================================== */

        function getCsrfToken() {

            return document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                .getAttribute('content');

        }


        /* =====================================================
           LOAD BUSES
        ===================================================== */

        async function loadBuses() {

            const busList =
                document.getElementById(
                    'busList'
                );


            try {

                const response =
                    await fetch(
                        '/api/buses',
                        {
                            method: 'GET',

                            headers: {
                                'Accept':
                                    'application/json'
                            },

                            credentials:
                                'same-origin'
                        }
                    );


                const buses =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        buses.message ||
                        'Failed to load buses'
                    );

                }


                busList.innerHTML = '';


                /* SUMMARY */

                document
                    .getElementById(
                        'totalBuses'
                    )
                    .textContent =
                    buses.length;


                const onTime =
                    buses.filter(
                        bus =>
                            bus.status === 'On Time'
                    ).length;


                const delayed =
                    buses.filter(
                        bus =>
                            bus.status === 'Delayed'
                    ).length;


                document
                    .getElementById(
                        'onTimeBuses'
                    )
                    .textContent =
                    onTime;


                document
                    .getElementById(
                        'delayedBuses'
                    )
                    .textContent =
                    delayed;


                if (buses.length === 0) {

                    busList.innerHTML =
                        '<p>No buses found.</p>';

                    return;

                }


                /* DISPLAY BUS CARDS */

                buses.forEach(
                    bus => {

                        const card =
                            document
                                .createElement(
                                    'div'
                                );


                        card.className =
                            'bus-card';


                        const statusClass =
                            bus.status === 'Delayed'
                                ? 'status-delayed'
                                : 'status-on-time';


                        card.innerHTML = `

                            <h3>
                                🚌 Bus ${bus.bus_number}
                            </h3>

                            <p>
                                <strong>Route:</strong>
                                ${bus.route}
                            </p>

                            <p>
                                <strong>Driver:</strong>
                                ${bus.driver_name ?? 'Not assigned'}
                            </p>

                            <p>
                                <strong>Driver Email:</strong>
                                ${bus.driver_email ?? 'N/A'}
                            </p>

                            <p class="status ${statusClass}">
                                Status: ${bus.status}
                            </p>

                            <p>
                                <strong>Location:</strong>
                                ${bus.latitude ?? 'N/A'},
                                ${bus.longitude ?? 'N/A'}
                            </p>

                            <p>
                                <strong>Destination:</strong>
                                ${bus.destination_latitude ?? 'N/A'},
                                ${bus.destination_longitude ?? 'N/A'}
                            </p>

                            <div class="button-row">

                                <button
                                    class="edit-btn"
                                    type="button"
                                    onclick="openEditModal(
                                        '${escapeJs(bus.bus_number)}',
                                        '${escapeJs(bus.route)}',
                                        '${escapeJs(bus.status)}',
                                        '${escapeJs(bus.destination_latitude ?? '')}',
                                        '${escapeJs(bus.destination_longitude ?? '')}'
                                    )"
                                >
                                    ✏️ Edit Bus
                                </button>


                                <button
                                    class="delete-btn"
                                    type="button"
                                    onclick="deleteBus(
                                        '${escapeJs(bus.bus_number)}'
                                    )"
                                >
                                    🗑️ Delete Bus
                                </button>


                                <button
                                    class="assign-btn"
                                    type="button"
                                    onclick="openAssignModal(
                                        '${escapeJs(bus.bus_number)}'
                                    )"
                                >
                                    👤 Assign Driver
                                </button>

                            </div>

                        `;


                        busList.appendChild(
                            card
                        );


                        /* MAP */

                        if (
                            bus.latitude !== null &&
                            bus.longitude !== null
                        ) {

                            addBusMarker(
                                bus
                            );

                        }

                    }
                );


            } catch (error) {

                console.error(
                    error
                );


                busList.innerHTML = `

                    <div class="error">

                        Unable to load buses.

                        <br><br>

                        ${error.message}

                    </div>

                `;

            }

        }


        /* =====================================================
           ESCAPE JS
        ===================================================== */

        function escapeJs(
            value
        ) {

            return String(value)
                .replace(
                    /\\/g,
                    '\\\\'
                )
                .replace(
                    /'/g,
                    "\\'"
                )
                .replace(
                    /\r/g,
                    '\\r'
                )
                .replace(
                    /\n/g,
                    '\\n'
                );

        }


        /* =====================================================
           MAP MARKER
        ===================================================== */

        function addBusMarker(
            bus
        ) {

            const position = [

                Number(
                    bus.latitude
                ),

                Number(
                    bus.longitude
                )

            ];


            const popup = `

                <b>🚌 Bus ${bus.bus_number}</b>
                <br>

                Route:
                ${bus.route}

                <br>

                Driver:
                ${bus.driver_name ?? 'Not assigned'}

                <br>

                Status:
                ${bus.status}

            `;


            if (
                busMarkers[
                    bus.bus_number
                ]
            ) {

                busMarkers[
                    bus.bus_number
                ]
                    .setLatLng(
                        position
                    )
                    .setPopupContent(
                        popup
                    );

            } else {

                busMarkers[
                    bus.bus_number
                ] =

                    L.marker(
                        position
                    )
                    .addTo(map)
                    .bindPopup(
                        popup
                    );

            }

        }


        /* =====================================================
           ADD BUS
        ===================================================== */

        document
            .getElementById(
                'addBusForm'
            )
            .addEventListener(
                'submit',
                async function(
                    event
                ) {

                    event.preventDefault();


                    const message =
                        document
                            .getElementById(
                                'addBusMessage'
                            );


                    const busData = {

                        bus_number:
                            document
                                .getElementById(
                                    'newBusNumber'
                                )
                                .value
                                .trim(),

                        route:
                            document
                                .getElementById(
                                    'newBusRoute'
                                )
                                .value
                                .trim(),

                        status:
                            document
                                .getElementById(
                                    'newBusStatus'
                                )
                                .value,

                        latitude:
                            document
                                .getElementById(
                                    'newBusLatitude'
                                )
                                .value ||
                            null,

                        longitude:
                            document
                                .getElementById(
                                    'newBusLongitude'
                                )
                                .value ||
                            null,

                        destination_latitude:
                            document
                                .getElementById(
                                    'destinationLatitude'
                                )
                                .value ||
                            null,

                        destination_longitude:
                            document
                                .getElementById(
                                    'destinationLongitude'
                                )
                                .value ||
                            null

                    };


                    try {

                        const response =
                            await fetch(
                                '/api/buses',
                                {

                                    method:
                                        'POST',

                                    headers: {

                                        'Content-Type':
                                            'application/json',

                                        'Accept':
                                            'application/json',

                                        'X-CSRF-TOKEN':
                                            getCsrfToken()

                                    },

                                    credentials:
                                        'same-origin',

                                    body:
                                        JSON.stringify(
                                            busData
                                        )

                                }
                            );


                        const result =
                            await response.json();


                        if (
                            !response.ok
                        ) {

                            throw new Error(
                                result.message ||
                                'Failed to add bus'
                            );

                        }


                        message.style.color =
                            '#16a34a';


                        message.textContent =
                            `Bus ${result.bus_number} added successfully!`;


                        document
                            .getElementById(
                                'addBusForm'
                            )
                            .reset();


                        await loadBuses();


                    } catch (
                        error
                    ) {

                        console.error(
                            error
                        );


                        message.style.color =
                            '#dc2626';


                        message.textContent =
                            error.message ||
                            'Unable to add bus.';

                    }

                }
            );


        /* =====================================================
           EDIT MODAL
        ===================================================== */

        function openEditModal(
            busNumber,
            route,
            status,
            destinationLatitude,
            destinationLongitude
        ) {

            document
                .getElementById(
                    'editBusNumber'
                )
                .value =
                busNumber;


            document
                .getElementById(
                    'editBusNumberDisplay'
                )
                .value =
                busNumber;


            document
                .getElementById(
                    'editRoute'
                )
                .value =
                route;


            document
                .getElementById(
                    'editStatus'
                )
                .value =
                status;


            document
                .getElementById(
                    'editDestinationLatitude'
                )
                .value =
                destinationLatitude;


            document
                .getElementById(
                    'editDestinationLongitude'
                )
                .value =
                destinationLongitude;


            document
                .getElementById(
                    'editMessage'
                )
                .textContent =
                '';


            document
                .getElementById(
                    'editModal'
                )
                .style.display =
                'flex';

        }


        function closeEditModal() {

            document
                .getElementById(
                    'editModal'
                )
                .style.display =
                'none';

        }


        /* =====================================================
           SAVE EDITED BUS
        ===================================================== */

        async function saveEditedBus() {

            const busNumber =
                document
                    .getElementById(
                        'editBusNumber'
                    )
                    .value;


            const route =
                document
                    .getElementById(
                        'editRoute'
                    )
                    .value
                    .trim();


            const status =
                document
                    .getElementById(
                        'editStatus'
                    )
                    .value;


            const destinationLatitude =
                document
                    .getElementById(
                        'editDestinationLatitude'
                    )
                    .value
                    .trim();


            const destinationLongitude =
                document
                    .getElementById(
                        'editDestinationLongitude'
                    )
                    .value
                    .trim();


            const message =
                document
                    .getElementById(
                        'editMessage'
                    );


            if (!route) {

                message.style.color =
                    '#dc2626';

                message.textContent =
                    'Route cannot be empty.';

                return;

            }


            try {

                const response =
                    await fetch(
                        `/api/buses/${encodeURIComponent(busNumber)}`,
                        {

                            method:
                                'PUT',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    getCsrfToken()

                            },

                            credentials:
                                'same-origin',

                            body:
                                JSON.stringify({

                                    route:
                                        route,

                                    status:
                                        status,

                                    destination_latitude:
                                        destinationLatitude ||
                                        null,

                                    destination_longitude:
                                        destinationLongitude ||
                                        null

                                })

                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok
                ) {

                    throw new Error(
                        result.message ||
                        'Failed to update bus.'
                    );

                }


                message.style.color =
                    '#16a34a';


                message.textContent =
                    'Bus updated successfully!';


                await loadBuses();


                setTimeout(
                    function() {

                        closeEditModal();

                    },
                    800
                );


            } catch (
                error
            ) {

                console.error(
                    error
                );


                message.style.color =
                    '#dc2626';


                message.textContent =
                    error.message ||
                    'Unable to update bus.';

            }

        }


        /* =====================================================
           ASSIGN DRIVER MODAL
        ===================================================== */

        function openAssignModal(
            busNumber
        ) {

            document
                .getElementById(
                    'assignBusNumber'
                )
                .value =
                busNumber;


            document
                .getElementById(
                    'assignBusNumberDisplay'
                )
                .value =
                busNumber;


            document
                .getElementById(
                    'driverId'
                )
                .value =
                '';


            document
                .getElementById(
                    'assignMessage'
                )
                .textContent =
                '';


            document
                .getElementById(
                    'assignModal'
                )
                .style.display =
                'flex';

        }


        function closeAssignModal() {

            document
                .getElementById(
                    'assignModal'
                )
                .style.display =
                'none';

        }


        /* =====================================================
           SAVE DRIVER ASSIGNMENT
        ===================================================== */

        async function saveDriverAssignment() {

            const busNumber =
                document
                    .getElementById(
                        'assignBusNumber'
                    )
                    .value;


            const driverId =
                document
                    .getElementById(
                        'driverId'
                    )
                    .value
                    .trim();


            const message =
                document
                    .getElementById(
                        'assignMessage'
                    );


            if (!driverId) {

                message.style.color =
                    '#dc2626';

                message.textContent =
                    'Please enter the driver User ID.';

                return;

            }


            if (
                !/^\d+$/.test(
                    driverId
                )
            ) {

                message.style.color =
                    '#dc2626';

                message.textContent =
                    'Driver User ID must be a number.';

                return;

            }


            try {

                const response =
                    await fetch(
                        `/api/buses/${encodeURIComponent(busNumber)}/assign-driver`,
                        {

                            method:
                                'PUT',

                            headers: {

                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    getCsrfToken()

                            },

                            credentials:
                                'same-origin',

                            body:
                                JSON.stringify({

                                    driver_id:
                                        Number(
                                            driverId
                                        )

                                })

                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok
                ) {

                    throw new Error(
                        result.message ||
                        'Failed to assign driver.'
                    );

                }


                message.style.color =
                    '#16a34a';


                message.textContent =
                    `Driver ${result.driver.name} assigned successfully!`;


                await loadBuses();


                setTimeout(
                    function() {

                        closeAssignModal();

                    },
                    800
                );


            } catch (
                error
            ) {

                console.error(
                    error
                );


                message.style.color =
                    '#dc2626';


                message.textContent =
                    error.message ||
                    'Unable to assign driver.';

            }

        }


        /* =====================================================
           DELETE BUS
        ===================================================== */

        async function deleteBus(
            busNumber
        ) {

            const confirmed =
                window.confirm(
                    `Are you sure you want to delete Bus ${busNumber}?`
                );


            if (!confirmed) {

                return;

            }


            try {

                const response =
                    await fetch(
                        `/api/buses/${encodeURIComponent(busNumber)}`,
                        {

                            method:
                                'DELETE',

                            headers: {

                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    getCsrfToken()

                            },

                            credentials:
                                'same-origin'

                        }
                    );


                const result =
                    await response.json();


                if (
                    !response.ok
                ) {

                    throw new Error(
                        result.message ||
                        'Failed to delete bus.'
                    );

                }


                alert(
                    `Bus ${busNumber} deleted successfully!`
                );


                await loadBuses();


            } catch (
                error
            ) {

                console.error(
                    error
                );


                alert(
                    error.message ||
                    'Unable to delete bus.'
                );

            }

        }


        /* =====================================================
           CLOSE MODAL WHEN CLICKING OUTSIDE
        ===================================================== */

        window.addEventListener(
            'click',
            function(event) {

                const editModal =
                    document.getElementById(
                        'editModal'
                    );


                const assignModal =
                    document.getElementById(
                        'assignModal'
                    );


                if (
                    event.target ===
                    editModal
                ) {

                    closeEditModal();

                }


                if (
                    event.target ===
                    assignModal
                ) {

                    closeAssignModal();

                }

            }
        );


        /* =====================================================
           INITIAL LOAD
        ===================================================== */

        loadBuses();


        /* =====================================================
           REFRESH EVERY 30 SECONDS
        ===================================================== */

        setInterval(
            function() {

                loadBuses();

            },
            30000
        );

    </script>

</body>

</html>