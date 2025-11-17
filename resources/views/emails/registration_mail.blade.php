<head>
    <link href="https://fonts.googleapis.com/css?family=Ubuntu&display=swap" rel="stylesheet">
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            min-width: 100% !important;
            font-family: 'Ubuntu', sans-serif;
        }

        img {
            height: auto;
        }

        .content {
            width: 100%;
            max-width: 660px;
        }

        .innerpadding {
            padding: 30px 30px 30px 30px;
            line-height: 25px;
        }

        .borderbottom {
            border-bottom: 1px solid #f2eeed;
        }

        .bodycopy {
            font-size: 16px;
            line-height: 22px;
            color: #153643;
        }

        .footer {
            padding: 20px 30px 20px 30px;
        }

        .footercopy {
            font-size: 14px;
            color: #ffffff;
        }

        .footercopy a {
            color: #ffffff;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <table bgcolor="#ffffff" class="content" align="center" cellpadding="0" cellspacing="0" border="0">
        <tbody>

            <!-- Removed Header (orange area) -->
            <tr>
                <td style="padding: 0;"></td>
            </tr>

            <tr>
                <td class="innerpadding borderbottom" style="padding-top: 0px;">

                    Hi {{ $attributes['name'] }},
                    <br><br>

                    Thank you for being a part of EduEdgeProLMS. Your EduEdgeProLMS account credentials are given below.
                    Do not share with others.
                    <br><br>

                    <p><b>Email : {{ $attributes['email'] }}</b></p>
                    <p><b>Password : {{ $attributes['password'] }}</b></p>

                    <br><br>

                    @php
                        // Dynamic login URL based on environment
                        if (config('app.env') === 'staging') {
                            $loginUrl = env('LOGIN_URL_STAGING', 'https://eduedgeprolms.datavoice.co.in/login');
                        } elseif (config('app.env') === 'qa') {
                            $loginUrl = env('LOGIN_URL_QA', 'https://qaeduedgeprolms.datavoice.co.in/login');
                        } else {
                            $loginUrl = env('LOGIN_URL_PROD', 'https://eduedgeprolms.datavoice.co.in/login');
                        }
                    @endphp

                    <a href="{{ $loginUrl }}"
                        style="background-color: #f58457; border: none; color: white; padding: 15px 32px; text-align: center; text-decoration: none; display: inline-block; font-size: 16px;">
                        Login Now
                    </a>

                    <br><br>
                </td>
            </tr>

            <tr>
                <td class="footer" bgcolor="#f58457">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td align="center" class="footercopy">
                                    © {{ date('Y') }} EduEdgeProLMS, All rights reserved.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>

        </tbody>
    </table>
</body>
