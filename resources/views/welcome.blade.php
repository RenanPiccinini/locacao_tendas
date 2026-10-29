<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Locação de Tendas — Estruturas para Eventos, Festas e Feiras</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#030712">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Lucide Icons & Tailwind Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-bg {
            background-image: radial-gradient(circle at 50% 20%, rgba(37, 99, 235, 0.15) 0%, transparent 60%),
                              radial-gradient(circle at 80% 80%, rgba(79, 70, 229, 0.12) 0%, transparent 50%),
                              linear-gradient(to bottom, #030712 0%, #090d16 50%, #030712 100%);
        }
        .glow-text {
            text-shadow: 0 0 35px rgba(59, 130, 246, 0.4);
        }
        .glass-nav {
            background: rgba(3, 7, 18, 0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-6px);
            border-color: rgba(59, 130, 246, 0.4);
            box-shadow: 0 20px 40px -15px rgba(37, 99, 235, 0.25);
        }
        .badge-gradient {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.2) 0%, rgba(79, 70, 229, 0.2) 100%);
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
    </style>
</head>
<body class="bg-[#030712] text-slate-100 selection:bg-blue-600 selection:text-white overflow-x-hidden">

    <!-- Header Fixo Moderno -->
    <header class="fixed top-0 left-0 right-0 z-50 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 font-extrabold text-xl text-white group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                </div>
                <span class="tracking-tight text-white">Tenda<span class="text-blue-500">Tech</span> Eventos</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
                <a href="#estruturas" class="hover:text-blue-400 transition">Estruturas</a>
                <a href="#diferenciais" class="hover:text-blue-400 transition">Vantagens</a>
                <a href="#como-funciona" class="hover:text-blue-400 transition">Como Funciona</a>
                <a href="#depoimentos" class="hover:text-blue-400 transition">Projetos</a>
                <a href="#faq" class="hover:text-blue-400 transition">Dúvidas</a>
            </nav>

            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center gap-2">
                        <span>Painel do Sistema</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-blue-600/10 hover:bg-blue-600/20 border border-blue-500/30 text-blue-400 hover:text-white font-bold text-sm transition">
                        Área de Membros / Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- HERO SECTION ELEGANTE COM FOTO REAL DE EVENTO -->
    <section class="relative pt-32 pb-20 lg:pt-44 lg:pb-32 overflow-hidden hero-bg">
        <!-- Detalhes de iluminação sutil -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[900px] h-[500px] bg-blue-600/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Coluna de Texto -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full badge-gradient text-blue-400 text-xs sm:text-sm font-bold tracking-wide">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                        <span>Locação Completa com Montagem & Gestão Digital</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-6xl font-black tracking-tight text-white leading-[1.1] glow-text">
                        A Cobertura Perfeita para seu <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400">Evento Inesquecível</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 max-w-xl font-normal leading-relaxed">
                        Tendas piramidais, articuladas, galpões e tablados com laudo ART, lona blackout anti-chamas e equipe técnica especializada. Controle operacional 100% integrado via WhatsApp.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold text-base shadow-xl shadow-blue-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>Acessar Painel / Fazer Pedido</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#estruturas" class="w-full sm:w-auto px-7 py-4 rounded-xl bg-slate-900/90 hover:bg-slate-800 border border-slate-700/80 text-slate-200 font-bold text-base transition flex items-center justify-center gap-2">
                            Ver Tendas & Medidas
                        </a>
                    </div>

                    <!-- Indicadores Rápidos -->
                    <div class="grid grid-cols-3 gap-4 pt-8 border-t border-slate-800/80">
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-white">+1.200</div>
                            <div class="text-xs text-slate-400 mt-0.5">Montagens Realizadas</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-blue-400">100%</div>
                            <div class="text-xs text-slate-400 mt-0.5">Segurança & Laudo ART</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-cyan-400">24/7</div>
                            <div class="text-xs text-slate-400 mt-0.5">Suporte WhatsApp</div>
                        </div>
                    </div>
                </div>

                <!-- Coluna de Imagem Real com Efeito Card 3D -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-blue-900/30 border border-slate-700/60 group">
                        <!-- Foto Real de Tenda Iluminada em Evento -->
                        <img src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1200&q=80" 
                             alt="Tenda montada para evento com iluminação cênica" 
                             class="w-full h-[450px] object-cover group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
                        
                        <div class="absolute bottom-6 left-6 right-6 p-4 rounded-2xl bg-slate-900/80 backdrop-blur-md border border-white/10 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Qualidade Premium</span>
                                <h4 class="text-sm font-bold text-white">Casamentos, Feiras & Grandes Festivais</h4>
                            </div>
                            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CATÁLOGO DE ESTRUTURAS COM FOTOS REAIS -->
    <section id="estruturas" class="py-24 relative z-10 bg-slate-950/60 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Catálogo de Coberturas</span>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Tendas & Estruturas Modulares</h2>
                <p class="text-slate-400 text-sm sm:text-base">Equipamentos higienizados, lonas antichamas e estrutura com fixação técnica certificada.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Tenda Piramidal 5x5m -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=80" 
                             alt="Tenda Piramidal 5x5 branca montada em gramado" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-blue-600/90 text-white rounded-full text-xs font-black shadow-md">
                            5x5 Metros (25m²)
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Tenda Piramidal 5x5m</h3>
                            <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                                Estrutura clássica em aço galvanizado a fogo. Lona anti-chamas com proteção UV e calhas de escoamento. Perfeita para aniversários, estandes e áreas de buffet.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Capacidade recomendada:</span>
                            <span class="text-cyan-400 font-bold">30 a 45 pessoas</span>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tenda Piramidal 10x10m -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=800&q=80" 
                             alt="Grande tenda para casamento com iluminação especial" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-indigo-600/90 text-white rounded-full text-xs font-black shadow-md">
                            10x10 Metros (100m²)
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Tenda Piramidal 10x10m</h3>
                            <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                                Grande porte sem colunas no centro! Vão livre total com 100m² para acomodar palco, pista de dança, mesas de convidados e grandes recepções corporativas.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Capacidade recomendada:</span>
                            <span class="text-indigo-400 font-bold">120 a 160 pessoas</span>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tenda Sanfonada Articulada -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col">
                    <div class="relative h-60 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80" 
                             alt="Tenda para feiras e eventos ao ar livre" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-cyan-600/90 text-white rounded-full text-xs font-black shadow-md">
                            3x3 Metros (9m²)
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Tenda Sanfonada Articulada</h3>
                            <p class="text-slate-400 text-xs mt-2 leading-relaxed">
                                Agilidade máxima com montagem ultrarrápida. Excelente para pontos de credenciamento, postos médicos, estandes de vendas e eventos esportivos.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-slate-800 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Montagem Express:</span>
                            <span class="text-emerald-400 font-bold">Menos de 5 minutos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TECNOLOGIA & GESTÃO VIA WHATSAPP -->
    <section id="diferenciais" class="py-24 relative z-10 border-t border-slate-900 bg-slate-900/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="space-y-6">
                    <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Inovação Operacional</span>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight">
                        Sistema 100% Conectado com o WhatsApp da Sua Equipe
                    </h2>
                    <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                        Nossa plataforma revoluciona o setor de locação de coberturas. Toda a comunicação entre atendimento, cliente e montadores de campo acontece em tempo real de forma automática.
                    </p>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Ordem de Serviço Direta no WhatsApp</h4>
                                <p class="text-slate-400 text-xs">Ao confirmar o pedido, o montador recebe a O.S. com endereço, cliente e itens no celular.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Vistoria e Fotos em Tempo Real</h4>
                                <p class="text-slate-400 text-xs">Montador fotografa a tenda instalada e o sistema programa a O.S. de retirada automaticamente.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center shrink-0 mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Lembretes Toda Segunda e no Dia da Montagem</h4>
                                <p class="text-slate-400 text-xs">Cron automático avisa toda a equipe sobre os compromissos semanais e horários do dia.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Visual do Fluxo -->
                <div class="p-8 rounded-3xl glass-card border border-blue-500/20 shadow-2xl relative">
                    <div class="space-y-4">
                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-600/30 text-blue-400 flex items-center justify-center font-bold">1</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Pedido Cadastrado</div>
                                <div class="text-slate-400">Tendas reservadas e O.S. gerada na hora</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold">2</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Notificação no WhatsApp</div>
                                <div class="text-slate-400">Montador avisado com detalhes do pedido</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-purple-600/30 text-purple-400 flex items-center justify-center font-bold">3</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Fotos & O.S. de Retorno</div>
                                <div class="text-slate-400">Instalação concluída e estoque reabastecido após coleta</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- GALERIA REAL DE EVENTOS / PROJETOS -->
    <section id="depoimentos" class="py-24 relative z-10 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Nossas Montagens</span>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Galeria de Eventos Reais</h2>
                <p class="text-slate-400 text-sm sm:text-base">Confira algumas das estruturas montadas pela nossa equipe técnica.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="rounded-2xl overflow-hidden h-64 group relative">
                    <img src="https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=600&q=80" alt="Evento casamento" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                        <span class="text-xs font-bold text-white">Casamentos ao Ar Livre</span>
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden h-64 group relative">
                    <img src="https://images.unsplash.com/photo-1475721027785-f74eccf877e2?auto=format&fit=crop&w=600&q=80" alt="Evento corporativo" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                        <span class="text-xs font-bold text-white">Feiras Corporativas</span>
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden h-64 group relative">
                    <img src="https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=600&q=80" alt="Festival de música com coberturas" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                        <span class="text-xs font-bold text-white">Festivais & Gastronomia</span>
                    </div>
                </div>
                <div class="rounded-2xl overflow-hidden h-64 group relative">
                    <img src="https://images.unsplash.com/photo-1505373877841-8d25f7d46678?auto=format&fit=crop&w=600&q=80" alt="Cobertura iluminada" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                        <span class="text-xs font-bold text-white">Eventos Noturnos</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ / DÚVIDAS FREQUENTES -->
    <section id="faq" class="py-24 relative z-10 border-t border-slate-900 bg-slate-950/60">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-3">
                <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Tire Suas Dúvidas</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white">Perguntas Frequentes</h2>
            </div>

            <div class="space-y-4">
                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">As tendas possuem proteção contra chuva forte e vento?</h4>
                    <p class="text-slate-400 text-sm mt-2">Sim! Nossas tendas são confeccionadas em lona PVC vulcanizada, 100% impermeável e antichamas, com ancoragem profissional com estacas de aço ou pesos conforme o solo.</p>
                </div>

                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">Com quanta antecedência devo solicitar a locação?</h4>
                    <p class="text-slate-400 text-sm mt-2">Recomendamos reservar com pelo menos 5 a 15 dias de antecedência para garantir a disponibilidade do tamanho exato em nosso estoque.</p>
                </div>

                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">Quem realiza o transporte e a montagem?</h4>
                    <p class="text-slate-400 text-sm mt-2">Nossa própria equipe técnica realiza o transporte, descarregamento, montagem completa e a retirada após o término do evento.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="py-20 relative z-10 border-t border-slate-900">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-3xl sm:text-5xl font-black text-white">Pronto para transformar seu evento?</h2>
            <p class="text-slate-400 max-w-xl mx-auto text-sm sm:text-base">Acesse nosso sistema para emitir ordens de serviço, gerenciar calendários ou fazer login administrativo.</p>
            <div class="pt-2">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-9 py-4 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-extrabold text-base shadow-xl shadow-blue-500/30 transition">
                    <span>Acessar o Sistema Agora</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="border-t border-slate-900 py-10 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                </div>
                <span class="font-bold text-white">TendaTech Locação de Tendas</span>
            </div>

            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} TendaTech. Soluções Inteligentes em Estruturas e Coberturas. Todos os direitos reservados.
            </p>

            <a href="{{ route('login') }}" class="text-xs font-semibold text-blue-400 hover:text-white transition">
                Área Restrita / Login &rarr;
            </a>
        </div>
    </footer>

</body>
</html>
