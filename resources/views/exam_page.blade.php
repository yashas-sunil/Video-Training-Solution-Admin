<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Eduedge Exam</title>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* ===== PAGE ===== */
        body{
            margin:0;
            padding:0;
            background:#eef2f7;
            font-family: Arial, sans-serif;
        }

        /* ===== WRAPPER ===== */
        .exam-wrapper{
            min-height:100vh;
            display:flex;
            flex-direction:column;
            justify-content:center;
            align-items:center;
        }

        /* ===== EXAM CARD ===== */
        .exam-box{
            background:#fff;
            width:100%;
            max-width:520px;
            padding:35px;
            border-radius:16px;
            box-shadow:0 8px 22px rgba(0,0,0,0.12);
        }

        /* ===== QUESTION ===== */
        .question-title{
            font-size:20px;
            font-weight:700;
            text-align:center;
            margin-bottom:25px;
        }

        /* ===== OPTIONS ===== */
        .options-box{
            display:flex;
            flex-direction:column;
            gap:14px;
        }

        .option-item{
            display:flex;
            align-items:center;
            gap:12px;
            padding:14px 18px;
            border:1px solid #ddd;
            border-radius:10px;
            cursor:pointer;
            background:#fff;
            transition:.2s;
        }

        .option-item:hover{
            background:#f1f7ff;
            border-color:#2d89ff;
        }

        .option-item input{
            width:18px;
            height:18px;
        }

        .option-alpha{
            font-weight:700;
            width:25px;
        }

        .option-text{
            font-size:16px;
            font-weight:500;
        }

        /* ===== NAV BUTTONS ===== */
        .nav-btns{
            margin-top:25px;
            display:flex;
            gap:15px;
        }

        .back-btn,
        .next-btn{
            padding:10px 28px;
            border:none;
            border-radius:30px;
            font-size:16px;
            color:#fff;
            cursor:pointer;
        }

        .back-btn{ background:#7f8c8d; }
        .next-btn{ background:#27ae60; }

        /* ===== TOP BACK BUTTON ===== */
.top-back{
    width:100%;
    max-width:520px;
    margin-bottom:12px;
}

#topBackBtn{
    background:#2d89ff;
    color:#fff;
    border:none;
    padding:8px 18px;
    border-radius:20px;
    font-size:15px;
    cursor:pointer;
}

#topBackBtn:hover{
    background:#1b6fd6;
}

    </style>
</head>
<body>

    <!-- TOP BACK BUTTON -->
<div class="top-back">
    <button id="topBackBtn">⬅ Back</button>
</div>
<div class="exam-wrapper">

    <div class="exam-box" id="examBox">
        <p style="text-align:center;color:#999;">Loading questions...</p>
    </div>

    <div class="nav-btns" style="display:none">
        <button id="backBtn" class="back-btn">⬅ Back</button>
        <button id="nextBtn" class="next-btn">Next ➡</button>
    </div>

</div>

<script>
$(function(){

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

    $.get("/qbundle/filter", data, function(res){
        allQ = res.data || [];

        if(allQ.length === 0){
            $("#examBox").html("<p style='text-align:center;'>No Questions Found</p>");
            return;
        }

        showQuestion(0);
        $(".nav-btns").show();
    });

    function showQuestion(i){
        let q = allQ[i];

        let html = `
            <div class="question-title">
                Question ${i+1}
            </div>
            <div class="options-box">
        `;

        q.answers.forEach((a,idx)=>{
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

    $(document).on("change","input[name=opt]",function(){
        answers[$(this).data("qid")] = $(this).val();
    });

    $("#backBtn").click(()=>{
        if(qIndex>0){
            qIndex--;
            showQuestion(qIndex);
        }
    });

    $("#nextBtn").click(()=>{
        if(qIndex < allQ.length-1){
            qIndex++;
            showQuestion(qIndex);
            return;
        }

        // ✅ RESULT CALCULATION
        let correct = 0;
        let wrong = 0;

        allQ.forEach(q=>{
            let right = q.answers.find(a=>a.correctans==1);
            if(answers[q.id] == right?.id){
                correct++;
            }else{
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

$("#topBackBtn").click(function(){
    history.back();
});

</script>

</body>
</html>
