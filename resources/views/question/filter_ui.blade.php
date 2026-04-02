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

            .Exam_setup-text p {
                display: none;
            }
        }

        .tabs {
            display: flex;
            align-items: center;
            gap: 15px;
            box-sizing: border-box;
        }

        .sub-navbar {
            width: 100%;
            background: #f8f9fa;
            margin-top: 130px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            padding: 0;
            box-sizing: border-box;
            border-radius: 20px 20px 0px 0px;
        }

        .examstup_tabs {
            display: flex;
            flex-direction: row-reverse;
            justify-content: space-between;
            padding: 0px 22px;
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

        .error {
            border: 2px solid #e74c3c !important;
            background: #fff5f5;
        }

        .error-text {
            color: #e74c3c;
            font-size: 13px;
            margin-top: 4px;
        }

        /* ✅ tabs show/hide */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>

</head>

<body>
    <div class="main-container">
        <div class="navbar">
            <div class="logo">
                <img src="{{ asset('images/logo3.png') }}" alt="Company Logo">
            </div>

              <div class="logo2">
                <img src="{{ asset('images/Bloomberglogo.png') }}" alt="Company Logo">
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

        <div class="sub-navbar">
            <div class="examstup_tabs">
                <div class="tabs">
                    <button id="backTabBtn" class="back-btn"
                        style=" display: flex; flex-direction: row; align-items: center; gap: 10px; padding: 4px 16px;">
                        <img src="{{ asset('images/backbtn.png') }}" alt="BackBtn">
                        <p style="color: #000000;font-family: sans-serif;font-size: 14px;;">Back to Dashboard</p>
                    </button>
                </div>

                <div class="Exam_setup-header"
                    style=" display: flex; flex-direction: row; align-items: center; gap: 10px; margin: 14px 0px;">
                    <img src="{{ asset('images/Settings.png') }}" alt="Settings" style=" width: 50px; height: 50px;">
                    <div class="Exam_setup-text" style=" display: flex; flex-direction: column;gap:4px">
                        <p style=" margin: 0;font-family: sans-serif;font-size: 20px;font-weight: 400;">Exam Setup</p>
                        <p style=" margin: 0;font-size: 12px;font-family: sans-serif;font-weight: 100;">Configure your
                            exam parameters and preferences</p>
                    </div>
                </div>
            </div>

            <div class="Exam-setup">
                <button class="tab-btn active" onclick="openTab('examTab')">
                    <img src="{{ asset('images/user-test.png') }}" alt="User-test"
                        style="width: 20px;height: 20px;margin-right: 5px;">
                      Create User Questions
                </button>

                <button class="tab-btn" onclick="openTab('questionTab')">
                    <img src="{{ asset('images/create-test.png') }}" alt="create-test"
                        style="width: 20px;height: 20px;margin-right: 5px;">
                     Eduedge Created Questions
                </button>
            </div>
        </div>

        <div class="container" style="margin-top: 0px;">

            <!-- ================= TAB 1 : EXAM ================= -->
            <div id="examTab" class="tab-content active">
                <div class="filter-card">
                    <div class="row">
                        <div class="col">
                            <label>Course<span class="required"> *</span></label>
                            <select id="course_id" disabled>
                                <option value="{{$courseName ?? ''}}">{{ $courseName ?? '' }}</option>
                            </select>
                            <div class="error-text" id="course_error"></div>
                        </div>
                        <div class="col">
                            <label>Subject <span class="required"> *</span></label>
                            <select id="subject_id">
                                <option value="">Select</option>
                                @foreach ($subjects as $s)
                                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                            <div class="error-text" id="subject_error"></div>
                        </div>

                        <div class="col">
                            <label>Chapter <span class="required"> *</span></label>
                            <select id="chapter_id">
                                <option value="">Select</option>
                            </select>
                            <div class="error-text" id="chapter_error"></div>
                        </div>

                        <!-- ✅ CHANGED: Subchapter dropdown removed, Mode dropdown added -->
                        <div class="col">
                            <label>Mode <span class="required"> *</span></label>
                            <select id="exam_mode">
                                <option value="">Select</option>
                                <option value="test">Test</option>
                                <option value="study">Study</option>
                            </select>
                            <div class="error-text" id="mode_error"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col">
                            <label>Difficult Level <span class="required"> *</span></label>
                            <select id="difficult_level_id">
                                <option value="">Select</option>
                                @foreach ($levels as $lvl)
                                    <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                @endforeach
                            </select>
                            <div class="error-text" id="level_error"></div>
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

                    <button id="filterBtn">
                        <img src="{{ asset('images/start-test.png') }}" alt="start-test"
                            style="width: 20px;height: 20px;margin-right: 5px;">
                        Start Exam
                    </button>
                </div>
            </div>

            <!-- ================= TAB 2 : QUESTIONS (DUMMY) ================= -->
            <div id="questionTab" class="tab-content">
                <div class="filter-card">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <h3 style="margin:0;">📝 List of Test</h3>
                    </div>

                    <hr style="margin:15px 0;">

                    <div style="max-height: 400px; overflow-y: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 2px solid #ddd;background: #f8f9fa;">
                                    <th style="padding: 10px; text-align: left;">
                                        <input type="checkbox" id="selectAllTests" style="cursor: pointer; display:none;" >
                                    </th>
                                    <th style="padding: 10px; text-align: left;">Test Name</th>
                                    <th style="padding: 10px; text-align: left;">Subjects</th>
                                    <th style="padding: 10px; text-align: left;">Questions</th>
                                    <th style="padding: 10px; text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="testsList">
                                @php
                                    $tests = $admintest instanceof \Illuminate\Database\Eloquent\Builder 
                                        ? $admintest->get() 
                                        : $admintest;
                                @endphp
                                @if(is_iterable($tests) && count($tests) > 0)
                                    @foreach($tests as $test)
                                    <tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 10px;">
                                            <input type="checkbox" class="test-checkbox" value="{{ $test->id }}" style="cursor: pointer;">
                                        </td>
                                        <td style="padding: 10px;">{{ $test->test_name }}</td>
                                        <td style="padding: 10px;">
                                            @php
                                                $subjectIds = explode(',', $test->subject_id);
                                                $subjectNames = [];
                                                foreach($subjectIds as $subjectId) {
                                                    $subject = \App\Models\Subject::find(trim($subjectId));
                                                    if($subject) {
                                                        $subjectNames[] = $subject->name;
                                                    }
                                                }
                                            @endphp
                                            {{ implode(', ', $subjectNames) ?: 'N/A' }}
                                        </td>
                                        <td style="padding: 10px;">{{ $test->total_ques_count }}</td>
                                        <td style="padding: 10px; text-align: center;">
                                                <button class="start-btn" style="
                                                        display: none;
                                                        background:#2d89ff;
                                                        color:#fff;
                                                        border:none;
                                                        padding:9px 20px;
                                                        border-radius:8px;
                                                        font-size:13.5px;
                                                        cursor:pointer;
                                                    " onclick="startTest({{ $test->id }})">
                                                        📚 Start Exam 
                                                    </button>
                                            
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5" style="padding: 20px; text-align: center; color: #999;">
                                            No tests available
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <script>
                // ✅ tabs function
                function openTab(tabId) {
                    document.querySelectorAll(".tab-btn").forEach(btn => btn.classList.remove("active"));
                    document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));

                    const activeTab = document.getElementById(tabId);
                    if (activeTab) activeTab.classList.add("active");

                    document.querySelectorAll(".tab-btn").forEach(btn => {
                        const oc = btn.getAttribute("onclick") || "";
                        if (oc.indexOf("'" + tabId + "'") !== -1) btn.classList.add("active");
                    });
                }

                let allQ = [];
                let qIndex = 0;
                let userAnswers = {};

                $("#subject_id").on("change", function() {
                    let id = $(this).val();

                    $("#chapter_id").html('<option value="">Loading...</option>');

                    if (id === "") {
                        $("#chapter_id").html('<option value="">Select</option>');
                        return;
                    }

                    $.get("/get-chapterBy-subject", {
                        subjects_id: id
                    }, function(res) {
                        let html = '<option value="">Select</option>';
                        res.forEach(c => {
                            html += `<option value="${c.id}">${c.name}</option>`;
                        });
                        $("#chapter_id").html(html);
                    });
                });
                $(document).on('change', '.test-checkbox', function () {
                    $('.test-checkbox').not(this).prop('checked', false);
                    
                    // Hide all start buttons
                    $('.start-btn').hide();
                    
                    // Show start button in the checked row
                    if ($(this).is(':checked')) {
                        $(this).closest('tr').find('.start-btn').show();
                    }
                });

                $("#filterBtn").on("click", function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    $(".error-text").text("");
                    $("select").removeClass("error");

                    let isValid = true;

                    if ($("#subject_id").val() === "") {
                        $("#subject_id").addClass("error");
                        $("#subject_error").text("Subject is required");
                        isValid = false;
                    }

                    if ($("#chapter_id").val() === "") {
                        $("#chapter_id").addClass("error");
                        $("#chapter_error").text("Chapter is required");
                        isValid = false;
                    }

                    // ✅ NEW: mode required
                    if ($("#exam_mode").val() === "") {
                        $("#exam_mode").addClass("error");
                        $("#mode_error").text("Mode is required");
                        isValid = false;
                    }

                    if ($("#difficult_level_id").val() === "") {
                        $("#difficult_level_id").addClass("error");
                        $("#level_error").text("Difficulty level is required");
                        isValid = false;
                    }

                    if (!isValid) {
                        return false;
                    }

                    let params = $.param({
                        subject_id: $("#subject_id").val(),
                        chapter_id: $("#chapter_id").val(),
                        mode: $("#exam_mode").val(),
                        difficult_level_id: $("#difficult_level_id").val(),
                        used_status: $("#used_status").val(),
                        limit: $("#limit").val()
                    });

                    window.location.href = "/exam-page?" + params;
                });

                $("select").on("change", function() {
                    if ($(this).val() !== "") {
                        $(this).removeClass("error");
                        $(this).next(".error-text").text("");
                    }
                });

                $("#selectAllTests").on("change", function() {
                    let isChecked = $(this).is(":checked");
                    $(".test-checkbox").prop("checked", isChecked);
                    
                    // Show/hide start buttons based on select all checkbox
                    if (isChecked) {
                        $('.start-btn').show();
                    } else {
                        $('.start-btn').hide();
                    }
                });

                $(document).on("change", ".test-checkbox", function() {
                    let totalCheckboxes = $(".test-checkbox").length;
                    let checkedCheckboxes = $(".test-checkbox:checked").length;
                    
                    // Update select all checkbox
                    if (totalCheckboxes === checkedCheckboxes) {
                        $("#selectAllTests").prop("checked", true);
                    } else {
                        $("#selectAllTests").prop("checked", false);
                    }
                    
                    // Update button visibility - hide all buttons if nothing is checked
                    if (checkedCheckboxes === 0) {
                        $('.start-btn').hide();
                    }
                });

                $("#backTabBtn").on("click", function() {
                    window.history.back();
                });

                function getParam(name) {
                    return new URLSearchParams(window.location.search).get(name);
                }

                function showAlreadyAttemptedModal() {
                    $("#alertModal").remove();
                    $("body").append(`
                        <div id="alertModal" style="
                            position: fixed;
                            top: 0; left: 0;
                            width: 100%; height: 100%;
                            background: rgba(0,0,0,0.5);
                            z-index: 9999;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        ">
                            <div style="
                                background: white;
                                border-radius: 16px;
                                padding: 35px 30px;
                                max-width: 420px;
                                width: 90%;
                                text-align: center;
                                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                            ">
                                <div style="font-size: 55px; margin-bottom: 12px;">⚠️</div>
                                <h2 style="
                                    color: #b45309;
                                    margin: 0 0 10px 0;
                                    font-size: 20px;
                                    font-family: Arial;
                                ">Test Already Attempted</h2>
                                <p style="
                                    color: #78350f;
                                    font-size: 15px;
                                    margin: 0 0 25px 0;
                                    font-family: Arial;
                                    line-height: 1.6;
                                ">
                                    You have already attempted this test.<br>
                                    Each test can only be taken <b>once</b>.
                                </p>
                                <button onclick="$('#alertModal').remove();" style="
                                    background: linear-gradient(180deg, #700002 0%, #700002e0 100%);
                                    color: white;
                                    border: none;
                                    padding: 12px 30px;
                                    border-radius: 10px;
                                    font-size: 15px;
                                    cursor: pointer;
                                    font-family: Arial;
                                ">OK, Got it</button>
                            </div>
                        </div>
                    `);
                }

                function startTest(testId) {
                    $.ajax({
                        type    : "GET",
                        url     : "/gettestquestion",
                        data    : { test_id: testId },
                        success : function(res) {

                            if (res.already_attempted === true || res.status === false) {
                                showAlreadyAttemptedModal();
                                return;
                            }

                            let params = $.param({
                                test_ids : testId,
                                mode     : "test"
                            });
                            window.location.href = "/exam-page?" + params;
                        },
                        error : function(err) {

                            if (err.status === 403) {
                                showAlreadyAttemptedModal();
                                return;
                            }

                            alert("Something went wrong. Please try again.");
                            console.error("API Error:", err);
                        }
                    });
                }
            </script>

        </div>
    </div>
</body>

</html>