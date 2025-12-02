<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Mode</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            font-family: Arial;
            background: #eef2f7;
            padding: 20px;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background: #f8f9fa;
            padding: 15px;
        }

        /* FILTER CARD */
        .filter-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            margin-bottom: 25px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 18px;
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
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #c9c9c9;
            background: #fafafa;
        }

        #filterBtn {
            background: #2d89ff;
            padding: 12px 30px;
            color: white;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 16px;
        }


        /* NAV BUTTONS */
        .nav-btns {
            max-width: 700px;
            margin: 20px auto;
            display: flex;
            justify-content: center;
            gap: 12px;
        }

        .back-btn,
        .next-btn {
            padding: 9px 20px;
            font-size: 16px;
            border: none;
            border-radius: 55px;
            color: white;
            cursor: pointer;
        }

        .back-btn {
            background: #7f8c8d;
        }

        .next-btn {
            background: #27ae60;
        }

        /* ===== NAVBAR TABS ===== */
/* CENTERED NAVBAR TABS */
.navbar {
    position: fixed;
    top: 0;
    width: 100%;
    padding: 15px;
    background: #f8f9fa;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    z-index: 1000;
}

.navbar .tabs {
    display: flex;
    justify-content: center; /* tabs center */
    align-items: center;
    gap: 15px;
    position: relative;
}

/* Back button left */
.navbar .back-btn {
    position: absolute;
    left: 15px;
    padding: 8px 20px;
    border-radius: 30px;
    border: none;
    background: #7f8c8d;
    color: white;
    cursor: pointer;
    transition: 0.2s;
}

.navbar .back-btn:hover {
    background: #5d6d7e;
}

.navbar .tab-btn {
    padding: 8px 20px;
    border-radius: 30px;
    border: none;
    cursor: pointer;
    background: #ddd;
    font-weight: bold;
    transition: 0.2s;
}

.navbar .tab-btn.active {
    background: #2d89ff;
    color: #fff;
}


    </style>

</head>

<body>
<!-- NAVBAR WITH TABS -->
<div class="navbar">
    <div class="tabs">
        <button id="backTabBtn" class="back-btn">⬅ Back</button>
        <button class="tab-btn active" onclick="openTab('examTab')">📘 User Eduedge Questions</button>
        <button class="tab-btn" onclick="openTab('questionTab')">📝 Create Eduedge Questions</button>
                
    </div>

    <!-- Back Button -->
    {{-- <div style="text-align:center; margin-top:10px;">
        <button id="backTabBtn" class="back-btn">⬅ Back</button>
    </div> --}}
</div>

<div class="container" style="margin-top: 90px;">

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

            <button id="filterBtn">🔍 Start Exam</button>
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

</

</div>


</div>
<script>
let allQ = [];
let qIndex = 0;
let userAnswers = {};

/* ------------------------------
   SUBJECT → CHAPTER
--------------------------------*/
$("#subject_id").change(function () {
    let id = $(this).val();
    $("#chapter_id").html('<option>Loading...</option>');
    $("#subchapter_id").html('<option value="">Select</option>');

    $.get("/get-chapterBy-subject",{ subjects_id: id },function (res) {
        let html = '<option value="">Select</option>';
        res.forEach(c => html += `<option value="${c.id}">${c.name}</option>`);
        $("#chapter_id").html(html);
    });
});

/* ------------------------------
   CHAPTER → SUBCHAPTER
--------------------------------*/
$("#chapter_id").change(function () {
    let id = $(this).val();
    $("#subchapter_id").html('<option>Loading...</option>');

    $.get("/get-subchapters",{ chapter_id: id },function (res) {
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
$("#filterBtn").click(function () {

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


$("#startExamFromQuestionTab").click(function () {

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
function getParam(name){
    return new URLSearchParams(window.location.search).get(name);
}

</script>


<script>
function openTab(tabId){
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(tc => tc.style.display='none');
    event.target.classList.add('active');
    document.getElementById(tabId).style.display='block';
}
$("#backTabBtn").click(function(){
    window.history.back(); 
});
</script>
</body>

</html>
