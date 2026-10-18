<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-xl font-bold mb-4"><?= esc($title) ?></h1>

<?php if (empty($lines)): ?>
  <div class="bg-white border border-dashed border-gray-300 rounded-lg p-10 text-center">
    <p class="text-gray-500 text-sm mb-3">Keranjang Anda masih kosong.</p>
    <a href="<?= site_url('katalog') ?>" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-5 py-2.5 rounded-md inline-block">
      Mulai Belanja
    </a>
  </div>
<?php else: ?>

<div class="grid lg:grid-cols-3 gap-6 items-start">
  <!-- daftar item -->
  <div class="lg:col-span-2 space-y-3">
    <?php foreach ($lines as $line): $p = $line['product']; ?>
      <div class="bg-white border border-gray-200 rounded-lg p-3 sm:p-4 flex gap-3 items-center">
        <a href="<?= site_url('produk/' . $p->slug) ?>" class="w-16 h-16 shrink-0 bg-gray-100 rounded-md flex items-center justify-center overflow-hidden">
          <?php if (!empty($p->image)): ?>
            <img src="<?= base_url($p->image) ?>" alt="<?= esc($p->name) ?>" loading="lazy" class="product-img max-h-full max-w-full">
          <?php endif; ?>
        </a>

        <div class="flex-1 min-w-0">
          <a href="<?= site_url('produk/' . $p->slug) ?>" class="text-sm font-medium hover:text-brand line-clamp-1"><?= esc($p->name) ?></a>
          <p class="rp text-sm text-gray-600 mt-0.5"><?= rupiah($p->price) ?></p>
          <?php if ($line['issue']): ?>
            <p class="text-xs text-red-600 mt-1"><?= esc($line['issue']) ?></p>
          <?php endif; ?>
        </div>

        <!-- jumlah -->
        <div class="flex items-center gap-2">
          <?= form_open('cart/update', array('class' => 'flex items-center gap-2')) ?>
            <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
            <input type="number" name="qty" value="<?= (int) $line['qty'] ?>" min="0"
                   max="<?= (int) $p->stock ?>"
                   class="w-16 border border-gray-300 rounded-md px-2 py-1.5 text-center text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                   aria-label="Jumlah untuk <?= esc($p->name) ?>">
            <button type="submit" class="text-xs bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-md">Ubah</button>
          </form>

          <?= form_open('cart/remove') ?>
            <input type="hidden" name="product_id" value="<?= (int) $p->id ?>">
            <button type="submit" class="text-xs text-red-600 hover:underline px-1" title="Hapus produk">✕</button>
          </form>
        </div>

        <p class="rp text-sm font-semibold w-24 text-right hidden sm:block"><?= rupiah($line['subtotal']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- ringkasan -->
  <div class="bg-white border border-gray-200 rounded-lg p-5">
    <h2 class="font-semibold mb-3">Ringkasan Belanja</h2>
    <div class="flex justify-between text-sm mb-4 border-b pb-3">
      <span class="text-gray-600">Total</span>
      <span class="rp font-bold text-brand text-base"><?= rupiah($total) ?></span>
    </div>

    <?php $has_issue = array_filter($lines, function ($l) { return $l['issue'] !== NULL; }); ?>
    <?php if (!empty($has_issue)): ?>
      <p class="text-xs text-amber-600 bg-amber-50 border border-amber-200 rounded-md p-2 mb-3">
        Beberapa item bermasalah dan tidak akan ditagih — jumlah akhir dicek ulang saat checkout.
      </p>
    <?php endif; ?>

    <a href="<?= site_url('checkout') ?>" class="block text-center bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-4 py-2.5 rounded-md transition-colors">
      Lanjut ke Checkout
    </a>
    <a href="<?= site_url('katalog') ?>" class="block text-center text-sm text-brand hover:underline mt-3">Lanjut belanja</a>
  </div>
</div>

<?php endif; ?>
