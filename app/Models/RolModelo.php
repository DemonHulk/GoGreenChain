<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolModelo extends Model
{
    use HasFactory;

    protected $table = 'rol';
    protected $primaryKey = 'id_rol';
    public $timestamps = true;
    protected $fillable = ['tipo'];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_rol');
    }

}
