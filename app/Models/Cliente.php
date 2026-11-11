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
        'cep',
        'endereco', // Rua / Avenida
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'estado',
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

    public function propostas(): HasMany
    {
        return $this->hasMany(Proposta::class);
    }

    /**
     * Alias para permitir acessar e definir 'rua' como sinônimo de 'endereco'
     */
    public function getRuaAttribute(): ?string
    {
        return $this->attributes['endereco'] ?? null;
    }

    public function setRuaAttribute($value): void
    {
        $this->attributes['endereco'] = $value;
    }

    /**
     * Retorna o endereço completo e detalhado do cliente formatado
     */
    public function getEnderecoCompletoAttribute(): string
    {
        $partes = [];

        // Logradouro + Número
        if (!empty($this->endereco)) {
            $logradouro = $this->endereco;
            if (!empty($this->numero)) {
                $logradouro .= ', nº ' . $this->numero;
            } else {
                $logradouro .= ', S/N';
            }
            $partes[] = $logradouro;
        }

        // Complemento
        if (!empty($this->complemento)) {
            $partes[] = $this->complemento;
        }

        // Bairro
        if (!empty($this->bairro)) {
            $partes[] = 'Bairro ' . $this->bairro;
        }

        // Cidade / UF
        if (!empty($this->cidade)) {
            $cidadeUf = $this->cidade;
            if (!empty($this->estado)) {
                $cidadeUf .= '/' . strtoupper($this->estado);
            }
            $partes[] = $cidadeUf;
        } elseif (!empty($this->estado)) {
            $partes[] = strtoupper($this->estado);
        }

        // CEP
        if (!empty($this->cep)) {
            $partes[] = 'CEP: ' . $this->cep;
        }

        return !empty($partes) ? implode(' - ', $partes) : 'Endereço não informado';
    }

    /**
     * Retorna endereço formatado compacto para listagens
     */
    public function getEnderecoFormatadoAttribute(): string
    {
        $texto = $this->endereco ?? '';
        if (!empty($this->numero)) {
            $texto .= ', ' . $this->numero;
        }
        if (!empty($this->bairro)) {
            $texto .= ' - ' . $this->bairro;
        }
        if (!empty($this->cidade)) {
            $texto .= ' - ' . $this->cidade;
            if (!empty($this->estado)) {
                $texto .= '/' . strtoupper($this->estado);
            }
        }
        if (!empty($this->cep)) {
            $texto .= " (CEP: {$this->cep})";
        }

        return trim($texto) ?: 'Endereço não informado';
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
