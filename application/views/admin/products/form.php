<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-2xl">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-5"><?= esc($title) ?></h1>

  <div class="bg-white border border-gray-200 rounded-xl p-5 sm:p-6">
    <?php $is_edit = !empty($product) && !empty($product->id); ?>
    <?= form_open_multipart($is_edit ? 'admin/produk/ubah/' . (int) $product->id . '/proses' : 'admin/produk/tambah/proses') ?>

      <?php if (!empty($errors['general'])): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-3.5 py-2.5 rounded-lg mb-4"><?= esc($errors['general']) ?></div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
          <input type="text" id="name" name="name" required maxlength="200"
                 value="<?= esc($product->name ?? set_value('name')) ?>"
                 class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
          <?php if (!empty($errors['name'])): ?><p class="text-xs text-red-600 mt-1"><?= esc($errors['name']) ?></p><?php endif; ?>
        </div>

        <div class="grid sm:grid-cols-3 gap-4">
          <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
            <select id="category_id" name="category_id" required
                    class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
              <option value="">— pilih —</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat->id ?>"
                  <?= (int) ($product->category_id ?? 0) === (int) $cat->id ? 'selected' : '' ?>>
                  <?= esc($cat->name) ?><?= (int) $cat->is_active !== 1 ? ' (nonaktif)' : '' ?>
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['category_id'])): ?><p class="text-xs text-red-600 mt-1"><?= esc($errors['category_id']) ?></p><?php endif; ?>
          </div>

          <div>
            <label for="price" class="block text-sm font-medium text-gray-700 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
            <input type="text" inputmode="decimal" id="price" name="price" required
                   value="<?= esc($product->price ?? set_value('price')) ?>"
                   placeholder="10000"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
            <?php if (!empty($errors['price'])): ?><p class="text-xs text-red-600 mt-1"><?= esc($errors['price']) ?></p><?php endif; ?>
          </div>

          <div>
            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1.5">Stok <span class="text-red-500">*</span></label>
            <input type="number" id="stock" name="stock" required min="0" step="1"
                   value="<?= esc($product->stock ?? set_value('stock', '0')) ?>"
                   class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand">
            <?php if (!empty($errors['stock'])): ?><p class="text-xs text-red-600 mt-1"><?= esc($errors['stock']) ?></p><?php endif; ?>
          </div>
        </div>

        <div>
          <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi (opsional)</label>
          <textarea id="description" name="description" rows="3" maxlength="2000"
                    class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"><?= esc($product->description ?? set_value('description')) ?></textarea>
        </div>

        <div>
          <label for="image" class="block text-sm font-medium text-gray-700 mb-1.5">
            Gambar Produk <?= $is_edit ? '(kosongkan untuk mempertahankan gambar)' : '(<span class="text-red-500">*</span>)' ?>
          </label>
          <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                 class="w-full text-sm text-gray-600 file:mr-3 file:border-0 file:bg-brand file:text-white file:px-3.5 file:py-2 file:rounded-lg file:text-sm file:font-medium hover:file:bg-brand-dark">
          <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, atau WebP. Maks. <?= (int) $this->config->item('upload_max_size') / 1024 ?> MB.</p>
          <?php if (!empty($errors['image'])): ?><p class="text-xs text-red-600 mt-1"><?= esc($errors['image']) ?></p><?php endif; ?>

          <?php if ($is_edit && !empty($product->image)): ?>
            <div class="mt-2 w-20 h-20 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
              <img src="<?= base_url($product->image) ?>" alt="" class="product-img max-h-full max-w-full">
            </div>
          <?php endif; ?>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input type="checkbox" name="is_active" value="1"
            <?= (int) ($product->is_active ?? 1) === 1 ? 'checked' : '' ?> class="rounded text-brand focus:ring-brand">
          Tampilkan di toko (aktif)
        </label>
      </div>

      <div class="mt-6 flex items-center gap-4">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
          <?= $is_edit ? 'Simpan Perubahan' : 'Tambah Produk' ?>
        </button>
        <a href="<?= site_url('admin/produk') ?>" class="text-sm text-gray-500 hover:text-brand">Batal</a>
      </div>
    </form>
  </div>
</div>
