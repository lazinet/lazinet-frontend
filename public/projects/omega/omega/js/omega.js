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
      afterClose: function () {
        // Hide the menu container after slicknav finishes its slideUp animation
        $('.slicknav_menu').hide();
      }
    });
  }

  // Connect custom hamburger to SlickNav menu
  // NOTE: .triggerHandler() vs .trigger() - must use triggerHandler to prevent
  // event bubbling back up to #mobileToggle (slicknav_btn is injected inside navbar-toggle)
  $('#mobileToggle').on('click', function (e) {
    if ($(e.target).closest('.slicknav_btn').length) return;
    var isOpen = $(this).hasClass('open');
    $(this).toggleClass('open');
    if (!isOpen) {
      // Opening: show container first so slicknav can animate nav inside it
      $('.slicknav_menu').show();
    }
    // Closing: slicknav slideUp nav, then afterClose callback hides container
    $('.slicknav_btn').triggerHandler('click');
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
  if (mainBtn && contactButtons) {
    document.addEventListener("click", function (e) {
      if (
        !e.target.closest(".lazinet-contact-floating") &&
        contactButtons.classList.contains("active")
      ) {
        contactButtons.classList.remove("active");
        mainBtn.classList.remove("hidden");
      }
    });
  }
});

// ========== Bong bóng chat - Floating buttons ===========

