<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl font-bold">Manajemen Produk</h1>
    <p class="text-sm text-gray-500"><?= (int) $total ?> produk</p>
  </div>
  <div class="flex gap-2 items-center">
    <form action="<?= site_url('admin/produk') ?>" method="get" class="flex">
      <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari produk..."
             class="border border-gray-300 rounded-l-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand">
      <button class="bg-gray-100 hover:bg-gray-200 border border-l-0 border-gray-300 px-3 py-2 text-sm rounded-r-md">Cari</button>
    </form>
    <a href="<?= site_url('admin/produk/tambah') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md">
      + Tambah Produk
    </a>
  </div>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
  <table class="w-full text-sm min-w-[640px]">
    <thead class="bg-gray-50 text-xs text-gray-500">
      <tr>
        <th class="text-left px-4 py-2.5">Produk</th>
        <th class="text-left px-4 py-2.5">Kategori</th>
        <th class="text-right px-4 py-2.5">Harga</th>
        <th class="text-right px-4 py-2.5">Stok</th>
        <th class="text-center px-4 py-2.5">Status</th>
        <th class="text-right px-4 py-2.5">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php if (empty($products)): ?>
        <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada produk.</td></tr>
      <?php endif; ?>
      <?php foreach ($products as $p): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2.5">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 bg-gray-100 rounded flex items-center justify-center overflow-hidden shrink-0">
                <?php if (!empty($p->image)): ?>
                  <img src="<?= base_url($p->image) ?>" alt="" loading="lazy" class="product-img max-h-full max-w-full">
                <?php endif; ?>
              </div>
              <div class="min-w-0">
                <p class="font-medium line-clamp-1"><?= esc($p->name) ?></p>
                <p class="text-xs text-gray-400 font-mono truncate"><?= esc($p->slug) ?></p>
              </div>
            </div>
          </td>
          <td class="px-4 py-2.5 text-gray-600"><?= esc($p->category_name ?? '—') ?></td>
          <td class="px-4 py-2.5 text-right rp"><?= rupiah($p->price) ?></td>
          <td class="px-4 py-2.5 text-right <?= ((int) $p->stock > 0) ? '' : 'text-red-600 font-semibold' ?>"><?= (int) $p->stock ?></td>
          <td class="px-4 py-2.5 text-center">
            <?php if ((int) $p->is_active === 1): ?>
              <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
            <?php else: ?>
              <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-2.5 text-right whitespace-nowrap">
            <a href="<?= site_url('admin/produk/ubah/' . $p->id) ?>" class="text-blue-600 text-xs hover:underline">Ubah</a>
            <?= form_open('admin/produk/hapus/' . $p->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus produk ini? Riwayat pesanan tidak ikut terhapus.')")) ?>
              <button type="submit" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <?php if ($pages > 1): ?>
  <div class="flex justify-center gap-1 p-4 border-t">
    <?php for ($n = 1; $n <= $pages; $n++): ?>
      <?php $qs = http_build_query(array_filter(array('q' => $q, 'page' => $n > 1 ? $n : NULL))); ?>
      <?php if ($n === $page): ?>
        <span class="px-3 py-1 rounded border border-brand bg-brand text-white text-xs"><?= $n ?></span>
      <?php else: ?>
        <a href="<?= site_url('admin/produk' . ($qs !== '' ? '?' . $qs : '')) ?>" class="px-3 py-1 rounded border bg-white text-xs hover:border-brand"><?= $n ?></a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
