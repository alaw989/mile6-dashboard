<!-- resources/views/app.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mile 6 Dashboard</title>
    @vite('resources/css/app.css')
    @inertiaHead
</head>
    <body>
    @inertia
    @vite('resources/js/app.js')
    </body>
</html>
