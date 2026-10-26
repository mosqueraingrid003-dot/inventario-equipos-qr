<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password', 'role_id', 'activo'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = ['activo' => 'boolean'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'user_id');
    }

    /** ¿El usuario tiene alguno de estos roles? */
    public function tieneRol(string ...$roles): bool
    {
        return in_array($this->role?->nombre, $roles, true);
    }

    public function esAdmin(): bool
    {
        return $this->tieneRol('Administrador');
    }

    /** Administrador y Técnico pueden registrar y editar. */
    public function puedeEscribir(): bool
    {
        return $this->tieneRol('Administrador', 'Técnico');
    }
}
