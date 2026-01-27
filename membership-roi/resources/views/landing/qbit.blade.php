<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>QBIT - Quantum-Inspired Algorithms</title>
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
    <link rel="icon" type="image/png" href="{{ asset('images/Logo01.png') }}">

    <style>
*,
*::before,
*::after {
  box-sizing: border-box;
}

:root {
  --qb-bg: #0b0b0d;
  --qb-surface: #121216;
  --qb-surface-2: rgba(18, 18, 22, 0.82);
  --qb-text: rgba(255, 255, 255, 0.92);
  --qb-muted: rgba(255, 255, 255, 0.72);
  --qb-muted-2: rgba(255, 255, 255, 0.60);
  --qb-border: rgba(212, 175, 55, 0.22);
  --qb-border-2: rgba(156, 163, 175, 0.22);
  --qb-gold: #d4af37;
  --qb-gold-2: #b8860b;
  --qb-black: #0b0b0d;
  --qb-white: #ffffff;
}

html,
body {
  height: 100%;
}

body {
  margin: 0;
  font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
  color: var(--qb-text);
  background: var(--qb-bg);
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
  background: rgba(11, 11, 13, 0.78);
  backdrop-filter: blur(10px);
  border-bottom: 1px solid var(--qb-border);
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
  width: 120px;
  height: 62px;
  display: grid;
  place-items: center;
}

.brand__name {
  font-weight: 650;
  letter-spacing: -0.02em;
  color: var(--qb-text);
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
  color: var(--qb-muted);
  font-size: 13px;
  padding: 6px 6px;
  border-radius: 10px;
}

.nav__link:hover {
  background: rgba(255, 255, 255, 0.06);
  color: rgba(255, 255, 255, 0.86);
}

.topbar__actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-left: auto;
}

.lang {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 14px;
  border-radius: 999px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  color: var(--qb-text);
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
  background: linear-gradient(135deg, var(--qb-gold), var(--qb-gold-2));
  color: var(--qb-black);
  font-weight: 650;
  font-size: 13px;
  cursor: default;
  box-shadow: 0 12px 26px rgba(0, 0, 0, 0.55);
}

.hero {
  background:
    radial-gradient(900px 520px at 20% 10%, rgba(212, 175, 55, 0.16), transparent 55%),
    radial-gradient(700px 420px at 80% 10%, rgba(156, 163, 175, 0.10), transparent 55%),
    linear-gradient(180deg, rgba(11, 11, 13, 0.92), rgba(11, 11, 13, 1));
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
  color: var(--qb-white);
}

.hero__title span {
  display: block;
}

.hero__lead {
  margin: 18px 0 0;
  font-size: 16px;
  line-height: 1.6;
  color: var(--qb-muted);
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
  background: linear-gradient(135deg, var(--qb-gold), var(--qb-gold-2));
  color: var(--qb-black);
  font-weight: 650;
  font-size: 13px;
  cursor: default;
  box-shadow: 0 12px 26px rgba(0, 0, 0, 0.55);
}

.linkbtn {
  border: none;
  background: transparent;
  color: var(--qb-muted);
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
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.55);
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
  background: radial-gradient(circle at 30% 30%, rgba(212, 175, 55, 0.16), rgba(212, 175, 55, 0));
  border-radius: 999px;
}

.stat__label {
  font-size: 12px;
  color: var(--qb-muted-2);
}

.stat__value {
  margin-top: 10px;
  font-size: 34px;
  font-weight: 750;
  letter-spacing: -0.02em;
  color: var(--qb-white);
}

.hero__disclaimer {
  margin-top: 14px;
  font-size: 12px;
  color: var(--qb-muted-2);
}

.highlight-card__image {
  width: 100%;
  height: auto;
  margin-top: 18px;
  border-radius: 22px;
  display: block;
  animation: qbFloat 6s ease-in-out infinite;
  will-change: transform;
}

@keyframes qbFloat {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-16px); }
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
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
}

.cube--a { right: 0; top: 0; }
.cube--b { right: 52px; top: 34px; }
.cube--c { right: 0; top: 86px; }
.cube--d { right: 52px; top: 120px; }

.roadshowCard {
  margin-top: 22px;
  border-radius: 22px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid rgba(212, 175, 55, 0.18);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  overflow: hidden;
}

.roadshow {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  padding: 16px 18px;
}

.roadshow__divider {
  height: 1px;
  background: rgba(212, 175, 55, 0.14);
}

.roadshow__panel {
  padding: 18px;
}

.roadshow__frame {
  width: 100%;
  aspect-ratio: 16 / 9;
  border: 0;
  border-radius: 18px;
  display: block;
  background: #000;
}

.roadshow__title {
  font-weight: 650;
  color: var(--qb-text);
}

.roadshow__sub {
  margin-top: 4px;
  font-size: 13px;
  color: var(--qb-muted);
}

.ghost {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 999px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border-2);
  color: var(--qb-text);
  font-size: 13px;
  cursor: default;
}

.advantages {
  padding: 60px 0 70px;
  background:
    radial-gradient(900px 520px at 80% 10%, rgba(212, 175, 55, 0.12), transparent 55%),
    radial-gradient(700px 420px at 20% 0%, rgba(156, 163, 175, 0.10), transparent 55%),
    linear-gradient(180deg, rgba(11, 11, 13, 1), rgba(11, 11, 13, 1));
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
  background: rgba(212, 175, 55, 0.10);
  border: 1px solid var(--qb-border);
  margin: 0 auto 10px;
}

.advantages__title {
  margin: 0;
  font-size: 40px;
  letter-spacing: -0.03em;
  color: var(--qb-white);
}

.advantages__sub {
  margin: 10px 0 0;
  font-size: 15px;
  line-height: 1.65;
  color: var(--qb-muted);
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
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
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
  background: rgba(212, 175, 55, 0.08);
  border: 1px solid var(--qb-border);
}

.adv__title {
  font-weight: 650;
  color: var(--qb-text);
}

.adv__text {
  margin-top: 10px;
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--qb-muted);
}

