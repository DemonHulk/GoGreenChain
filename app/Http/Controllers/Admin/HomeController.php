<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class HomeController extends Controller
{

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Handle actions after the user is authenticated.
     * Store the user's wallet address in the session and update .env.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Almacena la dirección de wallet en la sesión
        $request->session()->put('wallet_address', $user->wallet_address);

        // Actualizar el archivo .env con el wallet_address como NEAR_ACCOUNT_ID
        $this->updateEnv('NEAR_ACCOUNT_ID', $user->wallet_address);
    }

    /**
     * Log out the user and remove wallet_address from the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        // Eliminar wallet_address de la sesión
        $request->session()->forget('wallet_address');

        // Cerrar sesión y destruir la sesión actual
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Update the .env file with a new key-value pair.
     *
     * @param string $key
     * @param string $value
     */
    public function updateEnv($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            // Obtener el contenido actual del archivo .env
            $env = file_get_contents($path);

            // Actualizar la línea si la clave ya existe, de lo contrario, agregarla
            $env = preg_replace("/^{$key}=.*/m", "{$key}={$value}", $env);

            // Si la clave no existe en el archivo .env, agregarla al final
            if (!preg_match("/^{$key}=/m", $env)) {
                $env .= "\n{$key}={$value}";
            }

            // Guardar el archivo .env actualizado
            file_put_contents($path, $env);

            // Limpiar el caché de configuración para que se aplique la nueva variable
            Artisan::call('config:clear');
        }
    }
}
