<!DOCTYPE html>
<html lang="en">


<head>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* overview layout */
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
            width: 210px;
            padding: 10px;
            background: #700002;
            height: -webkit-fill-available;
        }

        .sidebar {
            display: flex;
            flex-direction: column;
            position: fixed;
            height: -webkit-fill-available;
        }

        .lessons-list button {
            display: flex;
            justify-content: space-between;
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

        }

        .lessons-list button:hover {
            background: #ffffff;
            color: #700002;
        }

        /* Invert icon when its parent tab is hovered */
        .lesson-tab:hover .tab-icon {
            filter:
                brightness(0) saturate(100%) invert(9%) sepia(100%) saturate(9800%) hue-rotate(352deg) brightness(47%) contrast(112%);
        }





        .lesson-contents {
            flex: 1;
            min-width: 0;
        }


        /* 🔥 IMPORTANT: lesson kabhi hide nahi hoga */
        .lesson-section {
            display: none;
            margin-bottom: 60px;
            margin-left: 230px;
        }

        .continue-wrap {
            margin-top: 30px;
            text-align: center;
        }

        .course-layout {
            display: flex;
            height: 80vh;
        }

        .scroll-pane {
            overflow-y: auto;
            min-block-size: fit-content;
        }

        .lessons-list {
            flex-shrink: 0;
        }

        .chapter-hero {
            background-size: cover;
            background-position: center;
            padding: 50px 20px;
            position: relative;
        }

        /* Dark overlay so text is readable */
        .chapter-hero::after {
            content: "";
            position: absolute;
            inset: 0;
        }

        /* Title styling */
        .chapter-hero h1 {
            position: relative;
            z-index: 2;
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .lesson-tab {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .lesson-tab input {
            cursor: pointer;
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
        }



        /* Checked background */
        .lesson-tab input[type="checkbox"]:checked {
            background-color: #ff746777;
        }

        /* White tick */
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
            margin-top: 20px;
            box-shadow: 5px 4px 7px 2px #00000040 inset;
        }

        .progress-bar {
            height: 100%;
            width: 0%;
            background-color: #ffffff;
            /* green */
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

        .tab-label {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-icon {
            width: 30px;
            height: auto;
            object-fit: contain;
        }

        .icon_chapter {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        #fileViewerWrapper{
    width: 100%;
    margin-left: 230px;     /* sidebar ke baad align */
    background: #ffffff;
    padding: 10px;
    border-bottom: 2px solid #700002;
}

#fileViewerWrapper iframe{
    width: 100%;
    height: 70vh;
    border: none;
}

    </style>
</head>

<body>

    <div class="page">


        {{-- 🔹 OVERVIEW (no click, same as before) --}}
        {{-- ===================== --}}
        {{-- 🔹 TAB SIDEBAR --}}
        {{-- ===================== --}}

        <div class="course-layout">

            {{-- ===================== --}}
            {{-- 🔹 LEFT SIDEBAR --}}
            {{-- ===================== --}}
            <div class="sidebar">
                <div class="chapter-hero" style="display: flex;background: #700002;gap: 30px;flex-direction: column;">
                    <h1>{{ $chapter->name }}</h1>

                    <!-- Progress Bar -->
                    <div class="progress-container">
                        <div class="progress-bar"></div>
                        <span class="progress-text">0%</span>
                    </div>
                </div>


                <div class="lessons-list scroll-pane">

                    <button class="lesson-tab active" data-index="overview">
                        <span class="tab-label">
                            <img src="{{ asset('images/overview-icon.png') }}" class="tab-icon">
                            Overview
                        </span>
                        <input type="checkbox" style="border:none;" checked disabled>
                    </button>

                    @foreach ($lessons as $i => $lesson)
                        <button class="lesson-tab" data-index="{{ $i }}">
                            <span class="tab-label"><img src="{{ asset('images/lesson-icon.png') }}"
                                    class="tab-icon">{{ $lesson['lesson_name'] }}</span>
                            <input type="checkbox" data-check="{{ $i }}">
                        </button>
                    @endforeach

                </div>
            </div>


            {{-- ===================== --}}
            {{-- 🔹 RIGHT CONTENT --}}
            {{-- ===================== --}}
     <div class="lesson-contents scroll-pane">

    <!-- 🔥 INLINE PDF VIEWER -->
    <div id="fileViewerWrapper" style="display:none; background:#ffffff; padding:10px;">
        <button onclick="closeViewer()" class="btn secondary" style="margin-bottom:10px;">
            ← Back
        </button>
        <iframe id="fileViewer" style="width:100%; height:70vh; border:none;"></iframe>
    </div>


    {{-- 🔹 OVERVIEW --}}
    <div class="lesson-section" id="lesson-overview" data-index="overview">

        <div class="overview">
            <h2 style="background: #000000;padding: 55px;color: #ffffff;margin-top: 0;font-size: 40px;font-family: 'Lato', sans-serif;">
                Overview</h2>

            @foreach ($overview as $label => $items)
                <div class="overview-item">
                    <div class="label">{{ $label }}</div>
                    <div class="content">
                        <div class="grid">
                            @foreach ($items as $content)
                                <div class="card">
                                    <h3>{{ $content['original_name'] }}</h3>

                                    <a class="btn view-file" href="javascript:void(0)" data-url="{{ $content['url'] }}">View</a>

                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="continue-wrap">
            <button class="btn continue-btn unlock-once" data-next="0">Start Lesson →</button>
        </div>
    </div>


    {{-- 🔹 LESSONS --}}
    @foreach ($lessons as $i => $lesson)
        <div class="lesson-section" id="lesson-{{ $i }}" data-index="{{ $i }}">

            <h2 class="lesson-title" style="background: #727272; padding: 24px; color: #ffffff;">
                {{ $lesson['lesson_name'] }}</h2>

            @foreach ($lesson['contents'] as $type => $items)
                <h3 style=" padding-left: 60px; padding-right: 60px;">{{ $type }}</h3>
                <div class="grid">
                    @foreach ($items as $content)
                        <div class="card">
                            <h4>{{ $content['original_name'] }}</h4>

                            <a class="btn view-file" href="javascript:void(0)" data-url="{{ $content['url'] }}">View</a>

                        </div>
                    @endforeach
                </div>
            @endforeach

            @if (isset($lessons[$i + 1]))
                <div class="continue-wrap">
                    <button class="btn continue-btn unlock-once" data-next="{{ $i + 1 }}">
                        Continue →
                    </button>
                </div>
            @endif

        </div>
    @endforeach

</div>

        </div>



        {{-- ===================== --}}
        {{-- 🔹 SCRIPT --}}
        {{-- ===================== --}}

<script>
const contentPane = document.querySelector(".lesson-contents");
const tabs = document.querySelectorAll(".lesson-tab");
const checks = document.querySelectorAll("[data-check]");
const continueBtns = document.querySelectorAll(".unlock-once");

const viewer = document.getElementById("fileViewerWrapper");
const iframe = document.getElementById("fileViewer");

let unlockedIndex = -1;
let lastScrollTop = 0;

/* ================= RENDER ================= */
function render() {
    const sections = document.querySelectorAll(".lesson-section");

    sections.forEach(sec => {
        sec.style.display = "none";
        const btn = sec.querySelector(".unlock-once");
        if (btn) btn.style.display = "none";
    });

    document.getElementById("lesson-overview").style.display = "block";

    for (let i = 0; i <= unlockedIndex; i++) {
        const el = document.getElementById("lesson-" + i);
        if (el) el.style.display = "block";
    }

    checks.forEach(chk => {
        const i = parseInt(chk.dataset.check);
        chk.checked = i <= unlockedIndex;
    });

    if (unlockedIndex === -1) {
        document.querySelector("#lesson-overview .unlock-once")?.style.display = "inline-flex";
    } else {
        document.querySelector("#lesson-" + unlockedIndex + " .unlock-once")?.style.display = "inline-flex";
    }

    updateProgress();
}

function updateProgress() {
    const total = tabs.length - 1;
    const percent = total > 0 ? Math.round((unlockedIndex + 1) / total * 100) : 0;
    document.querySelector(".progress-bar").style.width = percent + "%";
    document.querySelector(".progress-text").innerText = percent + "%";
}

/* ================= SCROLL ================= */
function scrollToIndex(index) {
    const target = document.querySelector(`.lesson-section[data-index="${index}"]`);
    if (!target) return;

    contentPane.scrollTo({
        top: target.offsetTop - 20,
        behavior: "smooth"
    });
}

/* ================= TABS ================= */
tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        const index = tab.dataset.index;
        tabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        scrollToIndex(index);
    });
});

