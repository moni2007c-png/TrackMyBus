from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import sqlite3

app = Flask(__name__)
CORS(app)

DATABASE = "buses.db"


# ======================================================
# DATABASE CONNECTION
# ======================================================

def get_db():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row
    return conn


# ======================================================
# CREATE DATABASE TABLE
# ======================================================

def init_db():
    conn = get_db()

    conn.execute("""
        CREATE TABLE IF NOT EXISTS buses (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            bus_number TEXT NOT NULL UNIQUE,
            route TEXT NOT NULL,
            location TEXT NOT NULL,
            status TEXT NOT NULL
        )
    """)

    # Add GPS columns if they don't already exist
    columns = conn.execute("PRAGMA table_info(buses)").fetchall()
    column_names = [column["name"] for column in columns]

    if "latitude" not in column_names:
        conn.execute("ALTER TABLE buses ADD COLUMN latitude REAL")

    if "longitude" not in column_names:
        conn.execute("ALTER TABLE buses ADD COLUMN longitude REAL")

    if "gps_updated_at" not in column_names:
        conn.execute("ALTER TABLE buses ADD COLUMN gps_updated_at TEXT")

    conn.commit()
    conn.close()


# ======================================================
# HOME PAGE
# ======================================================

@app.route("/")
def home():
    return send_from_directory(".", "index.html")


# ======================================================
# FRONTEND FILES
# ======================================================

@app.route("/script.js")
def javascript():
    return send_from_directory(".", "script.js")


@app.route("/style.css")
def stylesheet():
    return send_from_directory(".", "style.css")


# ======================================================
# GET ALL BUSES
# ======================================================

@app.route("/api/buses", methods=["GET"])
def get_all_buses():

    conn = get_db()

    buses = conn.execute("""
        SELECT *
        FROM buses
        ORDER BY id
        LIMIT 15
    """).fetchall()

    conn.close()

    return jsonify([dict(bus) for bus in buses])


# ======================================================
# GET SINGLE BUS
# ======================================================

@app.route("/api/buses/<bus_number>", methods=["GET"])
def get_bus(bus_number):

    conn = get_db()

    bus = conn.execute(
        """
        SELECT *
        FROM buses
        WHERE bus_number = ?
        """,
        (bus_number,)
    ).fetchone()

    conn.close()

    if bus is None:
        return jsonify({
            "error": "Bus not found"
        }), 404

    return jsonify(dict(bus))


# ======================================================
# ADD NEW BUS
# ======================================================

@app.route("/api/buses", methods=["POST"])
def add_bus():

    data = request.get_json()

    if not data:
        return jsonify({
            "error": "JSON data is required"
        }), 400

    bus_number = data.get("bus_number")
    route = data.get("route")
    location = data.get("location")
    status = data.get("status")

    if not all([bus_number, route, location, status]):
        return jsonify({
            "error": "All fields are required"
        }), 400

    conn = get_db()

    try:

        conn.execute(
            """
            INSERT INTO buses
            (bus_number, route, location, status)
            VALUES (?, ?, ?, ?)
            """,
            (
                bus_number,
                route,
                location,
                status
            )
        )

        conn.commit()

    except sqlite3.IntegrityError:

        conn.close()

        return jsonify({
            "error": "Bus already exists"
        }), 409

    conn.close()

    return jsonify({
        "message": "Bus added successfully"
    }), 201


# ======================================================
# UPDATE BUS LOCATION
# ======================================================

@app.route("/api/buses/<bus_number>/location", methods=["PUT"])
def update_location(bus_number):

    data = request.get_json()

    if not data or "location" not in data:

        return jsonify({
            "error": "Location is required"
        }), 400

    conn = get_db()

    cursor = conn.execute(
        """
        UPDATE buses
        SET location = ?
        WHERE bus_number = ?
        """,
        (
            data["location"],
            bus_number
        )
    )

    conn.commit()

    if cursor.rowcount == 0:

        conn.close()

        return jsonify({
            "error": "Bus not found"
        }), 404

    conn.close()

    return jsonify({
        "message": "Bus location updated successfully"
    })


# ======================================================
# UPDATE GPS LOCATION
# ======================================================

@app.route("/api/buses/<bus_number>/gps", methods=["PUT"])
def update_gps(bus_number):

    data = request.get_json()

    if not data:
        return jsonify({
            "error": "GPS data is required"
        }), 400

    latitude = data.get("latitude")
    longitude = data.get("longitude")

    if latitude is None or longitude is None:
        return jsonify({
            "error": "Latitude and longitude are required"
        }), 400

    conn = get_db()

    cursor = conn.execute(
        """
        UPDATE buses
        SET latitude = ?,
            longitude = ?,
            gps_updated_at = datetime('now')
        WHERE bus_number = ?
        """,
        (
            latitude,
            longitude,
            bus_number
        )
    )

    conn.commit()

    if cursor.rowcount == 0:

        conn.close()

        return jsonify({
            "error": "Bus not found"
        }), 404

    conn.close()

    return jsonify({
        "message": "GPS location updated successfully",
        "bus_number": bus_number,
        "latitude": latitude,
        "longitude": longitude
    })


# ======================================================
# START APPLICATION
# ======================================================

init_db()


if __name__ == "__main__":

    app.run(
        host="0.0.0.0",
        port=5000,
        debug=True
    )