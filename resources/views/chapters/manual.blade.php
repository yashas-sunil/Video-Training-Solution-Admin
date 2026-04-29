<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $chapter->name }} - Manual Content</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
            color: #222;
        }

        .page {
            max-width: 100%;
        }

        .header {
            display: flex;
            justify-content: end;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
        }

        .btn {
            height: fit-content;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #700002;
            background: #700002;
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn:hover {
            background: #ffffff;
            color: #700002;
        }

        .btn.secondary {
            background: #fff;
            color: #700002;
        }

        .overview {
            margin-bottom: 40px;
        }

        .grid {
            display: flex;
            gap: 60px;
            flex-direction: column;
            padding-left: 60px;
            padding-right: 60px;
        }

        .card {
            display: flex;
            background: #fff;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #700002;
            justify-content: space-between;
            align-items: center;
        }

        .overview-item {
            display: flex;
            flex-direction: column;
            gap: 24px;
            margin-bottom: 26px;
        }

        .overview-item .label {
            min-width: 180px;
            font-weight: bold;
            font-size: 16px;
            color: #700002;
        }

        .label {
            padding-left: 60px;
            padding-right: 60px;
        }

        .lessons-wrapper {
            display: flex;
            gap: 20px;
        }

        .lessons-list {
            width: 240px;
            padding: 10px;
            background: #700002;
            height: -webkit-fill-available;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            width: 260px;
            top: 0;
            left: 0;
            z-index: 1500;
        }

        .lessons-list button {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            margin-bottom: 6px;
            padding: 8px;
            border-radius: 6px;
            background: #ffffff40;
            border: none;
            color: #ffffff;
            cursor: pointer;
            font-weight: 600;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            text-align: left;
            gap: 8px;
        }

        .lessons-list button:hover {
            background: #ffffff;
            color: #700002;
        }

        .lesson-tab:hover .tab-icon {
            filter: brightness(0) saturate(100%) invert(9%) sepia(100%) saturate(9800%) hue-rotate(352deg) brightness(47%) contrast(112%);
        }

        .lesson-contents {
            flex: 1;
            min-width: 0;
            margin-left: 260px;
        }

        .lesson-section {
            display: none;
            margin-bottom: 60px;
        }

        .continue-wrap {
            margin-top: 30px;
            text-align: center;
        }

        .course-layout {
            display: flex;
            min-height: 100vh;
        }

        .scroll-pane {
            overflow-y: auto;
            min-block-size: 0;
            min-height: 0;
        }

        /* ✅ FIX: Chapter hero - text properly wrapped */
        .chapter-hero {
            background-size: cover;
            background-position: center;
            padding: 30px 16px;
            position: relative;
            background: #700002;
            display: flex;
            gap: 20px;
            flex-direction: column;
            flex-shrink: 0;
        }

        .chapter-hero::after {
            content: "";
            position: absolute;
            inset: 0;
        }

        .chapter-hero h1 {
            position: relative;
            z-index: 2;
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        .lesson-tab {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lesson-tab input {
            cursor: pointer;
            flex-shrink: 0;
        }

        .lesson-tab.active {
            font-weight: bold;
        }

        .lesson-tab input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 10px;
            display: grid;
            place-content: center;
            cursor: pointer;
            background: #858282;
            flex-shrink: 0;
        }

        .lesson-tab input[type="checkbox"]:checked {
            background-color: #ff746777;
        }

        .lesson-tab input[type="checkbox"]:checked::before {
            content: "✓";
            color: white;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
        }

        .progress-container {
            position: relative;
            width: 100%;
            height: 6px;
            background: #b5b5b5;
            border-radius: 6px;
            box-shadow: 5px 4px 7px 2px #00000040 inset;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: #ffffff;
            border-radius: 6px;
            transition: width 0.3s ease;
        }

        .progress-text {
            position: absolute;
            right: 10px;
            top: -22px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        /* ✅ FIX: Tab label text wrapping */
        .tab-label {
            display: flex;
            align-items: center;
            gap: 8px;
            flex: 1;
            min-width: 0;
            word-wrap: break-word;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .tab-icon {
            width: 24px;
            height: 24px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .icon_chapter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .card {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .inline-viewer {
            width: 100%;
        }

        .inline-viewer iframe,
        .inline-viewer img {
            width: 100%;
            height: 70vh;
            border: none;
            border-radius: 8px;
            object-fit: contain;
        }

        .inline-viewer video {
            width: 100%;
            max-width: 900px;
            height: auto;
            max-height: 70vh;
            display: block;
            margin: 0 auto;
            border-radius: 8px;
            background: black;
        }

        .inline-viewer video::-webkit-media-controls-panel {
            background-image: none !important;
        }

        .file-name,
        .viewer-title,
        .video-name {
            display: none !important;
        }

        .lessons-list button input {
            pointer-events: none;
        }

        /* ✅ Sidebar lessons list scroll */
        .sidebar .lessons-list.scroll-pane {
            flex: 1;
            overflow-y: auto;
            height: auto;
            min-height: 0;
        }

        @media (max-width: 768px) {
            iframe {
                height: 100vh !important;
            }

            body {
                overflow-x: hidden;
                -webkit-tap-highlight-color: transparent;
            }

            .sidebar {
                position: fixed;
                top: 0;
                left: -280px;
                width: 260px;
                height: 100vh;
                background: #700002;
                z-index: 1200;
                transition: left 0.3s ease;
                box-shadow: 4px 0 18px rgba(0, 0, 0, 0.25);
            }

            .sidebar.mobile-open {
                left: 0;
            }

            .mobile-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.45);
                z-index: 1100;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .mobile-backdrop.show {
                opacity: 1;
                pointer-events: auto;
            }

            .lesson-contents {
                margin-left: 0 !important;
                width: 100%;
                padding-top: 72px;
            }

            .lesson-section {
                margin-left: 0 !important;
            }

            .grid {
                padding: 16px;
                gap: 24px;
            }

            h2 {
                padding: 20px 16px !important;
                font-size: 22px !important;
                line-height: 1.3;
            }

            h3 {
                padding: 0 16px !important;
                font-size: 16px;
            }

            .card {
                gap: 12px;
            }

            .card .btn {
                width: 100%;
                justify-content: center;
            }

            .inline-viewer iframe,
            .inline-viewer img,
            .inline-viewer video {
                width: 100%;
                height: 45vh;
                max-height: 45vh;
                border-radius: 8px;
                object-fit: contain;
            }

            .mobile-sidebar-toggle {
                position: fixed;
                top: 14px;
                left: 14px;
                z-index: 1300;
                width: 44px;
                height: 44px;
                border-radius: 8px;
                background: #700002;
                color: #ffffff;
                border: none;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                cursor: pointer;
                box-shadow: 0 6px 14px rgba(0, 0, 0, 0.3);
            }

            .mobile-sidebar-toggle:active {
                transform: scale(0.96);
            }

            /* ✅ Mobile chapter title fix */
            .chapter-hero h1 {
                font-size: 16px;
            }
        }
    </style>

</head>

<body>
    <div class="page">
        <div class="course-layout">
            <button class="mobile-sidebar-toggle" onclick="toggleSidebar()">☰</button>

            {{-- 🔹 LEFT SIDEBAR --}}
            <div class="sidebar">
                <div class="chapter-hero">
                    <h1>{{ $chapter->name }}</h1>

                    <div class="progress-container">
                        <div class="progress-bar"></div>
                        <span class="progress-text">0%</span>
                    </div>
                </div>

                <div class="lessons-list scroll-pane">
                    <button class="lesson-tab active" data-index="overview">
                        <span class="tab-label">
                            <img src="{{ asset('images/overview-icon.png') }}" class="tab-icon">
                            <span>Overview</span>
                        </span>
                        <input type="checkbox" checked disabled>
                    </button>

                    @foreach ($lessons as $i => $lesson)
                        <button class="lesson-tab" data-index="{{ $i }}">
                            <span class="tab-label">
                                <img src="{{ asset('images/lesson-icon.png') }}" class="tab-icon">
                                <span>{{ $lesson['lesson_name'] }}</span>
                            </span>
                            <input type="checkbox" data-check="{{ $i }}">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 🔹 RIGHT CONTENT --}}
            <div class="lesson-contents scroll-pane">

                {{-- 🔹 OVERVIEW --}}
                <div class="lesson-section" id="lesson-overview" data-index="overview">
                    <div class="overview">

                        @foreach ($overview as $label => $items)
                            <div class="overview-item">
                                <div class="grid">

                                    @foreach ($items as $content)
                                        @php
                                            $ext = strtolower(pathinfo($content['url'], PATHINFO_EXTENSION));
                                        @endphp

                                        <div class="card">
                                            <div class="media-title">{{ $content['original_name'] }}</div>

                                            @if (in_array($ext, ['mp4', 'webm', 'ogg']))
                                                <div class="inline-viewer show">
                                                    <video class="lesson-video" controls playsinline preload="none"
                                                        controlsList="nodownload noremoteplayback">
                                                        <source src="{{ $content['url'] }}" type="video/mp4">
                                                    </video>
                                                </div>
                                            @else
                                                <button class="btn open-inline"
                                                    data-url="{{ $content['url'] }}">Open</button>
                                                <div class="inline-viewer"></div>
                                            @endif
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        @endforeach

                        <div class="continue-wrap">
                            <button class="btn unlock-once" data-next="0">Start Lesson →</button>
                        </div>

                    </div>
                </div>

                {{-- 🔹 LESSONS --}}
                @foreach ($lessons as $i => $lesson)
                    <div class="lesson-section" id="lesson-{{ $i }}" data-index="{{ $i }}">

                        <h2 style="background:#fff;padding:60px;color:#700002;font-size:40px;">
                            {{ $lesson['lesson_name'] }}
                        </h2>

                        @foreach ($lesson['contents'] as $type => $items)
                            <h3 style="padding:0 60px;color:#700002;">{{ $type }}</h3>

                            <div class="grid">

                                @foreach ($items as $content)
                                    @php
                                        $ext = strtolower(pathinfo($content['url'], PATHINFO_EXTENSION));
                                    @endphp

                                    <div class="card">
                                        <div class="media-title">{{ $content['original_name'] }}</div>

                                        @if (in_array($ext, ['mp4', 'webm', 'ogg']))
                                            <div class="inline-viewer show">
                                                <video class="lesson-video" controls playsinline preload="none"
                                                    controlsList="nodownload noremoteplayback">
                                                    <source src="{{ $content['url'] }}" type="video/mp4">
                                                </video>
                                            </div>
                                        @else
                                            <button class="btn open-inline"
                                                data-url="{{ $content['url'] }}">Open</button>
                                            <div class="inline-viewer"></div>
                                        @endif
                                    </div>
                                @endforeach

                            </div>
                        @endforeach

                        @if (isset($lessons[$i + 1]))
                            <div class="continue-wrap">
                                <button class="btn unlock-once" data-next="{{ $i + 1 }}">Continue →</button>
                            </div>
                        @endif

                    </div>
                @endforeach

            </div>
        </div>
    </div>

    <script>
        const tabs = document.querySelectorAll(".lesson-tab");
        const checks = document.querySelectorAll("[data-check]");
        const continueBtns = document.querySelectorAll(".unlock-once");

        let unlockedIndex = -1;

        function setActiveTab(targetIndex) {
            tabs.forEach(t => t.classList.remove("active"));
            const btn = document.querySelector(`.lesson-tab[data-index="${targetIndex}"]`);
            if (btn) btn.classList.add("active");
        }

        function scrollToSection(targetIndex) {
            let sectionId = targetIndex === "overview" ? "lesson-overview" : "lesson-" + targetIndex;
            const section = document.getElementById(sectionId);
            if (!section) return;

            const container = document.querySelector(".lesson-contents");
            const isContainerScrollable = container && container.scrollHeight > container.clientHeight;

            if (isContainerScrollable) {
                container.scrollTo({
                    top: section.offsetTop - 20,
                    behavior: "smooth"
                });
            } else {
                const y = section.getBoundingClientRect().top + window.pageYOffset - 20;
                window.scrollTo({
                    top: y,
                    behavior: "smooth"
                });
            }

            setActiveTab(targetIndex);
        }

        function render() {
            const sections = document.querySelectorAll(".lesson-section");
            sections.forEach(sec => {
                sec.style.display = "none";
                const btn = sec.querySelector(".unlock-once");
                if (btn) btn.style.display = "none";
            });

            const overview = document.getElementById("lesson-overview");
            overview.style.display = "block";

            if (unlockedIndex === -1) {
                const startBtn = overview.querySelector(".unlock-once");
                if (startBtn) startBtn.style.display = "inline-flex";
            }

            for (let i = 0; i <= unlockedIndex; i++) {
                const el = document.getElementById("lesson-" + i);
                if (el) el.style.display = "block";
            }

            if (unlockedIndex >= 0) {
                const last = document.getElementById("lesson-" + unlockedIndex);
                if (last) {
                    const btn = last.querySelector(".unlock-once");
                    if (btn) btn.style.display = "inline-flex";
                }
            }

            checks.forEach(chk => {
                const i = parseInt(chk.dataset.check);
                chk.checked = i <= unlockedIndex;
            });

            updateProgress();
        }

        function updateProgress() {
            const total = tabs.length - 1;
            const percent = total > 0 ? Math.round((unlockedIndex + 1) / total * 100) : 0;

            document.querySelector(".progress-bar").style.width = percent + "%";
            const progressText = document.querySelector(".progress-text");
            progressText.textContent = percent + "%";
            progressText.style.opacity = "0.6";

            saveProgressToDatabase(percent);

            setTimeout(() => {
                progressText.style.opacity = "1";
            }, 500);
        }

        function saveProgressToDatabase(percent) {
            let courseId = {{ $chapter->course_id ?? 0 }};
            const chapterId = {{ $chapter->id ?? 0 }};

            if (!courseId) {
                const urlMatch = window.location.href.match(/\/course\/(\d+)/);
                if (urlMatch) {
                    courseId = parseInt(urlMatch[1]);
                }
            }

            console.log('Saving Progress:', {
                courseId,
                chapterId,
                percent
            });

            if (!courseId) {
                console.warn('❌ Course ID not found! Cannot save progress.');
                return;
            }

            const payload = {
                course_id: courseId,
                chapter_id: chapterId,
                progress_percent: percent,
            };

            fetch('{{ route('course.progress.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(data => console.log('Progress saved:', data))
                .catch(err => console.error('Error saving progress:', err));
        }

        continueBtns.forEach(btn => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                const next = parseInt(btn.dataset.next);
                unlockedIndex = next;
                render();
                setTimeout(() => {
                    scrollToSection(next);
                }, 150);
            });
        });

        tabs.forEach(tab => {
            tab.addEventListener("click", (e) => {
                e.preventDefault();
                if (window.innerWidth <= 768) {
                    document.querySelector('.sidebar')?.classList.remove('mobile-open');
                }
                const idx = tab.dataset.index;

                if (idx === "overview") {
                    render();
                    setTimeout(() => {
                        scrollToSection("overview");
                    }, 100);
                    return;
                }

                const lessonIndex = parseInt(idx);

                if (lessonIndex > unlockedIndex) {
                    unlockedIndex = lessonIndex;
                }

                render();
                setTimeout(() => {
                    scrollToSection(idx);
                }, 100);
            });
        });

        document.querySelectorAll(".open-inline").forEach(btn => {
            btn.addEventListener("click", async function() { // ✅ async add kiya
                const url = this.dataset.url;
                const viewer = this.nextElementSibling;
                const ext = url.split('.').pop().toLowerCase();

                if (viewer.classList.contains("show")) {
                    viewer.classList.remove("show");
                    viewer.innerHTML = "";
                    return;
                }

                // ✅ NAYA - Auth check pehle
                try {
                    const response = await fetch(url, {
                        method: 'HEAD',
                        cache: 'no-cache'
                    });

                    if (response.status === 403) {
                        alert('⚠️ Please login to view this file');
                        window.location.href = '/login';
                        return;
                    }

                    if (!response.ok) {
                        alert('❌ File not accessible');
                        return;
                    }
                } catch (error) {
                    alert('❌ Please login to access this file');
                    window.location.href = '/login';
                    return;
                }

                // Timestamp add karke cache bypass
                const timestamp = new Date().getTime();

                if (ext === "pdf") {
                    const cacheBypassUrl = url + (url.includes('?') ? '&' : '?') + '_t=' + timestamp;
                    viewer.innerHTML =
                        `<iframe src="/pdfjs/web/viewer.html?file=${encodeURIComponent(cacheBypassUrl)}" style="width:100%; height:70vh; border:none;"></iframe>`;
                } else {
                    viewer.innerHTML = `<img src="${url}?t=${timestamp}">`;
                }

                viewer.classList.add("show");
                viewer.scrollIntoView({
                    behavior: "smooth",
                    block: "center"
                });
            });
        });

        document.querySelectorAll(".lesson-video").forEach(video => {
            video.autoplay = false;
            video.addEventListener("play", () => {
                document.querySelectorAll("video").forEach(v => {
                    if (v !== video) v.pause();
                });
            });
        });

        const savedProgress = {{ $userProgress ?? 0 }};
        const totalLessons = tabs.length - 1;

        if (savedProgress > 0 && totalLessons > 0) {
            const calculatedIndex = Math.round((savedProgress / 100) * totalLessons) - 1;
            unlockedIndex = Math.max(0, calculatedIndex);

            console.log('📚 Resuming from saved progress:', {
                savedProgress: savedProgress + '%',
                totalLessons: totalLessons,
                resumingAtLesson: unlockedIndex
            });

            render();
            setActiveTab(unlockedIndex);

            setTimeout(() => {
                scrollToSection(unlockedIndex);
            }, 300);
        } else {
            render();
            setActiveTab("overview");
        }

        function toggleSidebar() {
            document.querySelector('.sidebar')?.classList.toggle('mobile-open');
        }

        document.addEventListener("click", function(e) {
            if (window.innerWidth > 768) return;

            const sidebar = document.querySelector(".sidebar");
            const toggleBtn = document.querySelector(".mobile-sidebar-toggle");

            if (!sidebar || !sidebar.classList.contains("mobile-open")) return;
            if (sidebar.contains(e.target)) return;
            if (toggleBtn && toggleBtn.contains(e.target)) return;

            sidebar.classList.remove("mobile-open");
        });
    </script>

</body>

</html>
