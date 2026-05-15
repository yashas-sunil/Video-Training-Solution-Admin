<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            justify-content: flex-start;
            align-items: center;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            box-sizing: border-box;
            z-index: 1;
        }

        .navbar-left,
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .navbar-right {
            margin-left: auto;
        }

        .navbar .logo img {
            height: 80px;
            width: auto;
        }

        .divider {
            width: 1px;
            height: 40px;
            border: none;
            background-color: rgba(255, 255, 255, 0.6);
        }

        .partner-block {
            display: flex;
            flex-direction: column;
            gap: 5px;
            align-items: flex-start;
        }

        .partner-text {
            font-size: 12px;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .logo2 img {
            height: 50px;
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

        .user-info-mobile a {
            width: 100%;
            display: inline-flex;
            justify-content: center;
            text-decoration: none;
            color: white;
        }

        .user-info-mobile .user-welcome {
            width: 100%;
            max-width: 420px;
            justify-content: center;
            padding: 10px 15px;
        }

        .page-content {
            padding: 30px;
            margin-top: 140px;
        }

        .page-header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding-bottom: 10px;
        }

        .back-btn {
            background: #ffffff;
            color: #700002;
            border: 2px solid #700002;
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .back-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
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

        .chapter-item.locked-chapter {
            pointer-events: none;
            opacity: 0.5;
            cursor: not-allowed !important;
            border-color: #999;
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

        .open-text:hover {
            background: #1f1f35;
        }

        .open-text.locked {
            background: #999 !important;
            cursor: not-allowed !important;
            pointer-events: all;
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

        .attempt-btn:hover {
            background: #f0f0f0;
        }

        .attempt-btn.disabled {
            background: #ccc;
            cursor: not-allowed;
            opacity: 0.7;
            border: 2px solid #999;
        }

        /* Loading indicator */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #700002;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
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

            .user-info {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <div class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <img src="{{ asset('images/logo1.png') }}" alt="Company Logo">
            </div>
            <hr class="divider">
            <div class="partner-block">
                <div class="partner-text">EDUCATION PARTNER</div>
                <div class="logo2">
                    <img src="{{ asset('images/logo2.png') }}" alt="Partner Logo">
                </div>
            </div>
        </div>

        <div class="navbar-right">
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
    </div>

    <div class="page-content">
       <div class="page-header">
    <button onclick="handleBack({{ $courseId }})" class="back-btn">
    ⬅ Back
</button>
</div>

        <h2>Course Chapters</h2>

        <div class="filter-container">
            <label for="subjectFilter">Filter by Subject:</label>
            <select id="subjectFilter" onchange="filterChaptersBySubject()">
                <option value="">-- All Subjects --</option>
                @foreach ($subjects as $subject)
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
                    $isLocked = $chapter->is_locked ?? false;
                @endphp

                <div class="chapter-item {{ $isLocked ? 'locked-chapter' : '' }}"
                    data-chapter-id="{{ $chapter->id }}" data-course-id="{{ $chapter->course_id ?? '' }}"
                    data-subject-id="{{ $chapter->subject_id }}">

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
                            $btnType = 'completed';
                        } elseif ($percent > 0) {
                            $btnText = 'Resume';
                            $btnType = 'resume';
                        } else {
                            $btnText = 'Start';
                            $btnType = 'start';
                        }
                    @endphp

                    <div class="list_buttons">
                        @if ($isLocked)
                            <div class="open-text locked"
                                onclick="event.stopPropagation(); alert('Course attempt limit reached. Please contact administrator.');">
                                🔒 Limit Reached
                            </div>
                        @else
                            <div class="open-text" data-button-type="{{ $btnType }}"
                                onclick="handleChapterClick(event, {{ $chapter->id }}, '{{ $btnType }}')">
                                {{ $btnText }}
                            </div>
                        @endif

                        <div class="attempt-btn {{ $isLocked ? 'disabled' : '' }}"
                            onclick="event.stopPropagation(); {{ $isLocked ? '' : "showAttempts('" . addslashes($chapter->name) . "')" }}">
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
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.addEventListener('DOMContentLoaded', function () {
        filterChaptersBySubject();
    });

    function filterChaptersBySubject() {
        const selectedSubjectId = document.getElementById('subjectFilter').value;
        const chapterItems = document.querySelectorAll('.chapter-item');

        const oldMsg = document.getElementById('noChaptersMsg');
        if (oldMsg) oldMsg.remove();

        let visibleCount = 0;

        chapterItems.forEach(item => {
            if (selectedSubjectId === '' || item.getAttribute('data-subject-id') === selectedSubjectId) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            const container = document.getElementById('chapterListContainer');
            const noChaptersMsg = document.createElement('div');
            noChaptersMsg.id = 'noChaptersMsg';
            noChaptersMsg.textContent = 'No chapters available for this subject.';
            noChaptersMsg.style.cssText = 'text-align: center; padding: 20px; color: #666;';
            container.appendChild(noChaptersMsg);
        }
    }

    // 🔥 MAIN CLICK HANDLER
    async function handleChapterClick(event, chapterId, buttonType) {
        event.stopPropagation();

        const chapterItem = event.target.closest('.chapter-item');

        if (chapterItem.classList.contains('locked-chapter')) {
            alert('Course attempt limit reached. Please contact administrator.');
            return;
        }

        const courseId = chapterItem.getAttribute('data-course-id');

        showLoading(true);

        try {
            // 1️⃣ Record click
            await recordButtonClick(chapterId, courseId, buttonType);

            // 2️⃣ 🔥 Attempt API call (IMPORTANT)
            await incrementCourseAttempt(courseId);

            // 3️⃣ Open chapter AFTER API
            openChapter(chapterId);

        } catch (err) {
            console.error(err);
        } finally {
            showLoading(false);
        }
    }

    // ✅ Record click API
    async function recordButtonClick(chapterId, courseId, buttonType) {
        const response = await fetch('/chapter/record-click', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                chapter_id: chapterId,
                course_id: courseId || null,
                button_type: buttonType
            })
        });

        return await response.json();
    }

    // ✅ YOUR ATTEMPT API
    async function incrementCourseAttempt(courseId) {
        const response = await fetch('/course/increment-attempt', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                course_id: courseId
            })
        });

        const data = await response.json();
        console.log("Attempt Response:", data);

        if (!data.success) {
            alert(data.message);
            throw new Error("Attempt limit reached");
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

    function showLoading(show) {
        const overlay = document.getElementById('loadingOverlay');
        if (show) overlay.classList.add('active');
        else overlay.classList.remove('active');
    }
    
    async function handleBack(courseId) {
    try {
        await incrementCourseAttempt(courseId); 
    } catch (e) {
        console.log(e);
    } finally {
        window.location.href = '/user/dashboard'; // redirect
    }
}
</script>

</body>

</html>
