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
        overflow-x:hidden;
      }
      .gridbg{
        position:fixed;inset:0;pointer-events:none;opacity:.25;
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

      .wrap{max-width:1280px;margin:0 auto;padding:22px 18px 64px;}
      .top{display:flex;align-items:center;gap:14px;}
      .brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;}
      .brand img{height:42px;width:auto;display:block;}
      .brand__t{font-weight:900;letter-spacing:-.02em;}
      .brand__s{margin-top:2px;font-size:12.5px;color:var(--muted);}
      .spacer{margin-left:auto;}
      .pill{
        display:inline-flex;align-items:center;gap:8px;padding:9px 12px;border-radius:999px;
        border:1px solid var(--border);background:rgba(18,18,22,.72);
        color:var(--text);text-decoration:none;font-size:13px;
      }
      .pill[aria-disabled="true"]{opacity:.45;pointer-events:none;}

      .hero{
        margin-top:14px;
        border-radius:24px;
        background:var(--surface);
        border:1px solid var(--border);
        box-shadow:0 22px 65px rgba(0,0,0,.60);
        overflow:hidden;
        position:relative;
      }
      .hero::before{
        content:"";
        position:absolute;inset:0;
        background:
          radial-gradient(420px 240px at 14% 10%, rgba(212,175,55,.18), transparent 60%),
          radial-gradient(420px 240px at 85% 35%, rgba(45,107,255,.16), transparent 62%);
        opacity:.8;
        pointer-events:none;
      }
      .hero__pad{position:relative;padding:18px;}
      .row{display:flex;gap:14px;align-items:flex-start;flex-wrap:wrap;}
      .kpi{
        display:grid;
        grid-template-columns:repeat(4, minmax(160px, 1fr));
        gap:10px;
        width:100%;
      }
      @media (max-width: 980px){.kpi{grid-template-columns:1fr 1fr;}}
      @media (max-width: 520px){.kpi{grid-template-columns:1fr;}}
      .k{
        border-radius:18px;border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        padding:12px;
      }
      .k__t{font-size:12px;color:rgba(226,232,240,.86);font-weight:750;}
      .k__v{margin-top:6px;font-size:18px;font-weight:950;letter-spacing:-.02em;}
      .k__v.flick{animation:flicker .22s linear infinite;}
      @keyframes flicker { 0%,100%{opacity:1} 50%{opacity:.68} }

      .main{
        margin-top:14px;
        display:grid;
        grid-template-columns: 1.35fr 1fr;
        gap:14px;
        align-items:start;
      }
      @media (max-width: 980px){.main{grid-template-columns:1fr;}}

      .panel{
        border-radius:22px;border:1px solid var(--border);
        background:rgba(18,18,22,.82);
        box-shadow:0 18px 55px rgba(0,0,0,.55);
        padding:14px;
        position:relative;
      }
      .panel__t{font-weight:900;letter-spacing:-.01em;}
      .panel__s{margin-top:6px;color:var(--muted);font-size:13px;line-height:1.55;}

      .stepbar{margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
      .step{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:999px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);font-size:12px;color:rgba(226,232,240,.86);}
      .dot{width:10px;height:10px;border-radius:999px;background:rgba(226,232,240,.25);}
      .step.active .dot{background:var(--blue);box-shadow:0 0 0 8px rgba(45,107,255,.12);}
      .step.done .dot{background:var(--green);box-shadow:0 0 0 8px rgba(34,197,94,.10);}
      .progress{flex:1;min-width:220px;height:10px;border-radius:999px;background:rgba(255,255,255,.07);border:1px solid rgba(212,175,55,.12);overflow:hidden;}
      .progress > div{height:100%;width:0%;background:linear-gradient(90deg,var(--blue),var(--violet),var(--gold));border-radius:999px;transition:width .25s ease;}

      .codegrid{margin-top:12px;display:grid;grid-template-columns:1fr 1fr;gap:12px;}
      @media (max-width: 980px){.codegrid{grid-template-columns:1fr;}}
      .code{
        border-radius:18px;border:1px solid rgba(212,175,55,.14);
        background:rgba(7,7,10,.55);
        padding:12px;
        min-height:180px;
        position:relative;
        overflow:hidden;
      }
      .code::after{
        content:"";
        position:absolute;inset:0;
        background:linear-gradient(180deg, rgba(255,255,255,.03), transparent);
        pointer-events:none;
      }
      .code pre{margin:0;font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;font-size:12px;line-height:1.35;color:rgba(226,232,240,.86);}
      .pulse{animation:pulse 1.2s ease-in-out infinite;}
      @keyframes pulse{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

      .canvasWrap{margin-top:12px;display:grid;grid-template-columns:1fr;gap:12px;}
      .can{width:100%;height:260px;border-radius:18px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);padding:10px;}
      canvas{width:100%;height:100%;}

      .result{display:none;margin-top:12px;border-radius:18px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);padding:14px;}
      .result.show{display:block;animation:fadeIn .35s ease;}
      @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
      .tbl{width:100%;border-collapse:collapse;margin-top:10px;font-size:13px;}
      .tbl th,.tbl td{padding:10px 10px;border-bottom:1px solid rgba(148,163,184,.16);text-align:left;}
      .tbl th{color:rgba(226,232,240,.9);font-weight:850;}
      .bul{margin:10px 0 0;padding-left:18px;color:rgba(226,232,240,.85);line-height:1.55;}
      .bul li{margin:6px 0;}

      .actions{margin-top:12px;display:flex;gap:10px;flex-wrap:wrap;}
      .btn{
        border:0;border-radius:12px;padding:9px 12px;font-weight:900;cursor:pointer;
        background:rgba(255,255,255,.06);border:1px solid rgba(212,175,55,.18);color:var(--text);
      }
      .btn:hover{background:rgba(255,255,255,.08);}
      .btn--primary{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111;border:0;}
      .btn--primary:hover{filter:brightness(1.03);}
      .btn[disabled]{opacity:.5;cursor:not-allowed;}

      .lock{
        position:fixed;inset:0;z-index:50;display:none;
        background:rgba(0,0,0,.35);
      }
      .lock.show{display:block;}
    </style>
  </head>
  <body>
    <div class="gridbg" aria-hidden="true"></div>
    <div class="scan" aria-hidden="true"></div>
    <div class="lock" id="lock" aria-hidden="true"></div>

    <div class="wrap">
      <div class="top">
        <a class="brand" href="{{ route('fintech.portfolio') }}">
          <img src="{{ asset('images/Logo01.png') }}" alt="">
          <div>
            <div class="brand__t">Portfolio Optimization Analysis</div>
            <div class="brand__s">Quantum-style simulation • staged convergence</div>
          </div>
        </a>
        <div class="spacer"></div>
        <a id="backBtn" class="pill" href="{{ route('fintech.portfolio') }}" aria-disabled="true">Back to Portfolio</a>
      </div>

      <div class="hero">
        <div class="hero__pad">
          <div class="row">
            <div style="flex:1;min-width:260px;">
              <div class="panel__t">Live Metrics</div>
              <div class="panel__s">Rapidly updating signals during analysis.</div>
            </div>
          </div>
          <div class="kpi">
            <div class="k"><div class="k__t">Risk %</div><div class="k__v flick" id="mRisk">—</div></div>
            <div class="k"><div class="k__t">Volatility</div><div class="k__v flick" id="mVol">—</div></div>
            <div class="k"><div class="k__t">Correlation</div><div class="k__v flick" id="mCorr">—</div></div>
            <div class="k"><div class="k__t">Alpha</div><div class="k__v flick" id="mAlpha">—</div></div>
          </div>

          <div class="stepbar">
            <div class="step active" id="s1"><span class="dot"></span><span>1. Data ingestion</span></div>
            <div class="step" id="s2"><span class="dot"></span><span>2. Correlation &amp; risk</span></div>
            <div class="step" id="s3"><span class="dot"></span><span>3. Optimization</span></div>
            <div class="step" id="s4"><span class="dot"></span><span>4. Convergence</span></div>
            <div class="progress" aria-hidden="true"><div id="prog"></div></div>
            <div style="font-size:12px;color:var(--muted);">Step <strong id="stepNum">1</strong>/4</div>
          </div>
        </div>
      </div>

      <div class="main">
        <div class="panel">
          <div class="panel__t">Quantum Compute Stream</div>
          <div class="panel__s">Pseudo-calculations, vectors, and matrices (simulated).</div>

          <div class="codegrid">
            <div class="code pulse"><pre id="codeA"></pre></div>
            <div class="code pulse"><pre id="codeB"></pre></div>
          </div>

          <div class="canvasWrap">
            <div class="can"><canvas id="cAlloc" width="900" height="420"></canvas></div>
            <div class="can"><canvas id="cRiskReturn" width="900" height="420"></canvas></div>
            <div class="can"><canvas id="cTrend" width="900" height="420"></canvas></div>
          </div>
        </div>

        <div class="panel">
          <div class="panel__t">Result</div>
          <div class="panel__s">After convergence, results become calm and readable.</div>

          <div class="result" id="result">
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
              <div class="badge" style="display:inline-flex;gap:8px;align-items:center;padding:8px 10px;border-radius:999px;border:1px solid rgba(212,175,55,.14);background:rgba(255,255,255,.06);font-size:12px;">
                Expected Return: <strong id="rRet">—</strong>
              </div>
              <div class="badge" style="display:inline-flex;gap:8px;align-items:center;padding:8px 10px;border-radius:999px;border:1px solid rgba(212,175,55,.14);background:rgba(255,255,255,.06);font-size:12px;">
                Estimated Risk: <strong id="rRisk">—</strong>
              </div>
              <div class="badge" style="display:inline-flex;gap:8px;align-items:center;padding:8px 10px;border-radius:999px;border:1px solid rgba(212,175,55,.14);background:rgba(255,255,255,.06);font-size:12px;">
                Diversification: <strong id="rDiv">—</strong>
              </div>
              <div class="badge" style="display:inline-flex;gap:8px;align-items:center;padding:8px 10px;border-radius:999px;border:1px solid rgba(212,175,55,.14);background:rgba(255,255,255,.06);font-size:12px;">
                Volatility: <strong id="rVolBand">—</strong>
              </div>
            </div>

            <table class="tbl" id="allocTbl">
              <thead><tr><th>Stock</th><th>Allocation</th></tr></thead>
              <tbody></tbody>
            </table>

            <div style="margin-top:10px;font-weight:900;">Key insights</div>
            <ul class="bul" id="insights"></ul>
          </div>

          <div class="actions">
            <button id="replayBtn" class="btn btn--primary" type="button" disabled>Replay Analysis</button>
            <a id="backBtn2" class="pill" href="{{ route('fintech.portfolio') }}" aria-disabled="true">Back to Portfolio</a>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function () {
        const lock = document.getElementById('lock');
        const backBtn = document.getElementById('backBtn');
        const backBtn2 = document.getElementById('backBtn2');
        const replayBtn = document.getElementById('replayBtn');
        const prog = document.getElementById('prog');
        const stepNum = document.getElementById('stepNum');
        const steps = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3'), document.getElementById('s4')];

        const mRisk = document.getElementById('mRisk');
        const mVol = document.getElementById('mVol');
        const mCorr = document.getElementById('mCorr');
        const mAlpha = document.getElementById('mAlpha');
        const codeA = document.getElementById('codeA');
        const codeB = document.getElementById('codeB');

        const result = document.getElementById('result');
        const rRet = document.getElementById('rRet');
        const rRisk = document.getElementById('rRisk');
        const rDiv = document.getElementById('rDiv');
        const rVolBand = document.getElementById('rVolBand');
        const allocTbl = document.getElementById('allocTbl').querySelector('tbody');
        const insights = document.getElementById('insights');

        const cAlloc = document.getElementById('cAlloc');
        const cRR = document.getElementById('cRiskReturn');
        const cTrend = document.getElementById('cTrend');

        function safeJsonParse(s) { try { return JSON.parse(s); } catch (e) { return null; } }
        const stored = safeJsonParse(localStorage.getItem('qbit_portfolio') || '') || { symbols: ['AAPL','MSFT','AMZN'], risk: 55 };
        const symbols = (stored.symbols || []).slice(0, 8);
        const baseRisk = Math.max(0, Math.min(100, Number(stored.risk || 0)));

        function hash32(str) {
          let h = 2166136261 >>> 0;
          for (let i = 0; i < str.length; i++) {
            h ^= str.charCodeAt(i);
            h = Math.imul(h, 16777619) >>> 0;
          }
          return h >>> 0;
        }
        function seeded(seed) {
          let s = seed >>> 0;
          return () => {
            s = (Math.imul(s, 1664525) + 1013904223) >>> 0;
            return (s & 0xffffffff) / 0x100000000;
          };
        }
        const seed = hash32(symbols.join('|') + '|' + baseRisk);
        const rnd = seeded(seed);

        function fmtPct(x) { return (x * 100).toFixed(2) + '%'; }

        let running = false;
        let timers = [];

        function setLocked(isLocked) {
          lock.classList.toggle('show', isLocked);
          backBtn.setAttribute('aria-disabled', isLocked ? 'true' : 'false');
          backBtn2.setAttribute('aria-disabled', isLocked ? 'true' : 'false');
          replayBtn.disabled = isLocked;
          if (isLocked) {
            window.onbeforeunload = () => true;
          } else {
            window.onbeforeunload = null;
          }
        }

        function setStep(idx) {
          for (let i = 0; i < steps.length; i++) {
            steps[i].classList.toggle('active', i === idx);
            steps[i].classList.toggle('done', i < idx);
          }
          stepNum.textContent = String(idx + 1);
        }

        function clearTimers() {
          for (const t of timers) clearInterval(t);
          timers = [];
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

        function makeCodeBlock(stage) {
          const n = Math.min(6, Math.max(3, symbols.length));
          const head = stage === 0 ? 'INGEST' : stage === 1 ? 'CORR/RISK' : stage === 2 ? 'OPTIMIZE' : 'CONVERGE';
          const a = [];
          a.push(`// QBIT Quantum Analysis :: ${head}`);
          a.push(`symbols = [${symbols.join(', ')}]`);
          a.push(`risk_pref = ${(baseRisk/100).toFixed(2)}`);
          a.push('');
          a.push(`Σ = cov(${n}x${n})`);
          a.push(makeMatrix(n));
          a.push('');
          a.push(makeVector(n));
          a.push(`α = ${(rnd()*0.18+0.02).toFixed(4)}  ρ̄ = ${(rnd()*0.7+0.2).toFixed(3)}`);
          return a.join('\n');
        }

        function tickMetrics(stage, t) {
          const wob = (x) => x + (rnd() * 2 - 1) * x * 0.08;
          const risk = Math.max(0, Math.min(1, wob(baseRisk/100)));
          const vol = Math.max(0.08, Math.min(0.95, wob(0.18 + risk*0.35)));
          const corr = Math.max(0.05, Math.min(0.98, wob(0.22 + risk*0.55)));
          const alpha = Math.max(-0.2, Math.min(0.35, wob(0.06 + (1-risk)*0.08)));
          mRisk.textContent = fmtPct(risk);
          mVol.textContent = fmtPct(vol);
          mCorr.textContent = fmtPct(corr);
          mAlpha.textContent = fmtPct(alpha);

          // stage-dependent intensity
          const pulse = stage < 3 ? 'flick' : '';
          mRisk.classList.toggle('flick', stage < 3);
          mVol.classList.toggle('flick', stage < 3);
          mCorr.classList.toggle('flick', stage < 3);
          mAlpha.classList.toggle('flick', stage < 3);
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
          for (let x = 0; x <= w; x += 60) { ctx.beginPath(); ctx.moveTo(x,0); ctx.lineTo(x,h); ctx.stroke(); }
          for (let y = 0; y <= h; y += 60) { ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(w,y); ctx.stroke(); }
          ctx.restore();
        }

        function drawAlloc(ctx, w, h, alloc, phase) {
          drawGrid(ctx,w,h);
          const cx = w*0.22, cy = h*0.5, r = Math.min(w,h)*0.28;
          const colors = ['#2D6BFF','#8B5CF6','#22C55E','#F59E0B','#d4af37','#ef4444','#38bdf8','#a3e635'];
          let start = -Math.PI/2;
          for (let i=0;i<alloc.length;i++){
            const ang = alloc[i].w * Math.PI*2 * phase;
            ctx.beginPath();
            ctx.moveTo(cx,cy);
            ctx.arc(cx,cy,r,start,start+ang);
            ctx.closePath();
            ctx.fillStyle = colors[i%colors.length];
            ctx.globalAlpha = 0.85;
            ctx.fill();
            start += ang;
          }
          ctx.globalAlpha = 1;
          // donut hole
          ctx.beginPath();
          ctx.arc(cx,cy,r*0.62,0,Math.PI*2);
          ctx.fillStyle = 'rgba(18,18,22,.92)';
          ctx.fill();
          ctx.lineWidth = 2;
          ctx.strokeStyle = 'rgba(212,175,55,.22)';
          ctx.stroke();

          // labels
          ctx.fillStyle = 'rgba(226,232,240,.92)';
          ctx.font = '700 13px ui-sans-serif, system-ui';
          ctx.fillText('Allocation', cx - 32, cy - 6);
          ctx.fillStyle = 'rgba(148,163,184,.92)';
          ctx.font = '500 12px ui-sans-serif, system-ui';
          ctx.fillText('quantum-weighted', cx - 54, cy + 14);

          // legend list
          const lx = w*0.48, ly = h*0.20;
          ctx.font = '700 12px ui-sans-serif, system-ui';
          for (let i=0;i<alloc.length;i++){
            const y = ly + i*22;
            ctx.fillStyle = colors[i%colors.length];
            ctx.globalAlpha = 0.95;
            ctx.beginPath(); ctx.arc(lx, y, 5, 0, Math.PI*2); ctx.fill();
            ctx.globalAlpha = 1;
            ctx.fillStyle = 'rgba(226,232,240,.92)';
            ctx.fillText(`${alloc[i].s}  ${(alloc[i].w*100).toFixed(1)}%`, lx + 14, y + 4);
          }
        }

        function drawRiskReturn(ctx, w, h, t, stage) {
          drawGrid(ctx,w,h);
          const pad = 44;
          ctx.save();
          ctx.strokeStyle = 'rgba(226,232,240,.28)';
          ctx.lineWidth = 1;
          ctx.beginPath();
          ctx.moveTo(pad, h-pad);
          ctx.lineTo(w-pad, h-pad);
          ctx.lineTo(w-pad, pad);
          ctx.stroke();
          ctx.restore();

          // pseudo frontier
          ctx.beginPath();
          for (let i=0;i<=60;i++){
            const x = i/60;
            const rx = pad + x*(w-2*pad);
            const ry = h-pad - (Math.pow(x,0.7)*(h-2*pad));
            if (i===0) ctx.moveTo(rx,ry);
            else ctx.lineTo(rx,ry);
          }
          ctx.strokeStyle = 'rgba(45,107,255,.75)';
          ctx.lineWidth = 2;
          ctx.shadowColor = 'rgba(45,107,255,.35)';
          ctx.shadowBlur = 10;
          ctx.stroke();
          ctx.shadowBlur = 0;

          // moving point (during analysis) -> converged point (end)
          const x = Math.max(0, Math.min(1, (baseRisk/100) + (stage<3 ? (Math.sin(t/300)*0.12) : 0)));
          const y = 0.25 + (1 - x) * 0.38 + (stage<3 ? (Math.cos(t/260)*0.04) : 0);
          const px = pad + x*(w-2*pad);
          const py = h-pad - y*(h-2*pad);
          ctx.beginPath();
          ctx.arc(px,py,7,0,Math.PI*2);
          ctx.fillStyle = 'rgba(212,175,55,.92)';
          ctx.shadowColor = 'rgba(212,175,55,.35)';
          ctx.shadowBlur = 18;
          ctx.fill();
          ctx.shadowBlur = 0;

          ctx.fillStyle = 'rgba(226,232,240,.9)';
          ctx.font = '800 12px ui-sans-serif, system-ui';
          ctx.fillText('Risk', w-pad-26, h-pad+26);
          ctx.fillText('Return', 10, pad);
        }

        function drawTrend(ctx, w, h, t, stage, alloc) {
          drawGrid(ctx,w,h);
          const pad = 30;
          // axes baseline
          ctx.strokeStyle = 'rgba(226,232,240,.18)';
          ctx.lineWidth = 1;
          ctx.beginPath(); ctx.moveTo(pad,h-pad); ctx.lineTo(w-pad,h-pad); ctx.stroke();

          const colors = ['rgba(45,107,255,.85)','rgba(139,92,246,.75)','rgba(34,197,94,.75)','rgba(245,158,11,.75)','rgba(212,175,55,.75)'];
          const series = alloc.slice(0, Math.min(4, alloc.length));
          for (let s=0;s<series.length;s++){
            const amp = 0.10 + series[s].w*0.22;
            ctx.beginPath();
            for (let i=0;i<80;i++){
              const x = i/79;
              const px = pad + x*(w-2*pad);
              const base = 0.55 + (s*0.08);
              const wob = stage<3 ? (Math.sin((t/260)+(s*1.2)+(x*6.2))*0.08) : (Math.sin((seed%1000)/100 + x*5 + s)*0.03);
              const val = base + wob + (x-0.5)*amp;
              const py = h-pad - (val*(h-2*pad))*0.55;
              if (i===0) ctx.moveTo(px,py);
              else ctx.lineTo(px,py);
            }
            ctx.strokeStyle = colors[s%colors.length];
            ctx.lineWidth = 2;
            ctx.shadowColor = colors[s%colors.length];
            ctx.shadowBlur = 10;
            ctx.stroke();
            ctx.shadowBlur = 0;
          }

          ctx.fillStyle = 'rgba(226,232,240,.9)';
          ctx.font = '800 12px ui-sans-serif, system-ui';
          ctx.fillText('Trend Lines', pad, pad+8);
        }

        function finalize(alloc) {
          // scores
          const expReturn = 0.12 + (1 - baseRisk/100) * 0.10 + rnd()*0.03;
          const estRisk = 0.18 + (baseRisk/100) * 0.35 + rnd()*0.03;
          const div = 0.62 + (1 - alloc.reduce((m,x)=>Math.max(m,x.w),0))*0.38;
          const band = estRisk < 0.28 ? 'Low' : estRisk < 0.40 ? 'Medium' : 'High';

          rRet.textContent = fmtPct(expReturn);
          rRisk.textContent = fmtPct(estRisk);
          rDiv.textContent = (div*100).toFixed(1) + '/100';
          rVolBand.textContent = band;

          allocTbl.innerHTML = alloc
            .map(x => `<tr><td>${x.s}</td><td>${(x.w*100).toFixed(2)}%</td></tr>`)
            .join('');

          const top = [...alloc].sort((a,b)=>b.w-a.w)[0];
          const ins = [
            `Weights converge with a ${(baseRisk/100).toFixed(2)} risk preference using covariance regularization and sampling.`,
            `Largest tilt is toward ${top.s}, balanced by diversified secondary positions to reduce correlation shock.`,
            `Risk band is ${band} with a diversification score of ${(div*100).toFixed(0)}/100.`,
            `Rebalancing friction is minimized by preferring smoother weight distributions (no single asset dominates).`,
          ];
          insights.innerHTML = ins.map(x => `<li>${x}</li>`).join('');

          result.classList.add('show');
          backBtn.setAttribute('aria-disabled','false');
          backBtn2.setAttribute('aria-disabled','false');
        }

        function run() {
          if (running) return;
          running = true;
          result.classList.remove('show');
          setLocked(true);
          clearTimers();
          setStep(0);
          prog.style.width = '0%';

          const alloc = weights();
          let stage = 0;
          let start = Date.now();

          // fast changing numbers
          timers.push(setInterval(() => tickMetrics(stage, Date.now()-start), 50));
          // code panels
          timers.push(setInterval(() => {
            codeA.textContent = makeCodeBlock(stage);
            codeB.textContent = makeCodeBlock(Math.min(3, stage+1));
          }, 90));

          // canvas loop
          let raf = 0;
          function loop() {
            const t = Date.now() - start;
            const ctxA = cAlloc.getContext('2d');
            const ctxR = cRR.getContext('2d');
            const ctxT = cTrend.getContext('2d');
            const phase = stage < 3 ? Math.min(1, (t % 1200) / 1200) : 1;
            drawAlloc(ctxA, cAlloc.width, cAlloc.height, alloc, phase);
            drawRiskReturn(ctxR, cRR.width, cRR.height, t, stage);
            drawTrend(ctxT, cTrend.width, cTrend.height, t, stage, alloc);
            raf = requestAnimationFrame(loop);
          }
          raf = requestAnimationFrame(loop);

          function goStage(next, pct) {
            stage = next;
            setStep(stage);
            prog.style.width = pct + '%';
          }

          // staged progression
          const schedule = [
            { t: 0, s: 0, p: 10 },
            { t: 1200, s: 1, p: 38 },
            { t: 2600, s: 2, p: 68 },
            { t: 4200, s: 3, p: 100 },
          ];
          for (const it of schedule) {
            timers.push(setTimeout(() => goStage(it.s, it.p), it.t));
          }

          timers.push(setTimeout(() => {
            cancelAnimationFrame(raf);
            clearTimers();
            // final draw static
            const ctxA = cAlloc.getContext('2d');
            const ctxR = cRR.getContext('2d');
            const ctxT = cTrend.getContext('2d');
            drawAlloc(ctxA, cAlloc.width, cAlloc.height, alloc, 1);
            drawRiskReturn(ctxR, cRR.width, cRR.height, Date.now()-start, 3);
            drawTrend(ctxT, cTrend.width, cTrend.height, Date.now()-start, 3, alloc);

            finalize(alloc);
            setLocked(false);
            replayBtn.disabled = false;
            running = false;
          }, 5200));
        }

        replayBtn.addEventListener('click', () => run());

        // Auto-run on load
        run();
      })();
    </script>
  </body>
</html>
