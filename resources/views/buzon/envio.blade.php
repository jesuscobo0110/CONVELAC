
<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-sky-50 to-sky-100 py-12 px-6">
        <div class="max-w-4xl mx-auto">

            <!-- Header con logo y cerrar sesión -->
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
                ¡Hola, {{ auth()->user()->name }}!
            </h1>
            <p class="text-center text-lg text-gray-600 mb-12">
                Envía tu comprobante de pago o retención
            </p>

            <!-- Mensajes de éxito o error -->
            @if(session('success'))
                <div class="mb-8 p-6 bg-green-100 border-2 border-green-400 rounded-2xl text-center">
                    <p class="text-green-800 font-bold text-xl">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-8 p-6 bg-red-100 border-2 border-red-400 rounded-2xl text-center">
                    <p class="text-red-800 font-bold">{{ session('error') }}</p>
                </div>
            @endif

            <!-- FORMULARIO FINAL (todo funcionando) -->
            <form method="POST" 
                  action="{{ route('comprobante.store') }}" 
                  enctype="multipart/form-data" 
                  class="bg-white rounded-3xl shadow-2xl p-10 space-y-8">

                @csrf

                <!-- Switch Pago / Retención -->
                <div class="flex justify-center items-center space-x-10">
                    <span class="text-xl font-medium text-gray-700">Pago</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="es_retencion" class="sr-only peer">
                        <div class="w-24 h-12 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-1 after:left-1 after:bg-white after:rounded-full after:h-10 after:w-10 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                    <span class="text-xl font-medium text-gray-700">Retención</span>
                </div>

                <!-- Fecha -->
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-2">Fecha del comprobante</label>
                    <input type="date" name="fecha_envio" required value="{{ old('fecha_envio') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-sky-300 focus:border-sky-500">
                </div>

                <!-- === AQUÍ VA EL DRAG & DROP 100% FUNCIONAL === -->
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-3">
                        Comprobante (PDF o imagen)
                    </label>

                    <div x-data="{ dragging: false, fileName: '' }"
                         class="relative border-4 border-dashed border-sky-300 rounded-2xl p-10 text-center hover:border-sky-500 transition cursor-pointer"
                         x-on:dragover.prevent="dragging = true"
                         x-on:dragleave.prevent="dragging = false"
                         x-on:drop.prevent="dragging = false; $refs.hiddenInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name; $refs.hiddenInput.dispatchEvent(new Event('change'))"
                         x-on:click="$refs.hiddenInput.click()">

                        <input type="file"
                               name="archivo"
                               accept=".pdf,image/*"
                               required
                               x-ref="hiddenInput"
                               class="hidden"
                               x-on:change="fileName = $event.target.files[0]?.name">

                        <div class="space-y-4">
                            <svg class="w-16 h-16 mx-auto text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>

                            <div>
                                <p class="text-lg font-medium text-gray-700">
                                    <span x-text="fileName ? fileName : 'Haz clic o arrastra tu archivo aquí'"></span>
                                </p>
                                <p class="text-sm text-gray-500">PDF o imagen • Máx. 10 MB</p>
                            </div>
                        </div>

                        <div x-show="dragging"
                             class="absolute inset-0 bg-sky-100 bg-opacity-80 rounded-2xl flex items-center justify-center">
                            <p class="text-2xl font-bold text-sky-700">¡Suelta aquí!</p>
                        </div>
                    </div>
                </div>
                <!-- === FIN DEL DRAG & DROP === -->

                <!-- Botón -->
                <div class="text-center pt-6">
                    <button type="submit" 
                            class="px-16 py-5 bg-sky-600 hover:bg-sky-700 text-white font-bold text-xl rounded-2xl shadow-xl transform hover:scale-105 transition">
                        Enviar Comprobante
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>