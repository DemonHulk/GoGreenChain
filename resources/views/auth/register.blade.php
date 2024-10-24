<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-green-50 to-blue-50">
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('storage/gogreenchain.jpg') }}" class="w-48 h-auto" alt="GoGreenChain Logo">
            </div>

            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Crear Nueva Cuenta</h2>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Tipo de Cuenta -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Tipo de Cuenta</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex p-4 border rounded-lg cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="id_rol" value="3" class="mt-1" checked>
                            <div class="ml-2">
                                <span class="block font-medium text-gray-700">Usuario</span>
                                <span class="text-sm text-gray-500">Cuenta personal</span>
                            </div>
                        </label>
                        <label class="flex p-4 border rounded-lg cursor-pointer hover:border-green-500 transition-colors">
                            <input type="radio" name="id_rol" value="2" class="mt-1">
                            <div class="ml-2">
                                <span class="block font-medium text-gray-700">Empresa</span>
                                <span class="text-sm text-gray-500">Cuenta corporativa</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Información Personal -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Información Personal</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="name" value="{{ __('Nombre Completo') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required placeholder="Ingrese su nombre completo" />
                        </div>
                        
                        <div>
                            <x-label for="username" value="{{ __('Nombre de Usuario') }}" />
                            <x-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required placeholder="Elija un nombre de usuario" />
                        </div>

                        <div>
                            <x-label for="email" value="{{ __('Correo Electrónico') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required placeholder="ejemplo@correo.com" />
                        </div>

                        <div>
                            <x-label for="phone" value="{{ __('Teléfono') }}" />
                            <x-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="(123) 456-7890" />
                        </div>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Dirección</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <x-label for="address" value="{{ __('Dirección') }}" />
                            <x-input id="address" class="block mt-1 w-full" type="text" name="address" :value="old('address')" required placeholder="Calle, Número, Colonia" />
                        </div>

                        <div>
                            <x-label for="city" value="{{ __('Ciudad') }}" />
                            <x-input id="city" class="block mt-1 w-full" type="text" name="city" :value="old('city')" required placeholder="Ciudad" />
                        </div>

                        <div>
                            <x-label for="state" value="{{ __('Estado') }}" />
                            <x-input id="state" class="block mt-1 w-full" type="text" name="state" :value="old('state')" required placeholder="Estado" />
                        </div>

                        <div>
                            <x-label for="postal_code" value="{{ __('Código Postal') }}" />
                            <x-input id="postal_code" class="block mt-1 w-full" type="text" name="postal_code" :value="old('postal_code')" required placeholder="12345" />
                        </div>
                    </div>
                </div>

                <!-- Información de Wallet -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Información de Wallet</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="username_wallet" value="{{ __('Usuario de Wallet') }}" />
                            <x-input id="username_wallet" class="block mt-1 w-full" type="text" name="username_wallet" :value="old('username_wallet')" placeholder="Usuario de wallet" />
                        </div>

                        <div>
                            <x-label for="id_wallet" value="{{ __('ID de Wallet') }}" />
                            <x-input id="id_wallet" class="block mt-1 w-full" type="text" name="id_wallet" :value="old('id_wallet')" placeholder="ID de wallet" />
                        </div>
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <h3 class="text-lg font-medium text-gray-700 mb-4">Contraseña</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-label for="password" value="{{ __('Contraseña') }}" />
                            <div class="relative">
                                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required placeholder="Mínimo 8 caracteres" />
                                <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center" 
                                        onclick="togglePasswordVisibility('password')">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <x-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
                            <div class="relative">
                                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required placeholder="Confirme su contraseña" />
                                <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center" 
                                        onclick="togglePasswordVisibility('password_confirmation')">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Términos y Condiciones -->
                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mt-4">
                        <x-label for="terms">
                            <div class="flex items-center">
                                <x-checkbox name="terms" id="terms" required />
                                <div class="ml-2">
                                    {!! __('Estoy de acuerdo con los :terminos_de_servicio', [
                                        'terminos_de_servicio' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-green-600 hover:text-green-900">'.__('Términos y Condiciones').'</a>'
                                    ]) !!}
                                </div>
                            </div>
                        </x-label>
                    </div>
                @endif

                <div class="flex items-center justify-between mt-6">
                    <a class="text-sm text-green-600 hover:text-green-900" href="{{ route('login') }}">
                        {{ __('¿Ya tienes una cuenta? Inicia sesión') }}
                    </a>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Completar Registro') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }
    </script>
    @endpush
</x-guest-layout>