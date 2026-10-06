<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Bus Routes - TrackMyBus</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        /* Navbar */
        .navbar {
            background: #0f172a;
            padding: 18px 8%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 25px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: #38bdf8;
        }

        .login-btn {
            background: white;
            color: #0f172a !important;
            padding: 9px 18px;
            border-radius: 6px;
        }

        /* Header */
        .page-header {
            text-align: center;
            padding: 55px 20px 35px;
        }

        .page-header h1 {
            font-size: 36px;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .page-header p {
            color: #64748b;
            font-size: 16px;
        }

        /* Bus container */
        .bus-container {
            width: 85%;
            max-width: 1100px;
            margin: 0 auto 60px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }

        .bus-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #e5e7eb;
        }

        .bus-number {
            font-size: 22px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 15px;
        }

        .route {
            color: #475569;
            margin-bottom: 15px;
            line-height: 1.5;
        }

        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .on-time {
            background: #dcfce7;
            color: #166534;
        }

        .delayed {
            background: #fee2e2;
            color: #991b1b;
        }

        .track-btn {
            display: inline-block;
            width: 100%;
            text-align: center;
            padding: 11px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .track-btn:hover {
            background: #1d4ed8;
        }

        .loading {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #64748b;
        }

        .error {
            text-align: center;
            grid-column: 1 / -1;
            padding: 40px;
            color: #dc2626;
        }

        /* Footer */
        footer {
            background: #0f172a;
            color: #cbd5e1;
            text-align: center;
            padding: 20px;
        }

        @media (max-width: 700px) {
            .navbar {
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }

            .page-header h1 {
                font-size: 28px;
            }

            .bus-container {
                width: 92%;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar">

        <div class="logo">
            🚌 TrackMyBus
        </div>

        <div class="nav-links">

            <a href="/">Home</a>

            <a href="/routes">Routes</a>

            <a href="/tracking">Live Tracking</a>

            @auth

                <form method="POST"
                      action="{{ route('logout') }}"
                      style="display:inline;">

                    @csrf

                    <button type="submit"
                            class="login-btn"
                            style="border:none; cursor:pointer;">
                        Logout
                    </button>

                </form>

            @else

                <a href="/login" class="login-btn">
                    Login
                </a>

            @endauth

        </div>

    </nav>


    <!-- Page Header -->
    <section class="page-header">

        <h1>Available Bus Routes</h1>

        <p>
            View available buses, routes and their current status.
        </p>

    </section>


    <!-- Bus Cards -->
    <section class="bus-container" id="busContainer">

        <div class="loading">
            Loading bus routes...
        </div>

    </section>


    <!-- Footer -->
    <footer>

        <p>
            © 2026 TrackMyBus Public Transit
        </p>

    </footer>


    <script>

        async function loadBuses() {

            const container = document.getElementById('busContainer');

            try {

                const response = await fetch('/api/buses');
                href="/tracking?bus=${bus.bus_number}"

                if (!response.ok) {
                    throw new Error('Failed to load buses');
                }

                const buses = await response.json();

                container.innerHTML = '';

                if (buses.length === 0) {

                    container.innerHTML = `
                        <div class="loading">
                            No buses available currently.
                        </div>
                    `;

                    return;
                }

                buses.forEach(bus => {

                    const statusClass =
                        bus.status === 'On Time'
                            ? 'on-time'
                            : 'delayed';

                    const card = document.createElement('div');

                    card.className = 'bus-card';

                    card.innerHTML = `

                        <div class="bus-number">
                            🚌 Bus ${bus.bus_number}
                        </div>

                        <div class="route">
                            <strong>Route:</strong><br>
                            ${bus.route}
                        </div>

                        <span class="status ${statusClass}">
                            ${bus.status}
                        </span>

                        <a
                            href="/tracking?bus=${bus.bus_number}"
                            class="track-btn">
                            Track This Bus
                        </a>

                    `;

                    container.appendChild(card);

                });

            } catch (error) {

                console.error(error);

                container.innerHTML = `
                    <div class="error">
                        Unable to load bus routes.
                        Please try again.
                    </div>
                `;

            }

        }


        // Load buses when page opens
        loadBuses();

    </script>

</body>
</html>