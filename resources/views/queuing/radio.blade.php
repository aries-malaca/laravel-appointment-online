<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <link rel="icon" href="../../../../favicon.png">
        <title>Queuing Screen: {{ $branch['branch_name'] }}</title>
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
            [v-cloak] { display:none; }
            #main,body{
                background-color:rgb(177,230,16) !important;
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
        </style>
    </head>
    <body>
        <div id="app" v-cloak>
            <input type="hidden" id="branch_id" value="{{$branch['id']}}">
            <main class="row-fluid">
                <h1 class="center-text brown-text">{{ $branch['branch_name'] }}</h1>

                <div class="row-fluid">
                    <div class="col-md-12">
                        <h1 class="center-text grey-text"> Serving </h1>
                    </div>
                    <div>
                        <div class="serving oval" v-for="item in clients" v-if="item.status==='serving'">
                            <div class="col-xs-7">
                                <span class="name">@{{ item.client_name }}</span>
                            </div>
                            <div class="col-xs-5">
                                <span class="details">
                                    ID: @{{ item.transaction.id }} <br/>
                                    Tech: @{{ item.technician_name }}<br/>
                                    Time: @{{ item.transaction_time }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="col-md-12">
                        <h1 class="center-text pink-text">
                            Upnext
                        </h1>
                    </div>
                    <div>
                        <div class="oval" v-bind:class="item.status" v-if="item.status!=='serving'"
                             v-for="item in clients">
                            <div class="col-xs-7">
                                <span class="name">@{{ item.client_name }}</span>
                            </div>
                            <div class="col-xs-5" >
                                <span class="details">
                                    ID: @{{ item.transaction.id }} <br/>
                                    Tech: @{{ item.technician_name }}<br/>
                                    Time: @{{ item.transaction_time }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- BEGIN CORE PLUGINS -->
        <script src="../../theme/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script src="../../theme/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="../../theme/global/plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="../../theme/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="../../theme/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="../../theme/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
        <!-- END CORE PLUGINS -->

        <script src="https://cdn.socket.io/socket.io-1.2.0.js"></script>
        <script src="https://unpkg.com/vue@2.5.13/dist/vue.js" type="text/javascript"></script>
        <script type="text/javascript" src="https://unpkg.com/axios/dist/axios.min.js"></script>
        <script type="text/javascript" src="../../js/helpers.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../../js/queuing.js"></script>
    </body>
</html>