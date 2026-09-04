// TrackMyBus - Basic JavaScript

// Track Bus button
const trackButton = document.querySelector(".hero button");

trackButton.addEventListener("click", function () {
    alert("Bus tracking feature will be available soon!");
});


// Search Bus
const searchButton = document.querySelector(".search button");
const busInput = document.querySelector(".search input");

searchButton.addEventListener("click", function () {

    const busNumber = busInput.value.trim();

    if (busNumber === "") {
        alert("Please enter a bus number.");
    } else {
        alert("Searching for bus: " + busNumber);
    }

});


// Allow Enter key to search
busInput.addEventListener("keypress", function (event) {

    if (event.key === "Enter") {
        searchButton.click();
    }

});