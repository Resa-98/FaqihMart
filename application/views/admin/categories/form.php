<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-lg">
  <h1 class="text-xl font-bold mb-4"><?= esc($title) ?></h1>

  <div class="bg-white border border-gray-200 rounded-lg p-5">
    <?php $is_edit = !empty($category) && !empty($category->id); ?>
    <?= form_open($is_edit ? 'admin/kategori/ubah/' . (int) $category->id . '/proses' : 'admin/kategori/tambah/proses') ?>

      <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2 rounded-md mb-4"><?= esc($errors['general']) ?></div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium mb-1">Nama Kategori <span class="text-red-500">*</span></label>
          <input type="text" id="name" name="name" required maxlength="100"
                 value="<?= esc($category->name ?? set_value('name')) ?>"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                 placeholder="Contoh: Sembako">
          <p class="text-xs text-gray-500 mt-1">Slug URL dibuat otomatis dari nama.</p>
        </div>

        <label class="flex items-center gap-2 text-sm">
          <input type="checkbox" name="is_active" value="1"
            <?= (int) ($category->is_active ?? 1) === 1 ? 'checked' : '' ?> class="rounded text-brand">
          Tampilkan di toko (aktif)
        </label>
      </div>

      <div class="mt-6 flex gap-3">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md">
          <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Kategori' ?>
        </button>
        <a href="<?= site_url('admin/kategori') ?>" class="text-sm text-brand hover:underline self-center">Batal</a>
      </div>
    </form>
  </div>
</div>
