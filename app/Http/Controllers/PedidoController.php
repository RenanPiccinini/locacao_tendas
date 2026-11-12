<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\OrdemServico;
use App\Models\Pedido;
use App\Models\Tenda;
use App\Models\User;
use App\Services\DisponibilidadeService;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function index(Request $request, DisponibilidadeService $disponibilidadeService): View
    {
        $ordem = $request->input('ordem') === 'desc' ? 'desc' : 'asc';

        $query = Pedido::with(['cliente', 'montador', 'tendas', 'ordensServico'])
            ->orderBy('data_instalacao', $ordem)
            ->orderBy('id', $ordem);

        if (auth()->user()->isMontador()) {
            $query->where('montador_id', auth()->id());
        }

        // Filtro rápido de dia: 'hoje' ou 'todos' (permite listar tudo para antecipações ou filtrar só o dia)
        if ($request->input('filtro_dia') === 'hoje') {
            $hoje = Carbon::today()->format('Y-m-d');
            $query->where(function ($q) use ($hoje) {
                $q->whereDate('data_instalacao', $hoje)
                  ->orWhereDate('data_retirada', $hoje);
            });
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

        if ($request->filled('data_inicio')) {
            $dataInicio = $request->data_inicio;
            $dataFim = $request->filled('data_fim') ? $request->data_fim : $dataInicio;
            $query->where(function ($q) use ($dataInicio, $dataFim) {
                $q->whereDate('data_instalacao', '<=', $dataFim)
                  ->whereDate('data_retirada', '>=', $dataInicio);
            });
        }

        $pedidos = $query->paginate(15)->withQueryString();

        $dadosDisponibilidade = null;
        if ($request->filled('data_inicio')) {
            $dadosDisponibilidade = $disponibilidadeService->consultarDisponibilidade(
                $request->data_inicio,
                $request->data_fim
            );
        }

        return view('pedidos.index', compact('pedidos', 'dadosDisponibilidade'));
    }

    public function create(Request $request, DisponibilidadeService $disponibilidadeService): View
    {
        $clientes = Cliente::orderBy('nome')->get();
        $montadores = User::where('role', 'montador')->orderBy('name')->get();

        $dataInstalacao = $request->input('data_instalacao', date('Y-m-d'));
        $dataRetirada = $request->input('data_retirada', date('Y-m-d', strtotime('+2 days')));

        $dadosDisponibilidade = $disponibilidadeService->consultarDisponibilidade($dataInstalacao, $dataRetirada);
        $tendas = $dadosDisponibilidade['tendas'];

        return view('pedidos.create', compact('clientes', 'montadores', 'tendas', 'dadosDisponibilidade', 'dataInstalacao', 'dataRetirada'));
    }

    /**
     * Retorna em JSON a disponibilidade em tempo real das tendas para o período informado
     */
    public function consultarDisponibilidadeTendas(Request $request, DisponibilidadeService $disponibilidadeService): JsonResponse
    {
        $dataInstalacao = $request->input('data_instalacao', date('Y-m-d'));
        $dataRetirada = $request->input('data_retirada', $dataInstalacao);
        $ignorarPedidoId = $request->filled('pedido_id') ? (int) $request->input('pedido_id') : null;

        $dadosDisponibilidade = $disponibilidadeService->consultarDisponibilidade($dataInstalacao, $dataRetirada, $ignorarPedidoId);

        $tendas = $dadosDisponibilidade['tendas']->map(function ($tenda) {
            return [
                'id' => $tenda->id,
                'nome' => $tenda->nome,
                'tamanho' => $tenda->tamanho,
                'quantidade_total' => (int) $tenda->quantidade_total,
                'quantidade_em_locacao' => (int) $tenda->quantidade_em_locacao,
                'quantidade_disponivel' => (int) $tenda->quantidade_disponivel_periodo,
                'disponivel' => (int) $tenda->quantidade_disponivel_periodo > 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data_inicio' => $dadosDisponibilidade['data_inicio'],
            'data_fim' => $dadosDisponibilidade['data_fim'],
            'data_formatada' => $dadosDisponibilidade['data_formatada'],
            'total_disponivel' => $dadosDisponibilidade['total_disponivel'],
            'total_em_locacao' => $dadosDisponibilidade['total_em_locacao'],
            'tendas' => $tendas,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Normalização robusta de moeda para float
        $rawValor = trim((string) $request->input('valor_total'));
        if ($rawValor !== '') {
            if (str_contains($rawValor, ',')) {
                $limpo = str_replace('.', '', $rawValor);
                $limpo = str_replace(',', '.', $limpo);
            } else {
                $limpo = $rawValor;
            }
            $valorNumerico = (float) preg_replace('/[^0-9.]/', '', $limpo);
            $request->merge(['valor_total' => $valorNumerico]);
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'montador_id' => 'nullable|exists:users,id',
            'data_instalacao' => 'required|date',
            'hora_instalacao' => 'nullable',
            'data_evento' => 'nullable|date',
            'data_retirada' => 'required|date|after_or_equal:data_instalacao',
            'hora_retirada' => 'nullable',
            'endereco_instalacao' => 'required|string|max:255',
            'cidade_instalacao' => 'nullable|string|max:100',
            'cep_instalacao' => 'nullable|string|max:20',
            'valor_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
            'nome_responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:30',
            'tendas_selecionadas' => 'required|array|min:1',
            'tendas_selecionadas.*' => 'exists:tendas,id',
            'quantidades' => 'nullable|array',
            'gerar_os_agora' => 'nullable',
        ], [
            'tendas_selecionadas.required' => 'Por favor, selecione ao menos uma tenda da lista.',
            'cliente_id.required' => 'Selecione um cliente cadastrado.',
            'data_retirada.after_or_equal' => 'A data de retirada deve ser igual ou posterior à data de instalação.',
        ]);

        $quantidades = $request->input('quantidades', []);
        $valorTotal = isset($validated['valor_total']) && (float) $validated['valor_total'] > 0
            ? (float) $validated['valor_total']
            : null;

        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'cliente_id' => $validated['cliente_id'],
                'montador_id' => $validated['montador_id'] ?? null,
                'data_instalacao' => $validated['data_instalacao'],
                'hora_instalacao' => $validated['hora_instalacao'] ?? null,
                'data_evento' => $validated['data_evento'] ?? null,
                'data_retirada' => $validated['data_retirada'],
                'hora_retirada' => $validated['hora_retirada'] ?? null,
                'endereco_instalacao' => $validated['endereco_instalacao'],
                'cidade_instalacao' => $validated['cidade_instalacao'] ?? null,
                'cep_instalacao' => $validated['cep_instalacao'] ?? null,
                'valor_total' => $valorTotal,
                'observacoes' => $validated['observacoes'] ?? null,
                'nome_responsavel' => $validated['nome_responsavel'] ?? null,
                'telefone_responsavel' => $validated['telefone_responsavel'] ?? null,
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

            // 1. Gera a primeira OS automaticamente quando cria o pedido (notifica o montador SOMENTE se marcado)
            $gerarOsAgora = $request->has('tem_campo_gerar_os')
                ? $request->boolean('gerar_os_agora')
                : $request->boolean('gerar_os_agora', true);

            $primeiraOs = $this->criarNovaOsParaPedido($pedido, 'instalacao', [
                'status' => 'pendente',
                'observacoes_montador' => 'Ordem de Serviço inicial gerada na criação do Pedido #' . $pedido->id,
            ], $gerarOsAgora);

            $pedido->update(['status' => 'os_gerada']);

            DB::commit();

            $msgSucesso = $gerarOsAgora
                ? "Pedido criado com sucesso! 1ª Ordem de Serviço ({$primeiraOs->numero_os}) gerada e montador notificado no WhatsApp."
                : "Pedido criado com sucesso! Notificação não enviada no momento (o montador receberá apenas nas próximas notificações programadas).";

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', $msgSucesso);
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

    public function edit(Pedido $pedido, DisponibilidadeService $disponibilidadeService): View
    {
        $clientes = Cliente::orderBy('nome')->get();
        $montadores = User::where('role', 'montador')->orderBy('name')->get();
        $pedido->load(['tendas', 'cliente', 'montador']);

        $dataInstalacao = old('data_instalacao', $pedido->data_instalacao ? $pedido->data_instalacao->format('Y-m-d') : date('Y-m-d'));
        $dataRetirada = old('data_retirada', $pedido->data_retirada ? $pedido->data_retirada->format('Y-m-d') : date('Y-m-d', strtotime('+2 days')));

        $dadosDisponibilidade = $disponibilidadeService->consultarDisponibilidade($dataInstalacao, $dataRetirada, $pedido->id);
        $tendas = $dadosDisponibilidade['tendas'];

        return view('pedidos.edit', compact('pedido', 'clientes', 'montadores', 'tendas', 'dadosDisponibilidade', 'dataInstalacao', 'dataRetirada'));
    }

    public function update(Request $request, Pedido $pedido): RedirectResponse
    {
        $montadorAnterior = $pedido->montador_id;

        // Normalização robusta de moeda para float
        $rawValor = trim((string) $request->input('valor_total'));
        if ($rawValor !== '') {
            if (str_contains($rawValor, ',')) {
                $limpo = str_replace('.', '', $rawValor);
                $limpo = str_replace(',', '.', $limpo);
            } else {
                $limpo = $rawValor;
            }
            $valorNumerico = (float) preg_replace('/[^0-9.]/', '', $limpo);
            $request->merge(['valor_total' => $valorNumerico]);
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'montador_id' => 'nullable|exists:users,id',
            'data_instalacao' => 'required|date',
            'hora_instalacao' => 'nullable',
            'data_evento' => 'nullable|date',
            'data_retirada' => 'required|date|after_or_equal:data_instalacao',
            'hora_retirada' => 'nullable',
            'endereco_instalacao' => 'required|string|max:255',
            'cidade_instalacao' => 'nullable|string|max:100',
            'cep_instalacao' => 'nullable|string|max:20',
            'valor_total' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string',
            'nome_responsavel' => 'nullable|string|max:255',
            'telefone_responsavel' => 'nullable|string|max:30',
            'tendas_selecionadas' => 'nullable|array',
            'tendas_selecionadas.*' => 'exists:tendas,id',
            'quantidades' => 'nullable|array',
        ]);

        $valorTotal = isset($validated['valor_total']) && (float) $validated['valor_total'] > 0
            ? (float) $validated['valor_total']
            : null;
        $validated['valor_total'] = $valorTotal;

        DB::beginTransaction();
        try {
            $pedido->update($validated);

            // Se tendas foram selecionadas na edição, atualiza a relação e ajusta os estoques
            if ($request->has('tendas_selecionadas')) {
                $novasTendasSelecionadas = $validated['tendas_selecionadas'] ?? [];
                $quantidades = $request->input('quantidades', []);

                // 1. Devolve ao estoque as tendas que estavam no pedido (se não estiver concluído)
                if (!in_array($pedido->status, ['concluido', 'cancelado'])) {
                    foreach ($pedido->tendas as $tendaAtual) {
                        $tendaAtual->increment('quantidade_disponivel', $tendaAtual->pivot->quantidade);
                    }
                }

                // 2. Registra as novas tendas selecionadas com suas quantidades
                $syncData = [];
                foreach ($novasTendasSelecionadas as $tendaId) {
                    $qtd = isset($quantidades[$tendaId]) ? max(1, intval($quantidades[$tendaId])) : 1;
                    $syncData[$tendaId] = ['quantidade' => $qtd];

                    if (!in_array($pedido->status, ['concluido', 'cancelado'])) {
                        $tenda = Tenda::find($tendaId);
                        if ($tenda) {
                            $tenda->decrement('quantidade_disponivel', min($tenda->quantidade_disponivel, $qtd));
                        }
                    }
                }

                $pedido->tendas()->sync($syncData);
            }

            // Atualiza as Ordens de Serviço (OS) vinculadas para refletir as alterações
            $pedido->ordensServico()->touch();

            // Se designou ou alterou o montador, gera a OS (caso ainda não exista) ou notifica
            if (!empty($validated['montador_id']) && $validated['montador_id'] != $montadorAnterior) {
                if ($pedido->ordensServico()->count() === 0) {
                    $this->processarGerarOs($pedido);
                } else {
                    $osRecente = $pedido->ordensServico()->latest()->first();
                    if ($osRecente) {
                        try {
                            $this->notificarMontadorNovaOs($pedido, $osRecente);
                        } catch (\Throwable $e) {
                            Log::warning("Erro ao notificar novo montador no WhatsApp: " . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();

            return redirect()->route('pedidos.show', $pedido)
                ->with('success', 'Pedido atualizado com sucesso! As Ordens de Serviço (OS) foram alteradas automaticamente com as novas informações.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao atualizar pedido: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Erro ao atualizar pedido: ' . $e->getMessage());
        }
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

    public function gerarOs(Request $request, Pedido $pedido): RedirectResponse
    {
        $tipo = $request->input('tipo');
        if (!$tipo) {
            $tipo = match($pedido->status) {
                'pendente', 'os_gerada', 'em_instalacao' => 'instalacao',
                'instalado', 'aguardando_retirada', 'em_retirada' => 'retirada',
                'concluido' => 'conclusao',
                default => 'instalacao',
            };
        }

        $os = $this->criarNovaOsParaPedido($pedido, $tipo);

        return redirect()->route('pedidos.show', $pedido)
            ->with('success', "Nova Ordem de Serviço gerada com sucesso: {$os->numero_os} ({$os->tipo_label})!");
    }

    public function criarNovaOsParaPedido(Pedido $pedido, string $tipo = 'instalacao', array $dadosExtras = [], bool $notificarMontador = false): OrdemServico
    {
        $os = OrdemServico::create([
            'pedido_id' => $pedido->id,
            'tipo' => $tipo,
            'numero_os' => OrdemServico::gerarNumero($tipo),
            'status' => $dadosExtras['status'] ?? 'pendente',
            'observacoes_montador' => $dadosExtras['observacoes_montador'] ?? null,
            'iniciado_em' => $dadosExtras['iniciado_em'] ?? null,
            'concluido_em' => $dadosExtras['concluido_em'] ?? null,
        ]);

        if ($pedido->status === 'pendente' && $tipo === 'instalacao') {
            $pedido->update(['status' => 'os_gerada']);
        }

        // Montadores recebem ordem no WhatsApp SOMENTE na geração da O.S. inicial
        if ($notificarMontador) {
            $this->notificarMontadorNovaOs($pedido, $os);
        }

        return $os;
    }

    protected function processarGerarOs(Pedido $pedido, string $tipo = 'instalacao'): OrdemServico
    {
        return $this->criarNovaOsParaPedido($pedido, $tipo, [], true);
    }

    public function iniciarInstalacao(Pedido $pedido): RedirectResponse
    {
        $osAnterior = $pedido->ordensServico()->where('tipo', 'instalacao')->first();
        if ($osAnterior) {
            $osAnterior->update([
                'status' => 'em_andamento',
                'iniciado_em' => $osAnterior->iniciado_em ?: now(),
            ]);
        }

        $pedido->update(['status' => 'em_instalacao']);

        return back()->with('success', 'Status atualizado: Em processo de instalação.');
    }

    public function anexarFotos(Request $request, Pedido $pedido): RedirectResponse
    {
        $request->validate([
            'fotos' => 'required|array|min:1',
            'fotos.*' => 'image|max:10240',
            'observacoes_montador' => 'nullable|string',
        ], [
            'fotos.required' => 'Por favor, selecione ou tire ao menos uma foto.',
        ]);

        $caminhosFotos = [];
        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                $path = $foto->store('fotos_instalacao', 'public');
                $caminhosFotos[] = $path;
            }
        }

        $todasOsInstalacao = $pedido->ordensServico()->where('tipo', 'instalacao')->get();
        foreach ($todasOsInstalacao as $osItem) {
            $fotosAntigas = $osItem->fotos ?? [];
            $fotosFinais = array_merge($fotosAntigas, $caminhosFotos);

            $osItem->update([
                'fotos' => $fotosFinais,
                'observacoes_montador' => $request->observacoes_montador ?: $osItem->observacoes_montador,
            ]);
        }

        return back()->with('success', 'Fotos da instalação anexadas com sucesso!');
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

        // 1. Conclui a ordem de serviço de instalação inicial
        $todasOsInstalacao = $pedido->ordensServico()->where('tipo', 'instalacao')->get();
        foreach ($todasOsInstalacao as $osItem) {
            $fotosAntigas = $osItem->fotos ?? [];
            $fotosFinais = !empty($caminhosFotos) ? array_merge($fotosAntigas, $caminhosFotos) : $fotosAntigas;

            $osItem->update([
                'status' => 'concluida',
                'concluido_em' => $osItem->concluido_em ?: now(),
                'fotos' => $fotosFinais,
                'observacoes_montador' => $request->observacoes_montador ?: $osItem->observacoes_montador,
            ]);
        }

        // 2. Emite a 2ª OS do ciclo: O.S. de Retirada (sem notificar montador)
        $osRetirada = $this->criarNovaOsParaPedido($pedido, 'retirada', [
            'status' => 'pendente',
            'observacoes_montador' => 'O.S. de Retirada gerada automaticamente após confirmação da instalação.',
        ], false);

        $pedido->update(['status' => 'aguardando_retirada']);

        // 3. Notifica o WhatsApp principal conectado (empresa/admin)
        $this->notificarAdminTendaInstalada($pedido);

        return back()->with('success', "Tenda instalada com sucesso! O.S. de Retirada ({$osRetirada->numero_os}) gerada.");
    }

    public function iniciarRetirada(Pedido $pedido): RedirectResponse
    {
        $osRetirada = $pedido->osRetirada;
        if ($osRetirada) {
            $osRetirada->update([
                'status' => 'em_andamento',
                'iniciado_em' => $osRetirada->iniciado_em ?: now(),
            ]);
        }

        $pedido->update(['status' => 'em_retirada']);

        return back()->with('success', 'Status atualizado: Em processo de retirada.');
    }

    public function confirmarRetirada(Pedido $pedido): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $osRetirada = $pedido->osRetirada;
            if ($osRetirada) {
                $osRetirada->update([
                    'status' => 'concluida',
                    'concluido_em' => now(),
                ]);
            }

            // 1. Registra automaticamente no estoque como disponível (somando de volta)
            foreach ($pedido->tendas as $tenda) {
                $qtdRetirada = $tenda->pivot->quantidade;
                $tenda->increment('quantidade_disponivel', $qtdRetirada);
            }

            // 2. Emite a 3ª OS do ciclo: Conclusão / Devolução ao estoque (sem notificar montador)
            $osDevolucao = $this->criarNovaOsParaPedido($pedido, 'conclusao', [
                'status' => 'concluida',
                'iniciado_em' => now(),
                'concluido_em' => now(),
                'observacoes_montador' => 'Retirada e desmontagem concluídas. Todas as tendas foram reintegradas ao estoque como disponíveis.',
            ], false);

            $pedido->update(['status' => 'concluido']);

            DB::commit();

            // 3. Notifica o WhatsApp principal conectado (empresa/admin)
            $this->notificarAdminTendaRetirada($pedido);

            return back()->with('success', "Tenda retirada com sucesso! Tendas retornaram ao estoque como disponíveis e O.S. de Conclusão ({$osDevolucao->numero_os}) emitida.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao confirmar devolução: ' . $e->getMessage());
        }
    }

    protected function notificarAdminTendaInstalada(Pedido $pedido): void
    {
        $pedido->loadMissing(['montador', 'cliente', 'tendas']);

        $nomeMontador = $pedido->montador ? $pedido->montador->name : 'Montador da Equipe';
        $telefoneMontador = $pedido->montador && $pedido->montador->whatsapp ? " ({$pedido->montador->whatsapp})" : '';
        $dataInst = Carbon::parse($pedido->data_instalacao)->format('d/m/Y');
        $horaInst = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : '09:00';
        $dataRet = Carbon::parse($pedido->data_retirada)->format('d/m/Y');

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $msg = "⛺ *TENDA INSTALADA COM SUCESSO! (#{$pedido->id})*\n\n"
             . "O montador *{$nomeMontador}*{$telefoneMontador} concluiu a instalação das tendas:\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📞 *Contato do Cliente:* {$pedido->cliente->telefone}\n"
             . "📍 *Local:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n"
             . "📅 *Data da Montagem:* {$dataInst} às {$horaInst}\n"
             . "🔄 *Previsão de Retirada:* {$dataRet}\n\n"
             . "⛺ *Tendas Montadas:*\n{$tendasLista}\n"
             . "📝 *Status:* Montagem concluída com sucesso. Pedido aguardando retirada.";

        $this->whatsapp->notificarWhatsappPrincipal($msg);
    }

    protected function notificarAdminTendaRetirada(Pedido $pedido): void
    {
        $pedido->loadMissing(['montador', 'cliente', 'tendas']);

        $nomeMontador = $pedido->montador ? $pedido->montador->name : 'Montador da Equipe';
        $telefoneMontador = $pedido->montador && $pedido->montador->whatsapp ? " ({$pedido->montador->whatsapp})" : '';
        $dataRet = Carbon::parse($pedido->data_retirada)->format('d/m/Y');

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $msg = "🔄 *TENDA RETIRADA & ESTOQUE ATUALIZADO! (#{$pedido->id})*\n\n"
             . "O montador *{$nomeMontador}*{$telefoneMontador} concluiu a retirada e desmontagem:\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📍 *Local:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n"
             . "📅 *Data da Retirada:* {$dataRet}\n\n"
             . "⛺ *Tendas Devolvidas ao Estoque:*\n{$tendasLista}\n"
             . "📦 *Estoque:* Todas as tendas foram reintegradas e já constam como disponíveis no sistema.";

        $this->whatsapp->notificarWhatsappPrincipal($msg);
    }

    protected function notificarMontadorNovoPedido(Pedido $pedido): void
    {
        $pedido->loadMissing(['montador', 'cliente', 'tendas']);

        if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
            return;
        }

        $dataInst = Carbon::parse($pedido->data_instalacao)->format('d/m/Y');
        $horaInst = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'A combinar';
        $dataRet = Carbon::parse($pedido->data_retirada)->format('d/m/Y');
        $horaRet = $pedido->hora_retirada ? substr($pedido->hora_retirada, 0, 5) : 'A combinar';

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $dadosResp = '';
        if ($pedido->nome_responsavel) {
            $dadosResp = "👤 *Responsável / Solicitante:* {$pedido->nome_responsavel}" . ($pedido->telefone_responsavel ? " ({$pedido->telefone_responsavel})" : "") . "\n";
        }

        $linhaEvento = '';
        if ($pedido->data_evento) {
            $linhaEvento = "🎉 *Data do Evento:* " . Carbon::parse($pedido->data_evento)->format('d/m/Y') . "\n";
        }

        $msg = "📋 *NOVO PEDIDO DE MONTAGEM AGENDADO! (#{$pedido->id})*\n\n"
             . "Olá *{$pedido->montador->name}*, você foi escalado para um novo pedido de locação:\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📞 *Contato:* {$pedido->cliente->telefone}\n"
             . $dadosResp
             . "📅 *Instalação:* {$dataInst} às {$horaInst}\n"
             . $linhaEvento
             . "🔄 *Retirada:* {$dataRet} às {$horaRet}\n"
             . "📍 *Endereço:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n\n"
             . "⛺ *Tendas a serem instaladas:*\n{$tendasLista}\n"
             . "📝 *Observações:* " . ($pedido->observacoes ?: 'Nenhuma observação informada.') . "\n\n"
             . "Acesse o sistema no seu celular para acompanhar e emitir sua Ordem de Serviço!";

        $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
    }

    protected function notificarMontadorNovaOs(Pedido $pedido, OrdemServico $os): void
    {
        $pedido->loadMissing(['montador', 'cliente', 'tendas']);

        if (!$pedido->montador || empty($pedido->montador->whatsapp)) {
            return;
        }

        // Trava anti-duplicação: impede envio repetido do aviso para a mesma O.S.
        $lockKey = "whatsapp_os_notificada_{$os->id}";
        if (Cache::has($lockKey)) {
            Log::info("Aviso WhatsApp para a O.S. {$os->numero_os} (#{$os->id}) já foi enviado. Evitando envio repetido.");
            return;
        }
        Cache::put($lockKey, true, now()->addDays(30));

        $dataInst = Carbon::parse($pedido->data_instalacao)->format('d/m/Y');
        $horaInst = $pedido->hora_instalacao ? substr($pedido->hora_instalacao, 0, 5) : 'A combinar';
        $dataRet = Carbon::parse($pedido->data_retirada)->format('d/m/Y');

        $tendasLista = '';
        foreach ($pedido->tendas as $t) {
            $tendasLista .= "• {$t->pivot->quantidade}x {$t->nome} ({$t->tamanho})\n";
        }

        $dadosResp = '';
        if ($pedido->nome_responsavel) {
            $dadosResp = "👤 *Responsável / Solicitante:* {$pedido->nome_responsavel}" . ($pedido->telefone_responsavel ? " ({$pedido->telefone_responsavel})" : "") . "\n";
        }

        $linhaEvento = '';
        if ($pedido->data_evento) {
            $linhaEvento = "🎉 *Data do Evento:* " . Carbon::parse($pedido->data_evento)->format('d/m/Y') . "\n";
        }

        $instrucao = match($os->tipo) {
            'instalacao' => "📲 Acesse o sistema pelo celular para clicar em *'Em Processo de Instalação'* assim que iniciar!",
            'retirada' => "🚚 Ordem de serviço de retirada liberada! Clique em *'A Caminho da Retirada'* ao se deslocar ao local.",
            'conclusao' => "🎉 Comprovante de encerramento emitido! Tendas retornadas com sucesso ao estoque.",
            default => "📲 Acompanhe e registre o andamento diretamente pelo sistema!",
        };

        $msg = "🚨 *ORDEM DE SERVIÇO EMITIDA: {$os->numero_os}*\n"
             . "📌 *Finalidade:* {$os->tipo_label}\n\n"
             . "Olá *{$pedido->montador->name}*, uma O.S. do Pedido #{$pedido->id} foi gerada para você:\n\n"
             . "👤 *Cliente:* {$pedido->cliente->nome}\n"
             . "📞 *Telefone:* {$pedido->cliente->telefone}\n"
             . $dadosResp
             . "📅 *Data de Montagem:* {$dataInst} às {$horaInst}\n"
             . $linhaEvento
             . "🔄 *Data Prevista de Retirada:* {$dataRet}\n"
             . "📍 *Local:* {$pedido->endereco_instalacao}" . ($pedido->cidade_instalacao ? " - {$pedido->cidade_instalacao}" : "") . "\n\n"
             . "⛺ *Tendas:*\n{$tendasLista}\n"
             . "📝 *Instruções:* " . ($pedido->observacoes ?: 'Sem observações adicionais') . "\n\n"
             . $instrucao;

        $this->whatsapp->enviarMensagem($pedido->montador->whatsapp, $msg);
    }

    /**
     * Serve arquivos e fotos do storage de forma direta e segura,
     * garantindo 100% de compatibilidade em ambientes de hospedagem compartilhada (Hostinger).
     */
    public function servirArquivoStorage(string $path)
    {
        // Proteção contra Directory Traversal
        $path = str_replace(['../', '..\\', '..'], '', ltrim($path, '/'));

        // Normalização de prefixos comuns
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, 8);
        }
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        // 1. Procura no disco 'public' oficial do Laravel Storage
        if (Storage::disk('public')->exists($path)) {
            $fullPath = Storage::disk('public')->path($path);
            if (file_exists($fullPath) && is_file($fullPath)) {
                return response()->file($fullPath, [
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        // 2. Procura diretamente em storage/app/public/{path}
        $appPublicPath = storage_path('app/public/' . $path);
        if (file_exists($appPublicPath) && is_file($appPublicPath)) {
            return response()->file($appPublicPath, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // 3. Procura diretamente em storage/app/{path}
        $appPath = storage_path('app/' . $path);
        if (file_exists($appPath) && is_file($appPath)) {
            return response()->file($appPath, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // 4. Procura diretamente em public/storage/{path}
        $pubStorage = public_path('storage/' . $path);
        if (file_exists($pubStorage) && is_file($pubStorage)) {
            return response()->file($pubStorage, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        // 5. Procura diretamente em public/{path}
        $pubDirect = public_path($path);
        if (file_exists($pubDirect) && is_file($pubDirect)) {
            return response()->file($pubDirect, [
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        abort(404, 'Arquivo não encontrado.');
    }
}
