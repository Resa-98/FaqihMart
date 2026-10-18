<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php // Card produk dipakai beranda & katalog. Input: $p (obj produk). ?>
<div class="bg-white rounded-lg border border-gray-200 hover:shadow-md transition-shadow flex flex-col">
  <a href="<?= site_url('produk/' . $p->slug) ?>" class="block p-3">
    <div class="aspect-square bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
      <?php if (!empty($p->image)): ?>
        <img src="<?= base_url($p->image) ?>" alt="<?= esc($p->name) ?>"
             loading="lazy" class="product-img max-h-full max-w-full"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div style="display:none" class="w-full h-full items-center justify-center text-gray-400 text-xs p-2 text-center">
          Gambar tidak tersedia
        </div>
      <?php else: ?>
        <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs">Tanpa gambar</div>
      <?php endif; ?>
    </div>
  </a>
  <div class="p-3 pt-0 flex flex-col flex-1">
    <a href="<?= site_url('produk/' . $p->slug) ?>" class="text-sm font-medium hover:text-brand line-clamp-2 leading-snug"><?= esc($p->name) ?></a>
    <?php if (!empty($p->category_name)): ?>
      <a href="<?= site_url('kategori/' . $p->category_slug) ?>" class="text-xs text-gray-400 hover:text-brand mt-0.5"><?= esc($p->category_name) ?></a>
    <?php endif; ?>
    <p class="rp font-bold text-brand mt-1.5"><?= rupiah($p->price) ?></p>
    <p class="text-xs mt-1 <?= ((int) $p->stock > 0) ? 'text-gray-500' : 'text-red-600' ?>">
      <?= ((int) $p->stock > 0) ? 'Stok: ' . (int) $p->stock : 'Habis' ?>
    </p>
    <div class="mt-auto pt-3">
      <?= form_open('cart/add') ?>
        <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
        <input type="hidden" name="qty" value="1">
        <button type="submit" <?= ((int) $p->stock > 0) ? '' : 'disabled' ?>
                class="w-full <?= ((int) $p->stock > 0) ? 'bg-brand hover:bg-brand-dark' : 'bg-gray-300 cursor-not-allowed' ?> text-white text-sm font-semibold px-3 py-2 rounded-md transition-colors">
          <?= ((int) $p->stock > 0) ? 'Tambah ke Keranjang' : 'Stok Habis' ?>
        </button>
      </form>
    </div>
  </div>
</div>
