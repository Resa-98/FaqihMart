<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between mb-5">
  <div>
    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Produk</h1>
    <p class="text-sm text-gray-500 mt-0.5"><?= (int) $total ?> produk</p>
  </div>
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
    <form action="<?= site_url('admin/produk') ?>" method="get" class="flex">
      <input type="search" name="q" value="<?= esc($q) ?>" placeholder="Cari produk..."
             class="w-full sm:w-56 border border-gray-300 rounded-l-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
      <button class="bg-gray-50 hover:bg-gray-100 border border-l-0 border-gray-300 px-3.5 py-2.5 text-sm rounded-r-lg font-medium text-gray-600">Cari</button>
    </form>
    <a href="<?= site_url('admin/produk/tambah') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg text-center transition-colors">
      + Tambah Produk
    </a>
  </div>
</div>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

  <!-- tabel (>= sm) -->
  <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs font-medium text-gray-500">
        <tr>
          <th class="text-left px-4 py-2.5">Produk</th>
          <th class="text-left px-4 py-2.5">Kategori</th>
          <th class="text-right px-4 py-2.5">Harga</th>
          <th class="text-right px-4 py-2.5">Stok</th>
          <th class="text-center px-4 py-2.5">Status</th>
          <th class="text-right px-4 py-2.5">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($products)): ?>
          <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500 text-sm">Tidak ada produk.</td></tr>
        <?php endif; ?>
        <?php foreach ($products as $p): ?>
          <tr class="hover:bg-gray-50/70">
            <td class="px-4 py-2.5">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
                  <?php if (!empty($p->image)): ?>
                    <img src="<?= base_url($p->image) ?>" alt="" loading="lazy" class="product-img max-h-full max-w-full">
                  <?php endif; ?>
                </div>
                <div class="min-w-0">
                  <p class="font-medium text-gray-900 line-clamp-1"><?= esc($p->name) ?></p>
                  <p class="text-xs text-gray-400 font-mono truncate"><?= esc($p->slug) ?></p>
                </div>
              </div>
            </td>
            <td class="px-4 py-2.5 text-gray-600"><?= esc($p->category_name ?? '—') ?></td>
            <td class="px-4 py-2.5 text-right rp"><?= rupiah($p->price) ?></td>
            <td class="px-4 py-2.5 text-right tabular-nums <?= ((int) $p->stock > 0) ? '' : 'text-red-600 font-semibold' ?>"><?= (int) $p->stock ?></td>
            <td class="px-4 py-2.5 text-center">
              <?php if ((int) $p->is_active === 1): ?>
                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
              <?php else: ?>
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-2.5 text-right whitespace-nowrap">
              <a href="<?= site_url('admin/produk/ubah/' . $p->id) ?>" class="text-blue-600 text-xs font-medium hover:underline">Ubah</a>
              <?= form_open('admin/produk/hapus/' . $p->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus produk ini? Riwayat pesanan tidak ikut terhapus.')")) ?>
                <button type="submit" class="text-red-600 text-xs font-medium hover:underline ml-2">Hapus</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- kartu (< sm) -->
  <ul class="sm:hidden divide-y divide-gray-100">
    <?php if (empty($products)): ?>
      <li class="px-4 py-10 text-center text-gray-500 text-sm">Tidak ada produk.</li>
    <?php endif; ?>
    <?php foreach ($products as $p): ?>
      <li class="p-4">
        <div class="flex gap-3">
          <div class="w-14 h-14 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center overflow-hidden shrink-0">
            <?php if (!empty($p->image)): ?>
              <img src="<?= base_url($p->image) ?>" alt="" loading="lazy" class="product-img max-h-full max-w-full">
            <?php endif; ?>
          </div>
          <div class="min-w-0 flex-1">
            <p class="font-medium text-gray-900 leading-snug"><?= esc($p->name) ?></p>
            <p class="text-xs text-gray-400 mt-0.5"><?= esc($p->category_name ?? '—') ?></p>
            <div class="flex flex-wrap items-center gap-2 mt-1.5">
              <span class="rp font-semibold text-brand"><?= rupiah($p->price) ?></span>
              <span class="text-xs text-gray-400">·</span>
              <span class="text-xs tabular-nums <?= ((int) $p->stock > 0) ? 'text-gray-500' : 'text-red-600 font-semibold' ?>">Stok <?= (int) $p->stock ?></span>
              <?php if ((int) $p->is_active === 1): ?>
                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
              <?php else: ?>
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
              <?php endif; ?>
            </div>
            <div class="flex items-center gap-4 mt-2.5">
              <a href="<?= site_url('admin/produk/ubah/' . $p->id) ?>" class="text-blue-600 text-xs font-medium hover:underline">Ubah</a>
              <?= form_open('admin/produk/hapus/' . $p->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus produk ini? Riwayat pesanan tidak ikut terhapus.')")) ?>
                <button type="submit" class="text-red-600 text-xs font-medium hover:underline">Hapus</button>
              </form>
            </div>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

  <?php if ($pages > 1): ?>
  <div class="flex flex-wrap justify-center gap-1 p-4 border-t border-gray-100">
    <?php for ($n = 1; $n <= $pages; $n++): ?>
      <?php $qs = http_build_query(array_filter(array('q' => $q, 'page' => $n > 1 ? $n : NULL))); ?>
      <?php if ($n === $page): ?>
        <span class="px-3 py-1.5 rounded-lg border border-brand bg-brand text-white text-xs font-medium tabular-nums"><?= $n ?></span>
      <?php else: ?>
        <a href="<?= site_url('admin/produk' . ($qs !== '' ? '?' . $qs : '')) ?>" class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-xs text-gray-600 hover:border-brand hover:text-brand tabular-nums"><?= $n ?></a>
      <?php endif; ?>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
