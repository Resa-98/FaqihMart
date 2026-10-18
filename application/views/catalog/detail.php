<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<nav class="text-xs text-gray-500 mb-4">
  <a href="<?= site_url('') ?>" class="hover:text-brand">Beranda</a> /
  <a href="<?= site_url('katalog') ?>" class="hover:text-brand">Katalog</a> /
  <a href="<?= site_url('kategori/' . $product->category_slug) ?>" class="hover:text-brand"><?= esc($product->category_name) ?></a>
</nav>

<div class="bg-white border border-gray-200 rounded-lg p-4 sm:p-6 grid md:grid-cols-2 gap-6">
  <!-- gambar -->
  <div>
    <div class="aspect-square bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
      <?php if (!empty($product->image)): ?>
        <img src="<?= base_url($product->image) ?>" alt="<?= esc($product->name) ?>"
             class="product-img max-h-full max-w-full"
             onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
        <div style="display:none" class="w-full h-full items-center justify-center text-gray-400 text-sm p-4 text-center">
          Gambar tidak tersedia
        </div>
      <?php else: ?>
        <div class="w-full h-full flex items-center justify-center text-gray-400 text-sm">Tanpa gambar</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- info -->
  <div class="flex flex-col">
    <p class="text-xs text-gray-400 mb-1">
      <a href="<?= site_url('kategori/' . $product->category_slug) ?>" class="hover:text-brand"><?= esc($product->category_name) ?></a>
    </p>
    <h1 class="text-xl sm:text-2xl font-bold"><?= esc($product->name) ?></h1>

    <p class="rp text-2xl font-bold text-brand mt-3"><?= rupiah($product->price) ?></p>

    <p class="text-sm mt-2 <?= ((int) $product->stock > 0) ? 'text-gray-600' : 'text-red-600 font-semibold' ?>">
      <?= ((int) $product->stock > 0) ? 'Tersedia — stok ' . (int) $product->stock : 'Stok habis' ?>
    </p>

    <?php if (!empty($product->description)): ?>
      <div class="mt-4 text-sm text-gray-700 leading-relaxed border-t pt-4">
        <?= nl2br(esc($product->description)) ?>
      </div>
    <?php endif; ?>

    <div class="mt-6">
      <?php if ((int) $product->stock > 0): ?>
        <?= form_open('cart/add', array('class' => 'flex items-stretch gap-2 max-w-sm')) ?>
          <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
          <input type="number" name="qty" value="1" min="1" max="<?= (int) $product->stock ?>"
                 class="w-20 border border-gray-300 rounded-md px-2 py-2 text-center text-sm focus:outline-none focus:ring-2 focus:ring-brand">
          <button type="submit" class="flex-1 bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2 rounded-md transition-colors">
            Tambah ke Keranjang
          </button>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-500 bg-gray-100 inline-block px-4 py-2 rounded-md">Produk sedang habis.</p>
      <?php endif; ?>
    </div>

    <a href="<?= site_url('katalog') ?>" class="text-sm text-brand hover:underline mt-4 inline-block">← Lanjut belanja</a>
  </div>
</div>
