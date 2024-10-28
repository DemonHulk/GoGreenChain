<?php

use App\Models\RolModelo;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->string('name');
            $table->string('username');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('address'); // Dirección completa
            $table->string('city'); // Ciudad
            $table->string('state'); // Estado
            $table->string('phone'); // Número de teléfono
            $table->string('postal_code'); // Código Postal
            $table->string('location');
            $table->string('username_wallet'); //Nombre del usuario del wallet
            $table->string('id_wallet'); //ID del wallet
            $table->boolean('active')->default(true); // Estado de la empresa
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable(); 
            $table->foreign('id_rol')->references('id_rol')->on('rol');
            $table->timestamps();
        });
            
        // Obtén los registros de roles
        $administradorRol = RolModelo::where('id_rol', 1)->first();
        $empresaRol = RolModelo::where('id_rol', 2)->first();
        $usuarioRol = RolModelo::where('id_rol', 3)->first();

            // Crear usuario con rol de Administrador
            User::create([
                'id_rol' => $administradorRol->id_rol,
                'name' => 'Administrador',
                'username' => 'Administrador',
                'email' => 'Administrador@gmail.com',
                'password' => bcrypt('Administrador'),
                'address' => 'Calle Falsa 123',
                'city' => 'Ciudad Admin',
                'state' => 'Estado Admin',
                'phone' => '1122334455',   
                'postal_code' => '54321',
                'location' => '21.800174588382458, -105.20558786763493',
                'username_wallet' => 'admin_wallet',
                'id_wallet' => 'wallet_1'    
            ]);
    
            // Crear usuario con rol de Empresa
            User::create([
                'id_rol' => $empresaRol->id_rol,
                'name' => 'Empresa',
                'username' => 'Empresa',
                'email' => 'Empresa@gmail.com',
                'password' => bcrypt('Empresa'),
                'address' => 'Calle Empresa',
                'city' => 'Ciudad Empresa',
                'state' => 'Estado Empresa',
                'phone' => '1122334455',
                'postal_code' => '54321',
                'location' => '21.800174588382458, -105.20558786763493',
                'username_wallet' => 'empresa_wallet',
                'id_wallet' => 'wallet_2'    
            ]);

            // Crear usuario con rol de Usuario
            User::create([
                'id_rol' => $usuarioRol->id_rol,
                'name' => 'Usuario',
                'username' => 'Usuario',
                'email' => 'Usuario@gmail.com',
                'password' => bcrypt('Usuario'),
                'address' => 'Calle Usuario',
                'city' => 'Ciudad Usuario',
                'state' => 'Estado Usuario',
                'phone' => '1122334455', 
                'postal_code' => '54321',
                'location' => '21.800174588382458, -105.20558786763493',
                'username_wallet' => 'usuario_wallet',
                'id_wallet' => 'wallet_3'    
            ]);


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
