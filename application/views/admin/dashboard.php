<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<h1 class="text-2xl font-bold tracking-tight text-gray-900 mb-1">Dashboard</h1>
<p class="text-sm text-gray-500 mb-6">Ringkasan toko hari ini.</p>

<!-- kartu ringkas -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <p class="text-xs font-medium text-gray-500">Pesanan Baru</p>
    <p class="text-2xl font-bold text-amber-600 mt-1 tabular-nums"><?= (int) $counts['menunggu_konfirmasi'] ?></p>
    <p class="text-xs text-gray-400 mt-1">Menunggu Konfirmasi</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <p class="text-xs font-medium text-gray-500">Sedang Diproses</p>
    <p class="text-2xl font-bold text-blue-600 mt-1 tabular-nums"><?= (int) $counts['diproses'] ?></p>
    <p class="text-xs text-gray-400 mt-1">+ <?= (int) $counts['dikirim'] ?> dikirim</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <p class="text-xs font-medium text-gray-500">Total Pesanan</p>
    <p class="text-2xl font-bold text-brand mt-1 tabular-nums"><?= (int) $total_orders ?></p>
    <p class="text-xs text-gray-400 mt-1"><?= (int) $counts['selesai'] ?> selesai</p>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <p class="text-xs font-medium text-gray-500">Produk</p>
    <p class="text-2xl font-bold text-gray-700 mt-1 tabular-nums"><?= (int) $total_products ?></p>
    <a href="<?= site_url('admin/produk/tambah') ?>" class="text-xs text-brand hover:underline mt-1 inline-block">+ tambah produk</a>
  </div>
</div>

<!-- pesanan menunggu konfirmasi -->
<section class="bg-white border border-gray-200 rounded-xl overflow-hidden">
  <div class="px-4 py-3.5 border-b border-gray-100 flex items-center justify-between">
    <h2 class="font-semibold text-gray-900">Pesanan Menunggu Konfirmasi</h2>
    <a href="<?= site_url('admin/pesanan?status=menunggu_konfirmasi') ?>" class="text-xs font-medium text-brand hover:underline">Lihat semua →</a>
  </div>

  <?php if (empty($pending_orders)): ?>
    <p class="p-4 text-sm text-gray-500">Tidak ada pesanan baru. 🎉</p>
  <?php else: ?>

    <!-- tabel (>= sm) -->
    <div class="hidden sm:block overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs font-medium text-gray-500">
          <tr>
            <th class="text-left px-4 py-2.5">Nomor</th>
            <th class="text-left px-4 py-2.5">Pelanggan</th>
            <th class="text-right px-4 py-2.5">Total</th>
            <th class="text-right px-4 py-2.5">Tanggal</th>
            <th></th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($pending_orders as $o): ?>
            <tr class="hover:bg-gray-50/70">
              <td class="px-4 py-2.5 font-mono text-xs"><?= esc($o->order_number) ?></td>
              <td class="px-4 py-2.5"><?= esc($o->customer_name) ?></td>
              <td class="px-4 py-2.5 text-right rp"><?= rupiah($o->total_amount) ?></td>
              <td class="px-4 py-2.5 text-right text-xs text-gray-500"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></td>
              <td class="px-4 py-2.5 text-right">
                <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="text-brand text-xs font-medium hover:underline">Detail →</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- kartu (< sm) -->
    <ul class="sm:hidden divide-y divide-gray-100">
      <?php foreach ($pending_orders as $o): ?>
        <li>
          <a href="<?= site_url('admin/pesanan/' . $o->order_number) ?>" class="block px-4 py-3.5 active:bg-gray-50">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="font-mono text-xs text-gray-500"><?= esc($o->order_number) ?></p>
                <p class="font-medium text-gray-900 truncate mt-0.5"><?= esc($o->customer_name) ?></p>
                <p class="text-xs text-gray-400 mt-0.5"><?= date('d/m/Y H:i', strtotime($o->created_at)) ?></p>
              </div>
              <div class="text-right shrink-0">
                <p class="rp font-semibold text-gray-900"><?= rupiah($o->total_amount) ?></p>
                <span class="text-xs text-brand font-medium">Detail →</span>
              </div>
            </div>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>

  <?php endif; ?>
</section>