.section {
  padding: 64px 0 72px;
  background:
    radial-gradient(900px 520px at 20% 0%, rgba(212, 175, 55, 0.12), transparent 55%),
    radial-gradient(700px 420px at 85% 20%, rgba(156, 163, 175, 0.10), transparent 55%),
    linear-gradient(180deg, rgba(11, 11, 13, 1), rgba(11, 11, 13, 1));
}

.section__head {
  text-align: center;
}

.section__badge {
  width: 38px;
  height: 38px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  background: rgba(212, 175, 55, 0.10);
  border: 1px solid var(--qb-border);
  margin: 0 auto 10px;
}

.section__title {
  margin: 0;
  font-size: 36px;
  letter-spacing: -0.03em;
  color: var(--qb-white);
}

.section__sub {
  margin: 10px 0 0;
  font-size: 15px;
  line-height: 1.65;
  color: var(--qb-muted);
}

.panel {
  margin-top: 26px;
  border-radius: 18px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  overflow: hidden;
}

.stack {
  display: grid;
  gap: 10px;
  padding: 16px;
}

.acc {
  border-radius: 14px;
  background: rgba(18, 18, 22, 0.90);
  border: 1px solid var(--qb-border-2);
  box-shadow: 0 12px 35px rgba(0, 0, 0, 0.55);
  overflow: hidden;
}

.acc > summary {
  list-style: none;
  cursor: default;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 14px;
  padding: 14px 16px;
}

.acc > summary::-webkit-details-marker {
  display: none;
}

.acc__title {
  font-weight: 650;
  font-size: 13.5px;
  color: var(--qb-text);
}

.acc__meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: var(--qb-muted-2);
  font-size: 12px;
}

.acc__chev {
  width: 18px;
  height: 18px;
  display: inline-grid;
  place-items: center;
  border-radius: 999px;
  border: 1px solid var(--qb-border-2);
  background: rgba(18, 18, 22, 0.82);
}

.acc__body {
  padding: 0 16px 14px;
  color: var(--qb-muted);
  font-size: 13px;
  line-height: 1.6;
}

.acc__body ul {
  margin: 10px 0 0;
  padding-left: 18px;
}

.grid-2 {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  padding: 18px;
}

.card {
  border-radius: 18px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border-2);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  padding: 18px 18px 16px;
}

.card__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}

.card__title {
  font-weight: 650;
  color: var(--qb-text);
}

.card__tag {
  font-size: 12px;
  color: var(--qb-muted-2);
  padding: 6px 10px;
  border-radius: 999px;
  border: 1px solid var(--qb-border-2);
  background: rgba(18, 18, 22, 0.70);
}

.card__text {
  margin-top: 10px;
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--qb-muted);
}

.table-wrap {
  padding: 18px;
  overflow: auto;
}

.tbl {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 13px;
  min-width: 760px;
}

.tbl th,
.tbl td {
  text-align: left;
  padding: 12px 12px;
  border-bottom: 1px solid rgba(212, 175, 55, 0.16);
  color: var(--qb-muted);
}

.tbl th {
  font-weight: 650;
  color: var(--qb-text);
  background: rgba(18, 18, 22, 0.92);
  position: sticky;
  top: 0;
}

.tbl tr:last-child td {
  border-bottom: none;
}

.donut-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  padding: 18px;
}

.donut-card {
  border-radius: 18px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  padding: 18px 18px 16px;
}

.donut-row {
  display: grid;
  grid-template-columns: 130px 1fr;
  gap: 14px;
  align-items: center;
  margin-top: 12px;
}

