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
                                        <p>This is to acknowledge your on-line appointment cancellation request of the following appointment:</p>
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
                                                            <span style="text-decoration: line-through;"> {{ $item['item_name'] }}</span>
                                                            @if(($key+1)< sizeof($appointment['items']))
                                                                <span>, </span>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Reason of Cancellation: </td>
                                                <td> {{ $appointment['items'][0]['item_data']->cancel_reason }} </td>
                                            </tr>
                                        </table>
                                        <br/>
                                        <p>Thank you again for using the appointment system.</p>
                                        <p>For any concerns, please don't hesitate to contact us through 0917-LAY-BARE or email us at info@lay-bare.com</p>
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