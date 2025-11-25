<x-app-layout>
    <!-- FONDO -->
    <div class="min-h-screen bg-cover bg-center bg-fixed"
         style="background-image: url('{{ asset('images/fondo-convelac.jpg') }}');">
        <div class="min-h-screen bg-black bg-opacity-60 backdrop-blur-md">

            <div class="max-w-7xl mx-auto px-6 pt-20 pb-12">

                <h1 class="text-6xl font-black text-white text-center mb-6 drop-shadow-2xl">
                    Buzón de Recepción
                </h1>
                <p class="text-2xl text-gray-200 text-center mb-20 drop-shadow-lg">
                    Aquí verás los comprobantes de tus clientes.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    @forelse($comprobantes as $comprobante)
                        @php
                            $archivos = json_decode($comprobante->archivos_json, true);
                            $vistos = $comprobante->archivos_vistos ? json_decode($comprobante->archivos_vistos, true) : [];
                        @endphp

                        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">
                            <div class="bg-gradient-to-br from-cyan-600 to-blue-800 p-6 text-white">
                                <h3 class="text-2xl font-bold">{{ $comprobante->codigo_envio }}</h3>
                                <p class="mt-2">Cliente: {{ $comprobante->user->name }}</p>
                                <p>Tipo: {{ $comprobante->tipo === 'retencion' ? 'Retención' : 'Pago' }}</p>
                                <p>Fecha: {{ \Carbon\Carbon::parse($comprobante->fecha_envio)->format('d/m/Y') }}</p>
                            </div>

                            <div class="p-8">
                                <p class="text-center font-bold text-gray-700 mb-6 text-lg">
                                    Adjuntos ({{ count($archivos) }})
                                </p>

                                <div class="grid grid-cols-2 gap-5">
                                    @foreach($archivos as $archivo)
                                        @php
                                            $publicId = $archivo['public_id']; // incluye comprobantes/
                                            $visto = in_array($publicId, $vistos);
                                        @endphp

                                        <div class="bg-gray-50 rounded-xl p-4 text-center border-4 transition-all duration-300
                                            {{ $visto ? 'border-green-500' : 'border-red-500' }}"
                                            id="file-{{ $comprobante->id }}-{{ $publicId }}">

                                            <div class="text-4xl mb-2">
                                                <span class="text-red-600 font-bold">PDF</span>
                                            </div>

                                            <p class="text-xs text-gray-600 truncate mb-3">
                                                {{ Str::limit($archivo['name'], 20) }}
                                            </p>

                                            <button type="button"
                                                    onclick="verYMarcar('{{ $archivo['url'] }}', {{ $comprobante->id }}, '{{ $publicId }}')"
                                                    class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-sm transition shadow">
                                                VER ARCHIVO
                                            </button>

                                            <p class="mt-3 text-sm font-bold {{ $visto ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $visto ? 'Visto' : 'Nuevo' }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-32">
                            <p class="text-5xl font-bold text-white drop-shadow-2xl">No hay comprobantes aún</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- SCRIPT QUE FUNCIONA AL 100% -->
    <script>
        function verYMarcar(url, comprobanteId, publicId) {
            // Abrir archivo
            window.open(url, '_blank');

            const caja = document.getElementById('file-' + comprobanteId + '-' + publicId);
            if (!caja || caja.classList.contains('border-green-500')) return;

            // Enviar AJAX
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');

            fetch(`/comprobante/${comprobanteId}/${publicId}/marcar-visto`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(() => {
                caja.classList.remove('border-red-500');
                caja.classList.add('border-green-500');
                caja.querySelector('p:last-child').textContent = 'Visto';
                caja.querySelector('p:last-child').classList.replace('text-red-600', 'text-green-600');
            });
        }
    </script>
</x-app-layout>