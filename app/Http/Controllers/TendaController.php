<?php

namespace App\Http\Controllers;

use App\Models\Tenda;
use App\Services\DisponibilidadeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TendaController extends Controller
{
    protected DisponibilidadeService $disponibilidadeService;

    public function __construct(DisponibilidadeService $disponibilidadeService)
    {
        $this->disponibilidadeService = $disponibilidadeService;
    }

    public function index(Request $request): View
    {
        $dataInicio = $request->query('data_inicio', date('Y-m-d'));
        $dataFim = $request->query('data_fim', $dataInicio);

        $dadosDisponibilidade = $this->disponibilidadeService->consultarDisponibilidade($dataInicio, $dataFim);
        $tendas = $dadosDisponibilidade['tendas'];

        return view('tendas.index', compact('tendas', 'dadosDisponibilidade'));
    }

    public function create(): View
    {
        return view('tendas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tamanho' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'quantidade_total' => 'required|integer|min:1',
            'valor_diaria' => 'nullable|numeric|min:0',
        ]);

        $validated['quantidade_disponivel'] = $validated['quantidade_total'];

        Tenda::create($validated);

        return redirect()->route('tendas.index')->with('success', 'Tenda cadastrada no estoque com sucesso!');
    }

    public function edit(Tenda $tenda): View
    {
        return view('tendas.edit', compact('tenda'));
    }

    public function update(Request $request, Tenda $tenda): RedirectResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tamanho' => 'required|string|max:50',
            'descricao' => 'nullable|string',
            'quantidade_total' => 'required|integer|min:1',
            'quantidade_disponivel' => 'required|integer|min:0',
            'valor_diaria' => 'nullable|numeric|min:0',
        ]);

        $tenda->update($validated);

        return redirect()->route('tendas.index')->with('success', 'Tenda atualizada com sucesso!');
    }

    public function destroy(Tenda $tenda): RedirectResponse
    {
        $tenda->delete();
        return redirect()->route('tendas.index')->with('success', 'Tenda excluída do estoque!');
    }
}
