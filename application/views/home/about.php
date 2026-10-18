<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="max-w-3xl mx-auto bg-white rounded-lg border border-gray-200 p-6 sm:p-8">
  <h1 class="text-2xl font-bold mb-4">Informasi <?= esc($store_name) ?></h1>

  <div class="prose prose-sm max-w-none text-gray-700 space-y-4 text-sm leading-relaxed">
    <p>
      <strong><?= esc($store_name) ?></strong> adalah toko online yang menyediakan kebutuhan pokok
      (sembako) dan kebutuhan rumah tangga dengan layanan pengiriman (delivery) untuk pelanggan
      di perumahan <strong><?= esc($store_area) ?></strong>.
    </p>

    <h2 class="font-semibold text-base mt-6">Cara berbelanja</h2>
    <ol class="list-decimal ml-5 space-y-1">
      <li>Pilih produk dari <a href="<?= site_url('katalog') ?>" class="text-brand hover:underline">Katalog</a>.</li>
      <li>Masukkan barang ke Keranjang, tentukan jumlahnya.</li>
      <li>Lanjutkan ke Checkout — isi nama, nomor WhatsApp, dan alamat.</li>
      <li>Pesanan tersimpan, Anda mendapatkan <strong>nomor pesanan</strong>.</li>
      <li>Konfirmasi pesanan Anda melalui WhatsApp ke nomor toko.</li>
    </ol>

    <h2 class="font-semibold text-base mt-6">Status pesanan</h2>
    <p>
      Pesanan bergerak melalui status: <em>Menunggu Konfirmasi → Diproses → Dikirim → Selesai</em>,
      dan dapat berakhir <em>Dibatalkan</em>. Pesanan dianggap <strong>selesai setelah
      dikonfirmasi oleh toko</strong>, bukan saat pesan WhatsApp dikirim.
    </p>

    <h2 class="font-semibold text-base mt-6">Pembayaran & pengiriman</h2>
    <p>
      Keterangan metode pembayaran, ongkos kirim, dan area pengiriman akan dicantumkan di sini
      segera setelah ditetapkan toko. Untuk pertanyaan, hubungi kami via WhatsApp.
    </p>
  </div>
</div>
