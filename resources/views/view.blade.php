<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
        }
        iframe {
            border: none;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>

<iframe src="{{ $launchUrl }}" id="scorm-content"></iframe>

<script>
    let progressData = {};
    const resumeTime = {{ $resumeTime ?? 0 }};
    const sessionStart = Date.now(); // Track session start time
    const lastLocation = "{{ $lastLocation ?? '' }}";

    // ✅ Inject Dummy SCORM API
    window.API = {
        LMSInitialize(param) {
            console.log('🟢 LMSInitialize called');
            return 'true';
        },
        LMSFinish(param) {
            console.log('🔴 LMSFinish called');
            return 'true';
        },
        LMSGetValue(name) {
            console.log('🟠 LMSGetValue:', name);
            if (name === 'cmi.core.lesson_location') {
                return lastLocation;
            }
            return progressData[name] || '';
        },
        LMSSetValue(name, value) {
            console.log('🟣 LMSSetValue:', name, '=', value);
            progressData[name] = value;

            if (name === 'cmi.core.lesson_status' && value === 'completed') {
                progressData['progress_percent'] = 100;
            }

            return 'true';
        },
        LMSCommit(param) {
            console.log('🔵 LMSCommit called');
            return 'true';
        },
        LMSGetLastError: () => '0',
        LMSGetErrorString: () => 'No error',
        LMSGetDiagnostic: () => 'No diagnostic'
    };
    window.API_1484_11 = window.API;
    console.log("✅ SCORM API injected");

    // 🕒 Get session time in seconds
    function getSessionTimeInSeconds() {
        return resumeTime + Math.floor((Date.now() - sessionStart) / 1000);
    }

    // 🧠 Try to read scroll percent
    function getScrollPercentFromIframe() {
        const iframe = document.getElementById('scorm-content');
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            const scrollElement = iframeDoc.scrollingElement || iframeDoc.documentElement || iframeDoc.body;

            const scrollTop = scrollElement.scrollTop;
            const scrollHeight = scrollElement.scrollHeight;
            const clientHeight = scrollElement.clientHeight;

            if (scrollHeight <= clientHeight) return 0;

            const percentScrolled = Math.floor((scrollTop / (scrollHeight - clientHeight)) * 100);
            console.log("📊 SCROLL:", percentScrolled + "%");
            return percentScrolled;
        } catch (err) {
            console.warn("⚠️ Cannot access iframe scroll (CORS or delay)", err);
            return 0;
        }
    }

    // ⏳ Auto-save every 5 seconds
    setInterval(() => {
        const sessionTime = getSessionTimeInSeconds();
        const lessonLoc = progressData['cmi.core.lesson_location'] || '';
        const lessonStatus = progressData['cmi.core.lesson_status'] || '';
        const scrollPercent = getScrollPercentFromIframe();
        const percent = progressData['progress_percent'] || scrollPercent || 0;

        if (!lessonLoc && !lessonStatus && percent === 0) return;

        fetch('/course/progress/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                course_id: "{{ $courseId }}",
                session_time: sessionTime,
                cmi_core_lesson_location: lessonLoc,
                cmi_core_lesson_status: lessonStatus,
                progress_percent: percent
            })
        }).then(res => {
            if (res.ok) {
                console.log("💾 Saved progress:", sessionTime + "s, status:", lessonStatus);
            } else {
                console.error("❌ Save failed");
            }
        }).catch(err => {
            console.error("🚫 Error saving progress", err);
        });
    }, 5000);
</script>

</body>
</html>
