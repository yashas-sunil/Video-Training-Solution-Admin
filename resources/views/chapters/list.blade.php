<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Chapters</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
        }

        .navbar {
            background-color: #700002;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            width: -webkit-fill-available;
            z-index: 1000;
        }

        .navbar .logo img {
            height: 55px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info a {
            display: flex;
            gap: 5px;
            color: white;
            text-decoration: none;
            align-items: center;
            border-radius: 30px;
            background: #8D8D8D87;
            border: 3px solid #FFFFFF33;
            padding: 7px 20px;
            box-shadow: 0px 4px 18px 10px #FFFFFF30;
            font-size: 14px;
        }

        .user-welcome {
            display: flex;
            gap: 10px;
            border-radius: 30px;
            background: #8D8D8D87;
            border: 3px solid #FFFFFF33;
            padding: 10px 20px;
            box-shadow: 0px 4px 18px 10px #FFFFFF30;
        }

        .page-content {
            padding: 30px;
            margin-top: 90px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .chapter-list {
            max-width: 90%;
            margin: auto;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: center;
        }

        .chapter-item {
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            border: 2px solid #700002;
            width: 29%;
            cursor: pointer;
            transition: 0.25s ease;
            position: relative;
        }

        .chapter-item:hover {
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .chapter-title {
            font-size: 16px;
            font-weight: 500;
        }

        .progress-bar {
            background: #eee;
            border-radius: 6px;
            height: 8px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 8px;
            border-radius: 6px;
            transition: width 0.4s ease;
        }

        .progress-text {
            font-size: 12px;
            color: #555;
            margin-top: 4px;
        }

        .open-text {
            margin-top: 10px;
            font-size: 14px;
            color: #007bff;
            font-weight: 500;
            display: inline-block;
        }

        .attempt-btn {
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 6px;
            background: #ffffff;
            border: 2px solid #2C2C49;
            cursor: pointer;
            font-size: 14px;
            font-family: sans-serif;
        }

        .attempt-btn.disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
            border: 2px solid #999;
        }

        @media (max-width: 768px) {
            .chapter-item {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="logo">
            <img src="{{ asset('images/logo-2.png') }}">
        </div>

        <div class="user-info">
            <div class="user-welcome">
                <div>Welcome back!</div>
                <div><b>{{ auth()->user()->name }}</b></div>
            </div>

            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                Logout <img src="{{ asset('images/Logout2.png') }}">
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="page-content">

        <button onclick="window.history.back()" class="back-btn">⬅ Back</button>

        <h2>Course Chapters</h2>

        <div class="chapter-list">
            @forelse($chapters as $chapter)
                @php
                    $percent = (float) ($chapter->progress_percent ?? 0);
                    if ($percent > 0 && $percent < 1) {
                        $percent = 1;
                    }
                    $currentAttempt = $chapter->attempt_count ?? 0;
                @endphp

                <div class="chapter-item" onclick="openChapter({{ $chapter->id }})">

                    <div class="chapter-title">{{ $chapter->name }}</div>

                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ $percent }}%;
                                background: {{ $chapter->is_completed ? '#58B100' : '#700002' }};">
                        </div>
                    </div>

                    <div class="progress-text">{{ $percent }}% Complete</div>

                    @php
                        if ($percent >= 100 || $chapter->is_completed) {
                            $btnText = 'Completed ✔';
                        } elseif ($percent > 0) {
                            $btnText = 'Resume →';
                        } else {
                            $btnText = 'Start →';
                        }
                    @endphp

                    <div class="open-text">
                        {{ $btnText }}
                    </div>
                    <div class="attempt-btn" onclick="event.stopPropagation(); showAttempts('{{ $chapter->name }}')">
                        View Attempts
                    </div>
                </div>

            @empty
                <div>No chapters available.</div>
            @endforelse
        </div>
    </div>

    <script>
        function openChapter(chapterId) {
            const w = window.screen.availWidth;
            const h = window.screen.availHeight;

            window.open(
                `/view/chapter/${chapterId}`,
                '_blank',
                `width=${w},height=${h},top=0,left=0,resizable=yes,scrollbars=yes`
            );
        }

        function showAttempts(quizName) {
            window.location.href = `/get-attempts?quiz_name=${encodeURIComponent(quizName)}`;
        }

        function viewAttemptQuestions(index, quizName) {
            const attempt = window.attemptData[index];
            const box = document.getElementById('attemptContent');

            box.innerHTML = `
        <button onclick="showAttempts('${quizName}')" 
            style="margin-bottom:15px; padding:6px 12px; border:none; background:#3498db; color:white; border-radius:4px; cursor:pointer;">
            ⬅ Back
        </button>

        <div style="padding:15px; background:#f7f9fc; border-radius:6px; border:1px solid #ddd; margin-bottom:20px;">
            <p style="margin:4px 0;"><strong>Chapter:</strong> ${attempt.chapter_name}</p>
            <p style="margin:4px 0;">
                <strong>Attempt:</strong> ${attempt.attempt_number} 
                <span style="color:#555; font-size:13px;">(${attempt.attempt_time})</span>
            </p>
            <p style="margin:4px 0;"><strong>Score:</strong> ${attempt.score_percent}%</p>
        </div>

        <div>
            ${attempt.questions.map((q, i) => {
                const isCorrect = q.is_correct ? '✅ Correct' : '❌ Wrong';
                const answerColor = q.is_correct ? '#e6ffed' : '#ffecec';

                return `
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            <div style="background:white; padding:12px; border-radius:6px; border:1px solid #ddd; margin-bottom:10px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="font-weight:bold; margin-bottom:6px;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    Q${i + 1}: ${q.question_id}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="margin:3px 0; padding:6px; border-radius:4px; background:${answerColor};">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    🧍 Your Answer: ${q.user_answer || '-'}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                <div style="margin:3px 0; padding:6px; border-radius:4px; background:#f0f0f0;">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    📌 Correct Answer: ${q.correct_answer || '-'}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        <div style="margin-top:5px; font-weight:bold; color:${q.is_correct ? 'green' : 'red'};">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ${isCorrect}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                `;
            }).join('')}
        </div>
    `;
        }
    </script>

</body>

</html>
