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
    <!-- BEGIN LOGIN FORM -->
    <div @keypress="listenKey($event)">
        <div class="form-group">
            <input class="form-control" id="email" type="email" placeholder="Email" v-model="email" />
        </div>
        <div class="form-group">
            <input class="form-control" type="password" placeholder="Password" v-model="password" />
        </div>
        <div class="form-actions">
            <button @click="login($event)" id="btn-login" data-loading-text="Please wait..." class="btn green btn-block uppercase">Register</button>
        </div>

        <div class="login-options" style="padding-bottom: 20px; padding-left:18%">
            <fb:login-button
                    scope="public_profile,email"
                    onlogin="checkLoginState();"
                    data-button-type="continue_with"
                    data-size="large">
            </fb:login-button>
        </div>
        <div class="create-account">
            <p>
                <a href="../../login" class="btn-primary btn" id="register-btn">I have an account</a>
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
<script type="text/javascript" src="../../js/register.js"></script>
</html>