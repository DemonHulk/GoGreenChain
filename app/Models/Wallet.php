<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Wallet extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_user',
        'wallet_address'
    ];
    // Relación: Una wallet pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}