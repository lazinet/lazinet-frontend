/*!
 * LAZIBOT - Bo nap bong bong tro ly ao cho cong thong tin / dich vu cong
 * (c) LAZINET. Tai lieu tich hop: lazibot-integration-portal-bubble.docx
 *
 * Cach dung (dan 1 lan vao cuoi <body>, hoac 1 Fragment/HTML block cua cong):
 *
 *   <script>
 *     window.LAZIBOT_EMBED = {
 *       key: "lzb_site_...",        // khoa cap rieng cho ten mien cua cong
 *       title: "Tro ly ao Dich vu cong",
 *       position: "right",          // right | left
 *       offset: { x: 20, y: 20 },
 *       autoOpen: false,
 *       zIndex: 2147483000
 *     };
 *     var s = document.createElement('script');
 *     s.src = "https://dichvucong-lazibot.lazinet.com/embed.js";
 *     s.id = "lazibot-embed"; s.async = true;
 *     document.body.appendChild(s);
 *   </script>
 *
 * NGUYEN TAC THIET KE (co y khac voi nhieu widget thuong gap):
 *  1. KHONG BAO GIO de lai "bong bong chet". Truoc khi ve bat cu thu gi, bo
 *     nap goi thu backend; khong dat thi im lang bo qua. Bai hoc rut ra tu
 *     widget cua don vi truoc van con nam lai tren chinh cong nay: CDN van
 *     tai, bong bong van hien, nhung bam vao chi ra "404 Route Not Found".
 *  2. Toan bo giao dien nam trong Shadow DOM dong (closed) - CSS cua cong
 *     khong lot vao lam vo bong bong, CSS cua bong bong khong lot ra lam
 *     hong cong. Khong chen the <style> nao vao trang cha.
 *  3. Khong nap thu vien ngoai, khong font ngoai, khong dat cookie tren ten
 *     mien cua cong. Tai bat dong bo, khong chan hien thi trang.
 *  4. postMessage luon kiem tra origin ca hai chieu, khong dung '*'.
 */
