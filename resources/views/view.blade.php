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
    const COURSE_ID  = "{{ $courseId }}";
    // ✅ chapterId SAFE (undefined error nahi aayega)
    const CHAPTER_ID = "{{ $chapterId ?? '' }}";

    let progressData = {
        'cmi.core.lesson_location': "{{ $lastLocation ?? '' }}",
        'cmi.core.lesson_status': "{{ $lessonStatus ?? '' }}",
        'cmi.core.score.raw': "{{ $score ?? '' }}",
        'cmi.suspend_data': `{!! $suspendData ?? '' !!}`
    };

    const resumeTime = {{ $resumeTime ?? 0 }};
    const sessionStart = Date.now();

    window.API = {
        LMSInitialize: () => 'true',
        LMSFinish: () => { saveAllProgress(true); return 'true'; },
        LMSGetValue: (name) => progressData[name] || '',
        LMSSetValue: (name, value) => {
            progressData[name] = value;
            if (name === 'cmi.core.lesson_status' && value === 'completed') {
                progressData['progress_percent'] = 100;
            }
            return 'true';
        },
        LMSCommit: () => { saveAllProgress(); return 'true'; },
        LMSGetLastError: () => '0',
        LMSGetErrorString: () => 'No error',
        LMSGetDiagnostic: () => 'No diagnostic'
    };
    window.API_1484_11 = window.API;

    function getSessionTimeInSeconds() {
        return resumeTime + Math.floor((Date.now() - sessionStart) / 1000);
    }

    function saveAllProgress(isFinal = false) {
        const sessionTime = getSessionTimeInSeconds();
        const lessonLoc = progressData['cmi.core.lesson_location'] || '';
        const lessonStatus = progressData['cmi.core.lesson_status'] || '';
        const suspendData = progressData['cmi.suspend_data'] || '';
        const percent = progressData['progress_percent'] || 0;
        const score = progressData['cmi.core.score.raw'] || null;

        // Collect interaction data
        const rawInteractions = {};
        for (const key in progressData) {
            if (key.startsWith('cmi.interactions.')) {
                rawInteractions[key] = progressData[key];
            }
        }

        // Parse quiz data
        const quizData = {};
        const regex = /cmi\.interactions\.(\d+)\.(id|result|student_response|correct_responses\.0\.pattern)/;

        for (const key in rawInteractions) {
            const match = key.match(regex);
            if (!match) continue;
            const index = match[1];
            const field = match[2];
            if (!quizData[index]) quizData[index] = {};

            if (field === 'correct_responses.0.pattern') {
                quizData[index]['correct_answer'] = rawInteractions[key];
            } else {
                quizData[index][field] = rawInteractions[key];
            }
        }

        // Group per chapter
        const chapterScores = {};

        for (const index in quizData) {
            const item = quizData[index];
            const id = item.id || '';
            const chapterMatch = id.match(/Quiz_(Module_\d+)/i);
            const chapter = chapterMatch ? chapterMatch[1] : 'Unknown';

            if (!chapterScores[chapter]) {
                chapterScores[chapter] = {
                    total: 0,
                    correct: 0,
                    question_ids: [],
                    answers: []
                };
            }

            chapterScores[chapter].total++;
            chapterScores[chapter].question_ids.push(id);
            chapterScores[chapter].answers.push({
                question_id: id,
                student_answer: item.student_response || '',
                correct_answer: item.correct_answer || ''
            });

            if ((item.result || '').toLowerCase() === 'correct') {
                chapterScores[chapter].correct++;
            }
        }

        const chapterResults = Object.entries(chapterScores).map(([chapter, data]) => ({
            chapter_name: chapter,
            total_questions: data.total,
            correct_answers: data.correct,
            wrong_answers: data.total - data.correct,
            question_ids: data.question_ids,
            student_answers: data.answers,
            score_percent: data.total > 0
                ? Math.round((data.correct / data.total) * 100)
                : 0
        }));

        // Save course progress
        fetch("{{ url('/course/progress/save') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                course_id: COURSE_ID,
                chapter_id: CHAPTER_ID || null, 
                session_time: sessionTime,
                cmi_core_lesson_location: lessonLoc,
                cmi_core_lesson_status: lessonStatus,
                progress_percent: percent,
                score: score,
                suspend_data: suspendData,
                is_final: isFinal ? 1 : 0
            })
        });

        // Save quiz data
        if (shouldSaveQuiz(chapterScores, quizData)) {
            const currentData = JSON.stringify(chapterResults);
            const lastSavedKey = '__last_saved_quiz_data__';

            if (localStorage.getItem(lastSavedKey) !== currentData) {
                localStorage.setItem(lastSavedKey, currentData);

                fetch("{{ url('/quiz/save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: "{{ auth()->user()->id }}",
                        quiz_name: "{{ $title }}",
                        results: chapterResults
                    })
                })
                .then(res => res.json())
                .then(data => console.log(" Quiz saved", data))
                .catch(err => console.error("❌ Quiz save error:", err));
            }
        }
    }

    function shouldSaveQuiz(chapterScores, quizData) {
        for (const index in quizData) {
            const q = quizData[index];
            if (q && q.id && q.result && q.student_response && q.student_response.trim() !== '') {
                return true;
            }
        }
        return false;
    }
</script>

</body>
</html>
