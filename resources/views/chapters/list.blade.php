<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Chapters</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f7f7f7;
        }

        /* TOP BAR */
        .navbar {
            background-color: #700002;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: fixed;
            top: 0px;
            width: -webkit-fill-available;
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

        /* PAGE */
        .page-content {
            padding: 30px;
            margin-top: 80px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        .chapter-list {
            max-width: 90%;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
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
            width: 29%;
            border-width: 2px;
            border-style: solid;
            border-color: #700002;
        }

        .open-text:hover {
            background:  #007bff;
            color:#fff
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

        @media (max-width: 425px) {
            .navbar {
                padding: 15px 6px;
            }

            .navbar .logo img {
                height: 30px;
                width: auto;
            }

            .navbar .user-info {
                gap: 8px;
            }
            .user-info-mobile {
                display: flex !important;
                gap: 8px;
            }

            .user-info a {
                margin-left: 0px;
                padding: 3px 10px;
                font-size: 12px;
            }

            .user-info-mobile a {
                color: white;
                text-decoration: none;
            }

            .user-info {
                display: none !important;
            }

            .user-welcome {
                gap: 4px;
                padding: 6px 10px;
            }
            .chapter-item {
                flex-wrap: wrap;
            }
        }
    </style>
</head>

<body>

    <!-- HEADER -->
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

    <!-- CONTENT -->
    <div class="page-content">

        <button onclick="window.history.back()" class="back-btn">⬅ Back</button>

        <h2>Course Chapters</h2>

        <div class="chapter-list">
            @forelse($chapters as $chapter)
                <div class="chapter-item" >

                    <span>{{ $chapter->name }}</span>
                    <span class="open-text"onclick="openChapter({{ $chapter->id }})" >Start →</span>
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
            const width = window.screen.availWidth;
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
