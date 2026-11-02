<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso ao Sistema — Tendas Eldorado</title>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#021811">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles com fallback de segurança -->
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @endif

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-box {
            background: rgba(3, 35, 26, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(16, 185, 129, 0.25);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 0 0 50px -10px rgba(245, 158, 11, 0.2);
        }
    </style>
</head>
<body class="bg-[#021811] text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Efeitos de iluminação de fundo (Glow Elegante) -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-600/20 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-teal-600/10 rounded-full blur-[160px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo e Cabeçalho -->
        <div class="text-center mb-6">
            <a href="/" class="inline-flex flex-col items-center gap-2 group">
                <img src="{{ asset('images/logo.png') }}" alt="Tendas Eldorado" class="w-20 h-20 rounded-full object-cover border-2 border-amber-400 shadow-xl shadow-amber-500/20 group-hover:scale-105 transition">
                <span class="text-2xl font-black tracking-tight text-white">Tendas <span class="text-amber-400">Eldorado</span></span>
                <span class="text-[11px] text-emerald-400 font-bold uppercase tracking-wider">Venda & Locação de Coberturas</span>
            </a>
            <h1 class="text-lg font-bold text-white mt-3">Acesso ao Sistema Operacional</h1>
            <p class="text-xs text-emerald-200/70 mt-0.5">Gerenciamento de pedidos, estoque e montagens em tempo real</p>
        </div>

        <!-- Card de Formulário -->
        <div class="glass-box rounded-3xl p-8">
            {{ $slot }}
        </div>

        <!-- Links Extras -->
        <div class="flex items-center justify-between mt-6 text-xs font-semibold text-emerald-300/80 px-2">
            <a href="/" class="hover:text-amber-300 transition flex items-center gap-1.5">
                &larr; Ir para o site
            </a>
            <a href="https://www.instagram.com/tendaseldorado/" target="_blank" rel="noopener noreferrer" class="hover:text-amber-300 transition flex items-center gap-1">
                <span>Instagram @tendaseldorado</span>
                <svg class="w-3.5 h-3.5 text-pink-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
            </a>
        </div>
    </div>

</body>
</html>
