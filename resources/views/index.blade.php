<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="pragma" content="no-cache">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">

    <title>Laravel</title>

    <link rel="stylesheet" href="{{ mix('/assets/css/app.css') }}">

    <script src="{{ mix('/assets/js/app.js') }}" defer></script>
</head>
<body>
    <div id="app">
        <index/>
    </div>
</body>
</html>
