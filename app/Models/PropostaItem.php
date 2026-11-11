<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropostaItem extends Model
{
    use HasFactory;

    protected $table = 'proposta_itens';

    protected $fillable = [
        'proposta_id',
        'tenda_id',
        'descricao',
        'quantidade',
        'valor_periodo',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'valor_periodo' => 'decimal:2',
    ];

    public function proposta(): BelongsTo
    {
        return $this->belongsTo(Proposta::class);
    }

    public function tenda(): BelongsTo
    {
        return $this->belongsTo(Tenda::class);
    }
}
