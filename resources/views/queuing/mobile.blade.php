<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.png">
        <title>Queuing Screen: </title>
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../../../../theme/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../../../theme/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../../../theme/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../../../theme/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <style type="text/css">
            body{
                color:white;
            }
            /*[v-cloak] { display:none; }*/
            #main,body{
                background-color:#e7e2e2 !important;
            }
            #main{
                min-height:700px;
            }
            .center-text{
                text-align: center;
            }
            .brown-text{
                color:rgb(132,88,41);
            }
            .white-text, .name, .details{
                color:white;
            }
            .name{
                font-size:30px;
                display:block;
                text-align:center;
            }
            .pending{
                background-color:rgb(96,57,18);
            }
            .calling{
                background-color:#bd6dac;
            }
            .serving{
                background-color:rgb(255,174,220);
            }
            .oval{
                display:block;
                border-radius:40px !important;
                height:65px;
                padding:10px 5px;
                margin:10px 0px;
            }
            .details{
                font-size:11px
            }
            @font-face {
                font-family: 'Century Gothic';
                font-style: bold;
                font-weight: 1500;
                src: local('Century Gothic'), local('CenturyGothic'), url(https://fonts.gstatic.com/l/font?kit=Yeh3y7ciGQhij8XzUSI-wDLItwS-qy_1vxH5V4_Yisk&skey=93c2fdf69b410576&v=v7) format('woff2');
                unicode-range: U+0100-024F, U+1E00-1EFF, U+20A0-20AB, U+20AD-20CF, U+2C60-2C7F, U+A720-A7FF;
            }
            /* latin */
            @font-face {
                font-family: 'Century Gothic';
                font-style: bold;
                font-weight: 1500;
                src: local('Century Gothic'), local('CenturyGothic'), url(https://fonts.gstatic.com/l/font?kit=Yeh3y7ciGQhij8XzUSI-wORYAdd4IhfhHjBEc43Trp8&skey=93c2fdf69b410576&v=v7) format('woff2');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
            }
            @font-face {
                font-family: 'Vladimir';
                font-style: italic;
                font-weight: 700;
                src: local('Vladimir Italic'),local('Vladimir Bold'), url("../fonts/vladimir.tff") format('tff');
                unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
            }
            .thumbnail {
                padding:0;
                border-radius: 10px;
            }
            .lblTransactionID{
                font-size: 22px;
                font-style: bold; 
                margin-top: 20%;
                text-align: center;
            }
        </style>
    </head>
    <body>
    <div id="app" v-cloak>
        <input type="hidden" id="branch_id" value="{{$branch['id']}}">
        <main class="row-fluid">
            <h1 class="center-text brown-text">Branch: Aguirre</h1>

            <div class="container">
                <div class="row-fluid">
                   <div class="row">

                         <div class="col-md-12">
                            <div class="thumbnail">
                                <!-- <img src="http://placehold.it/320x125/EEE" class=""> -->
                                <div class="caption">
                                     <h5 class="">Lay Bare Queuing System</h5>
                                            <div class="container ">
                                                <center>
                                                    <h4>Your Transaction ID is</h4>
                                                    <h3>0341</h3>
                                                </center>
                                            </div>
                                      
                                        <!-- <p class="">
                                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur,
                                             culpa itaque odio similique suscipit
                                        </p>
                                        <a href="#" class="btn btn-default btn-xs pull-right" role="button">
                                            <i class="glyphicon glyphicon-edit"></i>
                                        </a>  
                                        <a href="#" class="btn btn-default btn-xs" role="button">More Info</a> -->

                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="thumbnail">
                                <!-- <img src="http://placehold.it/320x125/EEE" class=""> -->
                                <div class="caption">
                                     <h4 class="">Now Serving: </h4>
                                    <table class="thumbnail container table table-bordered table-stripped table-condensed" >
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p class="lblTransactionID">002</p>
                                                </td>
                                                 <td style="width: 100%;">
                                                    <p class="">Mark</p>
                                                    <p class="">
                                                        <b>Technician: </b>
                                                        Michael Aries Malaca
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="lblTransactionID">00321</p>
                                                </td>
                                                 <td>
                                                    <p class="">Sherwin</p>
                                                    <p class="">
                                                        <b>Technician: </b>
                                                        Michael Aries Malaca
                                                    </p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="thumbnail">
                                <!-- <img src="http://placehold.it/320x125/EEE" class=""> -->
                                <div class="caption">
                                    <h4 class="">Up Next: </h4>
                                    <table class="thumbnail container table table-bordered table-stripped table-condensed" >
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <p class="lblTransactionID">1034</p>
                                                </td>
                                                 <td style="width: 100%;">
                                                    <p class="">Buboy</p>
                                                    <p class="">
                                                        <b>Technician: </b>
                                                        Sample Girl1
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p class="lblTransactionID">002491235</p>
                                                </td>
                                                 <td>
                                                    <p class="">Dale</p>
                                                    <p class="">
                                                        <b>Technician: </b>
                                                        Sample Girl2
                                                    </p>
                                                </td>
                                            </tr>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                       



                    </div>
                    </div>
                </div>
        </div>

                


            </main>
        </div>

        <!-- BEGIN CORE PLUGINS -->
        
     <!--    <script type="text/javascript" src="../../../js/queuing.js"></script> -->
    </body>
</html>