<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    @include('email.layouts.head')
</head>
<body class="">
<table border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <!-- START CENTERED WHITE CONTAINER -->
                <table class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            @include('email.layouts.logo')
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p>Dear {{ $user['delegation'] }} {{ $user['first_name'] }} {{ $user['last_name'] }},</p>
                                        <p>Based on our system records, you've been trying without success to access your Lay Bare On-line Profile.</p>
                                        <p>
                                            You may access the forgot password tool found in the log-in page of the system to have your password sent to your email.
                                            Please note that there has been a discovered issue with the password generation tool in some browsers wherein
                                            an additional space is added to the end. Make sure that when you copy and paste that there are only 8 masked characters
                                            that appear, or alternatively, paste the characters in the username field to view, delete the added character,
                                            and paste in the password field before entering your username and submitting.
                                        </p>

                                        <p>For any other concerns, you may email us at support@lay-bare.com.</p>
                                        <p>Sincerely,</p>
                                        <p>Lay Bare Waxing Salon</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- END MAIN CONTENT AREA -->
                </table>
                <!-- START FOOTER -->
            @include('email.layouts.footer')
            <!-- END FOOTER -->
                <!-- END CENTERED WHITE CONTAINER -->
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>