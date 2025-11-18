<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzón de Recepción - Convelac</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-sky-50 min-h-screen">
    <div class="max-w-6xl mx-auto p-6">

        <!-- Header + Cerrar sesión pequeño arriba derecha -->
        <div class="flex justify-between items-center mb-10 mt-6">
            <img src="{{ asset('images/Logo Convelac HD.png') }}" alt="Convelac" class="h-16">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">
                    Cerrar sesión
                </button>
            </form>
        </div>

        <h1 class="text-4xl font-bold text-sky-800 text-center mb-4">
            Buzón de Recepción
        </h1>
        <p class="text-center text-gray-600 mb-12 text-lg">
            Aquí aparecerán todos los comprobantes que te envíen tus clientes
        </p>

        <!-- Área donde caerán los comprobantes (futuro) -->
        <div class="bg-white rounded-2xl shadow-xl p-10 min-h-96 border-4 border-dashed border-sky-300">
            <p class="text-center text-gray-500 text-xl">
                Aún no hay comprobantes recibidos
            </p>
        </div>
    </div>
</body>
</html>