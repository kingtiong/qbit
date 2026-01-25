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

    @php
      $langKey = app()->getLocale() === 'zh_CN' ? 'zh-CN' : 'en-US';
      $contentPath = resource_path('content/deainexus_SD_groups.json');
      $payload = file_exists($contentPath) ? json_decode((string) file_get_contents($contentPath), true) : [];
      $groups = is_array($payload['groups'] ?? null) ? $payload['groups'] : [];

      $renderMarkdownWithTables = function (string $md): string {
          // Extract markdown table blocks (lines starting with '|') and render as HTML tables,
          // then run Str::markdown on remaining text.
          $lines = preg_split("/\\r?\\n/", $md);
          $tables = [];
          $outLines = [];
          $i = 0;
          while ($i < count($lines)) {
              $line = $lines[$i];
              if (preg_match('/^\\|.+\\|\\s*$/', $line)) {
                  $block = [];
                  while ($i < count($lines) && preg_match('/^\\|.+\\|\\s*$/', $lines[$i])) {
                      $block[] = $lines[$i];
                      $i++;
                  }
                  $key = '__TABLE_' . count($tables) . '__';
                  $tables[$key] = $block;
                  $outLines[] = $key;
                  continue;
              }
              $outLines[] = $line;
              $i++;
          }

          $html = \Illuminate\Support\Str::markdown(implode(\"\\n\", $outLines));

          foreach ($tables as $key => $block) {
              // Parse markdown table block
              $rows = [];
              foreach ($block as $b) {
                  $cells = array_values(array_filter(array_map('trim', explode('|', trim($b, \"|\"))), fn($c) => $c !== ''));
                  $rows[] = $cells;
              }
              // Detect separator row (---)
              $header = [];
              $body = [];
              $isHeaderDone = false;
              foreach ($rows as $idx => $cells) {
                  $allDashes = count($cells) > 0 && array_reduce($cells, fn($ok, $c) => $ok && preg_match('/^:?-{2,}:?$/', $c), true);
                  if ($allDashes) { $isHeaderDone = true; continue; }
                  if (!$isHeaderDone && empty($header)) { $header = $cells; continue; }
                  $body[] = $cells;
              }

              $tableHtml = '<div class=\"my-6 overflow-x-auto\"><table class=\"min-w-full text-sm border border-white/10 rounded-xl overflow-hidden\">';
              if (!empty($header)) {
                  $tableHtml .= '<thead class=\"bg-white/5\"><tr>';
                  foreach ($header as $h) {
                      $tableHtml .= '<th class=\"px-4 py-3 text-left font-semibold text-white\">' . e($h) . '</th>';
                  }
                  $tableHtml .= '</tr></thead>';
              }
              $tableHtml .= '<tbody class=\"bg-black/20\">';
              foreach ($body as $rIdx => $cells) {
                  $tableHtml .= '<tr class=\"border-t border-white/10\">';
                  foreach ($cells as $c) {
                      $tableHtml .= '<td class=\"px-4 py-3 text-white/80\">' . e($c) . '</td>';
                  }
                  $tableHtml .= '</tr>';
              }
              $tableHtml .= '</tbody></table></div>';

              $html = str_replace($key, $tableHtml, $html);
          }

          // Strip anchors but keep their visible text (no external dependency/clickthrough).
          $html = preg_replace('~<a\\b[^>]*>(.*?)</a>~is', '$1', $html) ?? $html;
          return $html;
      };
    @endphp

    <style>
      /* Homepage-only: keep DeAI Nexus light layout, but independent of SPA */
      body { background: linear-gradient(135deg, #f6f5ff 0%, #ffffff 45%, #e1f2ff 100%); color: #0f172a; }
      .card { background: rgba(255,255,255,0.85); border: 1px solid rgba(15,23,42,0.08); border-radius: 16px; box-shadow: 0 18px 55px rgba(15,23,42,0.08); }
      .chip { background: rgba(15,23,42,0.04); border: 1px solid rgba(15,23,42,0.08); border-radius: 999px; padding: .25rem .6rem; font-size: 12px; color: rgba(15,23,42,0.75); }
      .muted { color: rgba(15,23,42,0.65); }

      /* Markdown styling */
      .content h1,.content h2,.content h3 { color: #0f172a; font-weight: 700; }
      .content p { margin-top: .75rem; color: rgba(15,23,42,0.78); line-height: 1.75; }
      .content ul,.content ol { margin-top: .75rem; padding-left: 1.25rem; color: rgba(15,23,42,0.78); }
      .content li { margin-top: .25rem; }
      .content code { background: rgba(15,23,42,0.06); padding: 0 .35rem; border-radius: .35rem; }
      .content pre { margin-top: .75rem; background: rgba(15,23,42,0.92); color: #e2e8f0; padding: .9rem; border-radius: .9rem; overflow-x: auto; }
      .content pre code { background: transparent; padding: 0; }

      [x-cloak] { display: none !important; }
    </style>
  </head>
  <body>
    @php
      // You can still switch language via ?lang=en or ?lang=zh_CN (middleware).
      $activeGroupId = 'project_introduction';
      $activeGroup = collect($groups)->firstWhere('id', $activeGroupId);
      $heroMd = (string) (($activeGroup['children'][0]['content'][$langKey] ?? '') ?: ($activeGroup['children'][0]['content']['en-US'] ?? ''));
      $heroHtml = $renderMarkdownWithTables($heroMd);
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 md:px-10" x-data="{ q: '', active: '{{ $activeGroupId }}' }">
      <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
          <img src="{{ asset('images/Logo01.png') }}" alt="Logo" class="w-12 h-12 object-contain" />
        </div>
        <div class="flex items-center gap-2">
          <a href="{{ route('login') }}" class="btn-dark normal-case text-sm">Login</a>
        </div>
      </div>

      <div class="mt-6 card p-6 md:p-8">
        <div class="flex flex-col md:flex-row md:items-start gap-6">
          <div class="flex-1 content">
            {!! $heroHtml !!}
          </div>
          <div class="w-full md:w-[340px]">
            <div class="card p-4">
              <div class="text-sm font-semibold text-slate-900">Search</div>
              <div class="mt-3">
                <input
                  type="text"
                  class="input !bg-white !text-slate-900 !ring-slate-200 placeholder:!text-slate-400"
                  x-model="q"
                  placeholder="Type keywords…"
                />
              </div>
              <div class="mt-3 text-xs muted">
                Language can be changed via <span class="chip">?lang=en</span> / <span class="chip">?lang=zh_CN</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <aside class="lg:col-span-4">
          <div class="card p-4 sticky top-6">
            <div class="text-sm font-semibold text-slate-900">Sections</div>
            <div class="mt-3 space-y-2">
              @foreach ($groups as $g)
                @php
                  $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
                @endphp
                <button
                  type="button"
                  class="w-full text-left px-3 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition"
                  :class="active === @js($g['id']) ? 'bg-slate-50' : 'bg-white'"
                  @click="active = @js($g['id'])"
                >
                  <div class="font-medium text-slate-900">{{ $gTitle }}</div>
                  <div class="text-xs muted">{{ $g['id'] }}</div>
                </button>
              @endforeach
            </div>
          </div>
        </aside>

        <main class="lg:col-span-8 space-y-4">
          @foreach ($groups as $g)
            @php
              $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
              $children = is_array($g['children'] ?? null) ? $g['children'] : [];
            @endphp
            <section class="card p-5" x-show="active === @js($g['id'])" x-cloak>
              <div class="flex items-end justify-between gap-3">
                <div>
                  <div class="text-xs muted">{{ $g['id'] }}</div>
                  <div class="text-lg font-semibold text-slate-900">{{ $gTitle }}</div>
                </div>
                <div class="text-xs muted">{{ count($children) }} items</div>
              </div>

              <div class="mt-4 space-y-3">
                @foreach ($children as $c)
                  @php
                    $cTitle = $c['title'][$langKey] ?? $c['title']['en-US'] ?? $c['id'];
                    $md = (string) (($c['content'][$langKey] ?? '') ?: ($c['content']['en-US'] ?? ''));
                    $html = $renderMarkdownWithTables($md);
                    $searchHaystack = strtolower($cTitle . ' ' . strip_tags($html));
                  @endphp

                  <div class="border border-slate-200 rounded-xl overflow-hidden" x-show="!q || @js($searchHaystack).includes(q.toLowerCase())">
                    <button
                      type="button"
                      class="w-full flex items-center justify-between gap-3 px-4 py-3 bg-white hover:bg-slate-50 transition"
                      @click="$el.nextElementSibling.classList.toggle('hidden')"
                    >
                      <div class="text-slate-900 font-medium">{{ $cTitle }}</div>
                      <div class="text-xs muted">{{ $c['id'] }}</div>
                    </button>
                    <div class="px-4 pb-4 content hidden">
                      {!! $html !!}
                    </div>
                  </div>
                @endforeach
              </div>
            </section>
          @endforeach
        </main>
      </div>
    </div>
  </body>
</html>
