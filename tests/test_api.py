import pytest
from app import app, init_db


@pytest.fixture
def client():
    app.config["TESTING"] = True

    with app.test_client() as client:
        init_db()
        yield client


def test_home(client):
    response = client.get("/")
    assert response.status_code == 200


def test_add_bus(client):
    response = client.post(
        "/api/buses",
        json={
            "bus_number": "500",
            "route": "Chennai Central - Tambaram",
            "location": "Guindy",
            "status": "On Time"
        }
    )

    assert response.status_code == 201



