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
                                        <p>Thank you for visiting Lay Bare.</p>
                                        <p>Dear {{ delegation($user) }} {{ $user['first_name'] }} {{ $user['last_name'] }},</p>
                                        <p>You have successfully completed your waxing service. </p>
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
                                                <td>Completed Time: </td>
                                                <td> {{ date('h:i A', strtotime($appointment['complete_time'])) }} </td>
                                            </tr>
                                            <tr>
                                                <td>Service(s): </td>
                                                <td>
                                                    @foreach($appointment['items'] as $key => $item)
                                                        @if($item['item_type'] == 'service')
                                                            @if($item['item_status'] == 'completed')
                                                                {{ $item['item_name'] }}
                                                            @else
                                                                <span style="text-decoration: line-through;"> {{ $item['item_name'] }}</span>
                                                            @endif

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

                                        <img src="{{ url('images/app/postwaxingcare.jpg') }}" width="100%"/>
                                        <br/><br/>

                                        <p>Book your next visit now! Please follow the link below to create your own client profile.</p>

                                        <p align="center">
                                            <table width="50%" align="center" >
                                                <td width="50%" style="padding:5px;"> <a href="https://itunes.apple.com/us/app/lay-bare/id1113862522?mt=8"><img src="{{ url('images/app/appstoreicon.fw.png') }}" width="100%" height="70px;" /></a></td>
                                                <td width="50%" style="padding:5px;"><a href="https://play.google.com/store/apps/details?id=com.system.mobile.lay_bare"><img src="{{ url('images/app/googleplay.fw.png') }}" width="100%" height="70px;" /></a> </td>
                                            </table>
                                        </p>

                                        <p align="center">
                                            or signup to <a href="{{ url('register') }}">Lay Bare Online</a>
                                            <br/>
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