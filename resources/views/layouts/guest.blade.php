<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Acesso ao Sistema — Locação de Tendas</title>
    
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#020617">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-box {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 50px -10px rgba(59, 130, 246, 0.2);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Efeitos de iluminação de fundo (Glow 3D) -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-[130px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[160px] pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo e Cabeçalho -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-3 group">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 flex items-center justify-center text-white shadow-xl shadow-blue-500/30 group-hover:scale-105 transition">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tight text-white">Tenda<span class="text-blue-500">Tech</span></span>
            </a>
            <h1 class="text-xl font-bold text-white mt-4">Bem-vindo(a) de volta!</h1>
            <p class="text-xs text-slate-400 mt-1">Acesse sua conta para gerenciar ordens de serviço e montagens</p>
        </div>

        <!-- Card de Formulário -->
        <div class="glass-box rounded-3xl p-8">
            {{ $slot }}
        </div>

        <!-- Link Voltar para Home -->
        <div class="text-center mt-6">
            <a href="/" class="text-xs font-semibold text-slate-400 hover:text-white transition flex items-center justify-center gap-1.5">
                &larr; Voltar para o site principal
            </a>
        </div>
    </div>

</body>
</html>
