<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    Calendário de Instalações e Retiradas
                </h2>
                <p class="text-sm text-gray-500">
                    @if(Auth::user()->isAdmin())
                        Visão completa de todos os montadores e locações da empresa
                    @else
                        Sua agenda exclusiva de montagens e coletas
                    @endif
                </p>
            </div>

            <!-- Legenda -->
            <div class="flex items-center gap-4 text-xs font-semibold">
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-blue-600 inline-block"></span>
                    <span>Instalação / Montagem</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3.5 h-3.5 rounded-full bg-purple-600 inline-block"></span>
                    <span>Retirada / Desmontagem</span>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- FullCalendar CSS & JS via CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.15/locales/pt-br.global.min.js"></script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <div id="calendar" class="min-h-[650px]"></div>
            </div>
        </div>
    </div>

    <!-- Modal Detalhe Rápido do Evento -->
    <div id="modal-evento" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl relative">
            <button onclick="document.getElementById('modal-evento').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
            <div class="flex items-center gap-2 mb-2">
                <span id="evento-badge-tipo" class="px-2.5 py-0.5 rounded text-xs font-bold"></span>
                <span id="evento-badge-status" class="px-2.5 py-0.5 rounded text-xs font-bold bg-gray-100 text-gray-700"></span>
            </div>
            <h3 id="evento-titulo" class="text-lg font-bold text-gray-900 mb-3"></h3>
            
            <div class="space-y-2 text-sm text-gray-600 mb-4">
                <div><strong>Cliente:</strong> <span id="evento-cliente"></span></div>
                <div><strong>Endereço:</strong> <span id="evento-endereco"></span></div>
                <div><strong>Montador:</strong> <span id="evento-montador"></span></div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t">
                <button onclick="document.getElementById('modal-evento').classList.add('hidden')" class="px-4 py-2 bg-gray-100 text-gray-700 font-semibold rounded-lg text-xs">Fechar</button>
                <a id="evento-link" href="#" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs">
                    Abrir Pedido Completo &rarr;
                </a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'pt-br',
                initialView: window.innerWidth < 768 ? 'timeGridDay' : 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia',
                    list: 'Lista'
                },
                events: '{{ route("calendario.eventos") }}',
                navLinks: true,
                editable: false,
                dayMaxEvents: true,
                eventClick: function(info) {
                    info.jsEvent.preventDefault();

                    const props = info.event.extendedProps;
                    document.getElementById('evento-titulo').textContent = info.event.title;
                    document.getElementById('evento-cliente').textContent = props.cliente || '-';
                    document.getElementById('evento-endereco').textContent = props.endereco || '-';
                    document.getElementById('evento-montador').textContent = props.montador || '-';
                    document.getElementById('evento-badge-status').textContent = props.status || '';

                    const badgeTipo = document.getElementById('evento-badge-tipo');
                    badgeTipo.textContent = props.tipo;
                    if (props.tipo === 'Instalação') {
                        badgeTipo.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-800';
                    } else {
                        badgeTipo.className = 'px-2.5 py-0.5 rounded text-xs font-bold bg-purple-100 text-purple-800';
                    }

                    document.getElementById('evento-link').href = info.event.url;
                    document.getElementById('modal-evento').classList.remove('hidden');
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>