// ============================================================
// CLIENT-SIDE SEARCH
// ============================================================
(function () {
  // ----------------------------------------------------------
  // Search index – covers all pages & key sections
  // ----------------------------------------------------------
  var SEARCH_INDEX = [
    // Trang chủ
    { title: 'Trang chủ', desc: 'Giới thiệu tổng quan về Omega ERP', url: 'index.html', icon: 'fa-house', cat: 'Trang chủ', keywords: 'trang chủ omega erp giới thiệu' },

    // Về Omega
    { title: 'Về Omega', desc: 'Thông tin công ty, lịch sử hình thành', url: 've-omega.html', icon: 'fa-building', cat: 'Về Omega', keywords: 'về omega công ty lịch sử hình thành' },
    { title: 'Định vị & Năng lực', desc: 'Vị thế thương hiệu và năng lực cốt lõi', url: 've-omega.html#dinh-vi', icon: 'fa-bullseye', cat: 'Về Omega', keywords: 'định vị năng lực thương hiệu' },
    { title: 'Tầm nhìn & Sứ mệnh', desc: 'Chiến lược phát triển dài hạn của Omega', url: 've-omega.html#tam-nhin', icon: 'fa-eye', cat: 'Về Omega', keywords: 'tầm nhìn sứ mệnh chiến lược phát triển' },
    { title: 'Giá trị cốt lõi', desc: 'Những giá trị Omega cam kết mang lại', url: 've-omega.html#gia-tri', icon: 'fa-gem', cat: 'Về Omega', keywords: 'giá trị cốt lõi cam kết' },
    { title: 'Đội ngũ nhân sự', desc: 'Đội ngũ chuyên gia giàu kinh nghiệm', url: 've-omega.html#doi-ngu', icon: 'fa-users', cat: 'Về Omega', keywords: 'đội ngũ nhân sự chuyên gia kinh nghiệm' },

    // Giải pháp
    { title: 'Giải pháp ERP', desc: 'Giải pháp quản trị theo từng ngành nghề', url: 'giai-phap.html', icon: 'fa-lightbulb', cat: 'Giải pháp', keywords: 'giải pháp erp ngành nghề' },
    { title: 'Giải pháp ngành Nhựa', desc: 'ERP chuyên biệt cho doanh nghiệp sản xuất nhựa', url: 'giai-phap.html#nganh-nhua', icon: 'fa-industry', cat: 'Giải pháp', keywords: 'ngành nhựa sản xuất quản lý' },
    { title: 'Giải pháp ngành Gỗ – Nội thất', desc: 'ERP cho ngành chế biến gỗ và nội thất', url: 'giai-phap.html#nganh-go', icon: 'fa-tree', cat: 'Giải pháp', keywords: 'ngành gỗ nội thất chế biến' },
    { title: 'Giải pháp ngành F&B', desc: 'ERP cho nhà hàng, chuỗi thực phẩm & đồ uống', url: 'giai-phap.html#nganh-fb', icon: 'fa-utensils', cat: 'Giải pháp', keywords: 'ngành fb thực phẩm đồ uống nhà hàng' },
    { title: 'Giải pháp ngành FMCG', desc: 'ERP cho hàng tiêu dùng nhanh & phân phối', url: 'giai-phap.html#nganh-fmcg', icon: 'fa-box-open', cat: 'Giải pháp', keywords: 'fmcg hàng tiêu dùng phân phối' },
    { title: 'Giải pháp Thủy – Hải sản', desc: 'ERP cho chế biến và xuất khẩu thủy hải sản', url: 'giai-phap.html#nganh-thuy-san', icon: 'fa-fish', cat: 'Giải pháp', keywords: 'thủy sản hải sản chế biến xuất khẩu' },
    { title: 'Giải pháp Thiết bị Y tế', desc: 'ERP cho phân phối và quản lý thiết bị y tế', url: 'giai-phap.html#nganh-y-te', icon: 'fa-stethoscope', cat: 'Giải pháp', keywords: 'thiết bị y tế bệnh viện phân phối' },

    // Sản phẩm
    { title: 'Sản phẩm Omega', desc: 'Bộ phần mềm ERP và ứng dụng di động', url: 'san-pham.html', icon: 'fa-cubes', cat: 'Sản phẩm', keywords: 'sản phẩm phần mềm erp module' },
    { title: 'Phần mềm ERP', desc: 'Hệ thống quản trị tổng thể – kế toán, kho, bán hàng…', url: 'san-pham.html#phan-mem', icon: 'fa-desktop', cat: 'Sản phẩm', keywords: 'phần mềm erp kế toán kho bán hàng sản xuất' },
    { title: 'Mobile App', desc: 'Ứng dụng di động quản lý mọi lúc mọi nơi', url: 'san-pham.html#mobile-app', icon: 'fa-mobile-screen', cat: 'Sản phẩm', keywords: 'mobile app ứng dụng di động' },
    { title: 'Kế toán – Tài chính', desc: 'Module kế toán tổng hợp, công nợ, ngân hàng', url: 'san-pham.html#phan-mem', icon: 'fa-calculator', cat: 'Sản phẩm', keywords: 'kế toán tài chính công nợ ngân hàng' },
    { title: 'Quản lý Kho', desc: 'Module quản lý kho hàng, nhập xuất tồn', url: 'san-pham.html#phan-mem', icon: 'fa-warehouse', cat: 'Sản phẩm', keywords: 'kho hàng nhập xuất tồn quản lý kho' },
    { title: 'Quản lý Bán hàng (CRM)', desc: 'CRM, đơn hàng, báo giá, công nợ khách hàng', url: 'san-pham.html#phan-mem', icon: 'fa-handshake', cat: 'Sản phẩm', keywords: 'bán hàng crm đơn hàng báo giá khách hàng' },
    { title: 'Quản lý Sản xuất', desc: 'MRP, lệnh sản xuất, định mức nguyên liệu', url: 'san-pham.html#phan-mem', icon: 'fa-gears', cat: 'Sản phẩm', keywords: 'sản xuất mrp lệnh sản xuất định mức' },
    { title: 'Quản lý Nhân sự – HRM', desc: 'Tuyển dụng, lương thưởng, chấm công, đào tạo', url: 'san-pham.html#phan-mem', icon: 'fa-id-card', cat: 'Sản phẩm', keywords: 'nhân sự hrm tuyển dụng lương thưởng chấm công' },

    // Dịch vụ
    { title: 'Dịch vụ Omega', desc: 'Triển khai, đào tạo, tư vấn và hỗ trợ sau bán hàng', url: 'dich-vu.html', icon: 'fa-wrench', cat: 'Dịch vụ', keywords: 'dịch vụ triển khai đào tạo tư vấn hỗ trợ' },
    { title: 'Tư vấn & Triển khai', desc: 'Quy trình triển khai ERP chuyên nghiệp', url: 'dich-vu.html', icon: 'fa-comments', cat: 'Dịch vụ', keywords: 'tư vấn triển khai erp chuyên nghiệp' },
    { title: 'Đào tạo người dùng', desc: 'Khóa học vận hành phần mềm cho nhân viên', url: 'dich-vu.html', icon: 'fa-chalkboard-user', cat: 'Dịch vụ', keywords: 'đào tạo người dùng khóa học vận hành' },
    { title: 'Hỗ trợ kỹ thuật', desc: 'Hotline, remote support và bảo trì hệ thống', url: 'dich-vu.html', icon: 'fa-headset', cat: 'Dịch vụ', keywords: 'hỗ trợ kỹ thuật hotline bảo trì' },

    // Khách hàng
    { title: 'Khách hàng tiêu biểu', desc: '1000+ doanh nghiệp đang sử dụng Omega ERP', url: 'khach-hang.html', icon: 'fa-star', cat: 'Khách hàng', keywords: 'khách hàng tiêu biểu doanh nghiệp đối tác' },

    // Tin tức
    { title: 'Tin tức & Blog', desc: 'Cập nhật kiến thức ERP, chuyển đổi số', url: 'tin-tuc.html', icon: 'fa-newspaper', cat: 'Tin tức', keywords: 'tin tức blog kiến thức erp chuyển đổi số' },
    { title: 'Chuyển đổi số', desc: 'Xu hướng và kinh nghiệm chuyển đổi số doanh nghiệp', url: 'tin-tuc.html#chuyen-doi-so', icon: 'fa-rotate', cat: 'Tin tức', keywords: 'chuyển đổi số doanh nghiệp xu hướng' },
    { title: 'ERP – Quản trị', desc: 'Bài viết chuyên sâu về hệ thống ERP', url: 'tin-tuc.html#erp', icon: 'fa-chart-line', cat: 'Tin tức', keywords: 'erp quản trị bài viết chuyên sâu' },
    { title: 'Kế toán – Tài chính', desc: 'Kiến thức kế toán, thuế, tài chính doanh nghiệp', url: 'tin-tuc.html#ke-toan', icon: 'fa-file-invoice-dollar', cat: 'Tin tức', keywords: 'kế toán tài chính thuế kiến thức' },
    { title: 'Sự kiện Omega', desc: 'Hội thảo, webinar và sự kiện công nghệ', url: 'tin-tuc.html#su-kien', icon: 'fa-calendar-days', cat: 'Tin tức', keywords: 'sự kiện hội thảo webinar công nghệ' },
    { title: 'Tuyển dụng', desc: 'Cơ hội việc làm tại Omega', url: 'tin-tuc.html#tuyen-dung', icon: 'fa-briefcase', cat: 'Tin tức', keywords: 'tuyển dụng việc làm cơ hội' },

    // Liên hệ
    { title: 'Liên hệ & Tư vấn', desc: 'Đặt lịch tư vấn hoặc nhận báo giá miễn phí', url: 'lien-he.html', icon: 'fa-envelope', cat: 'Liên hệ', keywords: 'liên hệ tư vấn báo giá miễn phí' },
  ];

  // ----------------------------------------------------------
  // Helpers
  // ----------------------------------------------------------
  function normalize(str) {
    return (str || '').toLowerCase()
      .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
      .replace(/đ/g, 'd');
  }

  function search(query) {
    var q = normalize(query);
    if (q.length < 2) return [];
    var terms = q.split(/\s+/).filter(Boolean);
    var results = [];
    for (var i = 0; i < SEARCH_INDEX.length; i++) {
      var item = SEARCH_INDEX[i];
      var haystack = normalize(item.title + ' ' + item.desc + ' ' + item.keywords + ' ' + item.cat);
      var score = 0;
      for (var t = 0; t < terms.length; t++) {
        if (haystack.indexOf(terms[t]) !== -1) {
          score += (normalize(item.title).indexOf(terms[t]) !== -1) ? 2 : 1;
        }
      }
      if (score > 0) results.push({ item: item, score: score });
    }
    results.sort(function (a, b) { return b.score - a.score; });
    return results.slice(0, 8).map(function (r) { return r.item; });
  }

  function renderResults(items, query) {
    var $results = $('#searchResults');
    if (!items.length) {
      $results.html('<div class="search-no-results">Không tìm thấy kết quả cho "<strong>' + $('<span>').text(query).html() + '</strong>"</div>');
      return;
    }
    var html = '';
    items.forEach(function (item) {
      html += '<a class="search-result-item" href="' + item.url + '">' +
        '<div class="search-result-icon"><i class="fa-solid ' + item.icon + '"></i></div>' +
        '<div class="search-result-body">' +
          '<div class="search-result-title">' + $('<span>').text(item.title).html() + '</div>' +
          '<div class="search-result-desc">' + $('<span>').text(item.desc).html() + '</div>' +
        '</div>' +
        '<span class="search-result-badge">' + $('<span>').text(item.cat).html() + '</span>' +
      '</a>';
    });
    $results.html(html);
    selectedIndex = -1;
  }

  // ----------------------------------------------------------
  // Open / close overlay
  // ----------------------------------------------------------
  var $overlay = $('#searchOverlay');
  var $input   = $('#searchInput');

  function openSearch() {
    $overlay.addClass('active');
    $('body').css('overflow', 'hidden');
    setTimeout(function () { $input.focus(); }, 50);
  }

  function closeSearch() {
    $overlay.removeClass('active');
    $('body').css('overflow', '');
    $input.val('');
    $('#searchResults').empty();
    selectedIndex = -1;
  }

  $('#searchToggle').on('click', openSearch);
  $('#searchClose').on('click', closeSearch);

  // Click on backdrop closes overlay
  $overlay.on('click', function (e) {
    if ($(e.target).is($overlay)) closeSearch();
  });

  // ----------------------------------------------------------
  // Search input (debounced)
  // ----------------------------------------------------------
  var debounceTimer;
  var selectedIndex = -1;

  $input.on('input', function () {
    clearTimeout(debounceTimer);
    var q = $(this).val().trim();
    if (q.length < 2) { $('#searchResults').empty(); selectedIndex = -1; return; }
    debounceTimer = setTimeout(function () {
      renderResults(search(q), q);
    }, 150);
  });

  // ----------------------------------------------------------
  // Keyboard navigation inside overlay
  // ----------------------------------------------------------
  $input.on('keydown', function (e) {
    var $items = $('#searchResults .search-result-item');
    if (!$items.length) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      selectedIndex = Math.min(selectedIndex + 1, $items.length - 1);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      selectedIndex = Math.max(selectedIndex - 1, -1);
    } else if (e.key === 'Enter' && selectedIndex >= 0) {
      e.preventDefault();
      window.location.href = $items.eq(selectedIndex).attr('href');
      return;
    } else {
      return;
    }

    $items.removeClass('selected');
    if (selectedIndex >= 0) $items.eq(selectedIndex).addClass('selected');
  });

  // ----------------------------------------------------------
  // Global keyboard shortcuts
  // ----------------------------------------------------------
  document.addEventListener('keydown', function (e) {
    // Ctrl+K or Cmd+K  → open search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      $overlay.hasClass('active') ? closeSearch() : openSearch();
      return;
    }
    // Escape → close
    if (e.key === 'Escape' && $overlay.hasClass('active')) {
      closeSearch();
      return;
    }
    // '/' key when NOT typing in an input → open search
    if (e.key === '/' && !$overlay.hasClass('active')) {
      var tag = document.activeElement.tagName;
      if (tag !== 'INPUT' && tag !== 'TEXTAREA' && tag !== 'SELECT') {
        e.preventDefault();
        openSearch();
      }
    }
  });

})();
// ============================================================
// /CLIENT-SIDE SEARCH
// ============================================================