<?php

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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // Relación con el usuario que crea la tarea
            $table->string('title'); // nombre de la tarea
            $table->string('description'); // Descripción de la tarea
            $table->date('start_date'); // Fecha de inicio
            $table->date('end_date'); // Fecha de fin
            $table->decimal('reward', 10, 2); // Recompensa en tokens
            $table->string('location'); // Ubicación de la tarea
            $table->enum('task_type', ['Servicio Comunitario', 'Ambiental', 'Educativa', 'Técnica']); // Tipo de tarea
            $table->enum('status', ['pending', 'completed'])->default('pending'); // Estado de la tarea
            $table->timestamps(); // Timestamps para las fechas de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
