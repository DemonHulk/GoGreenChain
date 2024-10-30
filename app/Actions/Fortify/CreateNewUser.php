<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:70', 'regex:/^[\p{L}\s]+$/u'], 
            'id_rol' => ['required', 'in:2,3'], 
            'username' => ['required', 'string', 'max:70', 'regex:/^[a-zA-Z0-9]+$/'], 
            'email' => ['required', 'string', 'email', 'max:70', 'unique:users'],
            'password' => $this->passwordRules(),
            'address' => ['required', 'string', 'max:150'],
            'city' => ['required', 'string', 'max:70'],
            'state' => ['required', 'string', 'max:70'],
            'postal_code' => ['required', 'string', 'regex:/^\d{5}$/'], 
            'phone' => ['required', 'string', 'regex:/^\d{10}$/'],
            'username_wallet' => ['required', 'string', 'max:50'],
            'profile_photo_path' => ['required', 'image', 'max:2048'], 
            'id_wallet' => ['nullable', 'string', 'max:255'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
            'location' => ['required', 'string'], 
        ])->validate();
    
         // Agregar automáticamente ".testnet" si no está incluido en username_wallet
        if (!empty($input['username_wallet']) && !str_ends_with($input['username_wallet'], '.testnet')) {
            $input['username_wallet'] .= '.testnet';
        }

        // Procesamos la imagen y obtenemos la ruta
        $profilePhotoPath = null;
        if (request()->hasFile('profile_photo_path')) {
            // Determinamos la ruta de almacenamiento según el rol
            $directory = $input['id_rol'] == 3 ? 'usuario' : 'empresa';
            
            // Obtener el archivo de imagen
            $file = request()->file('profile_photo_path');
    
            // Generar un nombre único para el archivo
            $filename = time() . '_' . $file->getClientOriginalName();
    
            // Guardar la imagen en el directorio deseado
            $path = $file->move(public_path("storage/{$directory}"), $filename);
    
            // Guardar la ruta del archivo en la base de datos
            $profilePhotoPath = "{$directory}/{$filename}";
        }
    
        return User::create([
            'name' => $input['name'],
            'id_rol' => $input['id_rol'],
            'username' => $input['username'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'address' => $input['address'],
            'city' => $input['city'],
            'state' => $input['state'],
            'postal_code' => $input['postal_code'],
            'phone' => $input['phone'],
            'username_wallet' => $input['username_wallet'] ?? null,
            'profile_photo_path' => $profilePhotoPath, 
            'id_wallet' => $input['id_wallet'] ?? null,
            'location' => $input['location'], // Guardar ubicación
        ]);
    }
    
}
