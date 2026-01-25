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

    <script>
      // Make SPA language switching apply immediately (same-tab).
      // Some SPAs listen to the "storage" event for locale changes, but browsers only
      // fire it in *other* tabs. We re-dispatch it in this tab when locale changes.
      (function () {
        if (typeof window === 'undefined' || !window.localStorage) return;
        if (window.__deaiSameTabStoragePatched) return;
        window.__deaiSameTabStoragePatched = true;

        const origSetItem = window.localStorage.setItem.bind(window.localStorage);
        const origRemoveItem = window.localStorage.removeItem.bind(window.localStorage);

        function shouldDispatch(key, newValue) {
          const k = (key || '').toString().toLowerCase();
          const v = (newValue == null ? '' : String(newValue)).toLowerCase();
          return k.includes('locale') || v === 'en-us' || v === 'zh-cn' || v.startsWith('zh');
        }

        window.localStorage.setItem = function (key, value) {
          const oldValue = window.localStorage.getItem(key);
          const result = origSetItem(key, value);
          try {
            if (shouldDispatch(key, value)) {
              window.dispatchEvent(
                new StorageEvent('storage', {
                  key,
                  oldValue,
                  newValue: String(value),
                  storageArea: window.localStorage,
                  url: window.location.href,
                })
              );
            }
          } catch (_) {}
          return result;
        };

        window.localStorage.removeItem = function (key) {
          const oldValue = window.localStorage.getItem(key);
          const result = origRemoveItem(key);
          try {
            if (shouldDispatch(key, null)) {
              window.dispatchEvent(
                new StorageEvent('storage', {
                  key,
                  oldValue,
                  newValue: null,
                  storageArea: window.localStorage,
                  url: window.location.href,
                })
              );
            }
          } catch (_) {}
          return result;
        };
      })();
    </script>

    {{-- DeAI Nexus (mirrored build assets) --}}
    <script type="module" crossorigin src="{{ asset('assets/index-AfuN7V2V.js') }}"></script>
    <link rel="stylesheet" crossorigin href="{{ asset('assets/index-B2bo1EsD.css') }}">
  </head>
  <body class="bg-slate-50">
    <div id="root"></div>

    <style>
      /* Remove top menu button (hamburger/menu toggle) */
      button[aria-label*="menu" i],
      button[aria-label*="导航" i],
      button[aria-label*="菜單" i],
      button[aria-label*="菜单" i] {
        display: none !important;
      }
    </style>

    <script>
      (function () {
        const LOGIN_URL = @json(route('login'));
        const APP_LOGO_URL = @json(asset('assets/Logo01.png'));

        function normalizeText(el) {
          return (el && el.textContent ? el.textContent : "").trim().replace(/\s+/g, " ");
        }

        function setAppLogo() {
          // Replace the top-left app logo image used by the SPA with Logo01.png.
          // DeAI Nexus bundle uses an <img alt="DeAI logo" ...>. We swap src while preserving layout classes.
          const imgs = document.querySelectorAll('img');
          for (const img of imgs) {
            const alt = (img.getAttribute('alt') || '').toLowerCase();
            const src = (img.getAttribute('src') || '').toLowerCase();

            const isLogo =
              alt.includes('logo') ||
              src.includes('logo192') ||
              src.includes('deai') && alt.includes('logo');

            if (!isLogo) continue;

            // Prefer swapping only the small header logo (avoid changing large images in content).
            const w = img.naturalWidth || img.width || 0;
            const h = img.naturalHeight || img.height || 0;
            const className = (img.getAttribute('class') || '');
            const looksLikeHeaderIcon =
              className.includes('w-14') ||
              className.includes('h-14') ||
              className.includes('rounded-2xl') ||
              (w > 0 && h > 0 && w <= 200 && h <= 200);

            if (!looksLikeHeaderIcon) continue;

            if (img.getAttribute('src') !== APP_LOGO_URL) {
              img.setAttribute('src', APP_LOGO_URL);
            }
            // Also keep consistent alt text.
            img.setAttribute('alt', 'App logo');

            // Make it bigger (inside the white header area).
            img.style.width = '112px';
            img.style.height = '112px';
            img.style.objectFit = 'contain';
            img.style.filter = 'drop-shadow(0 10px 18px rgba(15, 23, 42, 0.18))';
            // Nudge upward so it stays within the white header band.
            img.style.transform = 'translateY(-18px)';

            // Ensure its immediate container can accommodate the larger logo.
            const parent = img.parentElement;
            if (parent) {
              parent.style.width = '112px';
              parent.style.height = '112px';
              parent.style.overflow = 'visible';
            }
          }
        }

        function removeTopMenuButton() {
          // Remove common SPA menu toggle buttons (labels vary by locale/build).
          const candidates = document.querySelectorAll('button');
          for (const btn of candidates) {
            const label = ((btn.getAttribute('aria-label') || '') + ' ' + (btn.getAttribute('title') || '')).toLowerCase();
            const text = normalizeText(btn).toLowerCase();

            if (
              label.includes('menu') ||
              label.includes('navigation') ||
              label.includes('nav') ||
              label.includes('导航') ||
              label.includes('菜單') ||
              label.includes('菜单') ||
              text === 'menu' ||
              text === '导航'
            ) {
              btn.remove();
              continue;
            }
          }
        }

        function replacePledgeWithLogin() {
          const nodes = document.querySelectorAll('a,button');
          for (const node of nodes) {
            if (node && node.dataset && node.dataset.loginPatched === '1') continue;

            const text = normalizeText(node);
            const isPledge =
              text.toLowerCase() === 'pledge' ||
              text.toLowerCase().includes('pledge') ||
              text.includes('质押') ||
              text.includes('質押');

            if (!isPledge) continue;

            // Replace element entirely to preserve layout classes while changing behavior.
            const link = document.createElement('a');
            link.href = LOGIN_URL;
            link.className = node.className || '';
            link.textContent = 'Login';
            link.dataset.loginPatched = '1';

            // Ensure click navigates to Laravel login flow.
            link.addEventListener('click', function (e) {
              e.preventDefault();
              window.location.href = LOGIN_URL;
            });

            node.replaceWith(link);
          }
        }

        function removeTopMenuItems() {
          // Remove top nav items requested by user (desktop + mobile menus)
          const banned = [
            'overview',
            'technology',
            'applications',
            'comparison',
            'tokenmics', // user spelling
            'tokenomics',
            'value capture',
            'audit',
            'tools',
            'data',
            'roadshow',
            'roadmap',
            'dapp',
            // Common Chinese labels seen on DeAI Nexus
            '概览',
            '總覽',
            '技术',
            '技術',
            '应用',
            '應用',
            '对比',
            '對比',
            '代币经济',
            '代幣經濟',
            '价值捕获',
            '價值捕獲',
            '审计',
            '審計',
            '工具',
            '数据',
            '數據',
            '路演',
            '路线图',
            '路線圖',
            'dapp',
          ];

          // 1) Remove anchor-based section navigation links regardless of label.
          // Most SPAs implement top menus as hash/anchor links.
          const anchorLinks = document.querySelectorAll('a[href^="#"], a[href*="/#"], a[href*="#"]');
          for (const a of anchorLinks) {
            const href = (a.getAttribute('href') || '').trim();
            if (!href) continue;

            const lowerHref = href.toLowerCase();
            // Keep nothing hash-based on homepage nav (user wants no menu items).
            // Avoid removing purely-empty/placeholder links.
            if (lowerHref.startsWith('#') || lowerHref.includes('/#') || lowerHref.includes('#')) {
              // But don't touch Login links if any ever use hash (unlikely).
              const text = normalizeText(a).toLowerCase();
              if (text.includes('login')) continue;
              a.remove();
            }
          }

          // 2) Remove by visible labels (English + Chinese), for both <a> and <button>.
          const nodes = document.querySelectorAll('a,button');
          for (const node of nodes) {
            const text = normalizeText(node);
            if (!text) continue;

            const lower = text.toLowerCase();

            // Keep login-related UI intact.
            if (lower === 'login' || lower.includes('login')) continue;

            // Remove only if it matches one of the menu items.
            if (banned.some((w) => lower === w || lower.includes(w))) {
              node.remove();
            }
          }

          // 3) Remove now-empty menu containers (common patterns: nav, ul, flex rows).
          const maybeContainers = document.querySelectorAll('nav, ul, ol, div');
          for (const el of maybeContainers) {
            // Skip if it still contains a login element.
            if (el.querySelector && el.querySelector('a[href*="login"], a[href="/login"], button')) {
              const loginEl = el.querySelector('a[href*="login"], a[href="/login"]');
              if (loginEl) continue;
            }
            const hasLinks = el.querySelector && el.querySelector('a,button');
            if (!hasLinks) continue;
            const visibleText = normalizeText(el);
            if (!visibleText) {
              // If container has no text and no images/inputs, drop it.
              const hasMedia = el.querySelector && el.querySelector('img,svg,input,select,textarea');
              if (!hasMedia) el.remove();
            }
          }
        }

        function applyPatches() {
          setAppLogo();
          removeTopMenuButton();
          removeTopMenuItems();
          replacePledgeWithLogin();
        }

        // Run now, then keep enforcing as the SPA renders/updates.
        applyPatches();
        const mo = new MutationObserver(function () {
          applyPatches();
        });
        mo.observe(document.documentElement, { subtree: true, childList: true });

        // Fallback periodic enforcement (in case of shadow DOM or rapid updates).
        setInterval(applyPatches, 1500);

      })();
    </script>
  </body>
</html>
