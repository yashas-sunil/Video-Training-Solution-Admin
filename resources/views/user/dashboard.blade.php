<!DOCTYPE html>


<html>

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://fonts.cdnfonts.com/css/digital-numbers" rel="stylesheet">
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

        .container {
            padding: 2rem;
            justify-items: center;
            margin-top: 89px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-card,
        .stat-card-time {
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
            /* top: 238px; left: 66.15px; */
            /* optional: uncomment for absolute positioning */
            transition: transform 0.3s ease;
            /* optional hover effect */
            border-width: 7px 2px 2px 2px;
            border-style: solid;
            border-color: #700002;
        }

        .stat-card-time {
            background: #282843;
            border: none;
            padding: 30px;

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

        .stat-card-time h2 {
            margin: 0;
            font-family: 'Digital Numbers', sans-serif;
            font-weight: 400;
            font-size: 30px;
            color: #FFFFFF;
        }

        .stat-card p {
            font-size: 12px;
            margin: 2px 0;
            color: #000000;
        }

        .stat-card-time p {
            font-size: 12px;
            color: #FFFFFF;
        }

        h2 {
            margin-top: 2rem;
            color: #333;
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

        .courses-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            flex-wrap: wrap;
            justify-content: flex-start;
            gap: 1rem;
            width: 100%;
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



        .your_courses {
            background: #282842;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            flex-wrap: nowrap;
            padding: 5px 20px;
            border-radius: 15px;
            width: 97%;
        }

        .yourcourses_header {
            display: flex;
            gap: 10px;
        }

        .user-info-mobile {
            display: none;
        }

        .dropdown-btn {
            padding: 10px 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
            background: #fff;
            cursor: pointer;
            width: 140px;

            /* Remove default arrow */
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;

            /* Add custom arrow */
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23000000' stroke-width='2' fill='none'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
        }

        .cross-btn:hover {
            opacity: 1;
        }

        .popup-mode button:hover {
            transform: translateY(-4px);
            transition: transform 0.3s ease;
        }

        .cross-btn {
            width: 12px;
            height: auto;
            opacity: 0.7;
        }


        @media (max-width: 992px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
                justify-content: center;
            }

            .courses-grid {
                display: grid !important;
                grid-template-columns: repeat(1, 1fr);
            }

            .course-card {
                max-width: 100% !important;
                width: 100% !important;
            }

            .stat-card p {
                font-size: 16px;
            }

        }

        /* Small screens (≤ 425px) */
        @media (max-width: 425px) {


            .container {
                padding: 1rem;
            }

            .course-card {
                width: 100%;
            }

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

            .your_courses {
                justify-content: flex-start;
                flex-wrap: wrap;
                padding: 15px 20px;
            }

            .yourcourses_header h2 {
                font-size: 17px;
            }

            .Search_Bar {
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                gap: 1px !important;
                flex-direction: row !important;
                flex-wrap: wrap !important;
                width: 100%;
            }

            .Search_Bar Select {
                width: 100%;
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

            .stat-card,
            .stat-card-time {
                width: 70px;
            }

            .stat-card h2 {
                font-size: 20px;
            }

            .stat-card p {
                font-size: 12px;
            }

            .stat-card-time {
                padding: 5px;
                width: 112px !important;
                height: auto;
            }

            .stat-card-time h2 {
                font-size: 15px;
            }

            .percent-card {
                right: 15px !important;
                top: 106px !important;
                width: 53px !important;
                height: 15% !important;
                padding: 0px !important;
            }

            .percent-no {
                font-size: 18px !important;
            }

            .percent-completed {
                font-size: 8px !important;
            }

            .course_detail_text {
                font-size: 10px !important;
            }

            .Tag {
                padding: 4px 12px !important;
                font-size: 10px !important;
            }

            .course-title {
                font-size: 14px !important;
            }

            .course_details_container {
                gap: 5px !important;
            }

            .ActionButtons a span {
                font-size: 12px !important;
            }

            .ActionButtons {
                align-self: center !important;
            }

            .upperline h2 {
                font-size: 20px !important;
            }

            .popup-mode {
                width: 70% !important;
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
            <img src="{{ asset('images/logo3.png') }}" alt="Company Logo">
        </div>

        <div class="logo2">
            {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
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

        <!-- Right: User Info-mobile -->
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
    <!-- Main Content -->
    <div class="container">
        <div class="header_journey"
            style="display: flex;flex-direction: column;align-items: flex-start;width: 100%; margin: 20px;">
            <div class="upperline" style="display: flex;align-items: center;">
                <img src="{{ asset('images/Increase.png') }}" alt="Learning Journey"
                    style="width:26px; height:26px;margin-right:10px;" />
                <h2 style="color:#000000; margin: 0px; align-self: center; font-family: 'system-ui'; font-weight: 600;">
                    Your Learning Journey</h2>
            </div>
            <p style="font-size: 17px; color: #000000B2; margin-top: 5px; font-weight: 400;">Track your progress and
                achievements</p>
        </div>
        <div class="stats">

            <!-- 1️ Total Courses Purchased -->
            <div class="stat-card" onclick="filterCourses('all')" style="cursor:pointer;">
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('images/total-purchase2.png') }}" alt="Total Courses Purchased"
                        style="width:40px; height:40px;" />
                </div>
                <h2 style="color: #700002;">{{ $totalCourses }}</h2>
                <p>Total Courses Purchased</p>
            </div>

            <!-- 2️ Courses Not Started -->
            <div class="stat-card" onclick="filterCourses('not-started')"
                style="cursor:pointer; border-color: #818181;">
                <div style="margin-bottom:6px;">
                    <img src="{{ asset('images/pending2.png') }}" alt="Courses Not Started"
                        style="width:40px; height:40px;" />
                </div>
                <h2 style="color: #818181;">
                    {{ $courses->filter(function ($course) {
                            return !$course['is_expired'] && !$course['is_disabled'] && $course['progress']->isEmpty();
                        })->count() }}
                </h2>
                <p>Courses Not Started</p>
            </div>

            <!-- 3️ Courses In Progress -->
            <div class="stat-card" onclick="filterCourses('in-progress')"
                style="cursor:pointer; border-color: #5A6AFF;">
                <div style="margin-bottom:6px;">
                    <img src="{{ asset('images/course-in-progress2.png') }}" alt="Courses in Progress"
                        style="width:40px; height:40px;" />
                </div>
                <h2 style="color: #5A6AFF;">
                    {{ $courses->filter(function ($course) {
                            return !$course['is_expired'] &&
                                !$course['is_disabled'] &&
                                $course['progress']->whereNotIn('cmi_core_lesson_status', ['passed', 'completed'])->isNotEmpty();
                        })->count() }}
                </h2>
                <p>Courses in Progress</p>
            </div>

            <!-- 4️ Completed Courses -->
            <div class="stat-card" onclick="filterCourses('completed')" style="cursor:pointer; border-color: #23B358;">
                <div style="margin-bottom:6px;">
                    <img src="{{ asset('images/completed-course2.png') }}" alt="Completed Courses"
                        style="width:40px; height:40px;" />
                </div>
                <h2 style="color: #23B358;">{{ $completedCourses }}</h2>
                <p>Completed Courses</p>
            </div>

            <!-- 5️ Expired Courses -->
            <div class="stat-card" onclick="filterCourses('expired')" style="cursor:pointer; border-color: #F65E1D;">
                <div style="margin-bottom:10px;">
                    <img src="{{ asset('images/course-expire2.png') }}" alt="Expired Courses"
                        style="width:40px; height:40px;" />
                </div>
                <h2 style="color: #F65E1D;">{{ $expiredCoursesCount }}</h2>
                <p>Expired Courses</p>
            </div>

            <!-- 6️ Total Watch Time -->
            <div class="stat-card-time">
                <div style="margin-top:10px;">
                    <img src="{{ asset('images/total-watch-time2.png') }}" alt="Total Watch Time"
                        style="width:40px; height:40px;" />
                </div>
                <h2>{{ gmdate('H:i:s', $totalWatchTime) }}</h2>
                <p>Total Watch Time</p>
            </div>

        </div>

        <div class="your_courses">
            <div class="yourcourses_header">
                <img src="{{ asset('images/your_courses.png') }}" alt="Total Watch Time"
                    style="width:40px; height:40px;" />
                <h2
                    style="color:#FFFFFF; margin: 0px; align-self: center; font-family: 'system-ui'; font-weight: 400;">
                    Your Courses</h2>
            </div>

            <!-- Search + Filter -->
            <div class="Search_Bar" style="display:flex; justify-content:flex-end; align-items:center; gap:40px;">
                <input type="text" id="courseSearch" placeholder="Search your courses..."
                    style="width:100%; padding:8px 10px; border-radius:6px; border:1px solid #ccc; font-size:14px;">
                <select id="courseFilter" class="dropdown-btn"
                    style="padding:8px 30px;border-radius:6px;border: 1px solid #ccc;
                    font-size:14px;-webkit-appearance: none;text-align: left;padding-left: 10px!important; ">
                    <option value="all">All Courses</option>
                    <option value="completed">Completed</option>
                    <option value="in-progress">In Progress</option>
                    <option value="not-started">Not Started</option>
                </select>
            </div>
        </div>

        <!-- Courses Grid -->
        <div class="courses-grid"
            style="display:flex; flex-wrap:nowrap; gap:1rem; justify-content:center; flex-direction: column; align-items: center; margin: 30px 0px;">
            @forelse($courses as $item)
                @php
                    $course = $item['course'] ?? null;
                    $progress = $item['progress'] ?? collect();
                    $view = $item['view'] ?? null;

                    $duration = optional($course)->watch_time ? optional($course)->watch_time * 60 : 0;
                    $totalWatched = $progress->sum('session_time');
                    $masterLimit = $course->view_limit ?? 1;
                    $userViewed = $view->view_limit ?? 0;
                    $currentAttempt = min($userViewed, $masterLimit);
                    $watchedThisAttempt = max(0, $totalWatched - ($currentAttempt - 1) * $duration);
                    $watchedThisAttempt = min($watchedThisAttempt, $duration);

                    $isCompleted = $progress->whereIn('cmi_core_lesson_status', ['completed', 'passed'])->isNotEmpty();

                    if ($progress->isEmpty()) {
                        $status = 'not-started';
                        $percent = 0;
                    } elseif ($isCompleted) {
                        $status = 'completed';
                        $percent = 100;
                    } else {
                        $status = 'in-progress';
                        $percent = $duration > 0 ? round(($watchedThisAttempt / $duration) * 100, 2) : 0;

                        //  Lock 80% until completed
                        if ($percent > 80) {
                            $percent = 80;
                        }
                    }

                    $isDisabled = optional($course)->status == 0 || ($item['is_disabled'] ?? false);
                    $isExpired = $item['is_expired'] ?? false;
                @endphp

                <div class="course-card" data-status="{{ $status }}"
                    data-disabled="{{ $isDisabled ? 'true' : 'false' }}"
                    data-expired="{{ $isExpired ? 'true' : 'false' }}"
                    style="background:white; padding:3rem 1.5rem; border-radius:20px; box-shadow:0 2px 5px rgba(0,0,0,0.05); width: 100%;
                           box-sizing: border-box; position: relative; border-width: 2px 2px 2px 7px; border-style: solid; border-color: {{ $status == 'completed' ? '#58B100' : '#700002' }}; display: flex;
                           flex-direction: column; flex-wrap: nowrap; gap: 10px">

                    <div class="title_n_status"
                        style="display: flex; flex-direction: row; flex-wrap: nowrap; align-items: center;">

                        <div class="course-title"
                            style="font-size:1.5em;font-family: sans-serif;font-weight: 400;color: #000000;">

                            {{ $course->title ?? 'Untitled Course' }}
                        </div>

                        @if ($isExpired)
                            <span class="Tag"
                                style="background: linear-gradient(90deg, #700002 5%, #900205 100%); color:#ffffff;padding: 4px 16px;border-radius: 20px;font-size: 12px;align-content: center;height: fit-content;margin-left: 10px; margin-top: 2px; font-family: sans-serif;">
                                Expired
                            </span>
                        @endif


                    </div>
                    <div class="course-infocontainer">

                        <div class="course-info"
                            style="margin-top: 0;font-size: 12px;display: flex;flex-direction: column;flex-wrap: nowrap;gap: 9px;font-family: sans-serif;">


                            <div class="details_percent-container"
                                style="display: flex;flex-direction: row;justify-content: space-between;">
                                <div class="course_details_container"
                                    style="display: flex;flex-direction: column;justify-content: space-between;">
                                    <div class="course_details"
                                        style="display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;align-items: center;">
                                        <img src="{{ asset('images/Ellipse.svg') }}" alt="Dot"
                                            style="width:10px;height:10px;background: #0000009E;border-radius: 50px;" />
                                        <div class="course_detail_text"
                                            style=" margin-top: 0;font-size: 12px;color: #0000009E;font-family: sans-serif;font-weight: 400;display: flex;margin-left: 8px;">
                                            Attempt: {{ $currentAttempt }} / {{ $masterLimit }}
                                        </div>
                                    </div>


                                    <div class="course_details"
                                        style="display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;align-items: center;">
                                        <img src="{{ asset('images/Ellipse.svg') }}" alt="Dot"
                                            style="width:10px;height:10px;background: #23233BE3;border-radius: 50px;" />
                                        <div class="course_detail_text"
                                            style="margin-bottom:0;color: #23233BE3;font-weight: 400;margin-left:8px">
                                            Watched: {{ gmdate('H:i:s', $watchedThisAttempt) }} /
                                            {{ gmdate('H:i:s', $duration) }}
                                        </div>
                                    </div>

                                    <div class="course_details"
                                        style="display: flex;flex-direction: row;flex-wrap: nowrap;align-content: center;align-items: center;">
                                        <img src="{{ asset('images/Ellipse.svg') }}" alt="Dot"
                                            style="width:10px;height:10px;background: #700002;border-radius: 50px;" />
                                        <div class="course_detail_text"
                                            style="margin-top:0;color: #700002;font-weight: 400;margin-left:8px">
                                            Expire Date:
                                            {{ $item['expire_date'] ? \Carbon\Carbon::parse($item['expire_date'])->format('d M Y h:i A') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                <div class="percent-card"
                                    style="right: 50px;top: 60px;width: 109px;height: 30%;border-radius: 5px;color: #FFFFFF;box-shadow: 5px 5px 10px 1px #00000040;text-align: center; align-content: center; display: grid; padding: 14px 0px;">
                                    <span class="percent-no"
                                        style="font-size:35px; font-weight:normal; color: {{ $status == 'completed' ? '#58B100' : '#700002' }}; font-family: sans-serif;">{{ $percent }}%</span>
                                    <span class="percent-completed"
                                        style="font-family:sans-serif; font-weight: 400; font-size: 10px; color: #000000;">Completed</span>
                                </div>
                            </div>


                            <div class="progress-bar-wrapper" style="display:flex; align-items:center; gap:0.5rem;">
                                <div class="progress-bar"
                                    style="flex:1;background: #D9D9D9;border-radius:5px;height:10px;overflow:hidden;position:relative;box-shadow: 0px 4px 5px -3px #00000040 inset;">
                                    <div class="progress-fill"
                                        style="height:100%; background: {{ $status == 'completed' ? '#58B100' : '#700002' }}; width:{{ $percent }}%; transition:width 0.5s;border-radius:10px;">
                                    </div>
                                </div>

                            </div>

                            {{-- Action Buttons --}}
                            <div class="ActionButtons"
                                style="display:flex; justify-content:flex-start; align-items:center; margin-top:0.5rem; gap:8px;">
                                @if ($status === 'not-started')
                                    <a href="javascript:void(0);"
                                        @if ($isDisabled) onclick="alert('⚠️ This course is currently disabled.');" 
                        style="background:#ccc; cursor:not-allowed; opacity:0.7;"
                    @elseif ($isExpired) 
                        onclick="alert('⚠️ This course has expired.');" 
                        style="background:#ccc; cursor:not-allowed; opacity:0.7;"
                    @else 
                        onclick="openModePage({{ $course->id }})"

                        style="background:#2C2C49;" @endif
                                        class="btn-start"
                                        style="display:inline-flex; align-items:center; justify-content:center; text-align:center; padding:6px 10px; border-radius:6px; height:32px;">
                                        <img src="{{ asset('images/ResumeButton2.png') }}" alt="Start"
                                            style="width:20px; height:20px; margin-right:8px;" />
                                        <span style="color:white; font-family:sans-serif font-size:14px;">Start</span>
                                    </a>
                                @elseif ($status === 'in-progress')
                                    <a href="javascript:void(0);"
                                        @if ($isDisabled) onclick="alert('⚠️ This course is currently disabled.');" 
                        style="background:#ccc; cursor:not-allowed; opacity:0.7;"
                    @elseif ($isExpired) 
                        onclick="alert('⚠️ This course has expired.');" 
                        style="background:#ccc; cursor:not-allowed; opacity:0.7;"
                    @else 
                        onclick="openModePage({{ $course->id }})"

                        style="background:#2C2C49;" @endif
                                        class="btn-resume"
                                        style="display:inline-flex; align-items:center; justify-content:center; text-align:center; background:#d0e4ff; padding:6px 10px; border-radius:6px; height:32px;">
                                        <img src="{{ asset('images/ResumeButton2.png') }}" alt="Resume"
                                            style="width:20px; height:20px; margin-right:5px;" />
                                        <span
                                            style="color:white; font-family:sans-serif; font-size:14px;">Resume</span>
                                    </a>
                                @elseif ($status === 'completed')
                                    @php
                                        $disableCompleted = $currentAttempt >= $masterLimit;
                                    @endphp

                                    <a href="javascript:void(0);"
                                        @if ($disableCompleted) onclick="alert('⚠️ View limit and watch time have been reached!');"
            style="background:#ccc; cursor:not-allowed; opacity:0.7;"
        @else
            onclick="openModePage({{ $course->id }})"

            style="background:#58B100;" @endif
                                        class="btn-resume"
                                        style="display:inline-flex; align-items:center; justify-content:center; text-align:center; padding:6px 10px; border-radius:6px; height:32px;">

                                        <img src="{{ asset('images/Check Mark.png') }}" alt="Completed"
                                            style="width:20px; height:20px; margin-right:5px;" />

                                        <span
                                            style="color:white; font-family:sans-serif; font-size:14px;">Completed</span>
                                    </a>
                                @endif

                                {{-- View Attempts --}}

                                <a href="javascript:void(0);" class="btn-attempts"
                                    @if ($currentAttempt == 1) onclick="alert('⚠️ No attempts yet.');" 
                        span style="background:#ccc; cursor:not-allowed; opacity:0.7;">View
                                        Attempts</span>
                        @else 
                                    onclick="showAttempts('{{ $course->title ?? '' }}')"
                                    style="display:inline-flex; align-items:center; justify-content:center; text-align:center; padding:6px 10px; border-radius:6px; background:#ffffff; border: 2px solid #2C2C49;">
                                    <span style="color:#2C2C49; font-size:14px; font-family: sans-serif;">View
                                        Attempts</span> @endif
                                    </a>
                            </div>
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
        <!-- Study / Test Modal -->
        <div id="modeModal"
            style="display:none; position:fixed; top:0;left:0; width:100%;height:100%; background:rgba(0,0,0,0.5); z-index:9999;justify-content:center; align-items:center; backdrop-filter: blur(4px);">

            <div class="popup-mode"
                style="background: #ffffff;padding:30px;border-radius: 20px;width:380px;height: 360px;text-align:center;position:relative;backdrop-filter: blur(8px);overflow: hidden;">
                <div class="popuptop"
                    style="background: #ffffff;height: 27%;width: 100%;position: absolute;left: 0px;top: 0px;">
                    <span onclick="closeModeModal()"
                        style="position:absolute; top:4px; right:15px; font-size:24px; cursor:pointer;">
                        <img src="{{ asset('images/cross.svg') }}" class="cross-btn" alt="Close" />
                    </span>

                    <h3
                        style="margin-bottom:0px;font-family: system-ui;font-weight: 400;font-size: 24px;color: #000000;margin-top: 36px;">
                        Choose Your Mode</h3>
                    <p style="top: 72px;left: 64px;color: #00000085;font-size: 12px;">
                        Select how you'd like to proceed with this module</p>
                </div>
                <button onclick="goStudy()"
                    style=" width:100%; padding: 20px; padding-top: 10px; margin-bottom:10px; background: #ffffff; color:rgb(0, 0, 0);
                border:none; border-radius: 15px; font-size:16px;margin-top: 120px;display: flex;align-items: center;justify-content:flex-start;flex-wrap: nowrap; box-shadow: 0 8px 25px rgba(0, 0, 0, 30%);cursor:pointer;">
                    <img src="{{ asset('images/Study.png') }}" alt="Study"
                        style="width:50px; height:50px; margin-right:20px; vertical-align: middle;" />
                    <div class="btn-text"
                        style=" display: flex; flex-direction: column; text-align: left;gap: 5px;margin-top: 10px;">
                        <p style=" margin: 0; font-size: 20px; font-weight: 400; font-family: system-ui;">Study</p>
                        <p style=" margin: 0px; font-size: 12px; font-weight: 100;">Learn at your own pace with
                            detailed explanations</p>
                    </div>
                </button>

                <button onclick="goTest()"
                    style="width:100%; padding: 20px; padding-top: 10px; margin-bottom:10px; background: #ffffff; color:rgb(0, 0, 0);
                border:none; border-radius: 15px; font-size:16px;margin-top: 20px;display: flex;align-items: center;justify-content:flex-start;flex-wrap: nowrap; box-shadow: 0 8px 25px rgba(0, 0, 0, 30%);cursor:pointer;">
                    <img src="{{ asset('images/Test.png') }}" alt="Test"
                        style="width:50px; height:50px; margin-right:20px; vertical-align: middle;" />
                    <div class="btn-text"
                        style=" display: flex; flex-direction: column; text-align: left;gap: 5px;margin-top: 10px;">
                        <p style=" margin: 0; font-size: 20px; font-weight: 400; font-family: system-ui;">Test</p>
                        <p style=" margin: 0px; font-size: 12px; font-weight: 100;">Challenge yourself and track your
                            progress</p>
                    </div>
                </button>

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

            //             function openModePage(courseId) {
            //     // redirect to mode selection page
            //     window.location.href = "/course-mode/" + courseId;
            // }

            // Suggestions ke liye course names collect karo (page load pe ek baar)
            const courseTitles = Array.from(document.querySelectorAll(".course-card .course-title"))
                .map(el => ({
                    text: el.textContent.trim(),
                    card: el.closest(".course-card")
                }));

            // Suggestion box HTML add karo search input ke baad
            const searchInput = document.getElementById("courseSearch");
            const suggestionBox = document.createElement("div");
            suggestionBox.id = "courseSuggestionBox";
            suggestionBox.style.cssText = `
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 6px 6px;
    z-index: 999;
    max-height: 220px;
    overflow-y: auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
`;
            searchInput.parentElement.style.position = "relative";
            searchInput.parentElement.appendChild(suggestionBox);

            // Search input event
            searchInput.addEventListener("input", function() {
                const val = this.value.toLowerCase().trim();
                const filterValue = document.getElementById("courseFilter").value;

                // Filter cards (existing logic)
                document.querySelectorAll(".course-card").forEach(function(card) {
                    const title = card.querySelector(".course-title")?.textContent.toLowerCase() || "";
                    const status = card.getAttribute("data-status");
                    let matchesSearch = title.includes(val);
                    let matchesFilter = filterValue === "all" || status === filterValue;
                    card.style.display = (matchesSearch && matchesFilter) ? "block" : "none";
                });

                // Suggestions dropdown
                if (!val) {
                    suggestionBox.style.display = "none";
                    return;
                }

                const matches = courseTitles.filter(c => c.text.toLowerCase().includes(val));

                if (!matches.length) {
                    suggestionBox.style.display = "none";
                    return;
                }

                suggestionBox.innerHTML = matches.map(c => {
                    const idx = c.text.toLowerCase().indexOf(val);
                    const highlighted =
                        c.text.slice(0, idx) +
                        `<strong style="color:#700002">${c.text.slice(idx, idx + val.length)}</strong>` +
                        c.text.slice(idx + val.length);
                    return `<div class="suggestion-item" data-value="${c.text}" 
                    style="padding:10px 14px; font-size:14px; cursor:pointer; color:#444; border-bottom:1px solid #f0f0f0;"
                    onmouseover="this.style.background='#f9f9f9'" 
                    onmouseout="this.style.background=''">
                    ${highlighted}
                </div>`;
                }).join("");

                suggestionBox.style.display = "block";

                // Click on suggestion
                suggestionBox.querySelectorAll(".suggestion-item").forEach(item => {
                    item.addEventListener("mousedown", function() {
                        searchInput.value = this.getAttribute("data-value");
                        suggestionBox.style.display = "none";
                        searchInput.dispatchEvent(new Event("input"));
                    });
                });
            });

            // Hide suggestions on outside click
            document.addEventListener("click", function(e) {
                if (!searchInput.contains(e.target) && !suggestionBox.contains(e.target)) {
                    suggestionBox.style.display = "none";
                }
            });

            // Filter change event (same as before)
            document.getElementById("courseFilter").addEventListener("change", function() {
                searchInput.dispatchEvent(new Event("input"));
            });

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

            function filterCourses(status) {
                const cards = document.querySelectorAll('.course-card');

                cards.forEach(card => {
                    const courseStatus = card.dataset.status;
                    const isDisabled = card.dataset.disabled === 'true';
                    const isExpired = card.dataset.expired === 'true';

                    card.style.display = 'none';

                    if (isDisabled) return;

                    switch (status) {
                        case 'not-started':
                            if (courseStatus === 'not-started' && !isExpired) card.style.display = 'block';
                            break;
                        case 'in-progress':
                            if (courseStatus === 'in-progress' && !isExpired) card.style.display = 'block';
                            break;
                        case 'completed':
                            if (courseStatus === 'completed') card.style.display = 'block';
                            break;
                        case 'expired':
                            if (isExpired) card.style.display = 'block';
                            break;
                        case 'all':
                        default:
                            card.style.display = 'block';
                    }
                });
            }

            let selectedCourseId = null;

            function openModePage(courseId) {
                selectedCourseId = courseId;
                document.getElementById('modeModal').style.display = 'flex';
            }

            function closeModeModal() {
                document.getElementById('modeModal').style.display = 'none';
            }

            function goStudy() {
                if (!selectedCourseId) {
                    alert('Course ID missing!');
                    return;
                }

                closeModeModal();

                window.location.href = `/course/${selectedCourseId}/chapters`;
            }

            function openChapter(chapterId) {

                const modal = document.getElementById('chapterModal');
                if (modal) {
                    modal.remove();
                }

                const width = window.screen.availWidth;
                const height = window.screen.availHeight;

                const popup = window.open(
                    `/view/chapter/${chapterId}`,
                    '_blank',
                    `width=${width},height=${height},top=0,left=0,resizable=yes,scrollbars=yes`
                );

                if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                    alert('Please allow popups to open the chapter.');
                } else {
                    popup.focus();
                }
            }

            function goTest() {
                if (!selectedCourseId) {
                    alert('Course ID missing!');
                    return;
                }

                closeModeModal();
                window.location.href = `/user/${selectedCourseId}`;
            }
        </script>

</body>

</html>
