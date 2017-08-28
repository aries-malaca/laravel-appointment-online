<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <title>User Registration</title>
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
        <!-- BEGIN LOGIN FORM -->
        <div class="content" id="registration">
            
        </div>
        <!-- END LOGIN FORM -->
        @include('layouts.javascripts')
    </body>
    <script type="text/javascript" src="../../vue.js"></script>
    <script type="text/javascript" src="../../vuejs/login.js"></script>
</html>