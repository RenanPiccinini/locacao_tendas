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

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'instalacao' => 'Montagem / Instalação',
            'retirada' => 'Desmontagem / Retirada',
            'conclusao' => 'Conclusão / Devolução',
            'manutencao' => 'Manutenção / Vistoria',
            default => ucfirst(str_replace('_', ' ', $this->tipo)),
        };
    }

    public function getTipoBadgeColorAttribute(): string
    {
        return match($this->tipo) {
            'instalacao' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300 border-blue-200 dark:border-blue-700/50',
            'retirada' => 'bg-purple-100 text-purple-800 dark:bg-purple-950/70 dark:text-purple-300 border-purple-200 dark:border-purple-700/50',
            'conclusao' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300 border-emerald-200 dark:border-emerald-700/50',
            'manutencao' => 'bg-amber-100 text-amber-800 dark:bg-amber-950/70 dark:text-amber-300 border-amber-200 dark:border-amber-700/50',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200 border-gray-200 dark:border-gray-700',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'concluida' => 'Concluída',
            'cancelada' => 'Cancelada',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            'pendente' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-950/70 dark:text-yellow-300',
            'em_andamento' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/70 dark:text-blue-300',
            'concluida' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/70 dark:text-emerald-300',
            'cancelada' => 'bg-red-100 text-red-800 dark:bg-red-950/70 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300',
        };
    }

    public static function gerarNumero(string $tipo): string
    {
        $prefix = match($tipo) {
            'instalacao' => 'OSI',
            'retirada' => 'OSR',
            'conclusao' => 'OSC',
            'manutencao' => 'OSM',
            default => 'OS-' . strtoupper(substr($tipo, 0, 3)),
        };
        $ano = date('Y');
        $ultimo = self::whereYear('created_at', $ano)
            ->where('tipo', $tipo)
            ->count() + 1;

        $numero = sprintf('%s-%s-%05d', $prefix, $ano, $ultimo);
        while (self::where('numero_os', $numero)->exists()) {
            $ultimo++;
            $numero = sprintf('%s-%s-%05d', $prefix, $ano, $ultimo);
        }

        return $numero;
    }
}
