<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Convelac') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- NUEVO HEADER LIMPIO Y PROFESIONAL -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <div class="flex items-center">
                <img src="{{ asset('images/Logo Convelac HD.png') }}" alt="Convelac" class="h-12">
                <!-- <span class="ml-4 text-2xl font-bold text-sky-800">Convelac</span>-->
            </div>
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition font-medium">
                        Cerrar sesión
                    </button>
                </form>
            @endauth
        </div>
    </header>

    <main class="py-8">
        {{ $slot }}
    </main>

</body>
</html>