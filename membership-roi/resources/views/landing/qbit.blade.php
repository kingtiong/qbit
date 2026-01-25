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

    {{-- DeAI Nexus (mirrored build assets) --}}
    <script type="module" crossorigin src="{{ asset('assets/index-AfuN7V2V.js') }}"></script>
    <link rel="stylesheet" crossorigin href="{{ asset('assets/index-B2bo1EsD.css') }}">
  </head>
  <body class="bg-slate-50">
    <div id="root"></div>
  </body>
</html>
