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
                                        <p>Dear {{ delegation($user) }} {{ $user['first_name'] }} {{ $user['last_name'] }},</p>
                                        <p>Thank you for completing the Premier Loyalty Card on-line application.</p>
                                        <p>
                                            This is to formally acknowledge receipt of your application dated {{ date('m/d/Y')  }} , with the following information:
                                        </p>

                                        <table class="datatable">
                                            <tr>
                                                <td>First Name:</td>
                                                <td>{{ $user['first_name']  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Last Name:</td>
                                                <td>{{ $user['last_name']  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Address:</td>
                                                <td>{{ $user['user_address']  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Mobile:</td>
                                                <td>{{ $user['user_mobile']  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Birth Date:</td>
                                                <td>{{ date('m/d/Y', strtotime($user['birth_date']))  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Branch:</td>
                                                <td>{{ $data['branch']['label']  }}</td>
                                            </tr>
                                            <tr>
                                                <td>Application Type:</td>
                                                <td>{{ $data['type'] }}</td>
                                            </tr>
                                        </table>
                                        <br/>
                                        <p>Our Customer Service team will be reviewing your application details and shall notify you via email if you have reached the minimum required accumulated service total to qualify or not.</p>
                                        <p>We appreciate your time and effort, and your continued patronage of Lay Bare Waxing Salon.</p>
                                        <p>Keep on waxing.</p>
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