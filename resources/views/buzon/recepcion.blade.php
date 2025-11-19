<x-app-layout>
    <div class="min-h-screen bg-sky-50 py-12 px-6">
        <div class="max-w-6xl mx-auto">

            <!-- Header con logout pequeño arriba derecha -->
            <div class="flex justify-between items-center mb-10">
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
                Aquí verás los comprobantes de tus clientes.
            </p>

<!-- Lista de comprobantes -->
@if ($comprobantes->isEmpty())
    <p class="text-center text-gray-500 text-xl">
        Aún no hay comprobantes recibidos
    </p>
@else
    <table class="w-full table-auto bg-white rounded-lg shadow-md">
        <thead>
            <tr class="bg-sky-200 text-left text-sky-800">
                <th class="px-4 py-2">Código</th>
                <th class="px-4 py-2">Tipo</th>
                <th class="px-4 py-2">Empresa</th>
                <th class="px-4 py-2">Fecha</th>
                <th class="px-4 py-2">Archivo</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comprobantes as $comprobante)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $comprobante->codigo_envio }}</td>
                    <td class="px-4 py-2">{{ ucfirst($comprobante->tipo) }}</td>
                    <td class="px-4 py-2">{{ $comprobante->user->name }}</td>
                    <td class="px-4 py-2">{{ $comprobante->fecha_envio }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ $comprobante->url_archivo }}" target="_blank" class="text-sky-600 hover:underline">
                            Ver/Download
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif
        </div>
    </div>
</x-app-layout>