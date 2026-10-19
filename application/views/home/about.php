<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-3xl mx-auto bg-white rounded-2xl border border-gray-200 p-6 sm:p-8">
  <h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-1">Informasi <?= esc($store_name) ?></h1>
  <p class="text-sm text-gray-500 mb-6">Panduan berbelanja &amp; layanan toko.</p>

  <div class="text-gray-700 space-y-4 text-sm leading-relaxed">
    <p>
      <strong class="text-gray-900"><?= esc($store_name) ?></strong> adalah toko online yang menyediakan kebutuhan pokok
      (sembako) dan kebutuhan rumah tangga dengan layanan pengiriman (delivery) untuk pelanggan
      di perumahan <strong class="text-gray-900"><?= esc($store_area) ?></strong>.
    </p>

    <h2 class="text-base font-bold tracking-tight text-gray-900 mt-8 mb-1">Cara berbelanja</h2>
    <ol class="list-decimal ml-5 space-y-1">
      <li>Pilih produk dari <a href="<?= site_url('katalog') ?>" class="text-brand font-medium hover:underline">Katalog</a>.</li>
      <li>Masukkan barang ke Keranjang, tentukan jumlahnya.</li>
      <li>Lanjutkan ke Checkout — isi nama, nomor WhatsApp, dan alamat.</li>
      <li>Pesanan tersimpan, Anda mendapatkan <strong class="text-gray-900">nomor pesanan</strong>.</li>
      <li>Konfirmasi pesanan Anda melalui WhatsApp ke nomor toko.</li>
    </ol>

    <h2 class="text-base font-bold tracking-tight text-gray-900 mt-8 mb-1">Status pesanan</h2>
    <p>
      Pesanan bergerak melalui status: <em>Menunggu Konfirmasi → Diproses → Dikirim → Selesai</em>,
      dan dapat berakhir <em>Dibatalkan</em>. Pesanan dianggap <strong class="text-gray-900">selesai setelah
      dikonfirmasi oleh toko</strong>, bukan saat pesan WhatsApp dikirim.
    </p>

    <h2 class="text-base font-bold tracking-tight text-gray-900 mt-8 mb-1">Pembayaran &amp; pengiriman</h2>
    <p>
      Keterangan metode pembayaran, ongkos kirim, dan area pengiriman akan dicantumkan di sini
      segera setelah ditetapkan toko.
      <?php $wa = store_whatsapp(); ?>
      Untuk pertanyaan, hubungi kami via
      <a href="<?= esc(wa_link($wa, 'Halo ' . $store_name . ', saya ingin bertanya.')) ?>" target="_blank" rel="noopener noreferrer" class="text-brand font-medium hover:underline">WhatsApp <?= esc(wa_display($wa)) ?></a>.
    </p>
  </div>
</div>
