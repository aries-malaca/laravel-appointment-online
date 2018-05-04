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
    <body class="login">
    <noscript>
        <div class="alert alert-warning"><strong>Javascript is Disabled</strong>
            <br/>
            Go to your browser's settings to fix this and refresh the page.
        </div>
    </noscript>

    <div id="fb-root"></div>
    <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.10&appId=1899966743571465";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
    </script>

        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="../../home">
                <img src="../../logo.png" style="height: 160px;" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content" id="login" v-if="token === undefined">
            @if(isset($_GET['message']))
            <div class="alert alert-success">
                Successfully registered, Please login.
            </div>
            @endif
            <input type="hidden" value="{{ (isset($_GET['email'])?$_GET['email']:'') }}" id="email" />
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
                        <a href="../../forgot" id="forget-password" class="forget-password">Forgot Password?</a>
                    </div>
                </div>
                <div class="login-options" style="padding-bottom: 20px; padding-left:18%">
                    <fb:login-button
                            scope="public_profile,email"
                            onlogin="checkLoginState(); checkLoginState();"
                            data-button-type="continue_with"
                            data-size="large">
                    </fb:login-button>
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