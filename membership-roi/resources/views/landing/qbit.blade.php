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

      $langKey = app()->getLocale() === 'zh_CN' ? 'zh-CN' : 'en-US';
      $otherLangQuery = app()->getLocale() === 'zh_CN' ? 'en' : 'zh_CN';
      $switchLangUrl = request()->fullUrlWithQuery(['lang' => $otherLangQuery]);

      $dataPath = resource_path('content/deai_nexus.json');
      $deai = file_exists($dataPath) ? json_decode((string) file_get_contents($dataPath), true) : [];
      $groups = is_array($deai['groups'] ?? null) ? $deai['groups'] : [];

      // Helper: render markdown but strip all links (<a href=...>) so there are no external link dependencies.
      $render = function (?string $md): string {
          $html = \Illuminate\Support\Str::markdown((string) $md);
          // Strip anchor tags but keep visible text
          $html = preg_replace('~<a\\b[^>]*>(.*?)</a>~is', '$1', $html) ?? $html;
          return $html;
      };
    @endphp
    <link rel="icon" href="{{ $faviconPath ? asset($faviconPath) : '/favicon.ico' }}">

    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
      /* Homepage-only palette: gold / black / white / grey */
      :root {
        --gold: #C59D5F;
        --gold-2: #F3E5B5;
        --ink: #050505;
        --panel: rgba(255, 255, 255, 0.06);
        --panel-2: rgba(255, 255, 255, 0.04);
        --stroke: rgba(197, 157, 95, 0.28);
        --muted: rgba(255, 255, 255, 0.70);
      }

      body {
        background: radial-gradient(1100px 600px at 20% 0%, rgba(197, 157, 95, 0.12) 0%, transparent 60%),
                    radial-gradient(900px 520px at 80% 10%, rgba(243, 229, 181, 0.10) 0%, transparent 55%),
                    #000;
        color: #fff;
      }

      .gold-text { color: var(--gold); }
      .panel {
        background: var(--panel);
        border: 1px solid var(--stroke);
        border-radius: 18px;
        box-shadow: 0 24px 70px rgba(0,0,0,0.65);
      }
      .panel-muted { background: var(--panel-2); border: 1px solid rgba(255,255,255,0.08); border-radius: 18px; }
      .kbd {
        display: inline-flex;
        align-items: center;
        padding: 0.15rem 0.45rem;
        border-radius: 0.5rem;
        background: rgba(255,255,255,0.06);
        border: 1px solid rgba(255,255,255,0.12);
        color: rgba(255,255,255,0.85);
        font-size: 12px;
      }

      /* Markdown rendering (exact text preserved; only presentation styled) */
      .content h1, .content h2, .content h3 { color: #fff; font-weight: 700; }
      .content h2 { margin-top: 1rem; font-size: 1.125rem; }
      .content h3 { margin-top: .75rem; font-size: 1rem; }
      .content p { margin-top: .75rem; color: var(--muted); line-height: 1.75; }
      .content ul, .content ol { margin-top: .75rem; padding-left: 1.25rem; color: var(--muted); }
      .content li { margin-top: .25rem; }
      .content code { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.10); padding: 0 .35rem; border-radius: .35rem; }
      .content pre { margin-top: .75rem; background: #0b0b0c; border: 1px solid rgba(197,157,95,0.22); border-radius: 14px; padding: .9rem; overflow-x:auto; }
      .content pre code { background: transparent; border: none; padding: 0; }
      .content hr { margin: 1rem 0; border: none; border-top: 1px solid rgba(197,157,95,0.22); }

      [x-cloak] { display: none !important; }
    </style>
  </head>

  <body>
    <div
      class="min-h-screen"
      x-data="{
        q: '',
        openGroup: null,
        openSection: null,
        filter() {
          const q = (this.q || '').trim().toLowerCase();
          const sections = document.querySelectorAll('[data-deai-section]');
          for (const el of sections) {
            if (!q) { el.classList.remove('hidden'); continue; }
            const txt = (el.textContent || '').toLowerCase();
            el.classList.toggle('hidden', !txt.includes(q));
          }
        },
        toggleGroup(id) { this.openGroup = (this.openGroup === id ? null : id); },
        toggleSection(id) { this.openSection = (this.openSection === id ? null : id); }
      }"
      x-init="$watch('q', () => filter())"
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header / Hero (new layout, same content) -->
        <header class="flex flex-col gap-6">
          <div class="flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-4 min-w-0">
              <div class="w-14 h-14 rounded-2xl ring-1 ring-[rgba(197,157,95,0.28)] bg-white/5 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/Logo01.png') }}" alt="Logo" class="w-full h-full object-contain" />
              </div>
              <div class="min-w-0">
                <div class="text-xl font-semibold tracking-wide">DeAI Nexus Space</div>
                <div class="text-sm text-white/70">
                  {{ $langKey === 'zh-CN' ? '去中心化 AI 算力网络平台' : 'Decentralized AI Computing Network Platform' }}
                </div>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <a href="{{ $switchLangUrl }}" class="btn-neutral normal-case text-sm">
                {{ $langKey === 'zh-CN' ? 'Switch to English' : '切换中文' }}
              </a>
              <a href="{{ route('login') }}" class="btn-primary normal-case text-sm">{{ $langKey === 'zh-CN' ? '登录' : 'Login' }}</a>
            </div>
          </div>

          <div class="panel p-6 md:p-8">
            @php
              $introGroup = collect($groups)->firstWhere('id', 'project_introduction');
              $intro = $introGroup['children'][0]['content'][$langKey] ?? '';
            @endphp

            <div class="flex flex-col lg:flex-row gap-6 lg:items-start">
              <div class="flex-1 min-w-0">
                <div class="text-xs tracking-[0.35em] uppercase text-white/60">On‑chain AI</div>
                <h1 class="mt-3 text-3xl md:text-4xl font-extrabold leading-tight">
                  <span class="gold-text">DeAI Nexus</span>
                  <span class="text-white/90">{{ $langKey === 'zh-CN' ? '内容总览' : 'Content Overview' }}</span>
                </h1>
                <p class="mt-3 text-white/70 leading-relaxed">
                  {{ $langKey === 'zh-CN'
                    ? '以下内容与 deainexus.space 的文字保持一致，仅更换为全新 UI 结构与风格。'
                    : 'All wording below matches deainexus.space exactly; only the UI structure and styling are redesigned.' }}
                </p>
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-white/70">
                  <span class="kbd">{{ $langKey === 'zh-CN' ? '搜索' : 'Search' }}</span>
                  <span>{{ $langKey === 'zh-CN' ? '在下面输入关键词过滤内容' : 'Type keywords below to filter content' }}</span>
                </div>
              </div>

              <div class="w-full lg:w-[420px] panel-muted p-4 md:p-5">
                <div class="text-sm font-semibold text-white">{{ $langKey === 'zh-CN' ? '快速搜索' : 'Quick Search' }}</div>
                <div class="mt-3">
                  <input
                    x-model="q"
                    type="text"
                    class="input !bg-white/5 !text-white !ring-white/15 placeholder:!text-white/50"
                    placeholder="{{ $langKey === 'zh-CN' ? '输入关键词…' : 'Type keywords…' }}"
                  />
                </div>
                <div class="mt-3 text-xs text-white/55">
                  {{ $langKey === 'zh-CN'
                    ? '提示：所有链接已移除（仅保留可见文本），不依赖任何外部脚本或域名。'
                    : 'Note: links are removed (visible text preserved). No external scripts/domains are used.' }}
                </div>
              </div>
            </div>
          </div>
        </header>

        <!-- New structure: left “library” index + right content chapters -->
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
          <aside class="lg:col-span-4 xl:col-span-3">
            <div class="panel p-5 sticky top-6">
              <div class="text-sm font-semibold text-white">{{ $langKey === 'zh-CN' ? '内容目录' : 'Library Index' }}</div>
              <div class="mt-3 space-y-2">
                @foreach ($groups as $g)
                  @php
                    $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
                  @endphp
                  <button
                    type="button"
                    class="w-full text-left px-3 py-2 rounded-xl border border-[rgba(255,255,255,0.10)] hover:border-[rgba(197,157,95,0.35)] hover:bg-white/5 transition"
                    @click="toggleGroup(@js($g['id']))"
                  >
                    <div class="flex items-center justify-between gap-3">
                      <div class="text-white/90 font-medium">{{ $gTitle }}</div>
                      <div class="text-white/60 text-xs" x-text="openGroup === @js($g['id']) ? '—' : '+'"></div>
                    </div>
                  </button>
                @endforeach
              </div>
              <div class="mt-4 text-xs text-white/55">
                {{ $langKey === 'zh-CN' ? '点击目录展开章节；右侧为全新排版呈现。' : 'Click an item to open its chapter. The right side shows the redesigned presentation.' }}
              </div>
            </div>
          </aside>

          <main class="lg:col-span-8 xl:col-span-9 space-y-6">
            @foreach ($groups as $g)
              @php
                $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
                $children = is_array($g['children'] ?? null) ? $g['children'] : [];
              @endphp

              <section class="panel p-6" x-show="!openGroup || openGroup === @js($g['id'])" x-cloak>
                <div class="flex items-center justify-between gap-4">
                  <div>
                    <div class="text-xs tracking-[0.35em] uppercase text-white/60">{{ $g['id'] }}</div>
                    <h2 class="mt-2 text-2xl font-bold text-white">{{ $gTitle }}</h2>
                  </div>
                  <button
                    type="button"
                    class="btn-neutral normal-case text-sm"
                    @click="openGroup = @js($g['id']); openSection = null;"
                  >
                    {{ $langKey === 'zh-CN' ? '聚焦本章' : 'Focus chapter' }}
                  </button>
                </div>

                <div class="mt-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                  @foreach ($children as $c)
                    @php
                      $cTitle = $c['title'][$langKey] ?? $c['title']['en-US'] ?? $c['id'];
                      $md = $c['content'][$langKey] ?? $c['content']['en-US'] ?? '';
                      $html = $render($md);
                    @endphp
                    <article class="panel-muted p-4 md:p-5" data-deai-section>
                      <button
                        type="button"
                        class="w-full text-left"
                        @click="toggleSection(@js($c['id']))"
                      >
                        <div class="flex items-start justify-between gap-3">
                          <div class="text-white font-semibold leading-snug">{{ $cTitle }}</div>
                          <div class="text-white/60 text-xs mt-1" x-text="openSection === @js($c['id']) ? 'Hide' : 'Show'"></div>
                        </div>
                        <div class="mt-1 text-xs text-white/55">{{ $c['id'] }}</div>
                      </button>

                      <div class="mt-4 content" x-show="openSection === @js($c['id'])" x-cloak>
                        {!! $html !!}
                      </div>
                    </article>
                  @endforeach
                </div>
              </section>
            @endforeach
          </main>
        </div>
      </div>
    </div>
  </body>
</html>
