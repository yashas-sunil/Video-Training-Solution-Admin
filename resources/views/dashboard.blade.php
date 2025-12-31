<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            background: #eef2f7;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: white;
            padding: 18px 30px;
            font-size: 20px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .container {
            margin-top: 80px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 35px;
        }

        .card-wrapper {
            display: flex;
            gap: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .dash-card {
            width: 280px;
            background: white;
            border-radius: 14px;
            padding: 35px 25px;
            text-align: center;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: 0.2s;
        }

        .dash-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.15);
        }

        .dash-card h3 {
            margin-top: 15px;
            font-size: 22px;
            color: #222;
        }

        .dash-card p {
            color: #555;
            margin-top: 8px;
            font-size: 15px;
        }
    </style>
</head>

<body>

    <div class="navbar">
        Welcome, {{ $user['name'] ?? 'User' }}
    </div>

    <div class="container">
        <h2>Choose Your Mode</h2>

        <div class="card-wrapper">

            <!-- LEARNING -->
            <a href="{{ route('user.dashboard') }}" style="text-decoration:none; color:inherit;">
                <div class="dash-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/2983/2983788.png" width="70">
                    <h3>Learning</h3>
                    <p>Read & Learn with concepts, chapters & material.</p>
                </div>
            </a>

            <!-- TEST -->
            <a href="{{ route('user') }}" style="text-decoration:none; color:inherit;">
                <div class="dash-card">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" width="70">
                    <h3>Test Mode</h3>
                    <p>Attempt questions and take the exam.</p>
                </div>
            </a>

        </div>
    </div>

</body>

</html>
