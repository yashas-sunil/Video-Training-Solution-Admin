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

        .overview, .lessons-wrapper {
            margin-bottom: 30px;
        }

        .overview .grid,
        .lesson-contents .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 14px;
        }

        .card {
            background: #fff;
            border: 1px solid #e6e6e6;
            border-radius: 10px;
            padding: 14px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04);
        }
        .card h3 {
            margin: 0 0 6px 0;
            font-size: 16px;
        }
        .small {
            font-size: 12px;
            color: #777;
        }

        /* overview layout */
        .overview-item {
            display: flex;
            gap: 24px;
            align-items: flex-start;
            margin-bottom: 26px;
        }
        .overview-item .label {
            min-width: 180px;
            text-align: right;
            font-weight: bold;
            font-size: 16px;
            padding-top: 6px;
        }
        .overview-item .content {
            flex: 1;
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
            text-align: left;
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
        .lesson-contents .type-section {
            display: none;
        }
        .lesson-contents .type-section.active {
            display: block;
        }

        /* 🔹 NEW: Floating Back to Overview Button */
        .back-to-overview {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: #700002;
            color: #fff;
            padding: 10px 16px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 6px 14px rgba(0,0,0,0.2);
            display: none;
            z-index: 999;
        }
    </style>
</head>

<body>

<!-- 🔹 Floating Button -->
<div class="back-to-overview" id="backToOverview">
    ↑ Back to Overview
</div>

<div class="page">

    <div class="header">
        <div>
            <h1>{{ $chapter->name }}</h1>
            <div class="small">Manual uploads for this chapter</div>
        </div>
        <div>
            <button class="btn secondary" onclick="window.history.back()">← Back</button>
        </div>
    </div>

    {{-- 🔹 Overview Section --}}
    <div class="overview" id="overviewSection">
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
                                @if ($content['size'])
                                    <div class="small">
                                        Size: {{ round($content['size'] / 1024, 1) }} KB
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- 🔹 Lessons Section --}}
    <div class="lessons-wrapper" id="lessonsSection">
        <div class="lessons-list">
            @foreach ($lessons as $idx => $lesson)
                <button class="{{ $idx === 0 ? 'active' : '' }}"
                        data-lesson="{{ $lesson['lesson_id'] }}">
                    {{ $lesson['lesson_name'] }}
                </button>
            @endforeach
        </div>

        <div class="lesson-contents">
            @foreach ($lessons as $idx => $lesson)
                <div class="type-section {{ $idx === 0 ? 'active' : '' }}"
                     data-lesson="{{ $lesson['lesson_id'] }}">
                    @foreach ($lesson['contents'] as $type => $items)
                        <h4>{{ $type }}</h4>
                        <div class="grid">
                            @foreach ($items as $content)
                                <div class="card">
                                    <h3>{{ $content['original_name'] }}</h3>
                                    <a class="btn" href="{{ $content['url'] }}" target="_blank">View</a>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>

</div>

<script>
    const lessonButtons = document.querySelectorAll('.lessons-list button');
    const lessonSections = document.querySelectorAll('.lesson-contents .type-section');
    const backBtn = document.getElementById('backToOverview');
    const overviewSection = document.getElementById('overviewSection');

    lessonButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const lessonId = btn.getAttribute('data-lesson');
            lessonButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            lessonSections.forEach(sec => {
                sec.classList.toggle('active', sec.getAttribute('data-lesson') === lessonId);
            });
        });
    });

    // 🔹 show button when scrolling down
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            backBtn.style.display = 'block';
        } else {
            backBtn.style.display = 'none';
        }
    });

    // 🔹 scroll to overview
    backBtn.addEventListener('click', () => {
        overviewSection.scrollIntoView({ behavior: 'smooth' });
    });
</script>

</body>
</html>
