<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME') }}</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="/login_asset/images/icons/favicon.ico"/>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="/login_asset/css/util.css">
    <link rel="stylesheet" type="text/css" href="/login_asset/css/main.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    @stack("css")
</head>
<body>

@yield("content")


<div id="dropDownSelect1"></div>

<!--===============================================================================================-->
<script src="/login_asset/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/vendor/bootstrap/js/popper.js"></script>
<script src="/login_asset/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/vendor/daterangepicker/moment.min.js"></script>
<script src="/login_asset/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
<script src="/login_asset/js/main.js"></script>
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    var notify = new Notyf({
        position: {
            x: 'right',
            y: 'top',
        },
        types: [
            {
                type: 'info',
                background: '#74edea',
                icon: {
                    className: 'zmdi zmdi-info text-white',
                    tagName: 'i'
                }
            },
            {
                type: 'warning',
                background: '#edc02c',
                icon: {
                    className: 'zmdi zmdi-alert-triangle text-white',
                    tagName: 'i'
                }
            }
        ]
    });
</script>
@stack("js")

</body>
</html>
