<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Locação de Tendas') }}</title>

        <!-- PWA Manifest & Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#021811">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Tendas Eldorado">
        <link rel="apple-touch-icon" href="/images/logo.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles com fallback de segurança -->
        @if(file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script>
                tailwind.config = {
                    darkMode: 'class',
                }
            </script>
            <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @endif

        <!-- Script de inicialização do Modo Escuro imediato (Zero FOUC) -->
        <script>
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-slate-50 dark:bg-[#021811] text-gray-900 dark:text-slate-100 transition-colors duration-200">
        <!-- Layout com Sidebar na Esquerda -->
        <div class="min-h-screen flex">
            <!-- Sidebar Lateral Esquerda Fixa/Dedicada -->
            @include('layouts.sidebar')

            <!-- Área Principal (Topo + Conteúdo) -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Barra Superior -->
                @include('layouts.topbar')

                <!-- Botão "Baixar App" (PWA) - Aparece SOMENTE quando acessado em celular -->
                <div id="pwa-install-banner" class="hidden md:hidden lg:hidden bg-gradient-to-r from-[#031d14] to-[#063f2e] border-b border-amber-400/30 text-white p-3 shadow-md transition-all">
                    <div class="flex items-center justify-between gap-2 max-w-7xl mx-auto">
                        <div class="flex items-center gap-2">
                            <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-6 h-6 rounded-full border border-amber-400 object-cover shrink-0">
                            <span class="text-xs font-medium text-emerald-100">Instale o app da Tendas Eldorado no celular!</span>
                        </div>
                        <button id="btn-instalar-app" type="button" class="px-3.5 py-1.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 text-xs font-black rounded-lg shadow-sm shrink-0 transition">
                            Baixar App
                        </button>
                    </div>
                </div>

                <!-- Botão / Banner "Criar Atalho no Computador" (PWA Desktop / Área de Trabalho) - Aparece SOMENTE em computadores -->
                <div id="desktop-install-banner" class="hidden bg-gradient-to-r from-[#031d14] via-[#053223] to-[#031d14] border-b border-amber-400/30 text-white p-3 shadow-md transition-all">
                    <div class="flex items-center justify-between gap-3 max-w-7xl mx-auto">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-6 h-6 rounded-full border border-amber-400 object-cover shrink-0">
                            <div>
                                <span class="text-xs font-bold text-amber-300">Instale o atalho da Tendas Eldorado no seu computador!</span>
                                <span class="hidden sm:inline text-[11px] text-emerald-100/90 ml-1.5">• Acesse o sistema operacional direto da Área de Trabalho</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <button id="btn-desktop-shortcut" type="button" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 text-xs font-black rounded-lg shadow-sm shrink-0 transition transform hover:scale-[1.02] cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <span>Baixar Atalho PC</span>
                            </button>
                            <button type="button" onclick="fecharBannerDesktop()" class="text-emerald-300/60 hover:text-white p-1 rounded transition text-base leading-none cursor-pointer" title="Dispensar aviso">
                                &times;
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Page Heading (se fornecido) -->
                @isset($header)
                    <div class="bg-white dark:bg-[#031d14] border-b border-gray-200 dark:border-[#083d2c] py-4 px-4 sm:px-6 lg:px-8 transition-colors">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Flash Messages -->
                <div class="px-4 sm:px-6 lg:px-8 pt-4 w-full">
                    @if(session('success'))
                        <div class="mb-4 bg-emerald-50 border-l-4 border-emerald-500 p-4 text-emerald-800 rounded shadow-xs flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 text-red-800 rounded shadow-xs flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Page Content -->
                <main class="flex-1 pb-8">
                    {{ $slot }}
                </main>

                <!-- Rodapé do Sistema -->
                <footer class="py-3 px-4 sm:px-6 lg:px-8 border-t border-gray-200 dark:border-[#083d2c] bg-white/40 dark:bg-[#021811]/60 text-xs text-gray-500 dark:text-emerald-400/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div>
                        &copy; {{ date('Y') }} Tendas Eldorado — Sistema Operacional de Locações & Montagens
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-600 dark:text-emerald-300/80">Desenvolvido por</span>
                        <a href="https://piccininidigitalsolutions.com.br/" target="_blank" rel="noopener noreferrer" class="inline-flex items-center group transition" title="Piccinini Digital Solutions">
                            <img src="{{ asset('images/pds_logo.png') }}" alt="Piccinini Digital Solutions" class="h-7 w-auto rounded bg-white p-0.5 object-contain shadow-xs group-hover:scale-105 transition-transform duration-200">
                        </a>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Modal Informativo para iOS / Não-Android (Mantido 100% intacto) -->
        <div id="modal-ios-pwa" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center shadow-2xl relative">
                <button onclick="document.getElementById('modal-ios-pwa').classList.add('hidden')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Instalar App no iPhone / Safari</h3>
                <p class="text-sm text-gray-600 text-left space-y-2 mb-4">
                    1. No Safari, toque no ícone de <strong>Compartilhar</strong> (quadrado com seta para cima).<br>
                    2. Role as opções e clique em <strong>"Adicionar à Tela de Início"</strong>.<br>
                    3. Confirme em <strong>Adicionar</strong>. Pronto! O app aparecerá na sua tela inicial.
                </p>
                <button onclick="document.getElementById('modal-ios-pwa').classList.add('hidden')" class="w-full py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                    Entendi
                </button>
            </div>
        </div>

        <!-- Modal Informativo para Computador (Atalho na Área de Trabalho) -->
        <div id="modal-desktop-shortcut" class="fixed inset-0 bg-black/75 z-50 hidden flex items-center justify-center p-4 backdrop-blur-xs">
            <div class="bg-white dark:bg-[#031d14] rounded-2xl max-w-md w-full p-6 text-center shadow-2xl relative border border-gray-200 dark:border-[#083d2c]">
                <button onclick="document.getElementById('modal-desktop-shortcut').classList.add('hidden')" class="absolute top-3.5 right-3.5 text-gray-400 hover:text-gray-600 dark:hover:text-white text-xl font-bold">&times;</button>
                
                <div class="w-14 h-14 bg-amber-500/20 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3 border border-amber-400/40">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1.5">Atalho para Computador Gerado!</h3>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mb-4">
                    ✓ O arquivo de atalho direto para o Painel foi baixado
                </p>

                <div class="text-xs text-left bg-gray-50 dark:bg-[#02140e] p-4 rounded-xl border border-gray-100 dark:border-[#083d2c] space-y-2.5 text-gray-700 dark:text-emerald-100/90 mb-5">
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-500 font-bold flex items-center justify-center shrink-0 text-[11px]">1</span>
                        <span>Abra a pasta de <strong>Downloads</strong> do seu Windows e encontre <strong>Tendas Eldorado - Painel.html</strong>.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-500 font-bold flex items-center justify-center shrink-0 text-[11px]">2</span>
                        <span><strong>Mova ou arraste o arquivo</strong> para a sua <strong>Área de Trabalho</strong> (Desktop).</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-amber-400/20 text-amber-500 font-bold flex items-center justify-center shrink-0 text-[11px]">3</span>
                        <span>Pronto! Dê <strong>dois cliques</strong> no atalho a qualquer momento para abrir o sistema diretamente no Painel Administrativo.</span>
                    </div>
                    <div class="pt-2 border-t border-gray-200 dark:border-[#083d2c] text-[11px] text-gray-500 dark:text-emerald-300/70">
                        💡 <strong>Dica no Chrome / Edge:</strong> Você também pode clicar no ícone <strong>⊕ (Instalar aplicativo)</strong> na barra de endereços para instalar como app de janela nativo no Windows.
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-2">
                    <a href="{{ route('atalho.pc') }}" class="w-full sm:flex-1 py-2.5 px-3 text-center bg-emerald-50 dark:bg-[#063325] text-emerald-700 dark:text-emerald-300 rounded-xl font-bold text-xs hover:bg-emerald-100 dark:hover:bg-[#084532] border border-emerald-300 dark:border-emerald-700/50 transition">
                        Baixar Novamente (.html)
                    </a>
                    <a href="{{ route('atalho.url') }}" class="w-full sm:flex-1 py-2.5 px-3 text-center bg-gray-50 dark:bg-[#021811] text-gray-700 dark:text-emerald-200 rounded-xl font-bold text-xs hover:bg-gray-100 dark:hover:bg-[#042419] border border-gray-300 dark:border-emerald-800/60 transition">
                        Baixar Atalho (.url)
                    </a>
                    <button type="button" onclick="document.getElementById('modal-desktop-shortcut').classList.add('hidden')" class="w-full sm:flex-1 py-2.5 px-4 bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 rounded-xl font-black text-xs shadow-md transition">
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        <!-- Script PWA e Service Worker (Mobile + Computador) -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW fail:', err));
                });
            }

            // Variáveis Celular (Mantidas 100% como no original)
            let deferredPrompt;
            const btnInstalar = document.getElementById('btn-instalar-app');
            const pwaBanner = document.getElementById('pwa-install-banner');
            const modalIos = document.getElementById('modal-ios-pwa');

            // Variáveis Computador / Desktop
            let deferredPromptDesktop = null;
            const desktopBanner = document.getElementById('desktop-install-banner');
            const btnDesktopShortcut = document.getElementById('btn-desktop-shortcut');
            const modalDesktop = document.getElementById('modal-desktop-shortcut');

            // Detecção precisa de celular (smartphones apenas)
            const isCelular = () => {
                const ua = (navigator.userAgent || navigator.vendor || window.opera || '').toLowerCase();
                const isMobileUA = /android.*mobile|iphone|ipod|blackberry|iemobile|opera mini|mobile/i.test(ua);
                const isSmallTouch = window.innerWidth <= 640 && ('ontouchstart' in window || navigator.maxTouchPoints > 0);
                return isMobileUA || isSmallTouch;
            };

            const isIos = () => /iphone|ipod/.test(navigator.userAgent.toLowerCase());
            const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone) || window.matchMedia('(display-mode: standalone)').matches;

            // 1. REGRAS DO BOTÃO DO CELULAR (NÃO ALTERADO - mantido 100% intacto)
            if (!isCelular() || isInStandaloneMode()) {
                if (pwaBanner) pwaBanner.remove();
            } else {
                if (pwaBanner) pwaBanner.classList.remove('hidden');
            }

            // 2. REGRAS DO BOTÃO DO COMPUTADOR (Aparece SOMENTE em computador, NUNCA em celular)
            function fecharBannerDesktop() {
                if (desktopBanner) {
                    desktopBanner.style.display = 'none';
                    sessionStorage.setItem('dismiss_desktop_banner', '1');
                }
            }

            if (isCelular() || isInStandaloneMode()) {
                // Se for celular, remove o banner de computador para NUNCA aparecer no celular
                if (desktopBanner) desktopBanner.remove();
            } else {
                // É computador (Desktop / Notebook): exibe se não tiver sido dispensado na sessão
                if (desktopBanner && !sessionStorage.getItem('dismiss_desktop_banner')) {
                    desktopBanner.classList.remove('hidden');
                }
            }

            // Gerenciamento de eventos de instalação PWA
            window.addEventListener('beforeinstallprompt', (e) => {
                if (isCelular()) {
                    if (isInStandaloneMode()) return;
                    e.preventDefault();
                    deferredPrompt = e;
                    if (pwaBanner) pwaBanner.classList.remove('hidden');
                } else {
                    // Computador
                    if (isInStandaloneMode()) return;
                    e.preventDefault();
                    deferredPromptDesktop = e;
                    if (desktopBanner && !sessionStorage.getItem('dismiss_desktop_banner')) {
                        desktopBanner.classList.remove('hidden');
                    }
                }
            });

            // Ação do Botão Mobile (Mantido intacto)
            if (btnInstalar) {
                btnInstalar.addEventListener('click', async () => {
                    if (deferredPrompt) {
                        deferredPrompt.prompt();
                        const { outcome } = await deferredPrompt.userChoice;
                        deferredPrompt = null;
                        if (outcome === 'accepted' && pwaBanner) {
                            pwaBanner.style.display = 'none';
                        }
                    } else if (isIos()) {
                        if (modalIos) modalIos.classList.remove('hidden');
                    } else {
                        window.appAlert('Instalar Aplicativo', 'Para instalar este app, acione o menu do seu navegador (três pontinhos) e toque em "Adicionar à tela inicial" ou "Instalar aplicativo".', 'info');
                    }
                });
            }

            // Ação do Botão Computador (Cria e baixa atalho para a Área de Trabalho que abre direto no Admin)
            function baixarAtalhoDesktop() {
                const a = document.createElement('a');
                a.href = "{{ route('atalho.pc') }}";
                a.setAttribute('download', 'Tendas Eldorado - Painel.html');
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }

            if (btnDesktopShortcut) {
                btnDesktopShortcut.addEventListener('click', async () => {
                    // 1. Gera e baixa o arquivo de atalho direto para a Área de Trabalho
                    baixarAtalhoDesktop();

                    // 2. Se o navegador no computador suportar prompt de instalação PWA na Área de Trabalho (Chrome/Edge/Brave):
                    if (deferredPromptDesktop) {
                        try {
                            deferredPromptDesktop.prompt();
                            const { outcome } = await deferredPromptDesktop.userChoice;
                            if (outcome === 'accepted' && desktopBanner) {
                                desktopBanner.style.display = 'none';
                            }
                            deferredPromptDesktop = null;
                        } catch (err) {
                            console.log('Desktop PWA prompt error:', err);
                        }
                    }

                    // 3. Abre a modal explicativa para o usuário mover ou fixar
                    if (modalDesktop) {
                        modalDesktop.classList.remove('hidden');
                    }
                });
            }
        </script>

        <!-- Modal Global Elegante do Sistema (Substitui completamente alerts e confirms nativos) -->
        <div id="global-app-modal" class="fixed inset-0 z-[999] hidden items-center justify-center p-4 bg-black/75 backdrop-blur-xs transition-opacity duration-200">
            <div id="global-app-modal-card" class="relative w-full max-w-md bg-white dark:bg-[#031d14] rounded-2xl shadow-2xl border border-gray-200 dark:border-[#083d2c] p-6 text-center transform transition-all scale-95 duration-200">
                <!-- Ícone Dinâmico -->
                <div id="global-modal-icon-container" class="mx-auto w-14 h-14 rounded-full flex items-center justify-center shadow-lg mb-4">
                </div>

                <!-- Título -->
                <h3 id="global-modal-title" class="text-lg font-bold text-gray-900 dark:text-white"></h3>

                <!-- Mensagem (suporta HTML seguro) -->
                <div id="global-modal-message" class="text-sm text-gray-600 dark:text-emerald-100/80 mt-2 leading-relaxed"></div>

                <!-- Botões de Ação -->
                <div class="mt-6 flex items-center justify-center gap-3">
                    <button id="global-modal-btn-cancel" type="button" class="px-5 py-2.5 rounded-xl border border-gray-300 dark:border-[#083d2c] bg-gray-100 dark:bg-[#04291c] text-gray-700 dark:text-emerald-200 hover:bg-gray-200 dark:hover:bg-[#073827] font-semibold text-sm transition">
                        Cancelar
                    </button>
                    <button id="global-modal-btn-confirm" type="button" class="px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>

        <!-- Script de Controle do Modal Global -->
        <script>
            window.appModal = function({
                title = 'Aviso do Sistema',
                message = '',
                type = 'info', // 'warning', 'danger', 'success', 'info'
                confirmText = 'OK',
                cancelText = 'Cancelar',
                showCancel = false
            }) {
                return new Promise((resolve) => {
                    const modal = document.getElementById('global-app-modal');
                    const card = document.getElementById('global-app-modal-card');
                    const titleEl = document.getElementById('global-modal-title');
                    const msgEl = document.getElementById('global-modal-message');
                    const iconContainer = document.getElementById('global-modal-icon-container');
                    const btnConfirm = document.getElementById('global-modal-btn-confirm');
                    const btnCancel = document.getElementById('global-modal-btn-cancel');

                    if (!modal) {
                        resolve(true);
                        return;
                    }

                    titleEl.innerHTML = title;
                    msgEl.innerHTML = message;
                    btnConfirm.innerHTML = confirmText;
                    btnCancel.innerHTML = cancelText;

                    if (showCancel) {
                        btnCancel.classList.remove('hidden');
                    } else {
                        btnCancel.classList.add('hidden');
                    }

                    // Configura ícone e estilos dos botões conforme o tipo
                    if (type === 'danger') {
                        iconContainer.className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center shadow-lg mb-4 bg-red-500/20 text-red-500 dark:bg-red-400/20 dark:text-red-400 border border-red-500/30';
                        iconContainer.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>';
                        btnConfirm.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-700 hover:from-red-700 hover:to-rose-800 text-white font-bold text-sm shadow-md shadow-red-900/20 transition';
                    } else if (type === 'warning') {
                        iconContainer.className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center shadow-lg mb-4 bg-amber-500/20 text-amber-500 dark:bg-amber-400/20 dark:text-amber-400 border border-amber-500/30';
                        iconContainer.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
                        btnConfirm.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold text-sm shadow-md transition';
                    } else if (type === 'success') {
                        iconContainer.className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center shadow-lg mb-4 bg-emerald-500/20 text-emerald-500 dark:bg-emerald-400/20 dark:text-emerald-400 border border-emerald-500/30';
                        iconContainer.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
                        btnConfirm.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 border border-amber-400/40 text-amber-300 font-bold text-sm shadow-md transition';
                    } else {
                        iconContainer.className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center shadow-lg mb-4 bg-emerald-500/10 text-emerald-600 dark:text-amber-400 border border-emerald-500/20';
                        iconContainer.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
                        btnConfirm.className = 'px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-700 to-emerald-600 hover:from-emerald-800 hover:to-emerald-700 border border-amber-400/40 text-amber-300 font-bold text-sm shadow-md transition';
                    }

                    // Exibe o modal com animação
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    setTimeout(() => {
                        card.classList.remove('scale-95');
                        card.classList.add('scale-100');
                    }, 10);

                    function fechar(resultado) {
                        card.classList.remove('scale-100');
                        card.classList.add('scale-95');
                        setTimeout(() => {
                            modal.classList.remove('flex');
                            modal.classList.add('hidden');
                            btnConfirm.onclick = null;
                            btnCancel.onclick = null;
                            modal.onclick = null;
                            resolve(resultado);
                        }, 150);
                    }

                    btnConfirm.onclick = () => fechar(true);
                    btnCancel.onclick = () => fechar(false);
                    modal.onclick = (e) => {
                        if (e.target === modal) fechar(false);
                    };
                });
            };

            window.appAlert = function(title, message, type = 'info') {
                return window.appModal({
                    title,
                    message,
                    type,
                    confirmText: 'Entendido',
                    showCancel: false
                });
            };

            window.appConfirm = function(title, message, options = {}) {
                return window.appModal({
                    title,
                    message,
                    type: options.type || 'warning',
                    confirmText: options.confirmText || 'Confirmar',
                    cancelText: options.cancelText || 'Cancelar',
                    showCancel: true,
                    ...options
                });
            };

            // Sobrescreve alert nativo para garantir que nunca mais apareça popup feio do navegador
            window.alert = function(msg) {
                window.appAlert('Aviso do Sistema', msg, 'info');
            };

            // Interceptador global para formulários que usam confirmação data-confirm
            document.addEventListener('submit', async function(e) {
                const form = e.target;
                if (form && form.hasAttribute('data-confirm')) {
                    e.preventDefault();
                    const mensagem = form.getAttribute('data-confirm');
                    const titulo = form.getAttribute('data-confirm-title') || 'Confirmar Ação';
                    const tipo = form.getAttribute('data-confirm-type') || 'danger';
                    const botao = form.getAttribute('data-confirm-btn') || 'Sim, Confirmar';

                    const confirmou = await window.appConfirm(titulo, mensagem, {
                        type: tipo,
                        confirmText: botao
                    });

                    if (confirmou) {
                        form.removeAttribute('data-confirm');
                        form.submit();
                    }
                }
            });
        </script>
    </body>
</html>
