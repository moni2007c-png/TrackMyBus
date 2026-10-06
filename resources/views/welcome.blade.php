<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>TrackMyBus | Public Transit</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f8fc;
            color: #172033;
        }

        /* NAVBAR */
        nav {
            height: 70px;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 7%;
            border-bottom: 1px solid #e5eaf0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1769ff;
        }

        .logo span {
            color: #172033;
        }

        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #4b5563;
            font-size: 15px;
        }

        .nav-links a:hover {
            color: #1769ff;
        }

        .login-btn {
            padding: 10px 20px;
            border: 1px solid #1769ff;
            border-radius: 8px;
            color: #1769ff !important;
        }

        .register-btn {
            padding: 10px 20px;
            background: #1769ff;
            color: white !important;
            border-radius: 8px;
        }

        /* HERO */
        .hero {
            min-height: 530px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 70px 7%;
            gap: 50px;
            background: linear-gradient(135deg, #eef5ff, #ffffff);
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-content h1 {
            font-size: 52px;
            line-height: 1.1;
            margin-bottom: 20px;
        }

        .hero-content h1 span {
            color: #1769ff;
        }

        .hero-content p {
            font-size: 18px;
            color: #667085;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .search-box {
            background: white;
            padding: 10px;
            border-radius: 12px;
            display: flex;
            max-width: 500px;
            box-shadow: 0 8px 30px rgba(23, 105, 255, 0.12);
        }

        .search-box input {
            flex: 1;
            border: none;
            outline: none;
            padding: 15px;
            font-size: 16px;
        }

        .search-box button {
            border: none;
            background: #1769ff;
            color: white;
            padding: 0 25px;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
        }

        .search-box button:hover {
            background: #0d55d8;
        }

        /* BUS CARD */
        .bus-illustration {
            width: 420px;
            height: 300px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bus-icon {
            font-size: 130px;
        }

        /* FEATURES */
        .features {
            padding: 70px 7%;
            text-align: center;
        }

        .features h2 {
            font-size: 34px;
            margin-bottom: 10px;
        }

        .section-subtitle {
            color: #667085;
            margin-bottom: 45px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .feature-card {
            background: white;
            padding: 35px 25px;
            border-radius: 15px;
            border: 1px solid #e5eaf0;
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(23, 105, 255, 0.1);
        }

        .feature-icon {
            font-size: 42px;
            margin-bottom: 18px;
        }

        .feature-card h3 {
            margin-bottom: 12px;
        }

        .feature-card p {
            color: #667085;
            line-height: 1.6;
        }

        /* FOOTER */
        footer {
            background: #172033;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 20px;
        }

        footer p {
            color: #b7c0cf;
            font-size: 14px;
        }

        /* RESPONSIVE */
        @media (max-width: 850px) {
            .hero {
                flex-direction: column;
                text-align: center;
            }

            .search-box {
                margin: auto;
            }

            .bus-illustration {
                width: 100%;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .nav-links {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav>
        <div class="logo">
            🚌 TrackMy<span>Bus</span>
        </div>

        <div class="nav-links">
    <a href="/">Home</a>
    <a href="/routes">Routes</a>
    <a href="/tracking">Live Tracking</a>

    @auth
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" class="login-btn" style="background:white; cursor:pointer;">
                Logout
            </button>
        </form>
    @else
        <a href="/login" class="login-btn">Login</a>
        <a href="/register" class="register-btn">Register</a>
    @endauth
</div>
    </nav>


    <!-- HERO SECTION -->
    <section class="hero">

        <div class="hero-content">

            <h1>
                Track Your Bus.
                <span>Travel Smarter.</span>
            </h1>

            <p>
                Track public buses, view routes and get real-time
                information about your journey with TrackMyBus.
            </p>

            <div class="search-box">
                <input
                    type="text"
                    id="busNumber"
                    placeholder="Enter bus number..."
                >

                <button onclick="searchBus()">
                    Track Bus
                </button>
            </div>

        </div>


        <div class="bus-illustration">
            <div class="bus-icon">
                🚌
            </div>
        </div>

    </section>


    <!-- FEATURES -->
    <section class="features">

        <h2>Everything You Need</h2>

        <p class="section-subtitle">
            Simple and convenient public transit tracking.
        </p>


        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-icon">
                    📍
                </div>

                <h3>Live Tracking</h3>

                <p>
                    View the current location of buses
                    on an interactive map.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🗺️
                </div>

                <h3>Bus Routes</h3>

                <p>
                    Find routes, stops and important
                    information for your journey.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    ⏱️
                </div>

                <h3>Estimated Arrival</h3>

                <p>
                    Get estimated arrival information
                    for your selected bus.
                </p>

            </div>

        </div>

    </section>


    <!-- FOOTER -->
    <footer>

        <p>
            © 2026 TrackMyBus Public Transit. All rights reserved.
        </p>

    </footer>


    <script>

        function searchBus() {

            const busNumber =
                document.getElementById("busNumber").value.trim();

            if (busNumber === "") {

                alert("Please enter a bus number.");

                return;
            }

            alert(
                "Tracking bus " + busNumber +
                " will be available soon!"
            );
        }

    </script>

</body>
</html>