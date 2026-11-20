document.addEventListener('DOMContentLoaded', function () {
  var toast = document.getElementById('toast-msg');
  if (toast) {
    setTimeout(function () {
      toast.style.transition = 'opacity .5s, transform .5s';
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(120%)';
      setTimeout(function () { toast.remove(); }, 500);
    }, 4000);
  }

  document.querySelectorAll('.alert').forEach(function (el) {
    setTimeout(function () {
      el.style.transition = 'opacity .4s';
      el.style.opacity = '0';
      setTimeout(function () { el.remove(); }, 400);
    }, 6000);
  });

  document.querySelectorAll('.pay-opt input[type=radio]').forEach(function (radio) {
    radio.addEventListener('change', function () {
      document.querySelectorAll('.pay-opt').forEach(function (opt) { opt.classList.remove('selected'); });
      if (radio.checked) radio.closest('.pay-opt').classList.add('selected');
    });
  });

  document.addEventListener('click', function (e) {
    var sidebar = document.getElementById('sidebar');
    var toggle = document.querySelector('.menu-toggle');
    if (sidebar && sidebar.classList.contains('open')) {
      if (!sidebar.contains(e.target) && !(toggle && toggle.contains(e.target))) {
        sidebar.classList.remove('open');
      }
    }
  });

  document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.getAttribute('data-toggle-password'));
      var icon = btn.querySelector('i');
      if (!input) return;
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      icon.className = show ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    });
  });

  document.querySelectorAll('[data-carousel]').forEach(function (root) {
    var slides = root.querySelectorAll('.lp-slide');
    var dots = root.querySelectorAll('.lp-dot');
    var bar = root.querySelector('.lp-progress span');
    var counter = root.querySelector('.lp-counter-now');
    var total = slides.length;
    var current = 0;
    var startX = 0;

    function pad(n) {
      return (n < 10 ? '0' : '') + n;
    }

    function restartBar() {
      if (!bar) return;
      bar.classList.remove('run');
      void bar.offsetWidth;
      bar.classList.add('run');
    }

    function show(i) {
      current = (i + total) % total;
      slides.forEach(function (s, n) { s.classList.toggle('active', n === current); });
      dots.forEach(function (d, n) { d.classList.toggle('active', n === current); });
      if (counter) counter.textContent = pad(current + 1);
      restartBar();
    }

    if (bar) {
      bar.addEventListener('animationend', function () { show(current + 1); });
    }

    root.querySelectorAll('.lp-car-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        show(current + parseInt(btn.getAttribute('data-dir')));
      });
    });

    dots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        show(parseInt(dot.getAttribute('data-index')));
      });
    });

    root.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; }, { passive: true });
    root.addEventListener('touchend', function (e) {
      var diff = e.changedTouches[0].clientX - startX;
      if (Math.abs(diff) > 40) show(current + (diff < 0 ? 1 : -1));
    });

    slides[0].classList.remove('active');
    requestAnimationFrame(function () {
      requestAnimationFrame(function () { show(0); });
    });
  });

  var lightbox = document.getElementById('lightbox');
  if (lightbox) {
    var lbImg = document.getElementById('lightbox-img');
    var lbCap = document.getElementById('lightbox-caption');
    var lbDesc = document.getElementById('lightbox-desc');
    var lbPrev = lightbox.querySelector('.lightbox-nav.prev');
    var lbNext = lightbox.querySelector('.lightbox-nav.next');
    var lbItems = Array.prototype.slice.call(document.querySelectorAll('[data-lightbox]'));
    var lbIndex = 0;

    function lbShow(i) {
      if (!lbItems.length) return;
      lbIndex = (i + lbItems.length) % lbItems.length;
      var item = lbItems[lbIndex];
      lbImg.src = item.getAttribute('data-lightbox');
      lbImg.alt = item.getAttribute('data-caption') || '';
      lbCap.textContent = item.getAttribute('data-caption') || '';
      if (lbDesc) lbDesc.textContent = item.getAttribute('data-desc') || '';
    }

    var lbOpener = null;

    function openLightbox(i) {
      lbOpener = document.activeElement;
      lbShow(i);
      if (lbPrev) lbPrev.hidden = lbItems.length < 2;
      if (lbNext) lbNext.hidden = lbItems.length < 2;
      lightbox.classList.add('open');
      lightbox.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      var lbClose = lightbox.querySelector('.lightbox-close');
      if (lbClose) lbClose.focus();
    }

    function closeLightbox() {
      lightbox.classList.remove('open');
      lightbox.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (lbOpener && lbOpener.focus) lbOpener.focus();
    }

    lbItems.forEach(function (item, i) {
      item.addEventListener('click', function () { openLightbox(i); });
    });

    if (lbPrev) lbPrev.addEventListener('click', function (e) { e.stopPropagation(); lbShow(lbIndex - 1); });
    if (lbNext) lbNext.addEventListener('click', function (e) { e.stopPropagation(); lbShow(lbIndex + 1); });

    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target.closest('.lightbox-close')) closeLightbox();
    });

    document.addEventListener('keydown', function (e) {
      if (!lightbox.classList.contains('open')) return;
      if (e.key === 'Escape') closeLightbox();
      if (e.key === 'ArrowLeft') lbShow(lbIndex - 1);
      if (e.key === 'ArrowRight') lbShow(lbIndex + 1);
    });

    var lbStartX = 0;
    lightbox.addEventListener('touchstart', function (e) { lbStartX = e.touches[0].clientX; }, { passive: true });
    lightbox.addEventListener('touchend', function (e) {
      var diff = e.changedTouches[0].clientX - lbStartX;
      if (Math.abs(diff) > 50 && lbItems.length > 1) lbShow(lbIndex + (diff < 0 ? 1 : -1));
    }, { passive: true });
  }

  document.querySelectorAll('[data-preview]').forEach(function (input) {
    input.addEventListener('change', function () {
      var target = document.querySelector(input.getAttribute('data-preview'));
      if (!target || !input.files || !input.files[0]) return;
      var reader = new FileReader();
      reader.onload = function (e) {
        target.src = e.target.result;
        target.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    });
  });

  var userMenu = document.getElementById('userMenu');
  if (userMenu) {
    var userBtn = userMenu.querySelector('.user-btn');

    function setUserMenu(open) {
      userMenu.classList.toggle('open', open);
      userBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }

    userBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      setUserMenu(!userMenu.classList.contains('open'));
    });

    document.addEventListener('click', function (e) {
      if (!userMenu.contains(e.target)) setUserMenu(false);
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') setUserMenu(false);
    });
  }

  var nav = document.getElementById('lpNav');
  var navToggle = document.getElementById('lpToggle');
  if (nav && navToggle) {
    navToggle.addEventListener('click', function () {
      var open = nav.classList.toggle('open');
      navToggle.querySelector('i').className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
    });
    nav.querySelectorAll('.lp-menu a').forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('open');
        navToggle.querySelector('i').className = 'fa-solid fa-bars';
      });
    });
    var onScroll = function () {
      nav.classList.toggle('scrolled', window.scrollY > 10);
    };
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }
});

function qtyChange(delta) {
  var el = document.getElementById('qty-input');
  if (!el) return;
  var v = parseInt(el.value) + delta;
  var min = parseInt(el.min) || 1;
  var max = parseInt(el.max) || 9999;
  if (v >= min && v <= max) el.value = v;
}
