<!doctype html>
<html lang="{{ app()->getLocale() }}" class="default-style layout-fixed layout-navbar-fixed">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/png" href="{{ asset('images/Favicon-Sauce-512-px.png') }}"/>

        <title>{{ config('app.name', 'Laravel') }}</title>
   
        <!-- Main font -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

        <!-- Promises -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.4/bluebird.min.js"></script>


    </head>
    <body>
        <div class="background-login"></div>
    <div id="app">
        <password-reset-helper
            cancel-url="{{route('login')}}"
            password-reset-action="{{ action('Auth\ForgotPasswordController@showLinkRequestForm') }}"
        >
        </password-reset-helper>
        <footerlogin></footerlogin>
        <notifications group="auth"/>
        
    </div>
    <script src="{{ mix('/auth.js') }}"></script>
    </body>
</html>
