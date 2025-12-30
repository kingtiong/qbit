<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IQBIT | Quantum Genesis</title>
    @php
        $faviconCandidates = [
            'images/favicon.ico',
            'images/favicon.png',
            'favicon.ico',
        ];
        $faviconPath = null;
        foreach ($faviconCandidates as $p) {
            if (file_exists(public_path($p))) {
                $faviconPath = $p;
                break;
            }
        }
    @endphp
    <link rel="icon" href="{{ $faviconPath ? asset($faviconPath) : '/favicon.ico' }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Inter:wght@300;400;600&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    <!-- Core libraries -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <!-- Tailwind theme -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg: '#020202',
                        bgCard: '#08080A',
                        gold: {
                            DEFAULT: '#C59D5F',
                            light: '#F3E5B5',
                            dim: '#8E6E38'
                        },
                        quantum: '#00F0FF',
                        nvidia: '#76B900',
                        textMain: '#EAEAEA',
                        textDim: '#888888'
                    },
                    fontFamily: {
                        serif: ['Cinzel', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    backgroundImage: {
                        'gold-gradient': 'linear-gradient(135deg, #FFF5D6 0%, #C59D5F 60%, #B68D40 100%)',
                        'green-glow': 'radial-gradient(circle at center, rgba(118, 185, 0, 0.15) 0%, transparent 70%)',
                    }
                }
            }
        }
    </script>

    <style>
        body { background-color: #020202; color: #EAEAEA; overflow-x: hidden; }

        .noise-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: url("https://grainy-gradients.vercel.app/noise.svg");
            opacity: 0.04; pointer-events: none; z-index: 0;
        }

        .text-gold-gradient {
            background: linear-gradient(135deg, #FFF5D6 0%, #C59D5F 60%, #B68D40 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .text-blue-gradient {
            background: linear-gradient(135deg, #E0FFFF 0%, #00F0FF 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }

        .tech-card {
            background: rgba(10, 10, 12, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.4s ease;
        }
        .tech-card:hover {
            border-color: rgba(0, 240, 255, 0.3);
            box-shadow: 0 0 30px rgba(0, 240, 255, 0.1);
            transform: translateY(-5px);
        }

        .scanline {
            width: 100%; height: 2px;
            background: rgba(118, 185, 0, 0.1);
            position: absolute; top: 0; left: 0;
            animation: scan 3s linear infinite;
            z-index: 10; pointer-events: none;
        }
        @keyframes scan { to { top: 100%; } }

        .circuit-line {
            position: absolute; left: 50%; transform: translateX(-50%);
            width: 2px; height: 100%;
            background: linear-gradient(to bottom, transparent, #00F0FF, #C59D5F, transparent);
            box-shadow: 0 0 10px rgba(0, 240, 255, 0.5);
        }
    </style>
</head>
<body class="antialiased">

<div class="noise-overlay"></div>

@verbatim
<div id="app" class="relative z-10">

    <!-- Nav -->
    <nav class="fixed top-0 w-full z-50 border-b border-white/5 bg-bg/90 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full border border-gold bg-black flex items-center justify-center">
                    <span class="font-serif text-gold font-bold text-xl">IQ</span>
                </div>
                <span class="font-sans font-bold text-xl tracking-widest text-gold-gradient">IQBIT</span>
            </div>
            <a href="/login" class="px-5 py-2 border border-gold/40 text-gold text-xs font-mono hover:bg-gold hover:text-black transition duration-300">
                LOGIN
            </a>
        </div>
    </nav>

    <!-- 1. Vision -->
    <section id="vision" class="relative h-screen flex items-center justify-center overflow-hidden">
        <div id="hero-canvas" class="absolute inset-0 z-0"></div>

        <div class="relative z-10 text-center px-4 max-w-5xl">
            <div class="mb-6 inline-block">
                <span class="font-mono text-quantum text-[10px] tracking-[0.4em] uppercase border border-quantum/20 px-4 py-1 rounded-full bg-quantum/5">
                    System Operational
                </span>
            </div>

            <h1 class="font-serif text-5xl md:text-7xl lg:text-8xl tracking-tight mb-6 leading-tight text-white drop-shadow-2xl">
                QUANTUM<br>
                <span class="text-gold-gradient font-extrabold">GENESIS</span>
            </h1>

            <p class="font-sans text-textDim text-sm md:text-lg max-w-2xl mx-auto mb-12 font-light leading-relaxed">
                Capture value before the market reacts. <br>
                <span class="text-white">Nanosecond precision. Absolute dominance.</span>
            </p>

            <div class="flex justify-center gap-6">
                <a href="/register" class="px-8 py-4 bg-gold text-black font-bold font-sans text-sm hover:bg-white transition shadow-[0_0_20px_rgba(197,157,95,0.4)]">
                    BUILD YOUR WEALTH
                </a>
                <a href="/login" class="px-8 py-4 border border-white/20 text-white font-sans text-sm hover:border-quantum hover:text-quantum transition backdrop-blur-sm">
                    READ WHITEPAPER
                </a>
            </div>
        </div>
    </section>

    <!-- 2. Technology -->
    <section id="tech" class="py-32 px-6 bg-bgCard relative border-t border-white/5">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="font-serif text-3xl md:text-4xl text-white mb-4">The Engine</h2>
                <p class="font-mono text-xs text-quantum">WHY IQBIT IS UNBEATABLE</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="tech-card p-8 rounded-xl group relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-quantum/10 rounded-full blur-[50px] group-hover:bg-quantum/20 transition"></div>
                    <div class="text-4xl mb-6">⚡</div>
                    <h3 class="font-sans text-xl font-bold text-white mb-2">Zero Latency</h3>
                    <p class="text-sm text-textDim leading-relaxed">
                        Quantum simulation allows us to capture spreads <span class="text-quantum">before on-chain confirmation</span>. Traditional HFT is milliseconds; IQBIT is nanoseconds.
                    </p>
                </div>

                <div class="tech-card p-8 rounded-xl group relative overflow-hidden border-t-2 border-t-gold/50">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-gold/10 rounded-full blur-[50px] group-hover:bg-gold/20 transition"></div>
                    <div class="text-4xl mb-6">🛡️</div>
                    <h3 class="font-sans text-xl font-bold text-white mb-2">Atomic Execution</h3>
                    <p class="text-sm text-textDim leading-relaxed">
                        Trades are completed within the same block or <span class="text-gold">rolled back immediately</span>. 100% Capital protection mechanism.
                    </p>
                </div>

                <div class="tech-card p-8 rounded-xl group relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-32 h-32 bg-nvidia/10 rounded-full blur-[50px] group-hover:bg-nvidia/20 transition"></div>
                    <div class="text-4xl mb-6">🌐</div>
                    <h3 class="font-sans text-xl font-bold text-white mb-2">Multidimensional Scan</h3>
                    <p class="text-sm text-textDim leading-relaxed">
                        Simultaneous monitoring of <span class="text-nvidia">500+ Exchanges & DEXs</span> liquidity pools. We see the entire market topology at once.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. Live Terminal -->
    <section id="live" class="py-24 px-6 bg-black relative">
        <div class="max-w-5xl mx-auto">
             <div class="flex justify-between items-end mb-6">
                <h2 class="font-serif text-2xl text-white">Live Feed</h2>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 bg-nvidia rounded-full animate-pulse"></span>
                    <span class="font-mono text-xs text-nvidia">SYSTEM ACTIVE</span>
                </div>
            </div>

            <div class="w-full bg-[#050505] border border-[#333] rounded-lg p-6 shadow-2xl h-[400px] overflow-hidden relative font-mono text-xs md:text-sm">
                <div class="scanline"></div>
                <div class="absolute top-0 left-0 w-full h-8 bg-[#111] border-b border-[#333] flex items-center px-4 space-x-2">
                    <div class="w-3 h-3 rounded-full bg-[#FF5F56]"></div>
                    <div class="w-3 h-3 rounded-full bg-[#FFBD2E]"></div>
                    <div class="w-3 h-3 rounded-full bg-[#27C93F]"></div>
                    <span class="ml-4 text-gray-500 text-[10px]">root@qbit-core:~# ./arbitrage_bot --verbose</span>
                </div>

                <div class="mt-8 h-full flex flex-col-reverse overflow-hidden" id="terminal-logs">
                    <div v-for="log in logs" :key="log.id" class="mb-2 border-l-2 border-transparent hover:border-nvidia pl-2 transition-all opacity-0 animate-fade-in">
                        <span class="text-gray-500">[{{ log.time }}]</span>
                        <span class="text-quantum ml-2">EXECUTE >></span>
                        <span class="text-white ml-2">{{ log.pair }}</span>
                        <span class="text-gray-400 ml-2">>> GAS: ${{ log.gas }}</span>
                        <span class="text-nvidia ml-2 font-bold">>> PROFIT: +{{ log.profit }}%</span>
                        <span class="text-nvidia ml-2">[CONFIRMED]</span>
                    </div>
                </div>
            </div>
            <p class="text-center text-textDim text-xs mt-4">*Real-time feed from Mainnet Node #001</p>
        </div>
    </section>

    <!-- 4. Ecosystem -->
    <section id="ecosystem" class="py-32 px-6 bg-bg relative overflow-hidden">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="font-serif text-4xl text-white mb-6">The Node Ecosystem</h2>
            <p class="text-textDim text-sm mb-16 max-w-xl mx-auto">
                A hierarchical network designed for maximum liquidity capture.
                Higher tiers receive exponentially higher hashrate allocation.
            </p>

            <div class="relative flex flex-col items-center gap-4">
                <div class="relative group w-full md:w-[400px] z-30">
                    <div class="absolute inset-0 bg-gold blur-[20px] opacity-20 group-hover:opacity-40 transition duration-500"></div>
                    <div class="relative bg-gradient-to-b from-[#C59D5F] to-[#8E6E38] p-[1px] rounded-lg">
                        <div class="bg-black/80 backdrop-blur-xl p-6 rounded-lg text-center border border-gold/50 group-hover:border-gold transition cursor-pointer">
                            <div class="text-2xl mb-2">👑</div>
                            <h3 class="font-serif text-gold font-bold tracking-widest text-sm">30 FOUNDING PARTNERS</h3>
                            <p class="font-mono text-[10px] text-white/80 mt-2">Governance Rights • Genesis Dividends</p>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-[1px] bg-gradient-to-b from-gold to-quantum"></div>

                <div class="relative group w-full md:w-[500px] z-20">
                    <div class="bg-gradient-to-b from-quantum to-transparent p-[1px] rounded-lg">
                        <div class="bg-black/80 backdrop-blur-xl p-5 rounded-lg text-center border border-quantum/30 group-hover:border-quantum transition cursor-pointer">
                            <h3 class="font-sans text-quantum font-bold tracking-widest text-sm">25-TIER NODE NETWORK</h3>
                            <p class="font-mono text-[10px] text-textDim mt-2">Tier 1 (Entry) ➔ Tier 25 (Elite)</p>
                            <div class="mt-3 h-1 w-32 mx-auto bg-gray-800 rounded-full overflow-hidden">
                                <div class="h-full bg-quantum w-3/4"></div>
                            </div>
                            <p class="text-[9px] text-quantum mt-1">Hashrate Allocation: High</p>
                        </div>
                    </div>
                </div>

                <div class="h-8 w-[1px] bg-white/10"></div>

                <div class="w-full md:w-[600px] border border-white/10 p-4 rounded-lg text-center z-10 hover:bg-white/5 transition">
                    <span class="font-mono text-xs text-gray-500">GLOBAL LIQUIDITY PROVIDERS (DAO)</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 4.5 Security & Audit -->
    <section class="py-12 bg-bgCard border-y border-white/5">
        <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <svg class="w-8 h-8 text-nvidia" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <div>
                    <h4 class="font-sans text-white font-bold text-sm">Non-Custodial Architecture</h4>
                    <p class="font-mono text-[10px] text-textDim">Smart Contracts Verified & Audited</p>
                </div>
            </div>
            <div class="flex gap-8 opacity-40 grayscale">
                <div class="font-bold text-lg text-white">CERTIK</div>
                <div class="font-bold text-lg text-white">HACKEN</div>
                <div class="font-bold text-lg text-white">SLOWMIST</div>
            </div>
        </div>
    </section>

    <!-- 5. Roadmap -->
    <section class="py-32 px-6 bg-bg relative">
        <div class="max-w-3xl mx-auto relative">
            <h2 class="font-serif text-3xl text-center text-white mb-20">The Quantum Leap</h2>

            <div class="circuit-line"></div>

            <div class="relative mb-20 flex justify-start w-full md:w-1/2 md:pr-12 md:ml-0 ml-8">
                <div class="absolute right-[-6px] md:right-[-7px] top-2 w-3 h-3 bg-bg border-2 border-gold rounded-full z-10 shadow-[0_0_10px_#C59D5F]"></div>
                <div class="text-right pr-6 w-full">
                    <div class="font-mono text-gold text-xs mb-1">PHASE 1</div>
                    <h3 class="font-sans text-xl text-white font-bold mb-2">GOVERNANCE LAYER ESTABLISHMENT</h3>
                    <p class="text-textDim text-sm">Assembly of the Founder Team. Definition of protocol direction and economic model.</p>
                </div>
            </div>

            <div class="relative mb-20 flex justify-end w-full md:w-1/2 md:pl-12 md:ml-auto ml-8">
                <div class="absolute left-[-22px] md:left-[-7px] top-2 w-3 h-3 bg-bg border-2 border-quantum rounded-full z-10 shadow-[0_0_10px_#00F0FF]"></div>
                <div class="text-left pl-6 w-full">
                    <div class="font-mono text-quantum text-xs mb-1">PHASE 2</div>
                    <h3 class="font-sans text-xl text-white font-bold mb-2">WEALTH LAYER ACTIVATION</h3>
                    <p class="text-textDim text-sm">Launch of the 25-Tier Genesis Node Network. Full operation of hashrate allocation system.</p>
                </div>
            </div>

            <div class="relative flex justify-start w-full md:w-1/2 md:pr-12 md:ml-0 ml-8">
                <div class="absolute right-[-6px] md:right-[-7px] top-2 w-3 h-3 bg-bg border-2 border-nvidia rounded-full z-10"></div>
                <div class="text-right pr-6 w-full">
                    <div class="font-mono text-nvidia text-xs mb-1">PHASE 3</div>
                    <h3 class="font-sans text-xl text-white font-bold mb-2">GLOBAL DAO &amp; ECOSYSTEM</h3>
                    <p class="text-textDim text-sm">Transition to decentralized community governance. Integration of global liquidity providers.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Footer -->
    <footer class="bg-bgCard border-t border-white/5 pt-20 pb-10">
        <div class="max-w-6xl mx-auto px-6 grid grid-cols-1 md:grid-cols-2 gap-12 items-center mb-16">
            <div>
                <h2 class="font-serif text-3xl text-white mb-4">Ready to dominate?</h2>
                <p class="text-textDim text-sm mb-6">Join the IQBIT network and start earning quantum yields today.</p>
                <div class="flex gap-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">Twitter / X</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Discord</a>
                    <a href="#" class="text-gray-400 hover:text-white transition">Telegram</a>
                </div>
            </div>
            <div class="text-center md:text-right">
                <a href="/register" class="inline-block bg-gold text-black font-bold font-sans text-lg px-12 py-5 rounded-sm hover:bg-white hover:scale-105 transition duration-300 shadow-[0_0_30px_rgba(197,157,95,0.3)]">
                    BECOME A NODE
                </a>
            </div>
        </div>

        <div class="text-center border-t border-white/5 pt-8">
            <p class="font-mono text-[10px] text-gray-600">
                © 2025 IQBIT QUANTUM SYSTEMS. ALL RIGHTS RESERVED.<br>
                RISK DISCLOSURE: CRYPTOCURRENCY TRADING INVOLVES SUBSTANTIAL RISK.
            </p>
        </div>
    </footer>

</div>
@endverbatim

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s forwards; }
</style>

<script>
    const { createApp, ref, onMounted } = Vue;

    createApp({
        setup() {
            const logs = ref([]);
            const pairs = [
                'BTC/USDT', 'ETH/USDC', 'SOL/ETH', 'BNB/USDT', 'XRP/BTC', 'ADA/USDT'
            ];
            const platforms = ['BINANCE', 'UNISWAP', 'COINBASE', 'OKX', 'BYBIT'];

            function addLog() {
                const now = new Date();
                const time = now.toTimeString().split(' ')[0];
                const pair = pairs[Math.floor(Math.random() * pairs.length)];
                const platform1 = platforms[Math.floor(Math.random() * platforms.length)];
                const platform2 = platforms[Math.floor(Math.random() * platforms.length)];

                if (platform1 === platform2) return;

                logs.value.unshift({
                    id: Date.now(),
                    time: time,
                    pair: `${platform1} <> ${platform2} [${pair}]`,
                    gas: (Math.random() * 5 + 1).toFixed(2),
                    profit: (Math.random() * 0.4 + 0.05).toFixed(2)
                });

                if (logs.value.length > 7) logs.value.pop();
            }

            function initThreeJS() {
                const container = document.getElementById('hero-canvas');
                const scene = new THREE.Scene();
                const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
                const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
                renderer.setSize(window.innerWidth, window.innerHeight);
                container.appendChild(renderer.domElement);

                const geometry = new THREE.IcosahedronGeometry(2.5, 1);
                const material = new THREE.MeshBasicMaterial({
                    color: 0xC59D5F,
                    wireframe: true,
                    transparent: true,
                    opacity: 0.15
                });
                const sphere = new THREE.Mesh(geometry, material);
                scene.add(sphere);

                const coreGeo = new THREE.IcosahedronGeometry(1, 0);
                const coreMat = new THREE.MeshBasicMaterial({ color: 0x76B900, wireframe: true, transparent: true, opacity: 0.3 });
                const core = new THREE.Mesh(coreGeo, coreMat);
                scene.add(core);

                const pGeo = new THREE.BufferGeometry();
                const pCount = 800;
                const pPos = new Float32Array(pCount * 3);
                for (let i = 0; i < pCount * 3; i++) pPos[i] = (Math.random() - 0.5) * 15;
                pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
                const pMat = new THREE.PointsMaterial({ size: 0.03, color: 0x00F0FF, transparent: true, opacity: 0.4 });
                const particles = new THREE.Points(pGeo, pMat);
                scene.add(particles);

                camera.position.z = 6;

                let mouseX = 0, mouseY = 0;
                document.addEventListener('mousemove', (e) => {
                    mouseX = (e.clientX / window.innerWidth - 0.5) * 2;
                    mouseY = (e.clientY / window.innerHeight - 0.5) * 2;
                });

                function animate() {
                    requestAnimationFrame(animate);
                    sphere.rotation.y += 0.002;
                    sphere.rotation.x += 0.001;
                    core.rotation.y -= 0.004;
                    particles.rotation.y += 0.0005;
                    sphere.rotation.x += mouseY * 0.01;
                    sphere.rotation.y += mouseX * 0.01;
                    renderer.render(scene, camera);
                }
                animate();

                window.addEventListener('resize', () => {
                    camera.aspect = window.innerWidth / window.innerHeight;
                    camera.updateProjectionMatrix();
                    renderer.setSize(window.innerWidth, window.innerHeight);
                });
            }

            onMounted(() => {
                setInterval(addLog, 600);
                initThreeJS();
                gsap.from("#tech .tech-card", {
                    scrollTrigger: { trigger: "#tech", start: "top 70%" },
                    y: 50, opacity: 0, stagger: 0.2, duration: 0.8
                });
            });

            return { logs };
        }
    }).mount('#app');
</script>
</body>
</html>
