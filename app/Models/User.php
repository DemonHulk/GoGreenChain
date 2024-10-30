<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    protected $fillable = [
        'id_rol',
        'name',
        'username',
        'email',
        'password',
        'address',
        'city',
        'state',
        'postal_code',
        'phone',
        'location',
        'username_wallet',
        'id_wallet',
        'active',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function rol()
    {
        return $this->belongsTo(RolModelo::class, 'id_rol');
    }

    public function tasks(): HasMany
{
    return $this->hasMany(Tasks::class, 'id_empresa'); 
}
public function tasks_as_empresa(): HasMany
{
    return $this->hasMany(Tasks::class, 'id_empresa');
}


public function tareas()
{
    return $this->hasMany(Tasks::class, 'id_usuario');
}

/**
 * Relación: usuario como el que acepta las tareas.
 */
public function tasks_as_usuario(): HasMany
{
    return $this->hasMany(Tasks::class, 'id_usuario');
}



public function tasks_usuario()
{
    return $this->hasMany(Tasks::class, 'id_usuario');
}

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];
}
