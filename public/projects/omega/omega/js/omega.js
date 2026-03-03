(function ($) {
  "use strict";

  /* ==============================
     01. Preloader
  ================================ */
  $(window).on('load', function () {
    setTimeout(function () {
      $('.preloader').addClass('hide');
    }, 800);
  });

  /* ==============================
     02. Sticky Header
  ================================ */
  var $header = $('header.main-header .header-sticky');
  var lastScroll = 0;

  $(window).on('scroll', function () {
    var scrollY = $(this).scrollTop();

    if (scrollY > 80) {
      $header.addClass('active');
    } else {
      $header.removeClass('active');
    }

    if (scrollY > lastScroll && scrollY > 300) {
      $header.addClass('hide');
    } else {
      $header.removeClass('hide');
    }
    lastScroll = scrollY;
  });

  /* ==============================
     03. Mobile Menu (SlickNav)
  ================================ */
  if ($('#menu').length) {
    $('#menu').slicknav({
      label: '',
      prependTo: '.responsive-menu',
      allowParentLinks: true,
    });
  }

  // Connect custom hamburger to SlickNav menu
  $('#mobileToggle').on('click', function () {
    $(this).toggleClass('open');
    $('.slicknav_btn').trigger('click');
  });

  /* ==============================
     04. WOW Animations
  ================================ */
  if (typeof WOW !== 'undefined') {
    new WOW({
      boxClass: 'wow',
      animateClass: 'animated',
      offset: 80,
      mobile: false,
      live: true
    }).init();
  }

  /* ==============================
     05. Counter Up
  ================================ */
  if ($('.count-up').length) {
    $('.count-up').counterUp({
      delay: 10,
      time: 1500
    });
  }

  /* ==============================
     06. Testimonials / Logo Slider (Swiper)
  ================================ */
  if ($('.client-swiper').length) {
    new Swiper('.client-swiper .swiper', {
      slidesPerView: 2,
      spaceBetween: 24,
      loop: true,
      autoplay: { delay: 2500, disableOnInteraction: false },
      breakpoints: {
        480: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 5 },
      }
    });
  }

  if ($('.news-swiper').length) {
    new Swiper('.news-swiper .swiper', {
      slidesPerView: 1,
      spaceBetween: 24,
      loop: true,
      autoplay: { delay: 4000 },
      pagination: { el: '.news-pagination', clickable: true },
      breakpoints: {
        640: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
      }
    });
  }

  if ($('.testimonial-slider').length) {
    new Swiper('.testimonial-slider .swiper', {
      slidesPerView: 1,
      spaceBetween: 30,
      loop: true,
      autoplay: { delay: 5000 },
      pagination: { el: '.testimonial-pagination', clickable: true },
      navigation: {
        nextEl: '.testimonial-button-next',
        prevEl: '.testimonial-button-prev',
      },
      breakpoints: {
        1024: { slidesPerView: 2 },
      }
    });
  }

  /* ==============================
     07. Video Popup (Magnific)
  ================================ */
  if ($('.popup-video').length && typeof $.fn.magnificPopup !== 'undefined') {
    $('.popup-video').magnificPopup({
      type: 'iframe',
      iframe: {
        markup: '<div class="mfp-iframe-scaler">' +
          '<div class="mfp-close"></div>' +
          '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
          '</div>',
        patterns: {
          youtube: { index: 'youtube.com/', id: 'v=', src: '//www.youtube.com/embed/%id%?autoplay=1' }
        },
        srcAction: 'iframe_src'
      }
    });
  }

  /* ==============================
     08. Smooth Scroll to anchor
  ================================ */
  $('a[href^="#"]').not('[href="#"]').on('click', function (e) {
    var target = $(this.hash);
    if (target.length) {
      e.preventDefault();
      $('html, body').animate({ scrollTop: target.offset().top - 80 }, 600, 'swing');
    }
  });

  /* ==============================
     09. Hero Canvas – Particle Network
  ================================ */
  var canvas = document.getElementById('tech-canvas');
  if (canvas) {
    var ctx = canvas.getContext('2d');
    var W, H, particles = [], RAF;

    function resize() {
      W = canvas.width = canvas.offsetWidth;
      H = canvas.height = canvas.offsetHeight;
    }
    window.addEventListener('resize', resize);
    resize();

    var PARTICLE_COUNT = 80;
    var MAX_DIST = 120;

    for (var i = 0; i < PARTICLE_COUNT; i++) {
      particles.push({
        x: Math.random() * W,
        y: Math.random() * H,
        vx: (Math.random() - 0.5) * 0.6,
        vy: (Math.random() - 0.5) * 0.6,
        r: Math.random() * 2 + 1
      });
    }

    function drawParticles() {
      ctx.clearRect(0, 0, W, H);

      // Draw connections
      for (var i = 0; i < particles.length; i++) {
        for (var j = i + 1; j < particles.length; j++) {
          var dx = particles[i].x - particles[j].x;
          var dy = particles[i].y - particles[j].y;
          var dist = Math.sqrt(dx * dx + dy * dy);
          if (dist < MAX_DIST) {
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(0,166,81,' + (1 - dist / MAX_DIST) * 0.35 + ')';
            ctx.lineWidth = 0.8;
            ctx.moveTo(particles[i].x, particles[i].y);
            ctx.lineTo(particles[j].x, particles[j].y);
            ctx.stroke();
          }
        }
      }

      // Draw dots
      particles.forEach(function (p) {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = 'rgba(126,217,87,0.7)';
        ctx.fill();

        p.x += p.vx;
        p.y += p.vy;

        if (p.x < 0 || p.x > W) p.vx *= -1;
        if (p.y < 0 || p.y > H) p.vy *= -1;
      });

      RAF = requestAnimationFrame(drawParticles);
    }
    drawParticles();
  }

  /* ==============================
     10. Ticker duplicate
  ================================ */
  var $track = $('.ticker-track');
  if ($track.length) {
    var $clone = $track.html();
    $track.append($clone);
  }

  /* ==============================
     11. Contact Form Handler (Google App Script ready)
  ================================ */
  var $form = $('#omega-contact-form');
  if ($form.length) {
    $form.on('submit', function (e) {
      e.preventDefault();

      var $btn = $form.find('[type="submit"]');
      var $msg = $form.find('.form-message');
      $btn.prop('disabled', true).text('Đang gửi...');

      var data = {
        name:    $form.find('[name="name"]').val(),
        phone:   $form.find('[name="phone"]').val(),
        email:   $form.find('[name="email"]').val(),
        company: $form.find('[name="company"]').val(),
        message: $form.find('[name="message"]').val(),
        timestamp: new Date().toISOString()
      };

      // TODO: Replace with Google App Script URL
      var GAS_URL = '';

      if (GAS_URL) {
        $.ajax({
          url: GAS_URL,
          method: 'POST',
          data: JSON.stringify(data),
          contentType: 'application/json',
          success: function () {
            $msg.html('<div class="alert-success-omega">Cảm ơn! Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.</div>');
            $form[0].reset();
          },
          error: function () {
            $msg.html('<div class="alert-error-omega">Có lỗi xảy ra, vui lòng thử lại hoặc liên hệ qua điện thoại.</div>');
          },
          complete: function () {
            $btn.prop('disabled', false).text('Gửi yêu cầu tư vấn');
          }
        });
      } else {
        // Demo mode: just show success
        setTimeout(function () {
          $msg.html('<div class="alert-success-omega">Cảm ơn! Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.</div>');
          $form[0].reset();
          $btn.prop('disabled', false).text('Gửi yêu cầu tư vấn');
        }, 800);
      }
    });
  }

  /* ==============================
     12. Tab switching (Products page)
  ================================ */
  $(document).on('click', '.tab-btn', function () {
    var target = $(this).data('tab');
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    $('.tab-content').removeClass('active').hide();
    $('#' + target).addClass('active').fadeIn(300);
  });

  /* ==============================
     13. Back to top
  ================================ */
  var $backTop = $('#back-to-top');
  if ($backTop.length) {
    $(window).on('scroll', function () {
      if ($(this).scrollTop() > 500) {
        $backTop.addClass('show');
      } else {
        $backTop.removeClass('show');
      }
    });
    $backTop.on('click', function () {
      $('html, body').animate({ scrollTop: 0 }, 500);
    });
  }

})(jQuery);


