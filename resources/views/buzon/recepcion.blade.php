@php
    $archivos = is_string($comprobante->archivos_json) 
        ? json_decode($comprobante->archivos_json, true) 
        : $comprobante->archivos_json;

    $archivos = is_array($archivos) ? $archivos : [];

    $vistos = $comprobante->archivos_vistos 
        ? json_decode($comprobante->archivos_vistos, true) 
        : [];
@endphp

<div class="mt-6">
    <p class="font-semibold text-lg text-gray-800 mb-4">
        Adjuntos ({{ count($archivos) }}):
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($archivos as $archivo)
            @php
                $url = $archivo['url'] ?? '#';
                $name = $archivo['name'] ?? 'archivo.pdf';
                $publicId = $archivo['public_id'];
                $yaVisto = in_array($publicId, $vistos);
            @endphp

            <div class="bg-gray-50 rounded-xl p-5 border-2 {{ $yaVisto ? 'border-green-400' : 'border-gray-200' }} hover:border-sky-400 transition relative">
                <!-- Check de revisado -->
                @if($yaVisto)
                    <div class="absolute top-2 right-2 bg-green-500 text-white rounded-full p-1">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                @endif

                @if(str_ends_with(strtolower($name), '.pdf'))
                    <div class="bg-red-50 rounded-lg h-48 flex items-center justify-center mb-4">
                        <div class="text-center">
                            <span class="text-5xl font-bold text-red-600">PDF</span>
                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($name, 30) }}</p>
                        </div>
                    </div>
                @else
                    <img src="{{ $url }}" class="w-full h-48 object-cover rounded-lg mb-4" alt="{{ $name }}">
                @endif

                <!-- Solo botón Ver (marca como visto al hacer clic) -->
                <a href="{{ route('comprobante.ver', [$comprobante->id, $publicId]) }}"
                   target="_blank"
                   class="block text-center bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 rounded-lg transition">
                    {{ $yaVisto ? 'Ver de nuevo' : 'Ver' }}
                </a>
            </div>
        @endforeach
    </div>
</div>