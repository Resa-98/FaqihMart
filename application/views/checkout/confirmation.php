<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-2xl mx-auto">
  <!-- sukses -->
  <div class="bg-white border border-green-300 rounded-lg p-6 text-center">
    <div class="w-14 h-14 mx-auto bg-green-100 rounded-full flex items-center justify-center text-2xl text-brand font-bold">✓</div>
    <h1 class="text-xl font-bold mt-3">Pesanan Berhasil Dibuat</h1>
    <p class="text-sm text-gray-600 mt-2">Terima kasih, <strong><?= esc($order->customer_name) ?></strong>.</p>

    <div class="bg-gray-50 border border-gray-200 rounded-md mt-4 px-4 py-3">
      <p class="text-xs text-gray-500">Nomor Pesanan Anda</p>
      <p class="text-lg font-bold tracking-wide"><?= esc($order->order_number) ?></p>
    </div>

    <p class="text-sm text-gray-600 mt-4">
      Total belanja: <strong class="rp"><?= rupiah($order->total_amount) ?></strong>
    </p>

    <!-- item yang dipesan -->
    <ul class="text-left text-sm mt-4 border-t pt-4 space-y-1.5">
      <?php foreach ($items as $item): ?>
        <li class="flex justify-between gap-2">
          <span class="text-gray-700">
            <?= esc($item->product_name) ?>
            <span class="text-gray-400">×<?= (int) $item->quantity ?></span>
          </span>
          <span class="rp shrink-0"><?= rupiah($item->subtotal) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- WhatsApp -->
    <div class="mt-6 border-t pt-5">
      <p class="text-sm text-gray-700 mb-3">
        Konfirmasi pesanan Anda ke toko melalui WhatsApp — sebutkan nomor pesanan
        <strong><?= esc($order->order_number) ?></strong>.
      </p>
      <a href="<?= esc($wa_link) ?>" target="_blank" rel="noopener noreferrer"
         class="inline-block bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-6 py-2.5 rounded-md transition-colors">
        💬 Konfirmasi via WhatsApp
      </a>
      <p class="text-xs text-gray-500 mt-3">
        Pesanan Anda berstatus <strong>Menunggu Konfirmasi</strong> dan akan diproses oleh toko.
        Mengirim pesan WhatsApp belum menandakan pesanan selesai.
      </p>
    </div>

    <div class="mt-6 flex justify-center gap-4 text-sm">
      <a href="<?= site_url('katalog') ?>" class="text-brand hover:underline">Lanjut belanja</a>
      <a href="<?= site_url('') ?>" class="text-brand hover:underline">Ke beranda</a>
    </div>
  </div>
</div>
