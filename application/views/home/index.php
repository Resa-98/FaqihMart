<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<section class="bg-brand text-white rounded-xl p-6 sm:p-10 mb-8">
  <h1 class="text-2xl sm:text-3xl font-bold"><?= esc($store_name) ?></h1>
  <p class="mt-2 opacity-90 max-w-xl">
    Kebutuhan pokok (sembako) dan kebutuhan rumah tangga, siap diantar ke rumah Anda.
    Area pengiriman: <?= esc($store_area) ?>.
  </p>
  <div class="mt-4 flex flex-wrap gap-3">
    <a href="<?= site_url('katalog') ?>" class="bg-accent hover:bg-amber-600 text-white font-semibold px-5 py-2.5 rounded-md text-sm">
      Belanja Sekarang
    </a>
    <a href="<?= site_url('informasi') ?>" class="border border-white/70 hover:bg-white/10 px-5 py-2.5 rounded-md text-sm">
      Informasi Toko
    </a>
  </div>
</section>

<?php if (!empty($nav_categories)): ?>
<section class="mb-8">
  <h2 class="text-lg font-semibold mb-3">Kategori</h2>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
    <?php foreach ($nav_categories as $cat): ?>
      <a href="<?= site_url('kategori/' . $cat->slug) ?>"
         class="bg-white border border-gray-200 hover:border-brand hover:text-brand rounded-lg px-3 py-4 text-center text-sm font-medium transition-colors">
        <?= esc($cat->name) ?>
      </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<section>
  <div class="flex items-center justify-between mb-3">
    <h2 class="text-lg font-semibold">Produk Terbaru</h2>
    <a href="<?= site_url('katalog') ?>" class="text-sm text-brand hover:underline">Lihat semua →</a>
  </div>

  <?php if (empty($products)): ?>
    <div class="bg-white border border-dashed border-gray-300 rounded-lg p-8 text-center text-gray-500 text-sm">
      Belum ada produk. Tambahkan produk melalui halaman admin.
    </div>
  <?php else: ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
      <?php foreach ($products as $p): ?>
        <?php $this->load->view('products/_card', array('p' => $p)); ?>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>
