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

                <!-- Botão "Baixar App" (PWA) - Aparece SOMENTE em celular -->
                <div id="pwa-install-banner" class="lg:hidden bg-gradient-to-r from-[#031d14] to-[#063f2e] border-b border-amber-400/30 text-white p-3 shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <img src="/images/logo.png" alt="Tendas Eldorado" class="w-6 h-6 rounded-full border border-amber-400 object-cover shrink-0">
                            <span class="text-xs font-medium text-emerald-100">Instale o app da Tendas Eldorado no celular!</span>
                        </div>
                        <button id="btn-instalar-app" type="button" class="px-3 py-1 bg-amber-400 hover:bg-amber-300 text-slate-950 text-xs font-bold rounded shadow shrink-0 transition">
                            Baixar App
                        </button>
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
                <main class="flex-1 pb-12">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Modal Informativo para iOS / Não-Android -->
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

        <!-- Script PWA e Service Worker -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW fail:', err));
                });
            }

            let deferredPrompt;
            const btnInstalar = document.getElementById('btn-instalar-app');
            const pwaBanner = document.getElementById('pwa-install-banner');
            const modalIos = document.getElementById('modal-ios-pwa');

            const isIos = () => /iphone|ipad|ipod/.test(window.navigator.userAgent.toLowerCase());
            const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone) || window.matchMedia('(display-mode: standalone)').matches;

            if (isInStandaloneMode()) {
                if (pwaBanner) pwaBanner.style.display = 'none';
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                if (pwaBanner) pwaBanner.style.display = 'block';
            });

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
