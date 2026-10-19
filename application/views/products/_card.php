<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php // Card produk dipakai beranda & katalog. Input: $p (obj produk, sudah punya ->sold). ?>
<?php $in_stock = (int) $p->stock > 0; ?>
<div class="product-card group bg-white rounded-xl border border-gray-200 hover:shadow-md hover:border-gray-300 transition-all flex flex-col overflow-hidden"
     data-stock="<?= (int) $p->stock ?>">
  <a href="<?= site_url('produk/' . $p->slug) ?>" class="block p-3">
    <div class="aspect-square bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden">
      <?php if (!empty($p->image)): ?>
        <img src="<?= base_url($p->image) ?>" alt="<?= esc($p->name) ?>"
             loading="lazy" class="product-img max-h-full max-w-full transition-transform duration-300 group-hover:scale-105"
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
    <a href="<?= site_url('produk/' . $p->slug) ?>" class="text-sm font-semibold text-gray-900 hover:text-brand line-clamp-2 leading-snug"><?= esc($p->name) ?></a>
    <?php if (!empty($p->category_name)): ?>
      <a href="<?= site_url('kategori/' . $p->category_slug) ?>" class="text-xs text-gray-400 hover:text-brand mt-1"><?= esc($p->category_name) ?></a>
    <?php endif; ?>

    <p class="rp text-base font-bold text-brand mt-2"><?= rupiah($p->price) ?></p>

    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1">
      <span class="text-xs <?= $in_stock ? 'text-gray-500' : 'text-red-600 font-medium' ?>">
        <?= $in_stock ? 'Stok: ' . (int) $p->stock : 'Habis' ?>
      </span>
      <?php if ((int) ($p->sold ?? 0) > 0): ?>
        <span class="text-xs text-gray-400">·</span>
        <span class="text-xs text-gray-500">Terjual <?= (int) $p->sold ?></span>
      <?php endif; ?>
    </div>

    <?php if ($in_stock): ?>
      <div class="mt-auto pt-3 space-y-2">
        <?= form_open('cart/add', array('data-ajax-cart' => '1', 'class' => 'space-y-2')) ?>
          <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
          <input type="hidden" name="return" value="<?= esc(current_return_path()) ?>">

          <!-- counter jumlah -->
          <div class="flex items-center justify-between border border-gray-300 rounded-lg overflow-hidden">
            <button type="button" data-qty-dec aria-label="Kurangi jumlah"
                    class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-lg leading-none">−</button>
            <input type="number" name="qty" data-qty value="1" min="1" max="<?= (int) $p->stock ?>"
                   inputmode="numeric" aria-label="Jumlah"
                   class="w-full min-w-0 border-0 text-center text-sm py-1.5 focus:outline-none focus:ring-2 focus:ring-brand focus:ring-inset">
            <button type="button" data-qty-inc aria-label="Tambah jumlah"
                    class="w-9 h-9 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-lg leading-none">+</button>
          </div>

          <div class="flex items-stretch gap-2">
            <!-- tombol tambah keranjang: ikon '+' modern -->
            <button type="submit" data-ajax-submit title="Tambah ke keranjang" aria-label="Tambah ke keranjang"
                    class="shrink-0 w-10 bg-brand hover:bg-brand-dark text-white rounded-lg flex items-center justify-center transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                   stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                <path d="M12 5v14"/><path d="M5 12h14"/>
              </svg>
            </button>
            <!-- beli sekarang: kirim form yang sama ke endpoint checkout-langsung -->
            <button type="submit" formaction="<?= site_url('cart/beli-sekarang') ?>"
                    class="flex-1 min-w-0 bg-accent hover:bg-amber-600 text-white text-xs sm:text-sm font-semibold px-2 py-2 rounded-lg transition-colors whitespace-nowrap">
              Beli Sekarang
            </button>
          </div>
        </form>
      </div>
    <?php else: ?>
      <div class="mt-auto pt-3">
        <button type="button" disabled
                class="w-full bg-gray-200 text-gray-400 cursor-not-allowed text-sm font-semibold px-3 py-2.5 rounded-lg">
          Stok Habis
        </button>
      </div>
    <?php endif; ?>
  </div>
</div>
