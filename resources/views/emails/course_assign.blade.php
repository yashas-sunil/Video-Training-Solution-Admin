<head>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Ubuntu', sans-serif;
        }

        .content {
            width: 100%;
            max-width: 660px;
            margin: auto;
        }

        .innerpadding {
            padding: 30px;
            line-height: 25px;
        }

        .footer {
            padding: 20px;
            background: #f58457;
            color: #fff;
            text-align: center;
        }
    </style>
</head>

<body>
    <table class="content" cellpadding="0" cellspacing="0">
        <tr>
            <td class="innerpadding">

                Hi {{ $attributes['name'] }},<br><br>

                You have been assigned a new course on EduEdgeProLMS.<br><br>

                <b>Course Name:</b> {{ $attributes['course'] }}<br>
                <b>Expiry Date:</b> {{ $attributes['expire_date'] }}<br><br>

                @php
                    if (config('app.env') === 'staging') {
                        $loginUrl = env('LOGIN_URL_STAGING');
                    } elseif (config('app.env') === 'qa') {
                        $loginUrl = env('LOGIN_URL_QA');
                    } else {
                        $loginUrl = env('LOGIN_URL_PROD');
                    }
                @endphp

                <b>URL:</b><br>
                <a href="{{ $loginUrl }}" target="_blank">
                    {{ $loginUrl }}
                </a>

                <br><br>

                <!-- Orange Login Button (email-safe) -->
                <table cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" bgcolor="#f58457" style="border-radius:4px;">
                            <a href="{{ $loginUrl }}" target="_blank"
                                style="
            font-size:16px;
            font-family:Ubuntu,sans-serif;
            color:#ffffff;
            text-decoration:none;
            padding:12px 24px;
            display:inline-block;">
                                Login Now
                            </a>
                        </td>
                    </tr>
                </table>

                <br><br>
                Please login to your account to start the course.

            </td>
        </tr>

        <tr>
            <td class="footer">
                © {{ date('Y') }} EduEdgeProLMS, All rights reserved.
            </td>
        </tr>
    </table>
</body>
