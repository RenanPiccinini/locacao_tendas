<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TendaTech — Soluções Inteligentes em Estruturas e Coberturas</title>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Three.js para o efeito 3D interativo -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glow {
            text-shadow: 0 0 40px rgba(59, 130, 246, 0.6);
        }
        .glow-box {
            box-shadow: 0 0 50px -10px rgba(59, 130, 246, 0.3);
        }
        .glass {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .glass-card:hover {
            border-color: rgba(59, 130, 246, 0.4);
            transform: translateY(-4px);
            transition: all 0.3s ease;
        }
        /* Canvas 3D de fundo */
        #hero-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 overflow-x-hidden selection:bg-blue-600 selection:text-white">

    <!-- Top Navigation Glass -->
    <header class="fixed top-0 left-0 right-0 z-50 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 font-extrabold text-xl tracking-tight text-white group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-400 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                </div>
                <span>Tenda<span class="text-blue-500">Tech</span></span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
                <a href="#solucoes" class="hover:text-blue-400 transition">Soluções</a>
                <a href="#tecnologia" class="hover:text-blue-400 transition">Tecnologia</a>
                <a href="#modelos" class="hover:text-blue-400 transition">Estruturas</a>
                <a href="#contato" class="hover:text-blue-400 transition">Contato</a>
            </nav>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                        <span>Painel do Sistema</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-blue-600/20 hover:bg-blue-600/30 border border-blue-500/30 text-blue-400 hover:text-white font-bold text-sm transition">
                        Entrar no Sistema
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Hero Section com Efeitos 3D & Parallax -->
    <section class="relative min-h-screen flex items-center justify-center pt-24 pb-16 overflow-hidden">
        <!-- Canvas do Three.js 3D -->
        <canvas id="hero-canvas"></canvas>

        <!-- Gradientes de iluminação de fundo (Luz volumétrica) -->
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[500px] bg-blue-600/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute bottom-10 left-1/4 w-[400px] h-[400px] bg-cyan-500/15 rounded-full blur-[120px] pointer-events-none"></div>

        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs sm:text-sm font-semibold tracking-wide">
                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                <span>Plataforma Inteligente de Locação & Gestão Operacional</span>
            </div>

            <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.1] glow">
                Estruturas de Alto Padrão <br>
                <span class="bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-indigo-300 to-cyan-400">
                    com Gestão em Tempo Real
                </span>
            </h1>

            <p class="max-w-2xl mx-auto text-base sm:text-xl text-slate-400 leading-relaxed font-normal">
                Locação de tendas piramidais, articuladas e galpões para eventos corporativos, feiras e recepções. Montagem ágil, integração com WhatsApp e rastreamento de ordens de serviço.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white font-bold text-base shadow-xl shadow-blue-500/30 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <span>Acessar Painel de Montagem</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
                <a href="#modelos" class="w-full sm:w-auto px-8 py-4 rounded-xl glass hover:bg-white/10 text-slate-200 font-bold text-base transition flex items-center justify-center gap-2">
                    Explorar Catálogo 3D
                </a>
            </div>

            <!-- Stats Rápidos com Parallax -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-12 max-w-4xl mx-auto">
                <div class="p-4 rounded-2xl glass-card">
                    <div class="text-3xl font-black text-white">+500</div>
                    <div class="text-xs text-slate-400 font-medium mt-1">Eventos Atendidos</div>
                </div>
                <div class="p-4 rounded-2xl glass-card">
                    <div class="text-3xl font-black text-cyan-400">100%</div>
                    <div class="text-xs text-slate-400 font-medium mt-1">Tendas com Laudo ART</div>
                </div>
                <div class="p-4 rounded-2xl glass-card">
                    <div class="text-3xl font-black text-blue-400">24/7</div>
                    <div class="text-xs text-slate-400 font-medium mt-1">Alertas WhatsApp</div>
                </div>
                <div class="p-4 rounded-2xl glass-card">
                    <div class="text-3xl font-black text-indigo-400">PWA</div>
                    <div class="text-xs text-slate-400 font-medium mt-1">App Operacional Móvel</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção de Recursos Tecnológicos -->
    <section id="tecnologia" class="py-24 relative z-10 border-t border-slate-900 bg-slate-950/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16 space-y-4">
                <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Tecnologia & Inovação</span>
                <h2 class="text-3xl sm:text-5xl font-black text-white">Engenharia Operacional Conectada</h2>
                <p class="text-slate-400 text-sm sm:text-base">Adeus às pranchetas de papel. Cada etapa da montagem, checagem e desmontagem é automatizada em nuvem.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Card 1 -->
                <div class="p-8 rounded-2xl glass-card relative overflow-hidden group">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-5.805 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">WhatsApp Automation</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Ao gerar o pedido, o montador recebe a O.S. completa diretamente no WhatsApp, com endereço exato, itens e dados do cliente.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-8 rounded-2xl glass-card relative overflow-hidden group">
                    <div class="w-12 h-12 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Comprovação com Fotos</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        O montador anexa fotos pelo celular assim que instala. O sistema gera automaticamente a O.S. de retorno e programa a retirada.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-8 rounded-2xl glass-card relative overflow-hidden group">
                    <div class="w-12 h-12 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-2">Agenda Inteligente</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Calendário estilo Google com filtros de montador, visualização mensal, semanal e diária com lembretes automáticos toda segunda-feira.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Catálogo de Estruturas -->
    <section id="modelos" class="py-24 relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-blue-500 font-bold text-xs uppercase tracking-widest">Catálogo Disponível</span>
                    <h2 class="text-3xl sm:text-4xl font-black text-white mt-1">Modelos em Destaque</h2>
                </div>
                <a href="{{ route('login') }}" class="text-blue-400 font-semibold text-sm hover:text-blue-300 flex items-center gap-1">
                    Ver disponibilidade em estoque &rarr;
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Modelo 1 -->
                <div class="rounded-2xl glass-card overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-blue-900/40 to-slate-900 flex items-center justify-center relative">
                        <svg class="w-24 h-24 text-blue-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                        </svg>
                        <span class="absolute top-4 right-4 px-3 py-1 bg-blue-600/60 text-white rounded-full text-xs font-bold">5x5 Metros</span>
                    </div>
                    <div class="p-6 space-y-3">
                        <h4 class="text-lg font-bold text-white">Tenda Piramidal 5x5m</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">Ideal para lounges, estandes e áreas gourmet. Lona blackout antimofo e estrutura galvanizada com ancoragem reforçada.</p>
                        <div class="pt-2 flex items-center justify-between border-t border-slate-800">
                            <span class="text-slate-400 text-xs">Capacidade média</span>
                            <span class="text-white font-bold text-sm">30 a 40 pessoas</span>
                        </div>
                    </div>
                </div>

                <!-- Modelo 2 -->
                <div class="rounded-2xl glass-card overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-indigo-900/40 to-slate-900 flex items-center justify-center relative">
                        <svg class="w-24 h-24 text-indigo-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="absolute top-4 right-4 px-3 py-1 bg-indigo-600/60 text-white rounded-full text-xs font-bold">10x10 Metros</span>
                    </div>
                    <div class="p-6 space-y-3">
                        <h4 class="text-lg font-bold text-white">Tenda Piramidal 10x10m</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">Perfeita para casamentos, festas de médio e grande porte e palcos. Excelente vão livre de 100m² sem pilares centrais.</p>
                        <div class="pt-2 flex items-center justify-between border-t border-slate-800">
                            <span class="text-slate-400 text-xs">Capacidade média</span>
                            <span class="text-white font-bold text-sm">120 a 160 pessoas</span>
                        </div>
                    </div>
                </div>

                <!-- Modelo 3 -->
                <div class="rounded-2xl glass-card overflow-hidden">
                    <div class="h-48 bg-gradient-to-br from-cyan-900/40 to-slate-900 flex items-center justify-center relative">
                        <svg class="w-24 h-24 text-cyan-500/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="absolute top-4 right-4 px-3 py-1 bg-cyan-600/60 text-white rounded-full text-xs font-bold">3x3 Metros</span>
                    </div>
                    <div class="p-6 space-y-3">
                        <h4 class="text-lg font-bold text-white">Tenda Sanfonada Express</h4>
                        <p class="text-slate-400 text-xs leading-relaxed">Montagem instantânea em minutos. Estrutura pantográfica em alumínio aeroespacial de altíssima leveza e firmeza.</p>
                        <div class="pt-2 flex items-center justify-between border-t border-slate-800">
                            <span class="text-slate-400 text-xs">Tempo de montagem</span>
                            <span class="text-white font-bold text-sm">&lt; 3 minutos</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer class="border-t border-slate-900 py-12 relative z-10 glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11m16-11v11" />
                    </svg>
                </div>
                <span class="font-bold text-white">TendaTech Brasil</span>
            </div>

            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} TendaTech. Sistema de Gestão de Locação & WhatsApp Automation. Todos os direitos reservados.
            </p>

            <a href="{{ route('login') }}" class="text-xs font-semibold text-blue-400 hover:text-white transition">
                Área Restrita / Login &rarr;
            </a>
        </div>
    </footer>

    <!-- Script 3D Three.js Interativo com Parallax do Mouse -->
    <script>
        const canvas = document.getElementById('hero-canvas');
        const scene = new THREE.Scene();
        const camera = new THREE.PerspectiveCamera(60, window.innerWidth / window.innerHeight, 0.1, 1000);
        camera.position.z = 25;

        const renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
        renderer.setSize(window.innerWidth, window.innerHeight);
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

        // Cria uma estrutura 3D piramidal tecnológica (Wireframe Glow)
        const geometry = new THREE.ConeGeometry(8, 10, 4, 3, true);
        const material = new THREE.MeshBasicMaterial({
            color: 0x3b82f6,
            wireframe: true,
            transparent: true,
            opacity: 0.35
        });
        const piramide = new THREE.Mesh(geometry, material);
        piramide.position.set(0, 0, 0);
        piramide.rotation.x = 0.2;
        scene.add(piramide);

        // Anel orbitante 3D
        const ringGeo = new THREE.TorusGeometry(12, 0.05, 16, 100);
        const ringMat = new THREE.MeshBasicMaterial({ color: 0x06b6d4, transparent: true, opacity: 0.4 });
        const ring = new THREE.Mesh(ringGeo, ringMat);
        ring.rotation.x = Math.PI / 2.3;
        scene.add(ring);

        // Partículas flutuantes (estrelas/poeira tecnológica)
        const particlesCount = 200;
        const positions = new Float32Array(particlesCount * 3);
        for (let i = 0; i < particlesCount * 3; i++) {
            positions[i] = (Math.random() - 0.5) * 60;
        }
        const particlesGeo = new THREE.BufferGeometry();
        particlesGeo.setAttribute('position', new THREE.BufferAttribute(positions, 3));
        const particlesMat = new THREE.PointsMaterial({ size: 0.15, color: 0x60a5fa, transparent: true, opacity: 0.6 });
        const particles = new THREE.Points(particlesGeo, particlesMat);
        scene.add(particles);

        // Movimento Parallax com Mouse
        let mouseX = 0;
        let mouseY = 0;
        let targetX = 0;
        let targetY = 0;

        window.addEventListener('mousemove', (e) => {
            mouseX = (e.clientX - window.innerWidth / 2) * 0.001;
            mouseY = (e.clientY - window.innerHeight / 2) * 0.001;
        });

        // Loop de Animação
        function animate() {
            requestAnimationFrame(animate);

            targetX += (mouseX - targetX) * 0.05;
            targetY += (mouseY - targetY) * 0.05;

            piramide.rotation.y += 0.003;
            piramide.rotation.x = 0.2 + targetY * 2;
            piramide.rotation.z = targetX * 2;

            ring.rotation.z += 0.002;
            ring.rotation.y += 0.001;

            particles.rotation.y += 0.0005;

            renderer.render(scene, camera);
        }
        animate();

        // Responsivo
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });
    </script>
</body>
</html>
