
// TRACKMYBUS PUBLIC TRANSIT
// COMPLETE JAVASCRIPT
// ======================================================


// ======================================================
// API URL
// ======================================================

const API_URL =
    window.location.hostname === "localhost" ||
    window.location.hostname === "127.0.0.1"
        ? ""
        : "https://trackmybus-production-6dde.up.railway.app";


// ======================================================
// PAGE NAVIGATION
// ======================================================

function showSection(sectionId) {

    const section =
        document.getElementById(sectionId);

    if (section) {

        section.scrollIntoView({
            behavior: "smooth"
        });

    }

}


// ======================================================
// NAVIGATION ACTIVE LINK
// ======================================================

const navLinks =
    document.querySelectorAll(".nav-link");


navLinks.forEach(function (link) {

    link.addEventListener("click", function () {

        navLinks.forEach(function (item) {

            item.classList.remove("active");

        });

        this.classList.add("active");

    });

});


// ======================================================
// DEMO LOGIN
// ======================================================

const loginButton =
    document.getElementById("loginButton");


if (loginButton) {

    loginButton.addEventListener(
        "click",
        function () {

            const username =
                document
                    .getElementById("username")
                    .value
                    .trim();


            const password =
                document
                    .getElementById("password")
                    .value
                    .trim();


            const loginResult =
                document.getElementById(
                    "loginResult"
                );


            if (
                username === "" ||
                password === ""
            ) {

                loginResult.innerHTML =
                    "<p>❌ Please enter username and password.</p>";

                return;

            }


            loginResult.innerHTML =
                "<p>✅ Demo login successful!</p>";

        }
    );

}


// ======================================================
// SEARCH BUS
// ======================================================

const searchButton =
    document.getElementById(
        "searchButton"
    );


if (searchButton) {

    searchButton.addEventListener(
        "click",
        async function () {


            const busNumber =
                document
                    .getElementById(
                        "busNumber"
                    )
                    .value
                    .trim();


            const busResult =
                document.getElementById(
                    "busResult"
                );


            if (busNumber === "") {

                busResult.innerHTML =
                    "<p>Please enter a bus number.</p>";

                return;

            }


            busResult.innerHTML =
                "<p>Searching for bus...</p>";


            try {


                const response =
                    await fetch(
                        `${API_URL}/api/buses/${encodeURIComponent(busNumber)}`
                    );


                const data =
                    await response.json();


                if (response.ok) {


                    busResult.innerHTML = `

                        <h3>Bus Details</h3>

                        <p>
                            <strong>Bus Number:</strong>
                            ${data.bus_number}
                        </p>

                        <p>
                            <strong>Route:</strong>
                            ${data.route}
                        </p>

                        <p>
                            <strong>Location:</strong>
                            ${data.location}
                        </p>

                        <p>
                            <strong>Status:</strong>
                            ${data.status}
                        </p>

                    `;


                } else {


                    busResult.innerHTML =
                        `<p>❌ ${data.error || "Bus not found"}</p>`;

                }


            } catch (error) {

                console.error(error);

                busResult.innerHTML =
                    "<p>❌ Unable to connect to the server.</p>";

            }

        }
    );

}


// ======================================================
// GET ALL FIRST 15 BUSES
// ======================================================

async function getBuses() {

    try {


        const response =
            await fetch(
                `${API_URL}/api/buses`
            );


        if (!response.ok) {

            throw new Error(
                "Unable to fetch buses"
            );

        }


        const buses =
            await response.json();


        return buses;


    } catch (error) {

        console.error(error);

        return [];

    }

}


// ======================================================
// LOAD TRACKING BUS DROPDOWN
// ======================================================

async function loadTrackingBuses() {


    const select =
        document.getElementById(
            "trackingBusSelect"
        );


    if (!select) {
        return;
    }


    select.innerHTML =
        `<option value="">
            Select a bus
        </option>`;


    const buses =
        await getBuses();


    if (buses.length === 0) {

        select.innerHTML +=
            `<option value="">
                No buses available
            </option>`;

        return;

    }


    buses.forEach(function (bus) {


        const option =
            document.createElement(
                "option"
            );


        option.value =
            bus.bus_number;


        option.textContent =
            `${bus.bus_number} - ${bus.route}`;


        select.appendChild(option);

    });

}


// ======================================================
// DISPLAY TRACKING BUS
// ======================================================

