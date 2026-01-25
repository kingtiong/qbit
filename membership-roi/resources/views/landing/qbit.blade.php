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
  width: 120px;
  height: 62px;
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
  margin-left: auto;
}

.langwrap {
  position: relative;
  display: inline-flex;
}

.langmenu {
  position: absolute;
  right: 0;
  top: calc(100% + 8px);
  min-width: 160px;
  padding: 6px;
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.96);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.10);
  z-index: 30;
}

.langitem {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  border-radius: 12px;
  text-decoration: none;
  color: rgba(15, 23, 42, 0.82);
  font-size: 13px;
  white-space: nowrap;
}

.langitem:hover {
  background: rgba(15, 23, 42, 0.04);
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

.section {
  padding: 64px 0 72px;
  background:
    radial-gradient(900px 520px at 20% 0%, rgba(122, 106, 255, 0.12), transparent 55%),
    radial-gradient(700px 420px at 85% 20%, rgba(164, 119, 255, 0.10), transparent 55%),
    linear-gradient(180deg, rgba(243, 242, 255, 1), rgba(243, 242, 255, 0.96));
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
  background: rgba(59, 44, 255, 0.08);
  border: 1px solid rgba(59, 44, 255, 0.12);
  margin: 0 auto 10px;
}

.section__title {
  margin: 0;
  font-size: 36px;
  letter-spacing: -0.03em;
  color: #0f172a;
}

.section__sub {
  margin: 10px 0 0;
  font-size: 15px;
  line-height: 1.65;
  color: rgba(51, 65, 85, 0.75);
}

.panel {
  margin-top: 26px;
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
  overflow: hidden;
}

.stack {
  display: grid;
  gap: 10px;
  padding: 16px;
}

.acc {
  border-radius: 14px;
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 12px 35px rgba(15, 23, 42, 0.05);
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
  color: rgba(15, 23, 42, 0.86);
}

.acc__meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  color: rgba(51, 65, 85, 0.65);
  font-size: 12px;
}

.acc__chev {
  width: 18px;
  height: 18px;
  display: inline-grid;
  place-items: center;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.28);
  background: rgba(255, 255, 255, 0.75);
}

.acc__body {
  padding: 0 16px 14px;
  color: rgba(51, 65, 85, 0.78);
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
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
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
  color: rgba(15, 23, 42, 0.88);
}

.card__tag {
  font-size: 12px;
  color: rgba(51, 65, 85, 0.65);
  padding: 6px 10px;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.28);
  background: rgba(255, 255, 255, 0.65);
}

.card__text {
  margin-top: 10px;
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(51, 65, 85, 0.78);
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
  border-bottom: 1px solid rgba(148, 163, 184, 0.22);
  color: rgba(51, 65, 85, 0.82);
}

.tbl th {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.86);
  background: rgba(243, 242, 255, 0.65);
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
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
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
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.06);
}

.donut::after {
  content: "";
  position: absolute;
  inset: 18px;
  background: rgba(255, 255, 255, 0.95);
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.20);
}

.donut__label {
  position: absolute;
  inset: 0;
  display: grid;
  place-items: center;
  z-index: 2;
  font-weight: 750;
  color: rgba(15, 23, 42, 0.90);
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
  color: rgba(51, 65, 85, 0.78);
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
  grid-template-columns: repeat(5, 1fr);
  gap: 14px;
  padding: 18px;
}

.mini-card {
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.85);
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: 0 14px 40px rgba(15, 23, 42, 0.05);
  padding: 14px 14px 12px;
}

.mini-card__title {
  font-weight: 650;
  font-size: 13px;
  color: rgba(15, 23, 42, 0.86);
}

.mini-card__text {
  margin-top: 8px;
  font-size: 12.8px;
  line-height: 1.5;
  color: rgba(51, 65, 85, 0.78);
}

.tools-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  padding: 18px;
}

.tool {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
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
  background: rgba(59, 44, 255, 0.06);
  border: 1px solid rgba(59, 44, 255, 0.12);
}

.tool__title {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.88);
}

.tool__link {
  margin-top: 8px;
  display: inline-block;
  font-size: 13px;
  color: rgba(45, 107, 255, 0.96);
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
  border: 1px solid rgba(148, 163, 184, 0.22);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.55);
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
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
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
  background: rgba(255, 255, 255, 0.86);
  border: 1px solid rgba(148, 163, 184, 0.35);
  color: rgba(51, 65, 85, 0.82);
  font-size: 13px;
}

.video__body {
  padding: 14px 16px 16px;
}

.video__title {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.88);
}

