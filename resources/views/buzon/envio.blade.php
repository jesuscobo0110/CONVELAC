<x-app-layout>
    <div class="min-h-screen bg-gradient-to-br from-sky-50 to-sky-100 py-12 px-6">
        <div class="max-w-4xl mx-auto">

            <!-- Header con botón IGUAL que en recepción -->
            <div class="flex justify-between items-center mb-10">
                <img src="{{ asset('images/Logo Convelac HD.png') }}" alt="Convelac" class="h-16">

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-5 py-2.5 bg-red-600 text-white rounded-lg shadow hover:bg-red-700 transition font-medium">
                        Cerrar sesión
                    </button>
                </form>
            </div>

            <h1 class="text-4xl font-bold text-sky-800 text-center mb-4">¡Hola, {{ auth()->user()->name }}!</h1>
            <p class="text-center text-lg text-gray-600 mb-12">Envía tu comprobante de pago o retención</p>

            <!-- Mensaje éxito -->
            @if(session('success'))
                <div class="mb-8 p-6 bg-green-100 border-2 border-green-400 rounded-2xl text-center">
                    <p class="text-green-800 font-bold text-xl">{{ session('success') }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('comprobante.store') }}" enctype="multipart/form-data" class="bg-white rounded-3xl shadow-2xl p-10 space-y-12">
                @csrf

                <!-- Switch Pago / Retención -->
                <div class="flex justify-center items-center space-x-12">
                    <span class="text-xl font-medium text-gray-700">Pago</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="es_retencion" value="1" class="sr-only peer">
                        <div class="w-28 h-14 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-1.5 after:left-1.5 after:bg-white after:rounded-full after:h-11 after:w-11 after:transition-all peer-checked:bg-green-600"></div>
                    </label>
                    <span class="text-xl font-medium text-gray-700">Retención</span>
                </div>

                <!-- Fecha -->
                <div>
                    <label class="block text-lg font-medium text-gray-700 mb-3">Fecha del comprobante</label>
                    <input type="date" name="fecha_envio" required class="w-full px-5 py-4 text-lg border border-gray-300 rounded-xl focus:ring-4 focus:ring-sky-300 focus:border-sky-500">
                </div>

                <!-- DRAG & DROP 100% FUNCIONAL (coma corregida) -->
                <div x-data="{
                    files: [],
                    addFiles(newFiles) {
                        const added = Array.from(newFiles || []);
                        added.forEach(file => {
                            if (file.size > 10 * 1024 * 1024) {
                                alert(`El archivo '${file.name}' supera los 10 MB`);
                                return;
                            }
                            if (!this.files.find(f => f.name === file.name && f.size === file.size)) {
                                this.files.push(file);
                            }
                        });
                        this.updateInput();
                    },
                    removeFile(fileToRemove) {
                        this.files = this.files.filter(f => f !== fileToRemove);
                        this.updateInput();
                    },
                    updateInput() {
                        const dt = new DataTransfer();
                        this.files.forEach(f => dt.items.add(f));
                        $refs.realInput.files = dt.files;
                    }
                }">
                    <input type="file" name="archivos[]" multiple accept=".pdf,.jpg,.jpeg,.png" x-ref="realInput" class="hidden" @change="addFiles($event.target.files)">

                    <div class="bg-sky-50 rounded-3xl p-12 border-4 border-dashed border-sky-400">
                        <div class="text-center mb-10">
                            <svg class="mx-auto h-20 w-20 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            <p class="mt-6 text-3xl font-bold text-gray-800">Arrastra tus comprobantes aquí o haz clic</p>
                            <p class="text-lg text-gray-600">Hasta 10 archivos • PDF o imágenes • Máx. 10 MB c/u</p>
                        </div>

                        <div 
                            @drop.prevent="addFiles($event.dataTransfer.files)"
                            @dragover.prevent
                            @dragenter.prevent="$el.classList.add('ring-8', 'ring-sky-400', 'bg-sky-100')"
                            @dragleave.prevent="$el.classList.remove('ring-8', 'ring-sky-400', 'bg-sky-100')"
                            @click="$refs.realInput.click()"
                            class="min-h-96 p-12 border-4 border-dashed border-gray-300 rounded-3xl cursor-pointer hover:border-sky-500 transition-all bg-white text-center"
                        >
                            <template x-if="files.length === 0">
                                <div class="py-24">
                                    <p class="text-2xl text-gray-500">No has seleccionado archivos aún</p>
                                </div>
                            </template>

                            <template x-if="files.length > 0">
                                <div class="space-y-8">
                                    <p class="text-4xl font-bold text-green-600">
                                        <span x-text="files.length"></span> archivo<span x-show="files.length > 1">s</span> listo<span x-show="files.length > 1">s</span>
                                    </p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-96 overflow-y-auto">
                                        <template x-for="file in files" :key="file.name">
                                            <div class="flex items-center justify-between bg-white rounded-2xl p-6 shadow-lg border hover:shadow-xl transition">
                                                <div class="flex items-center space-x-5">
                                                    <div class="text-5xl">
                                                        <span x-show="file.type.includes('pdf')" class="text-red-600">PDF</span>
                                                        <span x-show="!file.type.includes('pdf')" class="text-blue-600">IMG</span>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="font-bold text-gray-800" x-text="file.name"></p>
                                                        <p class="text-sm text-gray-500" x-text="(file.size / 1024 / 1024).toFixed(2) + ' MB'"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click.stop="removeFile(file)" class="text-red-600 hover:text-red-800 text-4xl font-light">×</button>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Botón Enviar -->
                    <div class="text-center mt-10">
                        <button type="submit" :disabled="files.length === 0"
                                class="px-28 py-7 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-700 hover:to-blue-800 text-white font-bold text-3xl rounded-3xl shadow-2xl transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="files.length === 0">Selecciona al menos un archivo</span>
                            <span x-show="files.length > 0">
                                Enviar <span x-text="files.length"></span> archivo<span x-show="files.length > 1">s</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>