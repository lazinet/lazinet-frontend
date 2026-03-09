// Simple Omega landing interactions (no frameworks)
(function () {
  var preloader = document.getElementById("preloader");
  var scrollToTop = document.getElementById("scrollToTop");
  var navToggle = document.getElementById("navToggle");
  var body = document.body;

  // Hide preloader
  window.addEventListener("load", function () {
    if (preloader) {
      preloader.classList.add("hidden");
      setTimeout(function () {
        if (preloader && preloader.parentNode) {
          preloader.parentNode.removeChild(preloader);
        }
      }, 500);
    }
  });

  // Scroll to top visibility
  window.addEventListener("scroll", function () {
    if (!scrollToTop) return;
    if (window.scrollY > 260) {
      scrollToTop.classList.add("visible");
    } else {
      scrollToTop.classList.remove("visible");
    }
  });

  // Mobile navigation toggle
  if (navToggle) {
    navToggle.addEventListener("click", function () {
      body.classList.toggle("nav-open");
    });
  }

  // Close nav on link click (mobile)
  var navLinks = document.querySelectorAll("#mainNav a[href^='#']");
  navLinks.forEach(function (link) {
    link.addEventListener("click", function () {
      if (window.innerWidth <= 991) {
        body.classList.remove("nav-open");
      }
    });
  });

  // Smooth scroll enhancement (in case browser doesn't support CSS smooth behavior)
  function smoothScroll(targetId) {
    var target = document.getElementById(targetId);
    if (!target) return;
    var top = target.getBoundingClientRect().top + window.scrollY - 70;
    window.scrollTo({ top: top, behavior: "smooth" });
  }

  document.addEventListener("click", function (e) {
    var link = e.target.closest("a[href^='#']");
    if (!link) return;
    var href = link.getAttribute("href") || "";
    var id = href.substring(1);
    if (!id) return;
    var target = document.getElementById(id);
    if (!target) return;
    e.preventDefault();
    smoothScroll(id);
  });

  // Dynamic year in footer
  var yearEl = document.getElementById("year");
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
})();

