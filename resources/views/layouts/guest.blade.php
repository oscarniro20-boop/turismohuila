<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TurismoHuila') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#FAF7F2] text-stone-800 antialiased">
    @include('components.navbar')

    <div class="d-flex flex-column align-items-center justify-content-center px-3" style="min-height: calc(100vh - 80px);">
        <div class="w-100 px-3 px-sm-0" style="max-width: 420px;">
            {{ $slot }}
        </div>
    </div>
</body>
</html>