// GOOGLE TRANSLATION
function googleTranslateElementInit() {
  new google.translate.TranslateElement(
    {
      pageLanguage: "vi",
      layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
      includedLanguages:
        "en,zh-CN,hi,es,fr,ar,ru,pt,ja,ko,id,ms,th,my,km,lo,tl,de,it,fa,ur,pl,tr,uk,ro,nl,sv,cs,he,el,hu,da,fi",
      autoDisplay: false,
    },
    "google_translate_element"
  );
}
// // Ẩn Google Translate Banner Frame (top bar) bằng JS - ổn định hơn CSS
// function hideGoogleTranslateBanner() {
//   // Tìm banner frame
//   const bannerFrame = document.querySelector('.goog-te-banner-frame');
//   const skipTranslate = document.querySelector('.skiptranslate');

//   if (bannerFrame) {
//     bannerFrame.style.display = 'none';
//     bannerFrame.style.visibility = 'hidden';
//     bannerFrame.style.height = '0';
//   }

//   if (skipTranslate) {
//     skipTranslate.style.display = 'none';
//   }

//   // Reset top của body (Google thêm inline style top:40px !important)
//   document.body.style.top = '0px !important';
//   document.body.style.position = 'static !important'; // hoặc 'relative' nếu header fixed

//   // Nếu header của bạn fixed/sticky, điều chỉnh margin-top nếu cần
//   // Ví dụ: document.querySelector('.main-header').style.marginTop = '0';
// }

