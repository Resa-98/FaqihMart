<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl font-bold"><?= esc($title) ?></h1>
    <p class="text-sm text-gray-500"><?= (int) $total ?> produk ditemukan</p>
  </div>

  <!-- filter kategori -->
  <div class="flex flex-wrap gap-2 text-xs">
    <a href="<?= site_url('katalog' . ($filters['q'] !== '' ? '?' . http_build_query(array('q' => $filters['q'])) : '')) ?>"
       class="px-3 py-1.5 rounded-full border <?= $filters['category_slug'] === '' ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 hover:border-brand' ?>">
      Semua
    </a>
    <?php foreach ($nav_categories as $cat): ?>
      <?php
        $qs = http_build_query(array_filter(array('kategori' => $cat->slug, 'q' => $filters['q'])));
      ?>
      <a href="<?= site_url('katalog' . ($qs !== '' ? '?' . $qs : '')) ?>"
         class="px-3 py-1.5 rounded-full border <?= $filters['category_slug'] === $cat->slug ? 'bg-brand text-white border-brand' : 'bg-white border-gray-300 hover:border-brand' ?>">
        <?= esc($cat->name) ?>
      </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($filters['q'] !== ''): ?>
  <p class="text-sm text-gray-600 mb-3">
    Hasil pencarian untuk "<strong><?= esc($filters['q']) ?></strong>"
    <a href="<?= site_url('katalog') ?>" class="text-brand hover:underline ml-1">hapus</a>
  </p>
<?php endif; ?>

<?php if (empty($products)): ?>
  <div class="bg-white border border-dashed border-gray-300 rounded-lg p-10 text-center">
    <p class="text-gray-500 text-sm">Tidak ada produk yang cocok.</p>
    <a href="<?= site_url('katalog') ?>" class="text-brand text-sm hover:underline mt-2 inline-block">Lihat semua produk</a>
  </div>
<?php else: ?>
  <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php foreach ($products as $p): ?>
      <?php $this->load->view('products/_card', array('p' => $p)); ?>
    <?php endforeach; ?>
  </div>

  <?php if ($pages > 1): ?>
  <nav class="flex justify-center gap-1 mt-8 flex-wrap" aria-label="Navigasi halaman">
    <?php
      $page_url = function ($n) use ($filters, $pages) {
        $qs = http_build_query(array_filter(array(
          'kategori' => $filters['category_slug'],
          'q'        => $filters['q'],
          'page'     => $n > 1 ? $n : NULL,
        )));
        return site_url('katalog' . ($qs !== '' ? '?' . $qs : ''));
      };
    ?>
    <?php if ($page > 1): ?>
      <a href="<?= $page_url($page - 1) ?>" class="px-3 py-1.5 rounded border bg-white text-sm hover:border-brand">‹</a>
    <?php endif; ?>
    <?php
      // jendela 5 halaman di sekitar halaman aktif
      $start = max(1, $page - 2);
      $end   = min($pages, $page + 2);
      for ($n = $start; $n <= $end; $n++):
    ?>
      <?php if ($n === $page): ?>
        <span class="px-3 py-1.5 rounded border border-brand bg-brand text-white text-sm"><?= $n ?></span>
      <?php else: ?>
        <a href="<?= $page_url($n) ?>" class="px-3 py-1.5 rounded border bg-white text-sm hover:border-brand"><?= $n ?></a>
      <?php endif; ?>
    <?php endfor; ?>
    <?php if ($page < $pages): ?>
      <a href="<?= $page_url($page + 1) ?>" class="px-3 py-1.5 rounded border bg-white text-sm hover:border-brand">›</a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>
<?php endif; ?>
