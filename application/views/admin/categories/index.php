<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
  <div>
    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Kategori</h1>
    <p class="text-sm text-gray-500 mt-0.5"><?= count($categories) ?> kategori</p>
  </div>
  <a href="<?= site_url('admin/kategori/tambah') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-lg text-center transition-colors">
    + Tambah Kategori
  </a>
</div>

<div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

  <!-- tabel (>= sm) -->
  <div class="hidden sm:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs font-medium text-gray-500">
        <tr>
          <th class="text-left px-4 py-2.5">Nama</th>
          <th class="text-left px-4 py-2.5">Slug</th>
          <th class="text-right px-4 py-2.5">Produk</th>
          <th class="text-center px-4 py-2.5">Status</th>
          <th class="text-right px-4 py-2.5">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php if (empty($categories)): ?>
          <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500 text-sm">Belum ada kategori.</td></tr>
        <?php endif; ?>
        <?php foreach ($categories as $cat): ?>
          <tr class="hover:bg-gray-50/70">
            <td class="px-4 py-2.5 font-medium text-gray-900"><?= esc($cat->name) ?></td>
            <td class="px-4 py-2.5 font-mono text-xs text-gray-400"><?= esc($cat->slug) ?></td>
            <td class="px-4 py-2.5 text-right tabular-nums"><?= (int) $cat->product_count ?></td>
            <td class="px-4 py-2.5 text-center">
              <?php if ((int) $cat->is_active === 1): ?>
                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
              <?php else: ?>
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-2.5 text-right whitespace-nowrap">
              <a href="<?= site_url('admin/kategori/ubah/' . $cat->id) ?>" class="text-blue-600 text-xs font-medium hover:underline">Ubah</a>
              <?= form_open('admin/kategori/hapus/' . $cat->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus kategori ini? Produk di dalamnya harus dipindahkan dulu.')")) ?>
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
    <?php if (empty($categories)): ?>
      <li class="px-4 py-10 text-center text-gray-500 text-sm">Belum ada kategori.</li>
    <?php endif; ?>
    <?php foreach ($categories as $cat): ?>
      <li class="p-4">
        <div class="flex items-start justify-between gap-3">
          <div class="min-w-0">
            <p class="font-medium text-gray-900"><?= esc($cat->name) ?></p>
            <p class="font-mono text-xs text-gray-400 mt-0.5 truncate"><?= esc($cat->slug) ?></p>
            <div class="flex items-center gap-2 mt-1.5">
              <span class="text-xs text-gray-500 tabular-nums"><?= (int) $cat->product_count ?> produk</span>
              <?php if ((int) $cat->is_active === 1): ?>
                <span class="text-xs font-medium bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
              <?php else: ?>
                <span class="text-xs font-medium bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
              <?php endif; ?>
            </div>
          </div>
          <div class="flex items-center gap-4 shrink-0 pt-0.5">
            <a href="<?= site_url('admin/kategori/ubah/' . $cat->id) ?>" class="text-blue-600 text-xs font-medium hover:underline">Ubah</a>
            <?= form_open('admin/kategori/hapus/' . $cat->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus kategori ini? Produk di dalamnya harus dipindahkan dulu.')")) ?>
              <button type="submit" class="text-red-600 text-xs font-medium hover:underline">Hapus</button>
            </form>
          </div>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>
</div>
