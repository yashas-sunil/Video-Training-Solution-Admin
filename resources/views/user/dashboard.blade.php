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
            width: 200px;
            height: 45px;

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

        .container {
            padding: 2rem;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            justify-content: center;
            display: flex;
            flex-direction: column;
            background: white;
            padding: 1.5rem;
            width: auto;
            height: 130px;
            border-radius: 20px;
            box-shadow: 0 4px 12px rgba(91, 113, 236, 0.342);
            text-align: center;
            position: relative;
            /* agar top/left Figma jaisa chahiye */
            /* top: 238px; left: 66.15px; */
            /* optional: uncomment for absolute positioning */
            transition: transform 0.3s ease;
            /* optional hover effect */
        }

        /* Optional: hover effect for Figma look */
        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card h2 {
            margin: 0;
            font-size: 2rem;
            color: #007bff;
        }

        .stat-card p {
            font-size: 14px;
            margin: 2px 0;
            color: #666;
        }

        h2 {
            margin-top: 2rem;
            color: #333;
        }

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 1rem;
            width: 100%;
            border-radius: 20px;
            overflow-y: auto;
            max-height: none;
            margin: 0 auto;
        }


        .course-card {
            background: white;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
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
            display: flex;
            align-items: center;
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            margin-top: 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-decoration: none;
        }

        .btn-start {
            display: flex;
            align-items: center;
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            margin-top: 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-decoration: none;
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
                justify-content: center;
            }
            .courses-grid {
                display: grid!important;
                grid-template-columns: repeat(1, 1fr); 
            }
            .course-card {
                max-width: 100%!important;
                width: 100%!important;                                                                     
            }
        }

        /* Small screens (≤ 425px) */
        @media (max-width: 425px) {
            .stats {
                grid-template-columns: repeat(1, 1fr)!important;
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
            background-color: rgba(0, 0, 0, 0.4);
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

        .btn-start:hover {
            display: flex;
            background: #6c757d;
            color: white;
            padding: 6px 12px;
            margin-top: 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            text-decoration: none;
        }
    </style>
</head>

<body>

    <div class="navbar">
        <!-- Left: Company Logo -->
        <div class="logo">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
            <img src="{{ asset('images/logo2.png') }}" alt="Company Logo">

        </div>

        <!-- Right: User Info -->
        <div class="user-info">
            <div>
                <div style="font-size: 0.85rem;">Welcome back !</div>
                <div style="font-size: 24px;font-weight: bold;">{{ auth()->user()->name }}</div>
            </div>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                style="display: flex; gap: 5px; color:white; text-decoration: none; margin-left: 20px;">Logout<img src="{{ asset('images/logout.png') }}" alt="Logout"></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    <!-- Main Content -->
    <div class="container">
        <div class="stats">

            <!-- Courses in Progress -->
            <div class="stat-card" onclick="filterCourses('in-progress')" style="cursor:pointer;">
                <div style="margin-bottom:6px;">
                    <img src="{{ asset('images/course-in-progress.png') }}" alt="Courses in Progress"
                        style="width:40px; height:40px;" />
                </div>

                {{--  Count only non-expired + in-progress courses --}}
                <h2>
                    {{ $courses->filter(function ($course) {
                            return !$course['is_expired'] && // not expired
                                !$course['is_disabled'] && // not disabled
                                $course['progress']->where('cmi_core_lesson_status', '!=', 'completed')->isNotEmpty(); // in progress
                        })->count() }}
                </h2>

                <p>Courses in Progress</p>
            </div>

            <!-- Completed Courses -->
            <div class="stat-card" onclick="filterCourses('completed')" style="cursor:pointer;">
                <div style="margin-bottom:6px;">
                    <img src="{{ asset('images/completed-course.png') }}" alt="Completed Courses"
                        style="width:40px; height:40px;" />
                </div>
                <h2>{{ $completedCourses }}</h2>
                <p>Completed Courses</p>
            </div>

            <!-- Total Watch Time (NO CLICK) -->
            <div class="stat-card">
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('images/total-watch-time.png') }}" alt="Total Watch Time"
                        style="width:40px; height:40px;" />
                </div>
                <h2>{{ gmdate('H:i:s', $totalWatchTime) }}</h2>
                <p>Total Watch Time</p>
            </div>

             <!-- Expired Courses -->
            <div class="stat-card" onclick="filterCourses('expired')" style="cursor:pointer;">
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('images/course-expire.png') }}" alt="Expired Courses"
                        style="width:40px; height:40px;" />
                </div>
                <h2>{{ $expiredCoursesCount }}</h2>
                <p>Expired Courses</p>
            </div>

            <!-- Total Courses Purchased -->
            <div class="stat-card" onclick="filterCourses('all')" style="cursor:pointer;">
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('images/total-purchase.png') }}" alt="Total Courses Purchased"
                        style="width:40px; height:40px;" />
                </div>
                <h2>{{ $totalCourses }}</h2>
                <p>Total Courses Purchased</p>
            </div>

        </div>


        <h2 style="margin-bottom:10px; text-align:center;">📚 Your Courses</h2>

        <!-- Search + Filter -->
        <div style="display:flex; justify-content:flex-end; align-items:center; gap:10px; margin-bottom:20px;">
            <input type="text" id="courseSearch" placeholder="Search your courses..."
                style="width:200px; padding:8px 10px; border-radius:6px; border:1px solid #ccc; font-size:14px;">
            <select id="courseFilter"
                style="padding:8px 10px; border-radius:6px; border:1px solid #ccc; font-size:14px;">
                <option value="">All Courses</option>
                <option value="completed">Completed</option>
                <option value="in-progress">In Progress</option>
                {{-- <option value="expired">Expired</option> --}}
            </select>
        </div>

        <!-- Courses Grid -->
        <div class="courses-grid"
            style="display:flex; flex-wrap:wrap; gap:1rem; justify-content:{{ count($courses) === 1 ? 'center' : 'flex-start' }};">
            @forelse($courses as $item)
                @php
                    $course = $item['course'] ?? null;
                    $progress = $item['progress'] ?? collect();
                    $view = $item['view'] ?? null;

                    $duration = optional($course)->watch_time ? optional($course)->watch_time * 60 : 0;
                    $totalWatched = $progress->sum('session_time');
                    $currentAttempt = $view->view_limit ?? 1;
                    $watchedThisAttempt = max(0, $totalWatched - ($currentAttempt - 1) * $duration);
                    if ($watchedThisAttempt > $duration) {
                        $watchedThisAttempt = $duration;
                    }
                    $percent = $duration > 0 ? round(($watchedThisAttempt / $duration) * 100, 2) : 0;

                    $isDisabled = optional($course)->status == 0 || ($item['is_disabled'] ?? false);
                    $isExpired = $item['is_expired'] ?? false;

                    if ($percent == 100) {
                        $status = 'completed';
                    } elseif ($isExpired) {
                        $status = 'expired';
                    } else {
                        $status = 'in-progress';
                    }
                @endphp

                <div class="course-card" data-status="{{ $status }}"
                    style="background:white; padding:1rem 1.5rem; text-align: center; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.05); width:{{ count($courses) === 1 ? '500px' : '48%' }}; max-width:90%; box-sizing:border-box; position:relative;">

                    {{-- Show badge if expired --}}
                    @if ($isExpired)
                        <span
                            style="position:absolute; top:10px; right:10px; background:#dc354644; color:red; padding:6px 10px; border-radius:6px; font-size:12px;">
                            Expired
                        </span>
                    @endif

                    <div class="course-title" style="font-size:1.2rem; font-weight:bold; color:#333;">
                        {{ $course->title ?? 'Untitled Course' }}
                    </div>

                    <div style="margin-top:4px; font-size:0.9rem; color:#555;">
                        Attempt: {{ $currentAttempt }}
                    </div>

                    <div class="course-info" style="margin-top:0.5rem; font-size:0.95rem;">
                        <div style="margin-bottom:0.3rem;">
                            Watched: {{ gmdate('H:i:s', $watchedThisAttempt) }} / {{ gmdate('H:i:s', $duration) }}
                        </div>

                        <div class="progress-bar-wrapper" style="display:flex; align-items:center; gap:0.5rem;">
                            <div class="progress-bar"
                                style="flex:1; background:#eee; border-radius:5px; height:10px; overflow:hidden; position:relative;">
                                <div class="progress-fill"
                                    style="height:100%; background:#28a745; width:{{ $percent }}%;"></div>
                            </div>
                            <span style="min-width:35px; font-weight:bold; color:#28a745;">{{ $percent }}%</span>
                        </div>

                        <div
                            style="display:flex; justify-content:center; align-items:center; margin-top:0.5rem; gap:8px;">

                            {{-- Start / Resume --}}
                            @if ($percent == 0)
                                <a href="javascript:void(0);"
                                    @if ($isDisabled) onclick="alert('⚠️ This course is currently disabled.');"
                                style="background:#ccc; cursor:not-allowed; opacity:0.7; margin-top:10px;"
                            @elseif ($isExpired)
                                onclick="alert('⚠️ This course has expired.');"
                                style="background:#ccc; cursor:not-allowed; opacity:0.7; margin-top:10px;"
                            @else
                                onclick="openScormWindow({{ $course->id ?? 0 }})"
                                style="background:#007bff; margin-top:10px;" @endif
                                    class="btn-start"
                                    style="display:inline-flex; align-items:center; justify-content:center; text-align:center; padding:6px 10px; border-radius:6px; height:32px; margin-top:10px;">
                                    <img src="{{ asset('images/start-btn.png') }}"
                                        onerror="this.onerror=null;this.src='https://cdn-icons-png.flaticon.com/512/892/892692.png';"
                                        alt="Start" style="width:18px; height:25px; margin-right:8px;" />
                                    <span style="color:white; font-weight: bold; font-size:14px; line-height:18px;">Start</span>
                                </a>
                            @else
                                <a href="javascript:void(0);"
                                    @if ($isDisabled) onclick="alert('⚠️ This course is currently disabled.');"
                                style="background:#ccc; cursor:not-allowed; opacity:0.7;"
                            @elseif ($isExpired)
                                onclick="alert('⚠️ This course has expired.');"
                                style="background:#b6b5b5; cursor:not-allowed; opacity:0.7;"
                            @else
                                onclick="openScormWindow({{ $course->id ?? 0 }})"
                                style="background:#007bff;" @endif
                                    class="btn-resume"
                                    style="display:inline-flex; align-items:center; justify-content:center; text-align:center; background:#d0e4ff; padding:6px 10px; border-radius:6px; height:32px;">
                                    <img src="{{ asset('images/Resume Button.png') }}" alt="Resume"
                                        style="width:18px; height:25px; margin-right:5px;" />
                                    <span style="color:white; font-weight: bold; font-size:14px; line-height:18px;">Resume</span>
                                </a>
                            @endif

                            {{-- View Attempts --}}
                            <a href="javascript:void(0);" class="btn-attempts"
                                onclick="showAttempts('{{ $course->title ?? '' }}')"
                                style="display:inline-flex; align-items:center; justify-content:center; text-align:center; background:#CCE5FF; padding:6px 10px; border-radius:6px; height:27px;">
                                <img src="{{ asset('images/View.png') }}" alt="View Attempts"
                                    style="width:18px; height:18px; margin-right:5px;" />
                                <span style="color:#007BFF; font-weight: bold; font-size:14px; line-height:18px;">View Attempts</span>
                            </a>
                        </div>

                    </div>
                </div>
            @empty
                <p>No courses assigned yet.</p>
            @endforelse
        </div>


        <!-- Modal -->
        <div id="attemptModal">
            <div id="attemptModalContent">
                <span class="close-btn"
                    onclick="document.getElementById('attemptModal').style.display='none'">&times;</span>
                <h3>Quiz Attempts</h3>
                <div id="attemptContent">Loading...</div>
            </div>
        </div>

        <script>
            function openScormWindow(courseId) {
                const width = screen.availWidth;
                const height = screen.availHeight;
                const popup = window.open(`/view/${courseId}`, '_blank',
                    `width=${width},height=${height},top=0,left=0,resizable=yes,scrollbars=yes`);
                if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                    alert("Please allow popups for this site to view the course.");
                } else {
                    popup.focus();
                }
            }

            document.getElementById("courseSearch").addEventListener("input", function() {
                const searchValue = this.value.toLowerCase();
                const filterValue = document.getElementById("courseFilter").value;

                const courses = document.querySelectorAll(".course-card");
                courses.forEach(function(card) {
                    const title = card.querySelector(".course-title").textContent.toLowerCase();

                    // progress percentage read karo
                    const percentText = card.querySelector(".progress-bar-wrapper span").textContent.replace(
                        '%', '').trim();
                    const percent = parseFloat(percentText);

                    let matchesSearch = title.includes(searchValue);
                    let matchesFilter = true;

                    if (filterValue === "completed") {
                        matchesFilter = percent === 100;
                    } else if (filterValue === "in-progress") {
                        matchesFilter = percent > 0 && percent < 100;
                    }

                    card.style.display = (matchesSearch && matchesFilter) ? "block" : "none";
                });
            });

            // Filter change
            document.getElementById("courseFilter").addEventListener("change", function() {
                document.getElementById("courseSearch").dispatchEvent(new Event("input"));
            });

            // Filter change
            document.getElementById("courseFilter").addEventListener("change", function() {
                document.getElementById("courseSearch").dispatchEvent(new Event("input"));
            });


            // Pehle attempts ka summary list dikhana


            function showAttempts(quizName) {
                window.location.href = `/get-attempts?quiz_name=${encodeURIComponent(quizName)}`;
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

            function filterCourses(status) {
                const cards = document.querySelectorAll('.course-card');
                cards.forEach(card => {
                    const percentText = card.querySelector('.progress-fill').style.width.replace('%', '');
                    const percent = parseFloat(percentText);

                    // Logic
                    if (status === 'in-progress') {
                        // Show only if 0 < percent < 100
                        if (percent > 0 && percent < 100) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    } else if (status === 'completed') {
                        card.style.display = percent === 100 ? 'block' : 'none';
                    } else if (status === 'expired') {
                        card.style.display = card.dataset.status === 'expired' ? 'block' : 'none';
                    } else if (status === 'all') {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'block';
                    }
                });
            }
        </script>

</body>

</html>