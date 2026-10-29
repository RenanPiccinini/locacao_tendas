<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Locação de Tendas') }}</title>

        <!-- PWA Manifest & Meta Tags -->
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#2563eb">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Tendas">
        <link rel="apple-touch-icon" href="/icons/icon-192.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-50 text-gray-900">
        <!-- Layout com Sidebar na Esquerda -->
        <div class="min-h-screen flex">
            <!-- Sidebar Lateral Esquerda Fixa/Dedicada -->
            @include('layouts.sidebar')

            <!-- Área Principal (Topo + Conteúdo) -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Barra Superior -->
                @include('layouts.topbar')

                <!-- Botão "Baixar App" (PWA) - Aparece SOMENTE em celular -->
                <div id="pwa-install-banner" class="lg:hidden bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-3 shadow-md">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <span class="text-xs font-medium">Instale no celular para uso direto em campo!</span>
                        </div>
                        <button id="btn-instalar-app" type="button" class="px-3 py-1 bg-white text-blue-700 text-xs font-bold rounded shadow hover:bg-gray-100 shrink-0">
                            Baixar App
                        </button>
                    </div>
                </div>

                <!-- Page Heading (se fornecido) -->
                @isset($header)
                    <div class="bg-white border-b border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
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
                        alert('Para instalar este app, acione o menu do seu navegador (três pontinhos) e toque em "Adicionar à tela inicial" ou "Instalar aplicativo".');
                    }
                });
            }
        </script>
    </body>
</html>
