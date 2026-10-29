<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'montador_id',
        'data_instalacao',
        'hora_instalacao',
        'data_retirada',
        'hora_retirada',
        'endereco_instalacao',
        'cidade_instalacao',
        'cep_instalacao',
        'status',
        'valor_total',
        'observacoes',
    ];

    protected $casts = [
        'data_instalacao' => 'date',
        'data_retirada' => 'date',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function montador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'montador_id');
    }

    public function tendas(): BelongsToMany
    {
        return $this->belongsToMany(Tenda::class, 'pedido_tendas')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }

    public function ordensServico(): HasMany
    {
        return $this->hasMany(OrdemServico::class);
    }

    public function osInstalacao()
    {
        return $this->hasOne(OrdemServico::class)->where('tipo', 'instalacao')->latestOfMany();
    }

    public function osRetirada()
    {
        return $this->hasOne(OrdemServico::class)->where('tipo', 'retirada')->latestOfMany();
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'os_gerada' => 'O.S. Gerada',
            'em_instalacao' => 'Em Instalação',
            'instalado' => 'Instalado',
            'aguardando_retirada' => 'Aguardando Retirada',
            'em_retirada' => 'Em Retirada',
            'concluido' => 'Concluído',
            'cancelado' => 'Cancelado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'bg-gray-100 text-gray-800',
            'os_gerada' => 'bg-blue-100 text-blue-800',
            'em_instalacao' => 'bg-amber-100 text-amber-800',
            'instalado' => 'bg-emerald-100 text-emerald-800',
            'aguardando_retirada' => 'bg-purple-100 text-purple-800',
            'em_retirada' => 'bg-orange-100 text-orange-800',
            'concluido' => 'bg-green-100 text-green-800',
            'cancelado' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

