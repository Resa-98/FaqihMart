<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-5"><?= esc($title) ?></h1>

<?php if (empty($lines)): ?>
  <div class="bg-white border border-dashed border-gray-300 rounded-2xl p-10 text-center">
    <div class="w-14 h-14 mx-auto bg-gray-100 rounded-full flex items-center justify-center text-gray-400 mb-4">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
    </div>
    <p class="text-gray-500 text-sm mb-4">Keranjang Anda masih kosong.</p>
    <a href="<?= site_url('katalog') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-lg inline-block transition-colors">
      Mulai Belanja
    </a>
  </div>
<?php else: ?>

<div class="grid lg:grid-cols-3 gap-6 items-start">
  <!-- daftar item -->
  <div class="lg:col-span-2 space-y-3">
    <?php foreach ($lines as $line): $p = $line['product']; ?>
      <div class="bg-white border border-gray-200 rounded-xl p-3 sm:p-4"
           data-cart-line data-price="<?= (float) $p->price ?>" data-stock="<?= (int) $p->stock ?>">
        <div class="flex gap-3 items-start">
          <a href="<?= site_url('produk/' . $p->slug) ?>" class="w-16 h-16 sm:w-20 sm:h-20 shrink-0 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
            <?php if (!empty($p->image)): ?>
              <img src="<?= base_url($p->image) ?>" alt="<?= esc($p->name) ?>" loading="lazy" class="product-img max-h-full max-w-full">
            <?php endif; ?>
          </a>

          <div class="flex-1 min-w-0">
            <a href="<?= site_url('produk/' . $p->slug) ?>" class="text-sm font-semibold text-gray-900 hover:text-brand line-clamp-2 leading-snug"><?= esc($p->name) ?></a>
            <p class="rp text-sm text-gray-500 mt-1"><?= rupiah($p->price) ?></p>
            <?php if ($line['issue']): ?>
              <p class="text-xs text-red-600 mt-1" data-line-issue><?= esc($line['issue']) ?></p>
            <?php endif; ?>

            <div class="flex items-center justify-between gap-3 flex-wrap mt-3">
              <div class="flex items-center gap-2">
                <?= form_open('cart/update', array('class' => 'flex items-center gap-2', 'data-cart-update' => '1')) ?>
                  <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
                  <input type="number" name="qty" value="<?= (int) $line['qty'] ?>" min="0"
                         max="<?= (int) $p->stock ?>" inputmode="numeric"
                         data-cart-qty
                         class="w-16 border border-gray-300 rounded-lg px-2 py-1.5 text-center text-sm focus:outline-none focus:ring-2 focus:ring-brand focus:border-brand"
                         aria-label="Jumlah untuk <?= esc($p->name) ?>">
                  <button type="submit" class="text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-600 px-3 py-2 rounded-lg transition-colors">Ubah</button>
                </form>

                <?= form_open('cart/remove') ?>
                  <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
                  <button type="submit" class="text-xs text-red-600 hover:bg-red-50 px-2.5 py-2 rounded-lg transition-colors" aria-label="Hapus <?= esc($p->name) ?>" title="Hapus produk">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                  </button>
                </form>
              </div>

              <p class="rp text-sm font-bold text-gray-900" data-line-subtotal><?= rupiah($line['subtotal']) ?></p>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ringkasan -->
  <div class="bg-white border border-gray-200 rounded-xl p-5 lg:sticky lg:top-24">
    <h2 class="font-bold tracking-tight text-gray-900 mb-3">Ringkasan Belanja</h2>
    <div class="flex justify-between items-center text-sm mb-4 border-b border-gray-100 pb-4">
      <span class="text-gray-500">Total</span>
      <span class="rp font-bold text-brand text-xl" data-cart-total><?= rupiah($total) ?></span>
    </div>

    <?php $has_issue = array_filter($lines, function ($l) { return $l['issue'] !== NULL; }); ?>
    <?php if (!empty($has_issue)): ?>
      <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg p-2.5 mb-3">
        Beberapa item bermasalah dan tidak akan ditagih — jumlah akhir dicek ulang saat checkout.
      </p>
    <?php endif; ?>

    <a href="<?= site_url('checkout') ?>" class="block text-center bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-3 rounded-lg transition-colors">
      Lanjut ke Checkout
    </a>
    <a href="<?= site_url('katalog') ?>" class="block text-center text-sm font-medium text-brand hover:underline mt-3">Lanjut belanja</a>
  </div>
</div>

<?php endif; ?>
