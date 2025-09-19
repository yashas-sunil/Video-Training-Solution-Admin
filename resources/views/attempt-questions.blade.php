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
            background: #007bff;
            color: white;
            padding: 10px 20px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
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
            background: #3498db;
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
            grid-template-columns: repeat(2, 1fr); /* Desktop: max 2 columns */
            gap: 20px;
        }

        /* Mobile adjustment */
        @media (max-width: 768px) {
            .questions-grid {
                grid-template-columns: 1fr; /* Mobile: 1 column */
            }
        }

        .question-card {
            background: white;
            border: 1px solid #ddd;   
            border-radius: 6px;       
            padding: 15px;
            box-sizing: border-box;
            width: 100%;
            max-width: 600px;
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
    </style>
</head>
<body>
    <!-- Logo -->
    <div class="top-logo-bar">
        <img src="{{ asset('images/logo.png') }}" alt="Company Logo">
    </div>

    <!-- Navbar -->
    <div class="navbar">
        <div>
            Welcome back, <strong>{{ auth()->user()->name }}</strong>
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               style="color:white; margin-left:15px; text-decoration:underline;">Logout</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="page-content">
        <div style="text-align: right; margin-bottom: 15px;">
            <button onclick="window.history.back()" class="back-btn">⬅ Back</button>
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
                        🧍 Your Answer: {{ $q['user_answer'] ?: '-' }}
                    </div>

                    <div class="answer correct-answer">
                        📌 Correct Answer: {{ $q['correct_answer'] ?: '-' }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
