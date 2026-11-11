<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proposta extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero',
        'user_id',
        'cliente_id',
        'data_proposta',
        'identificacao',
        'status',
        // De:
        'de_razao_social',
        'de_fantasia',
        'de_cnpj',
        'de_ie',
        'de_im',
        'de_cep',
        'de_endereco',
        'de_bairro',
        'de_cidade',
        'de_uf',
        'de_consultor',
        'de_celular',
        'de_email',
        // Para:
        'para_razao_social',
        'para_fantasia',
        'para_cnpj_cpf',
        'para_ie',
        'para_cep',
        'para_endereco',
        'para_bairro',
        'para_cidade',
        'para_uf',
        'para_solicitante',
        'para_celular',
        'para_email',
        // Período(s)
        'data_saida',
        'hora_saida',
        'data_inicio',
        'hora_inicio',
        'data_fim',
        'hora_fim',
        'data_retorno',
        'hora_retorno',
        // Totais
        'valor_total',
        'valor_desconto',
        'valor_total_geral',
        // Pagamento(s) e Observações
        'pagamentos',
        'observacoes',
    ];

    protected $casts = [
        'data_proposta' => 'date',
        'data_saida' => 'date',
        'data_inicio' => 'date',
        'data_fim' => 'date',
        'data_retorno' => 'date',
        'valor_total' => 'decimal:2',
        'valor_desconto' => 'decimal:2',
        'valor_total_geral' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(PropostaItem::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rascunho' => 'Rascunho',
            'enviada' => 'Enviada',
            'aprovada' => 'Aprovada',
            'recusada' => 'Recusada',
            default => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'rascunho' => 'bg-gray-100 text-gray-700 dark:bg-slate-800 dark:text-slate-300',
            'enviada' => 'bg-blue-100 text-blue-800 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50',
            'aprovada' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-700/50',
            'recusada' => 'bg-red-100 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800/50',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Retorna os dados padrão da empresa locadora ("De:") para pré-preenchimento no formulário.
     */
    public static function dadosPadraoLocadora(?User $usuario = null): array
    {
        return [
            'de_razao_social' => '58.950.200 MARCIA REGINA SABINO GIBERTONI',
            'de_fantasia' => 'TENDAS ELDORADO',
            'de_cnpj' => '58.950.200/0001-95',
            'de_ie' => 'ISENTO',
            'de_im' => '',
            'de_cep' => '15077-429',
            'de_endereco' => 'Rua Ruth Tranjan Donegá, 11',
            'de_bairro' => 'Parque Residencial Buona Vita',
            'de_cidade' => 'São José do Rio Preto',
            'de_uf' => 'SP',
            'de_consultor' => $usuario?->name ?? '',
            'de_celular' => $usuario?->whatsapp ?? '(17) 99142-9132',
            'de_email' => $usuario?->email ?? '',
        ];
    }

    /**
     * Calcula o próximo número sequencial sugerido para novas propostas.
     */
    public static function proximoNumero(): string
    {
        $maxNum = 0;

        foreach (static::pluck('numero') as $num) {
            if (is_numeric($num) && (int) $num > $maxNum) {
                $maxNum = (int) $num;
            }
        }

        $maxId = (int) (static::max('id') ?? 0);

        return (string) max($maxNum + 1, $maxId + 1);
    }
}
