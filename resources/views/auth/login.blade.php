<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center bg-sky-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">

            <!-- LOGO + TÍTULO -->
            <div class="text-center">
                <img 
                    src="{{ asset('images/Logo Convelac HD.png') }}" 
                    alt="Logo Convelac" 
                    class="w-32 h-32 mx-auto mb-6 object-contain rounded-full shadow-lg"
                >
                <h2 class="text-3xl font-bold text-sky-700 dark:text-sky-400">
                    Buzón de Control Administrativo de Pago y Tributos
                </h2>
                <p class="mt-2 text-sm text-sky-600 dark:text-sky-300">
                    Inicia sesión para gestionar tus documentos
                </p>
            </div>

            <!-- FORMULARIO -->
            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6">
                @csrf

                <!-- EMAIL -->
                <div>
                    <x-input-label for="email" :value="__('Correo electrónico')" class="text-sky-700 dark:text-sky-300" />
                    <x-text-input 
                        id="email" 
                        class="block mt-1 w-full" 
                        type="email" 
                        name="email" 
                        :value="old('email')" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="ej: admin@convelac.com"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- CONTRASEÑA -->
                <div>
                    <x-input-label for="password" :value="__('Contraseña')" class="text-sky-700 dark:text-sky-300" />
                    <x-text-input 
                        id="password" 
                        class="block mt-1 w-full" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="••••••••"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- RECORDARME -->
                <div class="flex items-center">
                    <input id="remember" type="checkbox" name="remember" class="h-4 w-4 text-sky-600 focus:ring-sky-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-sky-700 dark:text-sky-300">
                        {{ __('Recordarme') }}
                    </label>
                </div>

                <!-- BOTÓN + OLVIDÉ -->
                <div class="flex items-center justify-between">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm text-sky-600 hover:text-sky-700 dark:text-sky-400">
                            {{ __('¿Olvidaste tu contraseña?') }}
                        </a>
                    @endif

                    <x-primary-button class="bg-sky-600 hover:bg-sky-700">
                        {{ __('Iniciar Sesión') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>