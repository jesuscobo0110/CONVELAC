<x-app-layout>
    <div class="min-h-screen bg-sky-50 py-12 px-6">
        <div class="max-w-6xl mx-auto">

            <!-- Header -->
            <div class="flex justify-between items-center mb-10">
                <img src="{{ asset('images/Logo Convelac HD.png') }}" alt="Convelac" class="h-16">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition font-medium">
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

            @if ($comprobantes->isEmpty())
                <div class="text-center py-20">
                    <p class="text-2xl text-gray-500">Aún no hay comprobantes recibidos</p>
                </div>
            @else
                <div class="grid gap-8">
                    @foreach ($comprobantes as $comprobante)
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border-l-8 border-sky-600">
                            <div class="p-8">
                                <!-- Cabecera del comprobante -->
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <h3 class="text-2xl font-bold text-sky-800">{{ $comprobante->codigo_envio }}</h3>
                                        <div class="mt-2 space-y-1 text-gray-600">
                                            <p><span class="font-medium">Tipo:</span> {{ ucfirst($comprobante->tipo) }}</p>
                                            <p><span class="font-medium">Cliente:</span> {{ $comprobante->user->name }}</p>
                                            <p><span class="font-medium">Fecha:</span> {{ \Carbon\Carbon::parse($comprobante->fecha_envio)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <span class="bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-bold">
                                        Recibido
                                    </span>
                                </div>

                                <!-- Archivos adjuntos -->
                                <div class="mt-8">
                                    <h4 class="text-lg font-semibold text-gray-800 mb-4">
                                        Adjuntos ({{ $comprobante->archivos_json ? count($comprobante->archivos_json) : 1 }})
                                    </h4>

                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                                        @php
                                            $archivos = $comprobante->archivos_json ?? [['url' => $comprobante->url_archivo, 'name' => 'comprobante.pdf']];
                                        @endphp

                                        @foreach ($archivos as $archivo)
                                            @php
                                                $url = is_array($archivo) ? $archivo['url'] : $archivo;
                                                $nombre = is_array($archivo) ? ($archivo['name'] ?? basename($url)) : basename($url);
                                                $isPdf = str_ends_with(strtolower($url), '.pdf');
                                            @endphp

                                            <div class="bg-gray-50 rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition">
                                                <div class="h-48 bg-gray-100 flex items-center justify-center">
                                                    @if($isPdf)
                                                        <div class="text-center">
                                                            <svg class="w-20 h-20 text-red-600 mx-auto" fill="currentColor" viewBox="0 0 384 512">
                                                                <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM80 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
                                                            </svg>
                                                            <p class="mt-3 text-sm font-medium text-gray-700">PDF</p>
                                                        </div>
                                                    @else
                                                        <img src="{{ $url }}?w=300&h=200&fit=crop" 
                                                             class="w-full h-full object-cover" 
                                                             alt="{{ $nombre }}">
                                                    @endif
                                                </div>

                                                <div class="p-4 bg-white">
                                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $nombre }}</p>
                                                    <div class="mt-3 flex gap-3">
                                                        <a href="{{ $url }}" target="_blank"
                                                           class="flex-1 text-center py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium text-sm">
                                                            Ver
                                                        </a>
                                                        <a href="{{ $url }}" download="{{ $nombre }}"
                                                           class="flex-1 text-center py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm">
                                                            Descargar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-12 flex justify-center">
                    {{ $comprobantes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>