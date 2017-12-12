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
    <link href="../../theme/global/plugins/bootstrap-sweetalert/sweetalert.css" rel="stylesheet" type="text/css" />
    <style>
        /* Always set the map height explicitly to define the size of the div
 * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
    </style>
    <link type="text/css" rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500">
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
    <input type="hidden" value="{{ (isset($_GET['fbid'])?$_GET['fbid']:'') }}" id="fbid" />
    <input type="hidden" value="{{ (isset($_GET['accessToken'])?$_GET['accessToken']:'') }}" id="accessToken" />
    <!-- BEGIN LOGIN FORM -->
    <div @change="listenKey($event)">
        <div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">First Name</label>
                <div class="input-icon">
                    <i class="fa fa-font"></i>
                    <input :disabled="newUser.from_facebook" v-model="newUser.first_name" class="form-control placeholder-no-fix" type="text" placeholder="First Name"/>
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
                    <input :disabled="newUser.from_facebook" v-model="newUser.last_name" class="form-control placeholder-no-fix" type="text" placeholder="Last Name"/>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Address</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <textarea class="form-control placeholder-no-fix" rows="3" id="autocomplete" v-model="newUser.user_address" placeholder="Enter your address"
                           onFocus="geolocate()"></textarea>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-6">
                    <label class="control-label visible-ie8 visible-ie9">Gender</label>
                    <select :disabled="newUser.from_facebook" class="form-control" v-model="newUser.gender">
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </div>
                <div class="col-xs-6">
                    <label class="control-label visible-ie8 visible-ie9">Birth Date</label>
                    <input v-model="newUser.birth_date" class="form-control placeholder-no-fix" type="date" />
                </div>
            </div>
            <div class="clearfix"><br></div>
            <div class="row">
                <div class="col-xs-12">
                    <label class="control-label visible-ie8 visible-ie9">Home Branch</label>
                    <select v-model="newUser.home_branch" class="form-control">
                        <option value="0">--SELECT BRANCH--</option>
                        <option v-bind:value="branch.id" v-for="branch in branches">@{{ branch.branch_name }}</option>
                    </select>
                </div>
            </div>
            <div class="clearfix"><br></div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Mobile</label>
                <div class="input-icon">
                    <i class="fa fa-phone"></i>
                    <input class="form-control placeholder-no-fix" type="text" placeholder="Mobile" v-model="newUser.user_mobile"/>
                </div>
            </div>
            <div class="form-group">
                <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                <label class="control-label visible-ie8 visible-ie9">Email</label>
                <div class="input-icon">
                    <i class="fa fa-envelope"></i>
                    <input :disabled="newUser.from_facebook" class="form-control placeholder-no-fix" type="text" placeholder="Email" v-model="newUser.email"/>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <div class="input-icon">
                    <i class="fa fa-lock"></i>
                    <input class="form-control placeholder-no-fix" v-model="newUser.password" type="password" placeholder="Password" />
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                <div class="input-icon">
                    <i class="fa fa-check"></i>
                    <input class="form-control placeholder-no-fix" v-model="newUser.verify_password" type="password" placeholder="Re-type Your Password" />
                </div>
            </div>
            <div class="form-actions">
                <a href="#terms-modal" data-toggle="modal" id="btn-register" class="btn green btn-block uppercase">Register</a>
            </div>
        </div>
        <div class="create-account">
            <p>
                <a href="../../login" class="btn-primary btn" id="register-btn">I have an account</a>
            </p>
        </div>
    </div>
    <!-- END LOGIN FORM -->

    <div class="modal fade" id="terms-modal" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Terms and Conditions</h4>
                </div>
                <div class="modal-body" v-html="terms">

                </div>
                <div class="modal-footer">
                    <button class="btn-success btn green" @click="register($event)"  data-loading-text="Please wait...">I agree and Register</button>
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Close</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</div>
@include('layouts.javascripts')
</body>

<script src="../../theme/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../js/jquery-cookie.js"></script>
<script type="text/javascript" src="../../js/vue.js"></script>
<script type="text/javascript" src="../../js/helpers.js"></script>
<script type="text/javascript" src="../../js/map.js"></script>
<script async defer src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=places&callback=initAutocomplete"></script>
<script type="text/javascript" src="../../js/register.js"></script>
</html>