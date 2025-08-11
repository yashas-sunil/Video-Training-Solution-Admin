<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f7f7f7;
        }
        .top-logo-bar {
            text-align: left;
            padding: 10px;
            background: white;
        }
        .top-logo-bar img {
            height: 60px;
        }
        .navbar {
            background: #007bff;
            padding: 1rem 2rem;
            color: white;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .container {
            padding: 2rem;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h2 {
            margin: 0;
            font-size: 2rem;
            color: #007bff;
        }
        .stat-card p {
            margin: 0.5rem 0 0;
            color: #666;
        }
        h2 {
            margin-top: 2rem;
            color: #333;
        }
        .courses-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
        }
        .course-card {
            background: white;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            width: 48%;
            box-sizing: border-box;
        }
        .course-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }
        .course-info {
            margin-top: 0.5rem;
            font-size: 0.95rem;
        }
        .progress-bar {
            margin-top: 0.5rem;
            background: #eee;
            border-radius: 5px;
            height: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #28a745;
        }
        .btn-resume {
            margin-top: 0.7rem;
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn-attempts {
            display: inline-block;
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            margin-top: 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-decoration: none;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        @media (max-width: 992px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 576px) {
            .stats {
                grid-template-columns: 1fr;
            }
            .course-card {
                width: 100%;
            }
        }

        /* Modal Styles */
        #attemptModal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        #attemptModalContent {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
        }
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
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
    <div class="user-info">
        <div style="text-align: left;">
            <div style="font-size: 0.85rem;">Welcome back,</div>
            <div style="font-weight: bold;">{{ auth()->user()->name }}</div>
        </div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:white; text-decoration: underline;">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <div class="stats">
        <div class="stat-card"><h2>{{ $courses->count() }}</h2><p>Courses in Progress</p></div>
        <div class="stat-card"><h2>{{ $completedCourses }}</h2><p>Completed Courses</p></div>
        <div class="stat-card"><h2>{{ gmdate("H:i:s", $totalWatchTime) }}</h2><p>Total Watch Time</p></div>
        <div class="stat-card"><h2>{{ $totalCourses }}</h2><p>Total Courses Purchased</p></div>
    </div>

    <h2>📚 Your Courses</h2>
    <input type="text" id="courseSearch" placeholder="Search your courses...">

    <div class="courses-grid">
        @forelse($courses as $progress)
            @php
                $course = $progress->course;
                $duration = optional($course)->watch_time ? optional($course)->watch_time * 60 : 0;
                $watched = $progress->session_time ?? 0;
                $percent = $duration > 0 ? round(($watched / $duration) * 100, 2) : 0;
            @endphp
            <div class="course-card">
                <div class="course-title">{{ optional($course)->title ?? 'Untitled Course' }}</div>
                <div class="course-info">
                    Status: <strong>{{ $progress->cmi_core_lesson_status ?? 'incomplete' }}</strong><br>
                    Watched: {{ gmdate("H:i:s", $watched) }} / {{ gmdate("H:i:s", $duration) }} — {{ $percent }}%
                    <div class="progress-bar"><div class="progress-fill" style="width: {{ $percent }}%;"></div></div>
                    <a href="javascript:void(0);" onclick="openScormWindow({{ $progress->course_id }})" class="btn-resume">▶ Resume</a>
                    <a href="javascript:void(0);" class="btn-attempts" onclick="showAttempts('{{ optional($course)->title }}')">📊 View Attempts</a>
                </div>
            </div>
        @empty
            <p>No courses started yet.</p>
        @endforelse
    </div>
</div>

<!-- Modal -->
<div id="attemptModal">
    <div id="attemptModalContent">
        <span class="close-btn" onclick="document.getElementById('attemptModal').style.display='none'">&times;</span>
        <h3>Quiz Attempts</h3>
        <div id="attemptContent">Loading...</div>
    </div>
</div>

<script>
    function openScormWindow(courseId) {
        const width = screen.availWidth;
        const height = screen.availHeight;
        const popup = window.open(`/view/${courseId}`, '_blank', `width=${width},height=${height},top=0,left=0,resizable=yes,scrollbars=yes`);
        if (!popup || popup.closed || typeof popup.closed === 'undefined') {
            alert("Please allow popups for this site to view the course.");
        } else {
            popup.focus();
        }
    }

    document.getElementById("courseSearch").addEventListener("keyup", function () {
        const searchValue = this.value.toLowerCase();
        const courses = document.querySelectorAll(".course-card");
        courses.forEach(function (card) {
            const title = card.querySelector(".course-title").textContent.toLowerCase();
            card.style.display = title.includes(searchValue) ? "block" : "none";
        });
    });

 // Pehle attempts ka summary list dikhana
function showAttempts(quizName) {
    fetch(`/get-attempts?quiz_name=${encodeURIComponent(quizName)}`)
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById('attemptContent');
            if (data.length === 0) {
                box.innerHTML = "<p>No attempts found.</p>";
            } else {
                box.innerHTML = data.map((attempt, idx) => `
                    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px; background:#f9f9f9;">
                        <strong>Attempt:</strong> ${attempt.attempt_number}<br>
                        <strong>Chapter:</strong> ${attempt.chapter_name}<br>
                        <strong>Score:</strong> ${attempt.score_percent}%<br>
                        <button onclick="viewAttemptQuestions(${idx}, '${quizName}')"
                            style="margin-top:8px; padding:4px 8px;">View Questions</button>
                    </div>
                `).join('');

                // Data ko global store karte hain taki dobara fetch na karna pade
                window.attemptData = data;
            }
            document.getElementById('attemptModal').style.display = 'block';
        })
        .catch(err => {
            console.error(err);
            alert("Failed to load attempts.");
        });
}

// Specific attempt ke questions dikhana
function viewAttemptQuestions(index, quizName) {
    const attempt = window.attemptData[index];
    const box = document.getElementById('attemptContent');

    box.innerHTML = `
        <button onclick="showAttempts('${quizName}')" 
            style="margin-bottom:15px; padding:6px 12px; border:none; background:#3498db; color:white; border-radius:4px; cursor:pointer;">
            ⬅ Back
        </button>

        <div style="padding:15px; background:#f7f9fc; border-radius:6px; border:1px solid #ddd; margin-bottom:20px;">
            <h3 style="margin-bottom:5px;">Attempt: ${attempt.attempt_number}</h3>
            <p style="margin:4px 0;"><strong>Chapter:</strong> ${attempt.chapter_name}</p>
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
