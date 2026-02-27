<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eduedge Exam</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* ===== PAGE ===== */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f1f7ff;
        }

        .main-container {
            box-sizing: border-box;
            padding: 40px;
            padding-bottom: 0px;
        }

        * {
            box-sizing: border-box;
        }

        /* ===== WRAPPER ===== */
        .exam-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* ===== EXAM CARD ===== */
        .exam-box {
            width: 100%;
            max-width: 520px;
            border-radius: 16px;
        }

        .sub-exambox {
            border: 3px solid #00A63E;
            border-radius: 16px;
        }

        .questions-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .options-container {
            width: 100%;
            max-width: 520px;
            padding: 20px;
            border-radius: 16px;
            background: #ffffff;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 25px;
            display: flex;
            align-items: stretch;
            gap: 10px;
            flex-direction: column;
        }

        .Q-no {
            width: fit-content;
            padding: 8px;
            background: linear-gradient(180deg, #700002 0%, #700002e0 100%);
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 5px;
            border-radius: 10px;
            color: #ffffff;
            font-family: sans-serif;
            font-weight: 400;
            margin-right: 10px;
            height: fit-content;
        }

        /* ===== QUESTION ===== */
        .question-title {
            font-size: 20px;
            font-weight: 700;
            background: #ffffff;
            display: flex;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
        }

        .question-title2 {
            font-size: 20px;
            font-weight: 700;
            background: #00A63E;
            display: flex;
            padding: 20px;
            border-radius: 10px 10px 0px 0px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 20px;
            color: #ffffff;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            gap: 10px;
        }

        /* ===== OPTIONS ===== */
        .options-box {
            flex-direction: column;
            gap: 14px;
            font-size: 20px;
            font-weight: 700;
            background: #ffffff;
            display: flex;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            margin-bottom: 10px;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 10px;
            cursor: pointer;
            background: #fff;
            transition: .2s;
        }

        .option-item:hover {
            background: #f1f7ff;
            border-color: #2d89ff;
        }

        .option-item input {
            width: 18px;
            height: 18px;
        }

        .option-alpha {
            font-weight: 700;
            width: 20px;
            font-size: 16px;
        }

        .option-text {
            font-size: 16px;
            font-weight: 500;
        }

        /* ===== NAV BUTTONS ===== */
        .nav-btns {
            display: flex;
            width: 100%;
            max-width: 520px;
            justify-content: space-between;
            flex-direction: row;
            flex-wrap: nowrap;
        }

        .check_ans-btns {
            display: flex;
            width: 100%;
            max-width: 520px;
            justify-content: space-between;
            flex-direction: row;
            flex-wrap: nowrap;
        }

        .next-btn {
            padding: 10px 28px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            color: #fff;
            cursor: pointer;
        }

        .back_btn {
            background: #7f8c8d;
        }

        .next-btn {
            background: #27ae60;
        }

        /* ===== TOP BACK BUTTON ===== */
        .top-back {
            width: 100%;
            display: flex;
            margin-top: 65px;
            flex-direction: row;
            margin-bottom: 20px;
            gap: 10px;
            align-items: flex-start;
            justify-content: flex-end;
        }

        .test_progress {
            background: #ffffff;
            width: 100%;
            border-radius: 10px;
            box-shadow: 3px 4px 11px 0px #00000040;
            padding: 8px 20px;
            padding-bottom: 20px;
        }

        .test_text {
            display: flex;
            flex-direction: row;
            width: 100%;
            justify-content: space-between;
        }

        .back_btn {
            background: #ffffff;
            color: #000000;
            opacity: 0.7;
            padding: 4px 7px;
            border-radius: 9px;
            border-width: 1px;
            border: 2px solid #e3eefb;
            cursor: pointer;
            box-shadow: 3px 4px 11px 0px #00000040;
        }

        .back-btn {
            background: linear-gradient(180deg, #700002 0%, #700002e0 100%);
            color: #ffffff;
            padding: 10px 25px;
            border-radius: 9px;
            border-width: 1px;
            border: 2px solid #e3eefb;
            cursor: pointer;
            box-shadow: 3px 4px 11px 0px #00000040;
            display: flex;
            align-items: center;
        }

        .back-btn:hover {
            background: linear-gradient(180deg, #700002 0%, #440102e0 100%);
        }

        .check-btn {
            background: linear-gradient(180deg, #155DFC 0%, #155DFCe0 100%);
            color: #ffffff;
            padding: 10px 25px;
            border-radius: 9px;
            border-width: 1px;
            border: 2px solid #e3eefb;
            cursor: pointer;
            box-shadow: 3px 4px 11px 0px #00000040;
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .check-btn:hover {
            background: linear-gradient(180deg, #155DFC 0%, #1145b5e0 100%);
        }

        .tabs {
            display: flex;
            align-items: center;
            gap: 15px;
            box-sizing: border-box;
            margin-right: 10px;
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
            width: 100%;
            left: 0;
            box-sizing: border-box;
            z-index: 1;
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

        .result-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin: 20px 0;
        }

        .correct-answer {
            background: #e6fff1;
            border: 1px solid #00c950;
        }

        .wrong-answer {
            background: #ffeaea;
            border: 1px solid #ff4d4d;
        }

        /* ✅ NEW: Study feedback text */
        .study-feedback {
            padding: 12px 14px;
            border-radius: 10px;
            margin-top: 12px;
            font-size: 14px;
            font-weight: 600;
            display: none;
        }

        .study-feedback.correct {
            background: #e6fff1;
            border: 1px solid #00c950;
            color: #056d2c;
        }

        .study-feedback.wrong {
            background: #ffeaea;
            border: 1px solid #ff4d4d;
            color: #a10f0f;
        }

        /* ✅ NEW: Solution box (works for both test review + study checked) */
        .solution-box {
            background: #fff;
            border: 1px solid #e6eaf3;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
            display: none;
        }

        .solution-title {
            font-size: 14px;
            font-weight: 800;
            margin: 0 0 8px 0;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .solution-text {
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            line-height: 1.5;
            white-space: pre-line;
        }

        @media (max-width: 425px) {
            .main-container {
                padding: 10px !important;
            }

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

            .user-info-mobile a {
                color: white !important;
                text-decoration: none !important;
            }

            .user-info {
                display: none !important;
            }

            .top-back {
                flex-direction: column-reverse !important;
                align-items: flex-end !important;
            }

            .result-stats {
                gap: 0;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="navbar">
            <div class="logo">
                <img src="{{ asset('images/logo-2.png') }}" alt="Company Logo">
            </div>

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

        <div class="top-back">
            <div class="test_progress">
                <div class="test_text">
                    <p id="questionCounter" style="margin:0;margin-bottom:10px;margin-top:5px;">
                        Question 1 / 10
                    </p>
                    <p id="questionPercent"
                        style="margin:0;margin-bottom:10px;margin-top:5px;color:#700002;font-weight:600;">
                        0%
                    </p>
                </div>
                <div class="progress-bar-wrapper" style="display:flex; align-items:center; gap:0.5rem;">
                    <div class="progress-bar"
                        style="flex:1;background: #D9D9D9;border-radius:5px;height:10px;overflow:hidden;position:relative;box-shadow: 0px 4px 5px -3px #00000040 inset;">
                        <div class="progress-fill"
                            style="height:100%; background:#700002; width:50%; transition:width 0.5s;border-radius:10px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="tabs">
                <button id="topBackBtn" class="back_btn"
                    style=" display: flex; flex-direction: row; align-items: center; gap: 10px; padding: 4px 16px;">
                    <img src="{{ asset('images/backbtn.png') }}" alt="BackBtn">
                    <p style="color: #000000;font-family: sans-serif;font-size: 14px;;">Back</p>
                </button>
            </div>
        </div>

        <div class="exam-wrapper">
            <div class="exam-box" id="examBox">
                <p style="text-align:center;color:#999;">Loading questions...</p>
            </div>

            <div class="nav-btns" style="display:none">
                <button id="backBtn" class="back-btn"><img src="{{ asset('images/arrow.png') }}" alt="arrow"
                        style="margin-right: 10px;width: 15px;height: 15px;">Previous</button>

                <button id="nextBtn" class="back-btn">Next<img src="{{ asset('images/arrow.png') }}" alt="arrow"
                        style="margin-left: 10px; rotate: 180deg;width: 15px;height: 15px;"></button>
            </div>

            <div class="check_ans-btns" style="display:none">
                <button id="check_ans_btn" class="check-btn"><img src="{{ asset('images/review.png') }}" alt="arrow"
                        style="margin-right: 10px;width: 15px;height: 15px;">Review Answers</button>
            </div>

        </div>

        <script>
            $(function() {

                let allQ = [];
                let qIndex = 0;
                let answers = {};
                let reviewMode = false;
                let resultHTML = "";
                let answersSaved = false;

                const qp = n => new URLSearchParams(window.location.search).get(n);
                const mode = (qp('mode') || 'test').toLowerCase(); // test | study

                let checkedMap = {}; // study: { [questionId]: true/false }

                let data = {
                    subject_id: qp('subject_id'),
                    chapter_id: qp('chapter_id'),
                    difficult_level_id: qp('difficult_level_id'),
                    used_status: qp('used_status'),
                    limit: qp('limit'),
                    mode: mode
                };

                $.get("/qbundle/filter", data, function(res) {
                    allQ = res.data || [];
                    if (allQ.length === 0) {
                        $("#examBox").html("<p style='text-align:center;'>No Questions Found</p>");
                        return;
                    }
                    showQuestion(0);
                    $(".nav-btns").show();
                });

                // ✅ NEW: scroll to top (so next question shows at top without manual scroll)
                function scrollToQuestionTop() {
                    // immediate top for perfect UX
                    window.scrollTo({ top: 0, left: 0, behavior: "smooth" });
                }

                function updateTestProgress() {
                    let current = qIndex + 1;
                    let total = allQ.length;
                    let percent = Math.round((current / total) * 100);

                    $("#questionCounter").text(`Question ${current} / ${total}`);
                    $("#questionPercent").text(`${percent}%`);
                    $(".progress-fill").css("width", percent + "%");
                }

                function getRightAnswerId(q) {
                    return q.answers.find(a => a.correctans == 1)?.id;
                }

                function getSolutionText(q) {
                    return (q.solution_text || '').toString().trim();
                }

                function showSolutionBox(q) {
                    const sol = getSolutionText(q);
                    if (!sol) return;

                    const box = $("#solutionBox");
                    const text = $("#solutionText");

                    if (!box.length || !text.length) return;

                    text.text(sol);
                    box.show();
                }

                function showQuestion(i) {
                    updateTestProgress();

                    let q = allQ[i];
                    let rightAns = getRightAnswerId(q);
                    const isChecked = !!checkedMap[q.id];

                    let html = `
                        <div class="sub-exambox-q">
                            <div class="question-title">
                                <div class="Q-no">Q${i + 1}</div> ${q.question}
                            </div>
                            <div class="options-box">
                    `;

                    q.answers.forEach((a, idx) => {
                        let checked = (answers[q.id] == a.id) ? 'checked' : '';
                        let cls = '';

                        if (reviewMode || (mode === 'study' && isChecked)) {
                            if (a.id == rightAns) cls = ' correct-answer';
                            if (answers[q.id] == a.id && a.id != rightAns) cls = ' wrong-answer';
                        }

                        const disableInput = (reviewMode || (mode === 'study' && isChecked)) ? 'disabled' : '';

                        html += `
                            <label class="option-item ${cls}">
                                <input type="radio" name="opt"
                                    value="${a.id}"
                                    data-qid="${q.id}"
                                    ${checked}
                                    ${disableInput}>
                                <span class="option-alpha">${String.fromCharCode(65 + idx)}</span>
                                <span class="option-text">${a.answer}</span>
                            </label>
                        `;
                    });

                    if (mode === 'study' && !reviewMode) {
                        html += `
                            <div id="studyFeedback" class="study-feedback"></div>

                            <button id="checkNowBtn" class="check-btn" type="button" style="margin-top:10px;">
                                <img src="{{ asset('images/review.png') }}" alt="arrow"
                                    style="margin-right: 10px;width: 15px;height: 15px;">
                                Check Answer
                            </button>
                        `;
                    }

                    html += `
                            <div id="solutionBox" class="solution-box">
                                <div class="solution-title">
                                    ✅ Why correct? (Solution)
                                </div>
                                <div id="solutionText" class="solution-text"></div>
                            </div>
                        </div></div>
                    `;

                    $("#examBox").html(html);

                    // ✅ NEW: after rendering question, auto scroll to top
                    scrollToQuestionTop();

                    if (reviewMode) {
                        showSolutionBox(q);
                    }

                    if (mode === 'study' && isChecked) {
                        renderStudyFeedback(q);
                        showSolutionBox(q);
                    }
                }

                $(document).on("change", "input[name=opt]", function() {
                    if (!reviewMode) {
                        answers[$(this).data("qid")] = $(this).val();
                    }
                });

                $("#backBtn").click(() => {
                    if (qIndex > 0) {
                        qIndex--;
                        showQuestion(qIndex);
                    }
                });

                $(document).on("click", "#checkNowBtn", function() {
                    const q = allQ[qIndex];
                    const selected = answers[q.id];

                    if (!selected) {
                        alert("Please select an option first.");
                        return;
                    }

                    checkedMap[q.id] = true;
                    showQuestion(qIndex);
                });

                function renderStudyFeedback(q) {
                    const rightId = getRightAnswerId(q);
                    const selected = answers[q.id];

                    const isCorrect = (selected && rightId && String(selected) === String(rightId));
                    const fb = $("#studyFeedback");

                    if (!fb.length) return;

                    fb.removeClass("correct wrong").addClass(isCorrect ? "correct" : "wrong");

                    let correctText = q.answers.find(a => String(a.id) === String(rightId))?.answer || "Correct option";
                    fb.html(isCorrect
                        ? `✅ Correct!`
                        : `❌ Wrong! Correct Answer: <b>${correctText}</b>`
                    );

                    fb.show();
                }

                $("#nextBtn").click(() => {

                    if (reviewMode) {
                        if (qIndex < allQ.length - 1) {
                            qIndex++;
                            showQuestion(qIndex);
                        } else {
                            reviewMode = false;
                            $("#examBox").html(resultHTML);
                            $(".nav-btns").hide();
                            $(".check_ans-btns").show();
                            scrollToQuestionTop();
                        }
                        return;
                    }

                    if (mode === 'study') {
                        const q = allQ[qIndex];
                        const selected = answers[q.id];

                        if (!selected) {
                            alert("Please select an option first.");
                            return;
                        }

                        if (!checkedMap[q.id]) {
                            checkedMap[q.id] = true;
                            showQuestion(qIndex);
                            return;
                        }

                        if (qIndex < allQ.length - 1) {
                            qIndex++;
                            showQuestion(qIndex);
                            return;
                        }

                        finalizeResult();
                        return;
                    }

                    if (qIndex < allQ.length - 1) {
                        qIndex++;
                        showQuestion(qIndex);
                        return;
                    }

                    finalizeResult();
                });

                function finalizeResult() {
                    let correct = 0;
                    let wrong = 0;

                    allQ.forEach(q => {
                        let right = q.answers.find(a => a.correctans == 1);
                        if (answers[q.id] == right?.id) {
                            correct++;
                        } else {
                            wrong++;
                        }
                    });

                    if (!answersSaved) {
                        saveUserAnswers();
                        answersSaved = true;
                    }

                    let percentage = Math.round((correct / allQ.length) * 100);
                    let radius = 64;
                    let circumference = 2 * Math.PI * radius;
                    let offset = -(circumference * (1 - percentage / 100));

                    $(".check_ans-btns").show();
                    $(".nav-btns").hide();

                    resultHTML = `
                        <div class="sub-exambox">
                            <div class="question-title2">
                                <img src="{{ asset('images/test_completed.png') }}">
                                Exam Completed
                            </div>

                            <div class="result-full" style="padding:15px;">
                                <div class="result-circle-wrap">
                                    <svg width="140" height="140">
                                        <circle cx="70" cy="70" r="${radius}" stroke="#e4e6eb" stroke-width="10" fill="transparent"/>
                                        <circle cx="70" cy="70" r="${radius}"
                                            stroke="#00c950"
                                            stroke-width="10"
                                            fill="transparent"
                                            stroke-dasharray="${circumference}"
                                            stroke-dashoffset="${offset}"
                                            stroke-linecap="round"/>
                                    </svg>
                                    <div class="result-circle-text">${percentage}%</div>
                                </div>

                                <div class="result-stats">
                                    <div>Correct: ${correct}</div>
                                    <div>Wrong: ${wrong}</div>
                                    <div>Total: ${allQ.length}</div>
                                </div>
                            </div>
                        </div>
                    `;

                    $("#examBox").html(resultHTML);
                    scrollToQuestionTop();
                }

                $("#check_ans_btn").click(() => {
                    reviewMode = true;
                    qIndex = 0;
                    $(".check_ans-btns").hide();
                    $(".nav-btns").show();
                    showQuestion(qIndex);
                });

                $("#topBackBtn").click(function() {
                    history.back();
                });

                function saveUserAnswers() {
                    let payload = {
                        data: []
                    };

                    allQ.forEach(q => {
                        payload.data.push({
                            question_id: q.id,
                            answers_id: answers[q.id] ?? null,
                            time_taken: 0,
                            user_question_status: answers[q.id] ? 1 : 0,
                            is_cumulative_question: false,
                            mode: mode
                        });
                    });

                    $.ajax({
                        url: "/api/save-user-answers",
                        type: "POST",
                        data: JSON.stringify(payload),
                        contentType: "application/json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            console.log("✅ Answers saved");
                        },
                        error: function(err) {
                            console.error("❌ Save failed", err);
                        }
                    });
                }

            });
        </script>

    </div>
</body>

</html>