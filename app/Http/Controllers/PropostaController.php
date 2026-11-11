<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Proposta;
use App\Models\Tenda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PropostaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Proposta::with(['user', 'cliente', 'itens'])->latest();

        if ($request->filled('busca')) {
            $busca = trim((string) $request->busca);
            $query->where(function ($q) use ($busca) {
                $q->where('numero', 'like', "%{$busca}%")
                  ->orWhere('identificacao', 'like', "%{$busca}%")
                  ->orWhere('para_razao_social', 'like', "%{$busca}%")
                  ->orWhere('para_fantasia', 'like', "%{$busca}%")
                  ->orWhere('para_cnpj_cpf', 'like', "%{$busca}%")
                  ->orWhere('para_solicitante', 'like', "%{$busca}%")
                  ->orWhere('para_cidade', 'like', "%{$busca}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_proposta', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_proposta', '<=', $request->data_fim);
        }

        $propostas = $query->paginate(15)->withQueryString();

        return view('propostas.index', compact('propostas'));
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nome')->get();
        $tendas = Tenda::orderBy('nome')->get();
        $proximoNumero = Proposta::proximoNumero();
        $dadosPadrao = Proposta::dadosPadraoLocadora(Auth::user());

        return view('propostas.create', compact('clientes', 'tendas', 'proximoNumero', 'dadosPadrao'));
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $this->validateAndPrepare($request);

        $proposta = DB::transaction(function () use ($payload) {
            $dadosProposta = $payload['proposta'];
            $dadosProposta['user_id'] = Auth::id();

            $proposta = Proposta::create($dadosProposta);

            foreach ($payload['itens'] as $item) {
                $proposta->itens()->create($item);
            }

            return $proposta;
        });

        return redirect()
            ->route('propostas.show', $proposta)
            ->with('success', "Proposta Nº {$proposta->numero} criada com sucesso!");
    }

    public function show(Proposta $proposta): View
    {
        $proposta->load(['user', 'cliente', 'itens.tenda']);

        return view('propostas.show', compact('proposta'));
    }

    public function edit(Proposta $proposta): View
    {
        $proposta->load('itens');
        $clientes = Cliente::orderBy('nome')->get();
        $tendas = Tenda::orderBy('nome')->get();

        return view('propostas.edit', compact('proposta', 'clientes', 'tendas'));
    }

    public function update(Request $request, Proposta $proposta): RedirectResponse
    {
        $payload = $this->validateAndPrepare($request);

        DB::transaction(function () use ($proposta, $payload) {
            $proposta->update($payload['proposta']);
            $proposta->itens()->delete();

            foreach ($payload['itens'] as $item) {
                $proposta->itens()->create($item);
            }
        });

        return redirect()
            ->route('propostas.show', $proposta)
            ->with('success', "Proposta Nº {$proposta->numero} atualizada com sucesso!");
    }

    public function destroy(Proposta $proposta): RedirectResponse
    {
        $numero = $proposta->numero;
        $proposta->delete();

        return redirect()
            ->route('propostas.index')
            ->with('success', "Proposta Nº {$numero} excluída com sucesso.");
    }

    public function pdf(Proposta $proposta): Response
    {
        $proposta->load(['user', 'cliente', 'itens.tenda']);

        $pdf = Pdf::loadView('pdf.proposta', [
            'proposta' => $proposta,
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = 'Proposta_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $proposta->numero) . '.pdf';

        return $pdf->stream($nomeArquivo);
    }

    public function download(Proposta $proposta): Response
    {
        $proposta->load(['user', 'cliente', 'itens.tenda']);

        $pdf = Pdf::loadView('pdf.proposta', [
            'proposta' => $proposta,
        ])->setPaper('a4', 'portrait');

        $nomeArquivo = 'Proposta_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $proposta->numero) . '.pdf';

        return $pdf->download($nomeArquivo);
    }

    /**
     * Valida e normaliza todos os campos da proposta e seus itens.
     */
    private function validateAndPrepare(Request $request): array
    {
        $validated = $request->validate([
            'numero' => 'required|string|max:30',
            'cliente_id' => 'nullable|exists:clientes,id',
            'data_proposta' => 'required|date',
            'identificacao' => 'nullable|string|max:255',
            'status' => 'nullable|in:rascunho,enviada,aprovada,recusada',

            // De:
            'de_razao_social' => 'required|string|max:255',
            'de_fantasia' => 'nullable|string|max:255',
            'de_cnpj' => 'nullable|string|max:30',
            'de_ie' => 'nullable|string|max:30',
            'de_im' => 'nullable|string|max:30',
            'de_cep' => 'nullable|string|max:20',
            'de_endereco' => 'nullable|string|max:255',
            'de_bairro' => 'nullable|string|max:100',
            'de_cidade' => 'nullable|string|max:100',
            'de_uf' => 'nullable|string|max:10',
            'de_consultor' => 'nullable|string|max:255',
            'de_celular' => 'nullable|string|max:30',
            'de_email' => 'nullable|string|max:255',

            // Para:
            'para_razao_social' => 'required|string|max:255',
            'para_fantasia' => 'nullable|string|max:255',
            'para_cnpj_cpf' => 'nullable|string|max:30',
            'para_ie' => 'nullable|string|max:30',
            'para_cep' => 'nullable|string|max:20',
            'para_endereco' => 'nullable|string|max:255',
            'para_bairro' => 'nullable|string|max:100',
            'para_cidade' => 'nullable|string|max:100',
            'para_uf' => 'nullable|string|max:10',
            'para_solicitante' => 'nullable|string|max:255',
            'para_celular' => 'nullable|string|max:30',
            'para_email' => 'nullable|string|max:255',

            // Período(s)
            'data_saida' => 'nullable|date',
            'hora_saida' => 'nullable|string|max:10',
            'data_inicio' => 'nullable|date',
            'hora_inicio' => 'nullable|string|max:10',
            'data_fim' => 'nullable|date',
            'hora_fim' => 'nullable|string|max:10',
            'data_retorno' => 'nullable|date',
            'hora_retorno' => 'nullable|string|max:10',

            // Totais
            'valor_total' => 'nullable',
            'valor_desconto' => 'nullable',
            'valor_total_geral' => 'nullable',

            // Pagamento(s) e Observações
            'pagamentos' => 'nullable|string',
            'observacoes' => 'nullable|string',

            // Itens
            'itens' => 'nullable|array',
            'itens.*.tenda_id' => 'nullable|exists:tendas,id',
            'itens.*.descricao' => 'nullable|string|max:255',
            'itens.*.quantidade' => 'nullable|integer|min:1',
            'itens.*.valor_periodo' => 'nullable',
        ], [
            'numero.required' => 'O número da proposta é obrigatório.',
            'data_proposta.required' => 'A data da proposta é obrigatória.',
            'de_razao_social.required' => 'A Razão Social (De:) é obrigatória.',
            'para_razao_social.required' => 'A Razão Social (Para:) é obrigatória.',
        ]);

        if (!empty($validated['de_uf'])) {
            $validated['de_uf'] = strtoupper(trim($validated['de_uf']));
        }
        if (!empty($validated['para_uf'])) {
            $validated['para_uf'] = strtoupper(trim($validated['para_uf']));
        }
        if (empty($validated['status'])) {
            $validated['status'] = 'rascunho';
        }

        // Prepara os itens válidos (ignora linhas completamente em branco)
        $itensPreparados = [];
        $somaItens = 0.0;

        foreach ($request->input('itens', []) as $item) {
            $descricao = trim((string) ($item['descricao'] ?? ''));
            if ($descricao === '') {
                continue;
            }

            $qtd = max(1, (int) ($item['quantidade'] ?? 1));
            $valorPeriodo = $this->parseDecimal($item['valor_periodo'] ?? 0);

            $itensPreparados[] = [
                'tenda_id' => !empty($item['tenda_id']) ? (int) $item['tenda_id'] : null,
                'descricao' => $descricao,
                'quantidade' => $qtd,
                'valor_periodo' => $valorPeriodo,
            ];

            $somaItens += ($qtd * $valorPeriodo);
        }

        $valorTotal = $request->filled('valor_total')
            ? $this->parseDecimal($request->input('valor_total'))
            : $somaItens;

        $valorDesconto = $request->filled('valor_desconto')
            ? $this->parseDecimal($request->input('valor_desconto'))
            : 0.0;

        $valorTotalGeral = $request->filled('valor_total_geral')
            ? $this->parseDecimal($request->input('valor_total_geral'))
            : max(0.0, $valorTotal - $valorDesconto);

        $validated['valor_total'] = round($valorTotal, 2);
        $validated['valor_desconto'] = round($valorDesconto, 2);
        $validated['valor_total_geral'] = round($valorTotalGeral, 2);

        unset($validated['itens']);

        return [
            'proposta' => $validated,
            'itens' => $itensPreparados,
        ];
    }

    /**
     * Converte formatos monetários brasileiros ("1.500,00") ou decimais ("1500.00") para float.
     */
    private function parseDecimal(mixed $valor): float
    {
        if (is_int($valor) || is_float($valor)) {
            return max(0.0, (float) $valor);
        }

        $raw = trim((string) $valor);
        if ($raw === '') {
            return 0.0;
        }

        $raw = str_replace(['R$', 'r$', ' '], '', $raw);

        if (str_contains($raw, ',')) {
            $limpo = str_replace('.', '', $raw);
            $limpo = str_replace(',', '.', $limpo);
        } else {
            $limpo = $raw;
        }

        return max(0.0, (float) preg_replace('/[^0-9.]/', '', $limpo));
    }
}
