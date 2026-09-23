/*!
 * managed-graylog.com — site interactions (vanilla JS, no dependencies).
 * Progressive enhancement: every feature degrades to working HTML without JS.
 */
(() => {
  'use strict';

  const doc = document.documentElement;
  doc.classList.remove('no-js');
  doc.classList.add('js');

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* ------------------------------------------------------------------
   * Header: solid background after scrolling
   * ---------------------------------------------------------------- */
  const header = $('[data-header]');
  if (header) {
    let ticking = false;
    const update = () => {
      header.classList.toggle('is-scrolled', window.scrollY > 12);
      ticking = false;
    };
    update();
    window.addEventListener('scroll', () => {
      if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
  }

  /* ------------------------------------------------------------------
   * Mobile menu: aria-expanded, focus trap, Esc to close, scroll lock
   * ---------------------------------------------------------------- */
  const toggle = $('[data-menu-toggle]');
  const menu = $('[data-mobile-menu]');
  if (toggle && menu) {
    const label = $('.visually-hidden', toggle);
    const focusables = () => $$('a, button', menu);

    const open = () => {
      menu.hidden = false;
      requestAnimationFrame(() => menu.classList.add('is-open'));
      toggle.setAttribute('aria-expanded', 'true');
      if (label) label.textContent = 'Close menu';
      document.body.classList.add('menu-open');
      const first = focusables()[0];
      if (first) first.focus({ preventScroll: true });
    };
    const close = (returnFocus = true) => {
      menu.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      if (label) label.textContent = 'Open menu';
      document.body.classList.remove('menu-open');
      const hide = () => { menu.hidden = true; };
      if (reduceMotion.matches) hide(); else setTimeout(hide, 220);
      if (returnFocus) toggle.focus({ preventScroll: true });
    };

    toggle.addEventListener('click', () => (toggle.getAttribute('aria-expanded') === 'true' ? close() : open()));
    menu.addEventListener('click', (e) => { if (e.target.closest('a')) close(false); });
    document.addEventListener('keydown', (e) => {
      if (toggle.getAttribute('aria-expanded') !== 'true') return;
      if (e.key === 'Escape') { close(); return; }
      if (e.key === 'Tab') {
        const items = [toggle, ...focusables()];
        const first = items[0];
        const last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
    window.matchMedia('(min-width: 992px)').addEventListener('change', (m) => {
      if (m.matches && toggle.getAttribute('aria-expanded') === 'true') close(false);
    });
  }

  /* ------------------------------------------------------------------
   * Scroll reveal (with light stagger for siblings)
   * ---------------------------------------------------------------- */
  const revealEls = $$('[data-reveal]').filter((el) => !el.closest('.hero'));
  if ('IntersectionObserver' in window && !reduceMotion.matches) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const siblings = el.parentElement ? $$(':scope > [data-reveal]', el.parentElement) : [];
        const idx = Math.max(0, siblings.indexOf(el));
        el.style.setProperty('--d', `${Math.min(idx, 8) * 55}ms`);
        el.classList.add('is-visible');
        io.unobserve(el);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.08 });
    revealEls.forEach((el) => io.observe(el));
  } else {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  }

  /* ------------------------------------------------------------------
   * Service cards: pointer-following highlight
   * ---------------------------------------------------------------- */
  $$('.svc').forEach((card) => {
    card.addEventListener('pointermove', (e) => {
      const r = card.getBoundingClientRect();
      card.style.setProperty('--mx', `${e.clientX - r.left}px`);
      card.style.setProperty('--my', `${e.clientY - r.top}px`);
    });
  });

  /* ------------------------------------------------------------------
   * Layers: light the stack meter as rows scroll into view
   * ---------------------------------------------------------------- */
  const meter = $('[data-stack-meter]');
  const rows = $$('[data-layer-row]');
  if (meter && rows.length && 'IntersectionObserver' in window) {
    const bars = $$('span', meter);
    const lit = new Set();
    const lio = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        const i = rows.indexOf(entry.target);
        if (entry.isIntersecting) { lit.add(i); entry.target.classList.add('is-lit'); }
      });
      bars.forEach((b, i) => b.classList.toggle('is-lit', lit.has(i)));
    }, { rootMargin: '0px 0px -35% 0px' });
    rows.forEach((r) => lio.observe(r));
  }

  /* ------------------------------------------------------------------
   * Hero log stream (illustrative sample data)
   * ---------------------------------------------------------------- */
  const stream = $('[data-logstream]');
  const plane = $('[data-control-plane]');
  if (plane && reduceMotion.matches) {
    $$('svg', plane).forEach((svg) => svg.pauseAnimations && svg.pauseAnimations());
  }
  if (stream && !reduceMotion.matches) {
    const samples = [
      ['INFO', 'nginx-edge', 'GET /api/v1/cart 200 21ms'],
      ['INFO', 'k8s-node', 'pod api-5c7b8 started on node-3'],
      ['INFO', 'fw-core', 'accept udp 10.0.2.44:53 → 10.0.0.2:53'],
      ['WARN', 'nginx-edge', 'upstream response time 1.8s /search'],
      ['INFO', 'linux-01', 'systemd: Started daily apt upgrade'],
      ['INFO', 'k8s-node', 'checkout: order 48213 confirmed'],
      ['ERR', 'app-worker', 'payment gateway timeout after 30s'],
      ['INFO', 'fw-core', 'deny tcp 203.0.113.7:4431 → 10.0.1.8:22'],
      ['INFO', 'linux-01', 'sshd: accepted publickey for deploy'],
      ['WARN', 'k8s-node', 'container restarted: OOMKilled'],
      ['INFO', 'nginx-edge', 'POST /api/v1/orders 201 64ms'],
    ];
    const pad = (n, l = 2) => String(n).padStart(l, '0');
    const stamp = () => {
      const d = new Date();
      return `${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}.${pad(d.getMilliseconds(), 3)}`;
    };
    const cls = { INFO: 'lv--info', WARN: 'lv--warn', ERR: 'lv--err' };
    let i = 0;
    let timer = null;
    const push = () => {
      const [lv, src, msg] = samples[i++ % samples.length];
      const li = document.createElement('li');
      li.className = 'is-new';
      const t = document.createElement('time'); t.textContent = stamp();
      const b = document.createElement('b'); b.className = `lv ${cls[lv]}`; b.textContent = lv;
      const s = document.createElement('span'); s.className = 'src'; s.textContent = src;
      const m = document.createElement('span'); m.className = 'msg'; m.textContent = msg;
      li.append(t, b, s, m);
      stream.append(li);
      while (stream.children.length > 4) stream.firstElementChild.remove();
    };
    const start = () => { if (!timer) timer = setInterval(push, 1400); };
    const stop = () => { clearInterval(timer); timer = null; };
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(([e]) => (e.isIntersecting ? start() : stop())).observe(stream);
    } else {
      start();
    }
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
  }

  /* ------------------------------------------------------------------
   * Architecture diagram: hover / focus / click to inspect a component
   * ---------------------------------------------------------------- */
  const arch = $('[data-arch]');
  if (arch) {
    const nodes = $$('.ad-node', arch);
    const edges = $$('.ad-edge', arch);
    const panels = $$('[data-panel]', arch);
    const idLabel = $('[data-inspector-id]', arch);
    let pinned = null;

    const activate = (node) => {
      const id = node ? node.dataset.node : null;
      arch.classList.toggle('has-active', !!id);
      const linked = new Set();
      edges.forEach((edge) => {
        const hot = !!id && (edge.dataset.a === id || edge.dataset.b === id);
        edge.classList.toggle('is-hot', hot);
        if (hot) { linked.add(edge.dataset.a); linked.add(edge.dataset.b); }
      });
      nodes.forEach((n) => {
        n.classList.toggle('is-active', n === node);
        n.classList.toggle('is-linked', !!id && n !== node && linked.has(n.dataset.node));
        n.setAttribute('aria-pressed', n === node ? 'true' : 'false');
      });
      const target = node ? node.dataset.panelTarget : 'cluster';
      panels.forEach((p) => {
        const show = p.dataset.panel === target;
        if (show && p.hidden) { p.classList.remove('is-entering'); void p.offsetWidth; p.classList.add('is-entering'); }
        p.hidden = !show;
      });
      if (idLabel) idLabel.textContent = target;
    };

    nodes.forEach((node) => {
      node.addEventListener('mouseenter', () => activate(node));
      node.addEventListener('focus', () => activate(node));
      node.addEventListener('click', () => { pinned = node; activate(node); });
      node.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); pinned = node; activate(node); }
      });
    });
    $('.arch__svg', arch).addEventListener('mouseleave', () => activate(pinned));
    activate(nodes.find((n) => n.dataset.node === 'gl1') || null);
    pinned = nodes.find((n) => n.dataset.node === 'gl1') || null;
  }

  /* ------------------------------------------------------------------
   * Deployment request form
   * ---------------------------------------------------------------- */
  const form = $('[data-request-form]');
  if (form) {
    const preview = $('[data-spec-preview] code');
    const labelFor = (name) => {
      const el = form.elements[name];
      if (!el) return '';
      if (el instanceof RadioNodeList || (el.length && el[0] && el[0].type === 'radio')) {
        const checked = Array.from(el).find((r) => r.checked);
        return checked ? checked.nextElementSibling.textContent.trim() : '';
      }
      if (el.tagName === 'SELECT') return el.value ? el.options[el.selectedIndex].text : '';
      return (el.value || '').trim();
    };
    const esc = (s) => s.replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const renderPreview = () => {
      if (!preview) return;
      const rows = [
        ['company', labelFor('company')],
        ['environment', labelFor('environment')],
        ['graylog', labelFor('graylog_version')],
        ['volume', labelFor('volume')],
        ['sources', labelFor('servers')],
        ['retention', labelFor('retention')],
        ['infrastructure', labelFor('infrastructure')],
        ['ha', labelFor('ha')],
        ['monitoring', labelFor('monitoring')],
        ['backup', labelFor('backup')],
      ];
      preview.innerHTML = '<span class="k">request</span>:\n' + rows.map(([k, v]) => {
        const val = v ? `<span class="v is-set">"${esc(v.slice(0, 48))}"</span>` : '<span class="v">~</span>';
        return `  <span class="k">${k}</span>: ${val}`;
      }).join('\n');
    };
    form.addEventListener('input', renderPreview);
    form.addEventListener('change', renderPreview);
    renderPreview();

    const secretRe = [
      /-----BEGIN [A-Z ]*PRIVATE KEY-----/, /\bAKIA[0-9A-Z]{16}\b/, /\b(?:ghp|gho|ghs|github_pat)_[A-Za-z0-9_]{20,}/,
      /\bxox[baprs]-[A-Za-z0-9-]{10,}/, /\bAIza[0-9A-Za-z_\-]{35}\b/,
      /\b(?:password|passwd|pwd|secret|api[_-]?key|token)\s*[:=]\s*(?=\S*[\d!@#$%^&*])\S{8,}/i,
    ];

    const setError = (name, msg) => {
      const err = $(`#err-${name}`, form);
      const field = form.elements[name];
      if (err) { err.textContent = msg || ''; err.hidden = !msg; }
      if (field && field.setAttribute && !(field instanceof RadioNodeList)) {
        if (msg) field.setAttribute('aria-invalid', 'true'); else field.removeAttribute('aria-invalid');
      }
    };

    const validate = () => {
      const errors = {};
      const name = form.elements.name.value.trim();
      const email = form.elements.email.value.trim();
      if (name.length < 2) errors.name = 'Please enter your name.';
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) errors.email = 'Please enter a valid email address.';
      if (!form.elements.consent.checked) errors.consent = 'Please confirm so we can process your request.';
      ['architecture', 'notes', 'company'].forEach((f) => {
        const v = form.elements[f].value;
        if (secretRe.some((re) => re.test(v))) errors[f] = 'This looks like it may contain a password, key or token. Please remove it — we will agree a secure channel for access later.';
      });
      ['name', 'email', 'company', 'architecture', 'notes', 'consent', 'graylog_version'].forEach((f) => setError(f, errors[f]));
      return errors;
    };

    ['name', 'email'].forEach((f) => form.elements[f].addEventListener('blur', () => {
      if (form.elements[f].value.trim() !== '') validate();
    }));

    const showResult = (ok, message) => {
      let box = $('[data-form-result]', form.parentElement);
      if (!box) {
        box = document.createElement('div');
        box.setAttribute('data-form-result', '');
        box.tabIndex = -1;
        form.parentElement.insertBefore(box, form);
      }
      box.className = `form-result form-result--${ok ? 'ok' : 'err'}`;
      box.setAttribute('role', ok ? 'status' : 'alert');
      box.textContent = '';
      const strong = document.createElement('strong');
      strong.textContent = ok ? 'Request received. ' : 'Something needs attention. ';
      const span = document.createElement('div');
      span.append(strong, document.createTextNode(message));
      box.append(span);
      box.focus({ preventScroll: true });
      box.scrollIntoView({ behavior: reduceMotion.matches ? 'auto' : 'smooth', block: 'center' });
    };

    form.addEventListener('submit', async (e) => {
      const errors = validate();
      if (Object.keys(errors).length) {
        e.preventDefault();
        const first = form.elements[Object.keys(errors)[0]];
        if (first && first.focus) first.focus();
        return;
      }
      if (!window.fetch || !window.FormData) return; // fall back to normal POST

      e.preventDefault();
      const btn = $('[data-submit]', form);
      const lbl = $('[data-submit-label]', form);
      const original = lbl.textContent;
      btn.classList.add('is-loading');
      btn.disabled = true;
      lbl.textContent = 'Sending…';
      try {
        const res = await fetch(form.action.split('#')[0], {
          method: 'POST',
          body: new FormData(form),
          headers: { Accept: 'application/json' },
          credentials: 'same-origin',
        });
        const data = await res.json().catch(() => ({ ok: false, errors: {}, message: 'Unexpected response.' }));
        if (data.ok) {
          form.reset();
          if (data.token && form.elements._token) form.elements._token.value = data.token;
          renderPreview();
          showResult(true, data.message || 'An engineer will review your details and reply by email.');
        } else {
          Object.entries(data.errors || {}).forEach(([k, v]) => { if (k !== '_form') setError(k, v); });
          showResult(false, data.message || 'Please check the highlighted fields.');
        }
      } catch (err) {
        showResult(false, 'Network error. Please try again, or email us directly.');
      } finally {
        btn.classList.remove('is-loading');
        btn.disabled = false;
        lbl.textContent = original;
      }
    });

    // Focus server-rendered result (no-JS round trip with JS present on return).
    const serverResult = $('[data-form-result]', form.parentElement);
    if (serverResult) serverResult.focus({ preventScroll: true });
  }

  /* ------------------------------------------------------------------
   * FAQ: open the item targeted by the URL hash
   * ---------------------------------------------------------------- */
  const openHash = () => {
    if (!location.hash) return;
    const el = document.getElementById(location.hash.slice(1));
    if (el && el.tagName === 'DETAILS') el.open = true;
  };
  openHash();
  window.addEventListener('hashchange', openHash);
})();
