<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</main>

<footer class="bg-white border-t border-gray-200 mt-10">
  <div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
      <div class="max-w-sm">
        <img src="<?= base_url('assets/images/brand/primary-logo.webp') ?>" alt="<?= esc($store_name) ?>" class="h-10 w-auto mb-3">
        <p class="text-sm text-gray-500 leading-relaxed">
          Kebutuhan pokok &amp; rumah tangga, siap diantar ke rumah Anda. Delivery area <?= esc($store_area) ?>.
        </p>
      </div>
      <nav class="flex flex-col gap-2 text-sm sm:items-end">
        <a href="<?= site_url('katalog') ?>" class="text-gray-600 hover:text-brand">Katalog</a>
        <a href="<?= site_url('informasi') ?>" class="text-gray-600 hover:text-brand">Informasi</a>
        <a href="<?= site_url('keranjang') ?>" class="text-gray-600 hover:text-brand">Keranjang</a>
      </nav>
    </div>
    <p class="text-xs text-gray-400 mt-6 pt-6 border-t border-gray-100">&copy; <?= date('Y') ?> <?= esc($store_name) ?>. Semua harga dapat berubah tanpa pemberitahuan.</p>
  </div>
</footer>

<script>
(function () {
  var toggle = document.getElementById('menu-toggle');
  var menu = document.getElementById('mobile-menu');
  if (!toggle || !menu) return;
  var iconOpen = document.getElementById('menu-icon-open');
  var iconClose = document.getElementById('menu-icon-close');

  function setOpen(open) {
    menu.classList.toggle('hidden', !open);
    toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    toggle.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
    if (iconOpen) iconOpen.classList.toggle('hidden', open);
    if (iconClose) iconClose.classList.toggle('hidden', !open);
  }

  toggle.addEventListener('click', function () {
    setOpen(menu.classList.contains('hidden'));
  });

  // tutup saat layar diperbesar ke desktop
  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768) setOpen(false);
  });
})();
</script>

<!-- toast container -->
<div id="toast-root" class="fixed z-50 bottom-4 left-1/2 -translate-x-1/2 sm:left-auto sm:right-4 sm:translate-x-0 flex flex-col gap-2 w-[calc(100%-2rem)] sm:w-80 pointer-events-none"></div>

<script src="<?= base_url('assets/js/app.js') ?>" defer></script>

</body>
</html>
