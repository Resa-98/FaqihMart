<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-xl font-bold mb-1">Dashboard</h1>
<p class="text-sm text-gray-500 mb-5">Ringkasan toko hari ini.</p>

<!-- kartu ringkas -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white border border-gray-200 rounded-lg p-4">
    <p class="text-xs text-gray-500">Pesanan Baru</p>
    <p class="text-2xl font-bold text-yellow-600"><?= (int) $counts['menunggu_konfirmasi'] ?></p>
    <p class="text-xs text-gray-400 mt-1">Menunggu Konfirmasi</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-lg p-4">
    <p class="text-xs text-gray-500">Sedang Diproses</p>
    <p class="text-2xl font-bold text-blue-600"><?= (int) $counts['diproses'] ?></p>
    <p class="text-xs text-gray-400 mt-1">+ <?= (int) $counts['dikirim'] ?> dikirim</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-lg p-4">
    <p class="text-xs text-gray-500">Total Pesanan</p>
    <p class="text-2xl font-bold text-brand"><?= (int) $total_orders ?></p>
    <p class="text-xs text-gray-400 mt-1"><?= (int) $counts['selesai'] ?> selesai</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-lg p-4">
    <p class="text-xs text-gray-500">Produk</p>
    <p class="text-2xl font-bold text-gray-700"><?= (int) $total_products ?></p>
    <a href="<?= site_url('admin/produk/tambah') ?>" class="text-xs text-brand hover:underline mt-1 inline-block">+ tambah produk</a>
  </div>
</div>

<!-- pesanan menunggu konfirmasi -->
<section class="bg-white border border-gray-200 rounded-lg">
  <div class="px-4 py-3 border-b flex items-center justify-between">
    <h2 class="font-semibold text-sm">Pesanan Menunggu Konfirmasi</h2>
    <a href="<?= site_url('admin/pesanan?status=menunggu_konfirmasi') ?>" class="text-xs text-brand hover:underline">Lihat semua →</a>
  </div>

  <?php if (empty($pending_orders)): ?>
    <p class="p-4 text-sm text-gray-500">Tidak ada pesanan baru. 🎉</p>
  <?php else: ?>
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs text-gray-500">
        <tr>
          <th class="text-left px-4 py-2">Nomor</th>
          <th class="text-left px-4 py-2">Pelanggan</th>
          <th class="text-right px-4 py-2">Total</th>
          <th class="text-right px-4 py-2">Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php foreach ($pending_orders as $o): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-2.5 font-mono text-xs"><?= esc($o->order_number) ?></td>
            <td class="px-4 py-2.5"><?= esc($o->customer_name) ?></td>
            <td class="px-4 py-2.5 text-right rp"><?= rupiah($o->total_amount) ?></td>
            <td class="px-4 py-2.5 text-right text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
            <td class="px-4 py-2.5 text-right">
              <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="text-brand text-xs hover:underline">Detail →</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