(function () {
  'use strict';

  // Chan nap 2 lan (VD quan tri vien lo dan doan ma vao 2 vi tri) - neu
  // khong se co 2 bong bong chong len nhau.
  if (window.__lazibotEmbedLoaded) return;
  window.__lazibotEmbedLoaded = true;

  var cfg = window.LAZIBOT_EMBED || {};
  var script = document.currentScript || document.getElementById('lazibot-embed');

  // Goc cua he thong LAZIBOT suy ra tu chinh dia chi tep embed.js nay. Nho
  // vay neu sau nay cong dat reverse-proxy (VD hcc.quangtri.gov.vn/tro-ly-ao)
  // thi chi can doi dia chi script, khong phai sua gi trong cau hinh.
  var BASE = (function () {
    if (cfg.base) return String(cfg.base).replace(/\/+$/, '');
    try { return new URL(script.src).origin; } catch (e) { return 'https://dichvucong-lazibot.lazinet.com'; }
  })();

  // Khoa cong khai cap cho ten mien cua cong (lay trong trang quan tri,
  // muc "Tich hop su dung"). Thieu khoa thi bong bong van chay binh thuong,
  // nhung nguoi dan se bi tinh theo han muc khach vang lai chung.
  var SITE_KEY = cfg.key || '';
  var TITLE = cfg.title || '';
  var GREETING = cfg.greeting || '';
  var POSITION = cfg.position === 'left' ? 'left' : 'right';
  var OFF_X = (cfg.offset && +cfg.offset.x) || 20;
  var OFF_Y = (cfg.offset && +cfg.offset.y) || 20;
  var Z = +cfg.zIndex || 2147483000;
  var AUTO_OPEN = cfg.autoOpen === true;
  var LABEL = cfg.label || 'Hoi dap thu tuc';

  var HOST_ORIGIN = location.origin;
  var IFRAME_ORIGIN = BASE;

  // --- Kich thuoc khung tro chuyen (chi ap dung tren man hinh lon) ---
  // Mac dinh van la khung nho 400x620 de khong che noi dung cua cong.
  // "Phong to" KHONG lay tron 100% man hinh mot cach co y: tro ly la thu
  // dung BEN CANH noi dung cong, chiem sach man hinh la mat y nghia do.
  // Chieu rong toi da 1040px duoc chon vi qua moc 560px thi bang Tra cuu
  // hien lai du ca 7 cot (xem media query trong index.html) - tuc phong to
  // mo khoa them tinh nang that, khong chi la to hon.
  var MOBILE_BP = 640;          // duoi moc nay: tran man hinh, khong cho doi co
  var DEF_W = 400, DEF_H = 620; // kich thuoc mac dinh
  var MIN_W = 340, MIN_H = 420; // nho nhat cho phep khi keo tay
  var EXP_MAX_W = 1040;         // rong nhat khi phong to

  function esc(s) {
    return String(s).replace(/&/g, '&amp;').replace(/"/g, '&quot;')
      .replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  // ---------------------------------------------------------------
  // Buoc 1: kiem tra suc khoe. /api/site-settings la endpoint cong khai
  // nhe nhat, vua de biet backend con song vua lay luon mau thuong hieu va
  // logo cua don vi - 1 lan goi cho ca 2 viec.
  // ---------------------------------------------------------------
  function healthCheck() {
    var ctrl = typeof AbortController !== 'undefined' ? new AbortController() : null;
    var timer = setTimeout(function () { if (ctrl) ctrl.abort(); }, 5000);
    return fetch(BASE + '/api/site-settings', {
      method: 'GET',
      credentials: 'omit',
      signal: ctrl ? ctrl.signal : undefined
    }).then(function (r) {
      clearTimeout(timer);
      if (!r.ok) throw new Error('HTTP ' + r.status);
      return r.json();
    }).then(function (d) {
      return (d && d.settings) || {};
    }, function (e) {
      clearTimeout(timer);
      throw e;
    });
  }

  function boot(settings) {
    var primary = settings.primary_color || '#b3202a';
    var secondary = settings.secondary_color || '#ff6302';
    var logo = settings.logo_url
      ? (/^https?:/.test(settings.logo_url) ? settings.logo_url : BASE + settings.logo_url)
      : '';
    var appName = TITLE || settings.ten_ung_dung || 'Tro ly ao';

    // -------------------------------------------------------------
    // Buoc 2: ve giao dien trong Shadow DOM
    // -------------------------------------------------------------
    var mount = document.createElement('div');
    mount.setAttribute('data-lazibot-widget', '');
    // Vo boc phu toan man hinh nhung KHONG chan chuot o vung trong; chi cac
    // phan tu ben trong moi nhan su kien, tranh che mat link cua cong.
    mount.style.cssText = 'position:fixed;inset:0;pointer-events:none;border:0;margin:0;padding:0;z-index:' + Z;
    (document.body || document.documentElement).appendChild(mount);
    var root = mount.attachShadow ? mount.attachShadow({ mode: 'closed' }) : mount;

    // Khung neo ben phai -> goc keo nam o tren-TRAI; neo ben trai -> tren-PHAI.
    var RZ_SIDE = POSITION === 'left' ? 'right' : 'left';
    var RZ_CURSOR = POSITION === 'left' ? 'nesw-resize' : 'nwse-resize';

    var css = [
      ':host,*{box-sizing:border-box}',
      '.lz-btn{position:fixed;bottom:' + OFF_Y + 'px;' + POSITION + ':' + OFF_X + 'px;',
      'display:flex;align-items:center;gap:10px;pointer-events:auto;',
      'height:60px;padding:0 20px 0 10px;border:none;border-radius:999px;cursor:pointer;',
      'background:linear-gradient(135deg,' + primary + ',' + secondary + ');color:#fff;',
      'font:600 15px/1.2 "Be Vietnam Pro","Segoe UI",system-ui,Arial,sans-serif;',
      'box-shadow:0 6px 22px rgba(0,0,0,.28);transition:transform .18s ease,box-shadow .18s ease}',
      '.lz-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.34)}',
      '.lz-btn:focus-visible{outline:3px solid #fff;outline-offset:3px}',
      '.lz-btn .ic{position:relative;width:40px;height:40px;border-radius:50%;background:#fff;flex:0 0 40px;',
      'display:flex;align-items:center;justify-content:center;overflow:hidden}',
      '.lz-btn .ic img{width:100%;height:100%;object-fit:contain;padding:4px}',
      '.lz-btn .ic svg{width:24px;height:24px;fill:' + primary + '}',
      '.lz-btn .tx{white-space:nowrap}',
      '.lz-dot{position:absolute;top:4px;' + POSITION + ':8px;width:14px;height:14px;border-radius:50%;',
      'background:#e02020;border:2px solid #fff;display:none}',
      '.lz-btn.unread .lz-dot{display:block}',
      '.lz-panel{position:fixed;bottom:' + (OFF_Y + 72) + 'px;' + POSITION + ':' + OFF_X + 'px;',
      'width:400px;height:620px;max-height:calc(100vh - ' + (OFF_Y + 92) + 'px);',
      'pointer-events:auto;background:#fff;border-radius:16px;overflow:hidden;',
      'box-shadow:0 18px 50px rgba(0,0,0,.3);display:none;',
      'opacity:0;transform:translateY(14px);transition:opacity .2s ease,transform .2s ease}',
      '.lz-panel.open{display:block}',
      '.lz-panel.shown{opacity:1;transform:translateY(0)}',
      '.lz-panel iframe{width:100%;height:100%;border:0;display:block;background:#fff}',
      '.lz-load{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;background:#fff}',
      '.lz-load i{width:34px;height:34px;border:3px solid ' + primary + '33;border-top-color:' + primary + ';',
      'border-radius:50%;animation:lz-spin .8s linear infinite;display:block}',
      '@keyframes lz-spin{to{transform:rotate(360deg)}}',
      // Tay nam keo doi kich thuoc. Khung neo o goc duoi, nen canh keo duoc
      // la canh TREN va canh phia trong (trai neu khung ben phai va nguoc
      // lai) - keo ra ngoai la to len. Chi hien tren man hinh lon.
      '.lz-rz{position:absolute;display:none;z-index:4;pointer-events:auto;touch-action:none}',
      '.lz-panel.desk .lz-rz{display:block}',
      '.lz-rz-t{top:0;left:0;right:0;height:7px;cursor:ns-resize}',
      '.lz-rz-s{top:0;bottom:0;' + RZ_SIDE + ':0;width:7px;cursor:ew-resize}',
      '.lz-rz-c{top:0;' + RZ_SIDE + ':0;width:22px;height:22px;cursor:' + RZ_CURSOR + '}',
      // Dau hieu thi giac de nguoi dung biet goc nay keo duoc (nam tren
      // thanh tieu de mau dam cua khung nen dung mau trang).
      '.lz-rz-c::after{content:"";position:absolute;top:7px;' + RZ_SIDE + ':7px;width:8px;height:8px;',
      'border-top:2px solid rgba(255,255,255,.6);border-' + RZ_SIDE + ':2px solid rgba(255,255,255,.6)}',
      '@media (max-width:640px){',
      '.lz-panel{inset:0;width:100%;height:100%;max-height:100%;border-radius:0}',
      '.lz-btn{height:56px;padding:0 8px;gap:0}.lz-btn .tx{display:none}',
      '.lz-btn .ic{width:40px;flex:0 0 40px}}',
      '@media (prefers-reduced-motion:reduce){.lz-btn,.lz-panel{transition:none}.lz-load i{animation:none}}'
    ].join('');
    var style = document.createElement('style');
    style.textContent = css;
    root.appendChild(style);

    var btn = document.createElement('button');
    btn.className = 'lz-btn';
    btn.type = 'button';
    btn.setAttribute('aria-label', 'Mở ' + appName);
    btn.setAttribute('aria-expanded', 'false');
    btn.innerHTML =
      '<span class="ic">' +
      '<span class="lz-dot"></span>' +
      (logo
        ? '<img src="' + esc(logo) + '" alt="" />'
        : '<svg viewBox="0 0 24 24"><path d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>') +
      '</span><span class="tx">' + esc(LABEL) + '</span>';

    var panel = document.createElement('div');
    panel.className = 'lz-panel';
    panel.setAttribute('role', 'dialog');
    panel.setAttribute('aria-label', appName);
    panel.innerHTML =
      '<div class="lz-load"><i></i></div>' +
      '<div class="lz-rz lz-rz-t"></div>' +
      '<div class="lz-rz lz-rz-s"></div>' +
      '<div class="lz-rz lz-rz-c" title="Kéo để đổi kích thước"></div>';

    root.appendChild(btn);
    root.appendChild(panel);

    // -------------------------------------------------------------
    // Buoc 3: iframe tao LUOI (chi khi bam mo lan dau) - khong ton bang
    // thong va khong anh huong diem Lighthouse cua cong neu nguoi dung
    // khong dung den tro ly.
    // -------------------------------------------------------------
    var frame = null;
    var isOpen = false;

    function buildUrl() {
      var u = BASE + '/?embed=1&host=' + encodeURIComponent(HOST_ORIGIN);
      if (SITE_KEY) u += '&key=' + encodeURIComponent(SITE_KEY);
      if (TITLE) u += '&title=' + encodeURIComponent(TITLE);
      if (GREETING) u += '&greeting=' + encodeURIComponent(GREETING);
      return u;
    }

    function ensureFrame() {
      if (frame) return;
      frame = document.createElement('iframe');
      frame.src = buildUrl();
      frame.title = appName;
      // allow="microphone": thieu thuoc tinh nay thi nut ghi am cau hoi
      // trong khung se bi trinh duyet chan vinh vien, khong the xin lai
      // quyen tu ben trong iframe.
      frame.setAttribute('allow', 'microphone; clipboard-write; autoplay');
      frame.setAttribute('referrerpolicy', 'strict-origin-when-cross-origin');
      // Luoi an toan: neu vi ly do nao do khong nhan duoc thong diep
      // 'lazibot:ready' (VD trang cha mo bang file:// nen khong co origin
      // hop le de gui ve), van phai bo vong xoay khi iframe da tai xong -
      // khong de nguoi dung nhin man hinh cho vinh vien.
      frame.addEventListener('load', hideLoader);
      panel.appendChild(frame);
    }

    function hideLoader() {
      var load = panel.querySelector('.lz-load');
      if (load && load.parentNode) load.parentNode.removeChild(load);
    }

    function post(type, extra) {
      if (!frame || !frame.contentWindow) return;
      var msg = { type: 'lazibot:' + type };
      if (extra) for (var k in extra) if (Object.prototype.hasOwnProperty.call(extra, k)) msg[k] = extra[k];
      try { frame.contentWindow.postMessage(msg, IFRAME_ORIGIN); } catch (e) {}
    }

    // =============================================================
    // Kich thuoc khung tro chuyen
    // Ba che do: 'default' (400x620), 'expanded' (bam nut phong to),
    // 'custom' (nguoi dung tu keo tay nam). Nut trong khung chi can biet
    // "dang nho" hay "dang lon" nen ca expanded lan custom deu bao 'large'.
    // Co y KHONG ghi nho kich thuoc vao localStorage: localStorage o day
    // thuoc ten mien cua CONG, ma ta da cam ket voi khach la khong dung
    // bo nho trinh duyet cua ho (xem tai lieu ban giao muc 9.1).
    // =============================================================
    var sizeMode = 'default';
    var curW = DEF_W, curH = DEF_H;

    function isDesktop() { return window.innerWidth > MOBILE_BP; }
    function maxW() { return Math.max(MIN_W, window.innerWidth - OFF_X * 2); }
    function maxH() { return Math.max(MIN_H, window.innerHeight - (OFF_Y + 92)); }
    function clamp(v, lo, hi) { return Math.min(Math.max(v, lo), hi); }

    function applySize() {
      // Duoi moc dien thoai: xoa het style noi tuyen de media query "tran
      // man hinh" duoc quyen quyet dinh (style noi tuyen thang media query).
      if (!isDesktop()) { panel.style.width = ''; panel.style.height = ''; return; }
      curW = clamp(curW, MIN_W, maxW());
      curH = clamp(curH, MIN_H, maxH());
      panel.style.width = curW + 'px';
      panel.style.height = curH + 'px';
    }

    function sendMode() {
      post('mode', { desktop: isDesktop(), state: sizeMode === 'default' ? 'default' : 'large' });
    }

    function setSizeMode(m) {
      sizeMode = m;
      if (m === 'default') { curW = DEF_W; curH = DEF_H; }
      else if (m === 'expanded') { curW = Math.min(EXP_MAX_W, maxW()); curH = maxH(); }
      applySize();
      sendMode();
    }

    // Keo tay nam. axis: 'x' = chi doi rong, 'y' = chi doi cao, 'xy' = ca hai.
    function startDrag(e, axis) {
      if (!isDesktop() || e.button !== 0) return;
      e.preventDefault();
      var handle = e.currentTarget;
      var sx = e.clientX, sy = e.clientY;
      var sw = panel.offsetWidth, sh = panel.offsetHeight;
      // Con tro di ngang qua iframe se bi iframe "nuot" su kien -> tat tam
      // pointer-events cua iframe trong luc keo, bat lai khi tha.
      if (frame) frame.style.pointerEvents = 'none';
      try { handle.setPointerCapture(e.pointerId); } catch (err) {}

      function move(ev) {
        if (axis !== 'y') curW = sw + (POSITION === 'left' ? ev.clientX - sx : sx - ev.clientX);
        if (axis !== 'x') curH = sh + (sy - ev.clientY);
        sizeMode = 'custom';
        applySize();
      }
      function up(ev) {
        handle.removeEventListener('pointermove', move);
        handle.removeEventListener('pointerup', up);
        handle.removeEventListener('pointercancel', up);
        try { handle.releasePointerCapture(ev.pointerId); } catch (err) {}
        if (frame) frame.style.pointerEvents = '';
        sendMode();
      }
      handle.addEventListener('pointermove', move);
      handle.addEventListener('pointerup', up);
      handle.addEventListener('pointercancel', up);
    }

    panel.querySelector('.lz-rz-t').addEventListener('pointerdown', function (e) { startDrag(e, 'y'); });
    panel.querySelector('.lz-rz-s').addEventListener('pointerdown', function (e) { startDrag(e, 'x'); });
    panel.querySelector('.lz-rz-c').addEventListener('pointerdown', function (e) { startDrag(e, 'xy'); });

    panel.classList.toggle('desk', isDesktop());
    applySize();

    // Doi kich thuoc cua so trinh duyet: giu khung khong tran ra ngoai man
    // hinh, va neu dang o che do phong to thi tinh lai cho vua khung moi.
    var rzTimer = null;
    window.addEventListener('resize', function () {
      clearTimeout(rzTimer);
      rzTimer = setTimeout(function () {
        panel.classList.toggle('desk', isDesktop());
        if (sizeMode === 'expanded' && isDesktop()) { curW = Math.min(EXP_MAX_W, maxW()); curH = maxH(); }
        applySize();
        sendMode();
      }, 150);
    });

    function open() {
      ensureFrame();
      isOpen = true;
      panel.classList.add('open');
      // Doi 1 khung hinh roi moi bat hieu ung, neu khong trinh duyet gop 2
      // thay doi lam mot va mat hieu ung xuat hien.
      requestAnimationFrame(function () { panel.classList.add('shown'); });
      btn.classList.remove('unread');
      btn.setAttribute('aria-expanded', 'true');
      btn.setAttribute('aria-label', 'Đóng ' + appName);
      post('opened');
      sendMode();
    }

    function close() {
      isOpen = false;
      panel.classList.remove('shown');
      btn.setAttribute('aria-expanded', 'false');
      btn.setAttribute('aria-label', 'Mở ' + appName);
      post('closed');
      setTimeout(function () { if (!isOpen) panel.classList.remove('open'); }, 200);
      try { btn.focus(); } catch (e) {}
    }

    function toggle() { if (isOpen) { close(); } else { open(); } }

    btn.addEventListener('click', toggle);

    // Esc dong khung - yeu cau tiep can co ban. Khong bat phim nao khac de
    // khong pha phim tat san co cua cong.
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) close();
    });

    // -------------------------------------------------------------
    // Buoc 4: nhan thong diep tu khung chat. Chi nhan dung origin cua
    // LAZIBOT - moi thong diep khac bi bo qua hoan toan.
    // -------------------------------------------------------------
    window.addEventListener('message', function (ev) {
      if (ev.origin !== IFRAME_ORIGIN) return;
      var t = ev.data && ev.data.type;
      if (t === 'lazibot:ready') {
        hideLoader();
        sendMode(); // khung vua tai xong: cho biet dang o man hinh lon hay nho
      } else if (t === 'lazibot:close') {
        close();
      } else if (t === 'lazibot:unread') {
        if (!isOpen) btn.classList.add('unread');
      } else if (t === 'lazibot:expand') {
        if (isDesktop()) setSizeMode('expanded');
      } else if (t === 'lazibot:restore') {
        setSizeMode('default');
      }
    });

    if (AUTO_OPEN) setTimeout(open, 800);

    // API cong khai cho cong tu dieu khien - VD dat 1 nut ngay trong bai
    // viet: <button onclick="LazibotWidget.open()">Hoi tro ly ao</button>
    window.LazibotWidget = {
      open: open,
      close: close,
      toggle: toggle,
      isOpen: function () { return isOpen; },
      expand: function () { if (isDesktop()) setSizeMode('expanded'); },
      restore: function () { setSizeMode('default'); },
      destroy: function () {
        try { mount.parentNode.removeChild(mount); } catch (e) {}
        window.__lazibotEmbedLoaded = false;
        try { delete window.LazibotWidget; } catch (e) {}
      }
    };
  }

  function start() {
    healthCheck().then(boot)['catch'](function (err) {
      // Im lang CO CHU DICH: backend chua san sang thi KHONG ve gi ca.
      // Tuyet doi khong de lai nut bam dan den man hinh loi.
      if (window.console && console.warn) {
        console.warn('[LAZIBOT] Tro ly ao tam thoi khong san sang, da bo qua hien thi.', err && err.message);
      }
    });
  }

  if (document.body) start();
  else document.addEventListener('DOMContentLoaded', start);
})();
