<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portfolio Optimization Analysis</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo01.png') }}">
    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
      :root{
        --bg:#07070a;
        --surface:rgba(18,18,22,.82);
        --surface2:rgba(7,7,10,.55);
        --border:rgba(212,175,55,.18);
        --text:#f8fafc;
        --muted:rgba(148,163,184,.92);
        --gold:#d4af37;
        --gold2:#f2d06b;
        --blue:#2D6BFF;
        --violet:#8B5CF6;
        --green:#22C55E;
        --amber:#F59E0B;
        --red:#ef4444;
      }
      *{box-sizing:border-box}
      body{
        margin:0;
        background:
          radial-gradient(1200px 600px at 20% 0%, rgba(212,175,55,.10), transparent 60%),
          radial-gradient(1200px 700px at 85% 10%, rgba(45,107,255,.10), transparent 55%),
          radial-gradient(900px 500px at 55% 70%, rgba(139,92,246,.08), transparent 60%),
          var(--bg);
        color:var(--text);
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial;
        overflow:hidden;
      }

      .gridbg{
        position:fixed;inset:0;pointer-events:none;opacity:.23;
        background-image:
          linear-gradient(to right, rgba(255,255,255,.05) 1px, transparent 1px),
          linear-gradient(to bottom, rgba(255,255,255,.05) 1px, transparent 1px);
        background-size: 34px 34px;
        mask-image: radial-gradient(50% 40% at 50% 30%, black 30%, transparent 70%);
      }
      .scan{
        position:fixed;inset:-40% 0 0 0;pointer-events:none;
        background: linear-gradient(180deg, transparent, rgba(45,107,255,.06), transparent);
        animation: scan 3.2s linear infinite;
        mix-blend-mode: screen;
      }
      @keyframes scan { 0% { transform: translateY(-40%);} 100% { transform: translateY(140%);} }

      .lock{
        position:fixed;inset:0;z-index:60;
        background: transparent;
        display:none;
      }
      .lock.show{display:block;}

      .phase{
        position:fixed;inset:0;z-index:50;
        display:grid;
        place-items:center;
        opacity:0;
        transform: translateY(8px);
        transition: opacity .35s ease, transform .35s ease;
        pointer-events:none;
      }
      .phase.active{opacity:1;transform:none;pointer-events:auto;}
      .inner{
        width:min(1180px, 94vw);
      }
      .frame{
        border-radius:24px;
        background:var(--surface);
        border:1px solid var(--border);
        box-shadow:0 22px 65px rgba(0,0,0,.60);
        overflow:hidden;
        position:relative;
      }
      .frame::before{
        content:"";
        position:absolute;inset:0;
        background:
          radial-gradient(420px 240px at 14% 10%, rgba(212,175,55,.18), transparent 60%),
          radial-gradient(420px 240px at 85% 35%, rgba(45,107,255,.16), transparent 62%);
        opacity:.8;
        pointer-events:none;
      }
      .pad{position:relative;padding:18px;}
      .title{font-weight:950;letter-spacing:-.02em;font-size:20px;margin:0;}
      .sub{margin-top:6px;color:var(--muted);font-size:13.5px;line-height:1.55;}

      .codegrid{margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;}
      @media (max-width: 980px){.codegrid{grid-template-columns:1fr;}}
      .code{
        border-radius:18px;border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        padding:12px;
        min-height:240px;
        position:relative;
        overflow:hidden;
      }
      .code::after{
        content:"";
        position:absolute;inset:0;
        background:linear-gradient(180deg, rgba(255,255,255,.03), transparent);
        pointer-events:none;
      }
      pre{margin:0;font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-size:12px;line-height:1.35;color:rgba(226,232,240,.86);white-space:pre-wrap;}

      .stagebar{
        margin-top:12px;
        display:flex;align-items:center;gap:10px;flex-wrap:wrap;
      }
      .pill{
        display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:999px;
        border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        font-size:12px;color:rgba(226,232,240,.86);
      }
      .dot{width:10px;height:10px;border-radius:999px;background:rgba(226,232,240,.25);}
      .pill.active .dot{background:var(--blue);box-shadow:0 0 0 8px rgba(45,107,255,.12);}
      .pill.done .dot{background:var(--green);box-shadow:0 0 0 8px rgba(34,197,94,.10);}
      .progress{flex:1;min-width:220px;height:10px;border-radius:999px;background:rgba(255,255,255,.07);border:1px solid rgba(212,175,55,.12);overflow:hidden;}
      .progress > div{height:100%;width:0%;background:linear-gradient(90deg,var(--blue),var(--violet),var(--gold));border-radius:999px;transition:width .25s ease;}

      .canWrap{margin-top:14px;}
      .can{
        width:100%;
        height:min(70vh, 520px);
        border-radius:18px;
        border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        padding:10px;
      }
      canvas{width:100%;height:100%;}

      .topbar{
        display:flex;align-items:center;gap:12px;flex-wrap:wrap;
      }
      .brand{
        display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;
      }
      .brand img{height:40px;width:auto;display:block;}
      .brand__t{font-weight:950;letter-spacing:-.02em;}
      .brand__s{margin-top:2px;font-size:12.5px;color:var(--muted);}
      .spacer{margin-left:auto;}
      a.btnlink{
        display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;
        border:1px solid var(--border);background:rgba(18,18,22,.72);
        color:var(--text);text-decoration:none;font-size:13px;
      }
      a.btnlink[aria-disabled="true"]{opacity:.45;pointer-events:none;}

      .kpi{margin-top:12px;display:grid;grid-template-columns:repeat(4, minmax(160px, 1fr));gap:10px;width:100%;}
      @media (max-width: 980px){.kpi{grid-template-columns:1fr 1fr;}}
      @media (max-width: 520px){.kpi{grid-template-columns:1fr;}}
      .k{
        border-radius:18px;border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        padding:12px;
      }
      .k__t{font-size:12px;color:rgba(226,232,240,.86);font-weight:750;}
      .k__v{margin-top:6px;font-size:18px;font-weight:950;letter-spacing:-.02em;}

      .result{
        margin-top:14px;border-radius:18px;border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);padding:14px;
      }
      .tbl{width:100%;border-collapse:collapse;margin-top:10px;font-size:13px;}
      .tbl th,.tbl td{padding:10px 10px;border-bottom:1px solid rgba(148,163,184,.16);text-align:left;}
      .tbl th{color:rgba(226,232,240,.9);font-weight:850;}
      .bul{margin:10px 0 0;padding-left:18px;color:rgba(226,232,240,.85);line-height:1.55;}
      .bul li{margin:6px 0;}

      .actions{margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;}
      button.btn{
        border:0;border-radius:12px;padding:9px 12px;font-weight:900;cursor:pointer;
        background:rgba(255,255,255,.06);border:1px solid rgba(212,175,55,.18);color:var(--text);
      }
      button.btn:hover{background:rgba(255,255,255,.08);}
      button.btn--primary{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111;border:0;}
      button.btn--primary:hover{filter:brightness(1.03);}
      button.btn[disabled]{opacity:.5;cursor:not-allowed;}
    </style>
  </head>
  <body>
    <div class="gridbg" aria-hidden="true"></div>
    <div class="scan" aria-hidden="true"></div>
    <div class="lock" id="lock" aria-hidden="true"></div>

    <!-- Phase 1: Quantum Compute Stream (3s) -->
    <div class="phase active" id="phaseStream" aria-hidden="false">
      <div class="inner">
        <div class="frame">
          <div class="pad">
            <div class="title">Quantum Compute Stream</div>
            <div class="sub">Pseudo-calculations, vectors, and matrices (simulated). Running…</div>

            <div class="stagebar">
              <div class="pill active" id="p1"><span class="dot"></span><span>1. Stream</span></div>
              <div class="pill" id="p2"><span class="dot"></span><span>2. Allocation</span></div>
              <div class="pill" id="p3"><span class="dot"></span><span>3. Return</span></div>
              <div class="pill" id="p4"><span class="dot"></span><span>4. Trend</span></div>
              <div class="pill" id="p5"><span class="dot"></span><span>5. Result</span></div>
              <div class="progress" aria-hidden="true"><div id="prog"></div></div>
              <div style="font-size:12px;color:var(--muted);">Time <strong id="timeLeft">3.0</strong>s</div>
            </div>

            <div class="codegrid">
              <div class="code"><pre id="codeA"></pre></div>
              <div class="code"><pre id="codeB"></pre></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phase 2: Pie (2s) -->
    <div class="phase" id="phasePie" aria-hidden="true">
      <div class="inner">
        <div class="frame">
          <div class="pad">
            <div class="title">Portfolio Allocation</div>
            <div class="sub">Quantum-weighted allocation snapshot.</div>
            <div class="canWrap">
              <div class="can"><canvas id="cAlloc" width="1200" height="700"></canvas></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phase 3: Return Graph (2s) -->
    <div class="phase" id="phaseReturn" aria-hidden="true">
      <div class="inner">
        <div class="frame">
          <div class="pad">
            <div class="title">Return Graph</div>
            <div class="sub">Risk vs return frontier view.</div>
            <div class="canWrap">
              <div class="can"><canvas id="cRiskReturn" width="1200" height="700"></canvas></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phase 4: Trend Line (2s) -->
    <div class="phase" id="phaseTrend" aria-hidden="true">
      <div class="inner">
        <div class="frame">
          <div class="pad">
            <div class="title">Trend Line Chart</div>
            <div class="sub">Simulated performance trend lines.</div>
            <div class="canWrap">
              <div class="can"><canvas id="cTrend" width="1200" height="700"></canvas></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Phase 5: Final Metrics + Results (persistent) -->
    <div class="phase" id="phaseFinal" aria-hidden="true">
      <div class="inner">
        <div class="frame">
          <div class="pad">
            <div class="topbar">
              <a class="brand" href="{{ route('fintech.portfolio') }}" id="brandBack" aria-disabled="true">
                <img src="{{ asset('images/Logo01.png') }}" alt="">
                <div>
                  <div class="brand__t">Portfolio Optimization Analysis</div>
                  <div class="brand__s">Calm summary after convergence</div>
                </div>
              </a>
              <div class="spacer"></div>
              <a class="btnlink" id="backBtn" href="{{ route('fintech.portfolio') }}" aria-disabled="true">Back to Portfolio</a>
            </div>

            <div class="kpi">
              <div class="k"><div class="k__t">Expected Return</div><div class="k__v" id="mRet">—</div></div>
              <div class="k"><div class="k__t">Risk Level</div><div class="k__v" id="mRiskFinal">—</div></div>
              <div class="k"><div class="k__t">Volatility</div><div class="k__v" id="mVolFinal">—</div></div>
              <div class="k"><div class="k__t">Diversification</div><div class="k__v" id="mDivFinal">—</div></div>
            </div>

            <div class="result" id="result">
              <div style="font-weight:950;letter-spacing:-.01em;">Optimized portfolio allocation</div>
              <table class="tbl" id="allocTbl">
                <thead><tr><th>Stock</th><th>Allocation</th></tr></thead>
                <tbody></tbody>
              </table>

              <div style="margin-top:10px;font-weight:950;">Key insights</div>
              <ul class="bul" id="insights"></ul>
            </div>

            <div class="actions">
              <button id="replayBtn" class="btn btn--primary" type="button" disabled>Replay Analysis</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function () {
        const lock = document.getElementById('lock');
        const phases = {
          stream: document.getElementById('phaseStream'),
          pie: document.getElementById('phasePie'),
          ret: document.getElementById('phaseReturn'),
          trend: document.getElementById('phaseTrend'),
          final: document.getElementById('phaseFinal'),
        };
        const pills = [document.getElementById('p1'), document.getElementById('p2'), document.getElementById('p3'), document.getElementById('p4'), document.getElementById('p5')];
        const prog = document.getElementById('prog');
        const timeLeft = document.getElementById('timeLeft');
        const codeA = document.getElementById('codeA');
        const codeB = document.getElementById('codeB');
        const backBtn = document.getElementById('backBtn');
        const brandBack = document.getElementById('brandBack');
        const replayBtn = document.getElementById('replayBtn');

        const cAlloc = document.getElementById('cAlloc');
        const cRR = document.getElementById('cRiskReturn');
        const cTrend = document.getElementById('cTrend');

        const mRet = document.getElementById('mRet');
        const mRiskFinal = document.getElementById('mRiskFinal');
        const mVolFinal = document.getElementById('mVolFinal');
        const mDivFinal = document.getElementById('mDivFinal');
        const allocTbl = document.getElementById('allocTbl').querySelector('tbody');
        const insights = document.getElementById('insights');

        function safeJsonParse(s) { try { return JSON.parse(s); } catch (e) { return null; } }
        const stored = safeJsonParse(localStorage.getItem('qbit_portfolio') || '') || { symbols: ['AAPL','MSFT','AMZN'], risk: 55 };
        const symbols = (stored.symbols || []).slice(0, 8);
        const baseRisk = Math.max(0, Math.min(100, Number(stored.risk || 0)));

        function hash32(str) {
          let h = 2166136261 >>> 0;
          for (let i = 0; i < str.length; i++) { h ^= str.charCodeAt(i); h = Math.imul(h, 16777619) >>> 0; }
          return h >>> 0;
        }
        function seeded(seed) {
          let s = seed >>> 0;
          return () => { s = (Math.imul(s, 1664525) + 1013904223) >>> 0; return (s & 0xffffffff) / 0x100000000; };
        }
        const seed = hash32(symbols.join('|') + '|' + baseRisk);
        const rnd = seeded(seed);

        function fmtPct(x) { return (x * 100).toFixed(2) + '%'; }

        let timers = [];
        let running = false;
        let metricsTimer = null;

        function clearAll() {
          for (const t of timers) clearTimeout(t);
          timers = [];
          if (metricsTimer) { clearInterval(metricsTimer); metricsTimer = null; }
        }

        function setLocked(on) {
          lock.classList.toggle('show', on);
          replayBtn.disabled = on;
          backBtn.setAttribute('aria-disabled', on ? 'true' : 'false');
          brandBack.setAttribute('aria-disabled', on ? 'true' : 'false');
          if (on) window.onbeforeunload = () => true;
          else window.onbeforeunload = null;
        }

        function showPhase(key) {
          for (const k of Object.keys(phases)) {
            phases[k].classList.toggle('active', k === key);
            phases[k].setAttribute('aria-hidden', k === key ? 'false' : 'true');
          }
        }

        function setPills(activeIdx) {
          for (let i = 0; i < pills.length; i++) {
            pills[i].classList.toggle('active', i === activeIdx);
            pills[i].classList.toggle('done', i < activeIdx);
          }
        }

        function randSym() {
          const chars = '01×+-≈∑∫√λμσρΩΨΦΔΓπ→←⊗⊕';
          return chars[Math.floor(rnd() * chars.length)];
        }
        function makeMatrix(n) {
          const lines = [];
          for (let i = 0; i < n; i++) {
            const row = [];
            for (let j = 0; j < n; j++) {
              const v = (rnd() * 2 - 1);
              row.push((v >= 0 ? '+' : '') + v.toFixed(3));
            }
            lines.push('[' + row.join('  ') + ']');
          }
          return lines.join('\n');
        }
        function makeVector(n) {
          const v = [];
          for (let i = 0; i < n; i++) v.push((rnd() * 1).toFixed(4));
          return 'w = <' + v.join(', ') + '>';
        }
        function makeCodeBlock(head) {
          const n = Math.min(6, Math.max(3, symbols.length));
          const a = [];
          a.push(`// QBIT Quantum Analysis :: ${head}`);
          a.push(`symbols = [${symbols.join(', ')}]`);
          a.push(`ψ(t) ~ ${randSym()}${randSym()}${randSym()}  |  Δ = ${(rnd()*0.12).toFixed(4)}`);
          a.push('');
          a.push(`Σ = cov(${n}x${n})`);
          a.push(makeMatrix(n));
          a.push('');
          a.push(makeVector(n));
          a.push(`α = ${(rnd()*0.18+0.02).toFixed(4)}  ρ̄ = ${(rnd()*0.7+0.2).toFixed(3)}`);
          return a.join('\n');
        }

        function weights() {
          const n = Math.max(2, Math.min(8, symbols.length || 0));
          const g = [];
          for (let i = 0; i < n; i++) g.push(-Math.log(Math.max(1e-6, rnd())));
          const sum = g.reduce((a,b)=>a+b,0);
          const w = g.map(x => x/sum);
          return symbols.slice(0, n).map((s, i) => ({ s, w: w[i] }));
        }

        function drawGrid(ctx, w, h) {
          ctx.clearRect(0,0,w,h);
          ctx.save();
          ctx.globalAlpha = 0.9;
          ctx.strokeStyle = 'rgba(148,163,184,0.18)';
          ctx.lineWidth = 1;
          for (let x = 0; x <= w; x += 70) { ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,h); ctx.stroke(); }
          for (let y = 0; y <= h; y += 70) { ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(w,y); ctx.stroke(); }
          ctx.restore();
        }

        function drawAlloc(alloc) {
          const ctx = cAlloc.getContext('2d');
          const w = cAlloc.width, h = cAlloc.height;
          drawGrid(ctx,w,h);
          const cx = w*0.30, cy = h*0.52, r = Math.min(w,h)*0.27;
          const colors = ['#2D6BFF','#8B5CF6','#22C55E','#F59E0B','#d4af37','#38bdf8','#a3e635','#fb7185'];
          let start = -Math.PI/2;
          for (let i=0;i<alloc.length;i++){
            const ang = alloc[i].w * Math.PI*2;
            ctx.beginPath(); ctx.moveTo(cx,cy); ctx.arc(cx,cy,r,start,start+ang); ctx.closePath();
            ctx.fillStyle = colors[i%colors.length];
            ctx.globalAlpha = 0.88;
            ctx.fill();
            start += ang;
          }
          ctx.globalAlpha = 1;
          ctx.beginPath(); ctx.arc(cx,cy,r*0.62,0,Math.PI*2);
          ctx.fillStyle = 'rgba(18,18,22,.92)'; ctx.fill();
          ctx.strokeStyle = 'rgba(212,175,55,.22)'; ctx.lineWidth = 2; ctx.stroke();

          const lx = w*0.56, ly = h*0.26;
          ctx.font = '900 14px ui-sans-serif, system-ui';
          ctx.fillStyle = 'rgba(226,232,240,.92)';
          ctx.fillText('Weights', lx, ly - 18);
          ctx.font = '700 13px ui-sans-serif, system-ui';
          for (let i=0;i<alloc.length;i++){
            const y = ly + i*26;
            ctx.fillStyle = colors[i%colors.length];
            ctx.beginPath(); ctx.arc(lx, y, 6, 0, Math.PI*2); ctx.fill();
            ctx.fillStyle = 'rgba(226,232,240,.92)';
            ctx.fillText(`${alloc[i].s}  ${(alloc[i].w*100).toFixed(1)}%`, lx + 16, y + 4);
          }
        }

        function drawRiskReturn() {
          const ctx = cRR.getContext('2d');
          const w = cRR.width, h = cRR.height;
          drawGrid(ctx,w,h);
          const pad = 62;
          ctx.strokeStyle = 'rgba(226,232,240,.28)';
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(pad,h-pad); ctx.lineTo(w-pad,h-pad); ctx.lineTo(w-pad,pad); ctx.stroke();

          ctx.beginPath();
          for (let i=0;i<=80;i++){
            const x = i/80;
            const rx = pad + x*(w-2*pad);
            const ry = h-pad - (Math.pow(x,0.7)*(h-2*pad));
            if (i===0) ctx.moveTo(rx,ry); else ctx.lineTo(rx,ry);
          }
          ctx.strokeStyle = 'rgba(45,107,255,.75)';
          ctx.lineWidth = 2;
          ctx.shadowColor = 'rgba(45,107,255,.35)';
          ctx.shadowBlur = 12;
          ctx.stroke();
          ctx.shadowBlur = 0;

          const x = Math.max(0, Math.min(1, baseRisk/100));
          const y = 0.25 + (1 - x) * 0.38;
          const px = pad + x*(w-2*pad);
          const py = h-pad - y*(h-2*pad);
          ctx.beginPath(); ctx.arc(px,py,8,0,Math.PI*2);
          ctx.fillStyle = 'rgba(212,175,55,.92)';
          ctx.shadowColor = 'rgba(212,175,55,.35)';
          ctx.shadowBlur = 18;
          ctx.fill();
          ctx.shadowBlur = 0;
        }

        function drawTrend(alloc) {
          const ctx = cTrend.getContext('2d');
          const w = cTrend.width, h = cTrend.height;
          drawGrid(ctx,w,h);
          const pad = 44;
          ctx.strokeStyle = 'rgba(226,232,240,.18)';
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(pad,h-pad); ctx.lineTo(w-pad,h-pad); ctx.stroke();

          const colors = ['rgba(45,107,255,.85)','rgba(139,92,246,.75)','rgba(34,197,94,.75)','rgba(245,158,11,.75)','rgba(212,175,55,.75)'];
          const series = alloc.slice(0, Math.min(4, alloc.length));
          const t0 = (seed % 1000) / 100;
          for (let s=0;s<series.length;s++){
            const amp = 0.12 + series[s].w*0.22;
            ctx.beginPath();
            for (let i=0;i<90;i++){
              const x = i/89;
              const px = pad + x*(w-2*pad);
              const base = 0.55 + (s*0.08);
              const wob = Math.sin(t0 + x*5 + s)*0.03;
              const val = base + wob + (x-0.5)*amp;
              const py = h-pad - (val*(h-2*pad))*0.55;
              if (i===0) ctx.moveTo(px,py); else ctx.lineTo(px,py);
            }
            ctx.strokeStyle = colors[s%colors.length];
            ctx.lineWidth = 2;
            ctx.shadowColor = colors[s%colors.length];
            ctx.shadowBlur = 10;
            ctx.stroke();
            ctx.shadowBlur = 0;
          }
        }

        function finalize(alloc) {
          const expReturn = 0.12 + (1 - baseRisk/100) * 0.10 + rnd()*0.03;
          const estRisk = 0.18 + (baseRisk/100) * 0.35 + rnd()*0.03;
          const div = 0.62 + (1 - alloc.reduce((m,x)=>Math.max(m,x.w),0))*0.38;
          const band = estRisk < 0.28 ? 'Low' : estRisk < 0.40 ? 'Medium' : 'High';

          mRet.textContent = fmtPct(expReturn);
          mRiskFinal.textContent = fmtPct(estRisk);
          mVolFinal.textContent = band;
          mDivFinal.textContent = (div*100).toFixed(1) + '/100';

          allocTbl.innerHTML = alloc
            .map(x => `<tr><td>${x.s}</td><td>${(x.w*100).toFixed(2)}%</td></tr>`)
            .join('');

          const top = [...alloc].sort((a,b)=>b.w-a.w)[0];
          const ins = [
            `Risk preference ${baseRisk}/100 guides covariance regularization and allocation smoothness.`,
            `Highest weight: ${top.s}. Secondary weights improve correlation resilience and diversification.`,
            `Volatility band: ${band}. Diversification score: ${(div*100).toFixed(0)}/100.`,
            `Allocation avoids concentration to reduce drawdown sensitivity in adverse regimes.`,
          ];
          insights.innerHTML = ins.map(x => `<li>${x}</li>`).join('');

          // slower, stable metric shimmer
          const base = { expReturn, estRisk, div };
          metricsTimer = setInterval(() => {
            const j = () => (rnd()*2-1);
            const er = Math.max(0, base.expReturn + j()*0.0012);
            const rk = Math.max(0, base.estRisk + j()*0.0012);
            const dv = Math.max(0, Math.min(1, base.div + j()*0.003));
            mRet.textContent = fmtPct(er);
            mRiskFinal.textContent = fmtPct(rk);
            mDivFinal.textContent = (dv*100).toFixed(1) + '/100';
          }, 420);
        }

        function runSequence() {
          if (running) return;
          running = true;
          clearAll();
          setLocked(true);
          prog.style.width = '0%';
          setPills(0);
          showPhase('stream');

          const alloc = weights();
          const stageDur = { stream: 3000, pie: 2000, ret: 2000, trend: 2000 };
          const total = stageDur.stream + stageDur.pie + stageDur.ret + stageDur.trend;
          const start = Date.now();

          // stream animation (only stream visible)
          const tick = setInterval(() => {
            const t = Date.now() - start;
            const remain = Math.max(0, stageDur.stream - t);
            timeLeft.textContent = (remain/1000).toFixed(1);
            prog.style.width = Math.min(100, (t/total)*100).toFixed(1) + '%';
            codeA.textContent = makeCodeBlock('STREAM');
            codeB.textContent = makeCodeBlock('STREAM');
          }, 80);
          timers.push(tick);

          timers.push(setTimeout(() => {
            clearInterval(tick);
            setPills(1);
            showPhase('pie');
            drawAlloc(alloc);
            prog.style.width = ((stageDur.stream)/total*100).toFixed(1) + '%';
          }, stageDur.stream));

          timers.push(setTimeout(() => {
            setPills(2);
            showPhase('ret');
            drawRiskReturn();
            prog.style.width = ((stageDur.stream + stageDur.pie)/total*100).toFixed(1) + '%';
          }, stageDur.stream + stageDur.pie));

          timers.push(setTimeout(() => {
            setPills(3);
            showPhase('trend');
            drawTrend(alloc);
            prog.style.width = ((stageDur.stream + stageDur.pie + stageDur.ret)/total*100).toFixed(1) + '%';
          }, stageDur.stream + stageDur.pie + stageDur.ret));

          timers.push(setTimeout(() => {
            setPills(4);
            showPhase('final');
            prog.style.width = '100%';
            finalize(alloc);
            setLocked(false);
            backBtn.setAttribute('aria-disabled', 'false');
            brandBack.setAttribute('aria-disabled', 'false');
            replayBtn.disabled = false;
            running = false;
          }, stageDur.stream + stageDur.pie + stageDur.ret + stageDur.trend));
        }

        replayBtn.addEventListener('click', () => {
          if (running) return;
          runSequence();
        });

        // Auto-run once per trigger
        runSequence();
      })();
    </script>
  </body>
</html>
