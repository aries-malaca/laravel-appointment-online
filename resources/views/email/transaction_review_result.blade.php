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
                                        <p>Hi {{ delegation($user) }} {{ $user['first_name'] }} {{ $user['last_name'] }},</p>

                                        @if($action === 'approved')
                                            <p>
                                                We're done reviewing your transaction records and already updated your profile. Kindly
                                                check you Transactions page on your profile.
                                            </p>
                                        @else
                                            <p>
                                                We're reviewing possible transactions linked with your information, unfortunately we haven't
                                                find any transaction.
                                            </p>
                                            <p>For any concerns, please don't hesitate to contact us through 0917-LAY-BARE or email us at info@lay-bare.com</p>
                                        @endif

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