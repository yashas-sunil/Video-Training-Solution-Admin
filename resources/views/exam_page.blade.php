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
        }

        /* ===== QUESTION ===== */
        .question-title {
            font-size: 20px;
            font-weight: 700;
        }

        /* ===== OPTIONS ===== */
        .options-box {
            display: flex;
            flex-direction: column;
            gap: 14px;
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
            width: 25px;
        }

        .option-text {
            font-size: 16px;
            font-weight: 500;
        }

        /* ===== NAV BUTTONS ===== */
        .nav-btns {
            display: flex;
            width: 40%;
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
            margin-top: 70px;
            flex-direction: row-reverse;
        }




        .sub-navbar {
            width: 100%;
            background: #f8f9fa;
            margin-top: 135px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 0;
            box-sizing: border-box;
            border-radius: 20px 20px 0px 0px;
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

        .back_btn img {
            height: 15px;
            width: auto;

        }

        .back_btn:hover {
            opacity: 1;
            border: 2px solid #9ac9ff;
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

            .user-welcome {
                gap: 4px !important;
                padding: 6px 10px !important;
            }

            .user-info a {
                margin-left: 0px !important;
                padding: 3px 10px !important;
                font-size: 12px !important;
            }

            .user-info-mobile a {
                color: white !important;
                text-decoration: none !important;
            }

            .user-info {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="navbar">
            <!-- Left: Company Logo -->
            <div class="logo">
                {{-- <img src="{{ asset('images/logo.png') }}" alt="Company Logo"> --}}
                <img src="{{ asset('images/logo-2.png') }}" alt="Company Logo">
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

        <!-- TOP BACK BUTTON -->
        <div class="top-back">
            <div class="tabs">
                <button id="topBackBtn" class="back_btn"><img src="{{ asset('images/backbtn.png') }}"
                        alt="BackBtn"></button>
                <p style="color: #000000;font-family: sans-serif;font-size: 14px;;">Back to Dashboard</p>


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

        </div>

        <script>
            $(function() {

                let allQ = [];
                let qIndex = 0;
                let answers = {};

                const qp = n => new URLSearchParams(window.location.search).get(n);

                let data = {
                    subject_id: qp('subject_id'),
                    chapter_id: qp('chapter_id'),
                    subchapter_id: qp('subchapter_id'),
                    difficult_level_id: qp('difficult_level_id'),
                    used_status: qp('used_status'),
                    limit: qp('limit')
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

                function showQuestion(i) {
                    let q = allQ[i];

                    let html = `
        <div class="questions-container">
            <div class="Q-no">Q${i+1}</div>
                
            <div class="question-title">
                Question ${i+1}
            </div>
            </div>
            <div class="options-container">
            <div class="options-box">
                </div>
        `;

                    q.answers.forEach((a, idx) => {
                        html += `
                <label class="option-item">
                    <input type="radio" name="opt"
                        value="${a.id}"
                        data-qid="${q.id}"
                        ${answers[q.id]==a.id?'checked':''}>
                    <span class="option-alpha">${String.fromCharCode(65+idx)}</span>
                    <span class="option-text">${a.answer}</span>
                </label>
            `;
                    });

                    html += `</div>`;
                    $("#examBox").html(html);
                }

                $(document).on("change", "input[name=opt]", function() {
                    answers[$(this).data("qid")] = $(this).val();
                });

                $("#backBtn").click(() => {
                    if (qIndex > 0) {
                        qIndex--;
                        showQuestion(qIndex);
                    }
                });

                $("#nextBtn").click(() => {
                    if (qIndex < allQ.length - 1) {
                        qIndex++;
                        showQuestion(qIndex);
                        return;
                    }

                    // ✅ RESULT CALCULATION
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

                    $("#examBox").html(`
            <div class="question-title">🎉 Exam Completed</div>

            <p style="text-align:center;font-size:18px;color:green;">
                ✅ Correct : <b>${correct}</b>
            </p>

            <p style="text-align:center;font-size:18px;color:red;">
                ❌ Wrong : <b>${wrong}</b>
            </p>

            <p style="text-align:center;font-size:20px;margin-top:10px;">
                Score : <b>${correct} / ${allQ.length}</b>
            </p>
        `);

                    $(".nav-btns").hide();
                });

            });

            $("#topBackBtn").click(function() {
                history.back();
            });
        </script>
    </div>

</body>

</html>
