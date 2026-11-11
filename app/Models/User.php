<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'whatsapp',
        'ativo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isMontador(): bool
    {
        return $this->role === 'montador';
    }

    public function isAtivo(): bool
    {
        return (bool) ($this->ativo ?? true);
    }

    public function isBloqueado(): bool
    {
        return ! $this->isAtivo();
    }

    public function pedidosComoMontador(): HasMany
    {
        return $this->hasMany(Pedido::class, 'montador_id');
    }

    public function propostas(): HasMany
    {
        return $this->hasMany(Proposta::class);
    }
}
