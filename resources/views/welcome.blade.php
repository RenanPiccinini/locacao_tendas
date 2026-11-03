<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tendas Eldorado — Venda e Locação de Tendas Piramidais | São José do Rio Preto e Região</title>
    <meta name="description" content="Tendas Eldorado em São José do Rio Preto. Locação mensal e diária de tendas piramidais, coberturas para eventos, feiras e indústrias. Lonas antichamas e laudos de segurança.">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#021811">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

    <!-- OpenGraph / Social Media -->
    <meta property="og:title" content="Tendas Eldorado — Venda e Locação de Tendas">
    <meta property="og:description" content="Sua cobertura perfeita para todas as ocasiões. Locação mensal e diária de tendas piramidais de alta resistência.">
    <meta property="og:image" content="{{ asset('images/logo.png') }}">
    <meta property="og:url" content="https://locacao-tendas.piccininidigitalsolutions.com.br">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Tailwind & Scripts (com fallback caso build não exista no servidor) -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .hero-bg {
            background-color: #021811;
            background-image: 
                radial-gradient(circle at 50% 15%, rgba(16, 185, 129, 0.18) 0%, transparent 60%),
                radial-gradient(circle at 85% 85%, rgba(245, 158, 11, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 10% 90%, rgba(5, 150, 105, 0.15) 0%, transparent 50%),
                linear-gradient(to bottom, #021811 0%, #032418 50%, #021811 100%);
        }
        .glow-gold {
            text-shadow: 0 0 35px rgba(245, 158, 11, 0.35);
        }
        .glass-nav {
            background: rgba(2, 24, 17, 0.90);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.2);
        }
        .glass-card {
            background: rgba(3, 35, 26, 0.75);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(16, 185, 129, 0.25);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-6px);
            border-color: rgba(245, 158, 11, 0.6);
            box-shadow: 0 20px 40px -15px rgba(245, 158, 11, 0.25);
        }
        .badge-gold {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(16, 185, 129, 0.2) 100%);
            border: 1px solid rgba(245, 158, 11, 0.4);
        }
        .insta-gradient {
            background: radial-gradient(circle at 30% 107%, #fdf497 0%, #fdf497 5%, #fd5949 45%, #d6249f 60%, #285AEB 90%);
        }
    </style>
</head>
<body class="bg-[#021811] text-slate-100 selection:bg-amber-500 selection:text-slate-950 overflow-x-hidden">

    <!-- Header Fixo Moderno -->
    <header class="fixed top-0 left-0 right-0 z-40 glass-nav" x-data="{ mobileNav: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            <!-- Logo Oficial Tendas Eldorado -->
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Tendas Eldorado" class="w-12 h-12 rounded-full object-cover border-2 border-amber-400 shadow-md shadow-amber-500/20 group-hover:scale-105 transition">
                <div class="leading-tight">
                    <span class="tracking-tight text-white font-extrabold text-lg sm:text-xl block">
                        Tendas <span class="text-amber-400">Eldorado</span>
                    </span>
                    <span class="text-[10px] sm:text-xs text-emerald-400 font-bold uppercase tracking-wider block">
                        Venda & Locação
                    </span>
                </div>
            </a>

            <!-- Menu de Navegação Desktop -->
            <nav class="hidden lg:flex items-center gap-7 text-sm font-semibold text-emerald-100/90">
                <a href="#estruturas" class="hover:text-amber-400 transition">Estruturas</a>
                <a href="#locacao-mensal" class="hover:text-amber-400 transition">Locação Mensal</a>
                <a href="#fotos-reais" class="hover:text-amber-400 transition">Fotos Reais</a>
                <a href="#instagram" class="hover:text-amber-400 transition">Instagram</a>
                <a href="#diferenciais" class="hover:text-amber-400 transition">Vantagens</a>
                <a href="#faq" class="hover:text-amber-400 transition">Dúvidas</a>
            </nav>

            <!-- Ações Rápidas: Instagram + WhatsApp + Login + Botão Mobile -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Botão Ver Instagram -->
                <a href="https://www.instagram.com/tendaseldorado/" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white insta-gradient hover:opacity-95 shadow-md shadow-pink-900/30 transition transform hover:scale-105"
                   title="Siga @tendaseldorado no Instagram">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span class="hidden sm:inline">Instagram</span>
                </a>

                <!-- Botão WhatsApp Header -->
                <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Vim+pelo+site+da+Tendas+Eldorado+e+gostaria+de+solicitar+um+or%C3%A7amento." 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-900/40 transition">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                    </svg>
                    <span>(17) 99142-9132</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/20 transition flex items-center gap-1.5">
                        <span>Painel</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-xl bg-[#04281e] hover:bg-[#073c2e] border border-emerald-700/60 text-emerald-200 hover:text-white font-bold text-xs transition">
                        Área Restrita
                    </a>
                @endauth

                <!-- Botão Hamburger Mobile (Aparece em telas < lg) -->
                <button @click="mobileNav = !mobileNav" 
                        type="button" 
                        aria-label="Menu principal" 
                        class="lg:hidden p-2 rounded-xl text-emerald-300 hover:text-white hover:bg-emerald-800/40 border border-emerald-700/50 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path x-show="!mobileNav" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path x-show="mobileNav" x-cloak stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu Dropdown Mobile do Site -->
        <div x-show="mobileNav" 
             x-cloak 
             @click.away="mobileNav = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="lg:hidden bg-[#021811]/98 border-b border-emerald-700/50 px-4 py-5 shadow-2xl backdrop-blur-xl">
            <nav class="flex flex-col space-y-2">
                <a href="#estruturas" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-amber-400">⛺</span> Estruturas & Medidas
                </a>
                <a href="#locacao-mensal" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-amber-400">📅</span> Locação Mensal & Diária
                </a>
                <a href="#fotos-reais" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-amber-400">📸</span> Fotos Reais das Montagens
                </a>
                <a href="#instagram" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-pink-400">📸</span> Instagram Oficial @tendaseldorado
                </a>
                <a href="#diferenciais" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-amber-400">🌟</span> Vantagens & Laudos de Segurança
                </a>
                <a href="#faq" @click="mobileNav = false" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-bold text-emerald-100 hover:bg-[#04291c] hover:text-amber-400 transition">
                    <span class="text-amber-400">❓</span> Perguntas Frequentes (FAQ)
                </a>

                <div class="pt-3 border-t border-emerald-800/50 flex flex-col gap-2.5">
                    <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Vim+pelo+site+da+Tendas+Eldorado+e+gostaria+de+solicitar+um+or%C3%A7amento." 
                       target="_blank" 
                       class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-extrabold text-sm text-center flex items-center justify-center gap-2 shadow-md">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        <span>WhatsApp: (17) 99142-9132</span>
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full py-2.5 px-4 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-sm text-center">
                            Acessar Painel Gerenciador
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full py-2 px-4 rounded-xl bg-[#04281e] text-emerald-200 border border-emerald-700/60 font-bold text-xs text-center">
                            Área Restrita (Acesso ao Sistema)
                        </a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <!-- HERO SECTION ELEGANTE COM FOTO REAL DAS TENDAS -->
    <section class="relative pt-32 pb-20 lg:pt-44 lg:pb-32 overflow-hidden hero-bg">
        <!-- Detalhes de iluminação sutil (Verde & Dourado) -->
        <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[950px] h-[550px] bg-emerald-500/15 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute top-1/3 -right-20 w-80 h-80 bg-amber-500/15 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                <!-- Coluna de Texto Principal -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full badge-gold text-amber-300 text-xs sm:text-sm font-bold tracking-wide">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-ping"></span>
                        <span>Sua Cobertura Perfeita para Todas as Ocasiões</span>
                    </div>

                    <h1 class="text-4xl sm:text-6xl lg:text-6xl font-black tracking-tight text-white leading-[1.1] glow-gold">
                        Tendas Piramidais para <span class="bg-clip-text text-transparent bg-gradient-to-r from-amber-400 via-yellow-200 to-amber-500">Eventos & Indústrias</span>
                    </h1>

                    <p class="text-base sm:text-lg text-emerald-100/90 max-w-xl font-normal leading-relaxed">
                        Locação mensal e diária de tendas piramidais de alta durabilidade com lona blackout anti-chamas, proteção contra sol e chuva, montagem ágil e laudos de segurança. Atendemos São José do Rio Preto e toda a região.
                    </p>

                    <!-- Botões de Ação Principais -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-3.5 pt-2">
                        <!-- Botão WhatsApp Principal -->
                        <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Vim+pelo+site+da+Tendas+Eldorado+e+gostaria+de+solicitar+um+or%C3%A7amento+de+loca%C3%A7%C3%A3o." 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="w-full sm:w-auto px-7 py-4 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-extrabold text-base shadow-xl shadow-emerald-950/60 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2.5">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                            </svg>
                            <span>Orçamento no WhatsApp</span>
                        </a>

                        <!-- Botão Instagram -->
                        <a href="https://www.instagram.com/tendaseldorado/" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="w-full sm:w-auto px-6 py-4 rounded-xl insta-gradient text-white font-bold text-base shadow-xl shadow-pink-950/50 hover:opacity-95 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span>Ver Instagram</span>
                        </a>

                        <!-- Botão Catálogo -->
                        <a href="#estruturas" class="w-full sm:w-auto px-6 py-4 rounded-xl bg-[#04281e] hover:bg-[#073c2e] border border-emerald-600/40 text-emerald-200 font-bold text-base transition flex items-center justify-center gap-2">
                            Ver Medidas
                        </a>
                    </div>

                    <!-- Indicadores Rápidos -->
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-emerald-900/60">
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-amber-400">100%</div>
                            <div class="text-xs text-emerald-200/70 mt-0.5">Proteção Sol & Chuva</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-white">Mensal</div>
                            <div class="text-xs text-emerald-200/70 mt-0.5">E Diária c/ Desconto</div>
                        </div>
                        <div>
                            <div class="text-2xl sm:text-3xl font-black text-emerald-400">São José</div>
                            <div class="text-xs text-emerald-200/70 mt-0.5">do Rio Preto & Região</div>
                        </div>
                    </div>
                </div>

                <!-- Coluna de Imagem Real Tendas Eldorado -->
                <div class="lg:col-span-5 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-emerald-950/80 border-2 border-amber-400/50 group">
                        <!-- Foto Real Tendas Eldorado em Estacionamento -->
                        <img src="{{ asset('images/tenda_estacionamento.jpg') }}" 
                             alt="Tenda Piramidal Tendas Eldorado montada com fechamento lateral" 
                             class="w-full h-[460px] object-cover group-hover:scale-105 transition duration-700">
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-[#021811] via-[#021811]/25 to-transparent"></div>
                        
                        <!-- Selo de Autenticidade -->
                        <div class="absolute top-4 left-4 px-3 py-1.5 rounded-full bg-slate-950/85 backdrop-blur-md border border-amber-400/60 flex items-center gap-2 text-xs font-bold text-amber-300">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-5 h-5 rounded-full object-cover">
                            <span>Foto Real Tendas Eldorado</span>
                        </div>

                        <!-- Card Flutuante Inferior com Contato Direto -->
                        <div class="absolute bottom-5 left-5 right-5 p-4 rounded-2xl bg-[#021811]/90 backdrop-blur-md border border-emerald-600/40 flex items-center justify-between">
                            <div>
                                <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Atendimento Imediato</span>
                                <h4 class="text-sm font-bold text-white">(17) 99142-9132 / (17) 3217-9349</h4>
                            </div>
                            <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Gostaria+de+solicitar+um+or%C3%A7amento+pelo+WhatsApp." 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-10 h-10 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center shadow-lg transition">
                                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SEÇÃO DESTAQUE: LOCAÇÃO MENSAL DE TENDAS PIRAMIDAIS -->
    <section id="locacao-mensal" class="py-20 relative z-10 bg-[#032016] border-t border-b border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- Imagem do Flyer Promocional Real -->
                <div class="lg:col-span-5">
                    <div class="rounded-3xl overflow-hidden border-2 border-amber-400 shadow-2xl shadow-emerald-950/90 group relative">
                        <img src="{{ asset('images/banner_piramidal.jpg') }}" 
                             alt="Locação Mensal de Tendas Piramidais Tendas Eldorado" 
                             class="w-full h-auto object-cover group-hover:scale-105 transition duration-500">
                    </div>
                </div>

                <!-- Detalhes da Locação Mensal -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full badge-gold text-amber-300 text-xs font-bold uppercase tracking-wider">
                        <span>Melhor Custo-Benefício para Médio e Longo Prazo</span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight">
                        Locação Mensal de <span class="text-amber-400">Tendas Piramidais</span>
                    </h2>

                    <p class="text-emerald-100/90 text-sm sm:text-base leading-relaxed">
                        Precisa expandir seu galpão, cobrir pátio de triagem, estacionamento de revendedora ou proteger obras e eventos contínuos? A Tendas Eldorado oferece contratos flexíveis com o menor custo por metro quadrado da região.
                    </p>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-2xl bg-[#021811] border border-emerald-700/50">
                            <div class="flex items-center gap-2.5 text-amber-400 font-bold text-sm mb-1">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Manutenção & Troca Inclusas</span>
                            </div>
                            <p class="text-xs text-emerald-200/70">Estruturas sempre revisadas, lonas limpas e suporte emergencial no local.</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-[#021811] border border-emerald-700/50">
                            <div class="flex items-center gap-2.5 text-amber-400 font-bold text-sm mb-1">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Fechamentos Laterais Disponíveis</span>
                            </div>
                            <p class="text-xs text-emerald-200/70">Cortinas com zíper e janelas transparentes para isolamento total contra intempéries.</p>
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row gap-3">
                        <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Tenho+interesse+na+loca%C3%A7%C3%A3o+mensal+de+tendas+piramidais." 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="px-6 py-3.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-sm shadow-lg shadow-amber-500/20 transition flex items-center justify-center gap-2">
                            <span>Consultar Valores Mensais no WhatsApp</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="tel:1732179349" class="px-5 py-3.5 rounded-xl bg-[#021811] hover:bg-[#04281e] border border-emerald-700/60 text-emerald-100 font-bold text-sm transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>Ligue: (17) 3217-9349</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- CATÁLOGO DE ESTRUTURAS COM FOTOS REAIS -->
    <section id="estruturas" class="py-24 relative z-10 bg-[#021811] border-t border-emerald-950">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">Catálogo Oficial</span>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Modelos & Estruturas para Locação</h2>
                <p class="text-emerald-200/80 text-sm sm:text-base">Equipamentos higienizados, lonas antichamas e estrutura com fixação técnica certificada.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1: Tenda Piramidal 10x10m -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col border border-emerald-700/40">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('images/tenda_estacionamento.jpg') }}" 
                             alt="Tenda Piramidal 10x10m Tendas Eldorado" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-amber-500 text-slate-950 rounded-full text-xs font-black shadow-md">
                            10x10 Metros (100m²)
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Tenda Piramidal 10x10m</h3>
                            <p class="text-emerald-200/80 text-xs mt-2 leading-relaxed">
                                Grande vão livre sem colunas centrais! Perfeita para casamentos, festivais, feiras de negócios, eventos automotivos e armazenagem industrial temporária.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-emerald-800/80 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-emerald-300">Capacidade média:</span>
                                <span class="text-amber-400 font-bold">120 a 160 pessoas</span>
                            </div>
                            <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Gostaria+de+or%C3%A7ar+a+Tenda+Piramidal+10x10m." 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Orçar 10x10 no WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Tendas Modulares em Fileira com Fechamento Lateral -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col border border-emerald-700/40">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('images/tendas_fileira.jpg') }}" 
                             alt="Tendas Modulares Tendas Eldorado com fechamento lateral" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-emerald-500 text-slate-950 rounded-full text-xs font-black shadow-md">
                            Módulos Acopláveis
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Módulos Conectados & Fechamento</h3>
                            <p class="text-emerald-200/80 text-xs mt-2 leading-relaxed">
                                Sistema modular acoplável com calhas de união para criar galerias contínuas de 50m, 100m ou mais. Fechamentos laterais totais ou parciais para isolamento acústico e térmico.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-emerald-800/80 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-emerald-300">Configuração:</span>
                                <span class="text-amber-400 font-bold">Sob Medida para o Local</span>
                            </div>
                            <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Gostaria+de+or%C3%A7ar+tendas+modulares+em+fileira." 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Orçar Módulos no WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tendas para Eventos & Cerimônias -->
                <div class="rounded-3xl overflow-hidden glass-card flex flex-col border border-emerald-700/40">
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ asset('images/banner_evento.jpg') }}" 
                             alt="Tendas para Eventos Tendas Eldorado" 
                             class="w-full h-full object-cover hover:scale-110 transition duration-500">
                        <div class="absolute top-4 right-4 px-3 py-1 bg-amber-500 text-slate-950 rounded-full text-xs font-black shadow-md">
                            Festas & Feiras
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                        <div>
                            <h3 class="text-xl font-bold text-white">Tendas para Eventos Sociais</h3>
                            <p class="text-emerald-200/80 text-xs mt-2 leading-relaxed">
                                Estrutura, conforto e proteção contra sol e chuva para casamentos, formaturas, confraternizações e feiras gastronômicas com lonas brancas impecáveis.
                            </p>
                        </div>
                        <div class="pt-4 border-t border-emerald-800/80 space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-emerald-300">Montagem:</span>
                                <span class="text-amber-400 font-bold">Rápida & Pontual</span>
                            </div>
                            <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Gostaria+de+solicitar+or%C3%A7amento+para+evento." 
                               target="_blank" 
                               rel="noopener noreferrer" 
                               class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                <span>Orçar Evento no WhatsApp</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- GALERIA REAL DE FOTOS TENDAS ELDORADO -->
    <section id="fotos-reais" class="py-24 relative z-10 border-t border-emerald-950 bg-[#032016]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-3">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">Nossas Montagens</span>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Galeria de Fotos Reais</h2>
                <p class="text-emerald-200/80 text-sm sm:text-base">Confira as estruturas de grande porte montadas pela nossa equipe técnica em campo.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Foto 1 -->
                <div class="rounded-3xl overflow-hidden group relative border border-emerald-700/50 shadow-xl bg-[#021811] h-80">
                    <img src="{{ asset('images/tenda_estacionamento.jpg') }}" alt="Tenda Piramidal Estacionamento" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021811] via-transparent to-transparent flex items-end p-5">
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Estacionamento & Pátio</span>
                            <span class="text-sm font-bold text-white">Tenda Piramidal c/ Fechamento</span>
                        </div>
                    </div>
                </div>

                <!-- Foto 2 -->
                <div class="rounded-3xl overflow-hidden group relative border border-emerald-700/50 shadow-xl bg-[#021811] h-80">
                    <img src="{{ asset('images/tendas_fileira.jpg') }}" alt="Fileira de Tendas Piramidais" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021811] via-transparent to-transparent flex items-end p-5">
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Módulos Acoplados</span>
                            <span class="text-sm font-bold text-white">Fileira com Calha & Vedação</span>
                        </div>
                    </div>
                </div>

                <!-- Foto 3 -->
                <div class="rounded-3xl overflow-hidden group relative border border-emerald-700/50 shadow-xl bg-[#021811] h-80">
                    <img src="{{ asset('images/banner_piramidal.jpg') }}" alt="Locação Mensal Tendas Piramidais" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021811] via-transparent to-transparent flex items-end p-5">
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Comunicação Oficial</span>
                            <span class="text-sm font-bold text-white">Locação Mensal & Diária</span>
                        </div>
                    </div>
                </div>

                <!-- Foto 4 -->
                <div class="rounded-3xl overflow-hidden group relative border border-emerald-700/50 shadow-xl bg-[#021811] h-80">
                    <img src="{{ asset('images/banner_evento.jpg') }}" alt="Tendas para Eventos" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-[#021811] via-transparent to-transparent flex items-end p-5">
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 uppercase tracking-wider block">Qualidade & Confiança</span>
                            <span class="text-sm font-bold text-white">Estruturas para Festas & Feiras</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botão Ver Mais no Instagram -->
            <div class="mt-12 text-center">
                <a href="https://www.instagram.com/tendaseldorado/" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2.5 px-8 py-4 rounded-xl insta-gradient text-white font-black text-sm sm:text-base shadow-xl shadow-pink-950/60 transition transform hover:scale-105">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                    <span>Ver Mais Fotos no Instagram @tendaseldorado</span>
                </a>
            </div>
        </div>
    </section>

    <!-- DESTAQUE INSTAGRAM: PERFIL OFICIAL -->
    <section id="instagram" class="py-20 relative z-10 border-t border-emerald-950 bg-[#021811]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="p-8 sm:p-12 rounded-3xl glass-card border-2 border-pink-500/30 text-center relative overflow-hidden shadow-2xl">
                <!-- Luz de fundo Instagram -->
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-pink-500/20 rounded-full blur-[100px] pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-amber-500/20 rounded-full blur-[100px] pointer-events-none"></div>

                <div class="relative z-10 space-y-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-24 h-24 rounded-full mx-auto object-cover border-4 border-amber-400 shadow-xl shadow-amber-500/20">
                    
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 text-xs font-bold mb-2">
                            <span>Perfil Oficial no Instagram</span>
                        </div>
                        <h2 class="text-3xl sm:text-4xl font-black text-white">@tendaseldorado</h2>
                        <p class="text-emerald-200/80 text-sm max-w-xl mx-auto mt-2">
                            Sua cobertura perfeita para todas as ocasiões. Acompanhe montagens semanais, vídeos em campo e novidades direto no nosso feed.
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-4 text-xs font-semibold text-emerald-300">
                        <span class="flex items-center gap-1">📍 Rua Pedro Martins 190, São José do Rio Preto</span>
                        <span>•</span>
                        <span class="flex items-center gap-1">📞 (17) 3217-9349</span>
                        <span>•</span>
                        <span class="flex items-center gap-1">📱 (17) 99142-9132</span>
                    </div>

                    <div class="pt-2">
                        <a href="https://www.instagram.com/tendaseldorado/" 
                           target="_blank" 
                           rel="noopener noreferrer" 
                           class="inline-flex items-center gap-2.5 px-9 py-4 rounded-xl insta-gradient text-white font-black text-base shadow-xl shadow-pink-900/40 hover:opacity-95 transition transform hover:scale-105">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span>Abrir Perfil no Instagram</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TECNOLOGIA & DIFERENCIAIS -->
    <section id="diferenciais" class="py-24 relative z-10 border-t border-emerald-950 bg-[#032016]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <div class="space-y-6">
                    <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">Excelência em Atendimento</span>
                    <h2 class="text-3xl sm:text-5xl font-black text-white leading-tight">
                        Sistema 100% Conectado com o WhatsApp da Equipe
                    </h2>
                    <p class="text-emerald-200/80 text-sm sm:text-base leading-relaxed">
                        Nossa plataforma revoluciona o setor de locação de coberturas. Toda a comunicação entre o atendimento e os montadores de campo acontece em tempo real de forma automática.
                    </p>

                    <div class="space-y-4 pt-2">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0 mt-1 border border-emerald-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Ordem de Serviço Direta no WhatsApp</h4>
                                <p class="text-emerald-200/70 text-xs">Ao confirmar o pedido, o montador recebe a O.S. completa com endereço, cliente e itens no celular.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 mt-1 border border-amber-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Vistoria e Fotos em Tempo Real</h4>
                                <p class="text-emerald-200/70 text-xs">Montador fotografa a tenda instalada e o sistema programa a O.S. de retirada automaticamente.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-teal-500/20 text-teal-400 flex items-center justify-center shrink-0 mt-1 border border-teal-500/30">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-white text-sm">Lembretes Toda Segunda e no Dia da Montagem</h4>
                                <p class="text-emerald-200/70 text-xs">Robô automático avisa toda a equipe sobre os compromissos semanais e horários do dia.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Visual do Fluxo -->
                <div class="p-8 rounded-3xl glass-card border border-amber-400/30 shadow-2xl relative">
                    <div class="space-y-4">
                        <div class="p-4 rounded-xl bg-[#021811] border border-emerald-800/80 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-500/20 text-amber-400 font-black flex items-center justify-center border border-amber-400/40">1</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Pedido Fechado</div>
                                <div class="text-emerald-200/70">Tendas reservadas no estoque e O.S. gerada na hora</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#021811] border border-emerald-800/80 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-500/20 text-emerald-400 font-black flex items-center justify-center border border-emerald-400/40">2</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Notificação no WhatsApp</div>
                                <div class="text-emerald-200/70">Montador avisado com detalhes do pedido e mapa</div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#021811] border border-emerald-800/80 flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-teal-500/20 text-teal-300 font-black flex items-center justify-center border border-teal-400/40">3</div>
                            <div class="text-xs">
                                <div class="font-bold text-white">Fotos & Retorno Automático</div>
                                <div class="text-emerald-200/70">Instalação concluída com fotos e devolução ao estoque pós-coleta</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- FAQ / DÚVIDAS FREQUENTES -->
    <section id="faq" class="py-24 relative z-10 border-t border-emerald-950 bg-[#021811]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 space-y-3">
                <span class="text-amber-400 font-bold text-xs uppercase tracking-widest">Tire Suas Dúvidas</span>
                <h2 class="text-3xl sm:text-4xl font-black text-white">Perguntas Frequentes</h2>
            </div>

            <div class="space-y-4">
                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">Vocês atendem quais cidades além de São José do Rio Preto?</h4>
                    <p class="text-emerald-200/80 text-sm mt-2">Atendemos toda a região metropolitana de São José do Rio Preto, Mirassol, Cedral, Bady Bassitt, Catanduva, Votuporanga e municípios vizinhos para locações diárias e mensais.</p>
                </div>

                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">As tendas possuem proteção contra chuva forte e vento?</h4>
                    <p class="text-emerald-200/80 text-sm mt-2">Sim! Nossas lonas possuem tratamento antichamas, blackout com proteção UV e são 100% impermeáveis. A montagem é ancorada profissionalmente com estacas ou pesos apropriados para cada piso.</p>
                </div>

                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">Como funciona a locação mensal para empresas?</h4>
                    <p class="text-emerald-200/80 text-sm mt-2">A locação mensal conta com valor reduzido por diária, manutenções preventivas programadas e substituição imediata em caso de qualquer eventualidade, sem custos surpresa.</p>
                </div>

                <div class="p-6 rounded-2xl glass-card">
                    <h4 class="font-bold text-white text-base">Como faço para solicitar um orçamento agora?</h4>
                    <p class="text-emerald-200/80 text-sm mt-2">Basta clicar no botão do WhatsApp no canto da tela ou chamar diretamente no número (17) 99142-9132 informando a data, local e a medida aproximada que deseja.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section id="contato" class="py-20 relative z-10 border-t border-emerald-950 bg-[#032016]">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-20 h-20 rounded-full mx-auto object-cover border-2 border-amber-400 shadow-xl">
            <h2 class="text-3xl sm:text-5xl font-black text-white">Pronto para cobrir seu evento com segurança?</h2>
            <p class="text-emerald-200/80 max-w-xl mx-auto text-sm sm:text-base">Entre em contato agora mesmo pelo WhatsApp ou telefone fixo e receba uma cotação rápida com as melhores condições.</p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Vim+pelo+site+da+Tendas+Eldorado+e+gostaria+de+solicitar+um+or%C3%A7amento." 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-9 py-4 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black text-base shadow-xl shadow-emerald-950/70 transition">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Falar no WhatsApp (17) 99142-9132</span>
                </a>

                <a href="https://www.instagram.com/tendaseldorado/" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-2 px-8 py-4 rounded-xl insta-gradient text-white font-black text-base shadow-xl shadow-pink-950/60 transition">
                    <span>Acompanhar Instagram</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="border-t border-emerald-950 py-12 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center border-b border-emerald-900/40 pb-8">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-12 h-12 rounded-full object-cover border-2 border-amber-400">
                    <div>
                        <div class="font-black text-white text-base">Tendas <span class="text-amber-400">Eldorado</span></div>
                        <div class="text-xs text-emerald-300 font-semibold uppercase">Venda & Locação de Tendas</div>
                    </div>
                </div>

                <div class="text-center md:text-left text-xs text-emerald-200/80 space-y-1">
                    <div>📍 Rua Pedro Martins, 190 — São José do Rio Preto / SP</div>
                    <div>📞 (17) 3217-9349 | 📱 (17) 99142-9132</div>
                </div>

                <div class="flex items-center justify-center md:justify-end gap-3">
                    <a href="https://www.instagram.com/tendaseldorado/" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl insta-gradient text-white shadow hover:scale-110 transition" title="Instagram">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="https://wa.me/5517991429132" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white shadow hover:scale-110 transition" title="WhatsApp">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </a>
                    <a href="{{ route('login') }}" class="px-3 py-2 rounded-xl bg-[#04281e] hover:bg-[#073c2e] text-emerald-200 text-xs font-bold border border-emerald-700/60 transition">
                        Acesso Restrito
                    </a>
                </div>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-emerald-400/60 gap-4">
                <p>&copy; {{ date('Y') }} Tendas Eldorado. Todos os direitos reservados. São José do Rio Preto / SP.</p>
                <p>Estruturas e Coberturas de Alta Resistência.</p>
            </div>
        </div>
    </footer>

    <!-- BOTÕES FLUTUANTES (WHATSAPP + INSTAGRAM) -->
    
    <!-- Botão Flutuante Instagram (logo acima do WhatsApp) -->
    <aside aria-label="Acesso rápido ao Instagram" class="fixed bottom-24 right-5 sm:right-6 z-50">
        <a href="https://www.instagram.com/tendaseldorado/" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="group flex items-center gap-2.5 p-3 sm:px-4 sm:py-3 rounded-full insta-gradient text-white shadow-2xl shadow-pink-950/80 hover:scale-105 active:scale-95 transition duration-300"
           title="Ver perfil @tendaseldorado no Instagram">
            <svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
            </svg>
            <span class="hidden sm:inline text-xs font-black tracking-wide">Instagram @tendaseldorado</span>
        </a>
    </aside>

    <!-- Botão Flutuante WhatsApp (17 99142-9132) -->
    <aside aria-label="Atendimento via WhatsApp" class="fixed bottom-6 right-5 sm:right-6 z-50">
        <a href="https://wa.me/5517991429132?text=Ol%C3%A1!+Vim+pelo+site+da+Tendas+Eldorado+e+gostaria+de+um+or%C3%A7amento+de+loca%C3%A7%C3%A3o." 
           target="_blank" 
           rel="noopener noreferrer" 
           class="group flex items-center gap-2.5 p-3.5 sm:px-5 sm:py-3.5 rounded-full bg-emerald-500 hover:bg-emerald-400 text-white shadow-2xl shadow-emerald-950/90 hover:scale-105 active:scale-95 transition duration-300 relative border-2 border-emerald-300/40"
           title="Fale conosco no WhatsApp (17) 99142-9132">
            
            <!-- Anel Pulsante -->
            <span class="absolute -inset-1 rounded-full bg-emerald-500 opacity-40 animate-ping pointer-events-none"></span>

            <svg class="w-6 h-6 fill-current shrink-0 relative z-10" viewBox="0 0 24 24">
                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
            </svg>
            <div class="hidden sm:block text-left relative z-10 leading-tight">
                <span class="text-[10px] uppercase font-bold text-emerald-100 block">Atendimento Online</span>
                <span class="text-xs font-black text-white block">(17) 99142-9132</span>
            </div>
        </a>
    </aside>

</body>
</html>
