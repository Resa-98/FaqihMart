<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-lg">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-5"><?= esc($title) ?></h1>

  <div class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6">
    <?php $is_edit = !empty($category) && !empty($category->id); ?>
    <?= form_open($is_edit ? 'admin/kategori/ubah/' . (int) $category->id . '/proses' : 'admin/kategori/tambah/proses') ?>

      <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3.5 py-2.5 rounded-lg mb-4"><?= esc($errors['general']) ?></div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
          <input type="text" id="name" name="name" required maxlength="100"
                 value="<?= esc($category->name ?? set_value('name')) ?>"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                 placeholder="Contoh: Sembako">
          <p class="text-xs text-gray-500 mt-1">Slug URL dibuat otomatis dari nama.</p>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input type="checkbox" name="is_active" value="1"
            <?= (int) ($category->is_active ?? 1) === 1 ? 'checked' : '' ?> class="rounded text-brand focus:ring-brand">
          Tampilkan di toko (aktif)
        </label>
      </div>

      <div class="mt-6 flex items-center gap-4">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
          <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
        </button>
        <a href="<?= site_url('admin/kategori') ?>" class="text-sm text-gray-500 hover:text-brand">Batal</a>
      </div>
    </form>
  </div>
</div>
