<x-guest-layout>
    <!-- FONDO EXACTAMENTE IGUAL QUE LOS BUZONES -->
    <div class="min-h-screen bg-cover bg-center bg-fixed flex items-center justify-center px-4"
         style="background-image: url('{{ asset('images/fondo-convelac.jpg') }}');">
        
        <!-- CAPA OSCURA + BLUR SUAVE (igual que recepción y envío) -->
        <div class="absolute inset-0 bg-black bg-opacity-60 backdrop-blur-md"></div>

        <!-- TARJETA DE LOGIN CENTRADA -->
        <div class="relative z-10 w-full max-w-md">
            <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl p-10 border border-white/30 text-center">

                <!-- LOGO REDONDO Y PERFECTO (sin estirarse) -->
                <div class="mb-8">
                    <img src="{{ asset('images/Logo Convelac HD1.png') }}" 
                         alt="Convelac" 
                         class="w-32 h-32 mx-auto rounded-full object-contain shadow-2xl border-4 border-white/50">
                </div>

                <h1 class="text-4xl font-black text-gray-800 mb-2">La Pastoreña</h1>
                <p class="text-lg text-gray-600 mb-10">Buzón Administrativo</p>

                <!-- FORMULARIO -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-text-input 
                            id="email"
                            type="email"
                            name="email"
                            :value="old('email')"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Correo electrónico"
                            class="w-full px-6 py-4 text-lg rounded-xl border-2 border-gray-300 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100 transition placeholder-gray-500"
                        />
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-left" />
                    </div>

                    <div>
                        <x-text-input 
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Contraseña"
                            class="w-full px-6 py-4 text-lg rounded-xl border-2 border-gray-300 focus:border-cyan-600 focus:ring-4 focus:ring-cyan-100 transition placeholder-gray-500"
                        />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-left" />
                    </div>

                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-5 h-5 text-cyan-600 rounded">
                            <span>Recordarme</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-cyan-600 hover:text-cyan-800 font-semibold">
                                ¿Olvidaste tu contraseña?
                            </a>
                        @endif
                    </div>

                    <button type="submit"
                            class="w-full py-5 bg-gradient-to-r from-cyan-600 to-blue-700 hover:from-cyan-700 hover:to-blue-800 text-white font-bold text-xl rounded-2xl shadow-xl transition transform hover:scale-105 mt-6">
                        Iniciar Sesión
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="mt-8 text-gray-600">
                        ¿No tienes cuenta? 
                        <a href="{{ route('register') }}" class="text-cyan-600 hover:text-cyan-800 font-bold">
                            Regístrate aquí
                        </a>
                    </p>
                @endif

                <p class="text-gray-500 text-sm mt-10">
                    © {{ date('Y') }} Convelac · Leche de Calidad
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>