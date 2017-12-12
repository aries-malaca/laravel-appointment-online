<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Email Verification</title>
    @include('email.layouts.head')
</head>
<body class="">
<table border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <!-- START CENTERED WHITE CONTAINER -->
                <span class="preheader">Premier Loyalty Card Application</span>
                <table class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            @include('email.layouts.logo')
                            <table border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p>Hi {{  $user['first_name'] }},</p>

                                        @if(!$result)
                                            <p>Thank you for your application.</p>
                                            <p>
                                                We regret to inform you that based on our system records you have yet to reach the accumulated
                                                qualified amount of Php 5,000.00 worth of services needed to qualify for a Premier Loyalty Card.
                                                We will also manually review your records in case of any discrepancies and shall re-evaluate your application.
                                            </p>
                                            <p>You may re-apply again once you've attained this status.</p>
                                            <p>Keep on waxing.</p>
                                        @else
                                            <p>Congratulations!</p>

                                            <p>
                                                We're very happy to inform you that you are qualified to avail our Premier Loyalty Card.
                                                You will be entitled to get 10% off on all our services in any of our branches nationwide.
                                                Standard processing time is between 3 to 4 weeks before the card is delivered to your requested branch.
                                                <br/>
                                                You will be notified via email once the card is ready to be collected and you can also monitor
                                                the status of your application by viewing the PLC Application tool found in your Lay Bare access.
                                                <br/>
                                            </p>

                                            <p>Thank you and Keep on waxing!</p>
                                        @endif

                                        <p>Sincerely,</p>

                                        <p><b>Lay Bare Waxing Salon</b></p>
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