<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenda extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'tamanho',
        'descricao',
        'quantidade_total',
        'quantidade_disponivel',
        'valor_diaria',
    ];

    public function pedidos(): BelongsToMany
    {
        return $this->belongsToMany(Pedido::class, 'pedido_tendas')
                    ->withPivot('quantidade')
                    ->withTimestamps();
    }
}

