// Railway backend URL
const API_URL = "https://trackmybus-production-6dde.up.railway.app";


// =========================
// SEARCH BUS
// =========================

document.getElementById("searchButton").addEventListener("click", async function () {

    const busNumber = document.getElementById("busNumber").value.trim();
    const busResult = document.getElementById("busResult");

    // Empty input
    if (busNumber === "") {
        busResult.innerHTML = "<p>Please enter a bus number.</p>";
        return;
    }

    // Loading message
    busResult.innerHTML = "<p>Searching for bus...</p>";

    try {

        const response = await fetch(
            `${API_URL}/api/buses/${busNumber}`
        );

        const data = await response.json();

        if (response.ok) {

            busResult.innerHTML = `
                <h3>Bus Details</h3>
                <p><strong>Bus Number:</strong> ${data.bus_number}</p>
                <p><strong>Route:</strong> ${data.route}</p>
                <p><strong>Location:</strong> ${data.location}</p>
                <p><strong>Status:</strong> ${data.status}</p>
            `;

        } else {

            busResult.innerHTML = `
                <p>❌ ${data.error || "Bus not found"}</p>
            `;
        }

    } catch (error) {

        console.error(error);

        busResult.innerHTML = `
            <p>❌ Unable to connect to the server.</p>
        `;
    }

});


// =========================
// UPDATE BUS LOCATION
// =========================

document.getElementById("updateLocationButton").addEventListener("click", async function () {

    const busNumber = document.getElementById("updateBusNumber").value.trim();
    const newLocation = document.getElementById("newLocation").value.trim();
    const updateResult = document.getElementById("updateResult");

    // Check empty fields
    if (busNumber === "" || newLocation === "") {

        updateResult.innerHTML =
            "<p>Please enter bus number and new location.</p>";

        return;
    }

    // Loading message
    updateResult.innerHTML =
        "<p>Updating location...</p>";

    try {

        const response = await fetch(
            `${API_URL}/api/buses/${busNumber}/location`,
            {
                method: "PUT",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    location: newLocation
                })
            }
        );

        const data = await response.json();

        if (response.ok) {

            updateResult.innerHTML =
                `<p>✅ ${data.message}</p>`;

        } else {

            updateResult.innerHTML =
                `<p>❌ ${data.error || "Unable to update location"}</p>`;
        }

    } catch (error) {

        console.error(error);

        updateResult.innerHTML =
            "<p>❌ Unable to connect to the server.</p>";
    }

});


// =========================
// ADD NEW BUS
// =========================

document.getElementById("addBusButton").addEventListener("click", async function () {

    const busNumber = document.getElementById("addBusNumber").value.trim();
    const route = document.getElementById("addRoute").value.trim();
    const location = document.getElementById("addLocation").value.trim();
    const status = document.getElementById("addStatus").value.trim();
    const addResult = document.getElementById("addResult");

    // Check empty fields
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

    // Loading message
    addResult.innerHTML =
        "<p>Adding bus...</p>";

    try {

        const response = await fetch(
            `${API_URL}/api/buses`,
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                body: JSON.stringify({
                    bus_number: busNumber,
                    route: route,
                    location: location,
                    status: status
                })
            }
        );

        const data = await response.json();

        if (response.ok) {

            addResult.innerHTML =
                `<p>✅ ${data.message}</p>`;

        } else {

            addResult.innerHTML =
                `<p>❌ ${data.error || "Unable to add bus"}</p>`;
        }

    } catch (error) {

        console.error(error);

        addResult.innerHTML =
            "<p>❌ Unable to connect to the server.</p>";
    }

});