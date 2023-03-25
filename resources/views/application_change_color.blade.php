<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}" class="default-style layout-fixed layout-navbar-fixed">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge,chrome=1">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/Favicon-Sauce-512-px.png') }}"/>
    
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Main font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900" rel="stylesheet">

    <!-- Icons. Uncomment required icon fonts -->


    <!-- Promises -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.4/bluebird.min.js"></script>

    <!-- Layout helpers -->
    <script src="{{ mix('/vendor/js/layout-helpers.js') }}"></script>
    
    
<script>
    var asd = 1;

    if (asd == 2)
    {
        console.log("entro1")
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.id   = "TEMA";
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = "{{ mix('/app.css') }}";
        link.media = 'all';
        head.appendChild(link);
    }
    else
    {
        console.log("entro2")
        var head  = document.getElementsByTagName('head')[0];
        var link  = document.createElement('link');
        link.id   = "TEMA2";
        link.rel  = 'stylesheet';
        link.type = 'text/css';
        link.href = '/app_air.css';
        link.media = 'all';
        head.appendChild(link);
    }
    
    console.log("dsdsd")
</script>
    <script src="https://d3js.org/d3.v4.min.js"></script>

</head>
<body>
    <div id="app">
        <div class="background-system"></div>
        <router-view/>
    </div>

    <script>
        var authGlobal = @json($viewService->allAuthData());

        var keywords = @json($viewService->getKeywords());

        var locationForm = @json(Auth::user()->getLocationForm());
    </script>
    
    @if (false)
        <script src="{{ mix('/app.js') }}"></script>
    @else
        <script src="/app_air.js"></script>
    @endif

</body>
</html>
