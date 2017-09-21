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
<div class="content" id="register" v-if="token === undefined">
    <input type="hidden" value="{{ (isset($_GET['email'])?$_GET['email']:'') }}" id="email" />
    <input type="hidden" value="{{ (isset($_GET['first_name'])?$_GET['first_name']:'') }}" id="first_name" />
    <input type="hidden" value="{{ (isset($_GET['middle_name'])?$_GET['middle_name']:'') }}" id="middle_name" />
    <input type="hidden" value="{{ (isset($_GET['last_name'])?$_GET['last_name']:'') }}" id="last_name" />
    <!-- BEGIN LOGIN FORM -->
    <div @keypress="listenKey($event)">
        <div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">First Name</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input v-model="newUser.first_name" class="form-control placeholder-no-fix" type="text" placeholder="First Name"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Middle Name</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input v-model="newUser.middle_name" class="form-control placeholder-no-fix" type="text" placeholder="Middle Name"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Last Name</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input v-model="newUser.last_name" class="form-control placeholder-no-fix" type="text" placeholder="Last Name"/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Address</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input class="form-control placeholder-no-fix" type="text" placeholder="Address" />
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">

                </div>
                <div class="col-xs-6">

                </div>
            </div>

            <div class="form-group">
                <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                <label class="control-label visible-ie8 visible-ie9">Email</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input class="form-control placeholder-no-fix" type="text" placeholder="Email" v-model="newUser.email"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input class="form-control placeholder-no-fix" type="password" placeholder="Password" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                <div class="controls">
                    <div class="input-icon">
                        <i class="fa fa-check"></i>
                        <input class="form-control placeholder-no-fix" type="password" placeholder="Re-type Your Password" />
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="mt-checkbox mt-checkbox-outline">
                    <input type="checkbox" v-model="newUser.agree"> I agree to the
                    <a href="javascript:;">Terms & Conditions </a>
                    <span></span>
                </label>
            </div>
            <div class="form-actions">
                <button @click="register($event)" v-bind:disabled="!newUser.agree" id="btn-register" data-loading-text="Please wait..." class="btn green btn-block uppercase">Register</button>
            </div>
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