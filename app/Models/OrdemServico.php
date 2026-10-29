<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdemServico extends Model
{
    use HasFactory;

    protected $table = 'ordens_servico';

    protected $fillable = [
        'pedido_id',
        'tipo',
        'numero_os',
        'status',
        'fotos',
        'observacoes_montador',
        'iniciado_em',
        'concluido_em',
    ];

    protected $casts = [
        'fotos' => 'array',
        'iniciado_em' => 'datetime',
        'concluido_em' => 'datetime',
    ];

    public function pedido(): BelongsTo
    {
        return $this->belongsTo(Pedido::class);
    }

    public static function gerarNumero(string $tipo): string
    {
        $prefix = $tipo === 'instalacao' ? 'OSI' : 'OSR';
        $ano = date('Y');
        $ultimo = self::whereYear('created_at', $ano)
            ->where('tipo', $tipo)
            ->count() + 1;

        return sprintf('%s-%s-%05d', $prefix, $ano, $ultimo);
    }
}

