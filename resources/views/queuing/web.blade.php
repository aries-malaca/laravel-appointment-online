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
        <link rel="stylesheet" href="../../../css/queuing.web.css" type="text/css" />
    </head>
    <body>
        <div id="app" v-cloak>
            <input type="hidden" id="branch_id" value="{{$branch['id']}}">
            <header class="header clearfix">
                <nav>
                    <ul class="nav nav-pills float-right">
                        <li class="nav-item">
                            <h3>Serving:</h3>
                        </li>
                        <li class="nav-item" v-for="item in clients" v-if="item.status==='serving'">
                            <span class="serving bold">@{{ item.transaction.id }}</span>
                        </li>
                    </ul>
                </nav>
            </header>
            <main class="row-fluid">
                <div class="col-lg-3">
                    <h1 class="brown-text bold"> {{ $branch['branch_name'] }}</h1>
                    <div class="item" v-bind:class="item.status" v-if="item.status!=='serving'"
                            v-for="item in clients">
                        <div class="row">
                            <div class="col-sm-6">
                                <h2 class="bold uppercase">@{{ item.client_name }}</h2>
                            </div>
                            <div class="col-sm-6 bold">
                                ID: @{{ item.transaction.id }} <br/>
                                Tech: @{{ item.technician_name }}<br/>
                                Time: @{{ item.transaction_time }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div id="slider">
                        <transition name="bounce">
                            <img v-if="n===current_image" v-for="n in limit" :src="'../../../images/queuing/image' + n +'.jpg'" style="height:560px" />
                        </transition>
                    </div>
                </div>
            </main>
            <footer>
                <div class="row">
                    <div class="col-lg-9">
                        <h3>
                            www.lay-bare.com 0917 LAYBARE Find us on
                        </h3>
                    </div>
                    <div class="col-lg-3">
                        <h3 class="pull-right">@{{ current_time }}</h3>
                    </div>
                </div>
            </footer>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js" type="text/javascript"></script>
        <script type="text/javascript" src="../../../js/queuing.js"></script>
    </body>
</html>