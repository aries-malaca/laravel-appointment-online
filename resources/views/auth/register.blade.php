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
        .control-label{
            color:white;
        }
        [v-cloak]{
            display:none;
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
    <div>
        <div v-cloak>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">First Name</label>
                        <div class="input-icon">
                            <i class="fa fa-font"></i>
                            <input @change="onBlur" :disabled="newUser.from_facebook || newUser.boss_id !== null" v-model="newUser.first_name"
                                   class="form-control placeholder-no-fix" type="text" placeholder="First Name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Middle Name</label>
                        <div class="input-icon">
                            <i class="fa fa-font"></i>
                            <input @change="onBlur" v-model="newUser.middle_name" :disabled="newUser.boss_id !== null"
                                   class="form-control placeholder-no-fix" type="text" placeholder="Middle Name"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Last Name</label>
                        <div class="input-icon">
                            <i class="fa fa-font"></i>
                            <input @change="onBlur" :disabled="newUser.from_facebook || newUser.boss_id !== null"
                                   v-model="newUser.last_name" class="form-control placeholder-no-fix" type="text" placeholder="Last Name"/>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Mobile</label>
                        <div class="input-icon">
                            <i class="fa fa-phone"></i>
                            <input class="form-control placeholder-no-fix" type="text" placeholder="Mobile" v-model="newUser.user_mobile"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Birth Date</label>
                        <input @change="onBlur" v-model="newUser.birth_date" :disabled="newUser.boss_id !== null"
                               class="form-control placeholder-no-fix" type="date" />
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label">Gender</label>
                        <select :disabled="newUser.from_facebook" class="form-control" v-model="newUser.gender">
                            <option value="female">Female</option>
                            <option value="male">Male</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row" v-if="aj">
                <div class="col-sm-12">
                    <div class="alert alert-success" v-if="newUser.boss_id === null">
                        <span v-if="loading">
                            Searching possible accounts...
                        </span>
                        <span v-else>
                            <div v-if="accounts.length > 0">
                                <i class="fa fa-info-circle"></i>
                                Choose account to help us link your account and transactions.
                                <br/>
                                <table class="table table-condensed">
                                    <thead>
                                        <tr>
                                            <th>Birth Date</th>
                                            <th>Last Branch</th>
                                            <th>Service</th>
                                            <th>Visited</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="account in accounts">
                                            <td>@{{ account.birthdate }}</td>
                                            <td>
                                                <span v-if="account.last_transaction">
                                                    @{{ account.last_transaction.branch }}
                                                </span>
                                                <span v-else>N/A</span>
                                            </td>
                                            <td>
                                                <span v-if="account.last_transaction">
                                                    <span v-if="account.last_transaction.services.length>0">
                                                        @{{ account.last_transaction.services[0].item_name }}
                                                    </span>
                                                </span>
                                                <span v-else>N/A</span>
                                            </td>
                                            <td>
                                                <span v-if="account.last_transaction">
                                                    @{{ account.last_transaction.date }}
                                                </span>
                                                <span v-else>N/A</span>
                                            </td>
                                            <td>
                                                <button type="button" title="Select" @click="chooseAccount(account)" class="btn blue" style="padding: 1px !important;"><i class="fa fa-check"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else>
                                No account found from our BOSS Server, please continue signing up.
                            </div>
                        </span>
                    </div>
                    <div v-else>
                        <div class="row">
                            <div class="col-sm-12">
                                <label class="control-label">Selected Boss ID</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" disabled v-model="newUser.boss_id"/>
                                    <span class="input-group-btn">
                                        <button class="btn blue btn-lg" @click="removeBossID" type="button" title="Clear BOSS ID">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </span>
                                </div>
                                <br/>
                                <div class="alert alert-info">
                                    <small>You can only change your First Name, Last Name and Birthday if you clear your BOSS ID.</small>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"><br></div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <label class="control-label">Home Branch</label>
                    <select v-model="newUser.home_branch" class="form-control">
                        <option value="0">--SELECT BRANCH--</option>
                        <option v-bind:value="branch.id" v-for="branch in branches">@{{ branch.branch_name }}</option>
                    </select>
                </div>
            </div>
            <div class="clearfix"><br></div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Address</label>
                <div class="input-icon">
                    <i class="icon-pointer"></i>
                    <textarea class="form-control placeholder-no-fix" rows="3" id="autocomplete" v-model="newUser.user_address" placeholder="Enter your address"
                              onFocus="geolocate()"></textarea>
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
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Password</label>
                        <div class="input-icon">
                            <i class="fa fa-lock"></i>
                            <input class="form-control placeholder-no-fix" v-model="newUser.password" type="password" placeholder="Password" />
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="control-label visible-ie8 visible-ie9">Re-type Your Password</label>
                        <div class="input-icon">
                            <i class="fa fa-check"></i>
                            <input class="form-control placeholder-no-fix" v-model="newUser.verify_password" type="password" placeholder="Re-type Password" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="g-recaptcha" data-sitekey="6LciXVYUAAAAAIX2r2_jEZMFlsTmwE_CLB_IMcBm"></div><br/><br/>
            <div class="form-actions">
                <div style="margin-bottom: 5px">
                    <a href="#terms-modal" data-toggle="modal"><i class="fa fa-info-circle"></i> Read Terms and Conditions</a>
                </div>
                <a @click="register($event)" class="btn green btn-block uppercase">Register</a>
                <a @click="resetForm" class="btn yellow btn-block uppercase">Reset Form</a>
            </div>
        </div>
        <div class="create-account">
            <p>
                <a href="../../login" class="btn-primary btn">I have an account</a>
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
                    <button class="btn-success btn green" @click="agree">I agree</button>
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
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="../../theme/global/plugins/bootstrap-sweetalert/sweetalert.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../../js/jquery-cookie.js"></script>
<script type="text/javascript" src="../../js/vue.js"></script>
<script type="text/javascript" src="../../js/helpers.js"></script>
<script type="text/javascript" src="../../js/map.js"></script>
<script async defer src="//maps.googleapis.com/maps/api/js?v=3.exp&key=AIzaSyDmPrbBOD0PuleW3DuILWpMwExxoKXeF8k&libraries=places&callback=initAutocomplete"></script>
<script type="text/javascript" src="../../js/register.js"></script>
</html>