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

    <style>
*,
*::before,
*::after {
  box-sizing: border-box;
}

html,
body {
  height: 100%;
}

body {
  margin: 0;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
  color: #0f172a;
  background: #f3f2ff;
}

.container {
  width: 100%;
  max-width: 1220px;
  margin: 0 auto;
  padding: 0 18px;
}

.topbar {
  position: sticky;
  top: 0;
  z-index: 10;
  background: rgba(255, 255, 255, 0.86);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid rgba(148, 163, 184, 0.3);
}

.topbar__inner {
  display: flex;
  align-items: center;
  gap: 16px;
  height: 66px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 210px;
}

.brand__mark {
  width: 30px;
  height: 30px;
  display: grid;
  place-items: center;
}

.brand__name {
  font-weight: 650;
  letter-spacing: -0.02em;
  color: #0f172a;
}

.nav {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  overflow: hidden;
  white-space: nowrap;
}

.nav__link {
  text-decoration: none;
  color: rgba(15, 23, 42, 0.72);
  font-size: 13px;
  padding: 6px 6px;
  border-radius: 10px;
}

.nav__link:hover {
  background: rgba(15, 23, 42, 0.04);
  color: rgba(15, 23, 42, 0.88);
}

.topbar__actions {
  display: flex;
  align-items: center;
  gap: 10px;
}

.lang {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid rgba(148, 163, 184, 0.35);
  color: rgba(15, 23, 42, 0.78);
  font-size: 13px;
  cursor: default;
}

.pledge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #3b2cff, #2459ff);
  color: #fff;
  font-weight: 650;
  font-size: 13px;
  cursor: default;
  box-shadow: 0 10px 22px rgba(59, 44, 255, 0.28);
}

.hero {
  background:
    radial-gradient(900px 520px at 20% 10%, rgba(122, 106, 255, 0.22), transparent 55%),
    radial-gradient(700px 420px at 80% 10%, rgba(164, 119, 255, 0.16), transparent 55%),
    linear-gradient(180deg, rgba(243, 242, 255, 0.9), rgba(243, 242, 255, 0.95));
  padding: 52px 0 38px;
}

.hero__grid {
  display: grid;
  grid-template-columns: 1.15fr 0.85fr;
  gap: 36px;
  align-items: start;
}

.hero__title {
  margin: 0;
  font-size: 64px;
  line-height: 1.05;
  letter-spacing: -0.03em;
  color: #1f2a37;
}

.hero__title span {
  display: block;
}

.hero__lead {
  margin: 18px 0 0;
  font-size: 16px;
  line-height: 1.6;
  color: rgba(51, 65, 85, 0.82);
}

.hero__cta {
  margin-top: 22px;
  display: flex;
  align-items: center;
  gap: 14px;
}

.primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 18px;
  border-radius: 999px;
  border: none;
  background: linear-gradient(135deg, #3b2cff, #2459ff);
  color: #fff;
  font-weight: 650;
  font-size: 13px;
  cursor: default;
  box-shadow: 0 10px 22px rgba(59, 44, 255, 0.28);
}

.linkbtn {
  border: none;
  background: transparent;
  color: rgba(51, 65, 85, 0.86);
  font-size: 13px;
  padding: 10px 8px;
  cursor: default;
}

.stats {
  margin-top: 28px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
}

.stat {
  border-radius: 16px;
  padding: 16px 16px 14px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
  position: relative;
  overflow: hidden;
}

.stat::before {
  content: "";
  position: absolute;
  width: 84px;
  height: 84px;
  right: -22px;
  top: -26px;
  background: radial-gradient(circle at 30% 30%, rgba(59, 44, 255, 0.18), rgba(59, 44, 255, 0));
  border-radius: 999px;
}

.stat__label {
  font-size: 12px;
  color: rgba(51, 65, 85, 0.72);
}

.stat__value {
  margin-top: 10px;
  font-size: 34px;
  font-weight: 750;
  letter-spacing: -0.02em;
  color: #111827;
}

.hero__disclaimer {
  margin-top: 14px;
  font-size: 12px;
  color: rgba(51, 65, 85, 0.62);
}

.highlight-card {
  position: relative;
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.74);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.08);
  padding: 18px;
  overflow: hidden;
  min-height: 310px;
}

.highlight-card__title {
  font-weight: 650;
  font-size: 13px;
  color: rgba(15, 23, 42, 0.82);
}

