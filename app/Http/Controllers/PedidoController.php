<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PedidoController extends Controller
{
    protected WhatsappService $whatsapp;

    public function __construct(WhatsappService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function index(Request $request): View
    {
        $query = Pedido::with(['cliente', 'montador', 'tendas', 'ordensServico'])->latest();

        if (auth()->user()->isMontador()) {
            $query->where('montador_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('busca')) {
            $busca = $request->busca;
            $query->whereHas('cliente', function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%");
            });
        }

        $pedidos = $query->paginate(15);

        return view('pedidos.index', compact('pedidos'));
    }

    public function create(): View
    {
        $clientes = Cliente::orderBy('nome')->get();
        $montadores = User::where('role', 'montador')->orderBy('name')->get();
        $tendas = Tenda::where('quantidade_disponivel', '>', 0)->get();

        return view('pedidos.create', compact('clientes', 'montadores', 'tendas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'montador_id' => 'nullable|exists:users,id',
            'data_instalacao' => 'required|date',
            'hora_instalacao' => 'nullable',
            'data_retirada' => 'required|date|after_or_equal:data_instalacao',
            'hora_retirada' => 'nullable',
            'endereco_instalacao' => 'required|string|max:255',
            'cidade_instalacao' => 'nullable|string|max:100',
            'cep_instalacao' => 'nullable|string|max:20',
            'valor_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
            'tendas_selecionadas' => 'required|array|min:1',
            'tendas_selecionadas.*' => 'exists:tendas,id',
            'quantidades' => 'nullable|array',
            'gerar_os_agora' => 'nullable',
        ], [
            'tendas_selecionadas.required' => 'Por favor, selecione ao menos uma tenda da lista.',
            'cliente_id.required' => 'Selecione um cliente cadastrado.',
            'data_retirada.after_or_equal' => 'A data de retirada deve ser igual ou posterior à data de instalação.',
        ]);

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'cliente_id' => $validated['cliente_id'],
                'montador_id' => $validated['montador_id'] ?? null,
                'data_instalacao' => $validated['data_instalacao'],
                'hora_instalacao' => $validated['hora_instalacao'] ?? null,
                'data_retirada' => $validated['data_retirada'],
                'hora_retirada' => $validated['hora_retirada'] ?? null,
                'endereco_instalacao' => $validated['endereco_instalacao'],
                'cidade_instalacao' => $validated['cidade_instalacao'] ?? null,
                'cep_instalacao' => $validated['cep_instalacao'] ?? null,
                'valor_total' => $validated['valor_total'] ?? 0,
                'observacoes' => $validated['observacoes'] ?? null,
                'status' => 'pendente',
            ]);

            // Associa tendas e deduz do estoque disponível
            $quantidades = $request->input('quantidades', []);
            foreach ($validated['tendas_selecionadas'] as $tendaId) {
                $qtd = isset($quantidades[$tendaId]) ? max(1, intval($quantidades[$tendaId])) : 1;
                $pedido->tendas()->attach($tendaId, ['quantidade' => $qtd]);

                $tenda = Tenda::find($tendaId);
                if ($tenda) {
                    $tenda->decrement('quantidade_disponivel', min($tenda->quantidade_disponivel, $qtd));
                }
            }

            // Se marcado para gerar OS automaticamente
            if ($request->filled('gerar_os_agora')) {
                $this->processarGerarOs($pedido);
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido criado com sucesso!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao salvar pedido: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao salvar pedido: ' . $e->getMessage());
        }
    }

    public function show(Pedido $pedido): View
    {
        if (auth()->user()->isMontador() && $pedido->montador_id !== auth()->id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $pedido->load(['cliente', 'montador', 'tendas', 'ordensServico']);
        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido): View
    {
        $clientes = Cliente::orderBy('nome')->get();
        $montadores = User::where('role', 'montador')->orderBy('name')->get();
        $tendas = Tenda::all();
        $pedido->load(['tendas']);

        return view('pedidos.edit', compact('pedido', 'clientes', 'montadores', 'tendas'));
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'montador_id' => 'nullable|exists:users,id',
            'data_instalacao' => 'required|date',
            'hora_instalacao' => 'nullable',
            'data_retirada' => 'required|date|after_or_equal:data_instalacao',
            'hora_retirada' => 'nullable',
            'endereco_instalacao' => 'required|string|max:255',
            'cidade_instalacao' => 'nullable|string|max:100',
            'cep_instalacao' => 'nullable|string|max:20',
            'valor_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
        ]);

        $pedido->update($validated);

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido atualizado com sucesso!');
    }

    public function destroy(Pedido $pedido): RedirectResponse
    {
        if (!in_array($pedido->status, ['concluido', 'cancelado'])) {
            foreach ($pedido->tendas as $tenda) {
                $tenda->increment('quantidade_disponivel', $tenda->pivot->quantidade);
            }
        }

        $pedido->delete();
        return redirect()->route('pedidos.index')->with('success', 'Pedido excluído com sucesso!');
    }

    public function gerarOs(Pedido $pedido): RedirectResponse
    {
        $this->processarGerarOs($pedido);

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', 'Ordem de Serviço gerada e notificação enviada para o WhatsApp do montador!');
    }

    protected function processarGerarOs(Pedido $pedido): void
    {
        $osExistente = $pedido->ordensServico()->where('tipo', 'instalacao')->first();
        
        if (!$osExistente) {
            $os = OrdemServico::create([
                'pedido_id' => $pedido->id,
                'tipo' => 'instalacao',
                'numero_os' => OrdemServico::gerarNumero('instalacao'),
                'status' => 'pendente',
            ]);
        } else {
            $os = $osExistente;
        }

        $pedido->update(['status' => 'os_gerada']);

        // Notifica montador no WhatsApp
        $this->notificarMontadorNovaOs($pedido, $os);
    }

    public function iniciarInstalacao(Pedido $pedido): RedirectResponse
    {
        $os = $pedido->osInstalacao();
        if ($os) {
            $os->update([
                'status' => 'em_andamento',
                'iniciado_em' => now(),
            ]);
        }

        $pedido->update(['status' => 'em_instalacao']);

        return back()->with('success', 'Status atualizado: Em processo de instalação!');
    }

    public function finalizarInstalacao(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'fotos.*' => 'nullable|image|max:10240',
            'observacoes_montador' => 'nullable|string',
        ]);

        $caminhosFotos = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('fotos_instalacao', 'public');
                $caminhosFotos[] = $path;
            }
        }

        $osInstalacao = $pedido->osInstalacao();
        if ($osInstalacao) {
            $fotosAntigas = $osInstalacao->fotos ?? [];
            $fotosFinais = array_merge($fotosAntigas, $caminhosFotos);

            $osInstalacao->update([
                'status' => 'concluida',
                'concluido_em' => now(),
                'fotos' => $fotosFinais,
                'observacoes_montador' => $request->observacoes_montador,
            ]);
        }

        // Gera automaticamente a OS de Retirada
        $osRetiradaExistente = $pedido->ordensServico()->where('tipo', 'retirada')->first();
        if (!$osRetiradaExistente) {
            $osRetirada = OrdemServico::create([
                'pedido_id' => $pedido->id,
                'tipo' => 'retirada',
                'numero_os' => OrdemServico::gerarNumero('retirada'),
                'status' => 'pendente',
            ]);
        } else {
            $osRetirada = $osRetiradaExistente;
        }

        $pedido->update(['status' => 'aguardando_retirada']);

        // Notifica montador sobre a retirada
        if ($pedido->montador && $pedido->montador->whatsapp) {
            $dataRetirada = Carbon::parse($pedido->data_retirada)->format('d/m/Y');
            $msg = "✅ *Instalação Concluída!*\n\n"
                 . "Pedido #{$pedido->id} finalizado por você.\n"
                 . "📋 Nova *OS de Retirada* ({$osRetirada->numero_os}) gerada automaticamente.\n"
                 . "📅 Data prevista de retirada: *{$dataRetirada}*.\n"
                 . "📍 Local: {$pedido->endereco_instalacao}";
            $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
        }

        return back()->with('success', 'Instalação finalizada com sucesso! OS de Retirada gerada automaticamente.');
    }

    public function iniciarRetirada(Pedido $pedido): RedirectResponse
    {
        $osRetirada = $pedido->osRetirada();
        if ($osRetirada) {
            $osRetirada->update([
                'status' => 'em_andamento',
                'iniciado_em' => now(),
            ]);
        }

        $pedido->update(['status' => 'em_retirada']);

        return back()->with('success', 'Status atualizado: Em processo de retirada!');
    }

    public function confirmarRetirada(Pedido $pedido): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $osRetirada = $pedido->osRetirada();
            if ($osRetirada) {
                $osRetirada->update([
                    'status' => 'concluida',
                    'concluido_em' => now(),
                ]);
            }

            // Devolve as tendas ao estoque
            foreach ($pedido->tendas as $tenda) {
                $qtdRetirada = $tenda->pivot->quantidade;
                $tenda->increment('quantidade_disponivel', $qtdRetirada);
            }

            $pedido->update(['status' => 'concluido']);

            DB::commit();

            if ($pedido->montador && $pedido->montador->whatsapp) {
                $msg = "🎉 *Retirada Concluída!*\n\n"
                     . "Pedido #{$pedido->id} concluído com sucesso.\n"
                     . "As tendas foram reintegradas ao estoque do sistema.";
                $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
            }

            return back()->with('success', 'Retirada confirmada com sucesso! As tendas voltaram para o estoque automaticamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao confirmar retirada: ' . $e->getMessage());
        }
    }

    protected function notificarMontadorNovaOs(Pedido $pedido, OrdemServico $os): void
    {
        if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
            return;
        }

        $dataInst = Carbon::parse($pedido->data_instalacao)->format('d/m/Y');
        $horaInst = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'A combinar';

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $msg = "🚨 *NOVA ORDEM DE SERVIÇO: {$os->numero_os}*\n\n"
             . "Olá {$pedido->montador->name}, você foi designado para uma nova montagem:\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📞 *Telefone:* {$pedido->cliente->telefone}\n"
             . "📅 *Data de Instalação:* {$dataInst} às {$horaInst}\n"
             . "📍 *Endereço:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n\n"
             . "⛺ *Tendas a montar:*\n{$tendasLista}\n"
             . "📝 *Obs:* " . ($pedido->observacoes ?: 'Sem observações adicionais') . "\n\n"
             . "Acesse o sistema pelo seu celular para iniciar o atendimento!";

        $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
    }
}