.video__sub {
  margin-top: 6px;
  font-size: 13.5px;
  line-height: 1.55;
  color: rgba(51, 65, 85, 0.78);
}

.roadmap-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 18px;
  padding: 18px;
}

.timeline-card {
  border-radius: 18px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 18px 55px rgba(15, 23, 42, 0.06);
  padding: 18px 18px 16px;
}

.timeline-card__title {
  font-weight: 650;
  color: rgba(15, 23, 42, 0.88);
}

.timeline {
  margin-top: 10px;
  padding-left: 16px;
  color: rgba(51, 65, 85, 0.78);
  font-size: 13.5px;
  line-height: 1.6;
}

.cta {
  padding: 34px 18px;
}

.cta__panel {
  border-radius: 22px;
  background: rgba(255, 255, 255, 0.78);
  border: 1px solid rgba(148, 163, 184, 0.28);
  box-shadow: 0 22px 65px rgba(15, 23, 42, 0.08);
  padding: 26px 22px;
  text-align: center;
}

.cta__title {
  margin: 0;
  font-size: 28px;
  letter-spacing: -0.03em;
  color: rgba(15, 23, 42, 0.90);
}

.cta__sub {
  margin: 10px auto 0;
  max-width: 760px;
  font-size: 14px;
  line-height: 1.6;
  color: rgba(51, 65, 85, 0.78);
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
          <div class="langwrap">
            <button class="lang" type="button" id="langBtn" aria-haspopup="menu" aria-expanded="false">
              <span>{{ app()->getLocale() === 'zh_CN' ? '简体中文' : 'English' }}</span>
              <svg class="lang__chev" viewBox="0 0 20 20" width="16" height="16" fill="none" aria-hidden="true">
                <path d="M5 7.5L10 12.5L15 7.5" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </button>
            <div class="langmenu" id="langMenu" role="menu" aria-label="Language" hidden>
              <a class="langitem" role="menuitem" href="{{ request()->fullUrlWithQuery(['lang' => 'en']) }}">English</a>
              <a class="langitem" role="menuitem" href="{{ request()->fullUrlWithQuery(['lang' => 'zh_CN']) }}">简体中文</a>
            </div>
          </div>
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
              <button class="primary" type="button" onclick="window.location.href='{{ route('login') }}'">
                <span>Login</span>
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

      @php
        $fiveLayer = $getChild('tech_features', 'five_layer_architecture');
        $layers = $extractLayerBlocks(($fiveLayer['content'][$contentLocale] ?? null));

        $appsWanted = ['parrot_v1', 'one_click_deployment', 'ai_audit', 'nexus_finance'];
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
            <h2 class="section__title">Tech Path: Five-Layer Architecture</h2>
            <p class="section__sub">A modular stack designed for verifiable on-chain AI—from chain runtime to ecosystem.</p>
          </div>

          <div class="panel">
            <div class="stack">
              @foreach ($layers as $i => $layer)
                <details class="acc" @if ($i === 0) open @endif>
                  <summary>
                    <span class="acc__title">{{ $layer['title'] }}</span>
                    <span class="acc__meta">
                      <span class="acc__chev" aria-hidden="true">
                        <svg viewBox="0 0 20 20" width="14" height="14" fill="none">
                          <path d="M5 7.5L10 12.5L15 7.5" stroke="#475569" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                      </span>
                    </span>
                  </summary>
                  <div class="acc__body">
                    @if (!empty($layer['bullets']))
                      <ul>
                        @foreach ($layer['bullets'] as $b)
                          <li>{{ $b }}</li>
                        @endforeach
                      </ul>
                    @endif
                  </div>
                </details>
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
                <path d="M4 12a8 8 0 1 0 16 0A8 8 0 0 0 4 12Z" stroke="#2D6BFF" stroke-width="1.6" opacity="0.35"/>
                <path d="M12 7v5l3 2" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </div>
            <h2 class="section__title">Core Applications</h2>
            <p class="section__sub">Built on infrastructure primitives—models, deployment, security, and AI-native finance.</p>
          </div>

          <div class="panel">
            <div class="grid-2">
              @foreach ($apps as $app)
                <div class="card">
                  <div class="card__top">
                    <div class="card__title">{{ $app['title'][$contentLocale] ?? $app['title']['en-US'] ?? '' }}</div>
                    <div class="card__tag">App</div>
                  </div>
                  <div class="card__text">{{ $excerpt($app['content'][$contentLocale] ?? '') }}</div>
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
            <h2 class="section__title">Key Capability Comparison</h2>
            <p class="section__sub">A snapshot of core performance data and technical comparison.</p>
          </div>

          <div class="panel">
            <div class="table-wrap">
              <table class="tbl">
                <thead>
                  <tr>
                    <th>Performance Metric</th>
                    <th>Data</th>
                    <th>Comparative Advantage</th>
                    <th>Technical Implementation</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach (array_slice($perfRows, 1, 8) as $r)
                    <tr>
                      <td>{{ $stripMd($r[0] ?? '') }}</td>
                      <td>{{ $stripMd($r[1] ?? '') }}</td>
                      <td>{{ $stripMd($r[2] ?? '') }}</td>
                      <td>{{ $stripMd($r[3] ?? '') }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
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
            <h2 class="section__title">Tokenomics</h2>
            <p class="section__sub">Dual-token design with governance utility, staking utility, and deflation mechanisms.</p>
          </div>

          <div class="panel">
            <div class="donut-grid">
              <div class="donut-card">
                <div class="card__top">
                  <div class="card__title">DEAI</div>
                  <div class="card__tag">Allocation</div>
                </div>
                <div class="donut-row">
                  <div class="donut" aria-hidden="true">
                    <div class="donut__label">DEAI</div>
                  </div>
                  <ul class="legend">
                    <li><span class="swatch swatch--a"></span>PoDRC Hardware Mining — 76%</li>
                    <li><span class="swatch swatch--b"></span>Early Community DAO — 10%</li>
                    <li><span class="swatch swatch--c"></span>Ecosystem Fund — 9%</li>
                    <li><span class="swatch swatch--d"></span>Development Team — 5%</li>
                  </ul>
                </div>
              </div>

              <div class="donut-card">
                <div class="card__top">
                  <div class="card__title">DEAI-T</div>
                  <div class="card__tag">Staking</div>
                </div>
                <div class="card__text">
                  {{ $excerpt($economics['content'][$contentLocale] ?? '', 180) }}
                </div>
                <div class="donut-row">
                  <div class="donut" style="background: conic-gradient(#2D6BFF 0 62%, #8B5CF6 62% 82%, #22C55E 82% 92%, #F59E0B 92% 100%);" aria-hidden="true">
                    <div class="donut__label">DEAI-T</div>
                  </div>
                  <ul class="legend">
                    <li><span class="swatch swatch--a"></span>Settlement Unit</li>
                    <li><span class="swatch swatch--b"></span>Staking Utility</li>
                    <li><span class="swatch swatch--c"></span>Deflation Design</li>
                    <li><span class="swatch swatch--d"></span>Governable Supply</li>
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
            <h2 class="section__title">Five Value-Capture Mechanisms</h2>
            <p class="section__sub">Designed to align infrastructure growth with sustainable ecosystem value.</p>
          </div>

          <div class="panel">
            <div class="value-grid">
              <div class="mini-card">
                <div class="mini-card__title">Compute Demand</div>
                <div class="mini-card__text">Real AI computing usage drives on-chain fees and throughput.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Staking Utility</div>
                <div class="mini-card__text">DEAI-T required for nodes and settlement; usage grows with network.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Deflation Design</div>
                <div class="mini-card__text">Burn + loss + buyback-style levers reduce long-term sell pressure.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Ecosystem Fund</div>
                <div class="mini-card__text">Community-governed funding supports builders, tools, and adoption.</div>
              </div>
              <div class="mini-card">
                <div class="mini-card__title">Governance Premium</div>
                <div class="mini-card__text">Voting, proposals, and upgrades connect ownership to network evolution.</div>
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
                <path d="M12 2l8 4v6c0 5-3.5 9.5-8 10-4.5-.5-8-5-8-10V6l8-4Z" stroke="#2D6BFF" stroke-width="1.6"/>
                <path d="M12 8v6" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 17h.01" stroke="#2D6BFF" stroke-width="2.6" stroke-linecap="round"/>
              </svg>
            </div>
            <h2 class="section__title">Audit &amp; Security</h2>
            <p class="section__sub">Audit conclusions and operational security primitives—built for transparency.</p>
          </div>

          <div class="panel">
            <div class="grid-2">
              <div class="card">
                <div class="card__top">
                  <div class="card__title">Audit Overview</div>
                  <div class="card__tag">24 Checks</div>
                </div>
                <div class="card__text">Key conclusions from the security audit report.</div>
                <div class="acc__body" style="padding: 0; margin-top: 10px;">
                  <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($auditChecks as $c)
                      <li>{{ $c }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>

              <div class="card">
                <div class="card__top">
                  <div class="card__title">Non-Custodial Safety</div>
                  <div class="card__tag">Self-Custody</div>
                </div>
                <div class="card__text">
                  {{ $excerpt(((($getChild('code_audit', 'asset_security_guide') ?? [])['content'][$contentLocale] ?? '')), 220) }}
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
                <path d="M6 12h12" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M12 6v12" stroke="#2D6BFF" stroke-width="1.8" stroke-linecap="round"/>
                <path d="M5 19h14" stroke="#2D6BFF" stroke-width="1.6" opacity="0.25" stroke-linecap="round"/>
              </svg>
            </div>
            <h2 class="section__title">Community Tools</h2>
            <p class="section__sub">Official apps and community entry points.</p>
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
            <h2 class="section__title">Liquidity &amp; Holders</h2>
            <p class="section__sub">On-chain visibility into liquidity conditions and distribution context.</p>
          </div>

          <div class="panel">
            <div class="liquidity-grid">
              <div class="card">
                <div class="card__top">
                  <div class="card__title">Liquidity</div>
                  <div class="card__tag">Pool</div>
                </div>
                <div class="card__text">A simplified view of liquidity conditions and market stability signals.</div>
                <div class="viz" aria-hidden="true">
                  <svg class="viz__line" viewBox="0 0 200 80" fill="none">
                    <path d="M4 62 C28 55, 36 18, 64 26 C92 34, 108 70, 140 46 C160 32, 176 36, 196 22" stroke="#2D6BFF" stroke-width="3" stroke-linecap="round"/>
                    <path d="M4 62 C28 55, 36 18, 64 26 C92 34, 108 70, 140 46 C160 32, 176 36, 196 22" stroke="#8B5CF6" stroke-width="3" stroke-linecap="round" opacity="0.35"/>
                  </svg>
                </div>
              </div>

              <div class="card">
                <div class="card__top">
                  <div class="card__title">Holders</div>
                  <div class="card__tag">Context</div>
                </div>
                <div class="card__text">
                  Top holder addresses include infrastructure contracts and liquidity pool addresses, supporting fairness and decentralization of distribution.
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
            <h2 class="section__title">Roadshow</h2>
            <p class="section__sub">Highlights from recent community and ecosystem events.</p>
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
                  <div class="video__title">Bangkok Roadshow</div>
                  <div class="video__sub">A short clip capturing the vibe and key exchanges from the roadshow.</div>
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
                  <div class="video__title">Singapore Roadshow</div>
                  <div class="video__sub">Community meetups, partner conversations, and product demos in the field.</div>
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
            <h2 class="section__title">Roadmap</h2>
            <p class="section__sub">A view into milestones and the multi-epoch path forward.</p>
          </div>

          <div class="panel">
            <div class="roadmap-grid">
              @foreach (['2024', '2025', '2026'] as $yr)
                <div class="timeline-card">
                  <div class="timeline-card__title">{{ $yr }}</div>
                  <ul class="timeline">
                    @foreach (array_slice($roadmapYears[$yr] ?? [], 0, 8) as $it)
                      <li>{{ $it }}</li>
                    @endforeach
                  </ul>
                </div>
              @endforeach
              <div class="timeline-card">
                <div class="timeline-card__title">Key Milestones</div>
                <ul class="timeline">
                  <li>Epoch 1 Launch (2025-03-29)</li>
                  <li>Epoch 2 Launch (2025-09-21)</li>
                  <li>Epoch 3 Storm Deflation (2026-Q3)</li>
                  <li>Mainnet Launch (2026-Q4)</li>
                </ul>
              </div>
            </div>
          </div>

          <div class="cta">
            <div class="cta__panel">
              <h3 class="cta__title">Start Your AI Model Journey</h3>
              <p class="cta__sub">
                Build, deploy, and verify AI on-chain with a modular stack designed for composability, governance, and security.
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
                  <span>Whitepaper</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <script>
      (function () {
        const btn = document.getElementById('langBtn');
        const menu = document.getElementById('langMenu');
        if (!btn || !menu) return;

        function close() {
          menu.hidden = true;
          btn.setAttribute('aria-expanded', 'false');
        }

        btn.addEventListener('click', function (e) {
          e.preventDefault();
          const willOpen = menu.hidden;
          if (willOpen) {
            menu.hidden = false;
            btn.setAttribute('aria-expanded', 'true');
          } else {
            close();
          }
        });

        document.addEventListener('click', function (e) {
          if (!menu.hidden && !menu.contains(e.target) && !btn.contains(e.target)) {
            close();
          }
        });

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape') close();
        });
      })();
    </script>
  </body>
</html>
