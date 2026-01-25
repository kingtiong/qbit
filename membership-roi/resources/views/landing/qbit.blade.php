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
      $contentPath = resource_path('content/deainexus_SD_groups.json');
      $payload = file_exists($contentPath) ? json_decode((string) file_get_contents($contentPath), true) : [];
      $groups = is_array($payload['groups'] ?? null) ? $payload['groups'] : [];

      $renderMarkdownWithTables = function (string $md): string {
          $lines = preg_split("/\r?\n/", $md);
          $tables = [];
          $outLines = [];
          $i = 0;
          while ($i < count($lines)) {
              $line = $lines[$i];
              if (preg_match('/^\|.+\|\s*$/', $line)) {
                  $block = [];
                  while ($i < count($lines) && preg_match('/^\|.+\|\s*$/', $lines[$i])) {
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

          $html = \Illuminate\Support\Str::markdown(implode("\n", $outLines));

          foreach ($tables as $key => $block) {
              $rows = [];
              foreach ($block as $b) {
                  $cells = array_values(array_filter(array_map('trim', explode('|', trim($b, "|"))), fn ($c) => $c !== ''));
                  $rows[] = $cells;
              }

              $header = [];
              $body = [];
              $isHeaderDone = false;
              foreach ($rows as $cells) {
                  $allDashes = count($cells) > 0 && array_reduce(
                      $cells,
                      fn ($ok, $c) => $ok && preg_match('/^:?-{2,}:?$/', $c),
                      true
                  );
                  if ($allDashes) { $isHeaderDone = true; continue; }
                  if (!$isHeaderDone && empty($header)) { $header = $cells; continue; }
                  $body[] = $cells;
              }

              $tableHtml = '<div class="dh-table"><table><thead><tr>';
              foreach ($header as $h) {
                  $tableHtml .= '<th>' . e($h) . '</th>';
              }
              $tableHtml .= '</tr></thead><tbody>';
              foreach ($body as $cells) {
                  $tableHtml .= '<tr>';
                  foreach ($cells as $c) {
                      $tableHtml .= '<td>' . e($c) . '</td>';
                  }
                  $tableHtml .= '</tr>';
              }
              $tableHtml .= '</tbody></table></div>';

              $html = str_replace($key, $tableHtml, $html);
          }

          // Remove links but keep visible text.
          $html = preg_replace('~<a\b[^>]*>(.*?)</a>~is', '$1', $html) ?? $html;
          return $html;
      };
    @endphp

    <link rel="icon" href="{{ $faviconPath ? asset($faviconPath) : '/favicon.ico' }}">

    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
      /* Scoped homepage styles (easy to edit) */
      body { background: linear-gradient(135deg, #f6f5ff 0%, #ffffff 45%, #e1f2ff 100%); color: #0f172a; }
      .dh-wrap { max-width: 1120px; margin: 0 auto; padding: 24px 16px 56px; }
      .dh-header { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
      .dh-logo { display: flex; align-items: center; gap: 12px; }
      .dh-logo img { width: 56px; height: 56px; object-fit: contain; background: transparent; }
      .dh-login a { display: inline-flex; align-items: center; justify-content: center; padding: 10px 14px; border-radius: 12px; background: #0f172a; color: #fff; text-decoration: none; font-size: 14px; }
      .dh-login a:hover { background: #111c33; }

      .dh-hero { margin-top: 18px; display: grid; grid-template-columns: 1fr; gap: 16px; }
      @media (min-width: 900px) { .dh-hero { grid-template-columns: 1fr 320px; align-items: start; } }

      .dh-card { background: rgba(255,255,255,0.86); border: 1px solid rgba(15,23,42,0.08); border-radius: 16px; box-shadow: 0 18px 55px rgba(15,23,42,0.08); }
      .dh-card-pad { padding: 18px; }
      @media (min-width: 900px) { .dh-card-pad { padding: 22px; } }

      .dh-search input { width: 100%; border-radius: 12px; border: 1px solid rgba(15,23,42,0.14); padding: 10px 12px; font-size: 14px; background: #fff; color: #0f172a; }
      .dh-search input:focus { outline: 2px solid rgba(59,130,246,0.35); outline-offset: 2px; }
      .dh-hint { margin-top: 10px; font-size: 12px; color: rgba(15,23,42,0.65); }

      .dh-layout { margin-top: 18px; display: grid; grid-template-columns: 1fr; gap: 16px; }
      @media (min-width: 1024px) { .dh-layout { grid-template-columns: 340px 1fr; align-items: start; } }
      .dh-sidebar { position: sticky; top: 18px; }
      .dh-list { display: grid; gap: 10px; margin-top: 12px; }
      .dh-item { width: 100%; text-align: left; padding: 10px 12px; border-radius: 14px; border: 1px solid rgba(15,23,42,0.10); background: rgba(255,255,255,0.75); cursor: pointer; }
      .dh-item:hover { background: rgba(255,255,255,0.92); }
      .dh-item-title { font-weight: 600; color: #0f172a; }
      .dh-item-sub { margin-top: 2px; font-size: 12px; color: rgba(15,23,42,0.62); }
      .dh-item[aria-current="true"] { border-color: rgba(15,23,42,0.22); background: rgba(255,255,255,0.95); }

      .dh-group-title { font-size: 18px; font-weight: 700; color: #0f172a; }
      .dh-group-sub { margin-top: 2px; font-size: 12px; color: rgba(15,23,42,0.62); }
      .dh-section { margin-top: 12px; border: 1px solid rgba(15,23,42,0.10); border-radius: 14px; overflow: hidden; background: rgba(255,255,255,0.82); }
      .dh-section-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 14px; cursor: pointer; background: rgba(255,255,255,0.65); }
      .dh-section-head:hover { background: rgba(255,255,255,0.9); }
      .dh-section-title { font-weight: 600; color: #0f172a; }
      .dh-section-id { font-size: 12px; color: rgba(15,23,42,0.62); }
      .dh-section-body { padding: 0 14px 14px; }

      /* Markdown (content is exact; presentation only) */
      .dh-content h1, .dh-content h2, .dh-content h3 { color: #0f172a; font-weight: 700; margin-top: 14px; }
      .dh-content p { margin-top: 10px; line-height: 1.75; color: rgba(15,23,42,0.82); }
      .dh-content ul, .dh-content ol { margin-top: 10px; padding-left: 18px; color: rgba(15,23,42,0.82); }
      .dh-content li { margin-top: 4px; }
      .dh-content pre { margin-top: 10px; padding: 12px; border-radius: 12px; background: rgba(15,23,42,0.92); color: #e2e8f0; overflow-x: auto; }
      .dh-content code { background: rgba(15,23,42,0.06); padding: 0 6px; border-radius: 6px; }
      .dh-content pre code { background: transparent; padding: 0; }
      .dh-content hr { margin: 14px 0; border: none; border-top: 1px solid rgba(15,23,42,0.10); }

      .dh-table { margin-top: 12px; overflow-x: auto; }
      .dh-table table { width: 100%; border-collapse: collapse; min-width: 640px; }
      .dh-table th, .dh-table td { border: 1px solid rgba(15,23,42,0.10); padding: 10px 12px; text-align: left; vertical-align: top; }
      .dh-table thead th { background: rgba(15,23,42,0.04); font-weight: 700; }

      [x-cloak] { display: none !important; }
    </style>
  </head>

  <body>
    @php
      $activeGroupId = 'project_introduction';
      $activeGroup = collect($groups)->firstWhere('id', $activeGroupId);
      $heroMd = (string) (($activeGroup['children'][0]['content'][$langKey] ?? '') ?: ($activeGroup['children'][0]['content']['en-US'] ?? ''));
      $heroHtml = $renderMarkdownWithTables($heroMd);
    @endphp

    <div class="dh-wrap" x-data="{ q: '', active: '{{ $activeGroupId }}' }">
      <div class="dh-header">
        <div class="dh-logo">
          <img src="{{ asset('images/Logo01.png') }}" alt="Logo" />
        </div>
        <div class="dh-login">
          <a href="{{ route('login') }}">Login</a>
        </div>
      </div>

      <div class="dh-hero">
        <div class="dh-card dh-card-pad dh-content">
          {!! $heroHtml !!}
        </div>
        <div class="dh-card dh-card-pad dh-search">
          <input type="text" x-model="q" placeholder="Type keywords…" />
          <div class="dh-hint">
            Language via <strong>?lang=en</strong> / <strong>?lang=zh_CN</strong>
          </div>
        </div>
      </div>

      <div class="dh-layout">
        <aside class="dh-sidebar dh-card dh-card-pad">
          <div style="font-weight:700; color:#0f172a;">Sections</div>
          <div class="dh-list">
            @foreach ($groups as $g)
              @php
                $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
              @endphp
              <button
                type="button"
                class="dh-item"
                :aria-current="active === @js($g['id'])"
                @click="active = @js($g['id'])"
              >
                <div class="dh-item-title">{{ $gTitle }}</div>
                <div class="dh-item-sub">{{ $g['id'] }}</div>
              </button>
            @endforeach
          </div>
        </aside>

        <main>
          @foreach ($groups as $g)
            @php
              $gTitle = $g['title'][$langKey] ?? $g['title']['en-US'] ?? $g['id'];
              $children = is_array($g['children'] ?? null) ? $g['children'] : [];
            @endphp
            <section class="dh-card dh-card-pad" x-show="active === @js($g['id'])" x-cloak>
              <div class="dh-group-title">{{ $gTitle }}</div>
              <div class="dh-group-sub">{{ $g['id'] }} • {{ count($children) }} items</div>

              @foreach ($children as $c)
                @php
                  $cTitle = $c['title'][$langKey] ?? $c['title']['en-US'] ?? $c['id'];
                  $md = (string) (($c['content'][$langKey] ?? '') ?: ($c['content']['en-US'] ?? ''));
                  $html = $renderMarkdownWithTables($md);
                  $searchHaystack = strtolower($cTitle . ' ' . strip_tags($html));
                @endphp
                <div class="dh-section" x-show="!q || @js($searchHaystack).includes(q.toLowerCase())">
                  <div class="dh-section-head" @click="$el.nextElementSibling.classList.toggle('hidden')">
                    <div>
                      <div class="dh-section-title">{{ $cTitle }}</div>
                      <div class="dh-section-id">{{ $c['id'] }}</div>
                    </div>
                    <div class="dh-section-id">Toggle</div>
                  </div>
                  <div class="dh-section-body dh-content hidden">
                    {!! $html !!}
                  </div>
                </div>
              @endforeach
            </section>
          @endforeach
        </main>
      </div>
    </div>
  </body>
</html>
