/* KOPILUWAK — Mobile / Android (PWA). Aman dimuat di semua halaman. */
(function () {
  'use strict';

  var doc = document.documentElement;

  /* 1. Deteksi mode aplikasi terpasang */
  if ((window.matchMedia && matchMedia('(display-mode: standalone)').matches) || window.navigator.standalone) {
    doc.classList.add('is-standalone');
  }

  /* 2. Service worker */
  if ('serviceWorker' in navigator && (location.protocol === 'https:' || location.hostname === 'localhost' || location.hostname === '127.0.0.1')) {
    window.addEventListener('load', function () {
      navigator.serviceWorker.register('/sw.js', { scope: '/' }).catch(function () {});
    });
  }

  function ready(fn) {
    if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {

    /* 3. Tabel -> kartu: isi data-label dari <th> */
    document.querySelectorAll('table.table').forEach(function (table) {
      if (table.hasAttribute('data-no-cards')) return;
      var heads = Array.prototype.map.call(table.querySelectorAll('thead th'), function (th) {
        return th.textContent.trim();
      });
      if (!heads.length) return;
      table.classList.add('m-cards');
      table.querySelectorAll('tbody tr').forEach(function (tr) {
        var cells = tr.children;
        Array.prototype.forEach.call(cells, function (td, i) {
          if (td.hasAttribute('colspan')) { td.classList.add('m-empty'); return; }
          var label = heads[i] || '';
          td.setAttribute('data-label', label);
          if (i === 0) td.classList.add('m-first');
          if (/^(aksi|opsi|action)$/i.test(label) || (label === '' && td.querySelector('.btn, button, a.btn'))) {
            td.classList.add('m-actions');
            td.classList.remove('m-first');
          }
        });
      });
    });

    /* 4. Bottom sheet */
    var backdrop = document.querySelector('.m-sheet-backdrop');
    function closeSheets() {
      document.querySelectorAll('.m-sheet.open').forEach(function (s) { s.classList.remove('open'); });
      if (backdrop) backdrop.classList.remove('open');
      doc.style.overflow = '';
    }
    document.querySelectorAll('[data-sheet-open]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var sheet = document.getElementById(btn.getAttribute('data-sheet-open'));
        if (!sheet) return;
        closeSheets();
        sheet.classList.add('open');
        if (backdrop) backdrop.classList.add('open');
        doc.style.overflow = 'hidden';
      });
    });
    if (backdrop) backdrop.addEventListener('click', closeSheets);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSheets(); });
    document.querySelectorAll('.m-sheet a').forEach(function (a) { a.addEventListener('click', closeSheets); });

    /* Geser ke bawah untuk menutup sheet */
    document.querySelectorAll('.m-sheet').forEach(function (sheet) {
      var startY = 0, dy = 0, dragging = false;
      sheet.addEventListener('touchstart', function (e) {
        if (sheet.scrollTop > 0) return;
        startY = e.touches[0].clientY; dy = 0; dragging = true; sheet.style.transition = 'none';
      }, { passive: true });
      sheet.addEventListener('touchmove', function (e) {
        if (!dragging) return;
        dy = e.touches[0].clientY - startY;
        if (dy > 0) sheet.style.transform = 'translateY(' + dy + 'px)';
      }, { passive: true });
      sheet.addEventListener('touchend', function () {
        if (!dragging) return;
        dragging = false; sheet.style.transition = ''; sheet.style.transform = '';
        if (dy > 90) closeSheets();
      });
    });

    /* 5. Sembunyikan bottom nav saat keyboard terbuka */
    var typing = 'input:not([type=checkbox]):not([type=radio]):not([type=file]):not([type=button]):not([type=submit]), textarea, select';
    document.addEventListener('focusin', function (e) { if (e.target.matches && e.target.matches(typing)) doc.classList.add('kbd-open'); });
    document.addEventListener('focusout', function () { setTimeout(function () {
      var a = document.activeElement;
      if (!a || !(a.matches && a.matches(typing))) doc.classList.remove('kbd-open');
    }, 60); });

    /* 6. Indikator offline */
    var off = document.querySelector('.m-offline');
    function net() { if (off) off.classList.toggle('show', !navigator.onLine); }
    window.addEventListener('online', net); window.addEventListener('offline', net); net();

    /* 7. Pasang aplikasi (Android / Chrome) */
    var deferred = null;
    var banner = document.querySelector('.m-install');
    var dismissed = false;
    try { dismissed = localStorage.getItem('kopi_install_dismissed') === '1'; } catch (e) {}

    window.addEventListener('beforeinstallprompt', function (e) {
      e.preventDefault();
      deferred = e;
      document.querySelectorAll('[data-pwa-install]').forEach(function (b) { b.hidden = false; b.style.display = ''; });
      if (banner && !dismissed) banner.classList.add('show');
    });
    window.addEventListener('appinstalled', function () {
      deferred = null;
      if (banner) banner.classList.remove('show');
      document.querySelectorAll('[data-pwa-install]').forEach(function (b) { b.hidden = true; });
    });
    document.querySelectorAll('[data-pwa-install]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        if (!deferred) return;
        deferred.prompt();
        deferred.userChoice.finally(function () { deferred = null; if (banner) banner.classList.remove('show'); closeSheets(); });
      });
    });
    var x = document.querySelector('.m-install-x');
    if (x) x.addEventListener('click', function () {
      if (banner) banner.classList.remove('show');
      try { localStorage.setItem('kopi_install_dismissed', '1'); } catch (e) {}
    });
  });
})();