.highlight-grid {
  margin-top: 14px;
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  position: relative;
  z-index: 2;
}

.mini {
  display: grid;
  grid-template-columns: 34px 1fr;
  gap: 10px;
  padding: 14px 14px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.06);
}

.mini__icon {
  width: 34px;
  height: 34px;
  border-radius: 999px;
  background: rgba(59, 44, 255, 0.06);
  display: grid;
  place-items: center;
  border: 1px solid rgba(59, 44, 255, 0.12);
}

.mini__head {
  font-weight: 650;
  font-size: 13px;
  line-height: 1.25;
  color: rgba(15, 23, 42, 0.82);
}

.cube-art {
  position: absolute;
  right: 14px;
  top: 52px;
  width: 220px;
  height: 220px;
  z-index: 1;
  opacity: 0.95;
}

.cube {
  position: absolute;
  width: 86px;
  height: 86px;
  border-radius: 12px;
  background:
    linear-gradient(135deg, rgba(167, 139, 250, 0.26), rgba(59, 44, 255, 0.10)),
    linear-gradient(180deg, rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0.12));
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 18px 55px rgba(59, 44, 255, 0.10);
}

.cube--a { right: 0; top: 0; }
.cube--b { right: 52px; top: 34px; }
.cube--c { right: 0; top: 86px; }
.cube--d { right: 52px; top: 120px; }

.roadshow {
  margin-top: 22px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 18px;
}

.roadshow__title {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.86);
}

.roadshow__sub {
  margin-top: 4px;
  font-size: 13px;
  color: rgba(51, 65, 85, 0.75);
}

.ghost {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(148, 163, 184, 0.35);
  color: rgba(51, 65, 85, 0.82);
  font-size: 13px;
  cursor: default;
}

.advantages {
  padding: 60px 0 70px;
  background:
    radial-gradient(900px 520px at 80% 10%, rgba(122, 106, 255, 0.16), transparent 55%),
    radial-gradient(700px 420px at 20% 0%, rgba(164, 119, 255, 0.12), transparent 55%),
    linear-gradient(180deg, rgba(243, 242, 255, 0.95), rgba(243, 242, 255, 1));
}

.advantages__inner {
  text-align: center;
}

.spark {
  width: 38px;
  height: 38px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  background: rgba(59, 44, 255, 0.08);
  border: 1px solid rgba(59, 44, 255, 0.12);
  margin: 0 auto 10px;
}

.advantages__title {
  margin: 0;
  font-size: 40px;
  letter-spacing: -0.03em;
  color: #0f172a;
}

.advantages__sub {
  margin: 10px 0 0;
  font-size: 15px;
  line-height: 1.65;
  color: rgba(51, 65, 85, 0.75);
}

.adv-grid {
  margin-top: 26px;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
}

.adv {
  text-align: left;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
  padding: 18px 18px 16px;
}

.adv__head {
  display: flex;
  align-items: center;
  gap: 12px;
}

.adv__icon {
  width: 34px;
  height: 34px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  background: rgba(59, 44, 255, 0.06);
  border: 1px solid rgba(59, 44, 255, 0.12);
}

.adv__title {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.86);
}

.adv__text {
  margin-top: 10px;
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(51, 65, 85, 0.78);
}

@media (max-width: 1180px) {
  .hero__title { font-size: 56px; }
  .nav { gap: 10px; }
}

