<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buzón de Envío - Convelac</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-sky-100 to-blue-100 min-h-screen">
    <div class="max-w-4xl mx-auto p-8">

        <div class="flex justify-between items-center mb-10">
            <img src="{{ asset('images/Logo Convelac HD.png') }}" alt="Convelac" class="h-14">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-red-600 hover:text-red-800 underline">
                    Cerrar sesión
                </button>
            </form>
        </div>

        <h1 class="text-4xl font-bold text-sky-900 text-center mb-6">
            ¡Hola, {{ auth()->user()->name }}!
        </h1>
        <p class="text-center text-xl text-gray-700 mb-12">
            Este es tu Buzón de Envío
        </p>

        <div class="bg-white rounded-2xl shadow-2xl p-12 text-center border-4 border-dashed border-green-300">
            <svg class="w-24 h-24 mx-auto text-green-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
            </svg>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Sube tu comprobante aquí</h2>
            <p class="text-gray-600">Pronto podrás arrastrar y soltar XML + PDF</p>
        </div>
    </div>
</body>
</html>