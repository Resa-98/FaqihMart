/*
 * Faqih Mart — interaksi toko (vanilla JS, tanpa dependency).
 *
 * Berisi:
 *  1. Toast + format Rupiah (sama dengan helper PHP rupiah()).
 *  2. Tambah ke keranjang via AJAX (tetap di halaman) + refresh token CSRF.
 *     Tombol "Beli Sekarang" (formaction) sengaja dibiarkan submit biasa.
 *  3. Counter jumlah (+/−) pada kartu & detail produk.
 *  4. Halaman keranjang: total real-time + konfirmasi saat melebihi stok (AJAX).
 *  5. Tombol "Gunakan lokasi saya" (geolocation) di checkout.
 */
(function () {
  'use strict';

  // ---------------------------------------------------------------- helpers

  function rupiah(n) {
    n = Number(n) || 0;
    var decimals = (n % 1 !== 0) ? 2 : 0;
    return 'Rp' + n.toLocaleString('id-ID', {
      minimumFractionDigits: decimals,
      maximumFractionDigits: decimals
    });
  }

  function toast(message, type) {
    var root = document.getElementById('toast-root');
    if (!root) { return; }
    var isError = (type === 'error');
    var el = document.createElement('div');
    el.setAttribute('role', 'status');
    el.setAttribute('style',
      'pointer-events:auto;padding:12px 16px;border-radius:10px;font-size:13px;font-weight:600;' +
      'box-shadow:0 6px 20px rgba(0,0,0,.12);transition:opacity .3s,transform .3s;opacity:0;transform:translateY(6px);' +
      'border:1px solid ' + (isError ? '#fecaca' : '#bbf7d0') + ';' +
      'background:' + (isError ? '#fef2f2' : '#f0fdf4') + ';' +
      'color:' + (isError ? '#b91c1c' : '#166534') + ';');
    el.textContent = message;
    root.appendChild(el);

    requestAnimationFrame(function () {
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    });

    setTimeout(function () {
      el.style.opacity = '0';
      el.style.transform = 'translateY(6px)';
      setTimeout(function () {
        if (el.parentNode) { el.parentNode.removeChild(el); }
      }, 320);
    }, 3200);
  }

  // Simpan token CSRF terbaru ke meta + SEMUA input tersembunyi di halaman.
  // Penting karena csrf_regenerate = TRUE (token lama langsung kedaluwarsa).
  function syncCsrf(name, hash) {
    if (!name || !hash) { return; }
    var metas = document.querySelectorAll('meta[name="csrf-name"], meta[name="csrf-token"]');
    for (var i = 0; i < metas.length; i++) {
      metas[i].setAttribute('content', metas[i].getAttribute('name') === 'csrf-name' ? name : hash);
    }
    var inputs = document.querySelectorAll('input[name="' + name + '"]');
    for (var j = 0; j < inputs.length; j++) { inputs[j].value = hash; }
  }

  function updateCartBadges(count) {
    var badges = document.querySelectorAll('[data-cart-badge]');
    for (var i = 0; i < badges.length; i++) {
      badges[i].textContent = count;
      if (count > 0) { badges[i].classList.remove('hidden'); }
      else { badges[i].classList.add('hidden'); }
    }
    var inline = document.querySelectorAll('[data-cart-inline]');
    for (var k = 0; k < inline.length; k++) {
      inline[k].textContent = count > 0 ? ' (' + count + ')' : '';
    }
  }

  // ------------------------------------------------- 1. tambah ke keranjang

  // Lacak tombol submit yang terakhir diklik, karena `e.submitter` belum
  // didukung semua browser (mis. Safari lama).
  var lastSubmitter = null;
  document.addEventListener('click', function (e) {
    var t = e.target;
    lastSubmitter = (t && t.closest) ? t.closest('button[type="submit"], input[type="submit"]') : null;
  }, true);

  document.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form || !form.hasAttribute || !form.hasAttribute('data-ajax-cart')) { return; }
    if (typeof window.fetch !== 'function' || !window.FormData) { return; } // fallback: submit biasa

    // Tombol "Beli Sekarang" memakai formaction ke endpoint lain — biarkan
    // browser mengirimnya seperti biasa (bukan AJAX tambah-keranjang).
    var submitter = e.submitter || lastSubmitter;
    if (submitter && submitter.hasAttribute && submitter.hasAttribute('formaction')) { return; }

    e.preventDefault();

    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; }

    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (btn) { btn.disabled = false; }
        if (data && typeof data.cart_count === 'number') { updateCartBadges(data.cart_count); }
        if (data && data.csrf_name && data.csrf_hash) { syncCsrf(data.csrf_name, data.csrf_hash); }
        toast((data && data.message) || 'Keranjang diperbarui.', data && data.ok === false ? 'error' : 'success');
      })
      .catch(function () {
        if (btn) { btn.disabled = false; }
        // jaringan bermasalah -> kirim form seperti biasa supaya tidak menggantung
        form.submit();
      });
  }, false);

  // ------------------------------------------------------ 2. counter jumlah

  document.addEventListener('click', function (e) {
    var dec = e.target.closest ? e.target.closest('[data-qty-dec]') : null;
    var inc = e.target.closest ? e.target.closest('[data-qty-inc]') : null;
    if (!dec && !inc) { return; }

    var scope = (dec || inc).closest('.product-card') || document;
    var input = scope.querySelector('[data-qty]');
    if (!input) { return; }

    var min = parseInt(input.getAttribute('min'), 10);
    var max = parseInt(input.getAttribute('max'), 10);
    if (isNaN(min)) { min = 1; }
    if (isNaN(max) || max < 1) { max = 999999; }

    var val = parseInt(input.value, 10);
    if (isNaN(val)) { val = min; }

    val += inc ? 1 : -1;
    if (val < min) { val = min; }
    if (val > max) { val = max; }

    input.value = val;
    input.dispatchEvent(new Event('input', { bubbles: true }));
  }, false);

  // ------------------------------------------------- 4. keranjang real-time

  function recalcCart() {
    var totalEl = document.querySelector('[data-cart-total]');
    if (!totalEl) { return; }
    var lines = document.querySelectorAll('[data-cart-line]');
    var total = 0;

    for (var i = 0; i < lines.length; i++) {
      var line = lines[i];
      var price = parseFloat(line.getAttribute('data-price')) || 0;
      var stock = parseInt(line.getAttribute('data-stock'), 10);
      if (isNaN(stock) || stock < 0) { stock = 0; }

      var input = line.querySelector('[data-cart-qty]');
      var qty = input ? parseInt(input.value, 10) : 0;
      if (isNaN(qty) || qty < 0) { qty = 0; }

      // server membatasi ke stok, jadi total ditampilkan pakai nilai efektif
      var effective = Math.min(qty, stock);
      var sub = price * effective;

      var subEl = line.querySelector('[data-line-subtotal]');
      if (subEl) { subEl.textContent = rupiah(sub); }
      total += sub;
    }

    totalEl.textContent = rupiah(total);
  }

  // Satu permintaan keranjang diproses pada satu waktu. Ini penting karena
  // CSRF diregenerasi tiap POST — kalau dua request jalan bersamaan, yang kedua
  // bisa memakai token basi dan ditolak (403).
  var cartBusy = false;
  var cartQueue = [];

  function enqueueCart(task) {
    cartQueue.push(task);
    drainCart();
  }

  function drainCart() {
    if (cartBusy || !cartQueue.length) { return; }
    cartBusy = true;
    cartQueue.shift()(function () {
      cartBusy = false;
      drainCart();
    });
  }

  function postCart(form, done) {
    var settled = false;
    function finish(data) {
      if (settled) { return; }
      settled = true;
      done(data);
    }
    fetch(form.action, {
      method: 'POST',
      body: new FormData(form),
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      credentials: 'same-origin'
    })
      .then(function (res) { return res.json(); })
      .then(finish)
      .catch(function () {
        toast('Gagal memperbarui keranjang. Periksa koneksi lalu coba lagi.', 'error');
        finish(null);
      });
  }

  (function initCart() {
    var lines = document.querySelectorAll('[data-cart-line]');
    if (!lines.length) { return; }
    if (typeof window.fetch !== 'function' || !window.FormData) { return; } // fallback: form biasa

    recalcCart();

    for (var i = 0; i < lines.length; i++) {
      (function (line) {
        var input = line.querySelector('[data-cart-qty]');
        var form = line.querySelector('[data-cart-update]');
        if (!input || !form) { return; }

        var committed = input.value;
        var timer = null;

        function send() {
          var stock = parseInt(line.getAttribute('data-stock'), 10);
          var qty = parseInt(input.value, 10);
          if (isNaN(qty) || qty < 0) { qty = 0; }

          if (!isNaN(stock) && qty > stock && stock >= 0) {
            if (window.confirm('Stok tersedia hanya ' + stock + '. Ubah jumlah menjadi ' + stock + '?')) {
              input.value = stock;
              recalcCart();
            } else {
              input.value = committed;
              recalcCart();
              return;
            }
          }

          if (String(input.value) === String(committed)) { return; } // tidak ada perubahan

          // tandai optimistis supaya klik "Ubah" + event change tidak kirim dua kali
          var previous = committed;
          committed = input.value;

          enqueueCart(function (done) {
            postCart(form, function (data) {
              if (!data) {
                committed = previous; // gagal jaringan: biarkan user mencoba lagi
                recalcCart();
                done();
                return;
              }

              if (data.csrf_name && data.csrf_hash) { syncCsrf(data.csrf_name, data.csrf_hash); }
              if (typeof data.cart_count === 'number') { updateCartBadges(data.cart_count); }

              if (typeof data.qty === 'number') {
                if (data.qty === 0) {
                  if (line.parentNode) { line.parentNode.removeChild(line); }
                } else {
                  input.value = data.qty;
                }
              }
              committed = input.value;
              recalcCart();

              if (data.message && (data.ok === false || data.adjusted)) {
                toast(data.message, data.ok === false ? 'error' : 'success');
              }
              done();
            });
          });
        }

        form.__fmSend = send; // dipakai handler submit (mis. menekan Enter)

        input.addEventListener('input', function () {
          recalcCart();
          if (timer) { clearTimeout(timer); }
          timer = setTimeout(function () { timer = null; send(); }, 700);
        });

        input.addEventListener('change', function () {
          if (timer) { clearTimeout(timer); timer = null; }
          send();
        });
      })(lines[i]);
    }

    // Enter di kolom jumlah: jangan reload halaman, pakai alur AJAX yang sama
    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (form && form.__fmSend) {
        e.preventDefault();
        form.__fmSend();
      }
    }, false);
  })();

  // ---------------------------------------------------- 5. lokasi saya (maps)

  (function initGeo() {
    var btn = document.getElementById('btn-geoloc');
    if (!btn) { return; }
    var linkInput = document.getElementById('map_link');
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var status = document.getElementById('geo-status');

    function show(msg, isError) {
      if (!status) { return; }
      status.textContent = msg;
      status.classList.remove('hidden');
      status.style.color = isError ? '#b91c1c' : '#166534';
    }

    // kalau pengguna mengetik/menempel link sendiri, koordinat lama dibuang
    if (linkInput) {
      linkInput.addEventListener('input', function () {
        if (latInput) { latInput.value = ''; }
        if (lngInput) { lngInput.value = ''; }
      });
    }

    btn.addEventListener('click', function () {
      if (!navigator.geolocation) {
        show('Browser Anda tidak mendukung fitur lokasi. Tempelkan link Google Maps secara manual.', true);
        return;
      }
      show('Mengambil lokasi…', false);
      btn.disabled = true;

      navigator.geolocation.getCurrentPosition(function (pos) {
        btn.disabled = false;
        var lat = pos.coords.latitude.toFixed(7);
        var lng = pos.coords.longitude.toFixed(7);
        if (latInput) { latInput.value = lat; }
        if (lngInput) { lngInput.value = lng; }
        if (linkInput) { linkInput.value = 'https://www.google.com/maps?q=' + lat + ',' + lng; }
        show('Lokasi terisi. Anda bisa menyesuaikan link bila perlu.', false);
      }, function (err) {
        btn.disabled = false;
        var msg = 'Gagal mengambil lokasi.';
        if (err && err.code === 1) { msg = 'Akses lokasi ditolak. Izinkan lokasi di browser atau tempel link Google Maps manual.'; }
        else if (err && err.code === 3) { msg = 'Waktu pengambilan lokasi habis. Coba lagi.'; }
        show(msg, true);
      }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 });
    });
  })();

})();
