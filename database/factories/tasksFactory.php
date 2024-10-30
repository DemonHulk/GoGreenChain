<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\tasks>
 */
class tasksFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Ubicaciones cercanas a la ubicación dada
        $locations = [
            '21.8025,-105.1980', 
            '21.8000,-105.2000', 
            '21.8030,-105.1975', 
            '21.8040,-105.1985', 
            '21.7990,-105.1995', 
            '21.8010,-105.2010', 
            '21.8020,-105.2020', 
            '21.8050,-105.1980', 
            '21.7970,-105.1985',
            '21.8060,-105.1990', 
        ];

        return [
            'id_empresa' => 2, // Todos los id_empresa son 2
            'id_usuario' => null, 
            'title' => $this->faker->sentence(3), // Título aleatorio
            'description' => $this->faker->paragraph(), // Descripción aleatoria
            'start_date' => $this->faker->date(), // Fecha de inicio aleatoria
            'end_date' => $this->faker->date(), // Fecha de fin aleatoria
            'reward' => $this->faker->randomFloat(2, 10, 100), // Recompensa aleatoria entre 10 y 100
            'location' => $this->faker->randomElement($locations), // Ubicación aleatoria cercana
            'task_type' => $this->faker->randomElement(['Servicio Comunitario', 'Ambiental', 'Educativa', 'Técnica']), // Tipo de tarea aleatorio
            'status' => $this->faker->randomElement(['pending']), // Estado aleatorio
        ];
    }

}
