<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                    Calendário de Instalações e Retiradas
                </h2>
                <p class="text-sm text-gray-500 dark:text-emerald-300/70">
                    @if(Auth::user()->isAdmin())
                        Visão completa de todos os montadores e locações da empresa
                    @else
                        Sua agenda exclusiva de montagens e coletas
                    @endif
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-4 text-xs font-semibold">
                <!-- Legenda -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1.5 text-gray-700 dark:text-emerald-200">
                        <span class="w-3.5 h-3.5 rounded-full bg-emerald-600 inline-block"></span>
                        <span>Instalação</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-gray-700 dark:text-emerald-200">
                        <span class="w-3.5 h-3.5 rounded-full bg-purple-600 inline-block"></span>
                        <span>Retirada</span>
                    </div>
                </div>

                @if(Auth::user()->isAdmin())
                    <a href="{{ route('tendas.index') }}" class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-950/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 font-semibold text-xs rounded-lg transition flex items-center gap-1.5 border border-emerald-200 dark:border-emerald-800/40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Consultar Disponibilidade do Estoque
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <!-- FullCalendar CSS & JS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/pt-br.global.min.js"></script>

    <style>
        .dark .fc {
            --fc-page-bg-color: #031d14;
            --fc-neutral-bg-color: #02140e;
            --fc-neutral-text-color: #a7f3d0;
            --fc-border-color: #083d2c;
            --fc-button-text-color: #fff;
            --fc-button-bg-color: #047857;
            --fc-button-border-color: #059669;
            --fc-button-hover-bg-color: #059669;
            --fc-button-hover-border-color: #10b981;
            --fc-button-active-bg-color: #065f46;
            --fc-button-active-border-color: #047857;
            --fc-today-bg-color: rgba(16, 185, 129, 0.15);
        }
        .dark .fc .fc-col-header-cell-cushion,
        .dark .fc .fc-daygrid-day-number {
            color: #ecfdf5;
        }
        .dark .fc .fc-toolbar-title {
            color: #ffffff;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-[#031d14] p-6 rounded-xl shadow-sm border border-gray-100 dark:border-[#083d2c] transition-colors">
                <div id="calendar" class="min-h-[650px]"></div>
            </div>
        </div>
    </div>

    <!-- Modal Detalhe Rápido do Evento -->
    <div id="modal-evento" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white dark:bg-[#031d14] rounded-xl max-w-md w-full p-6 shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
            <button onclick="document.getElementById('modal-evento').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 dark:text-emerald-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
            <div class="flex items-center gap-2 mb-2">
                <span id="evento-badge-tipo" class="px-2.5 py-0.5 rounded text-xs font-bold"></span>
                <span id="evento-badge-status" class="px-2.5 py-0.5 rounded text-xs font-bold bg-gray-100 dark:bg-[#02140e] text-gray-700 dark:text-emerald-200 border border-transparent dark:border-emerald-700/40"></span>
            </div>
            <h3 id="evento-titulo" class="text-lg font-bold text-gray-900 dark:text-white mb-3"></h3>
            
            <div class="space-y-2 text-sm text-gray-600 dark:text-emerald-100/90 mb-4">
                <div><strong class="text-gray-900 dark:text-white">Cliente:</strong> <span id="evento-cliente"></span></div>
                <div><strong class="text-gray-900 dark:text-white">Endereço:</strong> <span id="evento-endereco"></span></div>
                <div><strong class="text-gray-900 dark:text-white">Montador:</strong> <span id="evento-montador"></span></div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-100 dark:border-[#083d2c]">
                <button onclick="document.getElementById('modal-evento').classList.add('hidden')" class="px-4 py-2 bg-gray-100 dark:bg-[#063325] text-gray-700 dark:text-emerald-200 font-semibold rounded-lg text-xs">Fechar</button>
                <a id="evento-link-pedido" href="#" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold rounded-lg text-xs transition">Ver Pedido Completo</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                initialView: 'dayGridMonth',
                headerToolbar: window.innerWidth < 768 ? {
                    left: 'prev,next',
                    center: 'title',
                    right: 'dayGridMonth,listMonth'
                } : {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,listMonth'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    list: 'Lista'
                },
                events: '{{ route("calendario.eventos") }}',
                eventClick: function(info) {
                    info.jsEvent.preventDefault();
                    var p = info.event.extendedProps;
                    
                    document.getElementById('evento-titulo').textContent = info.event.title;
                    document.getElementById('evento-cliente').textContent = p.cliente_nome || '-';
                    document.getElementById('evento-endereco').textContent = p.endereco || '-';
                    document.getElementById('evento-montador').textContent = p.montador_nome || 'Não definido';
                    document.getElementById('evento-link-pedido').href = p.pedido_url;

                    var badgeTipo = document.getElementById('evento-badge-tipo');
                    if (p.tipo === 'instalacao') {
                        badgeTipo.textContent = 'Instalação';
                        badgeTipo.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                    } else {
                        badgeTipo.textContent = 'Retirada';
                        badgeTipo.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800 dark:bg-purple-950 dark:text-purple-300';
                    }

                    document.getElementById('evento-badge-status').textContent = p.status_label || '';
                    document.getElementById('modal-evento').classList.remove('hidden');
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
