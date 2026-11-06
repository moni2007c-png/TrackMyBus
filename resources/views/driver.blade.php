<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Driver Panel | TrackMyBus</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        nav {
            background: #0f172a;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        nav a:hover {
            color: #38bdf8;
        }

        .container {
            width: 90%;
            max-width: 800px;
            margin: 35px auto;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        }

        h1 {
            margin-bottom: 10px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        label {
            display: block;
            margin: 18px 0 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 16px;
            background: #f8fafc;
        }

        .buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        button {
            margin-top: 20px;
            padding: 13px 20px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 15px;
            font-weight: bold;
            color: white;
            background: #2563eb;
        }

        #stopButton {
            background: #dc2626;
        }

        button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .gps-status,
        .location-box {
            margin-top: 22px;
            padding: 18px;
            border-radius: 10px;
        }

        .gps-status {
            background: #eff6ff;
            overflow-wrap: anywhere;
        }

        .location-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .location-box h3 {
            margin-bottom: 12px;
        }

        .location-box p {
            margin: 10px 0;
            overflow-wrap: anywhere;
        }

        .message {
            margin-top: 18px;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .note {
            margin-top: 15px;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 500px) {
            .card {
                padding: 20px;
            }

            nav {
                padding: 16px 5%;
            }

            nav a {
                margin-left: 10px;
            }

            .container {
                width: 92%;
                margin: 20px auto;
            }

            .buttons button {
                flex: 1;
            }
        }
    </style>
</head>

<body>

<nav>
    <div class="logo">🚌 TrackMyBus</div>

    <div>
        <a href="/">Home</a>
        <a href="/tracking">Live Tracking</a>
    </div>
</nav>

<div class="container">
    <div class="card">

        <h1>Driver Panel</h1>

        <p class="subtitle">
            Share your device's GPS location for your assigned bus.
        </p>

        <label for="busNumber">Assigned Bus Number</label>

        <input
            type="text"
            id="busNumber"
            value="{{ auth()->user()->bus_number }}"
            placeholder="No bus assigned"
            readonly
        >

        <div class="buttons">
            <button id="startButton" type="button">
                Start GPS Tracking
            </button>

            <button id="stopButton" type="button" disabled>
                Stop Tracking
            </button>
        </div>

        <div class="gps-status">
            GPS Status:
            <strong id="gpsStatus" role="status" aria-live="polite">
                Not started
            </strong>
        </div>

        <div class="location-box">
            <h3>Current Location</h3>

            <p>
                Latitude:
                <strong id="latitude">Waiting...</strong>
            </p>

            <p>
                Longitude:
                <strong id="longitude">Waiting...</strong>
            </p>

            <p>
                Last Successful Update:
                <strong id="lastUpdate">Waiting...</strong>
            </p>
        </div>

        <div id="message" class="message" role="status" aria-live="polite"></div>

        <p class="note">
            Keep this page open while tracking. GPS tracking requires location
            permission and a secure browser context (HTTPS or localhost).
            Stop tracking when you finish.
        </p>

    </div>
</div>

<script>
    const busInput = document.getElementById('busNumber');
    const startButton = document.getElementById('startButton');
    const stopButton = document.getElementById('stopButton');
    const gpsStatus = document.getElementById('gpsStatus');
    const message = document.getElementById('message');

    let watchId = null;
    let lastSentAt = 0;
    let sendingLocation = false;
    let trackingActive = false;

    // Avoid sending too many requests: send at most once every 5 seconds.
    const MIN_UPDATE_INTERVAL = 5000;

    function setMessage(text) {
        message.textContent = text;
    }

    function setStatus(text) {
        gpsStatus.textContent = text;
    }

    async function sendLocation(latitude, longitude) {
        if (!trackingActive || sendingLocation) {
            return;
        }

        const busNumber = busInput.value.trim();

        if (!busNumber) {
            setStatus('No bus assigned');
            setMessage('Your account does not have an assigned bus. Contact the administrator.');
            stopTracking();
            return;
        }

        sendingLocation = true;

        try {
            const response = await fetch(
                `/api/buses/${encodeURIComponent(busNumber)}/location`,
                {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN':
                            document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        latitude: latitude,
                        longitude: longitude
                    })
                }
            );

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                if (response.status === 401 || response.status === 419) {
                    throw new Error('Your session may have expired. Please log in again.');
                }

                if (response.status === 403) {
                    throw new Error('Permission denied. Check your driver account and assigned bus.');
                }

                throw new Error(data.message || `Location update failed (${response.status}).`);
            }

            lastSentAt = Date.now();

            document.getElementById('latitude').textContent =
                Number(latitude).toFixed(6);

            document.getElementById('longitude').textContent =
                Number(longitude).toFixed(6);

            document.getElementById('lastUpdate').textContent =
                new Date().toLocaleTimeString();

            setStatus(`Connected — Bus ${busNumber}`);
            setMessage(`✅ GPS location sent successfully for Bus ${busNumber}.`);

        } catch (error) {
            setStatus('Location update failed');
            setMessage(`❌ ${error.message}`);
        } finally {
            sendingLocation = false;
        }
    }

    function handlePosition(position) {
        if (!trackingActive) {
            return;
        }

        const now = Date.now();

        if (now - lastSentAt < MIN_UPDATE_INTERVAL) {
            return;
        }

        sendLocation(
            position.coords.latitude,
            position.coords.longitude
        );
    }

    function handleLocationError(error) {
        if (!trackingActive) {
            return;
        }

        let errorMessage = 'Unable to get your GPS location.';

        if (error.code === 1) {
            errorMessage = 'Location permission denied. Allow location access in your browser.';
        } else if (error.code === 2) {
            errorMessage = 'Your device could not determine its current location.';
        } else if (error.code === 3) {
            errorMessage = 'GPS request timed out. Move to an area with a better GPS signal.';
        }

        setStatus('GPS error');
        setMessage(`❌ ${errorMessage}`);
    }

    function stopTracking() {
        trackingActive = false;

        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
            watchId = null;
        }

        startButton.disabled = false;
        stopButton.disabled = true;

        setStatus('Tracking stopped');
        setMessage('⏹ GPS tracking stopped.');
    }

    async function startTracking() {
        if (trackingActive) {
            return;
        }

        const busNumber = busInput.value.trim();

        if (!busNumber) {
            setStatus('No bus assigned');
            setMessage('❌ No bus is assigned to your account. Ask the administrator to assign one.');
            return;
        }

        if (!navigator.geolocation) {
            setStatus('GPS unavailable');
            setMessage('❌ This browser does not support geolocation.');
            return;
        }

        startButton.disabled = true;
        setStatus('Checking assigned bus...');
        setMessage('');

        try {
            const response = await fetch(
                `/api/buses/${encodeURIComponent(busNumber)}`,
                {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                }
            );

            if (!response.ok) {
                throw new Error(
                    response.status === 404
                        ? `Bus ${busNumber} was not found.`
                        : `Could not verify bus (${response.status}).`
                );
            }

            trackingActive = true;
            lastSentAt = 0;

            setStatus('Waiting for GPS location...');
            setMessage(`Starting GPS tracking for Bus ${busNumber}.`);

            stopButton.disabled = false;

            watchId = navigator.geolocation.watchPosition(
                handlePosition,
                handleLocationError,
                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }
            );

        } catch (error) {
            trackingActive = false;
            startButton.disabled = false;
            stopButton.disabled = true;

            setStatus('Unable to start');
            setMessage(`❌ ${error.message}`);
        }
    }

    startButton.addEventListener('click', startTracking);
    stopButton.addEventListener('click', stopTracking);

    // Stop browser GPS monitoring when leaving the page.
    window.addEventListener('pagehide', () => {
        if (watchId !== null) {
            navigator.geolocation.clearWatch(watchId);
        }
    });
</script>

</body>
</html>