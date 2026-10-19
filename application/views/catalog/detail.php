<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $in_stock = (int) $product->stock > 0; ?>

<nav class="text-xs text-gray-500 mb-4" aria-label="Breadcrumb">
  <a href="<?= site_url('') ?>" class="hover:text-brand">Beranda</a>
  <span class="mx-1 text-gray-300">/</span>
  <a href="<?= site_url('katalog') ?>" class="hover:text-brand">Katalog</a>
  <span class="mx-1 text-gray-300">/</span>
  <a href="<?= site_url('kategori/' . $product->category_slug) ?>" class="hover:text-brand"><?= esc($product->category_name) ?></a>
</nav>

<div class="product-card bg-white border border-gray-200 rounded-2xl p-4 sm:p-6 grid md:grid-cols-2 gap-6 lg:gap-8"
     data-stock="<?= (int) $product->stock ?>">
  <!-- gambar -->
  <div>
    <div class="aspect-square bg-gray-50 rounded-xl flex items-center justify-center overflow-hidden">
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
    <p class="text-xs font-medium text-gray-400 mb-1">
      <a href="<?= site_url('kategori/' . $product->category_slug) ?>" class="hover:text-brand"><?= esc($product->category_name) ?></a>
    </p>
    <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 leading-tight"><?= esc($product->name) ?></h1>

    <p class="rp text-3xl font-extrabold text-brand mt-4"><?= rupiah($product->price) ?></p>

    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 text-sm">
      <span class="<?= $in_stock ? 'text-gray-600' : 'text-red-600 font-semibold' ?>">
        <?= $in_stock ? 'Tersedia — stok ' . (int) $product->stock : 'Stok habis' ?>
      </span>
      <?php if ((int) ($product->sold ?? 0) > 0): ?>
        <span class="text-gray-300">·</span>
        <span class="text-gray-500">Terjual <?= (int) $product->sold ?></span>
      <?php endif; ?>
    </div>

    <?php if (!empty($product->description)): ?>
      <div class="mt-5 text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-5">
        <?= nl2br(esc($product->description)) ?>
      </div>
    <?php endif; ?>

    <div class="mt-6">
      <?php if ($in_stock): ?>
        <?= form_open('cart/add', array('data-ajax-cart' => '1', 'class' => 'space-y-3 max-w-sm')) ?>
          <input type="hidden" name="product_id" value="<?= (int) $product->id ?>">
          <input type="hidden" name="return" value="<?= esc(current_return_path()) ?>">

          <!-- counter jumlah -->
          <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden w-40">
            <button type="button" data-qty-dec aria-label="Kurangi jumlah"
                    class="w-11 h-11 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-xl leading-none">−</button>
            <input type="number" name="qty" data-qty value="1" min="1" max="<?= (int) $product->stock ?>"
                   inputmode="numeric" aria-label="Jumlah"
                   class="w-full min-w-0 border-0 text-center text-sm py-2 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-inset">
            <button type="button" data-qty-inc aria-label="Tambah jumlah"
                    class="w-11 h-11 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-xl leading-none">+</button>
          </div>

          <div class="flex items-stretch gap-2">
            <!-- tambah ke keranjang: ikon '+' modern -->
            <button type="submit" data-ajax-submit title="Tambah ke keranjang" aria-label="Tambah ke keranjang"
                    class="shrink-0 w-12 bg-brand hover:bg-brand-dark text-white rounded-lg flex items-center justify-center transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                <path d="M12 5v14"/><path d="M5 12h14"/>
              </svg>
            </button>
            <!-- beli sekarang: kirim form yang sama ke endpoint checkout-langsung -->
            <button type="submit" formaction="<?= site_url('cart/beli-sekarang') ?>"
                    class="flex-1 bg-accent hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition-colors">
              Beli Sekarang
            </button>
          </div>
        </form>
      <?php else: ?>
        <p class="text-sm text-gray-500 bg-gray-50 border border-gray-200 inline-block px-4 py-2.5 rounded-lg">Produk sedang habis.</p>
      <?php endif; ?>
    </div>

    <a href="<?= site_url('katalog') ?>" class="text-sm font-medium text-brand hover:underline mt-5 inline-block">← Lanjut belanja</a>
  </div>
</div>
