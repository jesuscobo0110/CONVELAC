<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- TÍTULO PROFESIONAL -->
    <title>La Pastoreña | Buzón Administrativo de Pago y Tributos</title>

    <!-- FAVICON (ícono que sale en la pestaña y arriba) -->
    <link rel="icon" href="{{ asset('images/Logo Convelac HD1.png') }}" type="image/png">
    <link rel="shortcut icon" href="{{ asset('images/Logo Convelac HD1.png') }}" type="image/png">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/Logo Convelac HD1.png') }}">

    <!-- Fonts (si usas Breeze/Jetstream) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts y estilos -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-sky-300 dark:bg-gray-900 min-h-screen">
    <div class="flex flex-col items-center justify-center min-h-screen">
        {{ $slot }}
    </div>
</body>
</html>