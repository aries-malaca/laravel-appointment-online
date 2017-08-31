<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="Preview page of Metronic Admin Theme #4 for statistics, charts, recent events and reports" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="../../theme/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../theme/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../theme/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../theme/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->

        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="../../theme/global/css/components-md.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="../../theme/global/css/plugins-md.min.css" rel="stylesheet" type="text/css" />
        <link href="../../theme/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <link href="../../theme/layouts/layout4/css/layout.min.css" rel="stylesheet" type="text/css" />
        <link href="../../theme/layouts/layout4/css/themes/lbo.css" rel="stylesheet" type="text/css" id="style_color" />
        <link href="../../theme/layouts/layout4/css/custom.css" rel="stylesheet" type="text/css" />
        <!-- END THEME LAYOUT STYLES -->
        <link rel="icon" type="image/png" href="favicon.png">
        <title>Laybare Online</title>
    </head>
    <body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-md">
        <div id="app">

        </div>
    </body>
    <!--[if lt IE 9]>
    <script src="../../theme/global/plugins/respond.min.js"></script>
    <script src="../../theme/global/plugins/excanvas.min.js"></script>
    <script src="../../theme/global/plugins/ie8.fix.min.js"></script>
    <![endif]-->
    <!-- BEGIN CORE PLUGINS -->
    <script src="../../theme/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../../theme/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="../../theme/global/plugins/js.cookie.min.js" type="text/javascript"></script>
    <script src="../../theme/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="../../theme/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="../../theme/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->

    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../../theme/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="../../theme/global/scripts/app.min.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <!-- BEGIN THEME LAYOUT SCRIPTS -->
    <script src="../../theme/layouts/layout4/scripts/layout.min.js" type="text/javascript"></script>
    <script src="../../theme/layouts/layout4/scripts/demo.min.js" type="text/javascript"></script>
    <script src="../../theme/layouts/global/scripts/quick-sidebar.min.js" type="text/javascript"></script>
    <script src="../../theme/layouts/global/scripts/quick-nav.min.js" type="text/javascript"></script>
    <!-- END THEME LAYOUT SCRIPTS -->

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

    <script src="../../app/dist/build.js"></script>
</html>
