<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-4">
  <div>
    <h1 class="text-xl font-bold">Manajemen Kategori</h1>
    <p class="text-sm text-gray-500"><?= count($categories) ?> kategori</p>
  </div>
  <a href="<?= site_url('admin/kategori/tambah') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md">
    + Tambah Kategori
  </a>
</div>

<div class="bg-white border border-gray-200 rounded-lg overflow-x-auto">
  <table class="w-full text-sm min-w-[540px]">
    <thead class="bg-gray-50 text-xs text-gray-500">
      <tr>
        <th class="text-left px-4 py-2.5">Nama</th>
        <th class="text-left px-4 py-2.5">Slug</th>
        <th class="text-right px-4 py-2.5">Produk</th>
        <th class="text-center px-4 py-2.5">Status</th>
        <th class="text-right px-4 py-2.5">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php if (empty($categories)): ?>
        <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">Belum ada kategori.</td></tr>
      <?php endif; ?>
      <?php foreach ($categories as $cat): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-4 py-2.5 font-medium"><?= esc($cat->name) ?></td>
          <td class="px-4 py-2.5 font-mono text-xs text-gray-400"><?= esc($cat->slug) ?></td>
          <td class="px-4 py-2.5 text-right"><?= (int) $cat->product_count ?></td>
          <td class="px-4 py-2.5 text-center">
            <?php if ((int) $cat->is_active === 1): ?>
              <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full">Aktif</span>
            <?php else: ?>
              <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">Nonaktif</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-2.5 text-right whitespace-nowrap">
            <a href="<?= site_url('admin/kategori/ubah/' . $cat->id) ?>" class="text-blue-600 text-xs hover:underline">Ubah</a>
            <?= form_open('admin/kategori/hapus/' . $cat->id, array('class' => 'inline', 'onsubmit' => "return confirm('Hapus kategori ini? Produk di dalamnya harus dipindahkan dulu.')")) ?>
              <button type="submit" class="text-red-600 text-xs hover:underline ml-2">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
