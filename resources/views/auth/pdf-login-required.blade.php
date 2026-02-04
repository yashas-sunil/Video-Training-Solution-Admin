<!DOCTYPE html>
<html>
<head>
    <title>Login Required</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef1f4;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .wrapper {
            text-align: center;
            width: 100%;
            max-width: 420px;
        }

        .brand {
            margin-bottom: 20px;
        }

        .brand img {
            max-width: 220px;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        h2 {
            margin: 0 0 10px;
        }

        p {
            color: #666;
            margin-bottom: 25px;
        }

        a.button {
            display: inline-block;
            width: 100%;
            background: #0d6efd;
            color: #fff;
            padding: 12px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
        }

        a.button:hover {
            background: #0b5ed7;
        }
    </style>
</head>
<body>

    <div class="wrapper">

        <!-- ✅ TOP LOGO (same like your image) -->
        <div class="brand">
            <img src="{{ asset('images/logo-2.png') }}" alt="EduEdge Pro">
        </div>

        <!-- ✅ CARD -->
        <div class="card">
            <h2>Login Required</h2>
            <p>Please sign in to access this document.</p>

            <a class="button" href="{{ route('login', ['redirect' => request('redirect')]) }}">
                Sign In
            </a>
        </div>

    </div>

</body>
</html>
