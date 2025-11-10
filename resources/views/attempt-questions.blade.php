<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
        }

        .top-logo-bar {
            padding: 10px 20px;
            background: white;
            border-bottom: 1px solid #ddd;
        }

        .top-logo-bar img {
            height: 40px;
        }

        .navbar {
            background-color: #007bff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar .logo img {
            height: 55px;
            width: auto;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-content {
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .back-btn {
            margin-bottom: 15px;
            padding: 6px 12px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        .summary-box {
            padding: 15px;
            background: #f7f9fc;
            border-radius: 6px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            margin-left: 30px;
            width: 100%;
            max-width: 600px;
            height: 100px;
            box-sizing: border-box;
        }

        .summary-box p {
            font-size: 13px;
            margin: 3px 0;
        }

        .summary-box p strong {
            font-size: 12px;
        }

        /* Questions grid - max 2 columns desktop */
        .questions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            /* Desktop: max 2 columns */
            gap: 20px;
            padding: 10px 30px;
        }

        /* Mobile adjustment */
        @media (max-width: 768px) {
            .questions-grid {
                grid-template-columns: 1fr;
                /* Mobile: 1 column */
            }
        }

        .question-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            box-sizing: border-box;
            width: 100%;
            /* max-width: 600px; */
            min-height: 140px;
        }

        .question-card .q-title {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .answer {
            padding: 8px;
            border-radius: 4px;
            margin: 5px 0;
            font-size: 14px;
        }

        .your-answer {
            background: #ffecec;
            color: #a94442;
        }

        .your-answer.correct {
            background: #e6ffed;
            color: #2e7d32;
        }

        .correct-answer {
            background: #f0f0f0;
            color: #333;
        }

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            position: relative;
        }

        .quiz-title {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            margin: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .user-welcome {
            display: flex;
            align-items: center;
            gap: 10px;
        }
    </style>
</head>

<body>
    <!-- Logo -->
    <div class="navbar">
        <!-- Left: Company Logo -->
        <div class="logo">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
            <img src="{{ asset('images/logo-2.png') }}" alt="Company Logo">

        </div>

        <!-- Right: User Info -->
        <div class="user-info">
            <div class="user-welcome">
                <div style="font-size:16px;">Welcome back !</div>
                <div style="font-size: 24px; font-weight: bold;">{{ auth()->user()->name }}</div>
            </div>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                style="display: flex; gap: 5px; color:white; text-decoration: none; margin-left: 20px;">Logout<img
                    src="{{ asset('images/logout.png') }}" alt="Logout"></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="page-content">
        <div class="toolbar">
            <!-- Left: Quiz Attempt -->
            <h2 class="quiz-left">Quiz Attempt</h2>

            <!-- Center: Quiz Name -->
            <h2 class="quiz-center">{{ $quizName }}</h2>

            <!-- Right: Back Button -->
            <button onclick="window.history.back()" class="back-btn">⬅ Back</button>
        </div>
    </div>


    <!-- Summary -->
    <div class="summary-box">
        <p><strong>Chapter:</strong> {{ $attempt['chapter_name'] }}</p>
        <p><strong>Attempt:</strong> {{ $attempt['attempt_number'] }}
            <span style="font-size:12px; color:#777;">({{ $attempt['attempt_time'] }})</span>
        </p>
        <p><strong>Score:</strong> {{ $attempt['score_percent'] }}%</p>
    </div>

    <!-- Questions -->
    <div class="questions-grid">
        @foreach($attempt['questions'] as $index => $q)
        <div class="question-card">
            <div class="q-title">Q{{ $index+1 }}: {{ $q['question_id'] }}</div>

            <div class="answer your-answer {{ $q['is_correct'] ? 'correct' : '' }}">
                ✍️ Your Answer: {{ $q['user_answer'] ?: '-' }}
            </div>

            <div class="answer correct-answer">
                ✅ Correct Answer: {{ $q['correct_answer'] ?: '-' }}
            </div>
        </div>
        @endforeach
    </div>
    </div>
</body>

</html>