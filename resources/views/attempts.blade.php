<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Quiz Attempts</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
        }

        /* Logo bar */
        .top-logo-bar {
            text-align: left;
            padding: 10px 20px;
            background: white;
            border-bottom: 1px solid #ddd;
        }

        .top-logo-bar img {
            height: 40px;
        }

        /* Navbar */
        .navbar {
            background-color: #700002;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0px;
            width: -webkit-fill-available;
            z-index: 1;
        }

        .navbar .logo img {
            height: 80px;
            width: auto;
        }

         .logo2 {
    position: relative;
    left: -80px;   
}

.logo2 img {
    height: 100px;   
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


        /* Page content */
        .page-content {
            padding: 20px;
            margin-top: 89px;
        }

        /* Attempts grid */
        .attempts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            justify-items: stretch;
        }

        .attempt-card {

            border-radius: 8px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            width: auto;
            height: auto;
            margin-top: 30px;
        }

        .attempt-card p {
            margin: 5px 0;
            font-size: 14px;
        }

        .attempt-card a {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            background: #BFDEFF;
            color: #007BFF;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
        }

        /*.attempt-card a:hover {
      background: #0056b3;
    }*/

        /* Toolbar */
        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .quiz-title {
            margin: 0;
            font-size: 17px;
            font-weight: bold;
        }

        .toolbar {
            position: relative;
            /* zaroori hai absolute centering ke liye */
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
            height: fit-content;
        }

        /* Filter button (just image, no style) */
        .btn-filter {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
        }

        .btn-filter img {
            width: 20px;
            height: 20px;
            vertical-align: middle;
        }

        /* Back button styled */
        .btn-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 14px;
            border: none;
            font-size: 14px;
            font-weight: 500;
            border-radius: 6px;
            background: #2C3E50;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /*.btn-link:hover {
    background: #007bff;
    color: #fff;
  })*/

        /* Sidebar filter */
        .filter-sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            height: 100%;
            background: #fff;
            border-left: 1px solid #ddd;
            box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
            transition: right 0.3s ease;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .filter-sidebar.open {
            right: 0;
        }

        .filter-sidebar h3 {
            margin-top: 0;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .filter-section select,
        .filter-section input {
            width: 100%;
            padding: 6px;
            margin-bottom: 10px;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 20px;
            float: right;
            cursor: pointer;
        }

        .apply-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .apply-btn:hover {
            background: #0056b3;
        }

        .clear-btn {
            display: block;
            margin-top: 10px;
            width: 100%;
            padding: 8px;
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
        }

        /* Score slider overlay */
        .score-range-container {
            position: relative;
            height: 40px;
        }

        .score-range-container input[type=range] {
            position: absolute;
            width: 100%;
            pointer-events: none;
            background: none;
        }

        .score-range-container input[type=range]::-webkit-slider-thumb {
            pointer-events: all;
            position: relative;
            z-index: 2;
        }

        .score-range-container input[type=range]::-moz-range-thumb {
            pointer-events: all;
            position: relative;
            z-index: 2;
        }

        /* Overlay background */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            /* 45% opacity black */
            display: none;
            z-index: 900;
            /* below sidebar */
        }

        .overlay.show {
            display: block;
        }

        .performance_con {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            justify-content: space-around;
            align-items: center;
            gap: 30px;

        }

        .container_cards {
            display: flex;
            flex-direction: row;
            flex-wrap: nowrap;
            align-items: center;
            border: 3px solid #700002;
            border-radius: 20px;
            padding: 20px;
            gap: 15px;
            justify-content: center;
            width: 18%;
        }

        .best-score {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .best-score p:first-child {
            margin: 0;
            font-family: sans-serif;
            font-weight: 400;
            font-size: 34px;
            line-height: 100%;
        }

        .best-score p:last-child {
            margin: 0;
            font-family: sans-serif;
            font-weight: 300;
            font-size: 16px;
            line-height: 100%;
        }

        @media (max-width: 992px) {
            .attempts-grid {
                grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            }
        }

        @media (max-width: 425px) {
            .navbar {
                padding: 15px 6px;
            }

            .navbar .logo img {
                height: 30px;
                width: auto;
            }

            .navbar .user-info {
                gap: 8px;
            }

            .user-welcome {
                gap: 4px;
                padding: 6px 10px;
            }

            .user-info a {
                margin-left: 0px;
                padding: 3px 10px;
                font-size: 12px;
            }

            .user-info-mobile {
                display: flex !important;
                gap: 8px;
            }

            .user-welcome {
                gap: 4px;
                padding: 6px 10px;
            }

            .user-info a {
                margin-left: 0px;
                padding: 3px 10px;
                font-size: 12px;
            }

            .user-info-mobile a {
                color: white;
                text-decoration: none;
            }

            .user-info {
                display: none !important;
            }

            .toolbar {
                flex-wrap: wrap;
            }

            .user-welcome {
                gap: 4px;
                padding: 6px 10px;
            }

            .container_cards {
                width: 60% !important;
            }
        }

        @media (max-width: 768px) {
            .performance_con {
                flex-wrap: wrap !important;
                gap: 20px;
            }

            .container_cards {
                padding: 20px !important;
                width: auto;
            }
        }
    </style>
</head>

<body>

    <!-- Logo -->
    <div class="navbar">
        <!-- Left: Company Logo -->
        <div class="logo">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
            <img src="{{ asset('images/logo3.png') }}" alt="Company Logo">

        </div>

         <div class="logo2">
            {{-- <img src="{{ asset('images/Bloomberglogo.png') }}" alt="Company Logo"> --}}
            <img src="{{ asset('images/Bloomberglogo.png') }}" alt="Company Logo">

        </div>

        <!-- Right: User Info -->
        <div class="user-info">
            <div class="user-welcome">
                <div style="font-size: 14px;">Welcome back !</div>
                <div style="font-size: 14px;font-weight: bold;">{{ auth()->user()->name }}</div>
            </div>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout<img
                    src="{{ asset('images/Logout2.png') }}" alt="Logout"></a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
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

    <!-- Page Content -->
    <div class="page-content">

        <div class="toolbar" style="margin: 20px 0px;">
            <div class="toolbar_r">
                <div class="quiz_name"
                    style="background: #BEDBFF26;border: 2px solid #BEDBFF;border-radius: 13px;padding: 12px;display: flex;align-items: flex-start;flex-direction: column;flex-wrap: nowrap;gap: 10px;margin: 16px;margin-left: 0px;">
                    <p style="margin: 0;font-size: 14px;font-weight: 100;">Course</p>
                    <h2 class="quiz-title">{{ $quizName }}</h2>
                </div>
                <h2 style="font-family: sans-serif;margin: 0;">Quiz Attempts</h2>
                <p style="font-weight: lighter;">Review your performance and track your progress</p>
            </div>
            <div class="sub-toolbar"
                style="display: flex;align-items: center;flex-wrap: nowrap;flex-direction: row;gap: 10px;">

                <div class="toolbar-actions">

                    <!-- Filter Button -->
                    <button class="btn-filter" onclick="openFilter()">
                        <img src="{{ asset('images/filter.png') }}" alt="Filter" />
                    </button>

                    <!-- Back Button -->
                    <a href="{{ url()->previous() }}" class="btn-link"
                        style="gap: 5px; display:inline-flex; align-items:center;">
                        <img src="{{ asset('images/Left.png') }}" alt="Back"
                            style="width:15px; height:15px; cursor:pointer;" />
                        Back
                    </a>

                </div>
            </div>

        </div>

        <!-- Performance -->
        @php
            $scores = count($attempts) > 0 ? array_column($attempts->toArray(), 'score_percent') : [];
            $highestScore = count($scores) > 0 ? max($scores) : 0;
            $averageScore = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;
            $totalAttempts = count($attempts);
        @endphp

        <div class="performance_con">

            <div class="container_cards">
                <img src="{{ asset('images/TTAttempts.png') }}" alt="TTAttempts" style="width:60px; height:60px;" />

                <div class="best-score">
                    <p>{{ $totalAttempts }}</p>
                    <p>Total Attempts</p>
                </div>
            </div>
            <div class="container_cards">
                <img src="{{ asset('images/Average.png') }}" alt="Average" style="width:60px; height:60px;" />

                <div class="best-score">
                    <p>{{ $averageScore }}%</p>
                    <p>Average Score</p>
                </div>
            </div>
            <div class="container_cards">
                <img src="{{ asset('images/Performance.png') }}" alt="Performance" style="width:60px; height:60px;" />

                <div class="best-score">
                    <p>{{ $highestScore }}%</p>
                    <p>Best Score</p>
                </div>
            </div>

        </div>




        <!-- Attempts -->
        <div class="attempts-grid" id="attemptsGrid">
            @forelse($attempts as $attempt)
                <div class="attempt-card" data-score="{{ $attempt['score_percent'] }}"
                    style="border:{{ $attempt['score_percent'] >= 60 ? '1px solid #00A63E' : '1px solid #E7000B' }}"
                    data-chapter="{{ $attempt['chapter_name'] }}" data-attempt="{{ $attempt['attempt_number'] }}"
                    data-date="{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('Y-m-d') }}"
                    data-time="{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('H:i') }}">

                    <div class="sec1"
                        style="display: flex;flex-direction: row;flex-wrap: nowrap;justify-content: space-between;align-items: center;">
                        <div class="attempt_se1" style="display: flex;align-items: center;    margin: 10px 0px;">
                            <p
                                style="font-family: sans-serif; font-weight: bold; font-size: 16px;color: {{ $attempt['score_percent'] >= 60 ? '#00A63E' : '#E7000B' }};">
                                Attempt: {{ $attempt['attempt_number'] }}</p>
                            <div
                                class="passing"style="background-color: {{ $attempt['score_percent'] >= 60 ? '#00A63E' : '#E7000B' }};color: #ffffff;padding: 5px 10px;border-radius: 15px;font-size: 10px;margin-left: 10px;">
                                @if ($attempt['score_percent'] >= 60)
                                    <span>Passed</span>
                                @else
                                    <span>Failed</span>
                                @endif
                            </div>
                        </div>
                        <div class="best_score"
                            style="display: {{ $highestScore == $attempt['score_percent'] ? 'flex' : 'none' }}; border-radius: 20px; color:#ffffff; padding: 5px 10px; width: fit-content;gap: 5px;align-items: center;">
                            <img src="{{ asset('images/Prize.png') }}" alt="Best Score"
                                style="width:35px;height:auto;" />
                        </div>
                    </div>
                    <div class="sec2"
                        style="display: flex;flex-direction: row;flex-wrap: nowrap;justify-content: space-between;">
                        <div class="sec2_l" style="display: flex;flex-direction: column;flex-wrap: nowrap;gap: 5px;">

                            <p style="font-family: sans-serif;font-weight: 600;font-size: 12px;line-height: 100%;">
                                Chapter: {{ $attempt['chapter_name'] }}</p>
                            <div style="display: flex; align-items: center; gap: 5px; margin-top: 5px;">
                                <img src="{{ asset('images/Calendar.png') }}" alt="Date"
                                    style="width:16px;height:auto;margin-right:5px;" />
                                <span
                                    style="font-family: sans-serif;font-weight:400; font-size:12px; color:#000000;">{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('Y-m-d') }}</span>
                            </div>
                            <div style="display: flex; align-items: center; gap: 5px; margin-top: 2px;">
                                <img src="{{ asset('images/Clock.png') }}" alt="Date"
                                    style="width:16px;height:auto;margin-right:5px;" />
                                <span
                                    style="font-family: sans-serif;font-weight:400; font-size:12px; color:#000000; align-items: center;">{{ \Carbon\Carbon::parse($attempt['attempt_time'])->format('H:i:s') }}</span>
                            </div>
                        </div>
                        <div class="sec2_r"
                            style="display: flex;flex-direction: column;flex-wrap: nowrap;gap:5px;margin: 8px 0;align-items: center;">
                            <p
                                style="font-family: sans-serif;font-weight: 400;font-size: 48px;line-height: 100%;color:{{ $attempt['score_percent'] >= 60 ? '#00A63E' : '#E7000B' }};margin: 0;">
                                {{ $attempt['score_percent'] }}%</p>
                            <p>Score</p>
                        </div>
                    </div>

                    <a style="color:#FFFFFF; background-color:#2C3E50; display: flex; padding: 10px 8px;margin-top: 8px; border-radius: 4px; font-size: 12px; justify-content: center;align-items: center;"
                        href="{{ url('/course-attempts/' . $quizName . '/view/' . $attempt['attempt_number']) }}">
                        <img src="{{ asset('images/Eye.png') }}" alt="View Questions"
                            style="width:15px; height:15px; vertical-align: middle; margin-right: 5px;" />
                        View Questions
                    </a>
                </div>
            @empty
                <p>No attempts found.</p>
            @endforelse
        </div>
    </div>


    <!-- Overlay -->
    <div id="overlay" class="overlay" onclick="closeFilter()"></div>

    <!-- Sidebar Filter -->
    <div id="filterSidebar" class="filter-sidebar">
        <button class="close-btn" onclick="closeFilter()">✖</button>
        <h3>Filter</h3>

        <!-- Chapter Dropdown -->
        <div class="filter-section">
            <label for="chapter">Filter by Chapter:</label>
            <select id="chapter">
                <option value="">All</option>
                @foreach ($attempts->pluck('chapter_name')->unique() as $chapter)
                    <option value="{{ $chapter }}">{{ $chapter }}</option>
                @endforeach
            </select>
        </div>

        <!-- Attempt Dropdown -->
        <div class="filter-section">
            <label for="attemptNo">Filter by Attempt:</label>
            <select id="attemptNo">
                <option value="">All</option>
                @foreach ($attempts->pluck('attempt_number')->unique() as $atNo)
                    <option value="{{ $atNo }}">Attempt {{ $atNo }}</option>
                @endforeach
            </select>
        </div>

        <!-- Date -->
        <div class="filter-section">
            <label for="date">Filter by Date:</label>
            <input type="date" id="date">
        </div>

        <!-- Score Double Range -->
        <div class="filter-section">
            <label>Filter by Score Range:
                <span id="minScoreVal">0</span>% - <span id="maxScoreVal">100</span>%
            </label>
            <div class="score-range-container">
                <input type="range" id="minScore" min="0" max="100" value="0" step="1"
                    oninput="updateScoreRange()">
                <input type="range" id="maxScore" min="0" max="100" value="100" step="1"
                    oninput="updateScoreRange()">
            </div>
        </div>

        <button class="apply-btn" onclick="applyFilters()">Apply</button>
        <button class="clear-btn" onclick="clearFilters()">Clear</button>
    </div>
    <div id="noAttempts"
        style="display:none; text-align:center; padding:20px; font-size:18px; font-weight:bold; color:#ff0000;">
        No attempts found for this filter.
    </div>

    <script>
        function openFilter() {
            document.getElementById("filterSidebar").classList.add("open");
            document.getElementById("overlay").classList.add("show");
        }

        function closeFilter() {
            document.getElementById("filterSidebar").classList.remove("open");
            document.getElementById("overlay").classList.remove("show");
        }

        function updateScoreRange() {
            let minScore = parseInt(document.getElementById("minScore").value);
            let maxScore = parseInt(document.getElementById("maxScore").value);

            if (minScore > maxScore) {
                [minScore, maxScore] = [maxScore, minScore];
                document.getElementById("minScore").value = minScore;
                document.getElementById("maxScore").value = maxScore;
            }

            document.getElementById("minScoreVal").innerText = minScore;
            document.getElementById("maxScoreVal").innerText = maxScore;
        }

        function applyFilters() {
            const chapter = document.getElementById("chapter").value;
            const attempt = document.getElementById("attemptNo").value;
            const date = document.getElementById("date").value;
            const minScore = parseInt(document.getElementById("minScore").value);
            const maxScore = parseInt(document.getElementById("maxScore").value);

            const cards = document.querySelectorAll(".attempt-card");
            let visibleCount = 0;

            cards.forEach(card => {
                let show = true;

                if (chapter && card.getAttribute("data-chapter") !== chapter) show = false;
                if (attempt && card.getAttribute("data-attempt") !== attempt) show = false;
                if (date && card.getAttribute("data-date") !== date) show = false;

                let score = parseInt(card.getAttribute("data-score"));
                if (score < minScore || score > maxScore) show = false;

                if (show) {
                    card.style.display = "block";
                    visibleCount++;
                } else {
                    card.style.display = "none";
                }
            });

            document.getElementById("noAttempts").style.display =
                visibleCount === 0 ? "block" : "none";

            closeFilter();
        }

        function clearFilters() {
            document.getElementById("chapter").value = "";
            document.getElementById("attemptNo").value = "";
            document.getElementById("date").value = "";
            document.getElementById("minScore").value = 0;
            document.getElementById("maxScore").value = 100;
            document.getElementById("minScoreVal").innerText = "0";
            document.getElementById("maxScoreVal").innerText = "100";

            document.querySelectorAll(".attempt-card").forEach(card => {
                card.style.display = "block";
            });

            document.getElementById("noAttempts").style.display = "none";
        }
    </script>

</body>

</html>