async function loadTrackingBus(busNumber) {

    // Remove old GPS marker when selecting a bus
    if (busMarker && liveMap) {
        liveMap.removeLayer(busMarker);
        busMarker = null;
    }


    if (!busNumber) {
        return;
    }


    const busNumberElement =
        document.getElementById(
            "trackingBusNumber"
        );


    const locationElement =
        document.getElementById(
            "trackingLocation"
        );


    const routeElement =
        document.getElementById(
            "trackingRoute"
        );


    const statusElement =
        document.getElementById(
            "trackingStatus"
        );


    const resultElement =
        document.getElementById(
            "trackingResult"
        );


    resultElement.innerHTML =
        "<p>Loading bus location...</p>";
    

    try {// SHOW SELECTED BUS ROUTE ON MAP
showBusRouteOnMap(busNumber);


        const response =
            await fetch(
                `${API_URL}/api/buses/${encodeURIComponent(busNumber)}`
            );


        const data =
            await response.json();


        if (!response.ok) {

            resultElement.innerHTML =
                `<p>❌ ${data.error || "Bus not found"}</p>`;

            return;

        }


        // BUS NUMBER

        busNumberElement.textContent =
            data.bus_number;


        // LOCATION

        locationElement.textContent =
            data.location;


        // ROUTE

        routeElement.textContent =
            data.route;


        // STATUS

        statusElement.textContent =
            `● ${data.status}`;


        // REMOVE OLD STATUS CLASSES

        statusElement.classList.remove(
            "on-time",
            "delayed",
            "cancelled"
        );


        // ADD CORRECT STATUS CLASS

        if (
            data.status.toLowerCase() ===
            "on time"
        ) {

            statusElement.classList.add(
                "on-time"
            );

        }

        else if (
            data.status.toLowerCase() ===
            "delayed"
        ) {

            statusElement.classList.add(
                "delayed"
            );

        }

        else if (
            data.status.toLowerCase() ===
            "cancelled"
        ) {

            statusElement.classList.add(
                "cancelled"
            );

        }


        resultElement.innerHTML =
            `<p>✅ Location updated successfully.</p>`;


    } catch (error) {


        console.error(error);


        resultElement.innerHTML =
            "<p>❌ Unable to connect to the server.</p>";

    }

}


// ======================================================
// TRACKING BUS SELECTION
// ======================================================

const trackingBusSelect =
    document.getElementById(
        "trackingBusSelect"
    );


if (trackingBusSelect) {


    trackingBusSelect.addEventListener(
        "change",
        function () {


            const busNumber =
                this.value;


            if (busNumber) {

                loadTrackingBus(
                    busNumber
                );

            }

        }
    );

}


// ======================================================
// REFRESH TRACKING LOCATION
// ======================================================

const refreshTrackingButton =
    document.getElementById(
        "refreshTrackingButton"
    );


if (refreshTrackingButton) {


    refreshTrackingButton.addEventListener(
        "click",
        async function () {


            const busNumber =
                document
                    .getElementById(
                        "trackingBusSelect"
                    )
                    .value;


            const resultElement =
                document.getElementById(
                    "trackingResult"
                );


            if (!busNumber) {

                resultElement.innerHTML =
                    "<p>Please select a bus first.</p>";

                return;

            }


            await loadTrackingBus(
                busNumber
            );

        }
    );

}


// ======================================================
// UPDATE BUS LOCATION
// ======================================================

const updateLocationButton =
    document.getElementById(
        "updateLocationButton"
    );


if (updateLocationButton) {


    updateLocationButton.addEventListener(
        "click",
        async function () {


            const busNumber =
                document
                    .getElementById(
                        "updateBusNumber"
                    )
                    .value
                    .trim();


            const newLocation =
                document
                    .getElementById(
                        "newLocation"
                    )
                    .value
                    .trim();


            const updateResult =
                document.getElementById(
                    "updateResult"
                );


            if (
                busNumber === "" ||
                newLocation === ""
            ) {

                updateResult.innerHTML =
                    "<p>Please enter bus number and new location.</p>";

                return;

            }


            updateResult.innerHTML =
                "<p>Updating location...</p>";


            try {


                const response =
                    await fetch(
                        `${API_URL}/api/buses/${encodeURIComponent(busNumber)}/location`,
                        {

                            method: "PUT",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body:
                                JSON.stringify({
                                    location:
                                        newLocation
                                })

                        }
                    );


                const data =
                    await response.json();


                if (response.ok) {


                    updateResult.innerHTML =
                        `<p>✅ ${data.message}</p>`;


                    // Refresh tracking automatically
                    const selectedBus =
                        document
                            .getElementById(
                                "trackingBusSelect"
                            )
                            .value;


                    if (
                        selectedBus ===
                        busNumber
                    ) {

                        loadTrackingBus(
                            busNumber
                        );

                    }


                } else {


                    updateResult.innerHTML =
                        `<p>❌ ${data.error || "Unable to update location"}</p>`;

                }


            } catch (error) {


                console.error(error);


                updateResult.innerHTML =
                    "<p>❌ Unable to connect to the server.</p>";

            }

        }
    );

}


