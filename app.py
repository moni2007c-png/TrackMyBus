from flask import Flask, request, jsonify, send_from_directory
from flask_cors import CORS
import sqlite3

app = Flask(__name__)
CORS(app)

DATABASE = "buses.db"


def get_db():
    conn = sqlite3.connect(DATABASE)
    conn.row_factory = sqlite3.Row
    return conn


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

    conn.commit()
    conn.close()


@app.route("/")
def home():
    return send_from_directory(".", "index.html")


@app.route("/api/buses/<bus_number>", methods=["GET"])
def get_bus(bus_number):
    conn = get_db()

    bus = conn.execute(
        "SELECT * FROM buses WHERE bus_number = ?",
        (bus_number,)
    ).fetchone()

    conn.close()

    if bus is None:
        return jsonify({
            "error": "Bus not found"
        }), 404

    return jsonify(dict(bus))


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
            (bus_number, route, location, status)
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
        (data["location"], bus_number)
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


if __name__ == "__main__":
    init_db()
    app.run(debug=True)