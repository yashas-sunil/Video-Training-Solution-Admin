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
            top: 0px;
            width: 100%;
            left: 0;
            box-sizing: border-box;
            z-index: 1;
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

        .user-welcome {
            display: flex;
            align-items: center;
            gap: 10px;
            opacity: 1;
            border-radius: 30px;
            border-width: 3px;
            background: #8D8D8D87;
            border: 3px solid #FFFFFF33;
            padding: 10px 20px;
            box-shadow: 0px 4px 18px 10px #FFFFFF30;
        }

        .user-info a {
            display: flex;
            gap: 5px;
            color: white;
            text-decoration: none;
            margin-left: 20px;
            align-items: center;
            border-radius: 30px;
            border-width: 3px;
            background: #8D8D8D87;
            border: 3px solid #FFFFFF33;
            padding: 7px 20px;
            box-shadow: 0px 4px 18px 10px #FFFFFF30;
            font-size: 14px;
        }

        .user-info-mobile {
            display: none;
        }

        .page-content {
            padding: 30px;
            margin-top: 90px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .filter-container {
            max-width: 90%;
            margin: 20px auto;
            display: flex;
            gap: 15px;
            align-items: center;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .filter-container label {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .filter-container select {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            background: white;
            min-width: 200px;
        }

        .filter-container select:focus {
            outline: none;
            border-color: #700002;
            box-shadow: 0 0 0 3px rgba(112, 0, 2, 0.1);
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
            flex: 1;
            background: #D9D9D9;
            border-radius: 5px;
            height: 10px;
            overflow: hidden;
            position: relative;
            box-shadow: 0px 4px 5px -3px #00000040 inset;
            margin: 8px 0px;
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
            text-align: end;
        }
        .list_buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .open-text {
            color: #ffffff;
            margin-top: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-family: sans-serif;
            background: #2C2C49;
        
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
         @media (max-width: 425px) {

            .navbar {
                padding: 15px 6px !important;
            }

            .navbar .logo img {
                height: 30px !important;
                width: auto !important;
            }

            .navbar .user-info {
                gap: 8px !important;
            }

            .user-welcome {
                gap: 4px !important;
                padding: 6px 10px !important;
            }

            .user-info a {
                margin-left: 0px !important;
                padding: 3px 10px !important;
                font-size: 12px !important;
            }

            .user-info-mobile {
                display: flex !important;
                gap: 8px !important;
            }

            .user-welcome {
                gap: 4px !important;
                padding: 6px 10px !important;
            }

            .user-info a {
                margin-left: 0px !important;
                padding: 3px 10px !important;
                font-size: 12px !important;
            }

            .user-info-mobile a {
                color: white !important;
                text-decoration: none !important;
            }

            .user-info {
                display: none !important;
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
        <div class="user-info-mobile">
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="user-welcome">
                        <div style="font-size: 12px;">Welcome back !</div>
                        <div style="font-size: 12px;font-weight: bold;">{{ auth()->user()->name }}</div>
                        <img src="{{ asset('images/Logout2.png') }}" alt="Logout" style="margin-left: 5px;">
                    </div>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
    </div>

    <div class="page-content">

        <button onclick="window.history.back()" class="back-btn">⬅ Back</button>

        <h2>Course Chapters</h2>

        <div class="filter-container">
            <label for="subjectFilter">Filter by Subject:</label>
            <select id="subjectFilter" onchange="filterChaptersBySubject()">
                <option value="">-- All Subjects --</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                @endforeach
            </select>
        </div>
        

        <div class="chapter-list" id="chapterListContainer">
            @forelse($chapters as $chapter)
                @php
                    $percent = (float) ($chapter->progress_percent ?? 0);
                    if ($percent > 0 && $percent < 1) {
                        $percent = 1;
                    }
                    $currentAttempt = $chapter->attempt_count ?? 0;
                @endphp

                <div class="chapter-item" onclick="openChapter({{ $chapter->id }})" data-subject-id="{{ $chapter->subject_id }}">

                    <div class="chapter-title">{{ $chapter->name }}</div>

                    <div class="progress-text">{{ $percent }}% Complete</div>

                    <div class="progress-bar">
                        <div class="progress-fill"
                            style="width: {{ $percent }}%;
                                background: {{ $chapter->is_completed ? '#58B100' : '#700002' }};">
                        </div>
                    </div>

                    

                    @php
                        if ($percent >= 100 || $chapter->is_completed) {
                            $btnText = 'Completed';
                        } elseif ($percent > 0) {
                            $btnText = 'Resume';
                        } else {
                            $btnText = 'Start';
                        }
                    @endphp

                    <div class="list_buttons">
                    <div class="open-text">
                        {{ $btnText }}
                    </div>
                    <div class="attempt-btn" onclick="event.stopPropagation(); showAttempts('{{ $chapter->name }}')">
                        View Attempts
                    </div>
                </div>
                </div>

            @empty
                <div>No chapters available.</div>
            @endforelse
        </div>
    </div>

    <script>
        // Hide chapters by default on page load
        document.addEventListener('DOMContentLoaded', function() {
            filterChaptersBySubject();
        });

        function filterChaptersBySubject() {
            const selectedSubjectId = document.getElementById('subjectFilter').value;
            const chapterItems = document.querySelectorAll('.chapter-item');
            let visibleCount = 0;

            chapterItems.forEach(item => {
                item.style.display = 'none';
            });

            // If no subject is selected, show nothing
            if (selectedSubjectId === '') {
                const container = document.getElementById('chapterListContainer');
                if (!document.getElementById('noSelectionMsg')) {
                    const noSelectionMsg = document.createElement('div');
                    noSelectionMsg.id = 'noSelectionMsg';
                    noSelectionMsg.textContent = 'Please select a subject to view chapters.';
                    noSelectionMsg.style.cssText = 'grid-column: 1 / -1; text-align: center; padding: 20px; color: #666; font-size: 16px;';
                    container.appendChild(noSelectionMsg);
                }
                return;
            }

            // Show chapters matching the selected subject
            chapterItems.forEach(item => {
                const subjectId = item.getAttribute('data-subject-id');
                if (subjectId === selectedSubjectId) {
                    item.style.display = 'block';
                    visibleCount++;
                }
            });

            // Remove the "no selection" message when a subject is selected
            const noSelectionMsg = document.getElementById('noSelectionMsg');
            if (noSelectionMsg) {
                noSelectionMsg.remove();
            }

            // Show/hide "No chapters" message
            if (visibleCount === 0) {
                const container = document.getElementById('chapterListContainer');
                if (!document.getElementById('noChaptersMsg')) {
                    const noChaptersMsg = document.createElement('div');
                    noChaptersMsg.id = 'noChaptersMsg';
                    noChaptersMsg.textContent = 'No chapters available for this subject.';
                    noChaptersMsg.style.cssText = 'grid-column: 1 / -1; text-align: center; padding: 20px; color: #666;';
                    container.appendChild(noChaptersMsg);
                }
            } else {
                const noChaptersMsg = document.getElementById('noChaptersMsg');
                if (noChaptersMsg) {
                    noChaptersMsg.remove();
                }
            }
        }

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
