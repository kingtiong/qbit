<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>(QBP) Quantum Business Partner Program</title>

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

    {{-- Mirror deainexus.space styling locally --}}
    <link rel="stylesheet" crossorigin href="{{ asset('deainexus/assets/index-B2bo1EsD.css') }}">
  </head>
  <body class="bg-slate-50">
    <main class="min-h-screen w-full bg-gradient-to-br from-[#f6f5ff] via-white to-[#e1f2ff] px-4 py-8 md:px-10">
      <div class="mx-auto max-w-6xl relative">
        <div class="absolute inset-0 -z-10 opacity-60 blur-3xl pointer-events-none bg-gradient-to-r from-violet-200 via-white to-sky-100"></div>

        <header class="flex items-start justify-between gap-4 flex-wrap">
          <div class="flex items-center gap-4">
            <img
              src="{{ asset('deainexus/assets/logo192-CpuBC2F4.png') }}"
              alt="Logo"
              class="w-14 h-14 rounded-2xl shadow-lg shadow-slate-300/40 bg-white/90 p-2"
            />
            <div class="min-w-0">
              <div class="text-slate-900 font-semibold leading-tight text-lg">(QBP)</div>
              <div class="text-slate-600 text-sm leading-tight">Quantum Business Partner Program</div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <a
              href="{{ route('login') }}"
              class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
            >
              Login
            </a>
            <a
              href="{{ route('register') }}"
              class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
            >
              Register
            </a>
          </div>
        </header>

        <section class="mt-8 rounded-xl border border-slate-200 bg-white shadow-sm">
          <div class="p-6 md:p-8">
            <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">(QBP) Quantum Business Partner Program</h1>
            <p class="mt-2 text-slate-600 text-sm md:text-base max-w-3xl">
              A hierarchical 25-tier node network designed for long-term participation and structured allocation. Founder Team sits above the tier network as the governance layer.
            </p>

            <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-4">
              <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="text-slate-900 font-semibold">Founder Team</div>
                <div class="mt-1 text-slate-600 text-sm">
                  Governance layer with special participation conditions.
                </div>
              </div>
              <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="text-slate-900 font-semibold">25-Tier Network</div>
                <div class="mt-1 text-slate-600 text-sm">
                  Tier 1 (Entry) through Tier 25 (Elite).
                </div>
              </div>
              <div class="rounded-xl border border-slate-200 bg-white shadow-sm p-5">
                <div class="text-slate-900 font-semibold">Member Access</div>
                <div class="mt-1 text-slate-600 text-sm">
                  Sign in to view tiers, pricing, and your QBP activity.
                </div>
              </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
              <a
                href="{{ route('login') }}"
                class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
              >
                Go to Login
              </a>
              <a
                href="{{ route('register') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-900 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2"
              >
                Request Invite / Register
              </a>
            </div>

            <div class="mt-4 text-xs text-slate-500">
              Registration is invitation-only.
            </div>
          </div>
        </section>
      </div>
    </main>
  </body>
</html>
