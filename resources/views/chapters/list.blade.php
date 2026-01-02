<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course Chapters</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
        }

        /* TOP BAR */
        .navbar {
            background-color: #007bff;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .navbar .logo img {
            height: 55px;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        /* PAGE */
        .page-content {
            padding: 30px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .chapter-list {
            max-width: 700px;
            margin: 0 auto;
        }

        .chapter-item {
            background: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .chapter-item:hover {
            background: #f0f6ff;
            border-color: #007bff;
        }

        .chapter-item span {
            font-size: 16px;
            font-weight: 500;
        }

        .open-text {
            font-size: 14px;
            color: #007bff;
            font-weight: 500;
        }

        .no-data {
            text-align: center;
            color: #666;
            margin-top: 40px;
        }

        .back-btn {
            margin-bottom: 20px;
            padding: 6px 12px;
            border: none;
            background: #007bff;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>

<!-- HEADER -->
<div class="navbar">
    <div class="logo">
        <img src="{{ asset('images/logo-2.png') }}" alt="Company Logo">
    </div>

    <div class="user-info">
        <div>Welcome, <strong>{{ auth()->user()->name }}</strong></div>

        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
            @csrf
        </form>
    </div>
</div>

<!-- CONTENT -->
<div class="page-content">

    <button onclick="window.history.back()" class="back-btn">⬅ Back</button>

    <h2>Course Chapters</h2>

    <div class="chapter-list">
        @forelse($chapters as $chapter)
            <div class="chapter-item"
                 onclick="openChapter({{ $chapter->id }})">

                <span>{{ $chapter->name }}</span>
                <span class="open-text">Click to open →</span>
            </div>
        @empty
            <div class="no-data">
                No chapters available for this course.
            </div>
        @endforelse
    </div>

</div>

<script>
    function openChapter(chapterId) {
        const width  = window.screen.availWidth;
        const height = window.screen.availHeight;

        const popup = window.open(
            `/view/chapter/${chapterId}`,
            '_blank',
            `width=${width},height=${height},top=0,left=0,resizable=yes,scrollbars=yes`
        );

        if (!popup || popup.closed || typeof popup.closed === 'undefined') {
            alert('Please allow popups to open the chapter.');
        } else {
            popup.focus();
        }
    }
</script>

</body>
</html>
