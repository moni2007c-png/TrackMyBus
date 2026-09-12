document.getElementById("searchButton").addEventListener("click", async function () {

    const busNumber = document.getElementById("busNumber").value;

    if (busNumber === "") {
        alert("Please enter a bus number");
        return;
    }

    try {
        const response = await fetch(
            `http://127.0.0.1:5000/api/buses/${busNumber}`
        );

        const data = await response.json();

        if (response.ok) {
            alert(
                "Bus: " + data.bus_number +
                "\nRoute: " + data.route +
                "\nLocation: " + data.location +
                "\nStatus: " + data.status
            );
        } else {
            alert(data.error);
        }

    } catch (error) {
        alert("Unable to connect to the server");
    }
});



