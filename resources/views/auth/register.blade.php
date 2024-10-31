<html>
    <body>
        <x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-r from-green-50 to-blue-50">
        <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('storage/gogreenchain.png') }}" class="w-48 h-auto" alt="GoGreenChain Logo">
            </div>

            <h2 class="text-center text-2xl font-bold text-gray-700 mb-6">Crear Nueva Cuenta</h2>

            <x-validation-errors class="mb-4" />
            <button class="btn btn-success" onclick="obtenerUbicacion()">Obtener Ubicación</button>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/admin-lte/3.1.0/css/adminlte.min.css">
            <style>
                /* Estilo para la línea debajo del título */
                .title-line {
                    border-bottom: 2px solid #28a745; /* Color verde de la línea */
                    margin-bottom: 20px; /* Espacio entre la línea y el contenido */
                    width: 50%; /* Ancho de la línea */
                }
            </style>            
                <form method="POST" action="{{ route('register') }}" class="space-y-4" enctype="multipart/form-data">
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
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Dirección (Favor de obtener por el botón Obtener Ubicación) </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <x-label for="address" value="{{ __('dirección') }}" />
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
                            <!-- Campo para almacenar la ubicación -->
                            <input type="hidden" id="location" name="location" />
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
                        </div>
                    </div>
    
                    <!-- Foto de Perfil -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Foto de Perfil</h3>
                        <div>
                            <x-label for="profile_photo_path" value="{{ __('Sube tu foto de perfil') }}" />
                            <x-input id="profile_photo_path" class="block mt-1 w-full" type="file" name="profile_photo_path" />
                        </div>
                    </div>
    
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">Contraseña</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-label for="password" value="{{ __('Contraseña') }}" />
                                <div class="relative">
                                    <x-input id="password" 
                                        class="block mt-1 w-full" 
                                        type="password" 
                                        name="password" 
                                        required 
                                        placeholder="Mínimo 8 caracteres" />
                                    <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center" 
                                        onclick="togglePasswordVisibility('password')">
                                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" 
                                             fill="none" 
                                             stroke="currentColor" 
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <x-label for="password_confirmation" value="{{ __('Confirmar Contraseña') }}" />
                                <div class="relative">
                                    <x-input id="password_confirmation" 
                                        class="block mt-1 w-full" 
                                        type="password" 
                                        name="password_confirmation" 
                                        required 
                                        placeholder="Confirme su contraseña" />
                                    <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center" 
                                        onclick="togglePasswordVisibility('password_confirmation')">
                                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" 
                                             fill="none" 
                                             stroke="currentColor" 
                                             viewBox="0 0 24 24">
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
    
                <script>
                    function obtenerUbicacion() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(mostrarPosicion, manejarError);
                        } else {
                            alert("La geolocalización no es compatible con este navegador.");
                        }
                    }
                
                    function mostrarPosicion(position) {
                        const latitud = position.coords.latitude;
                        const longitud = position.coords.longitude;
                        document.getElementById("location").value = `${latitud},${longitud}`; // Almacena latitud y longitud
                        obtenerDireccion(latitud, longitud);
                    }
                
                    function obtenerDireccion(lat, lon) {
                        const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1`;
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                const direccion = data.display_name;
                                const ciudad = data.address.city || data.address.town || data.address.village || "No disponible";
                                const estado = data.address.state || "No disponible";
                                const cp = data.address.postcode || "No disponible";
                
                                document.getElementById("address").value = direccion; // Completa el campo de dirección
                                document.getElementById("city").value = ciudad; // Completa el campo de ciudad
                                document.getElementById("state").value = estado; // Completa el campo de estado
                                document.getElementById("postal_code").value = cp; // Completa el campo de código postal
                            })
                            .catch(error => {
                                console.error("Error al obtener la dirección:", error);
                            });
                    }
                
                    function manejarError(error) {
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                alert("Usuario denegó la solicitud de Geolocalización.");
                                break;
                            case error.POSITION_UNAVAILABLE:
                                alert("La ubicación no está disponible.");
                                break;
                            case error.TIMEOUT:
                                alert("La solicitud para obtener la ubicación ha expirado.");
                                break;
                            case error.UNKNOWN_ERROR:
                                alert("Ocurrió un error desconocido.");
                                break;
                        }
                    }
                </script>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const svg = button.querySelector('svg');
            
            if (input.type === 'password') {
                input.type = 'text';
                // Cambia el ícono a "ocultar"
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                `;
            } else {
                input.type = 'password';
                // Restaura el ícono original de "mostrar"
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                `;
            }
        }
    </script>

</x-guest-layout>
</body>
</html>