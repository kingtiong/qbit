<!doctype html>
<html lang="{{ app()->getLocale() === 'zh_CN' ? 'zh-CN' : 'en' }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DeAI Nexus Space</title>

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
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      .glass {
        background: rgba(255, 255, 255, 0.75);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(15, 23, 42, 0.08);
      }
    </style>
  </head>
  <body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-sky-50 text-slate-900">
    @php
      $isZh = app()->getLocale() === 'zh_CN';
      $langEnUrl = request()->fullUrlWithQuery(['lang' => 'en']);
      $langZhUrl = request()->fullUrlWithQuery(['lang' => 'zh_CN']);
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 md:px-8">
      <header class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
          <img
            src="{{ asset('assets/Logo01.png') }}"
            alt="Logo"
            class="h-10 w-10 rounded-xl object-cover ring-1 ring-slate-900/10"
          />
          <div class="min-w-0">
            <div class="font-semibold leading-tight truncate">DeAI Nexus Space</div>
            <div class="text-xs text-slate-600 leading-tight truncate">
              {{ $isZh ? '去中心化 AI 计算网络平台' : 'Decentralized AI Computing Network Platform' }}
            </div>
          </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <a href="{{ $langEnUrl }}" class="px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white hover:bg-slate-50">EN</a>
          <a href="{{ $langZhUrl }}" class="px-3 py-2 text-sm rounded-lg border border-slate-200 bg-white hover:bg-slate-50">中文</a>
          <a href="{{ route('login') }}" class="px-4 py-2 text-sm rounded-lg bg-slate-900 text-white hover:bg-slate-800">
            {{ $isZh ? '登录' : 'Login' }}
          </a>
        </div>
      </header>

      <main class="mt-8 space-y-6">
        <section class="glass rounded-2xl p-6 md:p-8">
          <div class="text-xs font-medium text-slate-500 tracking-wider uppercase">{{ $isZh ? '概览' : 'Overview' }}</div>
          <h1 class="mt-2 text-2xl md:text-3xl font-bold">
            {{ $isZh ? 'DeAI Nexus：可验证、可治理、可组合的链上 AI 基础设施' : 'DeAI Nexus: verifiable, governable, composable on-chain AI infrastructure' }}
          </h1>
          <p class="mt-3 text-slate-700 leading-relaxed">
            {{ $isZh
              ? 'DeAI Nexus 让 AI 从“黑盒服务”升级为“可证明、可治理、可组合”的链上能力：模型可调用、结果可验证、激励可分配。'
              : 'DeAI Nexus upgrades AI from “black-box services” into on-chain capabilities that are callable, verifiable, and incentive-aligned.' }}
          </p>
          <div class="mt-4 flex flex-wrap gap-2 text-sm">
            <a class="underline text-slate-900" href="https://chat.deainexus.com" target="_blank" rel="noreferrer">
              {{ $isZh ? '体验入口：chat.deainexus.com' : 'Experience: chat.deainexus.com' }}
            </a>
          </div>
        </section>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="glass rounded-2xl p-5">
            <div class="font-semibold">{{ $isZh ? '可验证推理' : 'Verifiable Inference' }}</div>
            <div class="mt-1 text-sm text-slate-700">
              {{ $isZh ? '通过 TEE / ZKP / zkML 等机制，让推理结果可验证、可追溯。' : 'Proof mechanisms (TEE/ZKP/zkML) make inference verifiable and auditable.' }}
            </div>
          </div>
          <div class="glass rounded-2xl p-5">
            <div class="font-semibold">{{ $isZh ? '生态共建' : 'Ecosystem Co-building' }}</div>
            <div class="mt-1 text-sm text-slate-700">
              {{ $isZh ? '支持可信协作与生态共建：开发者、节点、应用、社区共同参与。' : 'Builders, nodes, apps, and communities co-build with aligned incentives.' }}
            </div>
          </div>
          <div class="glass rounded-2xl p-5">
            <div class="font-semibold">{{ $isZh ? '透明与治理' : 'Transparency & Governance' }}</div>
            <div class="mt-1 text-sm text-slate-700">
              {{ $isZh ? '关键数据与规则可公开验证；治理与激励可持续迭代。' : 'Key data/rules are verifiable; governance and incentives evolve sustainably.' }}
            </div>
          </div>
        </section>

        <section class="glass rounded-2xl p-6 md:p-8">
          <div class="text-xs font-medium text-slate-500 tracking-wider uppercase">{{ $isZh ? '应用' : 'Applications' }}</div>
          <h2 class="mt-2 text-xl font-bold">{{ $isZh ? '项目应用与入口' : 'Project Applications & Entrypoints' }}</h2>
          <ul class="mt-3 space-y-2 text-slate-700">
            <li>- <span class="font-medium">Parrot V1</span> — {{ $isZh ? '对话式 AI 入口（链接模型/代理/应用体验）。' : 'Conversational AI portal connecting models/agents/apps.' }}</li>
            <li>- <span class="font-medium">One-click Deployment</span> — {{ $isZh ? '自然语言生成合约，一键部署到目标链。' : 'Natural language contract generation and one-click deploy.' }}</li>
            <li>- <span class="font-medium">Nexus Finance</span> — {{ $isZh ? 'AI 自适应金融协议（收益/风控/再平衡）。' : 'AI-adaptive DeFi protocol (yield/risk/rebalance).' }}</li>
          </ul>
        </section>

        <section class="glass rounded-2xl p-6 md:p-8">
          <div class="text-xs font-medium text-slate-500 tracking-wider uppercase">{{ $isZh ? '代币经济' : 'Tokenomics' }}</div>
          <h2 class="mt-2 text-xl font-bold">{{ $isZh ? '核心地址与代币说明（可编辑）' : 'Core Addresses & Token Notes (editable)' }}</h2>
          <div class="mt-3 text-slate-700 space-y-3">
            <div>
              <div class="font-medium">{{ $isZh ? 'DEAI-T 合约地址' : 'DEAI-T Contract' }}</div>
              <div class="mt-1 font-mono text-sm break-all">0x9F10a122a921d25Fa9084955c87c3FcAB103267F</div>
              <div class="mt-1 text-xs text-slate-500">{{ $isZh ? '（来自参考站点内容；如需替换可直接改这里）' : '(From reference-site content; edit here if you need changes)' }}</div>
            </div>
            <div>
              <div class="font-medium">{{ $isZh ? '机制摘要' : 'Mechanism Summary' }}</div>
              <ul class="mt-1 text-sm space-y-1">
                <li>- {{ $isZh ? '有效质押率 80%（示例口径）' : '80% effective staking rate (example basis)' }}</li>
                <li>- {{ $isZh ? '通缩：交易销毁/质押折损/定期回购（按实际规则可改）' : 'Deflation: burn/loss/buyback (adjust to actual rules)' }}</li>
              </ul>
            </div>
          </div>
        </section>

        <section class="glass rounded-2xl p-6 md:p-8">
          <div class="text-xs font-medium text-slate-500 tracking-wider uppercase">{{ $isZh ? '路线图' : 'Roadmap' }}</div>
          <h2 class="mt-2 text-xl font-bold">{{ $isZh ? '阶段规划（简化版）' : 'Phases (simplified)' }}</h2>
          <div class="mt-3 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-slate-700">
            <div class="rounded-xl border border-slate-900/10 bg-white p-4">
              <div class="font-semibold">{{ $isZh ? '阶段 1' : 'Phase 1' }}</div>
              <div class="mt-1">{{ $isZh ? '治理与基础设施搭建。' : 'Governance + infrastructure foundation.' }}</div>
            </div>
            <div class="rounded-xl border border-slate-900/10 bg-white p-4">
              <div class="font-semibold">{{ $isZh ? '阶段 2' : 'Phase 2' }}</div>
              <div class="mt-1">{{ $isZh ? '核心功能上线与生态扩展。' : 'Core launch and ecosystem expansion.' }}</div>
            </div>
            <div class="rounded-xl border border-slate-900/10 bg-white p-4">
              <div class="font-semibold">{{ $isZh ? '阶段 3' : 'Phase 3' }}</div>
              <div class="mt-1">{{ $isZh ? '社区治理深化与多应用落地。' : 'Deeper governance and multi-app adoption.' }}</div>
            </div>
          </div>
        </section>
      </main>

      <footer class="mt-10 pb-6 text-xs text-slate-500">
        <div class="flex flex-wrap items-center justify-between gap-3">
          <div>© {{ date('Y') }} DeAI Nexus Space</div>
          <div>{{ $isZh ? '语言切换：使用顶部 EN/中文 按钮（?lang=）' : 'Language: use EN/中文 buttons (via ?lang=)' }}</div>
        </div>
      </footer>
    </div>
  </body>
</html>
