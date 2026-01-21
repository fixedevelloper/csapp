<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}} | Gestion de salon</title>
    <link rel="stylesheet" href="{{asset('css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/app.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/icons.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
</head>
<body class="loading" data-layout='{"mode": "dark", "width": "fluid", "menuPosition": "fixed",
"sidebar": { "color": "dark", "size": "default", "showuser": true}, "topbar": {"color": "dark"},
 "showRightSidebarOnPageLoad": true}'>
<!-- Begin page -->

<div id="wrapper">
    @include('_partials._header')
    @include('_partials._siderbard')
    @include("_partials.errors-and-messages")
    @yield('content')
    @include('_partials._footer')
</div>

<script src="{{asset('js/vendor.min.js') }}"></script>
<script src="{{asset('js/databases/jquery.dataTables.min.js') }}"></script>
<script src="{{asset('js/databases/dataTables.bootstrap5.min.js') }}"></script>
<script src="{{asset('js/app.min.js') }}"></script>
<script>
    var configs={
        routes:{
            index: "{{\Illuminate\Support\Facades\URL::to('/')}}",
        }
    }
</script>
<script src="{{asset('js/script.js') }}"></script>
@stack('scripts')
</body>

</html>
