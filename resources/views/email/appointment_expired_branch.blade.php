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
                                        <p>Dear {{ $branch['branch_name'] }},</p>
                                        <p>Here's the list of Branch Expired Appointment last {{ date('m/d/Y') }} with the following detail.</p>
                                        <br/>
                                        <table class="datatable">
                                            <tr>
                                               <th>Client Name</th>
                                               <th>Time</th>
                                               <th>Booked Service(s)</th>
                                               <th>Technician</th>
                                            </tr>
                                            @foreach($appointments as $key=>$value)
                                                <tr>
                                                    <td>{{ $value['client_first_name'] }} {{ $value['client_last_name'] }}</td>
                                                    <td>{{ date('m/d/Y', strtotime($value['transaction_datetime'])) }}</td>
                                                    <td>
                                                        @foreach($value['items'] as $key => $item)
                                                            <span> {{ $item['item_name'] }}</span>
                                                            @if(($key+1)< sizeof($value['items']))
                                                                <span>, </span>
                                                            @endif
                                                        @endforeach
                                                    </td>
                                                    <td>
                                                        {{ $value['technician_first_name'] }} {{ $value['technician_last_name'] }}
                                                    </td>
                                                </tr>
                                            @endforeach
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