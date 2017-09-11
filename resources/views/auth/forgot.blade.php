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
    <!-- BEGIN LOGO -->
    <div class="logo">
        <a href="../../home">
            <img src="../../logo.png" style="height: 160px;" alt="" /> </a>
    </div>
    <!-- END LOGO -->
    <!-- BEGIN LOGIN -->
    <div class="content" id="forgot" v-if="token === undefined">
        <!-- BEGIN LOGIN FORM -->
        <div v-if="sent === false">
            <div class="form-group">
                <input class="form-control" type="email" placeholder="Email" v-model="email" />
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label style="font-size:20px;color:white;padding:5px;">Birth Date:</label>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <input class="form-control" type="date" placeholder="Birthday" v-model="birth_date" />
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button @click="requestPassword($event)" id="btn-login" data-loading-text="Please wait..." class="btn green btn-block uppercase">Request New Password</button>
            </div>
            <div class="create-account">
                <div class="row">
                    <div class="col-md-6">
                        <a href="../../register" class="btn-primary btn btn-block">Create an account</a>
                    </div>
                    <div class="col-md-6">
                        <a href="../../login" class="btn-primary btn btn-block">I have an Account</a>
                    </div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="alert alert-success" style="text-align:center">
                <b>Success!</b> Please check your email and close this window.
            </div>
        </div>
        <!-- END LOGIN FORM -->
    </div>
    @include('layouts.javascripts')
    </body>
    <script type="text/javascript" src="../../js/jquery-cookie.js"></script>
    <script type="text/javascript" src="../../js/vue.js"></script>
    <script type="text/javascript" src="../../js/helpers.js"></script>
    <script type="text/javascript" src="../../js/forgot.js"></script>
</html>