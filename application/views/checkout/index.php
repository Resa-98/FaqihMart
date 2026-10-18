<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-xl font-bold mb-4"><?= esc($title) ?></h1>

<div class="grid lg:grid-cols-3 gap-6 items-start">
  <!-- form -->
  <div class="lg:col-span-2 bg-white border border-gray-200 rounded-lg p-5">
    <?= form_open('checkout/proses') ?>
      <?php $vf = validation_errors(); ?>
      <?php if ($vf): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-md mb-4" role="alert">
          <?= $vf /* CI3 validation_errors meng-escape label; pesan runtime dari form rule — aman */ ?>
        </div>
      <?php endif; ?>

      <div class="space-y-4">
        <div>
          <label for="customer_name" class="block text-sm font-medium mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
          <input type="text" id="customer_name" name="customer_name" required maxlength="150"
                 value="<?= esc(set_value('customer_name')) ?>"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                 placeholder="Nama Anda">
        </div>

        <div>
          <label for="whatsapp" class="block text-sm font-medium mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
          <input type="tel" id="whatsapp" name="whatsapp" required maxlength="20"
                 value="<?= esc(set_value('whatsapp')) ?>"
                 class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                 placeholder="08xxxxxxxxxx">
          <p class="text-xs text-gray-500 mt-1">Dipakai toko untuk menghubungi Anda tentang pesanan ini.</p>
        </div>

        <div>
          <label for="address" class="block text-sm font-medium mb-1">Alamat Pengiriman <span class="text-red-500">*</span></label>
          <textarea id="address" name="address" required rows="3" maxlength="500"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                    placeholder="Nama perumahan, blok, nomor rumah, RT/RW, patokan"><?= esc(set_value('address')) ?></textarea>
        </div>

        <div>
          <label for="note" class="block text-sm font-medium mb-1">Catatan (opsional)</label>
          <textarea id="note" name="note" rows="2" maxlength="500"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand"
                    placeholder="Contoh: titip ke satpam kalau tidak ada orang"><?= esc(set_value('note')) ?></textarea>
        </div>
      </div>

      <div class="mt-5 flex flex-col sm:flex-row gap-3">
        <button type="submit" class="bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-6 py-2.5 rounded-md transition-colors">
          Buat Pesanan
        </button>
        <a href="<?= site_url('keranjang') ?>" class="text-sm text-brand hover:underline text-center sm:self-center">← Kembali ke keranjang</a>
      </div>
    </form>
  </div>

  <!-- ringkasan -->
  <div class="bg-white border border-gray-200 rounded-lg p-5">
    <h2 class="font-semibold mb-3">Ringkasan Belanja</h2>
    <ul class="text-sm space-y-2 mb-3 border-b pb-3">
      <?php foreach ($payable as $line): $p = $line['product']; ?>
        <li class="flex justify-between gap-2">
          <span class="text-gray-700 line-clamp-1">
            <?= esc($p->name) ?>
            <span class="text-gray-400">×<?= (int) $line['qty'] ?></span>
            <?php if ($line['issue']): ?>
              <span class="block text-xs text-red-600"><?= esc($line['issue']) ?></span>
            <?php endif; ?>
          </span>
          <span class="rp shrink-0"><?= rupiah($line['subtotal']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
    <div class="flex justify-between mb-4">
      <span class="text-gray-600 text-sm">Total</span>
      <span class="rp font-bold text-brand text-base"><?= rupiah($total) ?></span>
    </div>
    <p class="text-xs text-gray-500 leading-relaxed">
      Harga dan total dihitung ulang oleh server saat pesanan dibuat.
      Setelah pesanan tersimpan, konfirmasikan lewat WhatsApp ke toko.
    </p>
  </div>
</div>
