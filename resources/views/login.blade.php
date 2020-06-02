<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{config('admin.title')}} | {{ trans('admin.login') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/bootstrap/css/bootstrap.min.css") }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/font-awesome/css/font-awesome.min.css") }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ admin_asset("vendor/laravel-admin/AdminLTE/dist/css/AdminLTE.min.css") }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>

<body class="hold-transition login-page" @if(config('admin.login_background_image'))style="background: url({{config('admin.login_background_image')}}) no-repeat;background-size: cover;"@endif>

    <div class="login-box" id="login-box-oauth" style="min-width: 260px; width: auto; opacity: 0.8; display: {{ config('admin-oauth.allowed_password_login') ? 'none' : 'block' }}">

        <div class="login-logo">
            <b style="color:white; font-size: 20px; ">{{config('admin.name')}}</b>
        </div>

        <div class="row">
            @foreach($sources as $source => $sourceName)
                <div class="col-xs-12" style="margin-bottom: 10px;">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ admin_url('/oauth/authorize?source=' . $source) }}" class="btn btn-primary btn-block btn-flat">{{ $sourceName }}授权登录</a>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