/* ================= CHECK ================= */
checks.forEach(chk => {
    chk.addEventListener("click", e => {
        e.stopPropagation();
        const i = parseInt(chk.dataset.check);
        unlockedIndex = chk.checked ? Math.max(unlockedIndex, i) : i - 1;
        render();
        scrollToIndex(unlockedIndex >= 0 ? unlockedIndex : "overview");
    });
});

/* ================= CONTINUE ================= */
continueBtns.forEach(btn => {
    btn.addEventListener("click", () => {
        unlockedIndex = parseInt(btn.dataset.next);
        render();
        scrollToIndex(unlockedIndex);
    });
});

/* ================= INLINE FILE VIEWER (STAGING SAFE) ================= */
document.querySelectorAll(".view-file").forEach(btn => {
    btn.addEventListener("click", function () {
        lastScrollTop = contentPane.scrollTop;

        const fileUrl = this.dataset.url;
        const url = fileUrl.toLowerCase();

        // Clear iframe
        iframe.src = "";

        // VIDEO (mp4 etc)
        if (url.endsWith(".mp4") || url.endsWith(".webm") || url.endsWith(".ogg")) {
            iframe.src = fileUrl;
        }

        // PDF
        else if (url.endsWith(".pdf")) {
            iframe.src = fileUrl + "#toolbar=0&navpanes=0&scrollbar=1";
        }

        // Image
        else if (url.match(/\.(jpg|jpeg|png|webp)$/)) {
            iframe.src = fileUrl;
        }

        // Other
        else {
            iframe.src = fileUrl;
        }

        viewer.style.display = "block";
        contentPane.scrollTo({ top: 0, behavior: "smooth" });
    });
});

/* ================= CLOSE VIEWER ================= */
function closeViewer() {
    iframe.src = "";
    viewer.style.display = "none";
    contentPane.scrollTo({ top: lastScrollTop, behavior: "smooth" });
}

/* ================= INIT ================= */
render();
</script>













</body>

</html>
