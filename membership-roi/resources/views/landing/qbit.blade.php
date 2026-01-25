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
        const APP_LOGO_URL = @json(asset('images/Logo01.png'));

        function normalizeText(el) {
          return (el && el.textContent ? el.textContent : "").trim().replace(/\s+/g, " ");
        }

        function alignHeaderRow() {
          const { headerRow, leftBlock, rightBlock } = findHeaderBlocks();
          if (headerRow) headerRow.style.alignItems = 'center';
          if (leftBlock) leftBlock.style.alignItems = 'center';
          if (rightBlock) rightBlock.style.alignItems = 'center';
        }

        function findHeaderBlocks() {
          const root = document.getElementById('root');
          if (!root) return { headerRow: null, leftBlock: null, rightBlock: null };

          // Find the Login button/link in the header area.
          const nodes = Array.from(root.querySelectorAll('a,button'));
          const loginEl = nodes.find((el) => normalizeText(el).toLowerCase() === 'login') || null;
          if (!loginEl) return { headerRow: null, leftBlock: null, rightBlock: null };

          // Walk up to find a flex container near the top that likely represents the header row.
          let headerRow = loginEl.parentElement;
          for (let i = 0; i < 10 && headerRow; i++) {
            const cls = (headerRow.getAttribute('class') || '');
            const isFlex = cls.includes('flex') || getComputedStyle(headerRow).display === 'flex';
            const top = headerRow.getBoundingClientRect().top;
            if (isFlex && top >= -20 && top < 220 && headerRow.children.length >= 2) break;
            headerRow = headerRow.parentElement;
          }
          if (!headerRow) return { headerRow: null, leftBlock: null, rightBlock: null };

          // Identify which child contains the login element -> right block.
          const kids = Array.from(headerRow.children).filter((c) => c && c.nodeType === 1);
          const rightBlock = kids.find((c) => c.contains(loginEl)) || null;
          const leftBlock = kids.find((c) => c !== rightBlock) || null;

          return { headerRow, leftBlock, rightBlock };
        }

        function injectHeaderLogo() {
          // Ensure Logo01.png is always visible in the top-left header area,
          // even if the SPA uses SVG/text instead of an <img>.
          const { headerRow, leftBlock, rightBlock } = findHeaderBlocks();
          if (!headerRow || !leftBlock) return;

          // Already injected
          if (leftBlock.querySelector('[data-app-logo=\"1\"]')) return;

          // Prefer swapping an existing img in the left block if present.
          const existingImg = leftBlock.querySelector('img');
          if (existingImg) {
            existingImg.src = APP_LOGO_URL;
            existingImg.alt = 'App logo';
            existingImg.setAttribute('data-app-logo', '1');
            existingImg.style.width = '72px';
            existingImg.style.height = '72px';
            existingImg.style.objectFit = 'contain';
            existingImg.style.background = 'transparent';
            existingImg.style.display = 'block';
            // Remove border/ring/background from the wrapper if any.
            const p = existingImg.parentElement;
            if (p) {
              p.style.background = 'transparent';
              p.style.border = 'none';
              p.style.boxShadow = 'none';
              p.style.padding = '0';
            }
            return;
          }

          // Otherwise, inject a new img at the start of the left block.
          const img = document.createElement('img');
          img.src = APP_LOGO_URL;
          img.alt = 'App logo';
          img.setAttribute('data-app-logo', '1');
          img.style.width = '72px';
          img.style.height = '72px';
          img.style.objectFit = 'contain';
          img.style.background = 'transparent';
          img.style.flex = '0 0 auto';
          img.style.display = 'block';

          // Make sure the left block can show it nicely.
          leftBlock.style.display = 'flex';
          leftBlock.style.alignItems = 'center';
          leftBlock.style.gap = '0';
          leftBlock.style.marginLeft = '0';
          leftBlock.style.paddingLeft = '0';

          leftBlock.insertBefore(img, leftBlock.firstChild);
        }

        function logoOnlyOnLeft() {
          const { headerRow, leftBlock } = findHeaderBlocks();
          if (!headerRow || !leftBlock) return;

          // Keep only the injected/app logo visible on the left.
          const logo = leftBlock.querySelector('[data-app-logo=\"1\"], img');
          for (const child of Array.from(leftBlock.children)) {
            if (logo && (child === logo || child.contains(logo))) {
              child.style.display = 'flex';
              continue;
            }
            child.style.display = 'none';
          }

          // Remove any border/ring/pill effects around the logo container.
          const logoParent = logo ? logo.parentElement : null;
          if (logoParent) {
            logoParent.style.background = 'transparent';
            logoParent.style.border = 'none';
            logoParent.style.boxShadow = 'none';
            logoParent.style.padding = '0';
            logoParent.style.margin = '0';
          }
        }

        function removeLanguageSwitchButtons() {
          // Remove the language switch control(s) in header; keep Login.
          const { rightBlock } = findHeaderBlocks();
          if (!rightBlock) return;

          const nodes = Array.from(rightBlock.querySelectorAll('a,button'));
          for (const node of nodes) {
            const text = normalizeText(node);
            const lower = text.toLowerCase();

            // Keep Login button/link.
            if (lower === 'login' || lower.includes('login')) continue;

            // Remove common language switch labels.
            if (
              lower === 'en' ||
              lower === 'english' ||
              lower.includes('switch to english') ||
              text === '中文' ||
              text.includes('切换') ||
              text.includes('切換') ||
              lower.includes('language')
            ) {
              node.remove();
            }
          }
        }

        function moveLogoToTopLeft() {
          // Make the logo the leftmost element by shifting the left block to the viewport edge,
          // without changing the rest of the header layout.
          const { leftBlock } = findHeaderBlocks();
          if (!leftBlock) return;
          const logo = leftBlock.querySelector('[data-app-logo=\"1\"], img');
          if (!logo) return;

          // Only move when the left block contains only the logo (logoOnlyOnLeft()).
          const rect = leftBlock.getBoundingClientRect();
          const desiredLeft = 12; // keep a small gutter
          const dx = rect.left - desiredLeft;
          if (!Number.isFinite(dx)) return;

          leftBlock.style.transform = `translateX(${-dx}px)`;
          leftBlock.style.marginLeft = '0';
          leftBlock.style.paddingLeft = '0';
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

            // Bigger, transparent, and aligned with header buttons.
            img.style.width = '72px';
            img.style.height = '72px';
            img.style.objectFit = 'contain';
            img.style.background = 'transparent';
            img.style.padding = '0';
            img.style.borderRadius = '0';
            img.style.filter = 'none';
            img.style.transform = 'none';

            // Ensure its immediate container can accommodate the larger logo.
            const parent = img.parentElement;
            if (parent) {
              parent.style.width = '72px';
              parent.style.height = '72px';
              parent.style.overflow = 'visible';
              parent.style.marginTop = '0';
              parent.style.position = 'relative';
              parent.style.zIndex = '50';

              // Make container transparent (remove white pill background/padding if present).
              parent.style.background = 'transparent';
              parent.style.padding = '0';
              parent.style.border = 'none';
              parent.style.boxShadow = 'none';
              parent.style.display = 'flex';
              parent.style.alignItems = 'center';
              parent.style.justifyContent = 'center';
            }
          }
        }

        function moveCoreHighlightsToRight() {
          const root = document.getElementById('root');
          if (!root) return;

          const markers = ['core highlights', '核心亮点'];
          const all = Array.from(root.querySelectorAll('*'));
          const hits = all.filter((el) => {
            const t = normalizeText(el).toLowerCase();
            return t && markers.some((m) => t.includes(m));
          });

          for (const hit of hits) {
            // Walk up to find a 2-column container (grid/flex) that holds the highlight.
            let container = hit;
            for (let i = 0; i < 8 && container; i++) {
              container = container.parentElement;
              if (!container) break;

              const kids = Array.from(container.children).filter((c) => c && c.nodeType === 1);
              if (kids.length !== 2) continue;

              const cls = (container.getAttribute('class') || '');
              const isTwoColLayout =
                cls.includes('grid') ||
                cls.includes('flex') ||
                cls.includes('lg:grid-cols-') ||
                cls.includes('grid-cols-2');

              if (!isTwoColLayout) continue;

              const [left, right] = kids;
              // If the highlight is currently in the left column, swap order.
              if (left.contains(hit) && !right.contains(hit)) {
                container.insertBefore(right, left);
                return;
              }
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
          alignHeaderRow();
          injectHeaderLogo();
          logoOnlyOnLeft();
          removeLanguageSwitchButtons();
          moveLogoToTopLeft();
          setAppLogo();
          moveCoreHighlightsToRight();
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
