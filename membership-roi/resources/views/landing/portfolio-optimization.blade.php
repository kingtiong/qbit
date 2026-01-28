<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>S&amp;P 500 Stock Selector</title>
    <link rel="icon" type="image/png" href="{{ asset('images/Logo01.png') }}">
    @if (!app()->environment('testing'))
      @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
      :root{
        --bg:#07070a; --surface:rgba(18,18,22,.82); --surface2:rgba(7,7,10,.55);
        --border:rgba(212,175,55,.18); --text:#f8fafc; --muted:rgba(148,163,184,.92);
        --gold:#d4af37; --gold2:#f2d06b;
      }
      *{box-sizing:border-box}
      body{
        margin:0;
        background:
          radial-gradient(900px 500px at 20% 0%, rgba(212,175,55,.10), transparent 60%),
          radial-gradient(900px 500px at 80% 20%, rgba(45,107,255,.10), transparent 55%),
          var(--bg);
        color:var(--text);
        font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      }
      .wrap{max-width:1280px;margin:0 auto;padding:26px 18px 60px;}
      .top{display:flex;align-items:center;gap:14px;}
      .brand{display:flex;align-items:center;gap:10px;text-decoration:none;color:inherit;}
      .brand img{height:46px;width:auto;display:block;}
      .brand__title{font-weight:850;letter-spacing:-.02em;}
      .brand__sub{margin-top:2px;font-size:12.5px;color:var(--muted);line-height:1.2;}
      .spacer{margin-left:auto;}
      .pill{
        display:inline-flex;align-items:center;gap:8px;padding:10px 14px;border-radius:999px;
        border:1px solid var(--border);background:rgba(18,18,22,.72);
        color:var(--text);text-decoration:none;font-size:13px;
      }
      .pill:hover{background:rgba(18,18,22,.88);}

      .grid{margin-top:16px;display:grid;grid-template-columns:1.65fr 1fr;gap:18px;align-items:start;}
      @media (max-width: 980px){.grid{grid-template-columns:1fr;}}
      .card{
        border-radius:22px;background:var(--surface);border:1px solid var(--border);
        box-shadow:0 22px 65px rgba(0,0,0,.60);
      }
      .card__pad{padding:16px;}
      .h1{margin:0;font-size:22px;letter-spacing:-.02em;}
      .sub{margin-top:6px;color:var(--muted);font-size:13.5px;line-height:1.55;}
      .toolbar{margin-top:12px;display:grid;grid-template-columns:1fr 220px;gap:12px;}
      @media (max-width: 980px){.toolbar{grid-template-columns:1fr;}}
      .input,.select{
        width:100%;border-radius:14px;border:1px solid rgba(212,175,55,.18);
        background:rgba(7,7,10,.65);color:var(--text);padding:10px 12px;outline:none;
      }
      .input:focus,.select:focus{box-shadow:0 0 0 3px rgba(212,175,55,.12);}
      .metaRow{margin-top:10px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;}
      .badge{
        display:inline-flex;align-items:center;gap:8px;padding:8px 10px;border-radius:999px;
        background:rgba(255,255,255,.06);border:1px solid rgba(212,175,55,.14);color:rgba(226,232,240,.92);
        font-size:12px;
      }
      .warn{color:rgba(251,191,36,.95);font-size:12.5px;}

      .tableWrap{margin-top:14px;border-radius:18px;border:1px solid rgba(212,175,55,.14);background:var(--surface2);overflow:auto;max-height:560px;}
      table{width:100%;border-collapse:separate;border-spacing:0;min-width:980px;}
      thead th{
        position:sticky;top:0;z-index:2;
        background:rgba(18,18,22,.92);color:rgba(226,232,240,.92);
        font-weight:750;font-size:12px;text-align:left;padding:12px 12px;border-bottom:1px solid rgba(148,163,184,.18);
        white-space:nowrap;
      }
      tbody td{
        padding:12px 12px;border-bottom:1px solid rgba(148,163,184,.14);font-size:13px;color:rgba(226,232,240,.92);
        vertical-align:middle;
      }
      tbody tr:hover td{background:rgba(255,255,255,.03);}
      .sect td{
        background:rgba(212,175,55,.08);
        color:rgba(226,232,240,.95);
        font-weight:800;
        letter-spacing:.02em;
      }
      .code{font-weight:850;letter-spacing:.02em;}
      .name{color:rgba(226,232,240,.9);}
      .muted{color:var(--muted);}
      .btn{
        border:0;border-radius:12px;padding:7px 10px;font-weight:850;cursor:pointer;
        background:rgba(255,255,255,.06);border:1px solid rgba(212,175,55,.18);color:var(--text);
      }
      .btn:hover{background:rgba(255,255,255,.08);}
      .btn:disabled{opacity:.45;cursor:not-allowed;}
      .btn--primary{background:linear-gradient(135deg,var(--gold),var(--gold2));color:#111;border:0;}
      .btn--primary:hover{filter:brightness(1.03);}
      .btnRow{display:flex;gap:8px;align-items:center;}

      .portfolioBox{display:grid;gap:12px;}
      .portHead{display:flex;align-items:flex-end;justify-content:space-between;gap:10px;}
      .cap{font-size:12px;color:var(--muted);}
      .chips{display:flex;flex-wrap:wrap;gap:10px;}
      .portAction{margin-top:12px;display:flex;justify-content:flex-start;}
      .portAction .btn{width:auto;padding:8px 10px;border-radius:12px;font-size:12px;}
      .chip{
        display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:16px;
        border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);
      }
      .chip__l{display:grid;gap:2px;}
      .chip__t{font-weight:850;letter-spacing:.02em;}
      .chip__s{font-size:12px;color:var(--muted);}
      .chip__x{margin-left:6px;}

      .riskBox{margin-top:14px;border-radius:18px;border:1px solid rgba(212,175,55,.14);background:rgba(7,7,10,.55);padding:12px;}
      .riskTop{display:flex;align-items:center;justify-content:space-between;gap:10px;}
      .riskLbl{font-weight:800;font-size:13px;}
      .riskVal{font-size:12.5px;color:var(--muted);}
      .riskLine{margin-top:10px;display:grid;grid-template-columns:auto 1fr auto;gap:10px;align-items:center;}
      .riskEnd{font-size:12px;color:rgba(226,232,240,.82);white-space:nowrap;}
      input[type="range"]{width:100%;}
      input[type="range"]{accent-color: var(--gold);}
      .foot{margin-top:10px;font-size:12px;color:rgba(148,163,184,.85);line-height:1.4;}
    </style>
  </head>
  <body>
    <div class="wrap">
      <div class="top">
        <a class="brand" href="{{ url('/') }}">
          <img src="{{ asset('images/Logo01.png') }}" alt="">
          <div>
            <div class="brand__title">S&amp;P 500 Stocks</div>
            <div class="brand__sub">Search, filter by industry, and build a portfolio (max 8)</div>
          </div>
        </a>
        <div class="spacer"></div>
        <a class="pill" href="{{ url('/') }}">Back to Home</a>
      </div>

      <div class="grid">
        <div class="card">
          <div class="card__pad">
            <h1 class="h1">Stock List</h1>
            <div class="sub">Data source: S&amp;P 500 constituents list. Prices are simulated (demo logic).</div>

            <div class="toolbar">
              <input id="search" class="input" placeholder="Search by stock code or stock name (e.g., AAPL, Apple)" autocomplete="off">
              <select id="industry" class="select"></select>
            </div>

            <div class="metaRow">
              <div class="badge">Selected: <strong id="selCount">0</strong>/8</div>
              <div class="badge">Showing: <strong id="showCount">0</strong></div>
              <div class="warn" id="warn"></div>
            </div>
          </div>

          <div class="tableWrap">
            <table>
              <thead>
                <tr>
                  <th>Stock Code</th>
                  <th>Stock Name</th>
                  <th>Industry</th>
                  <th>Current Price</th>
                  <th>Price Limit</th>
                  <th>Lowest Price</th>
                  <th>Highest Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="tbody"></tbody>
            </table>
          </div>
        </div>

        <div>
          <div class="card">
            <div class="card__pad portfolioBox">
              <div class="portHead">
                <div>
                  <div class="h1" style="font-size:18px;margin:0;">Investment Portfolio</div>
                  <div class="cap">Click [+] Add to include stocks (max 8).</div>
                </div>
                <button id="clearBtn" class="btn" type="button">Clear</button>
              </div>

              <div class="chips" id="chips"></div>

              <div class="riskBox">
                <div class="riskTop">
                  <div class="riskLbl">Please choose risk preference</div>
                  <div class="riskVal">Risk level: <strong id="riskVal">55</strong>/100</div>
                </div>
                <div class="riskLine">
                  <div class="riskEnd">Low Risk</div>
                  <input id="risk" type="range" min="0" max="100" value="55">
                  <div class="riskEnd">High Risk</div>
                </div>
                <div class="foot">Higher risk may allow wider price ranges in this demo view.</div>
              </div>

              <div class="portAction">
                <button id="startOptBtn" class="btn btn--primary" type="button">Start Portfolio Optimization</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      (function () {
        const MAX = 8;
        const els = {
          search: document.getElementById('search'),
          industry: document.getElementById('industry'),
          tbody: document.getElementById('tbody'),
          chips: document.getElementById('chips'),
          selCount: document.getElementById('selCount'),
          showCount: document.getElementById('showCount'),
          warn: document.getElementById('warn'),
          risk: document.getElementById('risk'),
          riskVal: document.getElementById('riskVal'),
          clearBtn: document.getElementById('clearBtn'),
          startOptBtn: document.getElementById('startOptBtn'),
        };

        let all = [];
        let selected = []; // array of symbols
        let ohlcBySym = new Map(); // symbol -> [{date,open,high,low,close,volume}]

        function hash32(str) {
          let h = 2166136261 >>> 0;
          for (let i = 0; i < str.length; i++) {
            h ^= str.charCodeAt(i);
            h = Math.imul(h, 16777619) >>> 0;
          }
          return h >>> 0;
        }

        function seeded(h) {
          let s = h >>> 0;
          return () => {
            s = (Math.imul(s, 1664525) + 1013904223) >>> 0;
            return (s & 0xffffffff) / 0x100000000;
          };
        }

        function priceModel(sym) {
          const rows = ohlcBySym.get(sym);
          if (rows && rows.length) {
            const last = rows[rows.length - 1];
            const cp = Number(last.close) || 0;
            const limitPct = 0.10;
            const limLow = cp * (1 - limitPct);
            const limHigh = cp * (1 + limitPct);
            const window = rows.slice(-252);
            const lowest = window.reduce((m, r) => Math.min(m, Number(r.low) || Infinity), Infinity);
            const highest = window.reduce((m, r) => Math.max(m, Number(r.high) || 0), 0);
            return { cp, limLow, limHigh, low: lowest, high: highest };
          }
          // fallback demo model (deterministic)
          const r = seeded(hash32(sym));
          const base = 8 + r() * 520; // 8..528
          const cp = Math.max(1, base);
          const limitPct = 0.10;
          const low = cp * (1 - (0.02 + r() * 0.06));
          const high = cp * (1 + (0.02 + r() * 0.06));
          const limLow = cp * (1 - limitPct);
          const limHigh = cp * (1 + limitPct);
          return { cp, limLow, limHigh, low, high };
        }

        function money(x) {
          return '$' + x.toFixed(2);
        }

        function parseCsv(text) {
          const lines = text.split(/\r?\n/).filter(Boolean);
          const out = [];
          const header = lines.shift();
          if (!header) return out;

          function parseLine(line) {
            const res = [];
            let cur = '';
            let inQ = false;
            for (let i = 0; i < line.length; i++) {
              const ch = line[i];
              if (ch === '"') {
                if (inQ && line[i + 1] === '"') { cur += '"'; i++; }
                else inQ = !inQ;
              } else if (ch === ',' && !inQ) {
                res.push(cur);
                cur = '';
              } else {
                cur += ch;
              }
            }
            res.push(cur);
            return res;
          }

          for (const ln of lines) {
            const cols = parseLine(ln);
            const sym = (cols[0] || '').trim();
            const name = (cols[1] || '').trim();
            const sector = (cols[2] || '').trim();
            if (!sym || !name) continue;
            out.push({ sym, name, sector });
          }
          return out;
        }

        function setWarn(msg) { els.warn.textContent = msg || ''; }

        function isSelected(sym) {
          return selected.includes(sym);
        }

        function add(sym) {
          if (isSelected(sym)) return;
          if (selected.length >= MAX) {
            setWarn('Maximum 8 stocks selected.');
            return;
          }
          selected = [...selected, sym];
          setWarn('');
          render();
        }

        function remove(sym) {
          selected = selected.filter(s => s !== sym);
          setWarn('');
          render();
        }

        function industries() {
          const set = new Set(all.map(x => x.sector).filter(Boolean));
          return ['All Industries', ...Array.from(set).sort()];
        }

        function filtered() {
          const q = (els.search.value || '').trim().toLowerCase();
          const ind = els.industry.value || 'All Industries';
          let list = all;
          if (ind !== 'All Industries') list = list.filter(x => x.sector === ind);
          if (q) list = list.filter(x => x.sym.toLowerCase().includes(q) || x.name.toLowerCase().includes(q));
          return list;
        }

        function groupByIndustry(list) {
          const groups = new Map();
          for (const it of list) {
            const key = it.sector || 'Unknown';
            if (!groups.has(key)) groups.set(key, []);
            groups.get(key).push(it);
          }
          for (const [k, v] of groups) v.sort((a,b) => a.sym.localeCompare(b.sym));
          return Array.from(groups.entries()).sort((a,b) => a[0].localeCompare(b[0]));
        }

        function renderTable() {
          const list = filtered();
          els.showCount.textContent = String(list.length);

          const groups = groupByIndustry(list);
          const rows = [];
          for (const [sector, items] of groups) {
            rows.push(`<tr class="sect"><td colspan="8">${sector}</td></tr>`);
            for (const it of items) {
              const p = priceModel(it.sym);
              const sel = isSelected(it.sym);
              const canAdd = !sel && selected.length < MAX;
              rows.push(
                `<tr>
                  <td class="code">${it.sym}</td>
                  <td class="name">${it.name}</td>
                  <td class="muted">${it.sector}</td>
                  <td>${money(p.cp)}</td>
                  <td class="muted">${money(p.limLow)} – ${money(p.limHigh)}</td>
                  <td>${money(Math.min(p.low, p.cp))}</td>
                  <td>${money(Math.max(p.high, p.cp))}</td>
                  <td>
                    <div class="btnRow">
                      <button class="btn" type="button" data-add="${it.sym}" ${canAdd ? '' : 'disabled'}>[+] Add</button>
                      <button class="btn" type="button" data-rem="${it.sym}" ${sel ? '' : 'disabled'}>[-] Remove</button>
                    </div>
                  </td>
                </tr>`
              );
            }
          }
          els.tbody.innerHTML = rows.join('');

          els.tbody.querySelectorAll('button[data-add]').forEach(btn => {
            btn.addEventListener('click', () => add(btn.getAttribute('data-add')));
          });
          els.tbody.querySelectorAll('button[data-rem]').forEach(btn => {
            btn.addEventListener('click', () => remove(btn.getAttribute('data-rem')));
          });
        }

        function renderPortfolio() {
          els.selCount.textContent = String(selected.length);
          if (!selected.length) {
            els.chips.innerHTML = `<div class="muted">No stocks selected yet.</div>`;
            return;
          }
          const bySym = new Map(all.map(x => [x.sym, x]));
          els.chips.innerHTML = selected.map(sym => {
            const it = bySym.get(sym);
            const p = priceModel(sym);
            return `
              <div class="chip">
                <div class="chip__l">
                  <div class="chip__t">${sym}</div>
                  <div class="chip__s">${it ? it.name : ''} • ${money(p.cp)}</div>
                </div>
                <button class="btn chip__x" type="button" data-x="${sym}">Remove</button>
              </div>
            `;
          }).join('');
          els.chips.querySelectorAll('button[data-x]').forEach(b => {
            b.addEventListener('click', () => remove(b.getAttribute('data-x')));
          });
        }

        function render() {
          renderTable();
          renderPortfolio();
          if (selected.length >= MAX) setWarn('Maximum 8 stocks selected.');
        }

        function initIndustrySelect() {
          const opts = industries();
          els.industry.innerHTML = opts.map(x => `<option value="${x}">${x}</option>`).join('');
        }

        function initEvents() {
          els.search.addEventListener('input', render);
          els.industry.addEventListener('change', render);
          els.clearBtn.addEventListener('click', () => { selected = []; setWarn(''); render(); });
          els.risk.addEventListener('input', () => { els.riskVal.textContent = els.risk.value; });
          els.startOptBtn.addEventListener('click', () => {
            if (selected.length < 2) {
              setWarn('Select at least 2 stocks to start optimization.');
              return;
            }
            try {
              localStorage.setItem('qbit_portfolio', JSON.stringify({
                symbols: selected.slice(0, MAX),
                risk: Number(els.risk.value || 0),
                ts: Date.now(),
              }));
            } catch (e) {}
            window.location.href = '{{ route('fintech.portfolio.analysis') }}';
          });
        }

        Promise.all([
          fetch('{{ asset('data/sp500.csv') }}', { cache: 'no-store' }).then(r => r.text()),
          fetch('{{ asset('data/sp500_ohlc_sample.csv') }}', { cache: 'no-store' }).then(r => r.ok ? r.text() : ''),
        ])
          .then(([spTxt, ohlcTxt]) => {
            all = parseCsv(spTxt);
            // parse OHLC sample
            const lines = (ohlcTxt || '').split(/\r?\n/).filter(Boolean);
            lines.shift(); // header
            for (const ln of lines) {
              const cols = ln.split(',');
              if (cols.length < 7) continue;
              const [date, sym, open, high, low, close, volume] = cols;
              if (!sym) continue;
              if (!ohlcBySym.has(sym)) ohlcBySym.set(sym, []);
              ohlcBySym.get(sym).push({ date, open: Number(open), high: Number(high), low: Number(low), close: Number(close), volume: Number(volume) });
            }
            initIndustrySelect();
            initEvents();
            render();
          })
          .catch(() => {
            all = [];
            initIndustrySelect();
            initEvents();
            setWarn('Failed to load S&P 500 list.');
            render();
          });
      })();
    </script>
  </body>
</html>
