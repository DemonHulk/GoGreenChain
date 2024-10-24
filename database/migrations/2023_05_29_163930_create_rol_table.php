<?php

use App\Models\RolModelo;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rol', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('tipo');
            $table->timestamps();
        });

        RolModelo::create(['tipo' => 'Administrador']);
        RolModelo::create(['tipo' => 'Empresa']);
        RolModelo::create(['tipo' => 'Usuario']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rol');
    }
};
