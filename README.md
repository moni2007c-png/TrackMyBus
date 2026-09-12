# TrackMyBus Public Transit

TrackMyBus is a web application that helps users search for bus information such as route, location, and status.

## Technologies Used

- HTML
- CSS
- JavaScript
- Python
- Flask
- SQLite
- Pytest
- Postman
- Git & GitHub

## Features

- Search bus by bus number
- Get bus details
- Add bus information
- Update bus location
- Store bus data in SQLite database
- Unit testing for API endpoints

## Backend API

### GET /
Checks whether the API is running.

### POST /api/buses
Adds a new bus.

Example:
```json
{
    "bus_number": "111",
    "route": "Chennai Central - Tambaram",
    "location": "Guindy",
    "status": "On Time"
}
