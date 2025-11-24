<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-sky-50 to-sky-100 py-12 px-6">
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

            <h1 class="text-4xl font-bold text-sky-800 text-center mb-4">Buzón de Recepción</h1>
            <p class="text-center text-lg text-gray-600 mb-12">Aquí verás los comprobantes de tus clientes.</p>

            @if(session('success'))
                <div class="mb-8 p-6 bg-green-100 border-2 border-green-400 rounded-2xl text-center">
                    <p class="text-green-800 font-bold text-xl">{{ session('success') }}</p>
                </div>
            @endif

            @if($comprobantes->isEmpty())
                <p class="text-center text-gray-500 text-xl py-20">Aún no hay comprobantes recibidos</p>
            @else
                <div class="space-y-10">
                    @foreach($comprobantes as $comprobante)
                        @php
                            $archivos = is_array($comprobante->archivos_json)
                                ? $comprobante->archivos_json
                                : (is_string($comprobante->archivos_json) ? json_decode($comprobante->archivos_json, true) : []);

                            $vistos = is_array($comprobante->archivos_vistos)
                                ? $comprobante->archivos_vistos
                                : (is_string($comprobante->archivos_vistos) ? json_decode($comprobante->archivos_vistos, true) : []);

                            // Soporte para comprobantes antiguos
                            if (empty($archivos) && $comprobante->url_archivo) {
                                $archivos = [[
                                    'url' => $comprobante->url_archivo,
                                    'name' => 'comprobante.pdf',
                                    'public_id' => 'legacy_' . $comprobante->id
                                ]];
                            }
                        @endphp

                        <div class="bg-white rounded-3xl shadow-2xl p-10 border-l-8 border-sky-600">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h3 class="text-2xl font-bold text-sky-800">{{ $comprobante->codigo_envio }}</h3>
                                    <p class="text-lg text-gray-700"><strong>Cliente:</strong> {{ $comprobante->user->name }}</p>
                                    <p class="text-lg text-gray-700"><strong>Tipo:</strong> {{ ucfirst($comprobante->tipo) }}</p>
                                    <p class="text-lg text-gray-700"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($comprobante->fecha_envio)->format('d/m/Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-block bg-green-100 text-green-800 px-6 py-3 rounded-full text-xl font-bold">Recibido</span>

                                    @if(count($vistos) < count($archivos))
                                        <div id="aviso-nuevos-{{ $comprobante->id }}" class="mt-3">
                                            <span class="inline-block bg-red-600 text-white px-6 py-3 rounded-full text-lg font-bold animate-pulse">
                                                HAY ARCHIVOS NUEVOS
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-8">
                                <h4 class="text-xl font-bold text-gray-800 mb-6">Adjuntos ({{ count($archivos) }})</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                                    @foreach($archivos as $archivo)
                                        @php
                                            $nombre    = $archivo['name'] ?? 'archivo.pdf';
                                            $publicId  = $archivo['public_id'] ?? 'legacy_' . $comprobante->id;
                                            $url       = $archivo['url'] ?? $comprobante->url_archivo;
                                            $esVisto   = in_array($publicId, $vistos);
                                            $esPdf     = str_ends_with(strtolower($nombre), '.pdf');
                                        @endphp

                                        <div class="bg-gray-50 rounded-2xl p-8 border-4 {{ $esVisto ? 'border-gray-300' : 'border-sky-500 shadow-xl' }} hover:shadow-2xl transition">
                                            <div class="text-center mb-6">
                                                @if($esPdf)
                                                    <svg class="w-28 h-28 mx-auto text-red-600" fill="currentColor" viewBox="0 0 384 512">
                                                        <path d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM80 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16zm0 64h64c8.8 0 16 7.2 16 16s-7.2 16-16 16H80c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
                                                    </svg>
                                                    <p class="text-2xl font-bold text-gray-700 mt-3">PDF</p>
                                                @else
                                                    <img src="{{ $url }}?w=400&h=300&fit=crop" class="rounded-xl shadow-lg mx-auto max-h-48" alt="{{ $nombre }}">
                                                @endif
                                            </div>

                                            <p class="text-center font-bold text-gray-800 text-lg mb-6 truncate px-4">{{ $nombre }}</p>

                                            <!-- BOTÓN VER ARCHIVO + MARCAR COMO VISTO AL INSTANTE -->
                                            <div class="mt-4">
                                                <a href="{{ $url }}"
                                                   target="_blank"
                                                   onclick="event.preventDefault();
                                                            if(!{{ $esVisto ? 'true' : 'false' }}) {
                                                                marcarComoVisto({{ $comprobante->id }}, '{{ $publicId }}', this);
                                                            }
                                                            window.open('{{ $url }}', '_blank');"
                                                   class="block w-full text-center py-5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition font-bold text-xl shadow-lg">
                                                    VER ARCHIVO
                                                </a>
                                            </div>

                                            @if(!$esVisto)
                                                <div class="mt-4 text-center badge-nuevo">
                                                    <span class="inline-block bg-red-600 text-white px-6 py-3 rounded-full text-lg font-bold animate-pulse">
                                                        NUEVO
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    {{ $comprobantes->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- SCRIPTS PARA MARCAR COMO VISTO AL INSTANTE -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        function marcarComoVisto(comprobanteId, publicId, elemento) {
            // Quitar estilos de "nuevo" al instante
            const card = elemento.closest('.border-sky-500') || elemento.closest('.border-gray-300');
            if (card) {
                card.classList.remove('border-sky-500', 'shadow-xl');
                card.classList.add('border-gray-300');
            }
            elemento.closest('.bg-gray-50').querySelector('.badge-nuevo')?.remove();

            // Enviar al servidor
            axios.post(`/comprobante/${comprobanteId}/${publicId}/marcar-visto`)
                .then(() => {
                    const avisoGrande = document.getElementById(`aviso-nuevos-${comprobanteId}`);
                    if (avisoGrande && avisoGrande.parentElement.querySelectorAll('.badge-nuevo').length === 0) {
                        avisoGrande.remove();
                    }
                })
                .catch(() => console.error('Error al marcar como visto'));
        }
    </script>
</x-app-layout>