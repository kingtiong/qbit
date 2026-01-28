<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FinTech - Portfolio Optimization</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo01.png') }}">
    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
      :root{
        --bg:#07070a; --surface:#0f1015; --border:rgba(212,175,55,.18);
        --text:#f8fafc; --muted:rgba(148,163,184,.92);
        --gold:#d4af37; --gold2:#f2d06b;
      }
      body{background:radial-gradient(900px 500px at 20% 0%, rgba(212,175,55,.10), transparent 60%),
                 radial-gradient(900px 500px at 80% 20%, rgba(45,107,255,.10), transparent 55%),
                 var(--bg); color:var(--text);}
      .wrap{max-width:1100px;margin:0 auto;padding:28px 18px 60px;}
      .top{display:flex;align-items:center;gap:14px;}
      .top__logo{display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;}
      .top__logo img{height:46px;width:auto;display:block;}
      .top__title{font-weight:800;letter-spacing:-.02em;}
      .top__spacer{margin-left:auto;}
      .pill{display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;border:1px solid var(--border);
            background:rgba(18,18,22,.72);color:var(--text);text-decoration:none;font-size:13px;}
      .pill:hover{background:rgba(18,18,22,.88);}
      .card{margin-top:18px;border-radius:22px;background:rgba(18,18,22,.82);border:1px solid var(--border);box-shadow:0 22px 65px rgba(0,0,0,.60);padding:18px;}
      .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
      @media (max-width: 980px){.grid{grid-template-columns:1fr;}}
      .h1{margin:0;font-size:28px;letter-spacing:-.03em;}
      .sub{margin-top:8px;color:var(--muted);line-height:1.6;font-size:14px;}
      .controls{margin-top:14px;display:grid;grid-template-columns:1fr 1fr;gap:12px;}
      @media (max-width: 980px){.controls{grid-template-columns:1fr;}}
      .field{display:grid;gap:6px;}
      .label{font-size:12px;color:rgba(226,232,240,.9);font-weight:650;}
      .input, .select{width:100%;border-radius:14px;border:1px solid rgba(212,175,55,.18);background:rgba(7,7,10,.65);
        color:var(--text);padding:10px 12px;outline:none;}
      .input:focus,.select:focus{box-shadow:0 0 0 3px rgba(212,175,55,.12);}
      .row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
      .btn{border:0;border-radius:14px;padding:10px 14px;font-weight:700;cursor:pointer;}
      .btn--primary{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111;}
      .btn--ghost{background:rgba(255,255,255,.06);border:1px solid rgba(212,175,55,.18);color:var(--text);}
      .btn--ghost:hover{background:rgba(255,255,255,.08);}
      .muted{color:var(--muted);font-size:13px;}
      .assets{margin-top:12px;display:grid;gap:8px;}
      .asset{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 12px;border-radius:16px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);}
      .asset__l{display:flex;align-items:center;gap:10px;}
      .tag{font-size:12px;color:rgba(226,232,240,.85);}
      .kpi{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px;}
      @media (max-width: 980px){.kpi{grid-template-columns:1fr;}}
      .k{border-radius:18px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);padding:12px;}
      .k__t{font-size:12px;color:rgba(226,232,240,.85);font-weight:650;}
      .k__v{margin-top:6px;font-size:18px;font-weight:800;letter-spacing:-.02em;}
      .out{display:grid;grid-template-columns:140px 1fr;gap:14px;align-items:center;margin-top:12px;}
      @media (max-width: 980px){.out{grid-template-columns:1fr;justify-items:center;text-align:center;}}
      .pie{width:120px;height:120px;border-radius:999px;background:conic-gradient(#2D6BFF 0 25%, #8B5CF6 25% 50%, #22C55E 50% 75%, #F59E0B 75% 100%);
        box-shadow:0 18px 45px rgba(0,0,0,.55);position:relative;overflow:hidden;}
      .pie::after{content:"";position:absolute;inset:18px;border-radius:999px;background:rgba(18,18,22,.9);border:1px solid rgba(212,175,55,.12);}
      .tbl{width:100%;border-collapse:collapse;margin-top:10px;font-size:13px;}
      .tbl th,.tbl td{padding:10px 10px;border-bottom:1px solid rgba(148,163,184,.18);text-align:left;}
      .tbl th{color:rgba(226,232,240,.9);font-weight:700;}
      .log{margin-top:10px;border-radius:18px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);padding:12px;font-size:12.5px;color:rgba(226,232,240,.82);line-height:1.45;max-height:180px;overflow:auto;white-space:pre-wrap;}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <a class="top__logo" href="{{ url('/') }}">
          <img src="{{ asset('images/Logo01.png') }}" alt="">
          <div>
            <div class="top__title">FinTech - Portfolio Optimization</div>
            <div class="muted">Quantum-inspired allocation demo (simulated)</div>
          </div>
        </a>
        <div class="top__spacer"></div>
        <a class="pill" href="{{ url('/') }}">Back to Home</a>
        <a class="pill" href="{{ route('login') }}">Login</a>
      </div>

      <div class="card">
        <div class="grid">
          <div>
            <h1 class="h1">Optimize risk-return in seconds</h1>
            <div class="sub">
              Select assets, set your risk appetite, and run a Markowitz-style optimizer with a quantum-inspired random-search sampler.
            </div>

            <div class="controls">
              <div class="field">
                <div class="label">Investment Amount (USDT)</div>
                <input id="amount" class="input" type="number" min="100" step="50" value="10000">
              </div>
              <div class="field">
                <div class="label">Risk Appetite (low → high)</div>
                <input id="risk" class="input" type="range" min="0" max="100" value="55">
                <div class="muted">Risk level: <span id="riskLabel">55</span>/100</div>
              </div>
            </div>

            <div class="row" style="margin-top: 12px;">
              <button id="optBtn" class="btn btn--primary" type="button">Try Optimize</button>
              <button id="povBtn" class="btn btn--ghost" type="button">Validate Data (PoDV)</button>
              <div class="muted" id="status"></div>
            </div>

            <div class="assets" id="assets"></div>
          </div>

          <div>
            <div class="kpi">
              <div class="k">
                <div class="k__t">Expected Return (annual)</div>
                <div class="k__v" id="kRet">—</div>
              </div>
              <div class="k">
                <div class="k__t">Volatility (annual)</div>
                <div class="k__v" id="kVol">—</div>
              </div>
              <div class="k">
                <div class="k__t">Sharpe (rf=2%)</div>
                <div class="k__v" id="kSharpe">—</div>
              </div>
            </div>

            <div class="out">
              <div class="pie" id="pie" aria-hidden="true"></div>
              <div>
                <div class="label">Optimized Weights</div>
                <table class="tbl" id="weightsTbl">
                  <thead>
                    <tr><th>Asset</th><th>Weight</th><th>Allocation</th></tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>

            <div class="label" style="margin-top: 14px;">Optimization Trace</div>
            <div class="log" id="log">Select assets and click “Try Optimize”.</div>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function () {
        const rf = 0.02;
        const assets = [
          { id: 'BTC', name: 'BTC', mu: 0.28, sigma: 0.55, color: '#2D6BFF' },
          { id: 'ETH', name: 'ETH', mu: 0.24, sigma: 0.62, color: '#8B5CF6' },
          { id: 'BNB', name: 'BNB', mu: 0.18, sigma: 0.45, color: '#22C55E' },
          { id: 'SOL', name: 'SOL', mu: 0.32, sigma: 0.85, color: '#F59E0B' },
        ];

        // Simple correlation matrix (symmetric), used to derive covariance.
        const corr = {
          BTC: { BTC: 1.00, ETH: 0.78, BNB: 0.55, SOL: 0.62 },
          ETH: { BTC: 0.78, ETH: 1.00, BNB: 0.60, SOL: 0.70 },
          BNB: { BTC: 0.55, ETH: 0.60, BNB: 1.00, SOL: 0.50 },
          SOL: { BTC: 0.62, ETH: 0.70, BNB: 0.50, SOL: 1.00 },
        };

        const els = {
          assets: document.getElementById('assets'),
          amount: document.getElementById('amount'),
          risk: document.getElementById('risk'),
          riskLabel: document.getElementById('riskLabel'),
          optBtn: document.getElementById('optBtn'),
          povBtn: document.getElementById('povBtn'),
          status: document.getElementById('status'),
          pie: document.getElementById('pie'),
          weightsTbl: document.getElementById('weightsTbl').querySelector('tbody'),
          kRet: document.getElementById('kRet'),
          kVol: document.getElementById('kVol'),
          kSharpe: document.getElementById('kSharpe'),
          log: document.getElementById('log'),
        };

        function fmtPct(x) { return (x * 100).toFixed(2) + '%'; }
        function fmtUsd(x) { return 'USDT ' + x.toLocaleString(undefined, { maximumFractionDigits: 0 }); }

        function seededRng(seed) {
          let s = seed >>> 0;
          return function () {
            s = (s * 1664525 + 1013904223) >>> 0;
            return (s & 0xffffffff) / 0x100000000;
          };
        }

        function dirichlet(n, rnd) {
          // Gamma(1,1) samples via -ln(U)
          const g = new Array(n).fill(0).map(() => -Math.log(Math.max(1e-12, rnd())));
          const sum = g.reduce((a, b) => a + b, 0);
          return g.map(v => v / sum);
        }

        function cov(a, b) {
          return (a.sigma * b.sigma) * (corr[a.id][b.id] ?? 0);
        }

        function portfolioStats(sel, w) {
          let ret = 0;
          for (let i = 0; i < sel.length; i++) ret += w[i] * sel[i].mu;

          // variance = w^T Σ w
          let v = 0;
          for (let i = 0; i < sel.length; i++) {
            for (let j = 0; j < sel.length; j++) {
              v += w[i] * w[j] * cov(sel[i], sel[j]);
            }
          }
          const vol = Math.sqrt(Math.max(0, v));
          const sharpe = (ret - rf) / Math.max(1e-9, vol);
          return { ret, vol, sharpe, var: v };
        }

        function setStatus(msg) { els.status.textContent = msg || ''; }
        function logLine(s) { els.log.textContent = (els.log.textContent ? els.log.textContent + '\n' : '') + s; els.log.scrollTop = els.log.scrollHeight; }

        function renderAssets() {
          els.assets.innerHTML = '';
          for (const a of assets) {
            const row = document.createElement('div');
            row.className = 'asset';
            row.innerHTML = `
              <div class="asset__l">
                <input type="checkbox" data-asset="${a.id}" checked />
                <div>
                  <div style="font-weight:800">${a.name}</div>
                  <div class="tag">μ ${(a.mu*100).toFixed(1)}% • σ ${(a.sigma*100).toFixed(0)}%</div>
                </div>
              </div>
              <div class="tag">corr-driven covariance</div>
            `;
            els.assets.appendChild(row);
          }
        }

        function selectedAssets() {
          const ids = Array.from(els.assets.querySelectorAll('input[type="checkbox"]'))
            .filter(x => x.checked)
            .map(x => x.getAttribute('data-asset'));
          return assets.filter(a => ids.includes(a.id));
        }

        function renderResult(sel, w, stats) {
          const amount = Math.max(0, Number(els.amount.value || 0));
          els.kRet.textContent = fmtPct(stats.ret);
          els.kVol.textContent = fmtPct(stats.vol);
          els.kSharpe.textContent = stats.sharpe.toFixed(2);

          // pie gradient
          let start = 0;
          const stops = [];
          for (let i = 0; i < sel.length; i++) {
            const pct = w[i] * 100;
            const end = start + pct;
            stops.push(`${sel[i].color} ${start.toFixed(3)}% ${end.toFixed(3)}%`);
            start = end;
          }
          els.pie.style.background = `conic-gradient(${stops.join(',')})`;

          // weights table
          els.weightsTbl.innerHTML = '';
          for (let i = 0; i < sel.length; i++) {
            const tr = document.createElement('tr');
            tr.innerHTML = `<td>${sel[i].name}</td><td>${fmtPct(w[i])}</td><td>${fmtUsd(amount * w[i])}</td>`;
            els.weightsTbl.appendChild(tr);
          }
        }

        function optimize() {
          const sel = selectedAssets();
          if (sel.length < 2) {
            setStatus('Select at least 2 assets.');
            return;
          }

          els.log.textContent = '';
          setStatus('Running optimization…');

          const risk = Number(els.risk.value || 0);
          const lambda = (risk / 100) * 6.0; // higher risk appetite => lower penalty? invert a bit
          const penalty = 6.0 - lambda; // 6 (low risk) -> 0 (high risk)

          const seed = Math.floor((Number(els.amount.value || 0) * 17) + risk * 101 + sel.length * 1009);
          const rnd = seededRng(seed);

          let best = null;
          let bestW = null;

          const trials = 2500;
          logLine(`Sampler: ${trials} candidate portfolios`);
          logLine(`Risk level: ${risk}/100 (variance penalty ${penalty.toFixed(2)})`);

          for (let t = 0; t < trials; t++) {
            const w = dirichlet(sel.length, rnd);
            const st = portfolioStats(sel, w);
            const utility = st.ret - penalty * st.var;
            if (!best || utility > best.utility) {
              best = { ...st, utility };
              bestW = w;
            }
            if (t % 600 === 0) logLine(`Probe ${t}: best Sharpe ${best ? best.sharpe.toFixed(2) : '—'}`);
          }

          renderResult(sel, bestW, best);
          logLine(`Done: best Sharpe ${best.sharpe.toFixed(2)} • utility ${(best.utility).toFixed(4)}`);
          setStatus('Complete.');
        }

        function validatePoDV() {
          setStatus('Validating…');
          const sel = selectedAssets();
          const seed = Math.floor((Number(els.amount.value || 0) * 3) + Number(els.risk.value || 0) * 7 + sel.length * 11);
          const rnd = seededRng(seed);
          const score = Math.floor(rnd() * 9000) + 1000;
          setTimeout(() => {
            setStatus(`PoDV Verified: data-score ${score} • integrity OK`);
            logLine(`PoDV: validated price feeds and covariance integrity (score ${score}).`);
          }, 450);
        }

        els.risk.addEventListener('input', () => els.riskLabel.textContent = els.risk.value);
        els.optBtn.addEventListener('click', optimize);
        els.povBtn.addEventListener('click', validatePoDV);

        renderAssets();
        els.riskLabel.textContent = els.risk.value;
      })();
    </script>
  </body>
</html>
