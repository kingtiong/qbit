<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
      /* Override app-wide dark base styles for this landing page only */
      body {
        background: linear-gradient(135deg, #f6f5ff 0%, #ffffff 45%, #e1f2ff 100%);
        color: #0f172a;
      }

      /* Minimal markdown-ish styling (no external typography plugin) */
      .content h1, .content h2, .content h3 { font-weight: 700; color: #0f172a; }
      .content h2 { font-size: 1.25rem; line-height: 1.75rem; margin-top: 1.25rem; }
      .content h3 { font-size: 1.05rem; line-height: 1.5rem; margin-top: 1rem; }
      .content p { margin-top: .75rem; color: #334155; line-height: 1.7; }
      .content ul { margin-top: .75rem; padding-left: 1.25rem; list-style: disc; color: #334155; }
      .content ol { margin-top: .75rem; padding-left: 1.25rem; list-style: decimal; color: #334155; }
      .content li { margin-top: .25rem; }
      .content code { background: rgba(15, 23, 42, 0.06); padding: 0 .35rem; border-radius: .35rem; font-size: .875em; }
      .content pre { margin-top: .75rem; padding: .9rem; border-radius: .75rem; background: #0b1220; color: #e2e8f0; overflow-x: auto; }
      .content pre code { background: transparent; padding: 0; }

      /* Ensure no clickable external links from copied source */
      .content a { pointer-events: none; color: inherit; text-decoration: none; }
    </style>
  </head>
  <body>
    @php
      $isZh = app()->getLocale() === 'zh_CN';
      $langEnUrl = request()->fullUrlWithQuery(['lang' => 'en']);
      $langZhUrl = request()->fullUrlWithQuery(['lang' => 'zh_CN']);

      // NOTE: External links are intentionally rendered as plain text (no <a href>)
      // to remove hard-coded/external dependencies per requirements.
      $sections = [
        [
          'key' => 'overview',
          'title' => $isZh ? '概览' : 'Overview',
          'body' => $isZh
            ? "DeAI Nexus 是「可在链上原生运行、调用与验证 AI 模型」的去中心化 AI 基础设施，让 AI 从「黑盒服务」变为「可证明、可治理、可组合」的链上能力。"
            : "DeAI Nexus is a decentralized AI infrastructure that can run, call, and verify AI models natively on-chain, turning AI from black-box services into provable, governable, composable on-chain capabilities.",
        ],
        [
          'key' => 'technology',
          'title' => $isZh ? '技术与特色' : 'Technology & Features',
          'body' => $isZh
            ? "核心方向：可验证推理（TEE + ZKP/zkML）、隐私保护、并行计算、跨模型兼容、抗审查与去中心化协作训练。"
            : "Core focus: verifiable inference (TEE + ZKP/zkML), privacy, parallel compute, cross-model compatibility, censorship resistance, and decentralized collaborative training.",
        ],
        [
          'key' => 'applications',
          'title' => $isZh ? '应用' : 'Applications',
          'body' => $isZh
            ? "- Parrot V1：对话式 AI 入口\\n- 一键发行与部署：自然语言生成合约并部署\\n- Nexus Finance：AI 自适应金融协议\\n- ADEX：AI 驱动 DEX"
            : "- Parrot V1: conversational AI portal\\n- One-click deployment: natural-language contracts\\n- Nexus Finance: AI-adaptive DeFi\\n- ADEX: AI-driven DEX",
        ],
        [
          'key' => 'tokenomics',
          'title' => $isZh ? '代币经济' : 'Tokenomics',
          'body' => $isZh
            ? "双代币：DEAI（治理）+ DEAI‑T（质押/算力凭证）。\\n\\nDEAI‑T 合约地址（文本展示）：\\n`0x9F10a122a921d25Fa9084955c87c3FcAB103267F`"
            : "Dual-token: DEAI (governance) + DEAI‑T (staking / compute certificate).\\n\\nDEAI‑T contract (display-only):\\n`0x9F10a122a921d25Fa9084955c87c3FcAB103267F`",
        ],
        [
          'key' => 'value-capture',
          'title' => $isZh ? '价值捕获' : 'Value Capture',
          'body' => $isZh
            ? "价值来源：可验证推理与链上 AI 合约化带来的开发者生态、数据/模型资产化、协议费用与通缩机制。"
            : "Value drivers: verifiable inference and AI-as-contract enabling developer ecosystem, model/data assetization, protocol fees, and deflation mechanisms.",
        ],
        [
          'key' => 'audit',
          'title' => $isZh ? '审计与安全' : 'Audit & Security',
          'body' => $isZh
            ? "强调非托管架构、链上可验证、分阶段开源与第三方审计。\\n\\n（外部审计/资源链接已移除，仅保留文字内容。）"
            : "Focus on non-custodial architecture, on-chain verifiability, phased open-source strategy, and third-party audits.\\n\\n(External audit/resource links removed; text retained.)",
        ],
        [
          'key' => 'tools',
          'title' => $isZh ? '工具' : 'Tools',
          'body' => $isZh
            ? "本页面工具类内容已改为内部实现占位（不请求外部 API）。需要接入时，请使用项目内路由与后端服务。"
            : "Tools are implemented as internal placeholders (no external API calls). If you need integrations, use project routes and backend services.",
        ],
        [
          'key' => 'data',
          'title' => $isZh ? '数据' : 'Data',
          'body' => $isZh
            ? "所有展示数据应通过本项目后端/数据库提供。外部数据源依赖已移除。"
            : "All displayed data should come from this app’s backend/database. External data dependencies removed.",
        ],
        [
          'key' => 'roadshow',
          'title' => $isZh ? '路演' : 'Roadshow',
          'body' => $isZh
            ? "路演与活动内容保留为可编辑文本块（不含外链）。"
            : "Roadshow and activities are kept as editable text blocks (no external links).",
        ],
        [
          'key' => 'roadmap',
          'title' => $isZh ? '路线图' : 'Roadmap',
          'body' => $isZh
            ? "- Phase 1：基础设施与治理\\n- Phase 2：核心功能与网络激活\\n- Phase 3：生态扩展与全球 DAO"
            : "- Phase 1: foundation & governance\\n- Phase 2: core activation\\n- Phase 3: ecosystem expansion & global DAO",
        ],
        [
          'key' => 'dapp',
          'title' => $isZh ? 'DApp' : 'DApp',
          'body' => $isZh
            ? "DApp 入口与交互将使用本项目已有的认证/路由体系实现。外部域名入口已移除。"
            : "DApp entry and interactions should use this project’s auth and routing. External domain entrypoints removed.",
        ],
      ];
    @endphp

    <div
      class="min-h-screen w-full px-4 py-8 md:px-10"
      x-data="{ q: '', open: null }"
    >
      <div class="mx-auto max-w-6xl relative">
        <div class="absolute inset-0 -z-10 opacity-60 blur-3xl pointer-events-none bg-gradient-to-r from-violet-200 via-white to-sky-100"></div>

        <header class="flex items-start justify-between gap-4 flex-wrap">
          <div class="flex items-center gap-4">
            <img
              src="{{ asset('images/Logo01.png') }}"
              alt="App logo"
              class="w-14 h-14 rounded-2xl shadow-lg shadow-slate-300/40 bg-white/90 p-2 object-contain"
            />
            <div class="min-w-0">
              <div class="text-slate-900 font-semibold leading-tight text-lg">DeAI Nexus Space</div>
              <div class="text-slate-600 text-sm leading-tight">
                {{ $isZh ? '去中心化 AI 计算网络平台（内部实现）' : 'Decentralized AI Computing Network (internal implementation)' }}
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ $langEnUrl }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-100">
              EN
            </a>
            <a href="{{ $langZhUrl }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-100">
              中文
            </a>
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
              {{ $isZh ? '登录' : 'Login' }}
            </a>
          </div>
        </header>

        <div class="mt-6 rounded-xl border border-slate-200 bg-white shadow-sm p-4">
          <div class="flex flex-col md:flex-row md:items-center gap-3">
            <div class="text-sm text-slate-700 font-medium">{{ $isZh ? '搜索内容' : 'Search content' }}</div>
            <input
              type="text"
              class="w-full md:max-w-xl rounded-lg border-slate-300 bg-white text-slate-900"
              x-model="q"
              placeholder="{{ $isZh ? '输入关键词…' : 'Type keywords…' }}"
            />
          </div>
        </div>

        <main class="mt-6 space-y-3">
          @foreach ($sections as $idx => $s)
            @php
              $body = (string) ($s['body'] ?? '');
              // Convert plain newlines to markdown paragraphs/bullets via Str::markdown
              $html = \Illuminate\Support\Str::markdown($body);
              // Strip anchors if any survived markdown conversion
              $html = preg_replace('~<a\\b[^>]*>(.*?)</a>~is', '$1', $html);
              $search = strtolower(($s['title'] ?? '') . ' ' . strip_tags($html));
            @endphp

            <section
              class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden"
              x-show="!q || @json($search).includes(q.toLowerCase())"
            >
              <button
                type="button"
                class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left"
                @click="open === {{ $idx }} ? open = null : open = {{ $idx }}"
              >
                <div class="text-slate-900 font-semibold">{{ $s['title'] }}</div>
                <div class="text-slate-500 text-sm" x-text="open === {{ $idx }} ? '−' : '+'"></div>
              </button>
              <div class="px-5 pb-5 content" x-show="open === {{ $idx }}" x-cloak>
                {!! $html !!}
              </div>
            </section>
          @endforeach
        </main>

        <footer class="mt-8 text-xs text-slate-500">
          {{ $isZh
            ? '注：本页面为内部重构版本，已移除外部域名依赖、硬编码外链与遗留脚本；内容与布局可在本项目中直接维护。'
            : 'Note: This is an internally refactored version. External domain dependencies, hard-coded external links, and legacy scripts have been removed; content/layout are maintained within this project.' }}
        </footer>
      </div>
    </div>
  </body>
</html>
