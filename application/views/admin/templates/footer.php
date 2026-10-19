<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</main>

<footer class="max-w-6xl mx-auto px-4 py-6 text-xs text-gray-400">
  &copy; <?= date('Y') ?> <?= esc($store_name) ?> — Panel Admin
</footer>

<script>
(function () {
  var toggle = document.getElementById('admin-menu-toggle');
  var menu = document.getElementById('admin-mobile-menu');
  if (!toggle || !menu) return;
  var iconOpen = document.getElementById('admin-menu-icon-open');
  var iconClose = document.getElementById('admin-menu-icon-close');

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

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 768) setOpen(false);
  });
})();
</script>

</body>
</html>
