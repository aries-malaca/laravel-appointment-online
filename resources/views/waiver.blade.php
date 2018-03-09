<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="author" />
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="../../theme/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="../../theme/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../theme/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="../../theme/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="../../theme/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="../../theme/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
    <link href="../../theme/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN THEME LAYOUT STYLES -->
    <link href="../../theme/layouts/layout4/css/layout.min.css" rel="stylesheet" type="text/css" />
    <!-- END THEME LAYOUT STYLES -->
    <link rel="icon" type="image/png" href="../../favicon.png">
    <title>Waiver Form</title>
</head>
<body>
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" style="height:900px">
            <div class="modal-header">
                <div>
                    <div style="width:20%;float:left">
                        <img src="/logo.png" width="100px;" height="100px;">
                    </div>
                    <div style="width:80%;float:left">
                        <p align="center" style="font-family:'Times New Roman', Times, serif; font-size:18px;">L A Y &nbsp;  B A R E &nbsp;&nbsp; W A X I N G &nbsp;&nbsp;  S A L O N
                            <br><span style="font-size:10px;">{{ $branch->branch_address  }}</span>
                            <br><span style="font-size:10px;">{{ $branch->branch_contact }}</span>
                        </p>
                    </div>
                </div>
                <a class="btn btn-lg blue hidden-print margin-bottom-5 pull-right" onclick="javascript:window.print();"> Print
                    <i class="fa fa-print"></i>
                </a>
            </div>

            <div class="modal-body" id="modal-body">
                <h5 style="font-weight:bolder;text-align:center">CLIENT WAIVER FORM</h5>
                <div>
                    <div style="width:50%;float:left">
                        {{ $client_type }} <br>
                        Name: <span style="text-decoration:underline">{{ $client->username }}</span> <br>
                        Address: <span style="text-decoration:underline">{{  $client->user_address }}</span> <br>
                        Email Address: <span style="text-decoration:underline">{{ $client->email }}</span> <br>
                        Gender: <span style="text-decoration:underline">{{ ucfirst($client->gender) }}</span> <br>
                    </div>
                    <div style="width:50%;float:left">
                        Client ID #: <span style="text-decoration:underline">{{ $client->id }}</span> <br>
                        Date/Time: <span style="text-decoration:underline">{{ date('m/d/Y h:i A', strtotime($appointment_date)) }}</span> <br>
                        Birthdate: <span style="text-decoration:underline">{{ date('m/d/Y', strtotime($client->birth_date)) }}</span> <br>
                        Phone No.: <span style="text-decoration:underline">{{ $client->user_mobile }}</span> <br>
                        Waxing Technician: <span style="text-decoration:underline" id="waxer"> {{ $technician  }} </span> <br><br>
                    </div>
                </div>
                <table style="border:thin; border-color:#666; border-style:solid;width:100%;">
                    <thead>
                        <tr style="border:thin; border-color:#666; border-style:solid;">
                            <th>Questionnaire</th>
                            <th style="width:50px">Yes/No</th>
                            <th style="width:200px">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data->questions as $key=>$value)
                        <tr>
                            <td style="border:thin; border-color:#666; border-style:solid;">
                                {{ $value->question }}
                            </td>
                            <td style="border:thin; border-color:#666; border-style:solid; text-align:center;">
                                 {{ $value->selected ? 'YES':'NO' }}
                            </td>
                            <td style="border:thin; border-color:#666; border-style:solid;">
                                @if(isset($value->data->answer))
                                    {{ $value->data->answer }}
                                @else
                                    @if(isset($value->data->options))
                                        @if($value->data->options[$value->data->selected_option]->textbox)
                                            {{ $value->data->options[$value->data->selected_option]->answer }}
                                        @endif
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <p>
                    I  {{ $client->username }} of legal age, fully understood that the procedure/s involves certain risk/s to my body,
                    which includes scratches, pain, soreness, injury, sickness, irittation, or rash, etc., which may be present
                    and/or after the procedure and I fully accept and assume such risk and responsibility for losses, cost, and
                    damages I may occur. I hereby release and discharge LAY BARE WAXING SALON, its stockholders, directors,
                    franchisees, officers and technicians from all liability, claims, damages, losses, arising from the services
                    they have rendered into me. I acknowledge that I have read this Agreement and fully understand its terms and
                    conditions.
                </p>


                <div style="padding:20px" >

                    <div style="width:50%;float:left">
                        @if( $data->signature != null)
                        <img style="height:50px; width:100px" src="{{ $data->signature }}"/>
                        @else
                        <img style="height:50px; width:100px" src="../../images/na.png"/>
                        @endif
                        <br>
                        {{ $client->username }}

                    </div>

                    <div style="width:50%;float:left">
                        <p>
                            <u>{{ date('m/d/Y h:i A', strtotime($appointment_date)) }}</u>
                            <br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date/Time
                        </p>
                        <h4>{{ $appointment->reference_no }}</h4>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
