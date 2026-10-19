<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-2xl mx-auto">
  <!-- sukses -->
  <div class="bg-white border border-green-200 rounded-2xl p-6 sm:p-8 text-center">
    <div class="w-16 h-16 mx-auto bg-brand-light rounded-full flex items-center justify-center text-brand">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8"><path d="M20 6 9 17l-5-5"/></svg>
    </div>
    <h1 class="text-2xl font-bold tracking-tight text-gray-900 mt-4">Pesanan Berhasil Dibuat</h1>
    <p class="text-sm text-gray-500 mt-2">Terima kasih, <strong class="text-gray-700"><?= esc($order->customer_name) ?></strong>.</p>

    <div class="bg-gray-50 border border-gray-200 rounded-xl mt-5 px-4 py-3.5">
      <p class="text-xs font-medium text-gray-500">Nomor Pesanan Anda</p>
      <p class="text-xl font-bold tracking-wide text-gray-900 mt-0.5"><?= esc($order->order_number) ?></p>
    </div>

    <p class="text-sm text-gray-500 mt-5">
      Total belanja: <strong class="rp text-gray-900"><?= rupiah($order->total_amount) ?></strong>
    </p>

    <!-- item yang dipesan -->
    <ul class="text-left text-sm mt-4 border-t border-gray-100 pt-4 space-y-2">
      <?php foreach ($items as $item): ?>
        <li class="flex justify-between gap-2">
          <span class="text-gray-600">
            <?= esc($item->product_name) ?>
            <span class="text-gray-400">×<?= (int) $item->quantity ?></span>
          </span>
          <span class="rp font-medium text-gray-900 shrink-0"><?= rupiah($item->subtotal) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- WhatsApp -->
    <div class="mt-7 border-t border-gray-100 pt-6">
      <p class="text-sm text-gray-600 mb-3">
        Konfirmasi pesanan Anda ke toko melalui WhatsApp — sebutkan nomor pesanan
        <strong class="text-gray-900"><?= esc($order->order_number) ?></strong>.
      </p>
      <a href="<?= esc($wa_link) ?>" target="_blank" rel="noopener noreferrer"
         class="inline-block bg-brand hover:bg-brand-dark text-white text-sm font-semibold px-6 py-3 rounded-lg transition-colors">
        Konfirmasi via WhatsApp
      </a>
      <p class="text-xs text-gray-500 mt-3">
        Pesanan Anda berstatus <strong>Menunggu Konfirmasi</strong> dan akan diproses oleh toko.
        Mengirim pesan WhatsApp belum menandakan pesanan selesai.
      </p>
    </div>

    <!-- struk digital -->
    <div class="mt-6">
      <a href="<?= site_url('konfirmasi/struk') ?>" target="_blank" rel="noopener"
         class="inline-flex items-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold px-5 py-2.5 rounded-lg transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Lihat / Cetak Struk
      </a>
    </div>

    <div class="mt-7 flex justify-center gap-6 text-sm font-medium">
      <a href="<?= site_url('katalog') ?>" class="text-brand hover:underline">Lanjut belanja</a>
      <a href="<?= site_url('') ?>" class="text-gray-500 hover:text-brand">Ke beranda</a>
    </div>
  </div>
</div>
