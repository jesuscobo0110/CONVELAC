<x-app-layout>
    <div class="min-h-screen bg-cover bg-center bg-fixed"
         style="background-image: url('{{ asset('images/fondo-convelac.jpg') }}');">
        <div class="min-h-screen bg-black bg-opacity-60 backdrop-blur-md">

            <div class="max-w-4xl mx-auto px-6 pt-20 pb-12">

                <h1 class="text-6xl font-black text-white text-center mb-6 drop-shadow-2xl">
                    ¡Hola, {{ auth()->user()->name }}!
                </h1>
                <p class="text-2xl text-gray-200 text-center mb-16 drop-shadow-lg">
                    Envía tu comprobante de pago o retención
                </p>

                @if(session('success'))
                    <div class="mb-8 p-6 bg-green-100 bg-opacity-90 border-2 border-green-400 rounded-2xl text-center">
                        <p class="text-green-800 font-bold text-xl">{{ session('success') }}</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('comprobante.store') }}" enctype="multipart/form-data" class="bg-white/95 backdrop-blur-sm rounded-3xl shadow-2xl p-12 space-y-12" x-data="uploadZone()">
                    @csrf

                    <!-- Switch Pago / Retención -->
                    <div class="flex justify-center items-center space-x-12">
                        <span class="text-2xl font-medium text-gray-700">Pago</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="es_retencion" value="1" class="sr-only peer">
                            <div class="w-32 h-16 bg-gray-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-2 after:left-2 after:bg-white after:rounded-full after:h-12 after:w-12 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                        <span class="text-2xl font-medium text-gray-700">Retención</span>
                    </div>

                    <div>
                        <label class="block text-xl font-medium text-gray-700 mb-3">Fecha del comprobante</label>
                        <input type="date" name="fecha_envio" required class="w-full px-6 py-4 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-sky-300 focus:border-sky-500 transition">
                    </div>

                    <div>
                        <input type="file" name="archivos[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="hidden" x-ref="input" @change="addFiles($event.target.files)">

                        <div class="bg-sky-50/90 rounded-3xl p-12 border-4 border-dashed border-sky-400">
                            <div class="text-center mb-10">
                                <p class="mt-6 text-4xl font-bold text-gray-800">Arrastra tus comprobantes aquí o haz clic</p>
                                <p class="text-xl text-gray-600">Hasta 10 archivos • PDF o imágenes • Máx. 10 MB c/u</p>
                            </div>

                            <div @drop.prevent="addFiles($event.dataTransfer.files)"
                                 @dragover.prevent
                                 @dragenter.prevent="$el.classList.add('ring-8','ring-sky-400','bg-sky-100')"
                                 @dragleave.prevent="$el.classList.remove('ring-8','ring-sky-400','bg-sky-100')"
                                 @click="$refs.input.click()"
                                 class="min-h-96 p-12 border-4 border-dashed border-gray-300 rounded-3xl cursor-pointer hover:border-sky-500 transition-all bg-white/90 text-center">

                                <template x-if="!files.length">
                                    <div class="py-24">
                                        <p class="text-2xl text-gray-500">No has seleccionado archivos aún</p>
                                    </div>
                                </template>

                                <template x-if="files.length">
                                    <div class="space-y-8">
                                        <p class="text-4xl font-bold text-green-600">
                                            <span x-text="files.length"></span> archivo<span x-show="files.length > 1">s</span> listo<span x-show="files.length > 1">s</span>
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-h-96 overflow-y-auto">
                                            <template x-for="file in files" :key="file.name">
                                                <div class="flex items-center justify-between bg-white rounded-2xl p-6 shadow-lg border hover:shadow-xl transition">
                                                    <div class="flex items-center space-x-5">
                                                        <div class="text-5xl font-bold">
                                                            <span x-show="file.name.toLowerCase().endsWith('.pdf')" class="text-red-600">PDF</span>
                                                            <span x-show="!file.name.toLowerCase().endsWith('.pdf')" class="text-blue-600">IMG</span>
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

                        <div class="text-center mt-12">
                            <button type="submit" :disabled="!files.length"
                                    class="px-32 py-8 bg-gradient-to-r from-sky-600 to-blue-700 hover:from-sky-700 hover:to-blue-800 text-white font-bold text-4xl rounded-3xl shadow-2xl transform hover:scale-105 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                <span x-show="!files.length">Selecciona al menos un archivo</span>
                                <span x-show="files.length">Enviar <span x-text="files.length"></span> archivo<span x-show="files.length > 1">s</span></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function uploadZone() {
            return {
                files: [],
                addFiles(newFiles) {
                    Array.from(newFiles).forEach(file => {
                        if (file.size > 10 * 1024 * 1024) {
                            alert(`El archivo '${file.name}' supera los 10 MB`);
                            return;
                        }
                        if (!this.files.find(f => f.name === file.name && f.size === file.size)) {
                            this.files.push(file);
                        }
                    });
                    this.$refs.input.files = this.dataTransferFiles();
                },
                removeFile(file) {
                    this.files = this.files.filter(f => f !== file);
                    this.$refs.input.files = this.dataTransferFiles();
                },
                dataTransferFiles() {
                    const dt = new DataTransfer();
                    this.files.forEach(f => dt.items.add(f));
                    return dt.files;
                }
            }
        }
    </script>
</x-app-layout>