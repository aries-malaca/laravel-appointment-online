<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <title>User Login</title>
        @include('layouts.head')
        <link href="../../theme/pages/css/login-2.min.css" rel="stylesheet" type="text/css" />
    </head>
    <!-- END HEAD -->
    <body class=" login">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="../../home">
                <img src="../../logo.png" style="height: 160px;" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content" id="login">
            <!-- BEGIN LOGIN FORM -->
            <div @keypress="listenKey($event)">
                <div class="form-group">
                    <input class="form-control" id="email" type="email" placeholder="Email" v-model="email" />
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" placeholder="Password" v-model="password" /> 
                </div>
                <div class="form-actions">
                    <button @click="login($event)" id="btn-login" data-loading-text="Please wait..." class="btn green btn-block uppercase">Login</button>
                </div>
                <div class="form-actions">
                    <div class="pull-right forget-password-block">
                        <a href="javascript:;" id="forget-password" class="forget-password">Forgot Password?</a>
                    </div>
                </div>
                <div class="login-options">
                    <h4 class="pull-left">Or login with</h4>
                    <ul class="social-icons pull-right">
                        <li>
                            <a class="social-icon-color facebook" data-original-title="facebook" href="javascript:;"></a>
                        </li>
                    </ul>
                </div>
                <div class="create-account">
                    <p>
                        <a href="../../register" class="btn-primary btn" id="register-btn">Create an account</a>
                    </p>
                </div>
            </div>
            <!-- END LOGIN FORM -->
        </div>
        @include('layouts.javascripts')
    </body>
    <script type="text/javascript" src="../../js/jquery-cookie.js"></script>
    <script type="text/javascript" src="../../js/vue.js"></script>
    <script type="text/javascript" src="../../js/helpers.js"></script>
    <script type="text/javascript" src="../../js/login.js"></script>
</html>