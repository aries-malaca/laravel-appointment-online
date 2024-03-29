<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <title>Verify Email</title>
    @include('layouts.head')
    <link href="../../../../theme/pages/css/login-2.min.css" rel="stylesheet" type="text/css" />
</head>
<!-- END HEAD -->
<body class="login">
<!-- BEGIN LOGO -->
<div class="logo">
    <a href="../../../../home">
        <img src="../../../../logo.png" style="height: 160px;" alt="" /> </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    @if($result == 'success')
        <div class="alert alert-success" style="text-align:center">
            <b>Success!</b> Your email address has been verified.
        </div>
        <a href="../../login" class="btn-primary btn btn-block" id="register-btn">Okay</a>
    @else
        <div class="alert alert-danger" style="text-align:center">
            <b>Failed!</b> {{ $error }}
        </div>
        <a href="../../login" class="btn-primary btn btn-block" id="register-btn">Okay</a>
    @endif
</div>
</body>
</html>