// ======================================================
// ADD NEW BUS
// ======================================================

const addBusButton =
    document.getElementById(
        "addBusButton"
    );


if (addBusButton) {


    addBusButton.addEventListener(
        "click",
        async function () {


            const busNumber =
                document
                    .getElementById(
                        "addBusNumber"
                    )
                    .value
                    .trim();


            const route =
                document
                    .getElementById(
                        "addRoute"
                    )
                    .value
                    .trim();


            const location =
                document
                    .getElementById(
                        "addLocation"
                    )
                    .value
                    .trim();


            const status =
                document
                    .getElementById(
                        "addStatus"
                    )
                    .value
                    .trim();


            const addResult =
                document.getElementById(
                    "addResult"
                );


            if (
                busNumber === "" ||
                route === "" ||
                location === "" ||
                status === ""
            ) {

                addResult.innerHTML =
                    "<p>Please fill in all fields.</p>";

                return;

            }


            addResult.innerHTML =
                "<p>Adding bus...</p>";


            try {


                const response =
                    await fetch(
                        `${API_URL}/api/buses`,
                        {

                            method: "POST",

                            headers: {
                                "Content-Type":
                                    "application/json"
                            },

                            body:
                                JSON.stringify({

                                    bus_number:
                                        busNumber,

                                    route:
                                        route,

                                    location:
                                        location,

                                    status:
                                        status

                                })

                        }
                    );


                const data =
                    await response.json();


                if (response.ok) {


                    addResult.innerHTML =
                        `<p>✅ ${data.message}</p>`;


                    // Reload dropdown
                    loadTrackingBuses();


                } else {


                    addResult.innerHTML =
                        `<p>❌ ${data.error || "Unable to add bus"}</p>`;

                }


            } catch (error) {


                console.error(error);


                addResult.innerHTML =
                    "<p>❌ Unable to connect to the server.</p>";

            }

        }
    );

}


// ======================================================
// LOAD ROUTES
// ======================================================

async function loadRoutes() {


    const routesContainer =
        document.getElementById(
            "routesContainer"
        );


    if (!routesContainer) {
        return;
    }


    const buses =
        await getBuses();


    if (buses.length === 0) {

        routesContainer.innerHTML =
            "<p>No routes available.</p>";

        return;

    }


    routesContainer.innerHTML = "";


    buses.forEach(function (bus) {


        const card =
            document.createElement(
                "div"
            );


        card.className =
            "route-card";


        card.innerHTML = `

            <h3>
                Bus ${bus.bus_number}
            </h3>

            <p>
                ${bus.route}
            </p>

            <p>
                <strong>Location:</strong>
                ${bus.location}
            </p>

            <p>
                <strong>Status:</strong>
                ${bus.status}
            </p>

        `;


        routesContainer.appendChild(
            card
        );

    });

}


// ======================================================
// BUS STATUS COUNTS
// ======================================================

async function loadBusStatus() {


    const buses =
        await getBuses();


    let onTime = 0;

    let delayed = 0;

    let cancelled = 0;


    buses.forEach(function (bus) {


        const status =
            bus.status.toLowerCase();


        if (status === "on time") {

            onTime++;

        }

        else if (status === "delayed") {

            delayed++;

        }

        else if (status === "cancelled") {

            cancelled++;

        }

    });


    const onTimeElement =
        document.getElementById(
            "onTimeCount"
        );


    const delayedElement =
        document.getElementById(
            "delayedCount"
        );


    const cancelledElement =
        document.getElementById(
            "cancelledCount"
        );


    if (onTimeElement) {

        onTimeElement.textContent =
            onTime;

    }


    if (delayedElement) {

        delayedElement.textContent =
            delayed;

    }


    if (cancelledElement) {

        cancelledElement.textContent =
            cancelled;

    }

}


