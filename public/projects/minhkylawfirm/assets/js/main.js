/**
 * MINH KY LAWFIRM – Main JavaScript
 * Handles: Preloader, Navbar, Slider, Lawyers panel,
 *          Scroll animations, FAQ, Mobile menu, Floating CTA
 */

/* ============================================================
   PRELOADER
   ============================================================ */
(function initPreloader() {
  const preloader = document.getElementById('preloader');
  if (!preloader) return;

  const progress = preloader.querySelector('.preloader-progress');
  let pct = 0;

  const tick = setInterval(() => {
    pct += Math.random() * 20;
    if (pct >= 100) { pct = 100; clearInterval(tick); }
    if (progress) progress.style.width = pct + '%';
  }, 180);

  const hide = () => {
    clearInterval(tick);
    if (progress) progress.style.width = '100%';
    setTimeout(() => preloader.classList.add('hidden'), 300);
  };

  if (document.readyState === 'complete') hide();
  else window.addEventListener('load', hide);
  setTimeout(hide, 3500); // fallback
})();

/* ============================================================
   NAVBAR – scroll state + mobile menu
   ============================================================ */
(function initNavbar() {
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');
  const isSolid = navbar.classList.contains('solid');

  // Scroll effect (only when not already solid)
  if (!isSolid) {
    const onScroll = () => {
      if (window.scrollY > 60) navbar.classList.add('scrolled');
      else navbar.classList.remove('scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // Hamburger toggle
  if (hamburger && mobileMenu) {
    hamburger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('open');
      hamburger.classList.toggle('open', isOpen);
      hamburger.setAttribute('aria-expanded', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
      if (!navbar.contains(e.target) && mobileMenu.classList.contains('open')) {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
      }
    });

    // Close on link click
    mobileMenu.querySelectorAll('a').forEach(a => {
      a.addEventListener('click', () => {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
      });
    });
  }

  // Active link
  const links = navbar.querySelectorAll('.nav-link');
  const currentPath = location.pathname.replace(/\/$/, '').split('/').pop() || 'index.html';
  links.forEach(link => {
    const href = link.getAttribute('href')?.split('/').pop() || '';
    if (href === currentPath) link.classList.add('active');
  });
})();

/* ============================================================
   SLIDER
   ============================================================ */
class Slider {
  constructor(wrapper) {
    this.wrapper = wrapper;
    this.track  = wrapper.querySelector('.slider-track');
    this.slides = Array.from(wrapper.querySelectorAll('.slider-slide'));
    this.dotsEl = wrapper.querySelector('.slider-dots');
    this.counterEl = wrapper.querySelector('.slider-counter');
    this.prevBtn = wrapper.querySelector('.slider-btn.prev');
    this.nextBtn = wrapper.querySelector('.slider-btn.next');
    this.current = 0;
    this.total = this.slides.length;
    this.autoTimer = null;
    this.AUTO_DELAY = 4500;

    if (this.total < 2) {
      if (this.prevBtn) this.prevBtn.style.display = 'none';
      if (this.nextBtn) this.nextBtn.style.display = 'none';
    }

    this._buildDots();
    this._bindEvents();
    this._update();
    this._startAuto();
  }

  _buildDots() {
    if (!this.dotsEl) return;
    this.dots = this.slides.map((_, i) => {
      const d = document.createElement('button');
      d.className = 'slider-dot';
      d.setAttribute('aria-label', `Ảnh ${i + 1}`);
      d.addEventListener('click', () => this.go(i));
      this.dotsEl.appendChild(d);
      return d;
    });
  }

  _bindEvents() {
    if (this.prevBtn) this.prevBtn.addEventListener('click', () => this.prev());
    if (this.nextBtn) this.nextBtn.addEventListener('click', () => this.next());

    // Touch / swipe
    let startX = 0, isDragging = false;
    this.wrapper.addEventListener('touchstart', e => { startX = e.touches[0].clientX; isDragging = true; }, { passive: true });
    this.wrapper.addEventListener('touchend', e => {
      if (!isDragging) return;
      const diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) diff > 0 ? this.next() : this.prev();
      isDragging = false;
    });

    // Pause auto on hover
    this.wrapper.addEventListener('mouseenter', () => this._stopAuto());
    this.wrapper.addEventListener('mouseleave', () => this._startAuto());

    // Pause videos when navigating away
  }

  go(index) {
    this.current = (index + this.total) % this.total;
    this._update();
    this._resetAuto();
  }

  next() { this.go(this.current + 1); }
  prev() { this.go(this.current - 1); }

  _update() {
    this.track.style.transform = `translateX(-${this.current * 100}%)`;
    if (this.dots) {
      this.dots.forEach((d, i) => d.classList.toggle('active', i === this.current));
    }
    if (this.counterEl) this.counterEl.textContent = `${this.current + 1} / ${this.total}`;

    // Pause all videos, play current if video
    this.slides.forEach((slide, i) => {
      const video = slide.querySelector('video');
      if (!video) return;
      if (i === this.current) {
        // don't autoplay video - let user control
      } else {
        video.pause();
      }
    });
  }

  _startAuto() {
    this._stopAuto();
    // Don't auto-advance if current slide has a video playing
    this.autoTimer = setInterval(() => {
      const currentSlide = this.slides[this.current];
      const video = currentSlide?.querySelector('video');
      if (video && !video.paused) return; // wait for video
      this.next();
    }, this.AUTO_DELAY);
  }

  _stopAuto() { clearInterval(this.autoTimer); }
  _resetAuto() { this._stopAuto(); this._startAuto(); }
}

function initSliders() {
  document.querySelectorAll('.slider-wrapper').forEach(el => new Slider(el));
}

/* ============================================================
   SCROLL ANIMATIONS (IntersectionObserver)
   ============================================================ */
function initScrollAnimations() {
  const els = document.querySelectorAll('.animate-on-scroll');
  if (!els.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  els.forEach(el => observer.observe(el));
}

/* ============================================================
   FAQ ACCORDION
   ============================================================ */
function initFAQ() {
  document.querySelectorAll('.faq-question').forEach(btn => {
    btn.addEventListener('click', () => {
      const isOpen = btn.classList.contains('open');
      // Close all
      document.querySelectorAll('.faq-question').forEach(q => {
        q.classList.remove('open');
        const ans = q.nextElementSibling;
        if (ans) ans.classList.remove('open');
      });
      // Toggle current
      if (!isOpen) {
        btn.classList.add('open');
        const ans = btn.nextElementSibling;
        if (ans) ans.classList.add('open');
      }
    });
  });
}

/* ============================================================
   LAWYERS PANEL
   ============================================================ */
function initLawyersPanel() {
  const grid = document.querySelector('.lawyers-grid');
  if (!grid) return;

  const cards = Array.from(grid.querySelectorAll('.lawyer-card'));
  const panels = Array.from(grid.querySelectorAll('.lawyer-profile-panel'));

  cards.forEach(card => {
    card.addEventListener('click', () => {
      const id = card.dataset.lawyerId;
      const panel = grid.querySelector(`.lawyer-profile-panel[data-lawyer-id="${id}"]`);
      if (!panel) return;

      const wasActive = card.classList.contains('active');

      // Close all
      cards.forEach(c => c.classList.remove('active'));
      panels.forEach(p => p.classList.remove('visible'));

      if (!wasActive) {
        card.classList.add('active');
        panel.classList.add('visible');
        // Scroll panel into view
        setTimeout(() => {
          panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);
      }
    });
  });
}

/* ============================================================
   CONTACT FORM (simple client-side)
   ============================================================ */
function initContactForm() {
  const form = document.getElementById('contact-form');
  if (!form) return;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = form.querySelector('[type="submit"]');
    const original = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Đang gửi…';

    // Simulate send (replace with actual backend/GAS endpoint)
    await new Promise(r => setTimeout(r, 1200));

    btn.textContent = 'Đã gửi! ✓';
    btn.style.background = '#22c55e';
    form.reset();
    setTimeout(() => { btn.disabled = false; btn.textContent = original; btn.style.background = ''; }, 4000);
  });
}

/* ============================================================
   CHATBOT (simple rule-based demo)
   ============================================================ */
function initChatbot(containerId, type) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const messagesEl = container.querySelector('.chat-messages');
  const input = container.querySelector('.chat-input-area input');
  const sendBtn = container.querySelector('.chat-send-btn');

  const companyQA = {
    'địa chỉ': 'Văn phòng Minh Kỳ tại: Số F2.2, Tầng 2, Tòa nhà Sabay Building, 38 Cộng Hòa, Phường Tân Sơn Nhất, TP.HCM.',
    'điện thoại': 'Số điện thoại: 0964 037 746 (ThS LS Lê Viết Kỳ)',
    'email': 'Email: levietkylaw@gmail.com',
    'giờ làm việc': 'Chúng tôi làm việc từ Thứ 2 – Thứ 7, 8:00 – 17:30. Ngoài giờ có thể liên hệ qua Zalo/Điện thoại.',
    'dịch vụ': 'Minh Kỳ cung cấp 8 nhóm dịch vụ pháp lý: tư vấn cá nhân & DN, tố tụng, pháp lý DN & FDI, M&A, lao động & XKLĐ, thuế, đại diện ngoài tố tụng, giải thể & phá sản. Xem chi tiết tại trang Dịch vụ.',
    'luật sư': 'Luật sư chính là ThS LS Lê Viết Kỳ – Giám đốc & Người sáng lập. Liên hệ trực tiếp: 0964 037 746.',
    'chi phí': 'Chi phí dịch vụ pháp lý phụ thuộc vào từng vụ việc cụ thể. Minh Kỳ cam kết minh bạch: Rõ việc – Rõ chi phí – Rõ trách nhiệm. Liên hệ để được tư vấn cụ thể.',
    'mã số thuế': 'Mã số thuế công ty: 0319365453',
  };

  const legalQA = {
    'thành lập công ty': 'Để thành lập công ty tại Việt Nam, bạn cần: (1) Chọn loại hình DN (TNHH, CP, HKD...), (2) Chuẩn bị hồ sơ đăng ký, (3) Nộp tại Sở KH&ĐT hoặc qua cổng điện tử. Minh Kỳ hỗ trợ toàn bộ quy trình. Liên hệ: 0964 037 746.',
    'hợp đồng': 'Hợp đồng có giá trị pháp lý khi đáp ứng đủ điều kiện theo Bộ luật Dân sự: chủ thể có năng lực, tự nguyện, nội dung không trái luật. Nên có công chứng với hợp đồng bất động sản.',
    'ly hôn': 'Thủ tục ly hôn tại Việt Nam có 2 hình thức: thuận tình (2 bên đồng ý) và đơn phương (1 bên). Cần có đơn, chứng nhận kết hôn, và giải quyết tài sản/con cái. Minh Kỳ hỗ trợ đại diện tại Tòa.',
    'lao động': 'Pháp luật lao động VN quy định quyền lợi NLĐ: hợp đồng lao động, lương, BHXH, nghỉ phép, sa thải... Nếu bị vi phạm, liên hệ ngay để được tư vấn.',
    'thừa kế': 'Thừa kế theo pháp luật VN có 2 hình thức: theo di chúc và theo pháp luật (theo hàng thừa kế). Tranh chấp thừa kế cần có luật sư hỗ trợ để bảo vệ quyền lợi hợp pháp.',
    'fdi': 'Nhà đầu tư nước ngoài (FDI) tại VN cần: (1) Xin chấp thuận chủ trương đầu tư, (2) Đăng ký đầu tư, (3) Thành lập DN. Minh Kỳ có chuyên môn sâu về pháp lý FDI.',
  };

  const qa = type === 'legal' ? legalQA : companyQA;

  function addMessage(text, type) {
    const msg = document.createElement('div');
    msg.className = `chat-msg ${type}`;
    msg.innerHTML = `<div class="chat-bubble">${text}</div>`;
    messagesEl.appendChild(msg);
    messagesEl.scrollTop = messagesEl.scrollHeight;
  }

  function botReply(userText) {
    const lower = userText.toLowerCase();
    let reply = null;

    for (const [keyword, answer] of Object.entries(qa)) {
      if (lower.includes(keyword)) { reply = answer; break; }
    }

    if (!reply) {
      if (type === 'legal') {
        reply = `Cảm ơn bạn đã hỏi về "<strong>${userText}</strong>". Đây là vấn đề pháp lý cần tư vấn chuyên sâu. Vui lòng liên hệ trực tiếp với ThS LS Lê Viết Kỳ qua:<br>📞 <a href="tel:0964037746" style="color:var(--color-gold)">0964 037 746</a><br>📧 levietkylaw@gmail.com`;
      } else {
        reply = `Cảm ơn câu hỏi của bạn về "${userText}". Để được hỗ trợ chi tiết nhất, vui lòng liên hệ trực tiếp:<br>📞 <a href="tel:0964037746" style="color:var(--color-gold)">0964 037 746</a> hoặc xem trang <a href="contacts.html" style="color:var(--color-gold)">Liên hệ</a>.`;
      }
    }

    setTimeout(() => addMessage(reply, 'bot'), 600);
  }

  function send() {
    const text = input.value.trim();
    if (!text) return;
    addMessage(text, 'user');
    input.value = '';
    botReply(text);
  }

  sendBtn.addEventListener('click', send);
  input.addEventListener('keydown', e => { if (e.key === 'Enter') send(); });
}

/* ============================================================
   COUNTER ANIMATION
   ============================================================ */
function initCounters() {
  const counters = document.querySelectorAll('[data-count]');
  if (!counters.length) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      const el = entry.target;
      const target = +el.dataset.count;
      const duration = 1800;
      const step = target / (duration / 16);
      let current = 0;
      const timer = setInterval(() => {
        current += step;
        if (current >= target) { current = target; clearInterval(timer); }
        el.textContent = Math.floor(current) + (el.dataset.suffix || '');
      }, 16);
      observer.unobserve(el);
    });
  }, { threshold: 0.5 });

  counters.forEach(el => observer.observe(el));
}

/* ============================================================
   INIT ALL
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  initSliders();
  initScrollAnimations();
  initFAQ();
  initLawyersPanel();
  initContactForm();
  initCounters();
  initChatbot('chatbot-company', 'company');
  initChatbot('chatbot-legal', 'legal');
});