// // Chạy ngay lập tức
// hideGoogleTranslateBanner();

// // Chạy lại sau 1-2 giây (vì widget load async)
// setTimeout(hideGoogleTranslateBanner, 1000);
// setTimeout(hideGoogleTranslateBanner, 3000); // an toàn hơn

// // Theo dõi thay đổi DOM (MutationObserver) - tốt nhất cho dynamic insert
// const observer = new MutationObserver(() => {
//   hideGoogleTranslateBanner();
// });

// // Quan sát body hoặc document
// observer.observe(document.body, { childList: true, subtree: true });



// BONG BÓNG CHAT - FLOATING BUTTONS ================================================
document.addEventListener("DOMContentLoaded", function () {
  const mainBtn = document.querySelector(".lazinet-contact-main-btn");
  const contactButtons = document.querySelector(".lazinet-contact-buttons");
  const contactBtns = document.querySelectorAll(".lazinet-contact-btn");

  // Main button click - Ẩn nút chính, hiện 4 nút con
  if (mainBtn) {
    mainBtn.addEventListener("click", function () {
      // Ẩn nút chính
      this.classList.add("hidden");
      // Hiện 4 nút con
      contactButtons.classList.add("active");
    });
  }

  // Xử lý click cho từng nút con
  contactBtns.forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      const type = this.classList[1]; // phone, zalo, messenger, ai-bot

      // Thực hiện chức năng tương ứng
      switch (type) {
        case "phone":
          window.open("tel:+84-908303609");
          break;
        case "zalo":
          window.open("http://zalo.me/0908303609", "_blank");
          break;
        case "messenger":
          window.open("https://m.me/112977021025347", "_blank");
          break;
        case "ai-bot":
          openAIChat();
          break;
      }

      // Sau khi click: Ẩn 4 nút con, hiện lại nút chính
      contactButtons.classList.remove("active");
      mainBtn.classList.remove("hidden");
    });
  });

function openAIChat() {
    // Thay đường dẫn dưới đây bằng link thật của file bizcard của bạn
    const bizcardUrl = "https://lazinet.com/bizcards/hoangminhphung-lazinet.html?chat=true";
    window.open(bizcardUrl, "_blank");
}

  // Optional: Đóng menu khi click ra ngoài
  document.addEventListener("click", function (e) {
    if (
      !e.target.closest(".lazinet-contact-floating") &&
      contactButtons.classList.contains("active")
    ) {
      contactButtons.classList.remove("active");
      mainBtn.classList.remove("hidden");
    }
  });
});

// ========== Bong bóng chat - Floating buttons ===========