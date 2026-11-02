<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'cpf_cnpj',
        'endereco',
        'cidade',
        'cep',
        'observacoes',
        'contrato_path',
        'contrato_gerado_em',
    ];

    protected $casts = [
        'contrato_gerado_em' => 'datetime',
    ];

    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class);
    }

    /**
     * Verifica se o cliente já possui um contrato gerado (no banco ou em disco)
     */
    public function temContrato(): bool
    {
        if (!empty($this->contrato_path)) {
            return true;
        }

        if ($this->contrato_gerado_em !== null) {
            return true;
        }

        $caminhoPadrao = "contratos/contrato_cliente_{$this->id}.pdf";
        return Storage::disk('public')->exists($caminhoPadrao) 
            || file_exists(storage_path("app/public/{$caminhoPadrao}"))
            || file_exists(public_path("storage/{$caminhoPadrao}"));
    }
}
