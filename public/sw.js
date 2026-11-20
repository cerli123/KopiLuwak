/* KOPILUWAK — Service Worker. Hanya menyimpan aset statis; halaman, login,
   keranjang, pembayaran & admin SELALU diambil langsung dari server. */
var VERSION = 'kopi-v2';
var STATIC = 'kopi-static-' + VERSION;
var OFFLINE_URL = '/offline.html';
var PRECACHE = [OFFLINE_URL, '/css/app.css', '/css/pro.css', '/css/mobile.css', '/js/app.js', '/js/mobile.js', '/icons/icon-192.png'];
var CDN = ['fonts.googleapis.com', 'fonts.gstatic.com', 'cdnjs.cloudflare.com'];

self.addEventListener('install', function (e) {
  e.waitUntil(caches.open(STATIC).then(function (c) {
    return Promise.all(PRECACHE.map(function (u) { return c.add(u).catch(function () {}); }));
  }).then(function () { return self.skipWaiting(); }));
});

self.addEventListener('activate', function (e) {
  e.waitUntil(caches.keys().then(function (keys) {
    return Promise.all(keys.filter(function (k) { return k.indexOf('kopi-static-') === 0 && k !== STATIC; })
      .map(function (k) { return caches.delete(k); }));
  }).then(function () { return self.clients.claim(); }));
});

self.addEventListener('fetch', function (e) {
  var req = e.request;
  if (req.method !== 'GET') return;
  var url = new URL(req.url);

  /* Navigasi halaman: jaringan dulu, gagal -> halaman offline */
  if (req.mode === 'navigate') {
    e.respondWith(fetch(req).catch(function () { return caches.match(OFFLINE_URL); }));
    return;
  }

  /* Aset statis milik sendiri: cache dulu, perbarui di latar belakang */
  var own = url.origin === location.origin && /^\/(css|js|icons)\//.test(url.pathname);
  var cdn = CDN.indexOf(url.hostname) !== -1;
  if (own || cdn) {
    e.respondWith(caches.open(STATIC).then(function (cache) {
      return cache.match(req).then(function (hit) {
        var net = fetch(req).then(function (res) {
          if (res && (res.ok || res.type === 'opaque')) cache.put(req, res.clone());
          return res;
        }).catch(function () { return hit; });
        return hit || net;
      });
    }));
  }
  /* selain itu (gambar produk, /media, midtrans, dsb) dibiarkan lewat jaringan */
});
