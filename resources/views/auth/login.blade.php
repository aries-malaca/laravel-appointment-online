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
            <div>
                <div class="form-group">
                    <input class="form-control" type="email" placeholder="Email" v-model="email" /> 
                </div>
                <div class="form-group">
                    <input class="form-control" type="password" placeholder="Password" v-model="password" /> 
                </div>
                <div class="form-actions">
                    <button @click="login($event)" data-loading-text="Please wait..." class="btn green btn-block uppercase">Login</button>
                </div>
                <div class="form-actions">
                    <div class="pull-left">
                        <label class="rememberme mt-checkbox mt-checkbox-outline">
                            <input type="checkbox" v-model="remember"> Remember me
                            <span></span>
                        </label>
                    </div>
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
    <script type="text/javascript">
        /*!
         * jQuery Cookie Plugin v1.4.1
         * https://github.com/carhartl/jquery-cookie
         *
         * Copyright 2006, 2014 Klaus Hartl
         * Released under the MIT license
         */
        (function (factory) {
            if (typeof define === 'function' && define.amd) {
                // AMD (Register as an anonymous module)
                define(['jquery'], factory);
            } else if (typeof exports === 'object') {
                // Node/CommonJS
                module.exports = factory(require('jquery'));
            } else {
                // Browser globals
                factory(jQuery);
            }
        }(function ($) {

            var pluses = /\+/g;

            function encode(s) {
                return config.raw ? s : encodeURIComponent(s);
            }

            function decode(s) {
                return config.raw ? s : decodeURIComponent(s);
            }

            function stringifyCookieValue(value) {
                return encode(config.json ? JSON.stringify(value) : String(value));
            }

            function parseCookieValue(s) {
                if (s.indexOf('"') === 0) {
                    // This is a quoted cookie as according to RFC2068, unescape...
                    s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
                }

                try {
                    // Replace server-side written pluses with spaces.
                    // If we can't decode the cookie, ignore it, it's unusable.
                    // If we can't parse the cookie, ignore it, it's unusable.
                    s = decodeURIComponent(s.replace(pluses, ' '));
                    return config.json ? JSON.parse(s) : s;
                } catch(e) {}
            }

            function read(s, converter) {
                var value = config.raw ? s : parseCookieValue(s);
                return $.isFunction(converter) ? converter(value) : value;
            }

            var config = $.cookie = function (key, value, options) {

                // Write

                if (arguments.length > 1 && !$.isFunction(value)) {
                    options = $.extend({}, config.defaults, options);

                    if (typeof options.expires === 'number') {
                        var days = options.expires, t = options.expires = new Date();
                        t.setMilliseconds(t.getMilliseconds() + days * 864e+5);
                    }

                    return (document.cookie = [
                        encode(key), '=', stringifyCookieValue(value),
                        options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                        options.path    ? '; path=' + options.path : '',
                        options.domain  ? '; domain=' + options.domain : '',
                        options.secure  ? '; secure' : ''
                    ].join(''));
                }

                // Read

                var result = key ? undefined : {},
                    // To prevent the for loop in the first place assign an empty array
                    // in case there are no cookies at all. Also prevents odd result when
                    // calling $.cookie().
                    cookies = document.cookie ? document.cookie.split('; ') : [],
                    i = 0,
                    l = cookies.length;

                for (; i < l; i++) {
                    var parts = cookies[i].split('='),
                        name = decode(parts.shift()),
                        cookie = parts.join('=');

                    if (key === name) {
                        // If second argument (value) is a function it's a converter...
                        result = read(cookie, value);
                        break;
                    }

                    // Prevent storing a cookie that we couldn't decode.
                    if (!key && (cookie = read(cookie)) !== undefined) {
                        result[name] = cookie;
                    }
                }

                return result;
            };

            config.defaults = {};

            $.removeCookie = function (key, options) {
                // Must not alter options, thus extending a fresh object...
                $.cookie(key, '', $.extend({}, options, { expires: -1 }));
                return !$.cookie(key);
            };

        }));

    </script>
    <script type="text/javascript" src="../../vue.js"></script>
    <script type="text/javascript" src="../../vuejs/login.js"></script>
</html>