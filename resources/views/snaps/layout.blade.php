<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }}</title>
    <style>
        @font-face {
            font-family: 'SolaimanLipi';
            src: url('{{ asset('fonts/SolaimanLipi.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'kalpurush';
            src: url('{{ asset('fonts/kalpurush.ttf') }}') format('truetype');
        }

        body {
            font-family: 'kalpurush', Arial, sans-serif;
        }
    </style>
</head>
<body>
@yield('content')
</body>
</html>