// ======================================================
// INITIAL LOAD
// ======================================================

document.addEventListener(
    "DOMContentLoaded",
    function () {


        loadTrackingBuses();


        loadRoutes();


        loadBusStatus();



    }
);


// ======================================================
// GPS TRACKING
// ======================================================

let gpsWatchId = null;

// Start GPS Tracking
document.getElementById("startGpsButton").addEventListener("click", function () {

    const busNumber = document.getElementById("trackingBusSelect").value;
    const gpsStatus = document.getElementById("gpsStatus");

    if (!busNumber) {
        gpsStatus.textContent = "Please select a bus first.";
        return;
    }

    if (!navigator.geolocation) {
        gpsStatus.textContent = "GPS is not supported by this browser.";
        return;
    }

    gpsStatus.textContent = "Requesting GPS permission...";

    gpsWatchId = navigator.geolocation.watchPosition(

        function (position) {

            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            updateMapLocation(latitude, longitude, busNumber);

            gpsStatus.textContent =
                `GPS Active | Latitude: ${latitude.toFixed(6)} | Longitude: ${longitude.toFixed(6)}`;

            fetch(`${API_URL}/api/buses/${busNumber}/gps`, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log("GPS sent to server:", data);
            })
            .catch(error => {
                console.error("GPS update error:", error);
                gpsStatus.textContent = "GPS detected, but server update failed.";
            });
        },

        function (error) {

            console.error("GPS error:", error);

            if (error.code === 1) {
                gpsStatus.textContent =
                    "Location permission denied.";
            } else if (error.code === 2) {
                gpsStatus.textContent =
                    "Unable to get your location.";
            } else if (error.code === 3) {
                gpsStatus.textContent =
                    "GPS request timed out.";
            }
        },

        {
            enableHighAccuracy: false,
            maximumAge: 10000,
            timeout: 30000
        }
    );
});


// Stop GPS Tracking
document.getElementById("stopGpsButton").addEventListener("click", function () {

    const gpsStatus = document.getElementById("gpsStatus");

    if (gpsWatchId !== null) {

        navigator.geolocation.clearWatch(gpsWatchId);

        gpsWatchId = null;

        gpsStatus.textContent = "GPS tracking stopped.";
    }
});// ======================================================
// BUS ROUTE MAP LOCATIONS
// ======================================================

