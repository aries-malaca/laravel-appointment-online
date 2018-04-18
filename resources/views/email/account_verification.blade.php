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
                                        <p>Dear {{ $user['first_name'] }} {{ $user['last_name'] }},</p>

                                        <p>
                                            Thank you for using Lay Bare On-line. To activate your account, please select the link found below and follow the instruction in the web page.
                                        </p>

                                        @if($raw_password !== null)
                                            <p>
                                                Your log-in credentials are specified below:
                                            </p>
                                            <p>
                                                <table style="width:50%" class="datatable">
                                                    <tr>
                                                        <td>Username: </td>
                                                        <td> {{ $user['email'] }} </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Password: </td>
                                                        <td> {{ $raw_password}} </td>
                                                    </tr>
                                                </table>
                                            </p>
                                        @endif

                                        <p>Please click this link or copy & paste into your address bar to activate your account:</p>

                                        <p>
                                            <a href="{{ url('/register/verify?email='. $user['email'] .'&key=' . $generated)  }}" target="_blank">
                                                {{ url('/register/verify?email='. $user['email'] .'&key=' . $generated)  }}
                                            </a>
                                        </p>

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