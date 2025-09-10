/**
* LAZINET Unified Optimized JavaScript
* Combined main.js + lazinet.js for better performance
* Enhanced with lazy loading and performance optimizations
*/

(function() {
  "use strict";

  // Performance tracking
  const startTime = performance.now();

  // Google Translate initialization function
  function googleTranslateElementInit() {
    if (typeof google !== 'undefined' && google.translate) {
      new google.translate.TranslateElement({
        pageLanguage: 'en',
        layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
        includedLanguages: "vi,zh-CN,hi,es,fr,ar,ru,pt,ja,ko,id,ms,th,my,km,lo,tl,de,it,fa,ur,pl,tr,uk,ro,nl,sv,cs,he,el,hu,da,fi",
        autoDisplay: false
      }, 'google_translate_element');
    }
  }
  
  // Make googleTranslateElementInit globally available
  window.googleTranslateElementInit = googleTranslateElementInit;

  // Lazy Loading Implementation
  function initLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const image = entry.target;
            image.src = image.dataset.src;
            image.classList.remove('lazy');
            image.classList.add('loaded');
            image.removeAttribute('data-src');
            imageObserver.unobserve(image);
          }
        });
      }, {
        rootMargin: '50px 0px',
        threshold: 0.01
      });

      lazyImages.forEach(img => imageObserver.observe(img));
    } else {
      // Fallback for browsers without IntersectionObserver
      lazyImages.forEach(img => {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
      });
    }
  }

  // Optimized scroll handling with throttling
  let ticking = false;
  function handleScroll() {
    if (!ticking) {
      requestAnimationFrame(() => {
        toggleScrolled();
        toggleScrollTop();
        navmenuScrollspy();
        ticking = false;
      });
      ticking = true;
    }
  }

  // Header scroll detection
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader?.classList.contains('sticky-top')) return;
    
    if (window.scrollY > 100) {
      selectBody.classList.add('scrolled');
    } else {
      selectBody.classList.remove('scrolled');
    }
  }

  // Mobile navigation toggle
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToggle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToggle);
  }

  // Hide mobile nav on same-page/hash links
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToggle();
      }
    });
  });

  // Toggle mobile nav dropdowns
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      e.preventDefault();
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
    });
  });

  // Scroll to top button
  let scrollTop = document.querySelector('.scroll-top');
  
  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  
  if (scrollTop) {
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  // Initialize AOS with optimization
  function aosInit() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        disable: 'mobile' // Disable on mobile for better performance
      });
    }
  }

  // Initialize GLightbox conditionally
  function initGLightbox() {
    if (typeof GLightbox !== 'undefined') {
      const glightbox = GLightbox({
        selector: '.glightbox',
        touchNavigation: true,
        loop: false,
        autoplayVideos: false
      });
    }
  }

  // Initialize PureCounter conditionally
  function initPureCounter() {
    if (typeof PureCounter !== 'undefined') {
      new PureCounter({
        duration: 2,
        delay: 10
      });
    }
  }

  // Initialize Swiper sliders with optimization
  function initSwiper() {
    if (typeof Swiper !== 'undefined') {
      document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
        try {
          let config = JSON.parse(
            swiperElement.querySelector(".swiper-config").innerHTML.trim()
          );
          
          // Add performance optimizations
          config.observer = true;
          config.observeParents = true;
          config.watchSlidesProgress = true;
          config.watchSlidesVisibility = true;
          
          new Swiper(swiperElement, config);
        } catch (e) {
          console.warn('Swiper configuration error:', e);
        }
      });
    }
  }

  // Initialize Isotope layout with optimization
  function initIsotope() {
    if (typeof Isotope !== 'undefined' && typeof imagesLoaded !== 'undefined') {
      document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
        let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
        let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
        let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

        let initIsotope;
        imagesLoaded(isotopeItem.querySelector('.isotope-container'), function() {
          initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
            itemSelector: '.isotope-item',
            layoutMode: layout,
            filter: filter,
            sortBy: sort,
            transitionDuration: '0.3s'
          });
        });

        isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filters) {
          filters.addEventListener('click', function() {
            const activeFilter = isotopeItem.querySelector('.isotope-filters .filter-active');
            if (activeFilter) activeFilter.classList.remove('filter-active');
            this.classList.add('filter-active');
            
            if (initIsotope) {
              initIsotope.arrange({
                filter: this.getAttribute('data-filter')
              });
            }
          }, false);
        });
      });
    }
  }

  // FAQ toggle functionality
  function initFAQ() {
    document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
      faqItem.addEventListener('click', () => {
        faqItem.parentNode.classList.toggle('faq-active');
      });
    });
  }

  // Navigation scrollspy with optimization
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    });
  }

  // Correct scrolling position for hash links
  function handleHashLinks() {
    if (window.location.hash) {
      const section = document.querySelector(window.location.hash);
      if (section) {
        setTimeout(() => {
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  }

  // Flip card functionality
  function initFlipCards() {
    document.querySelectorAll('.flip-card').forEach(function(card) {
      card.addEventListener('click', function() {
        card.classList.toggle('flipped');
      });
    });
  }

  // Form handling with optimization
  function initFormHandling() {
    // Contact form handling
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
      contactForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const button = document.getElementById('submit-btn');
        if (!button) return;

        button.textContent = 'Sending your message ...';
        button.style.color = '#ffff00';

        const formData = {
          name: document.getElementById('name')?.value || '',
          userType: document.getElementById('user-type')?.value || '',
          otherType: document.getElementById('other-type')?.value || '',
          phone: document.getElementById('phone')?.value || '',
          email: document.getElementById('email')?.value || '',
          subject: document.getElementById('subject')?.value || '',
          message: document.getElementById('message')?.value || ''
        };

        try {
          await fetch('https://script.google.com/macros/s/AKfycbz4t2LtIb1In7obXC_EKujEH3ZJGbvrz3uevuqVw4d-zErIj3Tf10QWwxlWH2-5IJ7KqA/exec', {
            method: 'POST',
            mode: 'no-cors',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData),
          });

          button.textContent = 'Sent successfully, thank you for contacting LAZINET!';
          button.style.color = '#00FF00';
          button.disabled = true;
        } catch (error) {
          button.textContent = `Error: ${error.message}`;
          button.style.color = '#FF3333';
        }
      });
    }

    // Newsletter form handling
    window.submitNewsletter = async function() {
      const emailInput = document.getElementById('email-newsletter');
      const button = document.getElementById('newsletter-btn');
      
      if (!emailInput || !button) return;
      
      const email = emailInput.value;
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      
      if (!emailRegex.test(email)) {
        button.textContent = 'Invalid email address!';
        button.style.color = 'red';
        return;
      }

      button.textContent = 'Subscribing...';
      button.style.color = '#ffff00';
      
      try {
        await fetch('https://script.google.com/macros/s/AKfycbz38pxrAZj7NprlijMQszw3k1tRL1YC8Phzlcl7v7mAZfd8_JGqOgAP6rusC2ZkrK2E/exec', {
          method: 'POST',
          mode: 'no-cors',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ email }),
        });

        button.textContent = 'Thank you! You are with LAZINET!';
        button.style.color = '#00FF00';
        button.disabled = true;
      } catch (error) {
        button.textContent = `Error: ${error.message}`;
        button.style.color = 'red';
      }
    };
  }

  // User type form handling
  function initUserTypeForm() {
    const userTypeSelect = document.getElementById('user-type');
    const otherInput = document.getElementById('other-type');
    
    if (userTypeSelect) {
      function updateSelectColor() {
        if (userTypeSelect.value === "") {
          userTypeSelect.style.color = "#595959";
        } else {
          userTypeSelect.style.color = "";
        }
      }

      updateSelectColor();
      userTypeSelect.addEventListener('change', updateSelectColor);
      
      userTypeSelect.addEventListener('change', function() {
        if (otherInput) {
          if (this.value === 'Other') {
            userTypeSelect.style.width = '30%';
            otherInput.style.display = 'inline-block';
            otherInput.setAttribute('required', '');
          } else {
            userTypeSelect.style.width = '100%';
            otherInput.style.display = 'none';
            otherInput.removeAttribute('required');
          }
        }
      });
    }
  }

  // Animated background for tech grid
  function initAnimatedBackground() {
    const canvas = document.getElementById("tech-grid");
    const section = document.getElementById("blog");
    
    if (!canvas || !section) return;
    
    const ctx = canvas.getContext("2d");
    let animationId;
    let nodes = [];
    let mouseNode = null;

    function resizeCanvas() {
      const dpr = window.devicePixelRatio || 1;
      canvas.width = section.offsetWidth * dpr;
      canvas.height = section.offsetHeight * dpr;
      canvas.style.width = `${section.offsetWidth}px`;
      canvas.style.height = `${section.offsetHeight}px`;
      ctx.scale(dpr, dpr);
      initializeNodes();
    }

    function randomRange(min, max) {
      return min + Math.random() * (max - min);
    }

    function initializeNodes() {
      nodes.length = 0;
      const width = canvas.width / (window.devicePixelRatio || 1);
      const height = canvas.height / (window.devicePixelRatio || 1);
      const area = width * height;
      const baseArea = 320 * 480;
      const baseNodes = 20;
      const numNodes = Math.floor(Math.sqrt(area / baseArea) * baseNodes);

      for (let i = 0; i < numNodes; i++) {
        nodes.push({
          x: randomRange(10, width - 10),
          y: randomRange(10, height - 10),
          vx: (Math.random() - 0.5) * 0.5,
          vy: (Math.random() - 0.5) * 0.5,
          color: getRandomColor(),
          brightness: Math.random()
        });
      }
    }

    function getRandomColor() {
      const colors = ["#ffffff", "#0000ff", "#ff6302"];
      return colors[Math.floor(Math.random() * colors.length)] + (Math.random() > 0.5 ? "cc" : "ff");
    }

    function updateNodes() {
      const width = canvas.width / (window.devicePixelRatio || 1);
      const height = canvas.height / (window.devicePixelRatio || 1);
      
      nodes.forEach(node => {
        node.x += node.vx;
        node.y += node.vy;

        if (node.x < 0 || node.x > width) node.vx *= -1;
        if (node.y < 0 || node.y > height) node.vy *= -1;
      });
    }

    function drawGrid() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      
      // Draw connections
      for (let i = 0; i < nodes.length; i++) {
        for (let j = i + 1; j < nodes.length; j++) {
          const dx = nodes[i].x - nodes[j].x;
          const dy = nodes[i].y - nodes[j].y;
          const distance = Math.sqrt(dx * dx + dy * dy);
          
          if (distance < 100) {
            ctx.strokeStyle = `rgba(255, 99, 0, ${0.1 - distance / 1000})`;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(nodes[i].x, nodes[i].y);
            ctx.lineTo(nodes[j].x, nodes[j].y);
            ctx.stroke();
          }
        }
      }

      // Draw nodes
      nodes.forEach(node => {
        ctx.fillStyle = node.color;
        ctx.beginPath();
        ctx.arc(node.x, node.y, 2, 0, Math.PI * 2);
        ctx.fill();
      });
    }

    function animate() {
      updateNodes();
      drawGrid();
      animationId = requestAnimationFrame(animate);
    }

    // Initialize
    resizeCanvas();
    animate();

    // Event listeners
    window.addEventListener("resize", resizeCanvas);
    
    // Cleanup function
    return () => {
      if (animationId) {
        cancelAnimationFrame(animationId);
      }
      window.removeEventListener("resize", resizeCanvas);
    };
  }

  // Main initialization function
  function init() {
    // Core functionality
    initLazyLoading();
    initFlipCards();
    initFormHandling();
    initUserTypeForm();
    
    // Enhanced functionality (conditional)
    setTimeout(() => {
      aosInit();
      initGLightbox();
      initPureCounter();
      initSwiper();
      initIsotope();
      initFAQ();
      
      // Initialize animated background
      const cleanupBackground = initAnimatedBackground();
      
      // Store cleanup function for potential use
      window.cleanupLazinetBackground = cleanupBackground;
    }, 100);

    // Event listeners
    document.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('load', () => {
      toggleScrolled();
      toggleScrollTop();
      navmenuScrollspy();
      handleHashLinks();
      
      // Performance logging
      const endTime = performance.now();
      console.log(`LAZINET optimized scripts loaded in ${Math.round(endTime - startTime)}ms`);
    });
  }

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();