const busRoutes = {

    "111A": {
        start: {
            name: "K.R. Market",
            lat: 12.9629,
            lng: 77.5758
        },
        end: {
            name: "Kaval Byrasandra",
            lat: 13.0215,
            lng: 77.6030
        }
    },

    "112": {
        start: {
            name: "Shivajinagar",
            lat: 12.9857,
            lng: 77.6057
        },
        end: {
            name: "Kaval Byrasandra",
            lat: 13.0215,
            lng: 77.6030
        }
    },

    "114": {
        start: {
            name: "K.R. Market",
            lat: 12.9629,
            lng: 77.5758
        },
        end: {
            name: "Sulthanpalya",
            lat: 13.0110,
            lng: 77.6020
        }
    },

    "120": {
        start: {
            name: "Yeshwantpur",
            lat: 13.0285,
            lng: 77.5405
        },
        end: {
            name: "Lingarajapuram",
            lat: 13.0105,
            lng: 77.6250
        }
    },

    "122": {
        start: {
            name: "Kempegowda Bus Station",
            lat: 12.9779,
            lng: 77.5725
        },
        end: {
            name: "Old Baiyappanahalli",
            lat: 13.0015,
            lng: 77.6320
        }
    },

    "131": {
        start: {
            name: "Kempegowda Bus Station",
            lat: 12.9779,
            lng: 77.5725
        },
        end: {
            name: "Domlur",
            lat: 12.9609,
            lng: 77.6387
        }
    },

    "201": {
        start: {
            name: "Srinagar",
            lat: 12.9580,
            lng: 77.5550
        },
        end: {
            name: "Domlur",
            lat: 12.9609,
            lng: 77.6387
        }
    },

    "201-R": {
        start: {
            name: "Central Silk Board",
            lat: 12.9177,
            lng: 77.6238
        },
        end: {
            name: "BEML Factory",
            lat: 12.9670,
            lng: 77.7150
        }
    },

    "202": {
        start: {
            name: "Kumaraswamy Layout",
            lat: 12.9070,
            lng: 77.5590
        },
        end: {
            name: "Yeshwantpur",
            lat: 13.0285,
            lng: 77.5405
        }
    },

    "203": {
        start: {
            name: "JP Nagar 3rd Phase",
            lat: 12.9063,
            lng: 77.5857
        },
        end: {
            name: "Yeshwantpur",
            lat: 13.0285,
            lng: 77.5405
        }
    },

    "210": {
        start: {
            name: "K.R. Market",
            lat: 12.9629,
            lng: 77.5758
        },
        end: {
            name: "Uttarahalli",
            lat: 12.9052,
            lng: 77.5450
        }
    },

    "210A": {
        start: {
            name: "Kempegowda Bus Station",
            lat: 12.9779,
            lng: 77.5725
        },
        end: {
            name: "ISRO Layout",
            lat: 12.8990,
            lng: 77.5700
        }
    },

    "215C": {
        start: {
            name: "K.R. Market",
            lat: 12.9629,
            lng: 77.5758
        },
        end: {
            name: "Jambusavari Dinne",
            lat: 12.8870,
            lng: 77.5700
        }
    },

    "215N": {
        start: {
            name: "Kempegowda Bus Station",
            lat: 12.9779,
            lng: 77.5725
        },
        end: {
            name: "Anjanapura",
            lat: 12.8580,
            lng: 77.5760
        }
    },

    "217": {
        start: {
            name: "K.R. Market",
            lat: 12.9629,
            lng: 77.5758
        },
        end: {
            name: "Sompura",
            lat: 12.8600,
            lng: 77.7000
        }
    }

};
// ======================================================
// SHOW BUS ROUTE ON MAP
// ======================================================

let routeLine = null;
let startMarker = null;
let endMarker = null;
let simulationTimer = null;


// ======================================================
// SHOW REAL ROAD ROUTE + ARTIFICIAL BUS
// ======================================================

async function showBusRouteOnMap(busNumber) {

    const route = busRoutes[busNumber];

    if (!route) {
        console.log("No map route defined for:", busNumber);
        return;
    }

    if (!liveMap) {
        initializeMap();
    }

    // Remove previous route
    if (routeLine) {
        liveMap.removeLayer(routeLine);
        routeLine = null;
    }

    if (startMarker) {
        liveMap.removeLayer(startMarker);
        startMarker = null;
    }

    if (endMarker) {
        liveMap.removeLayer(endMarker);
        endMarker = null;
    }
if (simulatedBusMarker) {
    liveMap.removeLayer(simulatedBusMarker);
    simulatedBusMarker = null;
}
if (simulationTimer) {
    clearInterval(simulationTimer);
    simulationTimer = null;
}

    // OSRM uses longitude,latitude
    const start =
        `${route.start.lng},${route.start.lat}`;

    const end =
        `${route.end.lng},${route.end.lat}`;

    const routingURL =
        `https://router.project-osrm.org/route/v1/driving/${start};${end}?overview=full&geometries=geojson`;

    try {

        const response =
            await fetch(routingURL);

        const data =
            await response.json();

        if (!response.ok || data.code !== "Ok") {

            console.error(
                "Route calculation failed:",
                data
            );

            return;
        }

        // Get road coordinates
        const coordinates =
            data.routes[0].geometry.coordinates;

        // Convert:
        // OSRM [longitude, latitude]
        // to Leaflet [latitude, longitude]

        const roadRoute =
            coordinates.map(function (point) {

                return [
                    point[1],
                    point[0]
                ];

            });


        // ==================================================
        // DRAW ROAD ROUTE
        // ==================================================

        routeLine =
            L.polyline(
                roadRoute,
                {
                    weight: 6
                }
            ).addTo(liveMap);


        // ==================================================
        // START MARKER
        // ==================================================

        startMarker =
            L.marker([
                route.start.lat,
                route.start.lng
            ])
            .addTo(liveMap)
            .bindPopup(
                `🟢 <b>${route.start.name}</b><br>` +
                `Bus ${busNumber} starts here`
            );


        // ==================================================
        // DESTINATION MARKER
        // ==================================================

        endMarker =
            L.marker([
                route.end.lat,
                route.end.lng
            ])
            .addTo(liveMap)
            .bindPopup(
                `🏁 <b>${route.end.name}</b><br>` +
                `Bus ${busNumber} destination`
            );


        // ==================================================
        // ARTIFICIAL BUS
        // ==================================================

        // Put bus approximately halfway
        // along the actual road

        const middleIndex =
            Math.floor(
                roadRoute.length / 2
            );

        const busPosition =
            roadRoute[middleIndex];


        const busIcon =
            L.divIcon({

                className:
                    "custom-bus-marker",

                html:
                    "🚌",

                iconSize:
                    [40, 40],

                iconAnchor:
                    [20, 20]

            });

simulatedBusMarker =
    L.marker(
        busPosition,
        {
            icon: busIcon
        }
    )
    .addTo(liveMap)
    .bindPopup(
        `🚌 <b>Bus ${busNumber}</b><br>` +
        `${route.start.name} → ` +
        `${route.end.name}<br>` +
        `Simulated bus location`
    );
    let busIndex = 0;

if (simulationTimer) {
    clearInterval(simulationTimer);
}

simulationTimer = setInterval(function () {

    if (!simulatedBusMarker) {
        clearInterval(simulationTimer);
        return;
    }

    busIndex++;

    if (busIndex >= roadRoute.length) {
        busIndex = 0;
    }

    simulatedBusMarker.setLatLng(
        roadRoute[busIndex]
    );

}, 100);
        // ==================================================
        // FIT MAP TO COMPLETE ROUTE
        // ==================================================

        liveMap.fitBounds(
            routeLine.getBounds(),
            {
                padding: [30, 30]
            }
        );


        console.log(
            `Road route loaded for bus ${busNumber}`
        );

    }

    catch (error) {

        console.error(
            "Unable to load road route:",
            error
        );

    }
}