.donut {
  width: 120px;
  height: 120px;
  border-radius: 999px;
  background: conic-gradient(#2D6BFF 0 76%, #8B5CF6 76% 86%, #22C55E 86% 95%, #F59E0B 95% 100%);
  position: relative;
  box-shadow: 0 18px 45px rgba(0, 0, 0, 0.55);
}

.donut--img {
  background: none;
  overflow: hidden;
}

.donut--img::after {
  display: none;
}

.donut__img {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.donut::after {
  content: "";
  position: absolute;
  inset: 18px;
  background: rgba(18, 18, 22, 0.95);
  border-radius: 999px;
  border: 1px solid rgba(212, 175, 55, 0.18);
}

.donut__label {
  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  z-index: 2;
  font-weight: 750;
  color: var(--qb-text);
  font-size: 13px;
}

.legend {
  margin: 0;
  padding: 0;
  list-style: none;
  display: grid;
  gap: 8px;
}

.legend li {
  display: flex;
  align-items: center;
  gap: 10px;
  color: var(--qb-muted);
  font-size: 13px;
}

.swatch {
  width: 10px;
  height: 10px;
  border-radius: 999px;
}

.swatch--a { background: #2D6BFF; }
.swatch--b { background: #8B5CF6; }
.swatch--c { background: #22C55E; }
.swatch--d { background: #F59E0B; }

.value-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 14px;
  padding: 18px;
}

.mini-card {
  border-radius: 16px;
  background: rgba(18, 18, 22, 0.90);
  border: 1px solid rgba(212, 175, 55, 0.18);
  box-shadow: 0 14px 40px rgba(0, 0, 0, 0.55);
  padding: 14px 14px 12px;
}

.mini-card__title {
  font-weight: 650;
  font-size: 13px;
  color: var(--qb-text);
}

.mini-card__text {
  margin-top: 8px;
  font-size: 12.8px;
  line-height: 1.5;
  color: var(--qb-muted);
}

.tools-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  padding: 18px;
}

.tool {
  border-radius: 18px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid var(--qb-border);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  padding: 18px 18px 16px;
}

.tool__k {
  display: flex;
  align-items: center;
  gap: 10px;
}

.tool__icon {
  width: 34px;
  height: 34px;
  border-radius: 999px;
  display: grid;
  place-items: center;
  background: rgba(212, 175, 55, 0.08);
  border: 1px solid rgba(212, 175, 55, 0.18);
}

.tool__title {
  font-weight: 650;
  color: var(--qb-text);
}

.tool__link {
  margin-top: 8px;
  display: inline-block;
  font-size: 13px;
  color: rgba(212, 175, 55, 0.95);
  text-decoration: none;
}

.tool__link:hover {
  text-decoration: underline;
}

.tool__text {
  margin-top: 8px;
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(51, 65, 85, 0.78);
}

.liquidity-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  padding: 18px;
}

.viz {
  height: 140px;
  border-radius: 14px;
  background:
    radial-gradient(120px 120px at 20% 40%, rgba(45, 107, 255, 0.20), transparent 60%),
    radial-gradient(120px 120px at 70% 30%, rgba(139, 92, 246, 0.16), transparent 60%),
    linear-gradient(180deg, rgba(243, 242, 255, 0.65), rgba(255, 255, 255, 0.65));
  border: 1px solid rgba(212, 175, 55, 0.18);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
  margin-top: 12px;
  position: relative;
  overflow: hidden;
}

.viz__line {
  position: absolute;
  left: 14px;
  right: 14px;
  bottom: 18px;
  height: 64px;
  opacity: 0.85;
}

.video-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  padding: 18px;
}

.video {
  border-radius: 18px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid rgba(212, 175, 55, 0.18);
  box-shadow: 0 18px 55px rgba(0, 0, 0, 0.55);
  overflow: hidden;
}

.video__thumb {
  height: 160px;
  background:
    radial-gradient(240px 180px at 20% 30%, rgba(45, 107, 255, 0.18), transparent 60%),
    radial-gradient(240px 180px at 75% 40%, rgba(139, 92, 246, 0.14), transparent 60%),
    linear-gradient(135deg, rgba(15, 23, 42, 0.04), rgba(15, 23, 42, 0.01));
  position: relative;
}

.video__play {
  position: absolute;
  left: 14px;
  bottom: 14px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  border-radius: 999px;
  background: rgba(18, 18, 22, 0.86);
  border: 1px solid rgba(212, 175, 55, 0.18);
  color: var(--qb-text);
  font-size: 13px;
}

.video__body {
  padding: 14px 16px 16px;
}

.video__title {
  font-weight: 650;
  color: var(--qb-text);
}

.video__sub {
  margin-top: 6px;
  font-size: 13.5px;
  line-height: 1.55;
  color: var(--qb-muted);
}

.roadmap-grid {
  display: grid;
  gap: 22px;
}

.roadmapPanel {
  padding: 0;
  background: transparent;
  border: none;
  box-shadow: none;
}

.rmYearCard {
  background: transparent;
  border: none;
  box-shadow: none;
  padding: 0;
  overflow-x: auto;
}

.rmInner {
  min-width: 980px;
}

.rmYear {
  display: flex;
  justify-content: flex-start;
  font-weight: 750;
  color: var(--qb-text);
  font-size: 18px;
}

.rmRow {
  margin-top: 10px;
  position: relative;
  padding-top: 22px;
}

.rmLine {
  position: absolute;
  left: 0;
  right: 0;
  top: 40px;
  height: 3px;
  border-radius: 999px;
  background: linear-gradient(90deg, rgba(226, 232, 240, 0.95) 0%, rgba(203, 213, 225, 0.65) 55%, rgba(99, 102, 241, 0.85) 100%);
}

.rmMonths {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  align-items: start;
}

.rmM {
  position: relative;
  height: 64px;
}

.rmM::after {
  content: "";
  position: absolute;
  left: 50%;
  top: 26px;
  transform: translateX(-50%);
  width: 8px;
  height: 8px;
  border-radius: 999px;
  background: #e2e8f0;
}

.rmM--label::before {
  content: attr(data-n);
  position: absolute;
  left: 50%;
  top: 0;
  transform: translateX(-50%);
  font-size: 12px;
  color: #94a3b8;
  font-weight: 650;
}

.rmM--active::after {
  width: 12px;
  height: 12px;
  background: #ef4444;
  box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.18);
}

.rmAchGrid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  margin-top: 14px;
}

.rmAchCell {
  text-align: center;
  color: var(--qb-text);
  font-weight: 650;
  font-size: 14px;
  line-height: 1.15;
}

.cta {
  padding: 34px 18px;
}

.cta__panel {
  border-radius: 22px;
  background: rgba(18, 18, 22, 0.82);
  border: 1px solid rgba(212, 175, 55, 0.18);
  box-shadow: 0 22px 65px rgba(0, 0, 0, 0.60);
  padding: 26px 22px;
  text-align: center;
}

.cta__title {
  margin: 0;
  font-size: 28px;
  letter-spacing: -0.03em;
  color: var(--qb-white);
}

.cta__sub {
  margin: 10px auto 0;
  max-width: 760px;
  font-size: 14px;
  line-height: 1.6;
  color: var(--qb-muted);
}

