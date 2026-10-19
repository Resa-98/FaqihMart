<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<!-- hero -->
<section class="relative overflow-hidden bg-brand text-white rounded-2xl p-6 sm:p-10 mb-8">
  <div class="relative max-w-2xl">
    <p class="inline-block bg-white/15 text-white text-xs font-semibold px-3 py-1 rounded-full mb-4">Belanja online · Delivery <?= esc($store_area) ?></p>
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight leading-tight">
      Kebutuhan pokok, diantar ke rumah Anda.
    </h1>
    <p class="mt-3 text-white/90 text-sm sm:text-base leading-relaxed max-w-xl">
      Sembako dan kebutuhan rumah tangga lengkap dari <?= esc($store_name) ?>. Pesan dari rumah, kami antar di area <?= esc($store_area) ?>.
    </p>
    <div class="mt-6 flex flex-col sm:flex-row gap-3">
      <a href="<?= site_url('katalog') ?>" class="bg-accent hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-lg text-sm text-center transition-colors shadow-sm">
        Belanja Sekarang
      </a>
      <a href="<?= site_url('informasi') ?>" class="border border-white/60 hover:bg-white/10 text-white font-semibold px-6 py-3 rounded-lg text-sm text-center transition-colors">
        Informasi Toko
      </a>
    </div>
  </div>
</section>

<!-- kategori -->
<?php if (!empty($nav_categories)): ?>
<section class="mb-8">
  <h2 class="text-xl font-bold tracking-tight text-gray-900 mb-3">Kategori</h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    <?php foreach ($nav_categories as $cat): ?>
      <a href="<?= site_url('kategori/' . $cat->slug) ?>"
         class="bg-white border border-gray-200 hover:border-brand hover:text-brand hover:shadow-sm rounded-xl px-3 py-4 text-center text-sm font-medium text-gray-700 transition-all">
        <?= esc($cat->name) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- produk terbaru -->
<section>
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-xl font-bold tracking-tight text-gray-900">Produk Terbaru</h2>
    <a href="<?= site_url('katalog') ?>" class="text-sm font-medium text-brand hover:underline">Lihat semua →</a>
  </div>

  <?php if (empty($products)): ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-xl p-10 text-center text-gray-500 text-sm">
      Belum ada produk. Tambahkan produk melalui halaman admin.
    </div>
  <?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
      <?php foreach ($products as $p): ?>
        <?php $this->load->view('products/_card', array('p' => $p)); ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