// ======================================================
// LIVE GPS MAP
// ======================================================

let liveMap = null;
let busMarker = null;
let simulatedBusMarker = null;

function initializeMap() {

    if (liveMap) return;

    liveMap =
        L.map("map")
        .setView(
            [12.9716, 77.5946],
            13
        );


    L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
            attribution:
                "&copy; OpenStreetMap contributors"
        }
    )
    .addTo(liveMap);


    setTimeout(
        function () {

            liveMap.invalidateSize();

        },
        200
    );
}


function updateMapLocation(
    latitude,
    longitude,
    busNumber
) {

    if (!liveMap) {

        initializeMap();

    }


    const position =
        [
            latitude,
            longitude
        ];


    const busIcon =
        L.divIcon({

            className:
                "custom-bus-marker",

            html:
                "🚌",

            iconSize:
                [40, 40],

            iconAnchor:
                [20, 20]

        });


    if (!busMarker) {

        busMarker =
            L.marker(
                position,
                {
                    icon: busIcon
                }
            )
            .addTo(liveMap)
            .bindPopup(
                `🚌 ${busNumber}`
            );

    }

    else {

        busMarker.setLatLng(
            position
        );

        busMarker.setIcon(
            busIcon
        );

        busMarker
            .getPopup()
            .setContent(
                `🚌 ${busNumber}`
            );

    }


    liveMap.setView(
        position,
        16
    );
}


// ======================================================
// AUTOMATIC LIVE MAP UPDATES
// ======================================================

let liveTrackingInterval = null;


function startLiveMapUpdates() {

    if (liveTrackingInterval) {

        clearInterval(
            liveTrackingInterval
        );

    }


    liveTrackingInterval =
        setInterval(
            async function () {

                const busNumber =
                    document
                    .getElementById(
                        "trackingBusSelect"
                    )
                    .value;


                if (!busNumber) return;


                try {

                    const response =
                        await fetch(
                            `${API_URL}/api/buses/${busNumber}`
                        );


                    const bus =
                        await response.json();


                    if (
                        bus.latitude !== null &&
                        bus.latitude !== undefined &&
                        bus.longitude !== null &&
                        bus.longitude !== undefined
                    ) {

                        updateMapLocation(
                            bus.latitude,
                            bus.longitude,
                            busNumber
                        );

                    }

                }

                catch (error) {

                    console.error(
                        "Live map update error:",
                        error
                    );

                }

            },
            3000
        );
}