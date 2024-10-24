<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tasks extends Model
{
    use HasFactory;

    // Definir la tabla asociada (opcional si sigue la convención de nombres pluralizados)
    protected $table = 'tasks';

    // Definir los campos que se pueden asignar masivamente (fillable)
    protected $fillable = [
        'id_user', 
        'title',
        'description', 
        'start_date', 
        'end_date', 
        'reward', 
        'location', 
        'task_type', 
        'status'
    ];

    /**
     * Relación: una tarea pertenece a un usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    /**
     * Establecer el formato de las fechas.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reward' => 'decimal:2',
    ];

    /**
     * Obtener el estado de la tarea en formato amigable.
     */
    public function getStatusAttribute($value)
    {
        return ucfirst($value); // Capitaliza el estado ('Pending', 'Completed')
    }
}