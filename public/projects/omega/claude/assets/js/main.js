/**
 * OMEGA ERP - Main JavaScript
 * Pure vanilla JS: Navigation, animations, counters, sliders, particles
 */

(function () {
  'use strict';

  // ============================================================
  // DOM Ready
  // ============================================================
  document.addEventListener('DOMContentLoaded', function () {
    initPreloader();
    initNavigation();
    initMobileNav();
    initScrollTop();
    initScrollAnimations();
    initCounters();
    initTestimonialSlider();
    initParticles();
    initHeroBars();
    initEcoTabs();
    initProductFilter();
    initIndustryTabs();
    initContactForm();
    initNewsFilter();
  });

  // ============================================================
  // Preloader
  // ============================================================
  function initPreloader() {
    var preloader = document.getElementById('preloader');
    if (!preloader) return;
    window.addEventListener('load', function () {
      setTimeout(function () {
        preloader.classList.add('hidden');
        setTimeout(function () {
          preloader.remove();
        }, 600);
      }, 500);
    });
  }

  // ============================================================
  // Navigation
  // ============================================================
  function initNavigation() {
    var header = document.querySelector('.header');
    if (!header) return;

    // Sticky on scroll
    function onScroll() {
      if (window.scrollY > 60) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Active nav link
    var currentPage = window.location.pathname.split('/').pop() || 'index.html';
    var navLinks = document.querySelectorAll('.nav-menu a');
    navLinks.forEach(function (link) {
      var href = link.getAttribute('href');
      if (href === currentPage || (currentPage === '' && href === 'index.html')) {
        link.classList.add('active');
      }
    });
  }

  // ============================================================
  // Mobile Navigation
  // ============================================================
  function initMobileNav() {
    var hamburger = document.querySelector('.hamburger');
    var mobileNav = document.querySelector('.mobile-nav');
    var overlay = document.querySelector('.overlay');
    var closeBtn = document.querySelector('.close-nav');

    function openNav() {
      if (mobileNav) mobileNav.classList.add('open');
      if (overlay) overlay.classList.add('open');
      if (hamburger) hamburger.classList.add('open');
      document.body.style.overflow = 'hidden';
    }

    function closeNav() {
      if (mobileNav) mobileNav.classList.remove('open');
      if (overlay) overlay.classList.remove('open');
      if (hamburger) hamburger.classList.remove('open');
      document.body.style.overflow = '';
    }

    if (hamburger) hamburger.addEventListener('click', openNav);
    if (closeBtn) closeBtn.addEventListener('click', closeNav);
    if (overlay) overlay.addEventListener('click', closeNav);
  }

  // ============================================================
  // Scroll to Top
  // ============================================================
  function initScrollTop() {
    var btn = document.querySelector('.scroll-top');
    if (!btn) return;

    window.addEventListener('scroll', function () {
      if (window.scrollY > 400) {
        btn.classList.add('show');
      } else {
        btn.classList.remove('show');
      }
    }, { passive: true });

    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // ============================================================
  // Scroll Animations (Intersection Observer)
  // ============================================================
  function initScrollAnimations() {
    var elements = document.querySelectorAll('.fade-up, .fade-left, .fade-right');
    if (!elements.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });

    elements.forEach(function (el) { observer.observe(el); });
  }

  // ============================================================
  // Number Counters
  // ============================================================
  function initCounters() {
    var counters = document.querySelectorAll('.stat-num, .counter');
    if (!counters.length) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { observer.observe(el); });
  }

  function animateCounter(el) {
    var text = el.textContent;
    var suffix = text.replace(/[0-9]/g, '');
    var target = parseInt(text.replace(/[^0-9]/g, ''), 10);
    if (isNaN(target)) return;

    var start = 0;
    var duration = 1800;
    var startTime = null;

    function step(timestamp) {
      if (!startTime) startTime = timestamp;
      var progress = Math.min((timestamp - startTime) / duration, 1);
      var ease = 1 - Math.pow(1 - progress, 3); // ease-out cubic
      var current = Math.floor(ease * target);
      el.textContent = current + suffix;
      if (progress < 1) {
        requestAnimationFrame(step);
      } else {
        el.textContent = target + suffix;
      }
    }
    requestAnimationFrame(step);
  }

  // ============================================================
  // Testimonial Slider
  // ============================================================
  function initTestimonialSlider() {
    var track = document.querySelector('.testimonial-track');
    var cards = document.querySelectorAll('.testimonial-card');
    var dots = document.querySelectorAll('.slider-dot');
    var prevBtn = document.querySelector('.slider-btn.prev');
    var nextBtn = document.querySelector('.slider-btn.next');
    if (!track || !cards.length) return;

    var current = 0;
    var total = cards.length;
    var autoPlay;

    function goTo(index) {
      current = (index + total) % total;
      track.style.transform = 'translateX(-' + (current * 100) + '%)';
      dots.forEach(function (d, i) { d.classList.toggle('active', i === current); });
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }
    function startAuto() { autoPlay = setInterval(next, 5000); }
    function stopAuto() { clearInterval(autoPlay); }

    if (nextBtn) nextBtn.addEventListener('click', function () { stopAuto(); next(); startAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', function () { stopAuto(); prev(); startAuto(); });
    dots.forEach(function (dot, i) {
      dot.addEventListener('click', function () { stopAuto(); goTo(i); startAuto(); });
    });

    goTo(0);
    startAuto();

    // Touch swipe
    var touchStartX = 0;
    track.addEventListener('touchstart', function (e) { touchStartX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', function (e) {
      var diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) { stopAuto(); diff > 0 ? next() : prev(); startAuto(); }
    });
  }

  // ============================================================
  // Hero Particles
  // ============================================================
  function initParticles() {
    var container = document.querySelector('.hero-particles');
    if (!container) return;

    for (var i = 0; i < 15; i++) {
      createParticle(container);
    }
  }

  function createParticle(container) {
    var el = document.createElement('div');
    el.className = 'hero-particle';
    var size = Math.random() * 80 + 20;
    el.style.cssText = [
      'width:' + size + 'px',
      'height:' + size + 'px',
      'left:' + (Math.random() * 100) + '%',
      'animation-duration:' + (Math.random() * 20 + 15) + 's',
      'animation-delay:' + (Math.random() * 10) + 's',
    ].join(';');
    container.appendChild(el);
  }

  // ============================================================
  // Hero Animated Bars
  // ============================================================
  function initHeroBars() {
    var bars = document.querySelectorAll('.hero-bar');
    var heights = [40, 70, 55, 85, 60, 90, 50, 75];
    bars.forEach(function (bar, i) {
      bar.style.height = (heights[i % heights.length] || 60) + '%';
      bar.style.animationDelay = (i * 0.1) + 's';
    });
  }

  // ============================================================
  // Ecosystem Tabs (Homepage)
  // ============================================================
  function initEcoTabs() {
    var tabs = document.querySelectorAll('.eco-tab');
    var cards = document.querySelectorAll('.eco-card');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var filter = tab.dataset.filter;
        cards.forEach(function (card) {
          if (filter === 'all' || card.dataset.category === filter) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // ============================================================
  // Product Filter (Products page)
  // ============================================================
  function initProductFilter() {
    var filterBtns = document.querySelectorAll('.filter-btn');
    var productCards = document.querySelectorAll('.product-card');
    if (!filterBtns.length) return;

    filterBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        filterBtns.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var filter = btn.dataset.filter;
        productCards.forEach(function (card) {
          if (filter === 'all' || card.dataset.type === filter) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // ============================================================
  // Industry Tabs (Customers page)
  // ============================================================
  function initIndustryTabs() {
    var tabs = document.querySelectorAll('.industry-tab');
    var clientCards = document.querySelectorAll('.client-logo-card');
    if (!tabs.length) return;

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        tabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var filter = tab.dataset.filter;
        clientCards.forEach(function (card) {
          if (filter === 'all' || card.dataset.industry === filter) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // ============================================================
  // News Filter
  // ============================================================
  function initNewsFilter() {
    var filterBtns = document.querySelectorAll('.news-filter .filter-btn');
    var newsCards = document.querySelectorAll('.news-card');
    if (!filterBtns.length) return;

    filterBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        filterBtns.forEach(function (b) { b.classList.remove('active'); });
        btn.classList.add('active');
        var filter = btn.dataset.filter;
        newsCards.forEach(function (card) {
          if (filter === 'all' || card.dataset.category === filter) {
            card.style.display = '';
          } else {
            card.style.display = 'none';
          }
        });
      });
    });
  }

  // ============================================================
  // Contact Form
  // ============================================================
  function initContactForm() {
    var forms = document.querySelectorAll('.contact-form, .cta-form');
    forms.forEach(function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('.btn-submit');
        var origText = btn ? btn.textContent : '';
        if (btn) {
          btn.textContent = 'Đang gửi...';
          btn.disabled = true;
        }
        setTimeout(function () {
          showToast('Cảm ơn bạn! Chúng tôi sẽ liên hệ sớm nhất.');
          form.reset();
          if (btn) {
            btn.textContent = origText;
            btn.disabled = false;
          }
        }, 1500);
      });
    });
  }

  function showToast(message) {
    var toast = document.createElement('div');
    toast.style.cssText = [
      'position:fixed', 'bottom:30px', 'right:30px', 'z-index:9999',
      'background:linear-gradient(135deg,#01a652,#7ed957)',
      'color:white', 'padding:16px 28px', 'border-radius:12px',
      'box-shadow:0 8px 32px rgba(1,166,82,0.35)',
      'font-weight:600', 'font-size:15px',
      'transform:translateY(20px)', 'opacity:0',
      'transition:all 0.4s ease'
    ].join(';');
    toast.textContent = message;
    document.body.appendChild(toast);

    setTimeout(function () { toast.style.transform = 'translateY(0)'; toast.style.opacity = '1'; }, 50);
    setTimeout(function () {
      toast.style.transform = 'translateY(20px)'; toast.style.opacity = '0';
      setTimeout(function () { toast.remove(); }, 400);
    }, 4000);
  }

  // ============================================================
  // Video Modal
  // ============================================================
  var videoBtn = document.querySelector('.hero-video-btn');
  if (videoBtn) {
    videoBtn.addEventListener('click', function () {
      var modal = document.createElement('div');
      modal.style.cssText = [
        'position:fixed', 'inset:0', 'z-index:9000',
        'background:rgba(0,0,0,0.92)',
        'display:flex', 'align-items:center', 'justify-content:center',
        'cursor:pointer'
      ].join(';');
      modal.innerHTML = '<div style="text-align:center;color:white;padding:40px"><div style="font-size:72px;margin-bottom:24px">▶</div><p style="font-size:20px;opacity:0.8">Video Demo OMEGA ERP</p><p style="font-size:14px;opacity:0.5;margin-top:12px">Click để đóng</p></div>';
      modal.addEventListener('click', function () { modal.remove(); });
      document.body.appendChild(modal);
    });
  }

})();
