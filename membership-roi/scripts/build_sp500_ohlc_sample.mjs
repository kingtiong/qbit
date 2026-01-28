import fs from "node:fs";
import path from "node:path";

const ROOT = path.resolve(process.cwd(), "membership-roi");
const IN_CSV = path.join(ROOT, "public", "data", "sp500.csv");
const OUT_CSV = path.join(ROOT, "public", "data", "sp500_ohlc_sample.csv");

const MAX_SYMBOLS = Number(process.env.MAX_SYMBOLS || 140);
const KEEP_DAYS = Number(process.env.KEEP_DAYS || 320);
const CONCURRENCY = Number(process.env.CONCURRENCY || 10);

function parseCsvLine(line) {
  const res = [];
  let cur = "";
  let inQ = false;
  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (ch === '"') {
      if (inQ && line[i + 1] === '"') {
        cur += '"';
        i++;
      } else {
        inQ = !inQ;
      }
    } else if (ch === "," && !inQ) {
      res.push(cur);
      cur = "";
    } else {
      cur += ch;
    }
  }
  res.push(cur);
  return res;
}

function parseSp500List(text) {
  const lines = text.split(/\r?\n/).filter(Boolean);
  lines.shift(); // header
  const syms = [];
  for (const ln of lines) {
    const cols = parseCsvLine(ln);
    const sym = (cols[0] || "").trim();
    if (sym) syms.push(sym);
  }
  return syms;
}

function stooqSymbol(sym) {
  // Stooq uses lowercase + ".us". Many tickers with "." work as-is (e.g. brk.b.us).
  return sym.toLowerCase() + ".us";
}

async function fetchText(url) {
  const res = await fetch(url, { headers: { "user-agent": "qbit-demo-builder" } });
  if (!res.ok) throw new Error(`HTTP ${res.status}`);
  return await res.text();
}

function parseStooqOhlc(text) {
  const lines = text.split(/\r?\n/).filter(Boolean);
  const header = lines.shift();
  if (!header || !header.toLowerCase().includes("date")) return [];
  const out = [];
  for (const ln of lines) {
    const cols = parseCsvLine(ln);
    if (cols.length < 6) continue;
    const [date, open, high, low, close, volume] = cols;
    if (!date || date === "Date") continue;
    out.push({
      date,
      open: Number(open),
      high: Number(high),
      low: Number(low),
      close: Number(close),
      volume: Number(volume),
    });
  }
  // Stooq returns ascending by date; keep most recent KEEP_DAYS
  return out.slice(-KEEP_DAYS);
}

async function mapPool(items, worker, concurrency) {
  const results = new Array(items.length);
  let idx = 0;
  async function run() {
    while (true) {
      const i = idx++;
      if (i >= items.length) break;
      results[i] = await worker(items[i], i);
    }
  }
  const runners = [];
  for (let k = 0; k < concurrency; k++) runners.push(run());
  await Promise.all(runners);
  return results;
}

async function main() {
  const spText = fs.readFileSync(IN_CSV, "utf8");
  const symbols = parseSp500List(spText).slice(0, MAX_SYMBOLS);

  const rows = [];
  let ok = 0;
  let fail = 0;

  await mapPool(
    symbols,
    async (sym) => {
      const stooq = stooqSymbol(sym);
      const url = `https://stooq.com/q/d/l/?s=${encodeURIComponent(stooq)}&i=d`;
      try {
        const txt = await fetchText(url);
        const ohlc = parseStooqOhlc(txt);
        if (!ohlc.length) throw new Error("empty");
        for (const r of ohlc) {
          rows.push(
            `${r.date},${sym},${r.open.toFixed(4)},${r.high.toFixed(4)},${r.low.toFixed(4)},${r.close.toFixed(4)},${Number.isFinite(r.volume) ? String(r.volume) : ""}`
          );
        }
        ok++;
      } catch (e) {
        fail++;
      }
    },
    CONCURRENCY
  );

  // Sort combined rows by date then symbol for stable diffs
  rows.sort((a, b) => {
    const da = a.slice(0, 10);
    const db = b.slice(0, 10);
    if (da < db) return -1;
    if (da > db) return 1;
    const sa = a.split(",")[1];
    const sb = b.split(",")[1];
    return sa.localeCompare(sb);
  });

  const header = "date,symbol,open,high,low,close,volume";
  fs.writeFileSync(OUT_CSV, header + "\n" + rows.join("\n") + "\n", "utf8");

  // eslint-disable-next-line no-console
  console.log(`Wrote ${OUT_CSV}`);
  // eslint-disable-next-line no-console
  console.log(`Symbols requested: ${symbols.length} | ok: ${ok} | fail: ${fail}`);
}

main().catch((e) => {
  // eslint-disable-next-line no-console
  console.error(e);
  process.exit(1);
});

