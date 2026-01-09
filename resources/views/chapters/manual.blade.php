<!DOCTYPE html>
<html lang="en">
<head>
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
            max-width: 1024px;
            margin: 30px auto;
            padding: 0 16px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .btn {
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
        .btn.secondary {
            background: #fff;
            color: #700002;
        }

        .overview {
            margin-bottom: 40px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .card {
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 14px;
        }

        /* overview layout */
        .overview-item {
            display: flex;
            gap: 24px;
            margin-bottom: 26px;
        }
        .overview-item .label {
            min-width: 180px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
        }

        .lessons-wrapper {
            display: flex;
            gap: 20px;
        }
        .lessons-list {
            min-width: 220px;
            border-right: 1px solid #ccc;
            padding-right: 10px;
        }
        .lessons-list button {
            display: block;
            width: 100%;
            margin-bottom: 6px;
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #700002;
            background: #fff;
            color: #700002;
            cursor: pointer;
        }
        .lessons-list button.active {
            background: #700002;
            color: #fff;
        }

        .lesson-contents {
            flex: 1;
        }

        /* 🔥 IMPORTANT: lesson kabhi hide nahi hoga */
        .lesson-section {
            display: none;
            margin-bottom: 60px;
        }

        .continue-wrap {
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>

<body>

<div class="page">

    <div class="header">
        <h1>{{ $chapter->name }}</h1>
        <button class="btn secondary" onclick="window.history.back()">← Back</button>
    </div>

    {{-- 🔹 OVERVIEW (no click, same as before) --}}
    <div class="overview">
        <h2>Overview</h2>

        @foreach ($overview as $label => $items)
            <div class="overview-item">
                <div class="label">{{ $label }}</div>
                <div class="content">
                    <div class="grid">
                        @foreach ($items as $content)
                            <div class="card">
                                <h3>{{ $content['original_name'] }}</h3>
                                <a class="btn" href="{{ $content['url'] }}" target="_blank">View</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 🔹 LESSONS --}}
    <div class="lessons-wrapper">
        <div class="lessons-list">
            @foreach ($lessons as $i => $lesson)
                <button data-index="{{ $i }}">
                    {{ $lesson['lesson_name'] }}
                </button>
            @endforeach
        </div>

        <div class="lesson-contents">
            @foreach ($lessons as $i => $lesson)
                <div class="lesson-section" id="lesson-{{ $i }}">

                    @foreach ($lesson['contents'] as $type => $items)
                        <h3>{{ $type }}</h3>
                        <div class="grid">
                            @foreach ($items as $content)
                                <div class="card">
                                    <h4>{{ $content['original_name'] }}</h4>
                                    <a class="btn" href="{{ $content['url'] }}" target="_blank">View</a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- 🔹 CONTINUE --}}
                    @if(isset($lessons[$i + 1]))
                        <div class="continue-wrap">
                            <button class="btn continue-btn"
                                    data-next="{{ $i + 1 }}">
                                Continue →
                            </button>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
    const lessonBtns = document.querySelectorAll('.lessons-list button');
    const lessonSections = document.querySelectorAll('.lesson-section');
    const continueBtns = document.querySelectorAll('.continue-btn');

    let unlocked = 0;

    function showLessonsTill(index) {
        for (let i = 0; i <= index; i++) {
            if (lessonSections[i]) {
                lessonSections[i].style.display = 'block';
            }
        }

        lessonBtns.forEach(btn => btn.classList.remove('active'));
        if (lessonBtns[index]) {
            lessonBtns[index].classList.add('active');
        }

        lessonSections[index].scrollIntoView({ behavior: 'smooth' });
    }

    // Sidebar click
    lessonBtns.forEach((btn, index) => {
        btn.addEventListener('click', () => {
            unlocked = Math.max(unlocked, index);
            showLessonsTill(unlocked);
        });
    });

    // Continue click
    continueBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            unlocked = parseInt(btn.dataset.next);
            showLessonsTill(unlocked);
        });
    });

    // Initial
    showLessonsTill(0);
</script>

</body>
</html>
