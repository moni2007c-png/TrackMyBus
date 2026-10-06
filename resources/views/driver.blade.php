<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Driver Panel | TrackMyBus</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        nav {
            background: #0f172a;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
        }

        nav a:hover {
            color: #38bdf8;
        }

        .container {
            width: 86%;
            max-width: 800px;
            margin: 40px auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin-bottom: 10px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 16px;
        }

        button {
            margin-top: 25px;
            padding: 13px 22px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background: #1d4ed8;
        }

        button:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        .location-box {
            margin-top: 25px;
            padding: 18px;
            background: #eff6ff;
            border-radius: 10px;
        }

        .location-box p {
            margin: 8px 0;
        }

        .message {
            margin-top: 20px;
            font-weight: bold;
        }

        .gps-status {
            margin-top: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
        }

        .gps-status span {
            font-weight: bold;
        }
    </style>
</head>

<body>

<nav>

    <div class="logo">
        🚌 TrackMyBus
    </div>

    <div>
        <a href="/welcome">Home</a>
        <a href="/tracking">Live Tracking</a>
    </div>

</nav>


<div class="container">

    <div class="card">

        <h1>Driver Panel</h1>

        <p class="subtitle">
            Share your live GPS location with TrackMyBus.
        </p>


        <label for="busNumber">
            Bus Number
        </label>

        <input
    type="text"
    id="busNumber"
    value="{{ auth()->user()->bus_number }}"
    placeholder="Assigned bus number"
    readonly
>


        <button
            id="startButton"
            onclick="startTracking()"
        >
            Start GPS Tracking
        </button>


        <button
            id="stopButton"
            onclick="stopTracking()"
            disabled
        >
            Stop Tracking
        </button>


        <div class="gps-status">

            GPS Status:
            <span id="gpsStatus">
                Not started
            </span>

        </div>


        <div class="location-box">

            <h3>
                Current Location
            </h3>

            <p>
                Latitude:
                <strong id="latitude">
                    Waiting...
                </strong>
            </p>

            <p>
                Longitude:
                <strong id="longitude">
                    Waiting...
                </strong>
            </p>

            <p>
                Last Update:
                <strong id="lastUpdate">
                    Waiting...
                </strong>
            </p>

        </div>


        <div
            id="message"
            class="message">
        </div>

    </div>

</div>


<script>

let trackingInterval = null;

let currentLatitude = null;
let currentLongitude = null;


// Start GPS tracking
async function startTracking() {

    const busNumber =
        document.getElementById('busNumber').value.trim();

    const message =
        document.getElementById('message');

    const gpsStatus =
        document.getElementById('gpsStatus');


    if (!busNumber) {

        message.textContent =
            '❌ Please enter a bus number.';

        return;
    }


    // Check whether the bus exists
    try {

        gpsStatus.textContent =
            'Checking bus number...';

        const response =
            await fetch(`/api/buses/${busNumber}`);

        const data =
            await response.json();


        if (!response.ok) {

            throw new Error(
                data.message ||
                'Bus not found.'
            );
        }


    } catch (error) {

        gpsStatus.textContent =
            'Bus not found';

        message.textContent =
            '❌ ' + error.message;

        return;
    }


    // Check browser GPS support
    if (!navigator.geolocation) {

        message.textContent =
            '❌ GPS is not supported by this browser.';

        return;
    }


    gpsStatus.textContent =
        'Requesting GPS permission...';


    // Get location immediately
    updateGPSLocation();


    // Update every 30 seconds
    trackingInterval = setInterval(
        updateGPSLocation,
        30000
    );


    document.getElementById('startButton')
        .disabled = true;

    document.getElementById('stopButton')
        .disabled = false;


    gpsStatus.textContent =
        'Tracking every 30 seconds';

}


// Stop GPS tracking
function stopTracking() {

    if (trackingInterval) {

        clearInterval(trackingInterval);

        trackingInterval = null;
    }


    document.getElementById('startButton')
        .disabled = false;

    document.getElementById('stopButton')
        .disabled = true;


    document.getElementById('gpsStatus')
        .textContent =
        'Tracking stopped';


    document.getElementById('message')
        .textContent =
        '⏹ GPS tracking stopped.';
}


// Get driver's GPS location
function updateGPSLocation() {

    const busNumber =
        document.getElementById('busNumber').value.trim();


    const message =
        document.getElementById('message');


    if (!busNumber) {

        message.textContent =
            '❌ Please enter a bus number.';

        return;
    }


    navigator.geolocation.getCurrentPosition(

        async (position) => {

            currentLatitude =
                position.coords.latitude;

            currentLongitude =
                position.coords.longitude;


            // Display GPS coordinates
            document.getElementById('latitude')
                .textContent =
                currentLatitude.toFixed(6);


            document.getElementById('longitude')
                .textContent =
                currentLongitude.toFixed(6);


            try {

                const response =
                    await fetch(
                        `/api/buses/${busNumber}/location`,
                        {
                            method: 'PUT',

                            headers: {
                                'Content-Type':
                                    'application/json',

                                'Accept':
                                    'application/json'
                            },

                            body: JSON.stringify({

                                latitude:
                                    currentLatitude,

                                longitude:
                                    currentLongitude

                            })
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Location update failed.'
                    );
                }


                document.getElementById('lastUpdate')
                    .textContent =
                    new Date().toLocaleTimeString();


                document.getElementById('gpsStatus')
                    .textContent =
                    'GPS location sent successfully';


                message.textContent =
                    '✅ Bus location updated successfully!';


            } catch (error) {

                message.textContent =
                    '❌ ' + error.message;

            }

        },


        (error) => {

            let errorMessage =
                'Unable to get GPS location.';


            if (error.code === 1) {

                errorMessage =
                    '❌ Location permission denied. Please allow location access.';

            } else if (error.code === 2) {

                errorMessage =
                    '❌ GPS location is unavailable.';

            } else if (error.code === 3) {

                errorMessage =
                    '❌ GPS request timed out.';

            }


            document.getElementById('gpsStatus')
                .textContent =
                'GPS error';


            document.getElementById('message')
                .textContent =
                errorMessage;

        },


        {
            enableHighAccuracy: true,

            timeout: 10000,

            maximumAge: 0
        }

    );

}

</script>

</body>

</html>