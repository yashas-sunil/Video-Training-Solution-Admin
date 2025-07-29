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
        .navbar {
            background: #007bff;
            padding: 1rem 2rem;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .container {
            padding: 2rem;
        }
        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 23%;
            text-align: center;
            margin-bottom: 1rem;
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
        .course-card {
            background: white;
            padding: 1rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
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
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 20px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div>Welcome back, {{ auth()->user()->name }}</div>
    <div>
        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" style="color:white;">Logout</a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

<div class="container">
    {{-- Overview Cards --}}
    <div class="stats">
        <div class="stat-card">
            <h2>{{ $courses->count() }}</h2>
            <p>Courses in Progress</p>
        </div>
        <div class="stat-card">
            <h2>{{ $completedCourses }}</h2>
            <p>Completed Courses</p>
        </div>
        <div class="stat-card">
            <h2>{{ gmdate("H \H\r i \M\i\n s \S\e\c", $totalWatchTime) }}</h2>
            <p>Total Watch Time</p>
        </div>
        <div class="stat-card">
            <h2>{{ $totalCourses }}</h2>
            <p>Total Courses Purchased</p>
        </div>
    </div>

    <h2>📚 Your Courses</h2>

    {{-- 🔍 Search Bar --}}
    <input type="text" id="courseSearch" placeholder="Search your courses...">

    {{-- List of All Courses --}}
    @forelse($courses as $progress)
        @php
            $course = $progress->course;
            $duration = $course->duration_in_seconds ?? 0;
            $watched = $progress->session_time ?? 0;
            $percent = $duration > 0 ? round(($watched / $duration) * 100, 2) : 0;
        @endphp

        <div class="course-card">
            <div class="course-title">{{ $course->title ?? 'Untitled Course' }}</div>
            <div class="course-info">
                Status: <strong>{{ $progress->cmi_core_lesson_status ?? 'incomplete' }}</strong><br>
                Watched: {{ gmdate("H:i:s", $watched) }} / {{ gmdate("H:i:s", $duration) }} — {{ $percent }}%
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ $percent }}%;"></div>
                </div>
                <a href="/view/{{ $progress->course_id }}" class="btn-resume">▶ Resume</a>
            </div>
        </div>
    @empty
        <p>No courses started yet.</p>
    @endforelse

    {{-- Pending --}}
    <h2>⏳ Pending Courses</h2>
    @forelse($pendingCourses as $pending)
        <div class="course-card">
            <div class="course-title">{{ $pending->course->title ?? 'Untitled Course' }}</div>
            <div class="course-info">
                Watched: {{ gmdate("H:i:s", $pending->session_time ?? 0) }} / 
                         {{ gmdate("H:i:s", $pending->course->duration_in_seconds ?? 0) }}
                <div class="progress-bar">
                    <div class="progress-fill" 
                         style="width: {{ $pending->course->duration_in_seconds > 0 ? round(($pending->session_time / $pending->course->duration_in_seconds) * 100, 2) : 0 }}%;">
                    </div>
                </div>
                <a href="/view/{{ $pending->course_id }}" class="btn-resume">▶ Resume</a>
            </div>
        </div>
    @empty
        <p>All courses are completed.</p>
    @endforelse
</div>

<script>
    // 🔍 Search Filter
    document.getElementById("courseSearch").addEventListener("keyup", function () {
        const searchValue = this.value.toLowerCase();
        const courses = document.querySelectorAll(".course-card");

        courses.forEach(function (card) {
            const title = card.querySelector(".course-title").textContent.toLowerCase();
            card.style.display = title.includes(searchValue) ? "block" : "none";
        });
    });
</script>

</body>
</html>
