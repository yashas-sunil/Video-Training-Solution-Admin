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

        /* EXAM BOX */
        .exam-box {
            background: white;
            padding: 35px;
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.12);
            min-height: 230px;
            max-width: 450px;
            margin: 0 auto;
        }

        .q-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }

        /* OPTIONS UPDATED & CLEAN */
        .options-box {
            margin-top: 10px;
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
            transition: 0.2s;
        }

        .option-item:hover {
            background: #f1f7ff;
            border-color: #2d89ff;
        }

        .option-item input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .opt-alpha {
            font-weight: 700;
            font-size: 16px;
            width: 25px;
            text-align: center;
        }

        .option-text {
            font-size: 16px;
            font-weight: 500;
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
    </style>

</head>

<body>

    <div class="navbar"><b>Exam Mode</b></div>

    <div class="container" style="margin-top: 90px;">

        <h2>🎯 Question Filter</h2>

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

        <div id="examBox" class="exam-box">
            <p style="text-align:center;color:#666;">No Questions Loaded...</p>
        </div>

        <div class="nav-btns">
            <button id="backBtn" class="back-btn">⬅️ Back</button>
            <button id="nextBtn" class="next-btn">Next ➡️</button>
        </div>

    </div>

    <script>
        let allQ = [];
        let qIndex = 0;
        $("#subject_id").change(function() {
            let id = $(this).val();

            $("#chapter_id").html('<option>Loading...</option>');
            $("#subchapter_id").html('<option value="">Select</option>');

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

        $("#chapter_id").change(function() {
            let id = $(this).val();

            $("#subchapter_id").html('<option>Loading...</option>');

            $.ajax({
                url: "/get-subchapters",
                type: "GET",
                data: {
                    chapter_id: id
                },
                success: function(res) {
                    console.log("Response:", res);

                    let html = '<option value="">Select</option>';

                    if (Array.isArray(res)) {
                        res.forEach(sc => {
                            html += `<option value="${sc.id}">${sc.name}</option>`;
                        });
                    } else if (res.data) {
                        res.data.forEach(sc => {
                            html += `<option value="${sc.id}">${sc.name}</option>`;
                        });
                    }

                    $("#subchapter_id").html(html);
                },
                error: function(err) {
                    console.log("Error:", err);
                    alert("API call Not...");
                }
            });
        });

        $("#filterBtn").click(function() {

            $("#examBox").html("<p style='text-align:center;'>Loading...</p>");
            $(".nav-btns").hide();

            $.ajax({
                url: "/qbundle/filter",
                type: "GET",
                data: {
                    subject_id: $("#subject_id").val(),
                    chapter_id: $("#chapter_id").val(),
                    subchapter_id: $("#subchapter_id").val(),
                    difficult_level_id: $("#difficult_level_id").val(),
                    used_status: $("#used_status").val(),
                    limit: $("#limit").val()
                },

                success: function(res) {

                    allQ = res.data;
                    qIndex = 0;

                    if (allQ.length === 0) {
                        $("#examBox").html("<p style='text-align:center;'>No Questions Found</p>");
                        return;
                    }

                    showQuestion(qIndex);
                    $(".nav-btns").show();
                }
            });
        });

        function showQuestion(i) {
            let q = allQ[i];

            // QUESTION HTML
            let html = `
        <div class="question-card">
            <div class="question-header">
                <span class="question-number">Question ${i + 1}</span>
            </div>

            <div class="question-text">
                ${q.question}
            </div>

            <div class="options-container">
    `;

            // OPTIONS LOOP
            q.answers.forEach((a, idx) => {
                html += `
            <label class="option-item">
                <input type="radio" name="opt" value="${a.id}">
                <div class="option-content">
                    <span class="option-index">${String.fromCharCode(65 + idx)}</span>
                    <span class="option-text">${a.answer}</span>
                </div>
            </label>
        `;
            });

            html += `
            </div>
        </div>
    `;

            $("#examBox").html(html);
        }
        // BACK BUTTON
        $("#backBtn").click(function() {
            if (qIndex > 0) {
                qIndex--;
                showQuestion(qIndex);
            }
        });

        // NEXT BUTTON
        $("#nextBtn").click(function() {
            qIndex++;

            if (qIndex >= allQ.length) {
                $("#examBox").html(`
            <div class='completed-box'>
                <h3>🎉 Exam Completed!</h3>
                <p>Nice Work! You answered all the questions.</p>
            </div>
        `);
                $(".nav-btns").hide();
                return;
            }

            showQuestion(qIndex);
        });
    </script>
</body>

</html>
