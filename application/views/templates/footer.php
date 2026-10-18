<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
</main>

<footer class="bg-white border-t mt-8">
  <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-gray-600 flex flex-col sm:flex-row gap-2 sm:justify-between">
    <p>&copy; <?= date('Y') ?> <?= esc($store_name) ?> — Kebutuhan pokok & rumah tangga, delivery <?= esc($store_area) ?>.</p>
    <nav class="flex gap-4">
      <a href="<?= site_url('katalog') ?>" class="hover:text-brand">Katalog</a>
      <a href="<?= site_url('informasi') ?>" class="hover:text-brand">Informasi</a>
    </nav>
  </div>
</footer>

</body>
</html>
