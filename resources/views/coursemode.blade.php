<!DOCTYPE html>
<html>
<head>
    <title>Select Mode</title>
    <style>
        body {
            background: #f7f9fc;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .mode-btn {
            display: block;
            width: 200px;
            margin: 15px auto;
            padding: 15px;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .study-btn { background: #007bff; }
        .test-btn { background: #28a745; }

        .mode-btn:hover { opacity: 0.85; }
    </style>
</head>

<body>
    <div class="box">
        <h2>Select Mode</h2>

        <a class="mode-btn study-btn" href="/view/{{ $courseId }}">
            Study
        </a>

        <a class="mode-btn test-btn" href="/user/{{ $courseId }}">
            Test
        </a>
    </div>
</body>
</html>