@media (max-width: 980px) {
  .hero__grid { grid-template-columns: 1fr; }
  .nav { display: none; }
  .stats { grid-template-columns: 1fr; }
  .adv-grid { grid-template-columns: 1fr; }
  .cube-art { display: none; }
}
    </style>
  </head>
  <body>
    <header class="topbar">
      <div class="container topbar__inner">
        <div class="brand">
          <div class="brand__mark" aria-hidden="true">
            <svg viewBox="0 0 48 48" width="22" height="22" fill="none">
              <defs>
                <linearGradient id="g1" x1="6" y1="6" x2="42" y2="42" gradientUnits="userSpaceOnUse">
                  <stop stop-color="#6B5BFF" />
                  <stop offset="1" stop-color="#2D6BFF" />
                </linearGradient>
              </defs>
              <path d="M24 6c9.94 0 18 8.06 18 18s-8.06 18-18 18c-4.52 0-8.65-1.67-11.8-4.43V10.43A17.92 17.92 0 0 1 24 6Z" fill="url(#g1)"/>
              <path d="M22 14h4.8c5.2 0 9.2 3.7 9.2 10s-4 10-9.2 10H22V14Zm4.6 4H26v12h.6c3.2 0 5.7-2 5.7-6s-2.5-6-5.7-6Z" fill="#fff" opacity="0.9"/>
            </svg>
          </div>
          <div class="brand__text">
            <div class="brand__name">DeAI Nexus Space</div>
          </div>
        </div>

        <nav class="nav" aria-label="Primary">
          <a class="nav__link" href="#">Overview</a>
          <a class="nav__link" href="#">Technology</a>
          <a class="nav__link" href="#">Applications</a>
          <a class="nav__link" href="#">Comparison</a>
          <a class="nav__link" href="#">Tokenomics</a>
          <a class="nav__link" href="#">Value Capture</a>
          <a class="nav__link" href="#">Audit</a>
          <a class="nav__link" href="#">Tools</a>
          <a class="nav__link" href="#">Data</a>
          <a class="nav__link" href="#">Roadshow</a>
          <a class="nav__link" href="#">Roadmap</a>
          <a class="nav__link" href="#">DApp</a>
        </nav>

        <div class="topbar__actions">
          <button class="lang" type="button">
            <span>English</span>
            <svg class="lang__chev" viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
              <path d="M5 7.5L10 12.5L15 7.5" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button class="pledge" type="button">
            <span>Pledge</span>
            <svg class="pledge__icon" viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
              <path d="M8.5 3.5h8v8" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M16.5 3.5l-9 9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              <path d="M7.5 5.5H5.6A2.1 2.1 0 0 0 3.5 7.6v6.8A2.1 2.1 0 0 0 5.6 16.5h6.8a2.1 2.1 0 0 0 2.1-2.1v-1.9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
            </svg>
          </button>
        </div>
      </div>
    </header>

    <main>
      <section class="hero">
        <div class="container hero__grid">
          <div class="hero__left">
            <h1 class="hero__title">
              <span>DeAI Nexus</span>
              <span>The Engine for</span>
              <span>Decentralized AI</span>
            </h1>

            <p class="hero__lead">
              A decentralized infrastructure that runs, calls, and verifies AI models on-chain—<br />
              turning AI from a black-box service into a provable, governable, and composable<br />
              on-chain capability.
            </p>

            <div class="hero__cta">
              <button class="primary" type="button">
                <span>Start</span>
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
                  <path d="M8.5 3.5h8v8" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 3.5l-9 9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M7.5 5.5H5.6A2.1 2.1 0 0 0 3.5 7.6v6.8A2.1 2.1 0 0 0 5.6 16.5h6.8a2.1 2.1 0 0 0 2.1-2.1v-1.9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                </svg>
              </button>
              <button class="linkbtn" type="button">Explore Tech</button>
              <button class="linkbtn" type="button">Tokenomics</button>
              <button class="linkbtn" type="button">Whitepaper</button>
            </div>

            <div class="stats">
              <div class="stat">
                <div class="stat__label">Max daily</div>
                <div class="stat__value">0.80%</div>
              </div>
              <div class="stat">
                <div class="stat__label">Max APY<br />(compounded)</div>
                <div class="stat__value">≈ 885%</div>
              </div>
              <div class="stat">
                <div class="stat__label">High staking ratio</div>
                <div class="stat__value">&gt;95%</div>
              </div>
            </div>

            <div class="hero__disclaimer">
              Return figures are for informational purposes only and do not constitute any promise or financial advice.
            </div>
          </div>

          <div class="hero__right">
            <div class="highlight-card">
              <div class="highlight-card__title">Core Highlights</div>
              <div class="highlight-grid">
                <div class="mini">
                  <div class="mini__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                      <path d="M7 12l3 3 7-7" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                    </svg>
                  </div>
                  <div class="mini__text">
                    <div class="mini__head">Innovative PoDRC<br />+ PoS Hybrid<br />Mining</div>
                  </div>
                </div>
                <div class="mini">
                  <div class="mini__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                      <path d="M12 6v6l4 2" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                      <path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                    </svg>
                  </div>
                  <div class="mini__text">
                    <div class="mini__head">Massive<br />Decentralized<br />Compute<br />Infrastructure</div>
                  </div>
                </div>
                <div class="mini">
                  <div class="mini__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                      <path d="M7 10a5 5 0 0 1 10 0v4a5 5 0 0 1-10 0v-4Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                      <path d="M9 14h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                      <path d="M12 12v4" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                  </div>
                  <div class="mini__text">
                    <div class="mini__head">100%<br />Community-Driven<br />Issuance, No VC</div>
                  </div>
                </div>
                <div class="mini">
                  <div class="mini__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                      <path d="M6 16h12" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                      <path d="M6 12h8" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                      <path d="M6 8h4" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                      <path d="M5 19h14" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25" stroke-linecap="round"/>
                    </svg>
                  </div>
                  <div class="mini__text">
                    <div class="mini__head">Four-Epoch Ladder<br />for Global<br />Consensus</div>
                  </div>
                </div>
              </div>
              <div class="cube-art" aria-hidden="true">
                <div class="cube cube--a"></div>
                <div class="cube cube--b"></div>
                <div class="cube cube--c"></div>
                <div class="cube cube--d"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="container">
          <div class="roadshow">
            <div class="roadshow__left">
              <div class="roadshow__title">Roadshow Video Preview</div>
              <div class="roadshow__sub">A short clip capturing the vibe and key exchanges from the roadshow.</div>
            </div>
            <button class="ghost" type="button">
              <span class="ghost__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                  <path d="M4 8h16" stroke="#475569" stroke-width="1.6" stroke-linecap="round"/>
                  <path d="M8 4v16" stroke="#475569" stroke-width="1.6" stroke-linecap="round"/>
                  <path d="M13 12l6-3v6l-6-3Z" fill="#475569" opacity="0.8"/>
                </svg>
              </span>
              <span>Expand Video</span>
            </button>
          </div>
        </div>
      </section>

      <section class="advantages">
        <div class="container advantages__inner">
          <div class="advantages__badge" aria-hidden="true">
            <div class="spark">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M12 2l1.2 5.2L18 9l-4.8 1.8L12 16l-1.2-5.2L6 9l4.8-1.8L12 2Z" fill="#2D6BFF"/>
              </svg>
            </div>
          </div>
          <h2 class="advantages__title">Project Advantages</h2>
          <p class="advantages__sub">
            Built around verifiability, composability, governance, and security—closing the loop from infrastructure<br />
            to applications.
          </p>

          <div class="adv-grid">
            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" stroke="#2D6BFF" stroke-width="1.6"/>
                    <path d="M9 12l2 2 4-4" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="adv__title">Verifiable Inference</div>
              </div>
              <div class="adv__text">
                Turn inference correctness into proofs—<br />
                reducing black-box risk and replacing<br />
                trust with verification.
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M7 3h10v18H7V3Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.9"/>
                    <path d="M9 7h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M9 11h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M9 15h4" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
                <div class="adv__title">AI as Contract</div>
              </div>
              <div class="adv__text">
                AI is not an external API—it’s a native<br />
                on-chain capability callable by contracts<br />
                and composable by design.
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 3a4 4 0 0 1 4 4v3H8V7a4 4 0 0 1 4-4Z" stroke="#2D6BFF" stroke-width="1.6"/>
                    <path d="M7 10h10v10H7V10Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                    <path d="M12 14v3" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
                <div class="adv__title">Privacy + Trust, Together</div>
              </div>
              <div class="adv__text">
                TEE for trusted execution, ZK for<br />
                correctness—balancing privacy and<br />
                verifiability.
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M4 17h16" stroke="#2D6BFF" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M6 17V11" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 17V7" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M18 17V13" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                  </svg>
                </div>
                <div class="adv__title">Scalable Execution</div>
              </div>
              <div class="adv__text">
                Shard models and schedule in parallel—<br />
                boosting throughput and stability for<br />
                large-model workloads.
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" stroke="#2D6BFF" stroke-width="1.6"/>
                    <path d="M12 8v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 17h.01" stroke="#2D6BFF" stroke-width="2.6" stroke-linecap="round"/>
                  </svg>
                </div>
                <div class="adv__title">Security Foundation</div>
              </div>
              <div class="adv__text">
                Audit-first engineering—surfacing positive<br />
                audit conclusions and a<br />
                remediation-ready posture.
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 2v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 16v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M2 12h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M16 12h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                    <circle cx="12" cy="12" r="3.2" stroke="#2D6BFF" stroke-width="1.6" opacity="0.9"/>
                  </svg>
                </div>
                <div class="adv__title">Ecosystem Interfaces</div>
              </div>
              <div class="adv__text">
                From SDKs to oracles and data layers—<br />
                building blocks for DApps across DeFi,<br />
                GameFi, SocialFi, and more.
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