.cta__actions {
  margin-top: 16px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 12px;
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
  .grid-2 { grid-template-columns: 1fr; }
  .donut-grid { grid-template-columns: 1fr; }
  .donut-row { grid-template-columns: 1fr; }
  .value-grid { grid-template-columns: 1fr; }
  .tools-grid { grid-template-columns: 1fr; }
  .liquidity-grid { grid-template-columns: 1fr; }
  .video-grid { grid-template-columns: 1fr; }
  .roadmap-grid { grid-template-columns: 1fr; }
  .cube-art { display: none; }
}
    </style>
  </head>
  <body>
    <header class="topbar">
      <div class="container topbar__inner">
        <div class="brand">
          <div class="brand__mark" aria-hidden="true">
            <img src="{{ asset('images/Logo01.png') }}" alt="" height="62" />
          </div>
        </div>

        <div class="topbar__actions">
          <button class="lang" type="button" onclick="window.location.href='{{ app()->getLocale() === 'zh_CN' ? request()->fullUrlWithQuery(['lang' => 'en']) : request()->fullUrlWithQuery(['lang' => 'zh_CN']) }}'">
            <span>{{ app()->getLocale() === 'zh_CN' ? '简体中文' : 'English' }}</span>
          </button>
          <button class="pledge" type="button" onclick="window.location.href='{{ route('login') }}'">
            <span>Login</span>
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
      @php
        $isZh = app()->getLocale() === 'zh_CN';
        $t = function (string $en, string $zh) use ($isZh): string {
            return $isZh ? $zh : $en;
        };
        $contentLocale = in_array(app()->getLocale(), ['zh', 'zh_CN', 'zh-CN', 'zh_TW', 'zh-TW'], true) ? 'zh-CN' : 'en-US';
        $sdPath = resource_path('content/deainexus_SD_groups.json');
        $sd = null;
        if (file_exists($sdPath)) {
            $sd = json_decode(file_get_contents($sdPath), true);
        }
        $groups = $sd['groups'] ?? [];
        $groupById = [];
        foreach ($groups as $g) {
            if (isset($g['id'])) {
                $groupById[$g['id']] = $g;
            }
        }
        $getChild = function (string $groupId, string $childId) use (&$groupById) {
            $g = $groupById[$groupId] ?? null;
            if (!$g) return null;
            foreach (($g['children'] ?? []) as $c) {
                if (($c['id'] ?? null) === $childId) return $c;
            }
            return null;
        };
        $stripMd = function (?string $s): string {
            $s = (string) $s;
            $s = preg_replace('/`([^`]+)`/', '$1', $s) ?? $s;
            $s = preg_replace('/\*\*([^*]+)\*\*/', '$1', $s) ?? $s;
            $s = preg_replace('/\*([^*]+)\*/', '$1', $s) ?? $s;
            $s = preg_replace('/\[(.*?)\]\((.*?)\)/', '$1', $s) ?? $s;
            $s = preg_replace('/^[-\d.]+\s+/m', '', $s) ?? $s;
            $s = trim(preg_replace('/\s+/', ' ', $s) ?? $s);
            return $s;
        };
        $excerpt = function (?string $md, int $max = 140) use ($stripMd): string {
            $md = (string) $md;
            $line = trim((string) strtok($md, "\n"));
            $txt = $stripMd($line);
            $len = function_exists('mb_strlen') ? mb_strlen($txt) : strlen($txt);
            if ($len > $max) {
                $txt = function_exists('mb_substr') ? mb_substr($txt, 0, $max - 1) : substr($txt, 0, $max - 1);
                $txt = rtrim((string) $txt) . '…';
            }
            return $txt;
        };
        $extractLayerBlocks = function (?string $md) use ($stripMd) {
            $md = (string) $md;
            $parts = preg_split("/\n\n(?=\*\*Layer)/", trim($md)) ?: [];
            $out = [];
            foreach ($parts as $p) {
                $title = null;
                if (preg_match('/^\*\*(Layer\s+\d+:[^*]+)\*\*/', $p, $m)) {
                    $title = $stripMd($m[1]);
                }
                $bullets = [];
                foreach (preg_split("/\n/", $p) ?: [] as $ln) {
                    if (preg_match('/^\-\s+(.*)$/', trim($ln), $bm)) {
                        $bullets[] = $stripMd($bm[1]);
                    }
                }
                if ($title) {
                    $out[] = ['title' => $title, 'bullets' => array_slice($bullets, 0, 3)];
                }
            }
            return $out;
        };
        $parseMarkdownTable = function (?string $md, string $headerNeedle) {
            $md = (string) $md;
            $lines = preg_split("/\r?\n/", $md) ?: [];
            $start = -1;
            for ($i = 0; $i < count($lines); $i++) {
                if (str_contains($lines[$i], $headerNeedle)) {
                    $start = $i;
                    break;
                }
            }
            if ($start < 0) return null;
            $rows = [];
            for ($i = $start; $i < count($lines); $i++) {
                $ln = trim($lines[$i]);
                if ($ln === '' || !str_starts_with($ln, '|')) {
                    if (count($rows) > 0) break;
                    continue;
                }
                if (preg_match('/^\|\s*[-: ]+\|/', $ln)) continue;
                $cells = array_values(array_filter(array_map('trim', explode('|', trim($ln, '|'))), fn ($c) => $c !== ''));
                if (count($cells) >= 2) $rows[] = $cells;
            }
            return $rows ?: null;
        };
      @endphp

      <section class="hero">
        <div class="container hero__grid">
          <div class="hero__left">
            <h1 class="hero__title">
              <span>{{ $t('Quantum Genesis', '量子创世') }}</span>
              <span>{{ $t('The Next Innovation', '下一次创新') }}</span>
            </h1>

            <p class="hero__lead">
              {{ $t(
                "QBIT is driven by Quantum-Inspired Algorithms. By running simulated QAOA and Quantum Annealing on elite computing clusters (Google Willow, NVIDIA, IBM), we unlock superior computational speed today. We don't wait for the quantum future—we apply its efficiency now to solve complex arbitrage routing problems instantly.",
                "QBIT 由量子启发算法驱动。通过在顶级算力集群（Google Willow、NVIDIA、IBM）上运行模拟 QAOA 与量子退火，我们在当下就释放更强的计算速度。我们不等待量子未来——而是把它的效率现在就用起来，瞬间解决复杂的套利路由问题。"
              ) }}
            </p>

            <div class="hero__cta">
              <button class="primary" type="button" onclick="window.location.href='{{ route('login') }}'">
                <span>Login</span>
                <svg viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
                  <path d="M8.5 3.5h8v8" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M16.5 3.5l-9 9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M7.5 5.5H5.6A2.1 2.1 0 0 0 3.5 7.6v6.8A2.1 2.1 0 0 0 5.6 16.5h6.8a2.1 2.1 0 0 0 2.1-2.1v-1.9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                </svg>
              </button>
              <button class="linkbtn" type="button">{{ $t('Explore Tech', '探索技术') }}</button>
              <button class="linkbtn" type="button">{{ $t('Tokenomics', '代币经济') }}</button>
              <button class="linkbtn" type="button">{{ $t('Whitepaper', '白皮书') }}</button>
            </div>

            <div class="hero__disclaimer">
            </div>
          </div>

          <div class="hero__right">
            <img class="highlight-card__image" src="{{ asset('images/Image01.jpg') }}" alt="" />
          </div>
        </div>

        <div class="container">
          <div class="roadshowCard" id="roadshowCard">
            <div class="roadshow">
              <div class="roadshow__left">
                <div class="roadshow__title">{{ $t('Quantum Intro Videos', '量子介绍视频') }}</div>
              </div>
              <button class="ghost" type="button" id="roadshowToggle">
                <span class="ghost__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                    <path d="M4 8h16" stroke="#475569" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M8 4v16" stroke="#475569" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M13 12l6-3v6l-6-3Z" fill="#475569" opacity="0.8"/>
                  </svg>
                </span>
                <span id="roadshowToggleLabel">{{ $t('Expand Video', '展开视频') }}</span>
              </button>
            </div>
            <div class="roadshow__divider"></div>
            <div class="roadshow__panel" id="roadshowPanel" hidden>
              <iframe
                class="roadshow__frame"
                id="roadshowFrame"
                title="Roadshow"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen
                data-src="https://www.youtube-nocookie.com/embed/RQWpF2Gb-gU?rel=0&modestbranding=1"
              ></iframe>
            </div>
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
          <h2 class="advantages__title">{{ $t('Quantum Genesis Advantages', '量子创世优势') }}</h2>
          <p class="advantages__sub"></p>

          <div class="adv-grid">
            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M13 2L4 14h7l-1 8 10-14h-7l0-6Z" stroke="var(--qb-gold)" stroke-width="1.8" stroke-linejoin="round"/>
                    <path d="M4 18h6" stroke="var(--qb-gold)" stroke-width="1.6" stroke-linecap="round" opacity="0.55"/>
                  </svg>
                </div>
                <div class="adv__title">{{ $t('Speed', '速度') }}</div>
              </div>
              <div class="adv__text">
                {{ $t('Extreme Velocity (Millisecond execution)', '极致速度（毫秒级执行）') }}
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" stroke="var(--qb-gold)" stroke-width="1.6"/>
                    <path d="M9.5 12.2l1.7 1.7 3.6-3.9" stroke="var(--qb-gold)" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <div class="adv__title">{{ $t('Security', '安全') }}</div>
              </div>
              <div class="adv__text">
                {{ $t('Post-Quantum Shield (Future-proof asset protection)', '后量子护盾（面向未来的资产保护）') }}
              </div>
            </div>

            <div class="adv">
              <div class="adv__head">
                <div class="adv__icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
                    <path d="M12 20c4.2 0 7-2.8 7-6.5S16.2 7 12 7 5 9.8 5 13.5 7.8 20 12 20Z" stroke="var(--qb-gold)" stroke-width="1.6"/>
                    <path d="M12 4v3" stroke="var(--qb-gold)" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M9 13.5h6" stroke="var(--qb-gold)" stroke-width="1.8" stroke-linecap="round"/>
                    <path d="M12 10.5v6" stroke="var(--qb-gold)" stroke-width="1.8" stroke-linecap="round" opacity="0.65"/>
                  </svg>
                </div>
                <div class="adv__title">{{ $t('Evolution', '演化') }}</div>
              </div>
              <div class="adv__text">
                {{ $t('Adaptive Intelligence (AI + Quantum-inspired models)', '自适应智能（AI + 量子启发模型）') }}
              </div>
            </div>

          </div>
        </div>
      </section>

      @php
        $fiveLayer = $getChild('tech_features', 'five_layer_architecture');
        $layers = $extractLayerBlocks(($fiveLayer['content'][$contentLocale] ?? null));

        $appsWanted = ['parrot_v1', 'one_click_deployment'];
        $apps = [];
        foreach ($appsWanted as $aid) {
            $c = $getChild('project_applications', $aid);
            if ($c) $apps[] = $c;
        }

        $techMetrics = $getChild('tech_features', 'tech_metrics');
        $perfRows = $parseMarkdownTable(($techMetrics['content'][$contentLocale] ?? ''), '| Performance Metric |') ?? [];
        if (!$perfRows) {
            $perfRows = $parseMarkdownTable(($techMetrics['content'][$contentLocale] ?? ''), '| 性能指标 |') ?? [];
        }

        $economics = $getChild('economics_model', 'economics_model');
        $allocRows = $parseMarkdownTable(($economics['content'][$contentLocale] ?? ''), '| Category |') ?? [];
        if (!$allocRows) {
            $allocRows = $parseMarkdownTable(($economics['content'][$contentLocale] ?? ''), '| 类别 |') ?? [];
        }

        $audit = $getChild('code_audit', 'security_audit_report') ?: $getChild('code_audit', 'code_audit');
        $auditMd = (string) (($audit['content'][$contentLocale] ?? '') ?: '');
        preg_match_all('/^\-\s*✅\s*(.+)$/m', $auditMd, $mOk);
        $auditChecks = array_slice(array_map($stripMd, $mOk[1] ?? []), 0, 6);
        if (!$auditChecks) {
            preg_match_all('/^-\s*✅\s*(.+)$/m', $auditMd, $mOk2);
            $auditChecks = array_slice(array_map($stripMd, $mOk2[1] ?? []), 0, 6);
        }

        $resources = $getChild('related_resources', 'related_resources');
        $resourceMd = (string) (($resources['content']['en-US'] ?? '') ?: ($resources['content']['zh-CN'] ?? ''));
        preg_match_all('/^\d+\.\s+([^:]+):\s+(https?:\/\/\S+)/m', $resourceMd, $mApps);
        $resourceApps = [];
        for ($i = 0; $i < count($mApps[0] ?? []); $i++) {
            $resourceApps[] = ['name' => trim($mApps[1][$i]), 'url' => trim($mApps[2][$i])];
        }
        $resourceApps = array_slice($resourceApps, 0, 6);

        $roadmap = $getChild('roadmap', 'roadmap');
        $roadmapMd = (string) (($roadmap['content'][$contentLocale] ?? '') ?: '');
        $roadmapYears = [];
        $current = null;
        foreach (preg_split("/\r?\n/", $roadmapMd) ?: [] as $ln) {
            $ln = trim($ln);
            if ($ln === '') continue;
            if (preg_match('/^\*\*(\d{4}).*?\*\*:?$/', $ln, $ym)) {
                $current = $ym[1];
                $roadmapYears[$current] = [];
                continue;
            }
            if ($current && preg_match('/^\-\s+(.*)$/', $ln, $bm)) {
                $roadmapYears[$current][] = $stripMd($bm[1]);
            }
        }
      @endphp

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M12 2l1.2 5.2L18 9l-4.8 1.8L12 16l-1.2-5.2L6 9l4.8-1.8L12 2Z" fill="#2D6BFF"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Technology Path: Hybrid Quantum Architecture', '技术路径：混合量子架构') }}</h2>
            <p class="section__sub">{{ $t('Bridging the gap between Classic AI reliability and Quantum potential.', '连接经典 AI 的可靠性与量子潜力。') }}</p>
          </div>

          <div class="panel">
            <div class="stack">
              @for ($i = 0; $i < 3; $i++)
                <details class="acc" @if ($i === 0) open @endif>
                  <summary>
                    <span class="acc__title">
                      @if ($i === 0)
                        {{ $t('Layer 1: Application Layer', '第一层：应用层') }}
                      @elseif ($i === 1)
                        {{ $t('Layer 2: QBIT Middleware (The Core)', '第二层：QBIT 中间件（核心）') }}
                      @elseif ($i === 2)
                        {{ $t('Layer 3: Computational Layer (IBM/GPU)', '第三层：计算层（IBM/GPU）') }}
                      @endif
                    </span>
                    <span class="acc__meta">
                      <span class="acc__chev" aria-hidden="true">
                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none">
                          <path d="M5 7.5L10 12.5L15 7.5" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </span>
                    </span>
                  </summary>
                  <div class="acc__body"></div>
                </details>
              @endfor
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M4 12a8 8 0 1 0 16 0A8 8 0 0 0 4 12Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.35"/>
                <path d="M12 7v5l3 2" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Core Applications', '核心应用') }}</h2>
            <p class="section__sub">{{ $t('Built on infrastructure primitives—models, deployment, security, and AI-native finance.', '基于基础设施原语构建——模型、部署、安全与 AI 原生金融。') }}</p>
          </div>

          <div class="panel">
            <div class="grid-2">
              @foreach ($apps as $app)
                <div class="card">
                  <div class="card__top">
                    <div class="card__title">
                      @if (($app['id'] ?? null) === 'parrot_v1')
                        FinTech - Portfolio Optimization
                      @elseif (($app['id'] ?? null) === 'one_click_deployment')
                        PoDV Node: Auto-Data Execution
                      @else
                        {{ $app['title'][$contentLocale] ?? $app['title']['en-US'] ?? '' }}
                      @endif
                    </div>
                  </div>
                  <div class="card__text">
                    @if (($app['id'] ?? null) === 'parrot_v1')
                      Grounded in Markowitz Portfolio Theory, our hybrid quantum-classical model is powered by QAOA. We map complex optimization challenges to quantum ground state searches, using quantum parallelism to crack NP-hard problems. This allows us to navigate exponentially vast configuration spaces and instantly locate the optimal risk-return portfolio, revolutionizing the efficiency of large-scale asset allocation.
                    @elseif (($app['id'] ?? null) === 'one_click_deployment')
                      Turn your node into an AI-driven data oracle. Utilizing the Proof of Data Value (PoDV) protocol, nodes automatically execute complex data scraping and cleaning tasks. This continuous stream of financial data fuels QBIT's quantum algorithms, converting raw data value directly into node yield.
                    @else
                      {{ $excerpt($app['content'][$contentLocale] ?? '') }}
                    @endif
                  </div>
                  @if (($app['id'] ?? null) === 'parrot_v1')
                    <div style="margin-top: 12px;">
                      <button class="ghost" type="button" onclick="window.location.href='{{ route('login') }}'">Try Now</button>
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" stroke="#2D6BFF" stroke-width="1.6"/>
                <path d="M9 12l2 2 4-4" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Tokenomics', '代币经济') }}</h2>
            <p class="section__sub">{!! $t('A symbiotic ecosystem separating <strong>(QBT)</strong> and <strong>(QBTX)</strong>. Featuring an elastic supply of 150M and a $9M FDV, protected by our industry-first Break-even Shield.', '一种将<strong>（QBT）</strong>与<strong>（QBTX）</strong>分离的共生生态系统。具备 1.5 亿弹性供应与 900 万美元 FDV，并由行业首创的盈亏平衡护盾保护。') !!}</p>
          </div>

          <div class="panel">
            <div class="donut-grid">
              <div class="donut-card">
                <div class="card__top">
                  <div class="card__title">QBT Governance &amp; Equity</div>
                  <div class="card__tag">{{ $t('esToken', 'esToken') }}</div>
                </div>
                <div class="donut-row">
                  <div class="donut donut--img" aria-hidden="true">
                    <img class="donut__img" src="{{ asset('images/QBT.png') }}" alt="" />
                  </div>
                  <ul class="legend">
                    <li><span class="swatch swatch--a"></span>Nature: Non-transferable Equity Proof. Holding QBT = Holding Pre-IPO Shares.</li>
                    <li><span class="swatch swatch--b"></span>Privilege: Exclusive voting rights and access to Phase 2 ecosystem yields.</li>
                    <li><span class="swatch swatch--c"></span>Vesting: Convertible 1:1 to QBTX via vesting mechanisms.</li>
                  </ul>
                </div>
              </div>

              <div class="donut-card">
                <div class="card__top">
                  <div class="card__title">QBTX Universal Utility</div>
                  <div class="card__tag">{{ $t('Universal Fuel', 'Universal Fuel') }}</div>
                </div>
                <div class="card__text"></div>
                <div class="donut-row">
                  <div class="donut donut--img" aria-hidden="true">
                    <img class="donut__img" src="{{ asset('images/QBTX.png') }}" alt="" />
                  </div>
                  <ul class="legend">
                    <li><span class="swatch swatch--a"></span>Nature: Fully liquid asset traded on CEX/DEX.</li>
                    <li><span class="swatch swatch--b"></span>Utility: Used for Gas fees, enterprise payments, and deflationary burns.</li>
                    <li><span class="swatch swatch--c"></span>Evolution: Launches on BSC (H1 2026) → Migrates to QBIT Native Chain (2027).</li>
                    <li><span class="swatch swatch--d"></span>Value Anchor: BNB Price Floor Support.</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M12 2v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 16v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M2 12h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M16 12h6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <circle cx="12" cy="12" r="3.2" stroke="#2D6BFF" stroke-width="1.6" opacity="0.9"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Six Value-Capture Mechanisms', '六大价值捕获机制') }}</h2>
            <p class="section__sub">{{ $t('Designed to align infrastructure growth with sustainable ecosystem value.', '用于将基础设施增长与可持续生态价值对齐。') }}</p>
          </div>

          <div class="panel">
            <div class="value-grid">
              <div class="mini-card">
                <div class="mini-card__title">BNB-Anchored Treasury</div>
                <div class="mini-card__text">Protocol revenue (Node sales, Gas, Fees) automatically buys back BNB. As the reserve grows, it mathematically raises the Floor Price of QBTX, eliminating the risk of the token going to zero.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">"Gold Shovel" Yield</div>
                <div class="mini-card__text">Holding QBT grants you network "taxing rights." Stakers directly share QaaS commercial profits and receive exclusive airdrops from every DeFi or GameFi project launching on the QBIT chain.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Deflationary Scissors</div>
                <div class="mini-card__text">A dual-burn engine driven by usage. High-frequency Gas consumption combined with Elastic Vesting friction continuously reduces supply. Higher activity = Scarcity = Price Surge.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Quantum Narrative</div>
                <div class="mini-card__text">Positioned at the intersection of AI, Quantum, and RWA. We solve high-frequency settlement bottlenecks, capturing the valuation premium of a trillion-dollar "commercial speed revolution."</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Institutional Moat</div>
                <div class="mini-card__text">Deep liquidity pools anchored by BNB/USDT and managed by top-tier Market Makers. We provide an institutional-grade entry channel with minimal slippage, resistant to extreme market volatility.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Proof of Elite (PoE)</div>
                <div class="mini-card__text">QBT is your ticket to the inner circle—granting access to Alpha Data, core governance, and the DAO. We filter out speculators to unite a community of true builders and believers.</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M6 12h12" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 6v12" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M5 19h14" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25" stroke-linecap="round"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Community Tools', '社区工具') }}</h2>
            <p class="section__sub">{{ $t('Official apps and community entry points.', '官方应用与社区入口。') }}</p>
          </div>

          <div class="panel">
            <div class="tools-grid">
              @foreach ($resourceApps as $idx => $ra)
                <div class="tool">
                  <div class="tool__k">
                    <div class="tool__icon" aria-hidden="true">
                      <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                        <path d="M7 12l3 3 7-7" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.5 12a7.5 7.5 0 1 0 15 0 7.5 7.5 0 0 0-15 0Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                      </svg>
                    </div>
                    <div class="tool__title">{{ $ra['name'] }}</div>
                  </div>
                  <a class="tool__link" href="{{ $ra['url'] }}" target="_blank" rel="noopener noreferrer">{{ $ra['url'] }}</a>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M4 17h16" stroke="#2D6BFF" stroke-width="1.6" stroke-linecap="round"/>
                <path d="M6 17V11" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 17V7" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M18 17V13" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Liquidity &amp; Holders', '流动性与持仓') }}</h2>
            <p class="section__sub">{{ $t('On-chain visibility into liquidity conditions and distribution context.', '链上可视化的流动性状况与分布背景。') }}</p>
          </div>

          <div class="panel">
            <div class="liquidity-grid">
              <div class="card">
                <div class="card__top">
                  <div class="card__title">{{ $t('Liquidity', '流动性') }}</div>
                  <div class="card__tag">{{ $t('Pool', '池子') }}</div>
                </div>
                <div class="card__text">{{ $t('A simplified view of liquidity conditions and market stability signals.', '流动性状况与市场稳定信号的简化视图。') }}</div>
                <div class="viz" aria-hidden="true">
                  <svg class="viz__line" viewBox="0 0 200 80" fill="none">
                    <path d="M4 62 C28 55, 36 18, 64 26 C92 34, 108 70, 140 46 C160 32, 176 36, 196 22" stroke="#2D6BFF" stroke-width="3" stroke-linecap="round"/>
                    <path d="M4 62 C28 55, 36 18, 64 26 C92 34, 108 70, 140 46 C160 32, 176 36, 196 22" stroke="#8B5CF6" stroke-width="3" stroke-linecap="round" opacity="0.35"/>
                  </svg>
                </div>
              </div>

              <div class="card">
                <div class="card__top">
                  <div class="card__title">{{ $t('Holders', '持仓') }}</div>
                  <div class="card__tag">{{ $t('Context', '背景') }}</div>
                </div>
                <div class="card__text">
                  {{ $t('Top holder addresses include infrastructure contracts and liquidity pool addresses, supporting fairness and decentralization of distribution.', '前几大持仓地址包含基础设施合约与流动性池地址，有助于保障分配公平与去中心化。') }}
                </div>
                <div class="viz" aria-hidden="true">
                  <svg class="viz__line" viewBox="0 0 200 80" fill="none">
                    <circle cx="40" cy="52" r="10" fill="#2D6BFF" opacity="0.25"/>
                    <circle cx="78" cy="38" r="14" fill="#8B5CF6" opacity="0.22"/>
                    <circle cx="116" cy="46" r="9" fill="#22C55E" opacity="0.22"/>
                    <circle cx="152" cy="30" r="16" fill="#2D6BFF" opacity="0.16"/>
                    <circle cx="180" cy="46" r="8" fill="#F59E0B" opacity="0.20"/>
                  </svg>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M4 8h16" stroke="#2D6BFF" stroke-width="1.6" stroke-linecap="round"/>
                <path d="M8 4v16" stroke="#2D6BFF" stroke-width="1.6" stroke-linecap="round"/>
                <path d="M13 12l6-3v6l-6-3Z" fill="#2D6BFF" opacity="0.65"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Roadshow', '路演') }}</h2>
            <p class="section__sub">{{ $t('Highlights from recent community and ecosystem events.', '近期社区与生态活动精彩回顾。') }}</p>
          </div>

          <div class="panel">
            <div class="video-grid">
              <div class="video">
                <div class="video__thumb">
                  <div class="video__play">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" aria-hidden="true">
                      <path d="M10 8l7 4-7 4V8Z" fill="#2D6BFF"/>
                      <circle cx="12" cy="12" r="9" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                    </svg>
                    Play
                  </div>
                </div>
                <div class="video__body">
                  <div class="video__title">{{ $t('Bangkok Roadshow', '曼谷路演') }}</div>
                  <div class="video__sub">{{ $t('A short clip capturing the vibe and key exchanges from the roadshow.', '一段短片记录路演现场氛围与关键交流。') }}</div>
                </div>
              </div>

              <div class="video">
                <div class="video__thumb">
                  <div class="video__play">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none" aria-hidden="true">
                      <path d="M10 8l7 4-7 4V8Z" fill="#2D6BFF"/>
                      <circle cx="12" cy="12" r="9" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25"/>
                    </svg>
                    Play
                  </div>
                </div>
                <div class="video__body">
                  <div class="video__title">{{ $t('Singapore Roadshow', '新加坡路演') }}</div>
                  <div class="video__sub">{{ $t('Community meetups, partner conversations, and product demos in the field.', '社区交流、合作伙伴对话与现场产品演示。') }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="section">
        <div class="container">
          <div class="section__head">
            <div class="section__badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" width="16" height="16" fill="none">
                <path d="M12 2l1.2 5.2L18 9l-4.8 1.8L12 16l-1.2-5.2L6 9l4.8-1.8L12 2Z" fill="#2D6BFF"/>
              </svg>
            </div>
            <h2 class="section__title">{{ $t('Roadmap', '路线图') }}</h2>
            <p class="section__sub">{{ $t('A month-based timeline showing momentum from the earliest milestones to what’s next.', '按月份展示从最早里程碑到下一阶段的推进轨迹。') }}</p>
          </div>

          <div class="panel roadmapPanel">
            <div class="roadmap-grid">
              <div class="rmYearCard">
                <div class="rmInner">
                  <div class="rmYear">2026</div>
                  <div class="rmRow">
                    <div class="rmLine" aria-hidden="true"></div>
                    <div class="rmMonths" aria-hidden="true">
                      <div class="rmM rmM--label rmM--active" data-n="1"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="3"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="6"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="9"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="12"></div>
                    </div>
                  </div>
                  <div class="rmAchGrid">
                    <div class="rmAchCell" style="grid-column: 3 / span 1;">Global AI<br>Summit</div>
                    <div class="rmAchCell" style="grid-column: 6 / span 1;">Testnet 1<br>Launch</div>
                    <div class="rmAchCell" style="grid-column: 9 / span 1;">Epoch III<br>(Deflation<br>Phase)</div>
                    <div class="rmAchCell" style="grid-column: 12 / span 1;">Mainnet<br>Launch</div>
                  </div>
                </div>
              </div>

              <div class="rmYearCard">
                <div class="rmInner">
                  <div class="rmYear">2027</div>
                  <div class="rmRow">
                    <div class="rmLine" aria-hidden="true"></div>
                    <div class="rmMonths" aria-hidden="true">
                      <div class="rmM rmM--label" data-n="1"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="3"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="6"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="9"></div>
                      <div class="rmM"></div>
                      <div class="rmM"></div>
                      <div class="rmM rmM--label" data-n="12"></div>
                    </div>
                  </div>
                  <div class="rmAchGrid"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="cta">
            <div class="cta__panel">
              <h3 class="cta__title">{{ $t('Start Your Quantum Journey', '开启你的量子之旅') }}</h3>
              <p class="cta__sub">
                {{ $t('Deploy nodes, validate data via PoDV, and capture institutional-grade yields. Join the revolution where quantum speed meets algorithmic safety.', '部署节点，通过 PoDV 验证数据并捕获机构级收益。加入量子速度与算法安全交汇的革命。') }}
              </p>
              <div class="cta__actions">
                <button class="primary" type="button" onclick="window.location.href='{{ route('login') }}'">
                  <span>Login</span>
                  <svg viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
                    <path d="M8.5 3.5h8v8" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M16.5 3.5l-9 9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7.5 5.5H5.6A2.1 2.1 0 0 0 3.5 7.6v6.8A2.1 2.1 0 0 0 5.6 16.5h6.8a2.1 2.1 0 0 0 2.1-2.1v-1.9" stroke="#ffffff" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" opacity="0.9"/>
                  </svg>
                </button>
                <button class="ghost" type="button">
                  <span>{{ $t('Whitepaper', '白皮书') }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <script>
      (function () {
        const toggle = document.getElementById('roadshowToggle');
        const panel = document.getElementById('roadshowPanel');
        const frame = document.getElementById('roadshowFrame');
        const label = document.getElementById('roadshowToggleLabel');
        if (!toggle || !panel || !frame || !label) return;

        const textExpand = @json($t('Expand Video', '展开视频'));
        const textCollapse = @json($t('Collapse Video', '收起视频'));

        function setOpen(isOpen) {
          if (isOpen) {
            panel.hidden = false;
            label.textContent = textCollapse;
            if (!frame.src) {
              frame.src = frame.getAttribute('data-src') || '';
            }
          } else {
            panel.hidden = true;
            label.textContent = textExpand;
          }
        }

        toggle.addEventListener('click', function () {
          setOpen(panel.hidden);
        });
      })();
    </script>
  </body>
</html>
