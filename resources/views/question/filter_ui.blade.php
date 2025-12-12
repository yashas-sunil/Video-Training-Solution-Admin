<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Mode</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
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



        /* FILTER CARD */
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 0px 0px 20px 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
        }

        label {
            font-weight: 700;
            margin-bottom: 5px;
            display: block;
        }

        select,
        input {
            width: -webkit-fill-available;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #c9c9c9;
            background: #fafafa;
        }

        #filterBtn {
            background: linear-gradient(180deg, #145EFC 0%, rgba(12, 56, 150, 0.81) 100%);
            padding: 12px 30px;
            color: white;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        #filterBtn:hover {
            background: linear-gradient(180deg, #0e3da2 0%, rgba(12, 56, 150, 0.81) 100%);
        }


        /* NAV BUTTONS */
        .nav-btns {
            max-width: 700px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            gap: 12px;
        }


        .next-btn {
            padding: 9px 20px;
            font-size: 16px;
            border: none;
            border-radius: 55px;
            color: white;
            cursor: pointer;
        }



        .next-btn {
            background: #27ae60;
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
            .Exam-setup {
                flex-wrap: wrap;
            }
        }

        /* ===== sub-navbar TABS ===== */
        /* CENTERED sub-navbar TABS */




        .tabs {
            display: flex;
            align-items: center;
            gap: 15px;
            box-sizing: border-box;
            position: absolute;
            top: 110px;
            right: 50px;

        }



        /* Back button left */
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



        .back-btn {
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

        .Exam-setup {
            width: 100%;
            background: #ffffff;
            border-radius: 20px 20px 0px 0px;
            padding: 10px;
            padding-left: 25px;

            display: flex;
            gap: 20px;
        }

        .back-btn img {
            height: 15px;
            width: auto;

        }

        .back-btn:hover {
            opacity: 1;
            border: 2px solid #9ac9ff;
        }


        .sub-navbar:hover {}

        .tab-btn {
            padding: 10px 20px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            background: #ffffff;
            font-weight: 400;
            transition: 0.2s;
            font-family: system-ui;
            display: flex;
            align-items: center;
            border: 1px solid #E1E7F5
        }

        .tab-btn.active {
            background: linear-gradient(180deg, #145EFC 0%, rgba(12, 56, 150, 0.81) 100%);
            color: #fff;
        }

        .tab-btn.active img {
            filter: invert(0);
        }

        .tab-btn img {
            filter: invert(1);
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
        <!-- sub-navbar WITH TABS -->

        <div class="sub-navbar">
            <div class="tabs">
                <button id="backTabBtn" class="back-btn"><img src="{{ asset('images/backbtn.png') }}"
                        alt="BackBtn"></button>
                <p style="color: #000000;font-family: sans-serif;font-size: 14px;;">Back to Dashboard</p>


            </div>
            <div class="Exam_setup-header" style=" display: flex; flex-direction: row; align-items: center; gap: 10px; margin: 14px 26px;">
                <img src="{{ asset('images/Settings.png') }}"
                        alt="Settings" style=" width: 50px; height: 50px;">
                <div class="Exam_setup-text" style=" display: flex; flex-direction: column;gap:4px">
                    <p style=" margin: 0;font-family: sans-serif;font-size: 20px;font-weight: 400;">Exam Setup</p>
                    <p style=" margin: 0;font-size: 12px;font-family: sans-serif;font-weight: 100;">Configure your exam parameters and preferences</p>
                </div>
            </div>
            <div class="Exam-setup">
                <button class="tab-btn active" onclick="openTab('examTab')"><img
                        src="{{ asset('images/user-test.png') }}"
                        alt="User-test"style="width: 20px;height: 20px;margin-right: 5px;">User Eduedge
                    Questions</button>
                <button class="tab-btn" onclick="openTab('questionTab')"><img
                        src="{{ asset('images/create-test.png') }}"
                        alt="create-test"style="width: 20px;height: 20px;margin-right: 5px;">Create Eduedge
                    Questions</button>
            </div>

            <!-- Back Button -->
            {{-- <div style="text-align:center; margin-top:10px;">
        <button id="backTabBtn" class="back-btn">⬅ Back</button>
    </div> --}}
        </div>

        <div class="container" style="margin-top: 0px;">

            <!-- ================= TAB 1 : EXAM ================= -->
            <div id="examTab" class="tab-content active">
                <!-- ORIGINAL EXAM FILTER & BOX HERE -->
                {{-- <h2>🎯 Question Filter</h2> --}}


                <div class="filter-card">
                    <div class="row">
                        <div class="col">
                            <label>Subject</label>
                            <select id="subject_id">
                                <option value="">Select</option>
                                @foreach ($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label>Chapter</label>
                            <select id="chapter_id">
                                <option value="">Select</option>
                            </select>
                        </div>

                        <div class="col">
                            <label>Subchapter</label>
                            <select id="subchapter_id">
                                <option value="">Select</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label>Difficult Level</label>
                            <select id="difficult_level_id">
                                <option value="">Select</option>
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label>Used Status</label>
                            <select id="used_status">
                                <option value="">All</option>
                                <option value="used">Used</option>
                                <option value="not_used">Not Used</option>
                            </select>
                        </div>

                        <div class="col">
                            <label>Limit</label>
                            <input type="number" id="limit" value="20">
                        </div>
                    </div>

                    <button id="filterBtn"><img src="{{ asset('images/start-test.png') }}"
                            alt="start-test"style="width: 20px;height: 20px;margin-right: 5px;">Start Exam</button>
                </div>

            </div>

            <!-- ================= TAB 2 : QUESTIONS (DUMMY) ================= -->
            <div id="questionTab" class="tab-content" style="display:none;">
                <h2>📝 Admin Questions</h2>

                <div class="filter-card">

                    <!-- header row -->
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3 style="margin:0;">📝 Questions</h3>

                        <button id="startExamFromQuestionTab"
                            style="
                    background:#2d89ff;
                    color:#fff;
                    border:none;
                    padding:10px 22px;
                    border-radius:8px;
                    font-size:15px;
                    cursor:pointer;
                ">
                            🔍 Start Exam
                        </button>
                    </div>

                    <hr style="margin:15px 0;">

                    <!-- questions list -->
                    <ul style="margin:0; padding-left:20px;">
                        <li>Question 1</li>
                        <li>Question 2</li>
                        <li>Question 3</li>
                        <li>Question 4</li>
                    </ul>

                    </ </div>


                </div>
                <script>
                    let allQ = [];
                    let qIndex = 0;
                    let userAnswers = {};

                    /* ------------------------------
                       SUBJECT → CHAPTER
                    --------------------------------*/
                    $("#subject_id").change(function() {
                        let id = $(this).val();
                        $("#chapter_id").html('<option>Loading...</option>');
                        $("#subchapter_id").html('<option value="">Select</option>');

                        $.get("/get-chapterBy-subject", {
                            subjects_id: id
                        }, function(res) {
                            let html = '<option value="">Select</option>';
                            res.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
                            $("#chapter_id").html(html);
                        });
                    });

                    /* ------------------------------
                       CHAPTER → SUBCHAPTER
                    --------------------------------*/
                    $("#chapter_id").change(function() {
                        let id = $(this).val();
                        $("#subchapter_id").html('<option>Loading...</option>');

                        $.get("/get-subchapters", {
                            chapter_id: id
                        }, function(res) {
                            let html = '<option value="">Select</option>';
                            (res.data || res).forEach(sc => {
                                html += `<option value="${sc.id}">${sc.name}</option>`;
                            });
                            $("#subchapter_id").html(html);
                        });
                    });

                    /* ===============================
                       FILTER BUTTON → ONLY REDIRECT
                    ================================*/
                    $("#filterBtn").click(function() {

                        let params = $.param({
                            subject_id: $("#subject_id").val(),
                            chapter_id: $("#chapter_id").val(),
                            subchapter_id: $("#subchapter_id").val(),
                            difficult_level_id: $("#difficult_level_id").val(),
                            used_status: $("#used_status").val(),
                            limit: $("#limit").val()
                        });

                        window.location.href = "/exam-page?" + params;
                    });


                    $("#startExamFromQuestionTab").click(function() {

                        let params = $.param({
                            subject_id: 5,
                            chapter_id: 8,
                            subchapter_id: 7,
                            difficult_level_id: 1,
                            used_status: "",
                            limit: 20
                        });

                        window.location.href = "/exam-page?" + params;
                    });

                    /* ===============================
                       EXAM PAGE : AUTO LOAD QUESTIONS
                    ================================*/
                    function getParam(name) {
                        return new URLSearchParams(window.location.search).get(name);
                    }
                </script>


                <script>
                    function openTab(tabId) {
                        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                        document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
                        event.target.classList.add('active');
                        document.getElementById(tabId).style.display = 'block';
                    }
                    $("#backTabBtn").click(function() {
                        window.history.back();
                    });
                </script>
            </div>
</body>

</html>
