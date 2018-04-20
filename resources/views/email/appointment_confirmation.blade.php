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
                                        <p>Thank you for booking your appointment on-line. This is to confirm your booking on: </p>
                                        <br/>
                                        <table class="datatable">
                                            <tr>
                                                <td>Branch Name: </td>
                                                <td> {{ $appointment['branch_name'] }} </td>
                                            </tr>
                                            <tr>
                                                <td>Date: </td>
                                                <td> {{ date('m/d/Y', strtotime($appointment['transaction_datetime'])) }} </td>
                                            </tr>
                                            <tr>
                                                <td>Time: </td>
                                                <td> {{ date('h:i A', strtotime($appointment['transaction_datetime'])) }} </td>
                                            </tr>
                                            <tr>
                                                <td>Service(s): </td>
                                                <td>
                                                    @foreach($appointment['items'] as $key => $item)
                                                        @if($item['item_type'] == 'service')
                                                            {{ $item['item_name'] }}
                                                            @if(($key+1)< sizeof($appointment['items']))
                                                                <span>, </span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Email: </td>
                                                <td> {{ $user['email'] }} </td>
                                            </tr>
                                            <tr>
                                                <td>Technician: </td>
                                                <td> {{ $appointment['technician_first_name'] }} {{ $appointment['technician_last_name'] }} </td>
                                            </tr>
                                        </table>
                                        <br/>
                                        <p>Please be in the branch 10 minutes before your scheduled appointment time. We look forward to seeing you in our branch.</p>
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